<?php

namespace App\Enums;

enum AttendanceStatus: string
{
    case Hadir = "hadir";
    case TidakHadir = "tidak_hadir";
    case TidakLengkap = "tidak_lengkap";

    public function badgeLabel(): string
    {
        return match ($this) {
            self::Hadir => 'hadir',
            self::TidakLengkap => 'tidak lengkap',
            self::TidakHadir => 'tidak absen',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Hadir => 'green',
            self::TidakLengkap => 'yellow',
            self::TidakHadir => 'red',
        };
    }

    public function badgeAccent(): string
    {
        return match ($this) {
            self::Hadir => 'text-green-800 dark:text-green-200',
            self::TidakLengkap => 'text-yellow-800 dark:text-yellow-200',
            self::TidakHadir => 'text-red-700 dark:text-red-200',
        };
    }

    public function filamentBadgeColor(): string
    {
        return match ($this) {
            self::Hadir => 'success',
            self::TidakLengkap => 'warning',
            self::TidakHadir => 'danger',
        };
    }
}
