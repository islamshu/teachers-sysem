<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\SchoolProfile;
use App\Models\Slide;
use App\Models\Subject;
use App\Models\TeacherProfile;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $query = TeacherProfile::with(['user', 'subject', 'grades'])
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

            return response()->json($query->paginate(6));
        }

        $slides = Slide::active()->ordered()->get();
        $features = \App\Models\Feature::active()->ordered()->get();
        $testimonials = \App\Models\Testimonial::active()->ordered()->get();
        $steps = \App\Models\Step::active()->ordered()->get();
        $totalTeachers = TeacherProfile::where('status', 'approved')->count();
        $totalSchools = SchoolProfile::count();
        $subjects = Subject::orderBy('name')->get();

        return Inertia::render('Home', [
            'slides' => $slides,
            'features' => $features,
            'testimonials' => $testimonials,
            'steps' => $steps,
            'totalTeachers' => $totalTeachers,
            'totalSchools' => $totalSchools,
            'subjects' => $subjects,
        ]);
    }

    public function show(TeacherProfile $teacher)
    {
        if ($teacher->status !== 'approved') {
            abort(404);
        }

        $teacher->load(['user', 'subject', 'grades']);

        $history = \App\Models\Employment::with(['school.schoolProfile', 'subject'])
            ->where('teacher_id', $teacher->id)
            ->whereIn('status', ['ended'])
            ->latest('updated_at')
            ->get();

        return Inertia::render('Teacher/Show', [
            'teacher' => $teacher,
            'history' => $history,
        ]);
    }
}
