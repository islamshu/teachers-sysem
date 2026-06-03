<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTeacherProfileRequest;
use App\Http\Requests\UpdateTeacherProfileRequest;
use App\Models\Grade;
use App\Models\Subject;
use App\Models\TeacherProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class TeacherProfileController extends Controller
{
    public function create()
    {
        if (Auth::user()->teacherProfile) {
            return redirect()->route('teacher-profile.edit');
        }

        $subjects = Subject::orderBy('name')->get();
        $grades = Grade::orderBy('name')->get();

        return Inertia::render('Teacher/ProfileForm', [
            'subjects' => $subjects,
            'grades' => $grades,
        ]);
    }

    public function store(StoreTeacherProfileRequest $request)
    {
        $data = $request->validated();

        $gradeIds = $data['grade_ids'];
        unset($data['grade_ids']);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('teachers', 'public');
        }

        $data['user_id'] = Auth::id();

        $profile = TeacherProfile::create($data);

        $profile->grades()->sync($gradeIds);

        return redirect()
            ->route('dashboard')
            ->with('success', 'تم حفظ بيانات المدرس بنجاح');
    }

    public function edit()
    {
        $profile = Auth::user()->teacherProfile;

        if (!$profile) {
            return redirect()->route('teacher-profile.create');
        }

        $subjects = Subject::orderBy('name')->get();
        $grades = Grade::orderBy('name')->get();

        return Inertia::render('Teacher/ProfileForm', [
            'profile' => $profile->load(['grades', 'subject']),
            'subjects' => $subjects,
            'grades' => $grades,
        ]);
    }

    public function update(UpdateTeacherProfileRequest $request)
    {
        $profile = Auth::user()->teacherProfile;

        if (!$profile) {
            return redirect()->route('teacher-profile.create');
        }

        $data = $request->validated();

        $gradeIds = $data['grade_ids'];
        unset($data['grade_ids']);

        if ($request->hasFile('photo')) {
            if ($profile->photo) {
                Storage::disk('public')->delete($profile->photo);
            }

            $data['photo'] = $request->file('photo')->store('teachers', 'public');
        }

        $profile->update($data);

        $profile->grades()->sync($gradeIds);

        return redirect()
            ->route('dashboard')
            ->with('success', 'تم تحديث بيانات المدرس بنجاح');
    }
}