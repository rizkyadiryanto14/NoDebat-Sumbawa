<?php

namespace App\Services;

use App\Enums\IntakeStatus;
use App\Models\Medicine;
use App\Models\MedicineIntakeLog;
use App\Models\Patient;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class NotificationService
{
    public const UPCOMING_WINDOW_MINUTES = 60;

    public const ACTIVE_START = '07:30';

    public const ACTIVE_END = '22:00';

    public const SCHEDULE_HORIZON_DAYS = 14;

    /**
     * Doses for a medicine on a specific calendar day in the patient's timezone.
     * Doses are anchored at 07:30 and spaced by interval_hours, filtered to
     * stay within the active window (07:30-22:00) and not before the countdown
     * from first_dose_at has elapsed.
     *
     * @return Collection<int, array{index:int, scheduled_at:CarbonImmutable}>
     */
    public function dosesForDay(Medicine $medicine, CarbonImmutable $day, string $timezone): Collection
    {
        $date = $day->setTimezone($timezone)->toDateString();
        $dayStart = CarbonImmutable::parse($date.' '.self::ACTIVE_START, $timezone);
        $dayEnd = CarbonImmutable::parse($date.' '.self::ACTIVE_END, $timezone);
        $countdownEnd = CarbonImmutable::instance($medicine->first_dose_at)
            ->setTimezone($timezone)
            ->addHours($medicine->interval_hours);

        return collect(range(0, $medicine->times_per_day - 1))
            ->map(fn (int $i) => [
                'index' => $i,
                'scheduled_at' => $dayStart->addHours($i * $medicine->interval_hours),
            ])
            ->filter(fn (array $dose) => $dose['scheduled_at']->lessThanOrEqualTo($dayEnd)
                && $dose['scheduled_at']->greaterThanOrEqualTo($countdownEnd))
            ->values();
    }

    /**
     * Resolve the status of a specific scheduled dose, persisting a Missed
     * log when the scheduled time has passed without intake.
     *
     * @return array{status:IntakeStatus,log:?MedicineIntakeLog,scheduled_at:CarbonImmutable}
     */
    public function resolveDoseStatus(Medicine $medicine, CarbonImmutable $scheduledAt, ?CarbonImmutable $now = null): array
    {
        $now ??= CarbonImmutable::now();
        $storedScheduledAt = $scheduledAt->utc();

        $log = MedicineIntakeLog::query()
            ->where('medicine_id', $medicine->id)
            ->where('scheduled_at', $storedScheduledAt)
            ->first();

        if ($log?->taken_at !== null) {
            return ['status' => IntakeStatus::Taken, 'log' => $log, 'scheduled_at' => $scheduledAt];
        }

        if ($now->greaterThan($scheduledAt)) {
            $log = $this->ensureMissed($medicine, $scheduledAt, $now, $log);

            return ['status' => IntakeStatus::Missed, 'log' => $log, 'scheduled_at' => $scheduledAt];
        }

        if ($now->diffInMinutes($scheduledAt, false) <= self::UPCOMING_WINDOW_MINUTES) {
            return ['status' => IntakeStatus::Upcoming, 'log' => $log, 'scheduled_at' => $scheduledAt];
        }

        return ['status' => IntakeStatus::Pending, 'log' => $log, 'scheduled_at' => $scheduledAt];
    }

    /**
     * @return Collection<int, array{index:int, scheduled_at:CarbonImmutable, status:IntakeStatus, log:?MedicineIntakeLog}>
     */
    public function todayDoses(Medicine $medicine, string $timezone, ?CarbonImmutable $now = null): Collection
    {
        $now ??= CarbonImmutable::now();

        return $this->dosesForDay($medicine, $now->setTimezone($timezone), $timezone)
            ->map(function (array $dose) use ($medicine, $now) {
                $resolved = $this->resolveDoseStatus($medicine, $dose['scheduled_at'], $now);

                return [
                    'index' => $dose['index'],
                    'scheduled_at' => $dose['scheduled_at'],
                    'status' => $resolved['status'],
                    'log' => $resolved['log'],
                ];
            });
    }

    /**
     * Find the next scheduled dose across the horizon that is not yet taken.
     *
     * @return array{medicine:Medicine, scheduled_at:CarbonImmutable, status:IntakeStatus}|null
     */
    public function nextDoseForMedicine(Medicine $medicine, string $timezone, ?CarbonImmutable $now = null): ?array
    {
        $now ??= CarbonImmutable::now();
        $start = $now->setTimezone($timezone)->startOfDay();

        for ($offset = 0; $offset < self::SCHEDULE_HORIZON_DAYS; $offset++) {
            $day = $start->addDays($offset);
            foreach ($this->dosesForDay($medicine, $day, $timezone) as $dose) {
                if ($dose['scheduled_at']->lessThan($now)) {
                    continue;
                }
                $log = MedicineIntakeLog::query()
                    ->where('medicine_id', $medicine->id)
                    ->where('scheduled_at', $dose['scheduled_at']->utc())
                    ->first();
                if ($log?->taken_at !== null) {
                    continue;
                }
                $resolved = $this->resolveDoseStatus($medicine, $dose['scheduled_at'], $now);

                return [
                    'medicine' => $medicine,
                    'scheduled_at' => $dose['scheduled_at'],
                    'status' => $resolved['status'],
                ];
            }
        }

        return null;
    }

    public function markTaken(Medicine $medicine, CarbonImmutable $scheduledAt, ?CarbonImmutable $now = null): MedicineIntakeLog
    {
        $now ??= CarbonImmutable::now();
        $storedScheduledAt = $scheduledAt->utc();

        return MedicineIntakeLog::query()->updateOrCreate(
            [
                'medicine_id' => $medicine->id,
                'scheduled_at' => $storedScheduledAt,
            ],
            [
                'status' => IntakeStatus::Taken->value,
                'taken_at' => $now,
                'missed_at' => null,
            ],
        );
    }

    /**
     * @return Collection<int, array{patient:Patient, medicine:Medicine, status:IntakeStatus, scheduled_at:CarbonImmutable, minutes_overdue:int|null, minutes_until_dose:int|null}>
     */
    public function dashboardAlerts(?CarbonImmutable $now = null): Collection
    {
        $now ??= CarbonImmutable::now();

        return Patient::query()
            ->with(['user', 'medicines'])
            ->get()
            ->flatMap(function (Patient $patient) use ($now) {
                $timezone = $patient->user->timezone ?? 'Asia/Jakarta';

                return $patient->medicines->flatMap(fn (Medicine $medicine) => $this->todayDoses($medicine, $timezone, $now)
                    ->map(fn (array $dose) => [
                        'patient' => $patient,
                        'medicine' => $medicine,
                        'status' => $dose['status'],
                        'scheduled_at' => $dose['scheduled_at'],
                        'minutes_overdue' => $dose['status'] === IntakeStatus::Missed
                            ? (int) $dose['scheduled_at']->diffInMinutes($now)
                            : null,
                        'minutes_until_dose' => $dose['status'] === IntakeStatus::Upcoming
                            ? (int) $now->diffInMinutes($dose['scheduled_at'])
                            : null,
                    ]));
            })
            ->values();
    }

    private function ensureMissed(Medicine $medicine, CarbonImmutable $scheduledAt, CarbonImmutable $now, ?MedicineIntakeLog $log): MedicineIntakeLog
    {
        if ($log === null) {
            return MedicineIntakeLog::query()->create([
                'medicine_id' => $medicine->id,
                'scheduled_at' => $scheduledAt->utc(),
                'status' => IntakeStatus::Missed->value,
                'missed_at' => $now,
            ]);
        }

        if ($log->status !== IntakeStatus::Missed->value) {
            $log->update([
                'status' => IntakeStatus::Missed->value,
                'missed_at' => $log->missed_at ?? $now,
            ]);
        }

        return $log->refresh();
    }
}
