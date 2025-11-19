<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Bill;
use App\Models\BillItem;
use App\Models\Clinic;
use App\Models\MedicineClinic;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Prescription;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PrescriptionFactory extends Factory
{
    protected $model = Prescription::class;

    public function definition()
    {
        $account = Account::inRandomOrder()->where('role', 'admin')->first();
        $patient = Patient::inRandomOrder()->first();
        $clinic = Clinic::inRandomOrder()->first();

        $medicineClinic = MedicineClinic::where('clinic_id', $clinic->clinic_id ?? null)
            ->inRandomOrder()
            ->first();

        $medicine = $medicineClinic ? $medicineClinic->medicine : null;
        $amount = $this->faker->numberBetween(1, 5);
        $medicineCost = $medicineClinic ? $medicineClinic->price * $amount : 0;

        // Random timestamps between -1 year and now
        $createdAt = $this->faker->dateTimeBetween('-1 years', 'now');
        $updatedAt = $this->faker->dateTimeBetween($createdAt, 'now');
        $prescribedAt = $this->faker->dateTimeBetween($createdAt, 'now');

        return [
            'prescription_id' => Str::uuid(),
            'account_id' => $account->account_id ?? null,
            'patient_id' => $patient->patient_id ?? null,
            'clinic_id' => $clinic->clinic_id ?? null,
            'medicine_id' => $medicine->medicine_id ?? null,
            'tooth_list_id' => null,
            'amount_prescribed' => $amount,
            'medicine_cost' => $medicineCost,
            'dosage_instructions' => $this->faker->sentence,
            'prescription_notes' => $this->faker->optional()->sentence,
            'prescribed_at' => $prescribedAt,
            'status' => 'prescribed',
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
        ];
    }

    /**
     * Attach prescription to a paid bill with a payment
     */
    public function paid()
    {
        return $this->afterCreating(function (Prescription $prescription) {

            // Random payment timestamp between -1 year and now
            $paidAt = $this->faker->dateTimeBetween('-1 years', 'now');

            // Random hour between 7AM (7) and 5PM (17)
            $hour = $this->faker->numberBetween(7, 17);
            $minute = $this->faker->numberBetween(0, 59);
            $second = $this->faker->numberBetween(0, 59);

            // Combine date and time
            $paidAtTime = Carbon::instance($paidAt)
                ->setHour($hour)
                ->setMinute($minute)
                ->setSecond($second);

            $bill = Bill::firstOrCreate(
                [
                    'patient_id' => $prescription->patient_id,
                    'clinic_id' => $prescription->clinic_id,
                    'status' => 'paid',
                ],
                [
                    'bill_id' => Str::uuid(),
                    'account_id' => $prescription->account_id,
                    'amount' => 0,
                    'discount' => 0,
                    'total_amount' => 0,
                    'created_at' => $prescription->created_at,
                    'updated_at' => $prescription->updated_at,
                ]
            );

            $billItem = BillItem::create([
                'bill_item_id' => Str::uuid(),
                'bill_id' => $bill->bill_id,
                'account_id' => $prescription->account_id,
                'item_type' => 'prescription',
                'prescription_id' => $prescription->prescription_id,
                'service_id' => null,
                'amount' => $prescription->medicine_cost,
                'created_at' => $prescription->created_at,
                'updated_at' => $prescription->updated_at,
            ]);

            $bill->increment('amount', (float) $billItem->amount);
            $bill->increment('total_amount', (float) $billItem->amount);

            Payment::create([
                'bill_id' => $bill->bill_id,
                'account_id' => $prescription->account_id,
                'payment_method' => $this->faker->randomElement(['cash', 'online', 'credit_card']),
                'amount' => $billItem->amount,
                'paid_at_date' => Carbon::instance($paidAt)->toDateString(),
                'paid_at_time' => Carbon::instance($paidAtTime)->toTimeString(),
                'payment_details' => [],
                'clinic_id' => $prescription->clinic_id,
                'created_at' => $paidAt,
                'updated_at' => $paidAt,
            ]);

            $bill->status = 'paid';
            $bill->save();

            $prescription->status = 'purchased';
            $prescription->save();
        });
    }
}
