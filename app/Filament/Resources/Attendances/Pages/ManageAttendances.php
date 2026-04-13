<?php

namespace App\Filament\Resources\Attendances\Pages;

use App\Filament\Exports\AttendanceExporter;
use App\Filament\Resources\Attendances\AttendanceResource;
use Filament\Actions\ExportAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Builder;

class ManageAttendances extends ManageRecords
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make()
                ->exporter(AttendanceExporter::class)
                ->columnMapping(false)
                ->fileName(fn() => 'presensi-karyawan-' . now()->format('Ymd_His'))
                ->color('primary')
                ->modifyQueryUsing(function (Builder $query, array $data) {
                    $start = $data['start_period'] ?? now()->startOfMonth()->toDateString();
                    $end = $data['end_period'] ?? now()->endOfMonth()->toDateString();

                    return $query
                        ->when(
                            $start ?? null,
                            fn($q) => $q->whereDate('attendance_date', '>=', $start)
                        )
                        ->when(
                            $end ?? null,
                            fn($q) => $q->whereDate('attendance_date', '<=', $end)
                        )
                        ->when(
                            filled($data['status'] ?? null),
                            fn($q) => $q->where('status', $data['status'])
                        );
                })
                ->schema([
                    DatePicker::make('start_period')->label('Periode Awal'),
                    DatePicker::make('end_period')->label('Periode Akhir'),
                    Select::make('status')->label('Status Presensi')->options([
                        'hadir' => 'Hadir',
                        'tidak_hadir' => 'Tidak Hadir',
                        'tidak_lengkap' => 'Tidak Lengkap',
                    ])->placeholder('Semua Status')->nullable(),
                ])
        ];
    }
}
