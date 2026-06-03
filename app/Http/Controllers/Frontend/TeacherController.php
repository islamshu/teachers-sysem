<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Subject;
use App\Models\TeacherProfile;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $query = TeacherProfile::with([
            'user',
            'subject',
            'grades'
        ])
        ->where('status', 'approved')
        ->when($request->subject_id, function ($query) use ($request) {
            $query->where('subject_id', $request->subject_id);
        })
        ->when($request->grade_id, function ($query) use ($request) {
            $query->whereHas('grades', function ($q) use ($request) {
                $q->where('grades.id', $request->grade_id);
            });
        })
        ->when($request->search, function ($query) use ($request) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        })
        ->latest();

        if ($request->wantsJson()) {
            return response()->json($query->paginate(6));
        }

        $teachers = $query->paginate(6);
        $subjects = Subject::orderBy('name')->get();
        $grades = Grade::orderBy('name')->get();

        return Inertia::render('Home', [
            'teachers' => $teachers->items(),
            'subjects' => $subjects,
            'grades' => $grades,
            'filters' => $request->only(['subject_id', 'grade_id', 'search']),
            'nextPage' => $teachers->currentPage() < $teachers->lastPage() ? $teachers->currentPage() + 1 : null,
        ]);
    }
}
