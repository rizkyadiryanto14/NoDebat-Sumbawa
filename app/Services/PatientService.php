<?php

namespace App\Services;

use App\Models\MedicineIntakeLog;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PatientService
{
    /**
     * @param  array{name:string,address:string,birth_place:string,birth_date:string,gender:string,email?:string|null}  $data
     * @return array{patient:Patient,plain_password:string}
     */
    public function createPatient(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $plainPassword = Str::password(10, symbols: false);
            $patientCode = $this->generatePatientCode();
            $email = $data['email'] ?? strtolower($patientCode).'@nodebat.local';

            $user = User::query()->create([
                'name' => $data['name'],
                'email' => $email,
                'role' => User::ROLE_PATIENT,
                'password' => $plainPassword,
                'plain_password' => $plainPassword,
                'password_changed' => false,
                'timezone' => 'Asia/Jakarta',
            ]);

            $patient = Patient::query()->create([
                'user_id' => $user->id,
                'patient_code' => $patientCode,
                'name' => $data['name'],
                'address' => $data['address'],
                'birth_place' => $data['birth_place'],
                'birth_date' => $data['birth_date'],
                'gender' => $data['gender'],
            ]);

            return ['patient' => $patient->fresh('user'), 'plain_password' => $plainPassword];
        });
    }

    /**
     * @param  array{name:string,address:string,birth_place:string,birth_date:string,gender:string,email?:string}  $data
     */
    public function updatePatient(Patient $patient, array $data): Patient
    {
        return DB::transaction(function () use ($patient, $data) {
            $patient->update([
                'name' => $data['name'],
                'address' => $data['address'],
                'birth_place' => $data['birth_place'],
                'birth_date' => $data['birth_date'],
                'gender' => $data['gender'],
            ]);

            $userAttributes = ['name' => $data['name']];
            if (! empty($data['email'])) {
                $userAttributes['email'] = $data['email'];
            }

            $patient->user->update($userAttributes);

            return $patient->fresh('user');
        });
    }

    public function deletePatient(Patient $patient): void
    {
        DB::transaction(function () use ($patient) {
            $user = $patient->user;
            $patient->delete();
            $user?->delete();
        });
    }

    public function resetPassword(Patient $patient): string
    {
        $plainPassword = Str::password(10, symbols: false);

        $patient->user->update([
            'password' => $plainPassword,
            'plain_password' => $plainPassword,
            'password_changed' => false,
        ]);

        return $plainPassword;
    }

    /**
     * @return Collection<int, Patient>
     */
    public function allWithStats(): Collection
    {
        return Patient::query()
            ->with(['user', 'medicines.intakeLogs'])
            ->latest()
            ->get();
    }

    /**
     * @return Collection<int, MedicineIntakeLog>
     */
    public function intakeHistory(Patient $patient): Collection
    {
        return MedicineIntakeLog::query()
            ->with('medicine:id,patient_id,name,dose,route')
            ->whereHas(
                'medicine',
                fn (Builder $query): Builder => $query->whereBelongsTo($patient),
            )
            ->orderByDesc('scheduled_at')
            ->orderByDesc('id')
            ->get();
    }

    /**
     * @return Collection<int, MedicineIntakeLog>
     */
    public function latestIntakeHistory(int $limit = 2): Collection
    {
        return MedicineIntakeLog::query()
            ->with([
                'medicine:id,patient_id,name,dose,route',
                'medicine.patient:id,user_id,name,patient_code',
                'medicine.patient.user:id,timezone',
            ])
            ->orderByDesc('scheduled_at')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    private function generatePatientCode(): string
    {
        do {
            $code = 'PSN-'.strtoupper(Str::random(6));
        } while (Patient::query()->where('patient_code', $code)->exists());

        return $code;
    }
}
