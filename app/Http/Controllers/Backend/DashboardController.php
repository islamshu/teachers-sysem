<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\SchoolProfile;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
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
            'schools_pending' => SchoolProfile::where('status', 'pending')->count(),
            'schools_approved' => SchoolProfile::where('status', 'approved')->count(),
            'schools_total' => SchoolProfile::count(),
            'users_total' => User::count(),
            'users_teachers' => User::where('role', 'teacher')->count(),
            'users_schools' => User::where('role', 'school')->count(),
            'users_admins' => User::where('role', 'admin')->count(),
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
