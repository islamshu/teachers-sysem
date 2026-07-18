<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Employment;
use App\Models\Interview;
use App\Models\InterviewQuestion;
use App\Notifications\InterviewCompleted;
use App\Notifications\TeacherHired;
use App\Notifications\TeacherRejected;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class InterviewController extends Controller
{
    public function create(Employment $employment)
    {
        if ($employment->status !== 'accepted') {
            return back()->withErrors(['employment' => 'يجب أن يقبل المدرس الدعوة أولاً']);
        }

        if ($employment->interview && $employment->interview->status === 'completed') {
            return redirect()->route('school.interviews.show', $employment->interview);
        }

        $questions = InterviewQuestion::orderBy('sort_order')->orderBy('id')->get();

        $existingInterview = $employment->interview;

        return Inertia::render('School/ConductInterview', [
            'employment' => $employment->load(['teacher.user', 'teacher.subject', 'subject']),
            'questions' => $questions,
            'existingInterview' => $existingInterview,
        ]);
    }

    public function store(Request $request, Employment $employment)
    {
        if ($employment->status !== 'accepted') {
            return back()->withErrors(['employment' => 'يجب أن يقبل المدرس الدعوة أولاً']);
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

        $interview = Interview::create([
            'employment_id' => $employment->id,
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

        $employment->update(['status' => 'interviewed']);

        $employment->teacher->user->notify(new InterviewCompleted($employment));

        return redirect()->route('school.interviews.show', $interview)
            ->with('success', 'تم إجراء المقابلة بنجاح');
    }

    public function show(Interview $interview)
    {
        $interview->load([
            'employment.teacher.user',
            'employment.teacher.subject',
            'employment.subject',
            'answers.question',
            'interviewer',
        ]);

        if ($interview->status === 'pending') {
            return redirect()->route('school.interviews.create', $interview->employment);
        }

        return Inertia::render('School/InterviewResult', [
            'interview' => $interview,
        ]);
    }

    public function hire(Interview $interview)
    {
        $employment = $interview->employment;

        if ($employment->status !== 'interviewed') {
            return back()->withErrors(['employment' => 'لا يمكن توظيف هذا المدرس حالياً']);
        }

        $employment->update([
            'status' => 'hired',
            'hired_at' => now(),
        ]);

        $employment->teacher->update(['employment_status' => 'employed']);

        $employment->teacher->user->notify(new TeacherHired($employment));

        return redirect()->route('school.invitations')
            ->with('success', 'تم توظيف المدرس بنجاح');
    }

    public function reject(Interview $interview)
    {
        $employment = $interview->employment;

        if ($employment->status !== 'interviewed') {
            return back()->withErrors(['employment' => 'لا يمكن رفض هذا المدرس حالياً']);
        }

        $employment->update(['status' => 'rejected']);

        $employment->teacher->user->notify(new TeacherRejected());

        return redirect()->route('school.invitations')
            ->with('success', 'تم رفض المدرس');
    }
}
