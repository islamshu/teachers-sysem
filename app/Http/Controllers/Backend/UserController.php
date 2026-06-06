<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        $role = request('role');

        $query = User::query();

        if ($role && in_array($role, ['teacher', 'school', 'admin'])) {
            $query->where('role', $role);
        }

        $users = $query->latest()->paginate(10);

        $stats = [
            'total' => User::count(),
            'teachers' => User::where('role', 'teacher')->count(),
            'schools' => User::where('role', 'school')->count(),
            'admins' => User::where('role', 'admin')->count(),
        ];

        if (request()->wantsJson()) {
            return response()->json($users);
        }

        return Inertia::render('Admin/UsersIndex', [
            'users' => $users->items(),
            'nextPage' => $users->currentPage() < $users->lastPage() ? $users->currentPage() + 1 : null,
            'currentRole' => $role,
            'stats' => $stats,
        ]);
    }

    public function destroy(User $user)
    {
        if ($user->is_admin) {
            return redirect()->back()->with('error', 'لا يمكن حذف حساب الإدارة');
        }

        if ($user->teacherProfile) {
            $user->teacherProfile->grades()->detach();
            $user->teacherProfile->delete();
        }

        if ($user->schoolProfile) {
            if ($user->schoolProfile->logo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->schoolProfile->logo);
            }
            $user->schoolProfile->delete();
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'تم حذف المستخدم بنجاح');
    }
}
