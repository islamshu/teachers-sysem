<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSchoolProfileRequest;
use App\Http\Requests\UpdateSchoolProfileRequest;
use App\Models\SchoolProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class SchoolProfileController extends Controller
{
    public function create()
    {
        if (Auth::user()->schoolProfile) {
            return redirect()->route('school-profile.edit');
        }

        return Inertia::render('School/ProfileForm');
    }

    public function store(StoreSchoolProfileRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('schools', 'public');
        }

        $data['user_id'] = Auth::id();

        SchoolProfile::create($data);

        return redirect()
            ->route('dashboard')
            ->with('success', 'تم حفظ بيانات المدرسة بنجاح');
    }

    public function edit()
    {
        $profile = Auth::user()->schoolProfile;

        if (!$profile) {
            return redirect()->route('school-profile.create');
        }

        return Inertia::render('School/ProfileForm', [
            'profile' => $profile,
        ]);
    }

    public function update(UpdateSchoolProfileRequest $request)
    {
        $profile = Auth::user()->schoolProfile;

        if (!$profile) {
            return redirect()->route('school-profile.create');
        }

        $data = $request->validated();

        if ($request->hasFile('logo')) {
            if ($profile->logo) {
                Storage::disk('public')->delete($profile->logo);
            }

            $data['logo'] = $request->file('logo')->store('schools', 'public');
        }

        $profile->update($data);

        return redirect()
            ->route('dashboard')
            ->with('success', 'تم تحديث بيانات المدرسة بنجاح');
    }
}
