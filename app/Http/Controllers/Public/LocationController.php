<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        return view('public.locations', [
            'branches' => config('bakso_atmo.branches'),
            'contact'  => config('bakso_atmo.contact'),
        ]);
    }
}