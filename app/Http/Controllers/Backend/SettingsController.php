<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingsController extends Controller
{
    public function edit()
    {
        return Inertia::render('Admin/Settings', [
            'settings' => Setting::getAll(),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'nullable|string|max:255',
            'site_logo' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
            'primary_color' => 'nullable|string|max:7',
            'description' => 'nullable|string|max:1000',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'about' => 'nullable|string|max:2000',
        ]);

        if ($request->hasFile('site_logo')) {
            $path = $request->file('site_logo')->store('settings', 'public');
            Setting::set('site_logo', $path);
        }

        collect($validated)->except('site_logo')->each(function ($value, $key) {
            if (!is_null($value)) {
                Setting::set($key, $value);
            }
        });

        return back()->with('message', 'تم تحديث الإعدادات بنجاح');
    }
}
