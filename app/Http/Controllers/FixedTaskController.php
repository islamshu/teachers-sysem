<?php

namespace App\Http\Controllers;

use App\Models\DailyTaskLog;
use App\Models\FixedTask;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FixedTaskController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $today = Carbon::today();

        $roleIds = $user->roles->pluck('id');

        $tasks = FixedTask::active()
            ->whereHas('roles', fn ($q) => $q->whereIn('roles.id', $roleIds))
            ->with(['logs' => fn ($q) => $q->where('user_id', $user->id)->whereDate('date', $today)])
            ->get()
            ->map(function ($task) use ($user, $today) {
                $log = $task->logs->first();
                return [
                    'id' => $task->id,
                    'name' => $task->name,
                    'description' => $task->description,
                    'completed' => (bool) $log,
                    'completed_at' => $log?->completed_at,
                ];
            });

        $completedCount = $tasks->where('completed', true)->count();
        $totalCount = $tasks->count();

        return Inertia::render('Tasks/Index', [
            'tasks' => $tasks,
            'completedCount' => $completedCount,
            'totalCount' => $totalCount,
            'today' => $today->format('Y-m-d'),
        ]);
    }

    public function complete(Request $request, FixedTask $fixedTask)
    {
        $user = auth()->user();
        $today = Carbon::today();

        $roleIds = $user->roles->pluck('id');
        $hasTask = $fixedTask->roles()->whereIn('roles.id', $roleIds)->exists();

        if (!$hasTask || !$fixedTask->is_active) {
            return redirect()->back()->with('error', 'لا يمكنك إنجاز هذه المهمة');
        }

        $existing = DailyTaskLog::where('fixed_task_id', $fixedTask->id)
            ->where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'تم إنجاز هذه المهمة اليوم بالفعل');
        }

        DailyTaskLog::create([
            'fixed_task_id' => $fixedTask->id,
            'user_id' => $user->id,
            'date' => $today,
            'completed_at' => now(),
        ]);

        return redirect()->back()->with('success', 'تم إنجاز المهمة');
    }

    public function undo(FixedTask $fixedTask)
    {
        $user = auth()->user();
        $today = Carbon::today();

        DailyTaskLog::where('fixed_task_id', $fixedTask->id)
            ->where('user_id', $user->id)
            ->whereDate('date', $today)
            ->delete();

        return redirect()->back()->with('success', 'تم التراجع عن إنجاز المهمة');
    }
}
