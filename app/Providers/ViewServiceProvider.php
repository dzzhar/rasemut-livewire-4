<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
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
        // menu items navigation
        View::share('menuItems', [
            ['label' => 'Beranda', 'icon' => 'finger-print', 'href' => '/', 'match' => '/',],
            ['label' => 'Izin/Sakit', 'icon' => 'document-text', 'href' => 'permission', 'match' => 'permission*',],
            ['label' => 'Cuti', 'icon' => 'calendar-days', 'href' => 'leave', 'match' => 'leave*',],
        ]);
    }
}
