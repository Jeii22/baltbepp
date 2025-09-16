<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trip;
use App\Models\Fare;

class WelcomeController extends Controller
{
    public function index()
    {
        // Fetch distinct origins & destinations from DB
        $origins = Trip::select('origin')->distinct()->pluck('origin');
        $destinations = Trip::select('destination')->distinct()->pluck('destination');
        
        // Fetch active fares with passenger types
        $fares = Fare::where('active', true)->get();

        return view('welcome', compact('origins', 'destinations', 'fares'));
    }
}
