<?php

namespace Database\Seeders;

use App\Models\ProgressNote;
use App\Models\Account;
use App\Models\Patient;
use App\Models\Associate;
use App\Models\PatientVisit;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProgressNoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Grab random related records (nullable relationships are optional)
        $account = Account::inRandomOrder()->first();
        $patient = Patient::inRandomOrder()->first();
        $associate = Associate::inRandomOrder()->first();
        // $visit = PatientVisit::inRandomOrder()->first();

        // Generate a few progress notes
        for ($i = 0; $i < 10; $i++) {
            ProgressNote::create([
                'progress_note_id' => Str::uuid(),
                'account_id' => $account?->account_id,   // nullable
                'patient_id' => $patient?->patient_id,   // nullable
                'associate_id' => $associate?->associate_id, // nullable
                // 'visit_id' => $visit?->visit_id,         // nullable
                'summary' => $faker->sentence(6),
                'progress_note' => $faker->paragraph(3),
                'created_at' => $faker->dateTimeBetween('-1 year', 'now'),
                'updated_at' => now(),
            ]);
        }
    }
}
