<?php

namespace App\Filament\Resources\Positions;

use App\Filament\Resources\Positions\Pages\ManagePositions;
use App\Models\Position;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class PositionResource extends Resource
{
    protected static ?string $model = Position::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;
    protected static ?string $navigationLabel, $modelLabel = "Data Jabatan";
    protected static string|UnitEnum|null $navigationGroup = "Master";
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label('Jabatan')->required(),
                Textarea::make('description')->label('Keterangan')->autosize(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')->label('Jabatan'),
                TextEntry::make('description')->label('Keterangan'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama Jabatan')->searchable(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePositions::route('/'),
        ];
    }
}
