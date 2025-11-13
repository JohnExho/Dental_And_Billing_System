<?php
namespace Database\Seeders;

use App\Models\Medicine;
use App\Models\MedicineClinic;
use App\Models\Clinic;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class MedicineSeeder extends Seeder
{
    public function run(): void
    {
        // Create a Faker instance for generating fake data
        $faker = Faker::create();

        // Create medicines
        $medicines = [];
        foreach (range(1, 10) as $index) {
            $medicines[] = Medicine::create([
                'medicine_id'    => (string) Str::uuid(),
                'name'           => $faker->word,
                'name_hash'      => hash('sha256', $faker->word),
                'description'    => $faker->sentence,
                'default_price'  => $faker->randomFloat(2, 5, 100),
            ]);
        }

        // Get all clinics to associate medicines with
        $clinics = Clinic::all();

        // Create medicine clinic entries
        foreach ($medicines as $medicine) {
            foreach ($clinics as $clinic) {
                MedicineClinic::create([
                    'medicine_clinic_id' => (string) Str::uuid(),
                    'medicine_id'        => $medicine->medicine_id,
                    'clinic_id'          => $clinic->clinic_id,
                    'stock'              => $faker->numberBetween(10, 200),
                    'price'              => $faker->randomFloat(2, 10, 50),
                ]);
            }
        }
    }
}
