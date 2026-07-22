<?php

namespace App\Http\Middleware;

use App\Models\Employment;
use App\Models\FixedTask;
use App\Models\GeneralTask;
use App\Models\Setting;
use App\Models\UserBalance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $user = $request->user();

        if ($user) {
            if ($user->isSchool()) {
                $user->load('schoolProfile', 'roles');
            } else {
                // All providers (teacher, employee, etc.) need teacherProfile
                $user->load('teacherProfile.subject', 'teacherProfile.grades', 'roles');
            }
        }

        $badges = [
            'incompleteTasks' => 0,
            'incompleteFixedTasks' => 0,
            'incompleteGeneralTasks' => 0,
            'incompletePurchases' => 0,
        ];

        if ($user) {
            $roleIds = $user->roles->pluck('id');

            $isTeacher = $user->isTeacher();
            $isHired = $user->is_hired;
            $showTasks = !$isTeacher || $isHired;

            if ($showTasks) {
                $incompleteFixedTasks = FixedTask::active()
                    ->whereHas('roles', fn ($q) => $q->whereIn('roles.id', $roleIds))
                    ->whereDoesntHave('logs', fn ($q) => $q->where('user_id', $user->id)->whereDate('date', Carbon::today()))
                    ->count();

                $incompleteGeneralTasks = GeneralTask::available()
                    ->where(function ($q) use ($user, $roleIds) {
                        $q->whereHas('roles', fn ($q2) => $q2->whereIn('roles.id', $roleIds))
                          ->orWhereHas('assignedUsers', fn ($q2) => $q2->where('users.id', $user->id));
                    })
                    ->whereDoesntHave('logs', fn ($q) => $q->where('user_id', $user->id))
                    ->count();
            } else {
                $incompleteFixedTasks = 0;
                $incompleteGeneralTasks = 0;
            }

            $badges['incompleteTasks'] = $incompleteFixedTasks + $incompleteGeneralTasks;
            $badges['incompleteFixedTasks'] = $incompleteFixedTasks;
            $badges['incompleteGeneralTasks'] = $incompleteGeneralTasks;

            $badges['incompletePurchases'] = $showTasks
                ? $user->purchases()->where('status', 'pending')->count()
                : 0;
        }

        $hasBalance = $user ? UserBalance::where('user_id', $user->id)->where('balance', '>', 0)->exists() : false;

        $isHired = false;
        if ($user && $user->teacherProfile) {
            $isHired = $user->teacherProfile->employment_status === 'employed'
                || Employment::where('teacher_id', $user->teacherProfile->id)
                    ->where('status', 'hired')
                    ->exists();
        }

        $spatieRoles = \Spatie\Permission\Models\Role::where('name', '!=', 'admin')
            ->orderBy('name')
            ->get(['name', 'id'])
            ->toArray();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user,
                'permissions' => $user ? $user->getAllPermissions()->pluck('name') : [],
            ],
            'spatieRoles' => $spatieRoles,
            'badges' => $badges,
            'hasBalance' => $hasBalance,
            'isHired' => $isHired,
            'locale' => session('locale', 'ar'),
            'flash' => [
                'message' => session('message') ?: session('success') ?: session('error'),
                'status' => session('status') ?: (session('success') ? 'success' : (session('error') ? 'error' : null)),
            ],
            'settings' => Setting::getAll(),
        ];
    }
}
