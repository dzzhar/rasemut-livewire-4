<?php

namespace App\Filament\Exports;

use App\Models\Permission;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class PermissionExporter extends Exporter
{
    protected static ?string $model = Permission::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('employee.fullname')->label('Nama Karyawan'),
            ExportColumn::make('permission_date')->label('Tanggal Izin'),
            ExportColumn::make('permission_type')->label('Tipe Izin'),
            ExportColumn::make('status'),
            ExportColumn::make('description')->label('Keterangan'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Ekspor izin Anda telah selesai. ' . Number::format($export->successful_rows) . ' baris berhasil diekspor.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' baris gagal diekspor.';
        }

        return $body;
    }
}
