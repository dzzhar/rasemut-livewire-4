<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;

class Settings extends Page
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.settings';
    protected static bool $shouldRegisterNavigation = false;

    protected ?string $heading = 'Pengaturan Profil';

    public ?array $data = [];

    public function schema(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('name')
                    ->label('Nama')
                    ->required(),
                Textarea::make('description')
                    ->label('Deskripsi'),
            ])->statePath('data');
    }
}
