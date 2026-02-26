<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class AttendanceSettings extends Page
{
    protected string $view = 'filament.pages.attendance-settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;
    protected static ?string $navigationLabel = "Pengaturan Presensi";
    protected static string|UnitEnum|null $navigationGroup = "Master";
    protected static ?int $navigationSort = 3;
}
