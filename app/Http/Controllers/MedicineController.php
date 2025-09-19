<?php

namespace App\Http\Controllers;

use App\Models\Logs;
use App\Models\Clinic;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MedicineController extends Controller
{
    protected $guard;

    public function __construct()
    {
        $this->guard = Auth::guard('account');
    }

    public function index()
    {
        $medicines = Medicine::latest()
            ->paginate(8);

        return view('pages.medicines.index', compact('medicines'));
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'clinic_id'   => 'nullable|exists:clinics,clinic_id',
            'description' => 'nullable|string|max:500',
            'price'       => 'required|numeric|min:0|max:999999.99',
            'stock'       => 'required|integer|min:0|max:100000',
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first());
        }

        $medicine = Medicine::create([
            'name'        => $request->name,
            'name_hash'   => hash('sha256', strtolower($request->name)),
            'description' => $request->description,
            'price'       => $request->price,
            'stock'       => $request->stock,
        ]);

        $authAccount = $this->guard->user();
        $clinic = Clinic::findOrFail($request->clinic_id);
        Logs::record(
            $authAccount, // actor (logged-in user)
            $clinic, // clinic context
            null,
            null,
            'create',
            'Medicine',
            'User created a medicine',
            'Medicine: ' . $medicine->name . ' (Stock: ' . $medicine->stock . ', Price: ' . $medicine->price . ')',
            $request->ip(),
            $request->userAgent()
        );

        return redirect()->route('medicines')->with('success', 'Medicine created successfully.');
    }
}
