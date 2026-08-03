<?php

namespace App\Http\Controllers\Saas;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        // Define default settings we want to fetch for the form
        $keys = [
            'app_name',
            'support_email',
            'sslcommerz_store_id',
            'sslcommerz_store_password',
            'sslcommerz_is_sandbox',
        ];

        // Fetch settings from the database
        $settings = [];
        foreach ($keys as $key) {
            $settings[$key] = Setting::get($key);
        }

        return view('admin.saas.settings.index', compact('settings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'app_name' => 'nullable|string|max:255',
            'support_email' => 'nullable|email|max:255',
            'sslcommerz_store_id' => 'nullable|string|max:255',
            'sslcommerz_store_password' => 'nullable|string|max:255',
            'sslcommerz_is_sandbox' => 'boolean',
        ]);

        // Default to boolean false if checkbox is missing
        $validated['sslcommerz_is_sandbox'] = $request->has('sslcommerz_is_sandbox');

        // Loop through validated settings and update them
        foreach ($validated as $key => $value) {
            Setting::set($key, $value);
        }

        return redirect()->route('saas.settings.index')->with('success', 'Settings updated successfully.');
    }
}
