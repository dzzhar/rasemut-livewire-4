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
                ->fileName(fn() => 'karyawan-' . now()->format('Ymd_His'))
                ->color('primary')
                ->modifyQueryUsing(function (Builder $query, array $data) {
                    return $query
                        // menampilkan data yang masih cuti setelah atau pada tanggal mulai yang dipilih
                        ->when(
                            $data['start_period'] ?? null,
                            fn($q) => $q->whereDate('end_date', '>=', $data['start_period'])
                        )
                        // menampilkan data yang sudah cuti sebelum atau pada tanggal selesai yang dipilih
                        ->when(
                            $data['end_periode'] ?? null,
                            fn($q) => $q->whereDate('start_date', '<=', $data['end_periode'])
                        )
                        ->when(
                            filled($data['status'] ?? null),
                            fn($q) => $q->where('status', $data['status'])
                        );
                })
                ->schema([
                    DatePicker::make('start_period')->label('Periode Awal'),
                    DatePicker::make('end_periode')->label('Periode Akhir'),
                    Select::make('status')->options([
                        'pending' => 'Pending',
                        'disetujui' => 'Disetujui',
                        'ditolak' => 'Ditolak',
                    ])->placeholder('Semua Status')
                ])
        ];
    }
}
