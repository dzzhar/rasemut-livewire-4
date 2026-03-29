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
                ->fileName(fn() => 'karyawan-' . now()->format('Ymd_His'))
                ->label('Ekspor Data')
                ->modifyQueryUsing(function (Builder $query, array $data) {
                    // retrieve the period filter from the form (default this month)
                    $start = $data['start_period'] ?? now()->startOfMonth()->toDateString();
                    $end = $data['end_period'] ?? now()->endOfMonth()->toDateString();
                    $totalDays = \Carbon\Carbon::parse($start)->diffInDays(\Carbon\Carbon::parse($end)) + 1;

                    return $query
                        ->withCount([
                            // count the attendances for each employee by status
                            'attendances as present_count' => fn($q) => $q
                                ->where('status', 'hadir')
                                // will be executed if start and end field are not null
                                // filter by attendance_date column
                                ->when($start, fn($q) => $q->whereDate('attendance_date', '>=', $start))
                                ->when($end, fn($q) => $q->whereDate('attendance_date', '<=', $end)),
                            'attendances as late_count' => fn($q) => $q
                                ->where('late_minutes', '>', '0')
                                ->when($start, fn($q) => $q->whereDate('attendance_date', '>=', $start))
                                ->when($end, fn($q) => $q->whereDate('attendance_date', '<=', $end)),
                            'attendances as early_count' => fn($q) => $q
                                ->where('early_leave_minutes', '>', '0')
                                ->when($start, fn($q) => $q->whereDate('attendance_date', '>=', $start))
                                ->when($end, fn($q) => $q->whereDate('attendance_date', '<=', $end)),
                            'attendances as overtime_count' => fn($q) => $q
                                ->where('overtime_minutes', '>', '0')
                                ->when($start, fn($q) => $q->whereDate('attendance_date', '>=', $start))
                                ->when($end, fn($q) => $q->whereDate('attendance_date', '<=', $end)),
                            'attendances as absent_count' => fn($q) => $q
                                ->where('status', 'tidak_hadir')
                                ->when($start, fn($q) => $q->whereDate('attendance_date', '>=', $start))
                                ->when($end, fn($q) => $q->whereDate('attendance_date', '<=', $end)),
                            'attendances as incomplete_count' => fn($q) => $q
                                ->where('status', 'tidak_lengkap')
                                ->when($start, fn($q) => $q->whereDate('attendance_date', '>=', $start))
                                ->when($end, fn($q) => $q->whereDate('attendance_date', '<=', $end)),

                            // count permissions
                            'permissions as permissions_count' => fn($q) => $q
                                ->when($start, fn($q) => $q->whereDate('permission_date', '>=', $start))
                                ->when($end, fn($q) => $q->whereDate('permission_date', '<=', $end))
                        ])
                        // make a new column called leaves_count
                        ->selectSub(function ($query) use ($start, $end) {
                            $query->from('leaves')
                                // COALESCE -> converts NULL to 0 if there are no leave records
                                // SUM -> adds up all leave durations that overlap the period
                                // DATEDIFF -> calculates the number of days between two dates
                                // LEAST(end_date, $end) -> ensures the end date does not go beyond the period end
                                // GREATEST(start_date, $start) -> ensures the start date is not before the period start
                                // +1 -> includes the first day in the count
                                ->selectRaw('COALESCE(SUM(DATEDIFF(LEAST(end_date, ?), GREATEST(start_date, ?)) + 1), 0)', [$end, $start])
                                ->whereColumn('leaves.employee_id', 'employees.id')
                                // filter by status disetujui
                                ->where('status', 'disetujui')
                                ->whereDate('start_date', '<=', $end)
                                ->whereDate('end_date', '>=', $start);
                        }, 'leaves_count')
                        ->selectRaw("
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
                            ) / ? * 100 as attendance_rate
                        ", [$start, $end, $start, $end, $totalDays])
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
                    Select::make('is_active')->label('Status Aktif')->options([
                        '1' => 'Aktif',
                        '0' => 'Tidak Aktif',
                    ])->placeholder('Semua Status'),
                    Select::make('role')->label('Role')->options([
                        'employee' => 'Karyawan',
                        'admin' => 'Admin',
                    ])->placeholder('Semua Role')
                ])
        ];
    }
}
