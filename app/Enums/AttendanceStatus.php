<?php

namespace App\Enums;

enum AttendanceStatus: string
{
    case TepatWaktu = "tepat waktu";
    case Terlambat = "terlambat";
    case PulangCepat = "pulang cepat";
    case AkhirShift = "akhir shift";
    case Lembur  = "lembur";
    case TidakAbsen = "tidak absen";

    public function badgeColor(): string
    {
        return match ($this) {
            self::TepatWaktu => 'green',
            self::Terlambat => 'orange',
            self::PulangCepat => 'yellow',
            self::AkhirShift => 'green',
            self::Lembur => 'blue',
            self::TidakAbsen => 'red',
        };
    }

    public function badgeIcon(): string
    {
        return match ($this) {
            self::TepatWaktu => 'check-circle',
            self::Terlambat => 'exclamation-circle',
            self::PulangCepat => 'information-circle',
            self::AkhirShift => 'check-circle',
            self::Lembur => 'clock',
            self::TidakAbsen => 'x-circle',
        };
    }

    public function badgeAccent(): string
    {
        return match ($this) {
            self::TepatWaktu => 'text-green-800 dark:text-green-200',
            self::Terlambat => 'text-orange-700 dark:text-orange-200',
            self::PulangCepat => 'text-yellow-800 dark:text-yellow-200',
            self::AkhirShift => 'text-green-800 dark:text-green-200',
            self::Lembur => 'text-blue-800 dark:text-blue-200',
            self::TidakAbsen => 'text-red-700 dark:text-red-200',
        };
    }
}
