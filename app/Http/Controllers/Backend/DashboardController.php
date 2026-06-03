<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Subject;
use App\Models\TeacherProfile;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'pending' => TeacherProfile::where('status', 'pending')->count(),
            'approved' => TeacherProfile::where('status', 'approved')->count(),
            'rejected' => TeacherProfile::where('status', 'rejected')->count(),
            'total' => TeacherProfile::count(),
        ];

        $recentPending = TeacherProfile::with(['user', 'subject'])
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        $allTeachersQuery = TeacherProfile::with(['user', 'subject', 'grades'])
            ->latest();

        $allTeachers = $allTeachersQuery->paginate(10);

        if (request()->wantsJson()) {
            return response()->json($allTeachers);
        }

        $subjects = Subject::orderBy('name')->get();
        $grades = Grade::orderBy('name')->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentPending' => $recentPending,
            'subjects' => $subjects,
            'grades' => $grades,
            'allTeachers' => $allTeachers->items(),
            'allTeachersNextPage' => $allTeachers->currentPage() < $allTeachers->lastPage() ? $allTeachers->currentPage() + 1 : null,
        ]);
    }
}
