<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\EmployeeInvitation;
use App\Models\User;
use App\Notifications\EmployeeHired;
use App\Notifications\EmployeeInvited;
use App\Notifications\EmployeeRejected;
use App\Notifications\EmployeeEmploymentEnded;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class EmployeeInvitationController extends Controller
{
    public function browse()
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

        return Inertia::render('School/EmployeesBrowse', [
            'employees' => $employees->items(),
            'nextPage' => $employees->currentPage() < $employees->lastPage() ? $employees->currentPage() + 1 : null,
        ]);
    }

    public function invite(Request $request)
    {
        $data = $request->validate([
            'employee_id' => ['required', 'exists:users,id'],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        $employee = User::where('id', $data['employee_id'])->whereNotIn('role', ['teacher', 'school', 'admin'])->firstOrFail();

        $schoolId = Auth::user()->effectiveSchoolId();

        $existing = EmployeeInvitation::where('school_id', $schoolId)
            ->where('employee_id', $employee->id)
            ->whereIn('status', ['invited', 'accepted', 'interviewed', 'hired'])
            ->first();

        if ($existing) {
            return back()->withErrors(['employee' => 'لديك دعوة سابقة لهذا الموظف']);
        }

        EmployeeInvitation::create([
            'school_id' => $schoolId,
            'employee_id' => $employee->id,
            'message' => $request->message,
            'status' => 'invited',
        ]);

        $invitation = EmployeeInvitation::where('school_id', $schoolId)
            ->where('employee_id', $employee->id)
            ->latest()
            ->first();

        $employee->notify(new EmployeeInvited($invitation));

        return back()->with('success', 'تم إرسال الدعوة بنجاح');
    }

    public function invitations()
    {
        $employments = EmployeeInvitation::with(['employee.roles', 'employee.branches', 'interview'])
            ->where('school_id', Auth::user()->effectiveSchoolId())
            ->whereIn('status', ['invited', 'accepted', 'interviewed'])
            ->latest()
            ->get();

        return Inertia::render('School/EmployeeInvitations', [
            'employments' => $employments,
        ]);
    }

    public function hired()
    {
        $employments = EmployeeInvitation::with(['employee.roles', 'employee.branches'])
            ->where('school_id', Auth::user()->effectiveSchoolId())
            ->where('status', 'hired')
            ->latest('hired_at')
            ->get();

        return Inertia::render('School/EmployeeHired', [
            'employments' => $employments,
        ]);
    }

    public function hire(EmployeeInvitation $invitation)
    {
        if (!in_array($invitation->status, ['accepted', 'interviewed'])) {
            return back()->withErrors(['invitation' => 'لا يمكن توظيف هذا الموظف حالياً']);
        }

        $invitation->update([
            'status' => 'hired',
            'hired_at' => now(),
        ]);

        $employee = $invitation->employee;
        $employee->update(['is_hired' => true]);
        $roleName = $employee->role;
        if ($roleName && !in_array($roleName, ['teacher', 'school', 'admin'])) {
            $spatieRole = Role::where('name', $roleName)->first();
            if ($spatieRole && !$employee->hasRole($roleName)) {
                $employee->assignRole($roleName);
            }
        }

        $employee->notify(new EmployeeHired($invitation));

        return back()->with('success', 'تم توظيف الموظف بنجاح');
    }

    public function reject(EmployeeInvitation $invitation)
    {
        if ($invitation->status === 'hired') {
            return back()->withErrors(['invitation' => 'لا يمكن رفض موظف تم توظيفه']);
        }

        $invitation->update(['status' => 'rejected']);

        $invitation->employee->notify(new EmployeeRejected($invitation));

        return back()->with('success', 'تم رفض الدعوة');
    }

    public function endEmployment(EmployeeInvitation $invitation)
    {
        if ($invitation->status != 'hired') {
            return back()->withErrors(['invitation' => 'لا يمكن إنهاء توظيف غير نشط']);
        }

        $invitation->update(['status' => 'ended']);

        $invitation->employee->notify(new EmploymentEnded($invitation));

        return back()->with('success', 'تم إنهاء توظيف الموظف بنجاح');
    }
}
