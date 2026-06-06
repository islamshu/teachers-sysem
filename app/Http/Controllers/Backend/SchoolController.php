<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\SchoolProfile;
use App\Models\User;
use App\Notifications\ProfileApproved;
use App\Notifications\ProfileRejected;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class SchoolController extends Controller
{
    public function index()
    {
        $status = request('status');

        $query = SchoolProfile::with('user');

        if ($status && in_array($status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $status);
        }

        $schools = $query->latest()->paginate(10);

        if (request()->wantsJson()) {
            return response()->json($schools);
        }

        return Inertia::render('Admin/SchoolsIndex', [
            'schools' => $schools->items(),
            'nextPage' => $schools->currentPage() < $schools->lastPage() ? $schools->currentPage() + 1 : null,
            'currentStatus' => $status,
        ]);
    }

    public function approve(SchoolProfile $school)
    {
        $school->update(['status' => 'approved']);
        $school->user->notify(new ProfileApproved('school'));

        return redirect()->back()->with('success', 'تم قبول المدرسة بنجاح');
    }

    public function reject(SchoolProfile $school)
    {
        $school->update(['status' => 'rejected']);
        $school->user->notify(new ProfileRejected('school'));

        return redirect()->back()->with('success', 'تم رفض المدرسة');
    }

    public function batchApprove()
    {
        $ids = request('ids', []);
        SchoolProfile::where('status', 'pending')
            ->whereIn('id', $ids)
            ->update(['status' => 'approved']);

        $schools = SchoolProfile::with('user')->whereIn('id', $ids)->get();
        foreach ($schools as $school) {
            $school->user->notify(new ProfileApproved('school'));
        }

        return redirect()->back()->with('success', 'تم قبول المدارس المحددة');
    }

    public function batchReject()
    {
        $ids = request('ids', []);
        SchoolProfile::where('status', 'pending')
            ->whereIn('id', $ids)
            ->update(['status' => 'rejected']);

        $schools = SchoolProfile::with('user')->whereIn('id', $ids)->get();
        foreach ($schools as $school) {
            $school->user->notify(new ProfileRejected('school'));
        }

        return redirect()->back()->with('success', 'تم رفض المدارس المحددة');
    }

    public function destroy(SchoolProfile $school)
    {
        if ($school->logo) {
            Storage::disk('public')->delete($school->logo);
        }

        $userId = $school->user_id;
        $school->delete();

        User::where('id', $userId)->delete();

        return redirect()->route('admin.schools.index')
            ->with('success', 'تم حذف المدرسة بنجاح');
    }
}
