<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_NURSE = 'perawat';

    public const ROLE_PATIENT = 'pasien';

    public const TIMEZONES = [
        'Asia/Jakarta' => 'WIB - Waktu Indonesia Barat',
        'Asia/Makassar' => 'WITA - Waktu Indonesia Tengah',
        'Asia/Jayapura' => 'WIT - Waktu Indonesia Timur',
    ];

    public const TIMEZONE_ABBREVIATIONS = [
        'Asia/Jakarta' => 'WIB',
        'Asia/Makassar' => 'WITA',
        'Asia/Jayapura' => 'WIT',
    ];

    protected $fillable = [
        'name',
        'email',
        'role',
        'password',
        'password_changed',
        'plain_password',
        'timezone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'password_changed' => 'boolean',
        ];
    }

    public function patient(): HasOne
    {
        return $this->hasOne(Patient::class);
    }

    public function isNurse(): bool
    {
        return $this->role === self::ROLE_NURSE;
    }

    public function isPatient(): bool
    {
        return $this->role === self::ROLE_PATIENT;
    }

    public function timezoneAbbr(): string
    {
        return self::TIMEZONE_ABBREVIATIONS[$this->timezone] ?? 'WIB';
    }
}
