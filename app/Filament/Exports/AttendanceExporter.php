<?php

namespace App\Filament\Exports;

use App\Models\Attendance;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class AttendanceExporter extends Exporter
{
    protected static ?string $model = Attendance::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('employee.fullname')->label('Nama Karyawan'),
            ExportColumn::make('attendance_date')->label('Tanggal Presensi'),
            ExportColumn::make('check_in')->label('Presensi Masuk'),
            ExportColumn::make('check_out')->label('Presensi Keluar'),
            ExportColumn::make('late_minutes')->label('Menit Terlambat'),
            ExportColumn::make('overtime_minutes')->label('Menit Lembur'),
            ExportColumn::make('early_leave_minutes')->label('Menit Pulang Cepat'),
            ExportColumn::make('status'),
            ExportColumn::make('description')->label('Keterangan'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Ekspor presensi Anda telah selesai. ' . Number::format($export->successful_rows) . ' baris berhasil diekspor.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' baris gagal diekspor.';
        }

        return $body;
    }
}
