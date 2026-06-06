<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\TeacherProfile;
use App\Models\User;
use App\Notifications\ProfileApproved;
use App\Notifications\ProfileRejected;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $query = TeacherProfile::with([
            'user',
            'subject',
            'grades'
        ]);

        $status = $request->status;
        if ($status && in_array($status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $status);
        }

        $teachers = $query->latest()->paginate(10);

        if ($request->wantsJson()) {
            return response()->json($teachers);
        }

        return Inertia::render('Admin/TeachersIndex', [
            'teachers' => $teachers->items(),
            'nextPage' => $teachers->currentPage() < $teachers->lastPage() ? $teachers->currentPage() + 1 : null,
            'currentStatus' => $status ?: 'all',
        ]);
    }

    public function show(TeacherProfile $teacher)
    {
        return Inertia::render('Admin/TeacherDetails', [
            'teacher' => $teacher->load(['user', 'subject', 'grades']),
        ]);
    }

    public function approve(TeacherProfile $teacher)
    {
        $teacher->update(['status' => 'approved']);
        $teacher->user->notify(new ProfileApproved('teacher'));

        return back()->with('message', 'تم قبول المدرس بنجاح');
    }

    public function reject(TeacherProfile $teacher)
    {
        $teacher->update(['status' => 'rejected']);
        $teacher->user->notify(new ProfileRejected('teacher'));

        return back()->with('message', 'تم رفض المدرس بنجاح');
    }

    public function batchApprove(Request $request)
    {
        $ids = $request->input('ids', []);
        TeacherProfile::whereIn('id', $ids)->update(['status' => 'approved']);

        $teachers = TeacherProfile::with('user')->whereIn('id', $ids)->get();
        foreach ($teachers as $teacher) {
            $teacher->user->notify(new ProfileApproved('teacher'));
        }

        return back()->with('message', 'تم قبول ' . count($ids) . ' مدرس بنجاح');
    }

    public function batchReject(Request $request)
    {
        $ids = $request->input('ids', []);
        TeacherProfile::whereIn('id', $ids)->update(['status' => 'rejected']);

        $teachers = TeacherProfile::with('user')->whereIn('id', $ids)->get();
        foreach ($teachers as $teacher) {
            $teacher->user->notify(new ProfileRejected('teacher'));
        }

        return back()->with('message', 'تم رفض ' . count($ids) . ' مدرس بنجاح');
    }

    public function destroy(TeacherProfile $teacher)
    {
        if ($teacher->photo) {
            Storage::disk('public')->delete($teacher->photo);
        }

        $userId = $teacher->user_id;
        $teacher->grades()->detach();
        $teacher->delete();

        User::where('id', $userId)->delete();

        return redirect()->route('admin.teachers.index')
            ->with('message', 'تم حذف المدرس بنجاح');
    }
}
