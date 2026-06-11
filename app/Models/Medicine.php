<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medicine extends Model
{
    protected $fillable = [
        'patient_id',
        'name',
        'dose',
        'route',
        'interval_hours',
        'times_per_day',
        'first_dose_at',
        'quantity',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'interval_hours' => 'integer',
            'times_per_day' => 'integer',
            'first_dose_at' => 'datetime',
            'quantity' => 'integer',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function intakeLogs(): HasMany
    {
        return $this->hasMany(MedicineIntakeLog::class);
    }
}
