<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition()
    {
        // Province info
        $provinceId = 35;       // Province table ID for Bulacan
        $provinceName = 'Bulacan';

        // City info (Plaridel)
        $cityId = 354;          // Yajra city PK for Plaridel
        $cityName = 'Plaridel';

        // Map of barangays in Plaridel (ID => Name)
        $barangays = [
            9030 => 'Agnaya',
            9031 => 'Bagong Silang',
            9032 => 'Banga I',
            9033 => 'Banga II',
            9034 => 'Bintog',
            9035 => 'Bulihan',
            9036 => 'Culianin',
            9037 => 'Dampol',
            9038 => 'Lagundi',
            9039 => 'Lalangan',
            9040 => 'Lumang Bayan',
            9041 => 'Parulan',
            9042 => 'Poblacion',
            9043 => 'Rueda',
            9044 => 'San Jose',
            9045 => 'Santa Ines',
            9046 => 'Santo Niño',
            9047 => 'Sipat',
            9048 => 'Tabang',
        ];

        // Pick a random barangay
        $barangayId = array_rand($barangays);
        $barangayName = $barangays[$barangayId];

        return [
            'patient_id' => Patient::inRandomOrder()->first()->patient_id ?? null,
            'house_no' => $this->faker->buildingNumber(),
            'street' => $this->faker->streetName(),
            'barangay_name' => $barangayName,
            'city_name' => $cityName,
            'province_name' => $provinceName,
            'barangay_id' => $barangayId,
            'city_id' => $cityId,
            'province_id' => $provinceId,
        ];
    }
}
