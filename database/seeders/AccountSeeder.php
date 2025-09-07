<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Address;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Create an admin account
        $adminAccount = Account::create([
            'last_name' => 'Admin User',
            'middle_name' => null,
            'first_name' => 'Admin',
            'email' => 'admin@example.com',
            'email_hash' => hash('sha256', strtolower('admin@example.com')),
            'mobile_no' => $faker->phoneNumber,
            'contact_no' => $faker->phoneNumber,
            'password' => Hash::make('password'), // Always hash passwords
            'can_act_as_staff' => true,
            'role' => 'admin',
            'is_active' => true,
            'otp_hash' => null,
            'otp_expires_at' => null,
        ]);

        Address::create([
            'account_id' => $adminAccount->account_id, // Use the ID of the created account
            'house_no' => $faker->buildingNumber,
            'street' => $faker->streetName,
            'barangay_id' => 38108, // Replace with actual valid ID
            'city_id' => 1456, // Replace with actual valid ID
            'province_id' => 106, // Replace with actual valid ID
        ]);

        // Example of another account
        $staffAccount = Account::create([
            'last_name' => 'Doe',
            'middle_name' => 'M',
            'first_name' => 'John',
            'email' => 'rci.bsis.hensonjohnvictor@gmail.com',
            'email_hash' => hash('sha256', strtolower('rci.bsis.hensonjohnvictor@gmail.com')),
            'mobile_no' => $faker->phoneNumber,
            'contact_no' => $faker->phoneNumber,
            'password' => Hash::make('secret123'),
            'role' => 'staff',
            'is_active' => true,
            'otp_hash' => null,
            'otp_expires_at' => null,
        ]);

        Address::create([
            'account_id' => $staffAccount->account_id, // Use the ID of the created account
            'house_no' => $faker->buildingNumber,
            'street' => $faker->streetName,
            'barangay_id' => 38108, // Replace with actual valid ID
            'city_id' => 1456, // Replace with actual valid ID
            'province_id' => 106, // Replace with actual valid ID
        ]);
    }
}
