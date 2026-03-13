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
            ExportColumn::make('employee_code')->label('Kode Karyawan'),
            ExportColumn::make('position.name')->label('Jabatan'),
            ExportColumn::make('ontime_count')->label('Masuk Tepat Waktu'),
            ExportColumn::make('late_count')->label('Masuk Terlambat'),
            ExportColumn::make('shiftend_count')->label('Pulang Akhir Shift'),
            ExportColumn::make('early_count')->label('Pulang Cepat'),
            ExportColumn::make('overtime_count')->label('Lembur'),
            ExportColumn::make('absent_count')->label('Tidak Absen'),
            ExportColumn::make('permissions_count')->label('Total Izin'),
            ExportColumn::make('leaves_count')->label('Total Cuti'),
            ExportColumn::make('created_at')->label('Akun Dibuat')
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
