<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Service;
use App\Models\Clinic;
use App\Models\ClinicService;
use Faker\Factory as Faker;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accountId = null;
        $faker = Faker::create();

        // Create the service using Eloquent
        $service = Service::create([
            'service_id'    => (string) Str::uuid(),
            'account_id'    => $accountId,
            'service_type'  => 'Dental',
            'name'          => 'Teeth Cleaning',
            'name_hash'     => hash('sha256', 'Teeth Cleaning'),
            'description'   => 'Professional dental cleaning service.',
            'default_price' => $faker->randomFloat(2, 50, 500),
        ]);

        // Get all clinics using Eloquent
        $clinics = Clinic::all();

        // Create clinic-specific prices using Eloquent
        foreach ($clinics as $clinic) {
            ClinicService::create([
                'clinic_service_id' => (string) Str::uuid(),
                'service_id'        => $service->service_id,
                'clinic_id'         => $clinic->clinic_id,
                'price'             => $faker->randomFloat(2, 50, 500),
            ]);
        }
    }
}
