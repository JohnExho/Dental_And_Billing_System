<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Address;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use App\Models\Laboratories;
use Illuminate\Database\Seeder;

class LaboratorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $faker = Faker::create();

        // Grab a random account to associate with the clinic
        $account = Account::inRandomOrder()->first();

        // Create a clinic
        $laboratory = Laboratories::create([
            'laboratory_id' => Str::uuid(),
            'account_id' => $account->account_id,
            'name' => $faker->company,
            'name_hash' => hash('sha256', strtolower($faker->company)),
            'description' => $faker->paragraph,
            'specialty' => $faker->word,
            'mobile_no' => $faker->phoneNumber,
            'contact_no' => $faker->phoneNumber,
            'contact_person' => $faker->name,
            'email' => $email = $faker->unique()->safeEmail,
            'email_hash' => hash('sha256', strtolower($email)),
        ]);

        // Create address
        Address::create([
            'account_id' => $account->account_id,
            'laboratory_id' => $laboratory->laboratory_id,
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
