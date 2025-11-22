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
    #bypass
    public function index()
    {
        $clinicId = session('clinic_id'); // if you want clinic-specific filtering

        $waitlist = Waitlist::whereHas('patient', function ($q) use ($clinicId) {
            if ($clinicId) {
                $q->where('clinic_id', $clinicId);
            }
        })->get();

        // Modified revenue query to show all revenue when no clinic is selected
        $revenueQuery = Payment::query();

        if ($clinicId) {
            $revenueQuery->where('clinic_id', $clinicId);
        }

        // Convert to array format for JavaScript consumption
        $revenueData = $revenueQuery
            ->get()
            ->groupBy('clinic_id') // group by clinic
            ->map(function ($items) {
                return $items->mapWithKeys(function ($item) {
                    $timestamp = $item->paid_at_date.' '.$item->paid_at_time;

                    return [$timestamp => (float) $item->amount];
                });
            })
            ->toArray();

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
        })->toArray();

        $treatment = Treatment::where('status', 'completed')->whereHas('patient', function ($q) use ($clinicId) {
            if ($clinicId) {
                $q->where('clinic_id', $clinicId);
            }
        })->get();

        $treatmentData = $treatment
            ->groupBy('treatment_name') // group by actual name
            ->map->count();

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

        $forecastedWaitlistValue = json_decode(
            file_get_contents('https://python-api-lrdd.onrender.com/forecastwaitlist?clinic_id='.($clinicId ?? '')),
            true
        );
        $forecastedRevenueValue = json_decode(file_get_contents('https://python-api-lrdd.onrender.com/forecastrevenue?clinic_id='.($clinicId ?? '')), true);
        $forecastedLocationValue = json_decode(file_get_contents('https://python-api-lrdd.onrender.com/forecastlocation'), true);
        // In your ReportController
        $forecastedTreatmentValue = json_decode(
            file_get_contents('https://python-api-lrdd.onrender.com/forecasttreatment?clinic_id='.($clinicId ?? '')),
            true
        );
        if (! empty($forecastedLocationValue['clusters'])) {
            $provinceMap = $provinces->pluck('name', 'id');
            $cityMap = $cities->pluck('name', 'id');
            $barangayMap = $barangays->pluck('name', 'id');

            foreach ($forecastedLocationValue['clusters'] as &$cluster) {
                $cluster['province_name'] = $provinceMap[$cluster['province_id']] ?? 'N/A';
                $cluster['city_name'] = $cityMap[$cluster['city_id']] ?? 'N/A';
                $cluster['barangay_name'] = $barangayMap[$cluster['barangay_id']] ?? 'N/A';
            }
        }

        return view('pages.reports.index', compact(
            'waitlist',
            'revenueData',
            'locations',
            'treatment',
            'treatmentData',
            'provinces',
            'cities',
            'waitlistByDateTime',
            'barangays',
            'forecastedLocationValue',
            'forecastedWaitlistValue',
            'forecastedRevenueValue',
            'forecastedTreatmentValue',
        ));
    }
}
