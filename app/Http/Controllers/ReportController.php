<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Payment;
use App\Models\Treatment;
use App\Models\Waitlist;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $clinicId = Session::get('clinic_id');

        // Only load basic data for initial page load
        return View::make('pages.reports.index', compact('clinicId'));
    }

    /**
     * Load dashboard partial
     */
    public function getDashboard(Request $request)
    {
        $clinicId = Session::get('clinic_id');
        $period = $request->get('period', 'weekly');

        $waitlist = $this->getWaitlistData($clinicId);
        $revenueData = $this->getRevenueData($clinicId);
        $locations = $this->getLocationData($clinicId);
        $treatment = $this->getTreatmentData($clinicId);

        $waitlistByDateTime = $waitlist->groupBy(function ($w) {
            return Carbon::parse($w->requested_at_date.' '.$w->requested_at_time)
                ->format('Y-m-d H:i');
        })->map(function (Collection $items) {
            return $items->count();
        })->toArray();

        $treatmentData = $treatment
            ->groupBy('treatment_name')
            ->map->count();

        $locationData = collect($locations)
            ->groupBy(fn($loc) => $loc['province_name'] ?? 'Unknown')
            ->map(fn($group) => count($group));

        $forecastedWaitlistValue = $this->getForecastedWaitlist($clinicId);
        $forecastedRevenueValue = $this->getForecastedRevenue($clinicId);

        return view('pages.reports.partials.dashboard-partial', compact(
            'waitlist',
            'revenueData',
            'locations',
            'treatment',
            'treatmentData',
            'waitlistByDateTime',
            'locationData',
            'forecastedWaitlistValue',
            'forecastedRevenueValue'
        ))->render();
    }

    /**
     * Load waitlist detail partial
     */
    public function getWaitlistDetail(Request $request)
    {
        $clinicId = Session::get('clinic_id');
        
        $waitlist = $this->getWaitlistData($clinicId);
        $waitlistByDateTime = $waitlist->groupBy(function ($w) {
            return Carbon::parse($w->requested_at_date.' '.$w->requested_at_time)
                ->format('Y-m-d H:i');
        })->map(function (Collection $items) {
            return $items->count();
        })->toArray();

        $forecastedWaitlistValue = $this->getForecastedWaitlist($clinicId);

        return view('pages.reports.partials.waitlist-partial', compact(
            'waitlist',
            'waitlistByDateTime',
            'forecastedWaitlistValue'
        ))->render();
    }

    /**
     * Load revenue detail partial
     */
    public function getRevenueDetail(Request $request)
    {
        $clinicId = Session::get('clinic_id');
        
        $revenueData = $this->getRevenueData($clinicId);
        $forecastedRevenueValue = $this->getForecastedRevenue($clinicId);

        return view('pages.reports.partials.payment-partial', compact(
            'revenueData',
            'forecastedRevenueValue'
        ))->render();
    }

    /**
     * Load location detail partial
     */
    public function getLocationDetail(Request $request)
    {
        $clinicId = Session::get('clinic_id');
        
        $locations = $this->getLocationData($clinicId);
        
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

        $forecastedLocationValue = $this->getForecastedLocation();

        if (!empty($forecastedLocationValue['clusters'])) {
            $provinceMap = $provinces->pluck('name', 'id');
            $cityMap = $cities->pluck('name', 'id');
            $barangayMap = $barangays->pluck('name', 'id');

            foreach ($forecastedLocationValue['clusters'] as &$cluster) {
                $cluster['province_name'] = $provinceMap[$cluster['province_id']] ?? 'N/A';
                $cluster['city_name'] = $cityMap[$cluster['city_id']] ?? 'N/A';
                $cluster['barangay_name'] = $barangayMap[$cluster['barangay_id']] ?? 'N/A';
            }
        }

        return view('pages.reports.partials.location-partial', compact(
            'locations',
            'provinces',
            'cities',
            'barangays',
            'forecastedLocationValue'
        ))->render();
    }

    /**
     * Load treatment detail partial
     */
    public function getTreatmentDetail(Request $request)
    {
        $clinicId = Session::get('clinic_id');
        
        $treatment = $this->getTreatmentData($clinicId);
        $treatmentData = $treatment
            ->groupBy('treatment_name')
            ->map->count();

        $forecastedTreatmentValue = $this->getForecastedTreatment($clinicId);

        return view('pages.reports.partials.treatment-partial', compact(
            'treatment',
            'treatmentData',
            'forecastedTreatmentValue'
        ))->render();
    }

    // Helper methods
    private function getWaitlistData($clinicId)
    {
        return Waitlist::whereHas('patient', function ($q) use ($clinicId) {
            if ($clinicId) {
                $q->where('clinic_id', $clinicId);
            }
        })->get();
    }

    private function getRevenueData($clinicId)
    {
        $revenueQuery = Payment::query();

        if ($clinicId) {
            $revenueQuery->where('clinic_id', $clinicId);
        }

        return $revenueQuery
            ->get()
            ->groupBy('clinic_id')
            ->map(function ($items) {
                return $items->mapWithKeys(function ($item) {
                    $timestamp = $item->paid_at_date.' '.$item->paid_at_time;
                    return [$timestamp => (float) $item->amount];
                });
            })
            ->toArray();
    }

    private function getLocationData($clinicId)
    {
        return Address::whereNotNull('patient_id')
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
    }

    private function getTreatmentData($clinicId)
    {
        return Treatment::where('status', 'completed')
            ->whereHas('patient', function ($q) use ($clinicId) {
                if ($clinicId) {
                    $q->where('clinic_id', $clinicId);
                }
            })
            ->get();
    }

    private function getForecastedWaitlist($clinicId)
    {
        $apiKey = env('API_KEY');
        $opts = [
            "http" => [
                "header" => "X-API-Key: {$apiKey}\r\n"
            ]
        ];
        $context = stream_context_create($opts);

        return Cache::remember('forecasted_waitlist_' . ($clinicId ?? 'all'), 600, function () use ($clinicId, $context) {
            return json_decode(
                file_get_contents('http://api.chomply.online/forecastwaitlist?clinic_id='.($clinicId ?? ''), false, $context),
                true
            );
        });
    }

    private function getForecastedRevenue($clinicId)
    {
        $apiKey = env('API_KEY');
        $opts = [
            "http" => [
                "header" => "X-API-Key: {$apiKey}\r\n"
            ]
        ];
        $context = stream_context_create($opts);

        return Cache::remember('forecasted_revenue_' . ($clinicId ?? 'all'), 600, function () use ($clinicId, $context) {
            return json_decode(
                file_get_contents('http://api.chomply.online/forecastrevenue?clinic_id='.($clinicId ?? ''), false, $context),
                true
            );
        });
    }

    private function getForecastedLocation()
    {
        $apiKey = env('API_KEY');
        $opts = [
            "http" => [
                "header" => "X-API-Key: {$apiKey}\r\n"
            ]
        ];
        $context = stream_context_create($opts);

        return Cache::remember('forecasted_location', 600, function () use ($context) {
            return json_decode(
                file_get_contents('http://api.chomply.online/forecastlocation', false, $context),
                true
            );
        });
    }

    private function getForecastedTreatment($clinicId)
    {
        $apiKey = env('API_KEY');
        $opts = [
            "http" => [
                "header" => "X-API-Key: {$apiKey}\r\n"
            ]
        ];
        $context = stream_context_create($opts);

        return Cache::remember('forecasted_treatment_' . ($clinicId ?? 'all'), 600, function () use ($clinicId, $context) {
            return json_decode(
                file_get_contents('http://api.chomply.online/forecasttreatment?clinic_id='.($clinicId ?? ''), false, $context),
                true
            );
        });
    }
}