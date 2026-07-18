<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\EmployeeInvitation;
use App\Notifications\EmployeeInvitationAccepted;
use App\Notifications\EmployeeInvitationDeclined;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class InvitationController extends Controller
{
    public function index()
    {
        $invitations = EmployeeInvitation::with(['school', 'interview'])
            ->where('employee_id', Auth::id())
            ->whereIn('status', ['invited', 'accepted', 'interviewed', 'hired'])
            ->latest()
            ->get();

        return Inertia::render('Employee/Invitations', [
            'invitations' => $invitations,
        ]);
    }

    public function accept(EmployeeInvitation $invitation)
    {
        if ($invitation->employee_id !== Auth::id()) {
            abort(403);
        }

        if ($invitation->status !== 'invited') {
            return back()->withErrors(['invitation' => 'لا يمكن قبول هذه الدعوة']);
        }

        $invitation->update([
            'status' => 'accepted',
        ]);

        $invitation->school->notify(new EmployeeInvitationAccepted($invitation));

        return back()->with('success', 'تم قبول الدعوة بنجاح، بانتظار إجراء المقابلة');
    }

    public function decline(EmployeeInvitation $invitation)
    {
        if ($invitation->employee_id !== Auth::id()) {
            abort(403);
        }

        if ($invitation->status !== 'invited') {
            return back()->withErrors(['invitation' => 'لا يمكن رفض هذه الدعوة']);
        }

        $invitation->update(['status' => 'rejected']);

        $invitation->school->notify(new EmployeeInvitationDeclined($invitation));

        return back()->with('success', 'تم رفض الدعوة');
    }
}
