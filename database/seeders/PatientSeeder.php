<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Patient;
use App\Models\Address;

class PatientSeeder extends Seeder
{
    public function run()
    {
        // Create 10 patients
        Patient::factory(500)->create()->each(function ($patient) {
                Address::factory()->create([
                    'patient_id' => $patient->patient_id
                ]);
        });
    }
}
