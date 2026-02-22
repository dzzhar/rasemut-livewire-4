<?php

namespace App\Filament\Resources\Attendances;

use App\Filament\Resources\Attendances\Pages\ManageAttendances;
use App\Models\Attendance;
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

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::FingerPrint;
    protected static ?string $navigationLabel, $modelLabel = 'Data Presensi';
    protected static string|UnitEnum|null $navigationGroup = "Kehadiran";
    protected static ?int $navigationSort = 1;

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('employee.fullname')
                    ->label('Nama Karyawan'),
                TextEntry::make('attendance_date')
                    ->label('Tanggal Presensi')
                    ->dateTime()
                    ->suffix(' WIB'),
                TextEntry::make('attendance_type')
                    ->label('Presensi')
                    ->badge()
                    ->color(fn($state) => $state === 'masuk' ? 'success' : 'danger'),
                TextEntry::make('status')
                    ->badge()
                    ->color(fn($state) => $state->filamentBadgeColor()),
                TextEntry::make('description')
                    ->label('Keterangan')
                    ->placeholder('-')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('attendance_date')
                    ->label('Tanggal Presensi')
                    ->dateTime('l, d M Y H:i:s')
                    ->suffix(' WIB')
                    ->sortable(),
                TextColumn::make('employee.fullname')
                    ->label('Nama Karyawan')
                    ->searchable(),
                TextColumn::make('attendance_type')
                    ->label('Presensi')
                    ->badge()
                    ->color(fn($state) => $state === 'masuk' ? 'success' : 'danger')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn($state) => $state?->filamentBadgeColor())
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
            'index' => ManageAttendances::route('/'),
        ];
    }
}
