<?php

namespace App\Providers;

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
        View::share('menuItems', [
            ['label' => 'Beranda', 'icon' => 'finger-print', 'href' => '/', 'match' => '/',],
            ['label' => 'Perizinan', 'icon' => 'calendar-days', 'href' => 'permission', 'match' => 'permission*',],
            ['label' => 'Ajukan Cuti', 'icon' => 'document-text', 'href' => 'leave', 'match' => 'leave*',],
        ]);
    }
}
