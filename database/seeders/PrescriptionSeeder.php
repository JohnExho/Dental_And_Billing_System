<?php

namespace Database\Seeders;

use App\Models\Prescription;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PrescriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Prescription::factory()->count(5000)->paid()->create();
    }
}
