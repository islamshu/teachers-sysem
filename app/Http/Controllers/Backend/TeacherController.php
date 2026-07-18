<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Subject;
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

    public function create()
    {
        return Inertia::render('Admin/TeacherCreate', [
            'subjects' => Subject::orderBy('name')->get(),
            'grades' => Grade::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp'],
            'phone' => ['required', 'string', 'max:30'],
            'birth_date' => ['required', 'date'],
            'gender' => ['required', 'in:male,female'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'grade_ids' => ['required', 'array', 'min:1'],
            'grade_ids.*' => ['exists:grades,id'],
            'experience_years' => ['required', 'integer', 'min:0', 'max:60'],
            'residence_place' => ['required', 'string', 'max:255'],
            'current_location' => ['required', 'string', 'max:255'],
            'qualification' => ['required', 'string', 'max:255'],
            'practical_experience' => ['required', 'string'],
            'bio' => ['required', 'string'],
            'status' => ['nullable', 'in:pending,approved,rejected'],
        ]);

        $gradeIds = $data['grade_ids'];
        unset($data['grade_ids']);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'role' => 'teacher',
        ]);

        $user->assignRole('teacher');

        unset($data['name'], $data['email'], $data['password']);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('teachers', 'public');
        }

        $data['user_id'] = $user->id;
        $data['status'] = $data['status'] ?? 'approved';

        $profile = TeacherProfile::create($data);
        $profile->grades()->sync($gradeIds);

        return redirect()->route('admin.teachers.index')
            ->with('success', 'تم إضافة المدرس بنجاح');
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
        $userName = $teacher->user->name;
        $teacher->grades()->detach();
        $teacher->delete();

        User::where('id', $userId)->delete();

        return redirect()->route('admin.teachers.index')
            ->with('message', 'تم حذف المدرس بنجاح');
    }
}
