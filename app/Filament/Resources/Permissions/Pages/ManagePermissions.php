<?php

namespace App\Filament\Resources\Permissions\Pages;

use App\Filament\Exports\PermissionExporter;
use App\Filament\Resources\Permissions\PermissionResource;
use Filament\Actions\ExportAction;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Resources\Pages\ManageRecords;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;

class ManagePermissions extends ManageRecords
{
    protected static string $resource = PermissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make()
                ->exporter(PermissionExporter::class)
                ->columnMapping(false)
                ->formats([ExportFormat::Csv])
                ->fileName(fn() => 'izin-karyawan-' . now()->format('Ymd_His'))
                ->color('primary')
                ->modifyQueryUsing(function (Builder $query, array $data) {
                    $start = $data['start_period'] ?? now()->startOfMonth()->toDateString();
                    $end = $data['end_period'] ?? now()->endOfMonth()->toDateString();
                    
                    return $query
                        ->when(
                            $start,
                            fn($q) => $q->whereDate('permission_date', '>=', $start)
                        )
                        ->when(
                            $end,
                            fn($q) => $q->whereDate('permission_date', '<=', $end)
                        )
                        ->when(
                            filled($data['permission_type'] ?? null),
                            fn($q) => $q->where('permission_type', $data['permission_type'])
                        );
                })
                ->schema([
                    DatePicker::make('start_period')->label('Periode Awal'),
                    DatePicker::make('end_period')->label('Periode Akhir'),
                    Select::make('permission_type')->label('Tipe Izin')->options([
                        'izin' => 'Izin',
                        'sakit' => 'Sakit',
                    ])->placeholder('Semua Tipe Izin')->nullable(),
                ])
        ];
    }
}
