<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Services\NotificationService;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MedicineLogController extends Controller
{
    public function __construct(private readonly NotificationService $notifications) {}

    public function dashboard(Request $request): View
    {
        $user = $request->user();
        $patient = $user->patient()->with('medicines')->firstOrFail();
        $timezone = $user->timezone;

        $medicines = $patient->medicines->map(fn (Medicine $medicine) => [
            'medicine' => $medicine,
            'doses' => $this->notifications->todayDoses($medicine, $timezone),
            'next' => $this->notifications->nextDoseForMedicine($medicine, $timezone),
        ]);

        return view('patient.dashboard', [
            'patient' => $patient,
            'medicines' => $medicines,
            'timezone' => $timezone,
        ]);
    }

    public function history(Request $request, Medicine $medicine): View
    {
        $patient = $request->user()->patient;
        abort_unless($patient && $medicine->patient_id === $patient->id, 403);

        $logs = $medicine->intakeLogs()
            ->orderByDesc('scheduled_at')
            ->limit(100)
            ->get();

        return view('patient.history', [
            'medicine' => $medicine,
            'logs' => $logs,
            'timezone' => $request->user()->timezone,
        ]);
    }

    public function markTaken(Request $request, Medicine $medicine): RedirectResponse
    {
        $patient = $request->user()->patient;
        abort_unless($patient && $medicine->patient_id === $patient->id, 403);

        $validated = $request->validate([
            'scheduled_at' => ['required', 'date'],
        ]);

        $this->notifications->markTaken($medicine, CarbonImmutable::parse($validated['scheduled_at']));

        return back()->with('status', 'Dosis ditandai sudah diminum.');
    }
}
