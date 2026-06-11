<?php

use App\Models\MedicineIntakeLog;
use App\Models\Patient;
use App\Models\User;
use Carbon\CarbonImmutable;
use Database\Seeders\MedicationScenarioSeeder;

afterEach(function () {
    CarbonImmutable::setTestNow();
});

it('shows the complete medicine history on the patient detail page', function () {
    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-11 15:45:00', 'Asia/Makassar'));

    $this->seed(MedicationScenarioSeeder::class);

    $nurse = User::factory()->create([
        'role' => User::ROLE_NURSE,
        'timezone' => 'Asia/Jakarta',
    ]);
    $patient = Patient::query()->where('patient_code', 'PSN-DEMO')->firstOrFail();
    $expectedHistoryCount = MedicineIntakeLog::query()
        ->whereHas('medicine', fn ($query) => $query->whereBelongsTo($patient))
        ->count();

    $response = $this->actingAs($nurse)->get(route('nurse.patients.show', $patient));

    $response
        ->assertSuccessful()
        ->assertSee('Riwayat Minum Obat')
        ->assertSee('Obat Terlewat')
        ->assertSee('Tercatat terlewat')
        ->assertViewHas('intakeLogs', fn ($logs) => $logs->count() === $expectedHistoryCount);
});

it('shows only two interactive latest histories on the nurse dashboard', function () {
    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-11 15:45:00', 'Asia/Makassar'));

    $this->seed(MedicationScenarioSeeder::class);

    $nurse = User::factory()->create([
        'role' => User::ROLE_NURSE,
        'timezone' => 'Asia/Jakarta',
    ]);
    $patient = Patient::query()->where('patient_code', 'PSN-DEMO')->firstOrFail();
    $historyUrl = route('nurse.patients.show', $patient).'#riwayat-minum-obat';

    $response = $this->actingAs($nurse)->get(route('dashboard'));

    $response
        ->assertSuccessful()
        ->assertSee('Riwayat Minum Obat Terbaru')
        ->assertSee('Buka riwayat')
        ->assertViewHas('recentIntakeLogs', fn ($logs) => $logs->count() === 2);

    expect(substr_count($response->getContent(), $historyUrl))->toBe(2);
});
