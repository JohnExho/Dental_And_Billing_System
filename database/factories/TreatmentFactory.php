<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Models\Bill;
use App\Models\Account;
use App\Models\Patient;
use App\Models\Service;
use App\Models\BillItem;
use App\Models\Treatment;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class TreatmentFactory extends Factory
{
    protected $model = Treatment::class;

    public function definition()
    {
        $patient = Patient::inRandomOrder()->first();
        $service = Service::inRandomOrder()->first();
        $account= Account::inRandomOrder()->where('role', 'admin')->first();

        $totalCost = $this->faker->numberBetween(500, 5000);

        // Find or create a bill
        $bill = Bill::firstOrCreate(
            [
                'patient_id' => $patient->patient_id,
                'status' => 'unpaid',
                'clinic_id' => $patient->clinic_id,
            ],
            [
                'bill_id' => Str::uuid(),
                'account_id' => $account->account_id,
                'amount' => 0,
                'total_amount' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Create a BillItem
        $billItem = BillItem::create([
            'bill_item_id' => Str::uuid(),
            'bill_id' => $bill->bill_id,
            'account_id' => $account->account_id,
            'item_type' => 'service',
            'service_id' => $service->service_id,
            'amount' => $totalCost,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Update bill totals
        $bill->increment('amount', $totalCost);
        $bill->increment('total_amount', $totalCost);

        return [
            'patient_treatment_id' => Str::uuid(),
            'patient_id' => $patient->patient_id,
            'account_id' => $account->account_id,
            'bill_item_id' => $billItem->bill_item_id,
            'clinic_id' => $patient->clinic_id,
            'status' => 'completed',
            'treatment_date' => $this->faker->dateTimeBetween('-1 years', 'now'),
            'treatment_name' => $service->name,
        ];
    }
}
