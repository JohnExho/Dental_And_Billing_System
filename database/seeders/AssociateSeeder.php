<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Associate;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AssociateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Create an admin account
        $associate = Associate::create([
            'last_name' => 'Admin User',
            'last_name_hash' => hash('sha256', strtolower('Admin User')),
            'middle_name' => null,
            'first_name' => 'Admin',
            'email' => 'admin@example.com',
            'email_hash' => hash('sha256', strtolower('admin@example.com')),
            'mobile_no' => $faker->phoneNumber,
            'contact_no' => $faker->phoneNumber,
            'specialty' => $faker->word,
            'account_id' => null,
            'clinic_id' => null,
            'is_active' => true,
        ]);


        Address::create([
            'associate_id' => $associate->associate_id, // Use the ID of the created account
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
