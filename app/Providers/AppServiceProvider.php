<?php

namespace App\Providers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Support\Facades\FilamentTimezone;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        FilamentTimezone::set('Asia/Jakarta');

        View::share('menuItems', [
            ['label' => 'Beranda', 'icon' => 'finger-print', 'href' => '/', 'match' => '/',],
            ['label' => 'Perizinan', 'icon' => 'calendar-days', 'href' => 'permission', 'match' => 'permission*',],
            ['label' => 'Ajukan Cuti', 'icon' => 'document-text', 'href' => 'leave', 'match' => 'leave*',],
        ]);

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
