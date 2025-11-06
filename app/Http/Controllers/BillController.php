<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class BillController extends Controller
{
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
