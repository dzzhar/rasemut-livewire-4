<?php

namespace App\Filament\Resources\Permissions;

use App\Filament\Resources\Permissions\Pages\ManagePermissions;
use App\Models\Permission;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;
    protected static ?string $navigationLabel, $modelLabel = 'Data Izin';
    protected static string|UnitEnum|null $navigationGroup = "Kehadiran";
    protected static ?int $navigationSort = 2;

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('permission_date')
                    ->label('Tanggal Izin')
                    ->dateTime('l, d M Y H:i:s')
                    ->suffix(' WIB'),
                TextEntry::make('employee.fullname')
                    ->label('Nama Karyawan'),
                TextEntry::make('permission_type')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'izin' => 'warning',
                        'sakit' => 'primary',
                        default => 'danger'
                    }),
                TextEntry::make('status')
                    ->badge()
                    ->color('success'),
                TextEntry::make('description')
                    ->label('Keterangan')
                    ->placeholder('-')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('permission_date', 'desc')
            ->columns([
                TextColumn::make('permission_date')
                    ->label('Tanggal Izin')
                    ->dateTime('l, d M Y H:i:s')
                    ->suffix(' WIB')
                    ->sortable(),
                TextColumn::make('employee.fullname')
                    ->label('Nama Karyawan')
                    ->searchable(),
                TextColumn::make('permission_type')
                    ->label('Jenis Izin')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'izin' => 'warning',
                        'sakit' => 'primary',
                        default => 'danger'
                    })
                    ->alignCenter()
                    ->sortable(),
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
            'index' => ManagePermissions::route('/'),
        ];
    }
}
