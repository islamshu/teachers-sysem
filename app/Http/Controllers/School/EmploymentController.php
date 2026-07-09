<?php

namespace App\Http\Controllers\School;

use App\Events\EmploymentUpdated;
use App\Http\Controllers\Controller;
use App\Models\Employment;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Notifications\EmploymentEnded;
use App\Notifications\TeacherHired;
use App\Notifications\TeacherInvited;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class EmploymentController extends Controller
{
    public function teachers()
    {
        $teachers = TeacherProfile::with(['user', 'subject', 'grades'])
            ->where('status', 'approved')
            ->where('employment_status', 'available')
            ->latest()
            ->paginate(12);

        if (request()->wantsJson()) {
            return response()->json($teachers);
        }

        return Inertia::render('School/TeachersIndex', [
            'teachers' => $teachers->items(),
            'nextPage' => $teachers->currentPage() < $teachers->lastPage() ? $teachers->currentPage() + 1 : null,
            'subjects' => Subject::orderBy('name')->get(),
            'isApproved' => true,
        ]);
    }

    public function invite(Request $request)
    {
        $data = $request->validate([
            'teacher_id' => ['required', 'exists:teacher_profiles,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        $teacher = TeacherProfile::findOrFail($data['teacher_id']);

        if ($teacher->employment_status !== 'available') {
            return back()->withErrors(['teacher' => 'هذا المدرس غير متاح']);
        }

        if ($teacher->status !== 'approved') {
            return back()->withErrors(['teacher' => 'هذا المدرس غير معتمد']);
        }

        $existing = Employment::where('school_id', Auth::id())
            ->where('teacher_id', $data['teacher_id'])
            ->whereIn('status', ['invited', 'accepted'])
            ->first();

        if ($existing) {
            return back()->withErrors(['teacher' => 'لديك دعوة سابقة لهذا المدرس']);
        }

        $user = Auth::user();

        $employment = Employment::create([
            'school_id' => Auth::id(),
            'teacher_id' => $data['teacher_id'],
            'subject_id' => $data['subject_id'],
            'message' => $data['message'],
            'contact_phone' => null,
            'contact_email' => $user->email,
            'status' => 'invited',
        ]);

        $teacher->user->notify(new TeacherInvited($employment));

        EmploymentUpdated::dispatch('invited', $employment);

        return back()->with('success', 'تم إرسال الدعوة بنجاح');
    }

    public function invitations()
    {
        $employments = Employment::with(['teacher.user', 'teacher.subject', 'teacher.grades', 'subject', 'interview'])
            ->where('school_id', Auth::id())
            ->whereIn('status', ['invited', 'accepted', 'interviewed'])
            ->latest()
            ->get();

        return Inertia::render('School/Invitations', [
            'employments' => $employments,
        ]);
    }

    public function markInterviewed(Employment $employment)
    {
        if ($employment->status !== 'accepted') {
            return back()->withErrors(['employment' => 'يجب أن يقبل المدرس الدعوة أولاً']);
        }

        $employment->update(['status' => 'interviewed']);

        return back()->with('success', 'تم تحديث الحالة إلى مقابلة');
    }

    public function hire(Employment $employment)
    {
        if (!in_array($employment->status, ['accepted', 'interviewed'])) {
            return back()->withErrors(['employment' => 'لا يمكن توظيف هذا المدرس حالياً']);
        }

        $employment->update([
            'status' => 'hired',
            'hired_at' => now(),
        ]);

        $employment->teacher->update(['employment_status' => 'employed']);

        $employment->teacher->user->notify(new TeacherHired($employment));

        EmploymentUpdated::dispatch('hired', $employment);

        return back()->with('success', 'تم توظيف المدرس بنجاح');
    }

    public function reject(Employment $employment)
    {
        if ($employment->status === 'hired') {
            return back()->withErrors(['employment' => 'لا يمكن رفض مدرس تم توظيفه']);
        }

        $employment->update(['status' => 'rejected']);

        EmploymentUpdated::dispatch('rejected', $employment);

        return back()->with('success', 'تم رفض الدعوة');
    }

    public function employees()
    {
        $employments = Employment::with(['teacher.user', 'teacher.subject', 'subject'])
            ->where('school_id', Auth::id())
            ->where('status', 'hired')
            ->latest('hired_at')
            ->get();

        return Inertia::render('School/Employees', [
            'employments' => $employments,
        ]);
    }

    public function endEmployment(Employment $employment)
    {
        if ($employment->status !== 'hired') {
            return back()->withErrors(['employment' => 'لا يمكن إنهاء توظيف غير نشط']);
        }

        $employment->update(['status' => 'ended']);

        $employment->teacher->update(['employment_status' => 'available']);

        $employment->teacher->user->notify(new EmploymentEnded($employment));

        EmploymentUpdated::dispatch('ended', $employment);

        return back()->with('success', 'تم إنهاء توظيف المدرس بنجاح');
    }
}
