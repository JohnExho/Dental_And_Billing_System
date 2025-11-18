<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Payment;
use App\Models\Treatment;
use App\Models\Waitlist;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ReportController extends Controller
{
    public function index()
    {
        $clinicId = session('clinic_id'); // if you want clinic-specific filtering

        $waitlist = Waitlist::whereHas('patient', function ($q) use ($clinicId) {
            if ($clinicId) {
                $q->where('clinic_id', $clinicId);
            }
        })->get();

        $revenueData = Payment::where('clinic_id', $clinicId)
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m-%d %H:%i:%s") as timestamp, amount')
            ->get()
            ->pluck('amount', 'timestamp');

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

        $waitlistByDateTime = $waitlist->groupBy(function ($w) {
            // Group by date + hour + minute
            return Carbon::parse($w->requested_at_date.' '.$w->requested_at_time)
                ->format('Y-m-d H:i');
        })->map(function (Collection $items) {
            return $items->count();
        });

        $treatment = Treatment::where('status', 'completed')->get();

            $treatmentData = $treatment
        ->groupBy('treatment_name') // group by actual name
        ->map->count(); // count occurrences

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
            'revenueData',
            'locations',
            'treatment',
            'treatmentData',
            'provinces',
            'cities',
            'waitlistByDateTime',
            'barangays'
        ));
    }
}
