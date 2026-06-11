<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'perawat@nodebat.local'],
            [
                'name' => 'Perawat Demo',
                'role' => User::ROLE_NURSE,
                'password' => 'password',
                'password_changed' => true,
                'timezone' => 'Asia/Jakarta',
            ],
        );

        $this->call(MedicationScenarioSeeder::class);
    }
}
