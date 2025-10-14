<?php
namespace Database\Seeders;

use App\Models\Note;
use App\Models\Account;
use App\Models\Patient;
use App\Models\Associate;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;

class NoteSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $account = Account::inRandomOrder()->first();
        $patient = Patient::inRandomOrder()->first();
        $associate = Associate::inRandomOrder()->first();

        for ($i = 0; $i < 10; $i++) {
            Note::create([
                'note_id' => Str::uuid(),
                'account_id' => $account?->account_id,
                'patient_id' => $patient?->patient_id,
                'associate_id' => $associate?->associate_id,
                'patient_visit_id' => null, // or assign a random visit if needed
                'note_type' => $faker->randomElement(['general', 'treatment', 'progress']),
                'summary' => ($faker->sentence(6)),
                'note' => ($faker->paragraph(3)),
                'created_at' => $faker->dateTimeBetween('-1 year', 'now'),
                'updated_at' => now(),
            ]);
        }
    }
}
