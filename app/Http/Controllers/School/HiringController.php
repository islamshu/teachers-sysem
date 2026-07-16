<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Employment;
use App\Models\EmployeeInvitation;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Inertia\Inertia;

class HiringController extends Controller
{
    public function applicants(Request $request)
    {
        $type = $request->input('type', 'teacher');

        if ($type === 'employee') {
            return $this->employeeApplicants($request);
        }

        return $this->teacherApplicants($request);
    }

    public function employeeShow(User $user)
    {
        $user->load(['roles', 'branches', 'teacherProfile.subject', 'teacherProfile.grades']);

        $employmentStatus = null;
        if ($user->teacherProfile) {
            $employmentStatus = Employment::where('teacher_id', $user->teacherProfile->id)
                ->whereIn('status', ['hired', 'invited', 'accepted', 'interviewed'])
                ->with('school')
                ->latest()
                ->first();
        }

        $employeeInvitationStatus = EmployeeInvitation::where('employee_id', $user->id)
            ->whereIn('status', ['hired', 'invited', 'accepted', 'interviewed'])
            ->with('school')
            ->latest()
            ->first();

        return Inertia::render('School/HiringEmployeeShow', [
            'employee' => $user,
            'employmentStatus' => $employmentStatus,
            'employeeInvitationStatus' => $employeeInvitationStatus,
        ]);
    }

    private function teacherApplicants(Request $request)
    {
        $teachers = TeacherProfile::with(['user', 'subject', 'grades'])
            ->where('status', 'approved')
            ->where('employment_status', 'available')
            ->whereHas('user', fn ($q) => $q->where('role', 'teacher'))
            ->latest()
            ->paginate(12);

        if (request()->wantsJson()) {
            return response()->json($teachers);
        }

        return Inertia::render('School/HiringApplicants', [
            'items' => $teachers->items(),
            'nextPage' => $teachers->currentPage() < $teachers->lastPage() ? $teachers->currentPage() + 1 : null,
            'type' => 'teacher',
            'subjects' => Subject::orderBy('name')->get(),
            'isApproved' => true,
        ]);
    }

    private function employeeApplicants(Request $request)
    {
        $schoolId = Auth::user()->effectiveSchoolId();

        $hiredElsewhere = EmployeeInvitation::where('status', 'hired')
            ->pluck('employee_id');

        $myActive = EmployeeInvitation::where('school_id', $schoolId)
            ->whereIn('status', ['invited', 'accepted', 'interviewed'])
            ->pluck('employee_id');

        $excludeIds = $hiredElsewhere->merge($myActive)->unique();

        $employees = User::whereNotIn('role', ['teacher', 'school', 'admin'])
            ->whereNotIn('id', $excludeIds)
            ->with(['roles', 'branches'])
            ->latest()
            ->paginate(12);

        if (request()->wantsJson()) {
            return response()->json($employees);
        }

        return Inertia::render('School/HiringApplicants', [
            'items' => $employees->items(),
            'nextPage' => $employees->currentPage() < $employees->lastPage() ? $employees->currentPage() + 1 : null,
            'type' => 'employee',
            'subjects' => [],
            'isApproved' => true,
        ]);
    }

    public function invitations(Request $request)
    {
        $type = $request->input('type', 'teacher');

        if ($type === 'employee') {
            return $this->employeeInvitations();
        }

        return $this->teacherInvitations();
    }

    private function teacherInvitations()
    {
        $employments = Employment::with(['teacher.user', 'teacher.subject', 'teacher.grades', 'subject', 'interview'])
            ->where('school_id', Auth::user()->effectiveSchoolId())
            ->whereIn('status', ['invited', 'accepted', 'interviewed'])
            ->latest()
            ->get();

        return Inertia::render('School/HiringInvitations', [
            'items' => $employments,
            'type' => 'teacher',
        ]);
    }

    private function employeeInvitations()
    {
        $employments = EmployeeInvitation::with(['employee.roles', 'employee.branches', 'interview'])
            ->where('school_id', Auth::user()->effectiveSchoolId())
            ->whereIn('status', ['invited', 'accepted', 'interviewed'])
            ->latest()
            ->get();

        return Inertia::render('School/HiringInvitations', [
            'items' => $employments,
            'type' => 'employee',
        ]);
    }

    public function hired(Request $request)
    {
        $type = $request->input('type', 'teacher');

        if ($type === 'employee') {
            return $this->employeeHired();
        }

        return $this->teacherHired();
    }

    private function teacherHired()
    {
        $employments = Employment::with(['teacher.user', 'teacher.subject', 'subject'])
            ->where('school_id', Auth::user()->effectiveSchoolId())
            ->where('status', 'hired')
            ->latest('hired_at')
            ->get();

        return Inertia::render('School/HiringHired', [
            'items' => $employments,
            'type' => 'teacher',
        ]);
    }

    private function employeeHired()
    {
        $employments = EmployeeInvitation::with(['employee.roles', 'employee.branches'])
            ->where('school_id', Auth::user()->effectiveSchoolId())
            ->where('status', 'hired')
            ->latest('hired_at')
            ->get();

        return Inertia::render('School/HiringHired', [
            'items' => $employments,
            'type' => 'employee',
        ]);
    }
}
