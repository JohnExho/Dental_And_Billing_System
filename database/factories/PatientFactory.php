<?php

namespace Database\Factories;

use App\Models\Clinic;
use App\Models\Account;
use App\Models\Patient;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition()
    {
        $firstName = $this->faker->firstName();
        $lastName = $this->faker->lastName();
        $email = $this->faker->unique()->safeEmail();

        return [
            'patient_id' => (string) Str::uuid(),
            'account_id' => Account::inRandomOrder()->where('role','admin')->first()->account_id ?? null,
            'clinic_id' => Clinic::inRandomOrder()->first()->clinic_id ?? null,
            'first_name' => $firstName,
            'middle_name' => $this->faker->optional()->firstName(),
            'last_name' => $lastName,
            'last_name_hash' => hash('sha256', strtolower($lastName)),
            'mobile_no' => $this->faker->optional()->phoneNumber(),
            'contact_no' => $this->faker->optional()->phoneNumber(),
            'profile_picture' => null, // optional, can add fake file
            // 'qr_id' => $this->faker->optional()->uuid(),
            'email' => strtolower($email),
            'email_hash' => hash('sha256', strtolower($email)),
            'sex' => $this->faker->randomElement(['Male', 'Female']),
            'civil_status' => $this->faker->randomElement(['Single', 'Married', 'Divorced']),
            'date_of_birth' => $this->faker->date(),
            'referral' => $this->faker->optional()->company(),
            'occupation' => $this->faker->optional()->jobTitle(),
            'company' => $this->faker->optional()->company(),
            'weight' => $this->faker->optional()->numberBetween(50, 100),
            'height' => $this->faker->optional()->numberBetween(150, 200),
            'school' => $this->faker->optional()->company(),
        ];
    }
}
