<?php

namespace Database\Seeders;

use App\Models\ToothList;
use App\Models\ClinicToothPrice;
use App\Models\Clinic;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class ToothListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Seed 32 teeth
        for ($i = 1; $i <= 32; $i++) {
            $name = "Tooth $i"; // or use real tooth names
            ToothList::create([
                'tooth_list_id' => Str::uuid(),
                'number' => $i,
                'name' => $name,
                'name_hash' => hash('sha256', strtolower($name)),
            ]);
        }
    }
}
