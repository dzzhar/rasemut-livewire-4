<?php

namespace App\Filament\Exports;

use App\Models\Employee;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class EmployeeExporter extends Exporter
{
    protected static ?string $model = Employee::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('fullname')->label('Nama'),
            ExportColumn::make('user.email')->label('Email'),
            ExportColumn::make('user.role')->label('Role'),
            ExportColumn::make('position.name')->label('Jabatan'),

            ExportColumn::make('present_count')->label('Hadir'),
            ExportColumn::make('late_count')->label('Terlambat'),
            ExportColumn::make('early_count')->label('Pulang Cepat'),
            ExportColumn::make('overtime_count')->label('Lembur'),
            ExportColumn::make('incomplete_count')->label('Tidak Lengkap'),
            ExportColumn::make('permissions_count')->label('Izin'),
            ExportColumn::make('leaves_count')->label('Cuti'),
            ExportColumn::make('absent_count')->label('Tidak Hadir'),

            ExportColumn::make('attendance_rate')
                ->label('Persentase Kehadiran (%)')
                ->formatStateUsing(fn($state) => number_format($state, 2) . '%'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Ekspor ringkasan karyawan Anda telah selesai. ' . Number::format($export->successful_rows) . ' baris berhasil diekspor.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' baris gagal diekspor.';
        }

        return $body;
    }
}
