<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\EmployeeInvitation;
use App\Models\EmployeeInterview;
use App\Models\InterviewQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class EmployeeInterviewController extends Controller
{
    public function create(EmployeeInvitation $invitation)
    {
        if ($invitation->status !== 'accepted') {
            return back()->withErrors(['invitation' => 'يجب أن يقبل الموظف الدعوة أولاً']);
        }

        if ($invitation->interview && $invitation->interview->status === 'completed') {
            return redirect()->route('school.employee-interviews.show', $invitation->interview);
        }

        $questions = InterviewQuestion::orderBy('sort_order')->orderBy('id')->get();

        return Inertia::render('School/EmployeeConductInterview', [
            'invitation' => $invitation->load(['employee.roles', 'employee.branches']),
            'questions' => $questions,
            'existingInterview' => $invitation->interview,
        ]);
    }

    public function store(Request $request, EmployeeInvitation $invitation)
    {
        if ($invitation->status !== 'accepted') {
            return back()->withErrors(['invitation' => 'يجب أن يقبل الموظف الدعوة أولاً']);
        }

        $request->validate([
            'answers' => ['required', 'array', 'min:1'],
            'answers.*.question_id' => ['required', 'exists:interview_questions,id'],
            'answers.*.score' => ['required', 'numeric', 'min:0', 'max:10'],
            'answers.*.notes' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $questionsCount = count($request->answers);
        $totalScore = collect($request->answers)->sum('score');
        $maxScore = $questionsCount * 10;

        $interview = EmployeeInterview::create([
            'employee_invitation_id' => $invitation->id,
            'interviewer_id' => Auth::id(),
            'total_score' => $totalScore,
            'max_score' => $maxScore,
            'status' => 'completed',
            'notes' => $request->notes,
        ]);

        foreach ($request->answers as $answer) {
            $interview->answers()->create([
                'interview_question_id' => $answer['question_id'],
                'score' => $answer['score'],
                'notes' => $answer['notes'] ?? null,
            ]);
        }

        $invitation->update(['status' => 'interviewed']);

        return redirect()->route('school.employee-interviews.show', $interview)
            ->with('success', 'تم إجراء المقابلة بنجاح');
    }

    public function show(EmployeeInterview $interview)
    {
        $interview->load([
            'invitation.employee.roles',
            'invitation.employee.branches',
            'answers.question',
            'interviewer',
        ]);

        if ($interview->status === 'pending') {
            return redirect()->route('school.employee-interviews.create', $interview->invitation);
        }

        return Inertia::render('School/EmployeeInterviewResult', [
            'interview' => $interview,
        ]);
    }

    public function hire(EmployeeInterview $interview)
    {
        $invitation = $interview->invitation;

        if ($invitation->status !== 'interviewed') {
            return back()->withErrors(['invitation' => 'لا يمكن توظيف هذا الموظف حالياً']);
        }

        $invitation->update([
            'status' => 'hired',
            'hired_at' => now(),
        ]);

        $employee = $invitation->employee;
        $roleName = $employee->role;
        if ($roleName && !in_array($roleName, ['teacher', 'school', 'admin'])) {
            $spatieRole = Role::where('name', $roleName)->first();
            if ($spatieRole && !$employee->hasRole($roleName)) {
                $employee->assignRole($roleName);
            }
        }

        return redirect()->route('school.employee-invitations')
            ->with('success', 'تم توظيف الموظف بنجاح');
    }

    public function reject(EmployeeInterview $interview)
    {
        $invitation = $interview->invitation;

        if ($invitation->status !== 'interviewed') {
            return back()->withErrors(['invitation' => 'لا يمكن رفض هذا الموظف حالياً']);
        }

        $invitation->update(['status' => 'rejected']);

        return redirect()->route('school.employee-invitations')
            ->with('success', 'تم رفض الموظف');
    }
}
