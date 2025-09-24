<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trip;
use App\Models\Fare;

class WelcomeController extends Controller
{
    public function index()
    {
        // Safe boot mode: avoid DB during deploy issues and provide fallbacks
        if (env('SAFE_BOOT', false)) {
            $origins = collect(['Bantayan', 'Cadiz']);
            $destinations = collect(['Cadiz', 'Bantayan']);
            $fares = collect(); // no fares shown
            return view('welcome', compact('origins', 'destinations', 'fares'));
        }

        try {
            // Fetch distinct origins & destinations from DB
            $origins = Trip::select('origin')->distinct()->pluck('origin');
            $destinations = Trip::select('destination')->distinct()->pluck('destination');

            // Fetch active fares with passenger types
            $fares = Fare::where('active', true)->get();
        } catch (\Throwable $e) {
            // Fallbacks if DB is not reachable to prevent 500s
            $origins = collect(['Bantayan', 'Cadiz']);
            $destinations = collect(['Cadiz', 'Bantayan']);
            $fares = collect();
        }

        return view('welcome', compact('origins', 'destinations', 'fares'));
    }
}
