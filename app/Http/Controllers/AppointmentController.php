<?php

// app/Http/Controllers/AppointmentController.php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        $cacheKey = "holidays_{$year}_{$month}";
        $holidayEvents = Cache::remember($cacheKey, now()->addDays(1), function () use ($year, $month) {
            $apiKey = 'bdfce213aa5d45b6b4dcfb3b47bacde0';
            $country = 'PH'; // Philippines

            $events = [];

            // Loop through days in the month (max 31 API calls)
            $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $response = Http::get('https://holidays.abstractapi.com/v1/', [
                    'api_key' => $apiKey,
                    'country' => $country,
                    'year' => $year,
                    'month' => $month,
                    'day' => $day,
                ]);

                if ($response->ok()) {
                    $data = $response->json();
                    if (!empty($data)) {
                        $date = Carbon::parse($data[0]['date'])->day;
                        $events[$date] = ['🎉 ' . $data[0]['name']];
                    }
                }

                // Prevent API throttling
                usleep(200000); // 0.2s pause per request
            }

            return $events;
        });

        return view('pages.appointments.index', [
            'holidayEvents' => $holidayEvents,
        ]);
    }
}
