<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Payment;
use App\Models\Treatment;
use App\Models\Waitlist;

class ReportController extends Controller
{
    public function index()
    {
        $clinicId = session('clinic_id'); // if you want clinic-specific filtering

        $waitlist = Waitlist::whereHas('patient', function ($q) {
            $q->where('clinic_id', $clinic->clinic_id ?? 0);
        })->get();

        $payments = Payment::whereHas('bill', function ($q) {
            $q->where('clinic_id', $clinic->clinic_id ?? 0);
        })->get();

        // Only addresses with patients
        $locations = Address::whereNotNull('patient_id')
            ->whereHas('patient', function ($q) use ($clinicId) {
                if ($clinicId) {
                    $q->where('clinic_id', $clinicId);
                }
            })
            ->with(['patient', 'province', 'city', 'barangay'])
            ->get()
            ->map(fn ($loc) => [
                'province_id' => $loc->province_id,
                'province_name' => $loc->province?->name ?? 'N/A',
                'city_id' => $loc->city_id,
                'city_name' => $loc->city?->name ?? 'N/A',
                'barangay_id' => $loc->barangay_id,
                'barangay_name' => $loc->barangay?->name ?? 'N/A',
            ]);

        $treatment = Treatment::all();

        // Prepare filter options
        $provinces = collect($locations)
            ->map(fn ($loc) => [
                'id' => $loc['province_id'],
                'name' => $loc['province_name'],
            ])
            ->unique('id')
            ->values();

        $cities = collect($locations)
            ->map(fn ($loc) => [
                'id' => $loc['city_id'],
                'name' => $loc['city_name'],
            ])
            ->unique('id')
            ->values();

        $barangays = collect($locations)
            ->map(fn ($loc) => [
                'id' => $loc['barangay_id'],
                'name' => $loc['barangay_name'],
            ])
            ->unique('id')
            ->values();

        return view('pages.reports.index', compact(
            'waitlist',
            'payments',
            'locations',
            'treatment',
            'provinces',
            'cities',
            'barangays'
        ));
    }
}
