<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InterviewQuestion;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InterviewQuestionController extends Controller
{
    public function index()
    {
        $questions = InterviewQuestion::orderBy('sort_order')->orderBy('id')->get();

        return Inertia::render('Admin/InterviewQuestions', [
            'questions' => $questions,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'question' => ['required', 'string', 'max:1000'],
            'category' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        InterviewQuestion::create($validated);

        return redirect()->back()->with('success', 'تم إضافة السؤال بنجاح');
    }

    public function update(Request $request, InterviewQuestion $interviewQuestion)
    {
        $validated = $request->validate([
            'question' => ['required', 'string', 'max:1000'],
            'category' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $interviewQuestion->update($validated);

        return redirect()->back()->with('success', 'تم تحديث السؤال بنجاح');
    }

    public function destroy(InterviewQuestion $interviewQuestion)
    {
        $interviewQuestion->delete();

        return redirect()->back()->with('success', 'تم حذف السؤال بنجاح');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'items' => ['required', 'array'],
            'items.*.id' => ['required', 'exists:interview_questions,id'],
            'items.*.sort_order' => ['required', 'integer', 'min:0'],
        ]);

        foreach ($request->items as $item) {
            InterviewQuestion::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        return redirect()->back()->with('success', 'تم إعادة ترتيب الأسئلة بنجاح');
    }
}
