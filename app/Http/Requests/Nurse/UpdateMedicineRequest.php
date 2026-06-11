<?php

namespace App\Http\Requests\Nurse;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMedicineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === User::ROLE_NURSE;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'dose' => ['required', 'string', 'max:60'],
            'route' => ['required', 'string', 'max:60'],
            'interval_hours' => ['required', 'integer', 'min:1', 'max:24'],
            'times_per_day' => ['required', 'integer', 'min:1', 'max:10'],
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
