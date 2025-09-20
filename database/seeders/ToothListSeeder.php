<?php

namespace Database\Seeders;

use App\Models\ToothList;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ToothListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Create a clinic
        $tooth_list = ToothList::create([
            'tooth_list_id' => Str::uuid(),
            'number' => $faker->numberBetween(1, 32),
            'name' => $faker->company,
            'name_hash' => hash('sha256', strtolower($faker->company)),
            'price' => $faker->randomFloat(2, 50, 500), // Random price between 50 and 500
        ]);
    }
}
