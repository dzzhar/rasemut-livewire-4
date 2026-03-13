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
            ExportColumn::make('attendance_type')->label('Tipe Presensi'),
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
