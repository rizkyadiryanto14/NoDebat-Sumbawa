<?php

use App\Enums\IntakeStatus;
use App\Models\Medicine;
use App\Models\User;
use App\Services\NotificationService;
use Carbon\CarbonImmutable;
use Database\Seeders\MedicationScenarioSeeder;

afterEach(function () {
    CarbonImmutable::setTestNow();
});

it('seeds valid medicine scenarios for the patient dashboard', function () {
    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-11 15:45:00', 'Asia/Makassar'));

    $this->seed(MedicationScenarioSeeder::class);

    $notifications = app(NotificationService::class);
    $medicines = Medicine::query()
        ->whereHas('patient.user', fn ($query) => $query->where('email', 'pasien.demo@nodebat.local'))
        ->get()
        ->keyBy('name');

    expect($medicines)->toHaveCount(4);

    $expectedStatuses = [
        'Obat Minum Sekarang' => IntakeStatus::Upcoming,
        'Obat Notifikasi' => IntakeStatus::Upcoming,
        'Obat Terlewat' => IntakeStatus::Missed,
        'Obat Belum Diminum' => IntakeStatus::Pending,
    ];

    foreach ($expectedStatuses as $medicineName => $expectedStatus) {
        $statuses = $notifications
            ->todayDoses($medicines->get($medicineName), 'Asia/Makassar')
            ->pluck('status');

        expect($statuses)->toContain($expectedStatus);
    }
});

it('shows the next dose inside every medicine card', function () {
    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-11 22:15:00', 'Asia/Makassar'));

    $this->seed(MedicationScenarioSeeder::class);

    $user = User::query()->where('email', 'pasien.demo@nodebat.local')->firstOrFail();
    $response = $this->actingAs($user)->get(route('patient.dashboard'));

    $response
        ->assertSuccessful()
        ->assertSee('Obat Minum Sekarang')
        ->assertSee('500mg')
        ->assertSee('Oral')
        ->assertSee('Friday, 12 June 2026')
        ->assertSee('07:30 WITA');

    expect(substr_count($response->getContent(), 'Dosis Berikutnya'))->toBe(4);
});

it('keeps a WITA dose marked as taken after the dashboard refreshes', function () {
    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-11 15:45:00', 'Asia/Makassar'));

    $this->seed(MedicationScenarioSeeder::class);

    $user = User::query()->where('email', 'pasien.demo@nodebat.local')->firstOrFail();
    $medicine = Medicine::query()->where('name', 'Obat Minum Sekarang')->firstOrFail();
    $notifications = app(NotificationService::class);
    $scheduledDose = $notifications
        ->todayDoses($medicine, 'Asia/Makassar')
        ->firstWhere('status', IntakeStatus::Upcoming);

    $this->actingAs($user)
        ->post(route('patient.medicines.mark-taken', $medicine), [
            'scheduled_at' => $scheduledDose['scheduled_at']->utc()->format('Y-m-d H:i:s'),
        ])
        ->assertRedirect();

    $status = $notifications
        ->todayDoses($medicine, 'Asia/Makassar')
        ->firstWhere('scheduled_at', $scheduledDose['scheduled_at'])['status'];

    expect($status)->toBe(IntakeStatus::Taken);
});
