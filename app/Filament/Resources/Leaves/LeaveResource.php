<?php

namespace App\Filament\Resources\Leaves;

use App\Enums\LeaveStatus;
use App\Filament\Resources\Leaves\Pages\ManageLeaves;
use App\Models\Leave;
use App\Services\LeaveService;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class LeaveResource extends Resource
{
    protected static ?string $model = Leave::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDateRange;
    protected static ?string $navigationLabel, $modelLabel = "Data Cuti";
    protected static string|UnitEnum|null $navigationGroup = "Kehadiran";
    protected static ?int $navigationSort = 3;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::where('status', 'pending')
            ->count() > 0 ? 'warning' : 'primary';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return static::getModel()::where('status', 'pending')
            ->count() > 0 ? 'Menunggu persetujuan Anda' : 'Semua telah Anda setujui';
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('leave_code')
                    ->label('Kode Cuti'),
                TextEntry::make('request_date')
                    ->label('Tanggal Pengajuan')
                    ->dateTime('l, d F Y'),
                TextEntry::make('employee.fullname')
                    ->label('Karyawan'),
                TextEntry::make('employee.leave_remaining')
                    ->label('Sisa Kuota Cuti')
                    ->suffix(' hari'),
                TextEntry::make('start_date')
                    ->label('Tanggal Mulai')
                    ->date(),
                TextEntry::make('end_date')
                    ->label('Tanggal Selesai')
                    ->date(),
                TextEntry::make('status')
                    ->badge()
                    ->color(fn($state) => $state?->filamentBadgeColor()),
                TextEntry::make('description')
                    ->label('Keterangan')
                    ->placeholder('-')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                $query->orderByRaw("
                    CASE status
                        WHEN 'pending' THEN 1
                        WHEN 'disetujui' THEN 2
                        WHEN 'ditolak' THEN 3
                    END ASC
                ")->orderBy('request_date', 'asc');
            })->columns([
                TextColumn::make('request_date')
                    ->label('Tanggal Pengajuan')
                    ->date('l, d F Y'),
                TextColumn::make('employee.fullname')
                    ->label('Karyawan')
                    ->searchable(),
                TextColumn::make('leave_code')
                    ->label('Kode Cuti')
                    ->searchable(),
                TextColumn::make('start_date')
                    ->label('Tanggal Mulai')
                    ->date(),
                TextColumn::make('end_date')
                    ->label('Tanggal Selesai')
                    ->date(),
                SelectColumn::make('status')
                    ->options([
                        'disetujui' => 'disetujui',
                        'ditolak' => 'ditolak',
                    ])
                    ->native(false)
                    ->selectablePlaceholder(false)
                    ->updateStateUsing(function ($record, $state) {
                        $map = [
                            'disetujui' => LeaveStatus::Disetujui,
                            'ditolak' => LeaveStatus::Ditolak,
                        ];

                        $newStatus = $map[$state] ?? $record->status;
                        $result = LeaveService::updateStatus($record, $newStatus);

                        $notification = Notification::make()
                            ->title($result['message']);

                        if ($result['success']) {
                            $notification->success();
                            $notification->send();
                            return $newStatus->value;
                        }

                        $notification->danger()->body($result['body'] ?? null)->send();

                        // kembalikan status lama jika gagal
                        return $record->status->value;
                    })
                    ->disabled(
                        fn($record) =>
                        $record->first_status_updated_at?->addHours(12)?->isPast() ?? false
                    )
            ])
            ->recordActions([
                ViewAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageLeaves::route('/'),
        ];
    }
}
