<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Payment;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class BillController extends Controller
{
    protected $guard;

    public function __construct()
    {
        $this->guard = Auth::guard('account');
    }

public function create(Request $request)
{
    $request->validate([
        'bill_id' => 'required|exists:bills,bill_id',
        'payment_method' => 'required|in:cash,online,credit_card',
        'clinic_id' => 'required|exists:clinics,clinic_id',
        'amount_paid' => 'required|numeric|min:0',
        'discount' => 'required|numeric|min:0|max:100',
        'online_payment_type' => 'required_if:payment_method,online',
        'other_payment_details' => 'required_if:online_payment_type,other',
        'credit_card_type' => 'required_if:payment_method,credit_card',
    ]);

    $bill = Bill::findOrFail($request->bill_id);

    // **FIX: Calculate subtotal from bill_items instead of using $bill->amount**
    // This ensures consistency with the JavaScript calculation
    $subtotal = $bill->billItems()
        ->whereNull('deleted_at')
        ->sum('amount');

    // Calculate discount and final amount
    $discountAmount = bcmul($subtotal, bcdiv($request->discount, 100, 4), 2);
    $finalAmount = bcsub($subtotal, $discountAmount, 2);

    // Verify payment amount matches calculated total (with small tolerance for rounding)
    $amountPaid = $request->amount_paid;
    $difference = abs(bcsub($amountPaid, $finalAmount, 2));
    
    if (bccomp($difference, '0.01', 2) > 0) {
        return back()->with('error', "Payment amount ({$amountPaid}) does not match the calculated total ({$finalAmount}).");
    }

    return DB::transaction(function () use ($request, $bill, $finalAmount, $subtotal) {
        // Create payment record
        $payment = new Payment([
            'bill_id' => $bill->bill_id,
            'account_id' => $this->guard->user()->account_id,
            'payment_method' => $request->payment_method,
            'amount' => $finalAmount,
            'paid_at_date' => now(),
            'paid_at_time' => now(),
            'payment_details' => $this->getPaymentDetails($request),
            'clinic_id' => $request->clinic_id,
        ]);
        $payment->save();

        // Update bill status and amounts
        $bill->status = 'paid';
        $bill->discount = $request->discount;
        $bill->amount = $subtotal; // Update with recalculated subtotal
        $bill->total_amount = $finalAmount;
        $bill->save();

        // Log the payment
        LogService::record(
            Auth::guard('account')->user(),
            $bill,
            'process',
            'bill',
            'User processed a bill payment',
            "Bill ID: {$bill->bill_id}, Amount: {$finalAmount}, Method: {$request->payment_method}",
            $request->ip(),
            $request->userAgent()
        );

        return redirect()
            ->route('specific-patient')
            ->with('success', 'Payment processed successfully.');
    });
}

    private function getPaymentDetails(Request $request): array
    {
        $details = [];

        switch ($request->payment_method) {
            case 'online':
                $details['type'] = $request->online_payment_type;
                if ($request->online_payment_type === 'other') {
                    $details['other_details'] = $request->other_payment_details;
                }
                break;

            case 'credit_card':
                $details['card_type'] = $request->credit_card_type;
                break;
        }

        return $details;
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'bill_id' => 'required|exists:bills,bill_id',
            'password' => 'required',
        ]);

        $deletor = Auth::guard('account')->user();
        $bill = Bill::findOrFail($request->bill_id);

        // 1. Make sure only cancelled bills can be deleted
        if ($bill->status !== 'cancelled') {
            return back()->with('error', 'Only cancelled bills can be deleted.');
        }

        // 2. Password check
        if (! Hash::check($request->password, $deletor->password)) {
            return back()->with('error', 'The password is incorrect.');
        }

        return DB::transaction(function () use ($bill, $request, $deletor) {

            // Load items without soft-deleted teeth
            $bill->load(['billItems.billItemTooths' => fn ($q) => $q->whereNull('deleted_at')]);

            // 3. Reverse totals safely before deleting
            foreach ($bill->billItems as $billItem) {
                $bill->amount = bcsub($bill->amount, $billItem->amount, 2);
                $bill->total_amount = bcsub($bill->total_amount, $billItem->amount, 2);

                // 4. Soft delete related tooth pivot rows
                foreach ($billItem->billItemTooths as $toothPivot) {
                    $toothPivot->delete();
                }

                // 5. Soft delete bill item
                $billItem->delete();
            }

            // 6. Soft delete visit if exists
            if ($bill->visit) {
                $bill->visit()->delete();
            }

            // 7. Save final totals & soft delete bill
            $bill->save();
            $bill->delete();

            // 8. Log the deletion
            LogService::record(
                $deletor,
                $bill,
                'delete',
                'bill',
                'User deleted a bill',
                'bill ID: '.$bill->bill_id,
                $request->ip(),
                $request->userAgent()
            );

            return redirect()
                ->route('specific-patient')
                ->with('success', 'Patient bill deleted successfully.');
        });
    }
}
