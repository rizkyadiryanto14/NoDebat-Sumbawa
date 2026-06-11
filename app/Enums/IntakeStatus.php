<?php

namespace App\Enums;

enum IntakeStatus: string
{
    case Pending = 'pending';
    case Upcoming = 'upcoming';
    case Taken = 'taken';
    case Missed = 'missed';

    public function color(): string
    {
        return match ($this) {
            self::Taken => 'green',
            self::Upcoming => 'yellow',
            self::Missed => 'red',
            self::Pending => 'purple',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Taken => 'Sudah Diminum',
            self::Upcoming => 'Mendekati Waktu',
            self::Missed => 'Terlewat',
            self::Pending => 'Belum Diminum',
        };
    }
}
