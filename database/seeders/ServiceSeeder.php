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
    public function run(): void
    {
        $faker = Faker::create();
        $accountId = null;

        // A map of ACTUAL medical/dental/surgical services
        $serviceMap = [
            'Dental' => [
                'Teeth Cleaning', 'Tooth Extraction', 'Root Canal Treatment',
                'Dental Filling', 'Orthodontic Consultation', 'Teeth Whitening'
            ],
            'Medical' => [
                'General Consultation', 'Blood Test', 'ECG',
                'Vaccination', 'Physical Examination', 'Allergy Testing'
            ],
            'Surgical' => [
                'Appendectomy', 'Cataract Removal', 'Tonsillectomy',
                'Minor Skin Surgery', 'Laparoscopy', 'Biopsy'
            ],
        ];

        $clinics = Clinic::all();

        foreach ($serviceMap as $type => $serviceNames) {

            foreach ($serviceNames as $name) {
                $service = Service::create([
                    'service_id'    => (string) Str::uuid(),
                    'account_id'    => $accountId,
                    'service_type'  => $type,
                    'name'          => $name,
                    'name_hash'     => hash('sha256', $name),
                    'description'   => $faker->sentence(),
                    'default_price' => $faker->randomFloat(2, 50, 500),
                ]);

                // Randomize which clinics offer this service
                $assignedClinics = $clinics->random(rand(1, $clinics->count()));

                foreach ($assignedClinics as $clinic) {
                    ClinicService::firstOrCreate([
                        'service_id' => $service->service_id,
                        'clinic_id'  => $clinic->clinic_id,
                    ], [
                        'clinic_service_id' => (string) Str::uuid(),
                        'price'             => $faker->randomFloat(2, 50, 500),
                    ]);
                }
            }
        }
    }
}
