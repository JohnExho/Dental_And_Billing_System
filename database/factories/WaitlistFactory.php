<?php

namespace Database\Factories;

use App\Models\Clinic;
use App\Models\Account;
use App\Models\Patient;
use App\Models\Waitlist;
use App\Models\Associate;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class WaitlistFactory extends Factory
{
    protected $model = Waitlist::class;

    public function definition(): array
    {
        $queuePosition = $this->faker->numberBetween(1, 50);

        return [
            'waitlist_id' => (string) Str::uuid(),
            'account_id' => Account::inRandomOrder()->where('role', 'admin')->first()->account_id ?? null,
            'patient_id' => Patient::inRandomOrder()->first()->patient_id ?? null,
            'clinic_id' => Clinic::inRandomOrder()->first()->clinic_id ?? null,
            'associate_id' => Associate::inRandomOrder()->first()->account_id ?? null,
            'requested_at_date' => $this->faker->dateTimeBetween('-1 years', 'now'),
            'requested_at_time' => $this->faker->time(),
            'queue_position' => $queuePosition,
            'queue_snapshot' => $queuePosition,
            'status' => $this->faker->randomElement(['waiting', 'completed', 'cancelled']),
            'created_at' => $this->faker->dateTimeBetween('-1 years', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 years', 'now'),
        ];
    }
}
