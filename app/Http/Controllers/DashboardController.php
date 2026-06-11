<?php

namespace App\Http\Controllers;

use App\Enums\IntakeStatus;
use App\Models\Patient;
use App\Services\NotificationService;
use App\Services\PatientService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private readonly NotificationService $notifications,
        private readonly PatientService $patients,
    ) {}

    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->isPatient()) {
            return redirect()->route('patient.dashboard');
        }

        $patients = Patient::query()->get();

        $alerts = $this->notifications->dashboardAlerts();
        $upcoming = $alerts->where('status', IntakeStatus::Upcoming)->values();
        $missed = $alerts->where('status', IntakeStatus::Missed)->values();

        return view('nurse.dashboard', [
            'totalPatients' => $patients->count(),
            'malePatients' => $patients->where('gender', 'laki-laki')->count(),
            'femalePatients' => $patients->where('gender', 'perempuan')->count(),
            'upcomingAlerts' => $upcoming,
            'missedAlerts' => $missed,
            'recentIntakeLogs' => $this->patients->latestIntakeHistory(),
        ]);
    }
}
