<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        $codEnabled = Setting::getBool('cod_enabled', true);
        return view('admin.settings.index', compact('codEnabled'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'cod_enabled' => ['nullable', 'in:on,1,0'],
        ]);

        // Checkbox present means true; absent means false
        $cod = $request->has('cod_enabled');
        Setting::setBool('cod_enabled', $cod);

        return back()->with('success', 'Settings updated.');
    }
}
