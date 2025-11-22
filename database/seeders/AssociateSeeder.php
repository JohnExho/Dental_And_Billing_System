<?php

namespace Database\Seeders;

use App\Models\Clinic;
use App\Models\Address;
use App\Models\Associate;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class AssociateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
                $clinic = Clinic::inRandomOrder()->first();


        // Create an associate / dentist account
        $associate = Associate::create([
            'last_name' => 'Doe',
            'last_name_hash' => hash('sha256', strtolower('Doe')),
            'middle_name' => 'A',
            'first_name' => 'John',
            'email' => 'dr.john.doe@example.com',
            'email_hash' => hash('sha256', strtolower('dr.john.doe@example.com')),
            'mobile_no' => $faker->phoneNumber,
            'contact_no' => $faker->phoneNumber,
            'specialty' => 'General Dentistry',
            'account_id' => null,
            'clinic_id' => $clinic->clinic_id, // set to the clinic ID this dentist belongs to
            'is_active' => true,
        ]);

        Address::create([
            'associate_id' => $associate->associate_id, // Use the ID of the created associate
            'house_no' => $faker->buildingNumber,
            'street' => $faker->streetName,
            'barangay_name' => $faker->streetName,
            'city_name' => $faker->city,
            'province_name' => $faker->state,
            'barangay_id' => 38108, // Replace with actual valid ID if needed
            'city_id' => 1456,      // Replace with actual valid ID if needed
            'province_id' => 106,   // Replace with actual valid ID if needed
        ]);
    }
}
