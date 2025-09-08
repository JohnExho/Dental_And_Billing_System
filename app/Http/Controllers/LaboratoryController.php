<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Laboratories;


class LaboratoryController extends Controller
{
    protected $guard;
    public function __construct()
    {
        $this->guard = Auth::guard('account');
    }
    public function index()
    {
        $laboratories = Laboratories::with('address.barangay', 'address.city', 'address.province')
            ->latest()
            ->paginate(4);


        return view('pages.laboratories.index', compact('laboratories'));
    }
}
