<?php

namespace App\Http\Controllers\Nurse;

use App\Http\Controllers\Controller;
use App\Http\Requests\Nurse\StoreMedicineRequest;
use App\Http\Requests\Nurse\UpdateMedicineRequest;
use App\Models\Medicine;
use App\Models\Patient;
use App\Services\MedicineService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class MedicineController extends Controller
{
    public function __construct(private readonly MedicineService $medicines) {}

    public function create(Patient $patient): View
    {
        return view('nurse.medicines.create', [
            'patient' => $patient,
        ]);
    }

    public function store(StoreMedicineRequest $request, Patient $patient): RedirectResponse
    {
        $this->medicines->createForPatient($patient, $request->validated());

        return redirect()
            ->route('nurse.patients.show', $patient)
            ->with('status', 'Obat berhasil ditambahkan.');
    }

    public function edit(Patient $patient, Medicine $medicine): View
    {
        abort_unless($medicine->patient_id === $patient->id, 404);

        return view('nurse.medicines.edit', [
            'patient' => $patient,
            'medicine' => $medicine,
        ]);
    }

    public function update(UpdateMedicineRequest $request, Patient $patient, Medicine $medicine): RedirectResponse
    {
        abort_unless($medicine->patient_id === $patient->id, 404);

        $this->medicines->updateMedicine($medicine, $request->validated());

        return redirect()
            ->route('nurse.patients.show', $patient)
            ->with('status', 'Data obat diperbarui.');
    }

    public function destroy(Patient $patient, Medicine $medicine): RedirectResponse
    {
        abort_unless($medicine->patient_id === $patient->id, 404);

        $this->medicines->deleteMedicine($medicine);

        return redirect()
            ->route('nurse.patients.show', $patient)
            ->with('status', 'Obat dihapus.');
    }
}
