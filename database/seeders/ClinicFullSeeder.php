<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Clinic;
use App\Models\ClinicSchedule;
use App\Models\Address;
use App\Models\Account; // Assuming clinics belong to accounts
use Faker\Factory as Faker;

class ClinicFullSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Grab a random account to associate with the clinic
        $account = Account::inRandomOrder()->first();

        // Create a clinic
        $clinic = Clinic::create([
            'clinic_id' => Str::uuid(),
            'account_id' => $account->account_id,
            'name' => $faker->company,
            'description' => $faker->paragraph,
            'speciality' => $faker->word,
            'mobile_no' => $faker->phoneNumber,
            'contact_no' => $faker->phoneNumber,
            'email' => $email = $faker->unique()->safeEmail,
            'email_hash' => hash('sha256', strtolower($email)),
            'schedule_summary' => 'No schedule yet',
        ]);

        // Create schedules (Mon-Fri)
        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $schedules = [];
        foreach ($daysOfWeek as $day) {
            $schedules[] = [
                'clinic_schedule_id' => Str::uuid(),
                'day_of_week' => $day,
                'start_time' => $faker->time('H:i'),
                'end_time' => $faker->time('H:i'),
            ];
        }
        $clinic->clinicSchedules()->createMany($schedules);

        // Create address
        Address::create([
            'account_id' => $account->account_id,
            'clinic_id' => $clinic->clinic_id,
            'house_no' => $faker->buildingNumber,
            'street' => $faker->streetName,
            'barangay_name' => $faker->streetName,
            'city_name' => $faker->streetName,
            'province_name' => $faker->streetName,
            'barangay_id' => 38108, // Replace with actual valid ID
            'city_id' => 1456, // Replace with actual valid ID
            'province_id' => 106, // Replace with actual valid ID
        ]);
    }
}
