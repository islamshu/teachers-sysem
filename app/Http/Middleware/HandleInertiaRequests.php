<?php

namespace App\Http\Middleware;

use App\Models\FixedTask;
use App\Models\GeneralTask;
use App\Models\Setting;
use App\Models\UserBalance;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
            } elseif ($user->isEmployee()) {
                $user->load('branches', 'roles', 'school');
            } else {
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

            $badges['incompleteTasks'] = $incompleteFixedTasks + $incompleteGeneralTasks;
            $badges['incompleteFixedTasks'] = $incompleteFixedTasks;
            $badges['incompleteGeneralTasks'] = $incompleteGeneralTasks;

            $badges['incompletePurchases'] = $user->purchases()
                ->where('status', 'pending')
                ->count();
        }

        $hasBalance = $user ? UserBalance::where('user_id', $user->id)->where('balance', '>', 0)->exists() : false;

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user,
                'permissions' => $user ? $user->getAllPermissions()->pluck('name') : [],
            ],
            'badges' => $badges,
            'hasBalance' => $hasBalance,
            'locale' => session('locale', 'ar'),
            'flash' => [
                'message' => session('message') ?: session('success') ?: session('error'),
                'status' => session('status') ?: (session('success') ? 'success' : (session('error') ? 'error' : null)),
            ],
            'settings' => Setting::getAll(),
        ];
    }
}
