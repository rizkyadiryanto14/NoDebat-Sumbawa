<?php

namespace App\Http\Controllers\Nurse;

use App\Http\Controllers\Controller;
use App\Http\Requests\Nurse\StorePatientRequest;
use App\Http\Requests\Nurse\UpdatePatientRequest;
use App\Models\Patient;
use App\Services\PatientService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class PatientController extends Controller
{
    public function __construct(private readonly PatientService $patients) {}

    public function index(): View
    {
        return view('nurse.patients.index', [
            'patients' => $this->patients->allWithStats(),
        ]);
    }

    public function create(): View
    {
        return view('nurse.patients.create');
    }

    public function store(StorePatientRequest $request): RedirectResponse
    {
        $result = $this->patients->createPatient($request->validated());

        return redirect()
            ->route('nurse.patients.show', $result['patient'])
            ->with('credentials', [
                'email' => $result['patient']->user->email,
                'password' => $result['plain_password'],
            ])
            ->with('status', 'Pasien berhasil ditambahkan. Lanjutkan dengan menambahkan obat.');
    }

    public function show(Patient $patient): View
    {
        $patient->load('user', 'medicines');

        return view('nurse.patients.show', [
            'patient' => $patient,
            'intakeLogs' => $this->patients->intakeHistory($patient),
        ]);
    }

    public function edit(Patient $patient): View
    {
        return view('nurse.patients.edit', [
            'patient' => $patient->load('user'),
        ]);
    }

    public function update(UpdatePatientRequest $request, Patient $patient): RedirectResponse
    {
        $this->patients->updatePatient($patient, $request->validated());

        return redirect()
            ->route('nurse.patients.show', $patient)
            ->with('status', 'Data pasien diperbarui.');
    }

    public function destroy(Patient $patient): RedirectResponse
    {
        $this->patients->deletePatient($patient);

        return redirect()
            ->route('nurse.patients.index')
            ->with('status', 'Pasien dihapus.');
    }

    public function resetPassword(Patient $patient): RedirectResponse
    {
        $password = $this->patients->resetPassword($patient);

        return redirect()
            ->route('nurse.patients.show', $patient)
            ->with('credentials', [
                'email' => $patient->user->email,
                'password' => $password,
            ])
            ->with('status', 'Kata sandi pasien direset.');
    }
}
