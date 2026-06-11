<?php

namespace App\Http\Requests\Nurse;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StorePatientRequest extends FormRequest
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
            'address' => ['required', 'string', 'max:500'],
            'birth_place' => ['required', 'string', 'max:120'],
            'birth_date' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'in:laki-laki,perempuan'],
            'email' => ['nullable', 'email', 'max:160', 'unique:users,email'],
        ];
    }
}
