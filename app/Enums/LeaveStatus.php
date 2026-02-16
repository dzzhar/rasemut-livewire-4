<?php

namespace App\Enums;

enum LeaveStatus: string
{
    case Pending = 'pending';
    case Disetujui = 'disetujui';
    case Ditolak = 'ditolak';

    public function badgeColor(): string
    {
        return match ($this) {
            self::Pending => 'yellow',
            self::Disetujui => 'green',
            self::Ditolak => 'red',
        };
    }
}
