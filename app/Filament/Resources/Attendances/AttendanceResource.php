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
                    ->date('l, d F Y'),
                TextEntry::make('check_in')
                    ->label('Jam Masuk')
                    ->placeholder('-')
                    ->suffix(' WIB'),
                TextEntry::make('check_out')
                    ->label('Jam Keluar')
                    ->placeholder('-')
                    ->suffix(' WIB'),
                TextEntry::make('status')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state->badgeLabel())
                    ->color(fn($state) => $state->filamentBadgeColor()),
                TextEntry::make('description')
                    ->label('Keterangan')
                    ->placeholder('-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('attendance_date', 'desc')
            ->columns([
                TextColumn::make('attendance_date')
                    ->label('Tanggal Presensi')
                    ->date('l, d F Y')
                    ->sortable(),
                TextColumn::make('employee.fullname')
                    ->label('Nama Karyawan')
                    ->searchable(),
                TextColumn::make('check_in')
                    ->label('Jam Masuk')
                    ->suffix(' WIB')
                    ->placeholder('-'),
                TextColumn::make('check_out')
                    ->label('Jam Keluar')
                    ->suffix(' WIB')
                    ->placeholder('-'),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state?->badgeLabel())
                    ->color(fn($state) => $state?->filamentBadgeColor())
                    ->alignCenter()
                    ->sortable(),
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
            'index' => ManageAttendances::route('/'),
        ];
    }
}
