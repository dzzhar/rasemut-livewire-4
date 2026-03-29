<?php

namespace App\Enums;

enum LeaveStatus: string
{
    case Pending = 'pending';
    case Disetujui = 'disetujui';
    case Ditolak = 'ditolak';

    public function badgeLabel(): string
    {
        return match ($this) {
            self::Pending => 'pending',
            self::Disetujui => 'disetujui',
            self::Ditolak => 'ditolak',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Pending => 'yellow',
            self::Disetujui => 'green',
            self::Ditolak => 'red',
        };
    }

    public function filamentBadgeColor(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Disetujui => 'success',
            self::Ditolak => 'danger',
        };
    }
}
