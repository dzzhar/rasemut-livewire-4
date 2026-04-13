<?php

namespace App\Filament\Resources\Employees\Pages;

use App\Filament\Exports\EmployeeExporter;
use App\Filament\Resources\Employees\EmployeeResource;
use App\Services\EmployeeService;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class ManageEmployees extends ManageRecords
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->using(fn(array $data) => app(EmployeeService::class)->create($data))
                ->label('Tambah Data'),

            ExportAction::make()
                ->exporter(EmployeeExporter::class)
                ->columnMapping(false)
                ->fileName(fn() => 'rekap-karyawan-' . now()->format('Ymd_His'))
                ->label('Ekspor Data')

                ->modifyQueryUsing(function (Builder $query, array $data) {
                    // export period
                    $start = $data['start_period'] ?? now()->startOfMonth()->toDateString();
                    $end = $data['end_period'] ?? now()->endOfMonth()->toDateString();

                    // weekdays in period exclude weekend
                    $period = CarbonPeriod::create($start, $end);
                    $totalDays = collect($period)
                        ->filter(fn(Carbon $date) => $date->isWeekday())
                        ->count();

                    return $query
                        ->withCount([
                            'attendances as present_count' => fn($q) => $q
                                ->where('status', 'hadir')
                                ->whereBetween('attendance_date', [$start, $end]),
                            'attendances as late_count' => fn($q) => $q
                                ->where('late_minutes', '>', 0)
                                ->whereBetween('attendance_date', [$start, $end]),
                            'attendances as early_count' => fn($q) => $q
                                ->where('early_leave_minutes', '>', 0)
                                ->whereBetween('attendance_date', [$start, $end]),
                            'attendances as overtime_count' => fn($q) => $q
                                ->where('overtime_minutes', '>', 0)
                                ->whereBetween('attendance_date', [$start, $end]),
                            'attendances as absent_count' => fn($q) => $q
                                ->where('status', 'tidak_hadir')
                                ->whereBetween('attendance_date', [$start, $end]),
                            'attendances as incomplete_count' => fn($q) => $q
                                ->where('status', 'tidak_lengkap')
                                ->whereBetween('attendance_date', [$start, $end]),
                            'permissions as permissions_count' => fn($q) => $q
                                ->whereBetween('permission_date', [$start, $end]),
                        ])
                        ->selectSub(function ($q) use ($start, $end) {
                            $q->from('leaves')
                                ->selectRaw('COALESCE(SUM(
                                    DATEDIFF(LEAST(end_date, ?), GREATEST(start_date, ?)) + 1
                                ), 0)', [$end, $start])
                                ->whereColumn('leaves.employee_id', 'employees.id')
                                ->where('status', 'disetujui')
                                ->whereDate('start_date', '<=', $end)
                                ->whereDate('end_date', '>=', $start);
                        }, 'leaves_count')
                        ->selectRaw("
                            (
                                (
                                    (SELECT COUNT(*) FROM attendances 
                                    WHERE attendances.employee_id = employees.id 
                                    AND status = 'hadir' 
                                    AND attendance_date BETWEEN ? AND ?)
                                    +
                                    (SELECT COUNT(*) * 0.5 FROM attendances 
                                    WHERE attendances.employee_id = employees.id 
                                    AND status = 'tidak_lengkap' 
                                    AND attendance_date BETWEEN ? AND ?)
                                )
                                /
                                NULLIF(
                                    (
                                        ? - (
                                            SELECT COALESCE(SUM(
                                                DATEDIFF(LEAST(end_date, ?), GREATEST(start_date, ?)) + 1
                                            ), 0)
                                            FROM leaves
                                            WHERE leaves.employee_id = employees.id
                                            AND status = 'disetujui'
                                            AND start_date <= ?
                                            AND end_date >= ?
                                        )
                                    ), 0
                                )
                            ) * 100 as attendance_rate
                            ", [
                            $start,
                            $end,
                            $start,
                            $end,
                            $totalDays,
                            $end,
                            $start,
                            $end,
                            $start
                        ])
                        ->when(
                            $data['role'] ?? null,
                            fn($q) => $q->whereHas('user', fn($user) => $user->where('role', $data['role']))
                        )
                        ->when(
                            $data['is_active'] ?? null,
                            fn($q) => $q->where('is_active', $data['is_active'])
                        );
                })
                ->schema([
                    DatePicker::make('start_period')->label('Periode Awal'),
                    DatePicker::make('end_period')->label('Periode Akhir'),
                    Select::make('is_active')
                        ->label('Status Aktif')
                        ->options([
                            '1' => 'Aktif',
                            '0' => 'Tidak Aktif',
                        ])
                        ->placeholder('Semua Status')
                        ->nullable(),

                    Select::make('role')
                        ->label('Role')
                        ->options([
                            'employee' => 'Karyawan',
                            'admin' => 'Admin',
                        ])
                        ->placeholder('Semua Role')
                        ->nullable(),
                ])
        ];
    }
}
