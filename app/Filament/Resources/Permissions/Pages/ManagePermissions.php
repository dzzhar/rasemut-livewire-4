<?php

namespace App\Filament\Resources\Permissions\Pages;

use App\Filament\Exports\PermissionExporter;
use App\Filament\Resources\Permissions\PermissionResource;
use Filament\Actions\ExportAction;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Actions\Exports\Models\Export;
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
                ->fileName(fn(Export $export): string => "izin-karyawan-{$export->created_at}")
                ->color('primary')
                ->schema([
                    DatePicker::make('start_period')->label('Periode Awal'),
                    DatePicker::make('end_period')->label('Periode Akhir'),
                    Select::make('permission_type')->label('Tipe Izin')->options([
                        'izin' => 'Izin',
                        'sakit' => 'Sakit',
                    ])->placeholder('Semua Tipe Izin')
                ])
                ->modifyQueryUsing(function (Builder $query, array $data) {
                    return $query
                        ->when(
                            $data['start_period'],
                            fn($q) => $q->whereDate('permission_date', '>=', $data['start_period'])
                        )
                        ->when(
                            $data['end_period'],
                            fn($q) => $q->whereDate('permission_date', '<=', $data['end_period'])
                        )
                        ->when(
                            filled($data['permission_type'] ?? null),
                            fn($q) => $q->where('permission_type', $data['permission_type'])
                        );
                })
        ];
    }
}
