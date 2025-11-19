<?php

namespace Database\Seeders;

use App\Models\Waitlist;
use Illuminate\Database\Seeder;

class WaitlistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Waitlist::factory()->count(5000)->create();
    }
}
