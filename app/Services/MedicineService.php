<?php

namespace App\Services;

use App\Models\Medicine;
use App\Models\Patient;
use Illuminate\Support\Carbon;

class MedicineService
{
    /**
     * @param  array{name:string,dose:string,route:string,interval_hours:int,times_per_day:int,quantity:int,notes?:string|null}  $data
     */
    public function createForPatient(Patient $patient, array $data): Medicine
    {
        $data['first_dose_at'] = Carbon::now();

        return $patient->medicines()->create($data);
    }

    /**
     * @param  array{name:string,dose:string,route:string,interval_hours:int,times_per_day:int,quantity:int,notes?:string|null}  $data
     */
    public function updateMedicine(Medicine $medicine, array $data): Medicine
    {
        $medicine->update($data);

        return $medicine->fresh();
    }

    public function deleteMedicine(Medicine $medicine): void
    {
        $medicine->delete();
    }
}
