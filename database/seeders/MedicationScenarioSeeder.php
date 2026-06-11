<?php

namespace Database\Seeders;

use App\Enums\IntakeStatus;
use App\Models\Medicine;
use App\Models\MedicineIntakeLog;
use App\Models\Patient;
use App\Models\User;
use App\Services\NotificationService;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MedicationScenarioSeeder extends Seeder
{
    private const TIMEZONE = 'Asia/Makassar';

    public function run(NotificationService $notifications): void
    {
        $now = CarbonImmutable::now(self::TIMEZONE);

        DB::transaction(function () use ($notifications, $now) {
            $user = User::query()->updateOrCreate(
                ['email' => 'pasien.demo@nodebat.local'],
                [
                    'name' => 'Pasien Demo Obat',
                    'role' => User::ROLE_PATIENT,
                    'password' => 'password',
                    'plain_password' => 'password',
                    'password_changed' => true,
                    'timezone' => self::TIMEZONE,
                ],
            );

            $patient = Patient::query()->updateOrCreate(
                ['patient_code' => 'PSN-DEMO'],
                [
                    'user_id' => $user->id,
                    'name' => 'Pasien Demo Obat',
                    'address' => 'Alamat pasien demo',
                    'birth_place' => 'Makassar',
                    'birth_date' => '1990-01-01',
                    'gender' => 'Laki-laki',
                ],
            );

            $scenarios = [
                [
                    'name' => 'Obat Minum Sekarang',
                    'dose' => '500mg',
                    'route' => 'Oral',
                    'interval_hours' => 1,
                    'times_per_day' => 10,
                    'notes' => 'Gunakan dosis kuning terdekat untuk mencoba tombol Sudah Diminum.',
                    'status' => IntakeStatus::Upcoming,
                ],
                [
                    'name' => 'Obat Notifikasi',
                    'dose' => '250mg',
                    'route' => 'Oral',
                    'interval_hours' => 3,
                    'times_per_day' => 5,
                    'notes' => 'Skenario notifikasi mendekati waktu minum obat.',
                    'status' => IntakeStatus::Upcoming,
                ],
                [
                    'name' => 'Obat Terlewat',
                    'dose' => '10ml',
                    'route' => 'Oral',
                    'interval_hours' => 2,
                    'times_per_day' => 8,
                    'notes' => 'Skenario dosis yang sudah melewati jadwal.',
                    'status' => IntakeStatus::Missed,
                ],
                [
                    'name' => 'Obat Belum Diminum',
                    'dose' => '5mg',
                    'route' => 'Oral',
                    'interval_hours' => 4,
                    'times_per_day' => 4,
                    'notes' => 'Skenario dosis yang masih lebih dari satu jam.',
                    'status' => IntakeStatus::Pending,
                ],
            ];

            foreach ($scenarios as $scenario) {
                $medicine = Medicine::query()->updateOrCreate(
                    [
                        'patient_id' => $patient->id,
                        'name' => $scenario['name'],
                    ],
                    [
                        'dose' => $scenario['dose'],
                        'route' => $scenario['route'],
                        'interval_hours' => $scenario['interval_hours'],
                        'times_per_day' => $scenario['times_per_day'],
                        'first_dose_at' => $now->startOfDay()->subDays(2)->utc(),
                        'quantity' => 30,
                        'notes' => $scenario['notes'],
                    ],
                );

                $medicine->intakeLogs()->delete();

                $this->seedScenarioLogs(
                    $medicine,
                    $notifications->dosesForDay($medicine, $now, self::TIMEZONE),
                    $scenario['status'],
                    $now,
                );
            }
        });
    }

    /**
     * @param  Collection<int, array{index:int, scheduled_at:CarbonImmutable}>  $doses
     */
    private function seedScenarioLogs(
        Medicine $medicine,
        Collection $doses,
        IntakeStatus $scenarioStatus,
        CarbonImmutable $now,
    ): void {
        $target = match ($scenarioStatus) {
            IntakeStatus::Upcoming => $doses->first(
                fn (array $dose): bool => $dose['scheduled_at']->greaterThanOrEqualTo($now)
                    && $now->diffInMinutes($dose['scheduled_at'], false) <= NotificationService::UPCOMING_WINDOW_MINUTES,
            ),
            IntakeStatus::Pending => $doses->first(
                fn (array $dose): bool => $now->diffInMinutes($dose['scheduled_at'], false) > NotificationService::UPCOMING_WINDOW_MINUTES,
            ),
            IntakeStatus::Missed => $doses->last(
                fn (array $dose): bool => $dose['scheduled_at']->lessThan($now),
            ),
            IntakeStatus::Taken => null,
        };

        foreach ($doses as $dose) {
            if ($dose['scheduled_at']->greaterThanOrEqualTo($now)) {
                continue;
            }

            if ($target !== null && $dose['scheduled_at']->equalTo($target['scheduled_at'])) {
                continue;
            }

            MedicineIntakeLog::query()->create([
                'medicine_id' => $medicine->id,
                'scheduled_at' => $dose['scheduled_at']->utc(),
                'status' => IntakeStatus::Taken->value,
                'taken_at' => $dose['scheduled_at']->utc(),
            ]);
        }

        if ($scenarioStatus === IntakeStatus::Missed && $target !== null) {
            MedicineIntakeLog::query()->create([
                'medicine_id' => $medicine->id,
                'scheduled_at' => $target['scheduled_at']->utc(),
                'status' => IntakeStatus::Missed->value,
                'missed_at' => $now->utc(),
            ]);
        }
    }
}
