<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class HolidayService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('HOLIDAY_API_KEY');
    }

    public function getHolidays($country = 'PH', $year = null, $month = null)
    {
        $year ??= now()->year;
        $month ??= now()->month;
        $cacheKey = "holidays_{$country}_{$year}_{$month}";

        return Cache::remember($cacheKey, now()->addHours(12), function () use ($country, $year, $month) {
            $holidays = collect();

            $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $response = Http::get('https://holidays.abstractapi.com/v1/', [
                    'api_key' => $this->apiKey,
                    'country' => $country,
                    'year' => $year,
                    'month' => $month,
                    'day' => $day,
                ]);

                if ($response->successful() && count($response->json()) > 0) {
                    foreach ($response->json() as $holiday) {
                        $date = Carbon::parse($holiday['date']);
                        $holidays->put(
                            $date->day,
                            ["🎉 " . $holiday['name']]
                        );
                    }
                }
            }

            return $holidays;
        });
    }
}
