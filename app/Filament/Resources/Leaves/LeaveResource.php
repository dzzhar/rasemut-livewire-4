<?php

namespace App\Filament\Resources\Leaves;

use App\Filament\Resources\Leaves\Pages\ManageLeaves;
use App\Models\Leave;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
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

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('request_date')
                    ->label('Tanggal Pengajuan')
                    ->dateTime('l, d M Y H:s:i')
                    ->suffix(' WIB'),
                TextEntry::make('employee.fullname')
                    ->label('Nama Karyawan')
                    ->label('Employee'),
                TextEntry::make('leave_code')
                    ->label('Kode Cuti'),
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
                    ->placeholder('-')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('request_date')
                    ->label('Tanggal Pengajuan')
                    ->date('l, d F Y')
                    ->sortable(),
                TextColumn::make('employee.fullname')
                    ->label('Nama Karyawan')
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
                        'pending' => 'Pending',
                        'disetujui' => 'Disetujui',
                        'ditolak' => 'Ditolak'
                    ])
                    ->native(false)
                    ->selectablePlaceholder(false)
                    ->rules(['required']),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageLeaves::route('/'),
        ];
    }
}
