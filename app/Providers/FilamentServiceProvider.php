<?php

namespace App\Providers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Support\Facades\FilamentTimezone;
use Illuminate\Support\ServiceProvider;

class FilamentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        FilamentTimezone::set('Asia/Jakarta');

        // filament forms attributes
        TextInput::configureUsing(function (TextInput $component): void {
            $component
                ->extraInputAttributes(['required' => false])
                ->validationAttribute(fn($component) => str($component->getLabel())->lower());;
        });

        Textarea::configureUsing(function (Textarea $component): void {
            $component
                ->extraInputAttributes(['required' => false])
                ->validationAttribute(fn($component) => str($component->getLabel())->lower());;
        });

        Select::configureUsing(function (Select $component): void {
            $component->native(false);
        });
    }
}
