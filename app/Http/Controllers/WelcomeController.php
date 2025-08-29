<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trip;

class WelcomeController extends Controller
{
    public function index()
    {
        // Fetch distinct origins & destinations from DB
        $origins = Trip::select('origin')->distinct()->pluck('origin');
        $destinations = Trip::select('destination')->distinct()->pluck('destination');

        return view('welcome', compact('origins', 'destinations'));
    }
}
