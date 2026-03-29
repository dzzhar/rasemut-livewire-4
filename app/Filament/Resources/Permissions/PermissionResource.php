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
use Illuminate\Support\Facades\Storage;
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
                    ->dateTime('l, d F Y'),
                TextEntry::make('employee.fullname')
                    ->label('Nama Karyawan'),
                TextEntry::make('permission_type')
                    ->label('Jenis Izin')
                    ->badge()
                    ->color(
                        fn($state) => match ($state) {
                            'izin' => 'warning',
                            'sakit' => 'primary',
                            default => 'danger'
                        }
                    ),
                TextEntry::make('status')
                    ->badge()
                    ->color('success'),
                TextEntry::make('description')
                    ->label('Keterangan')
                    ->placeholder('-'),
                TextEntry::make('file_path')
                    ->label('Bukti Izin/Sakit')
                    ->formatStateUsing(fn($state) => $state ? 'Lihat Bukti' : '-')
                    ->color('primary')
                    ->iconColor('primary')
                    ->placeholder('-')
                    ->url(fn($record) => $record->file_path
                        ? asset('storage/' . $record->file_path)
                        : null)
                    ->openUrlInNewTab()
                    ->icon(Heroicon::ArrowTopRightOnSquare)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('permission_date', 'desc')
            ->columns([
                TextColumn::make('permission_date')
                    ->label('Tanggal Izin')
                    ->dateTime('l, d F Y')
                    ->sortable(),
                TextColumn::make('employee.fullname')
                    ->label('Nama Karyawan')
                    ->searchable(),
                TextColumn::make('permission_type')
                    ->label('Jenis Izin')
                    ->badge()
                    ->color(
                        fn($state) => match ($state) {
                            'izin' => 'warning',
                            'sakit' => 'primary',
                            default => 'danger'
                        }
                    )
                    ->alignCenter()
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make(),
                DeleteAction::make()
                    ->before(function ($record) {
                        if ($record->file_path) {
                            Storage::disk('public')->delete($record->file_path);
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->before(
                            function ($records) {
                                foreach ($records as $record) {
                                    if ($record->file_path) {
                                        Storage::disk('public')->delete($record->file_path);
                                    }
                                }
                            }
                        ),
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
