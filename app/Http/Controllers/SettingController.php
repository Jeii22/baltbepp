<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        $codEnabled = Setting::getBool('cod_enabled', true);
        $paymongoEnabled = Setting::getBool('paymongo_enabled', true);
        return view('admin.settings.index', compact('codEnabled', 'paymongoEnabled'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'cod_enabled' => ['nullable', 'in:on,1,0'],
            'paymongo_enabled' => ['nullable', 'in:on,1,0'],
        ]);

        // Checkbox present means true; absent means false
        $cod = $request->has('cod_enabled');
        Setting::setBool('cod_enabled', $cod);

        $paymongo = $request->has('paymongo_enabled');
        Setting::setBool('paymongo_enabled', $paymongo);

        return back()->with('success', 'Settings updated.');
    }
}
