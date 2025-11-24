<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Address;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PanelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Create an admin account
        $IanAccount = Account::create([
            'last_name' => 'Africa',
            'last_name_hash' => hash('sha256', strtolower('Africa')),
            'middle_name' => null,
            'first_name' => 'Francis Ian',
            'email' => 'ian@example.com',
            'email_hash' => hash('sha256', strtolower('ian@example.com')),
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
            'account_id' => $IanAccount->account_id, // Use the ID of the created account
            'house_no' => $faker->buildingNumber,
            'street' => $faker->streetName,
            'barangay_name' => $faker->streetName,
            'city_name' => $faker->streetName,
            'province_name' => $faker->streetName,
            'barangay_id' => 38108, // Replace with actual valid ID
            'city_id' => 1456, // Replace with actual valid ID
            'province_id' => 106, // Replace with actual valid ID
        ]);

        $GilAccount = Account::create([
            'last_name' => 'Del Monte',
            'last_name_hash' => hash('sha256', strtolower('Del Monte')),
            'middle_name' => null,
            'first_name' => 'Vergil',
            'email' => 'gil@example.com',
            'email_hash' => hash('sha256', strtolower('gil@example.com')),
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
            'account_id' => $GilAccount->account_id, // Use the ID of the created account
            'house_no' => $faker->buildingNumber,
            'street' => $faker->streetName,
            'barangay_name' => $faker->streetName,
            'city_name' => $faker->streetName,
            'province_name' => $faker->streetName,
            'barangay_id' => 38108, // Replace with actual valid ID
            'city_id' => 1456, // Replace with actual valid ID
            'province_id' => 106, // Replace with actual valid ID
        ]);

        
        $MayAccount = Account::create([
            'last_name' => 'Torino',
            'last_name_hash' => hash('sha256', strtolower('Torino')),
            'middle_name' => null,
            'first_name' => 'Maylyn',
            'email' => 'may@example.com',
            'email_hash' => hash('sha256', strtolower('may@example.com')),
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
            'account_id' => $MayAccount->account_id, // Use the ID of the created account
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
