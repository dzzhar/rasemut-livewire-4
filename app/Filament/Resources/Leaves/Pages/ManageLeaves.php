<?php

namespace App\Filament\Resources\Leaves\Pages;

use App\Filament\Exports\LeaveExporter;
use App\Filament\Resources\Leaves\LeaveResource;
use Filament\Actions\ExportAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Builder;

class ManageLeaves extends ManageRecords
{
    protected static string $resource = LeaveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make()
                ->exporter(LeaveExporter::class)
                ->columnMapping(false)
                ->fileName(fn() => 'cuti-karyawan-' . now()->format('Ymd_His'))
                ->color('primary')
                ->modifyQueryUsing(function (Builder $query, array $data) {
                    // export period
                    $start = $data['start_period'] ?? now()->startOfMonth()->toDateString();
                    $end = $data['end_period'] ?? now()->endOfMonth()->toDateString();

                    return $query
                        ->when(
                            $start,
                            fn($q) => $q->whereDate('end_date', '>=', $start)
                        )
                        ->when(
                            $end,
                            fn($q) => $q->whereDate('start_date', '<=', $end)
                        )
                        ->when(
                            filled($data['status'] ?? null),
                            fn($q) => $q->where('status', $data['status'])
                        );
                })
                ->schema([
                    DatePicker::make('start_period')->label('Periode Awal'),
                    DatePicker::make('end_period')->label('Periode Akhir'),
                    Select::make('status')->options([
                        'pending' => 'Pending',
                        'disetujui' => 'Disetujui',
                        'ditolak' => 'Ditolak',
                    ])->placeholder('Semua Status')->nullable()
                ])
        ];
    }
}
