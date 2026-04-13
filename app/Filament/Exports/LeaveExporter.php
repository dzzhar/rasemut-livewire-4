<?php

namespace App\Filament\Exports;

use App\Models\Leave;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class LeaveExporter extends Exporter
{
    protected static ?string $model = Leave::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('employee.fullname')->label('Nama Karyawan'),
            ExportColumn::make('request_date')->label('Tanggal Pengajuan'),
            ExportColumn::make('leave_code')->label('Kode Cuti'),
            ExportColumn::make('start_date')->label('Tanggal Mulai'),
            ExportColumn::make('end_date')->label('Tanggal Akhir'),
            ExportColumn::make('status'),
            ExportColumn::make('description')->label('Keterangan'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Ekspor cuti Anda telah selesai. ' . Number::format($export->successful_rows) . ' baris berhasil diekspor.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' baris gagal diekspor.';
        }

        return $body;
    }
}
