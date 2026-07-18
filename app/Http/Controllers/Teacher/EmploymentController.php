<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Employment;
use App\Notifications\InvitationAccepted;
use App\Notifications\TeacherDeclined;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class EmploymentController extends Controller
{
    public function invitations()
    {
        $teacher = Auth::user()->teacherProfile;

        if (!$teacher) {
            return redirect()->route('teacher-profile.create');
        }

        $employments = Employment::with(['school.schoolProfile', 'subject'])
            ->where('teacher_id', $teacher->id)
            ->whereIn('status', ['invited', 'accepted', 'interviewed'])
            ->latest()
            ->get();

        return Inertia::render('Teacher/Invitations', [
            'employments' => $employments,
        ]);
    }

    public function accept(Employment $employment)
    {
        $teacher = Auth::user()->teacherProfile;

        if ((int) $employment->teacher_id != (int) $teacher->id) {
            abort(403);
        }

        if ($employment->status != 'invited') {
            return back()->withErrors(['employment' => 'لا يمكن قبول هذه الدعوة']);
        }

        $employment->update(['status' => 'accepted']);

        $employment->school->notify(new InvitationAccepted($employment));

        return back()->with('success', 'تم قبول الدعوة، يمكنك الآن التواصل مع المدرسة');
    }

    public function decline(Employment $employment)
    {
        $teacher = Auth::user()->teacherProfile;

        if ((int) $employment->teacher_id != (int) $teacher->id) {
            abort(403);
        }

        if ($employment->status != 'invited') {
            return back()->withErrors(['employment' => 'لا يمكن رفض هذه الدعوة']);
        }

        $employment->update(['status' => 'rejected']);

        $employment->school->notify(new TeacherDeclined($employment));

        return back()->with('success', 'تم رفض الدعوة');
    }

    public function mySchool()
    {
        $teacher = Auth::user()->teacherProfile;

        if (!$teacher) {
            return redirect()->route('teacher-profile.create');
        }

        $employment = Employment::with(['school.schoolProfile', 'subject'])
            ->where('teacher_id', $teacher->id)
            ->whereIn('status', ['hired', 'ended'])
            ->latest('hired_at')
            ->first();

        $history = Employment::with(['school.schoolProfile', 'subject'])
            ->where('teacher_id', $teacher->id)
            ->whereIn('status', ['ended', 'rejected'])
            ->latest('updated_at')
            ->get();

        return Inertia::render('Teacher/MySchool', [
            'employment' => $employment,
            'history' => $history,
        ]);
    }
}
