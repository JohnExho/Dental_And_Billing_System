<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Address\Entities\City;
use Yajra\Address\Entities\Barangay;

class AddressController extends Controller
{
    public function cities($provinceId)
    {
        return response()->json(
            City::where('province_id', $provinceId)
                ->orderBy('name')
                ->get(['id as city_id', 'name'])
        );
    }

    public function barangays($cityId)
    {
        return response()->json(
            Barangay::where('city_id', $cityId)
                ->orderBy('name')
                ->get(['id as barangay_id', 'name'])
        );
    }
}