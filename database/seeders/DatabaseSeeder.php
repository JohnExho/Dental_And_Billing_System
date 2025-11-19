<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Yajra\Address\Seeders\AddressSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(AddressSeeder::class);
        $this->call(AccountSeeder::class);
        $this->call(ClinicFullSeeder::class);
        $this->call(class: ClinicFullSeeder::class);
        $this->call(ToothListSeeder::class);
        $this->call(ServiceSeeder::class);
        $this->call(ServiceSeeder::class);
        $this->call(ServiceSeeder::class);
        $this->call(StaffSeeder::class);
        $this->call(MedicineSeeder::class);
        $this->call(PatientSeeder::class);
        $this->call(WaitlistSeeder::class);
    }
}
