<?php

namespace App\Http\Controllers;

use App\Events\TaskUpdated;
use App\Models\GeneralTask;
use App\Models\GeneralTaskLog;
use App\Models\User;
use App\Notifications\TaskCompleted;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GeneralTaskController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $roleIds = $user->roles->pluck('id');

        $assignedViaRoles = GeneralTask::available()
            ->whereHas('roles', fn ($q) => $q->whereIn('roles.id', $roleIds));

        $assignedDirectly = GeneralTask::available()
            ->whereHas('assignedUsers', fn ($q) => $q->where('users.id', $user->id));

        $taskIds = $assignedViaRoles->pluck('id')->merge($assignedDirectly->pluck('id'))->unique();

        $tasks = GeneralTask::whereIn('id', $taskIds)
            ->with('logs')
            ->get()
            ->map(function ($task) use ($user) {
                $log = $task->logs->where('user_id', $user->id)->first();
                return [
                    'id' => $task->id,
                    'name' => $task->name,
                    'description' => $task->description,
                    'start_at' => $task->start_at,
                    'end_at' => $task->end_at,
                    'completed' => (bool) $log,
                    'completed_at' => $log?->completed_at,
                ];
            });

        $completedCount = $tasks->where('completed', true)->count();
        $totalCount = $tasks->count();

        return Inertia::render('GeneralTasks/Index', [
            'tasks' => $tasks,
            'completedCount' => $completedCount,
            'totalCount' => $totalCount,
            'now' => now()->format('Y-m-d H:i:s'),
        ]);
    }

    public function complete(Request $request, GeneralTask $generalTask)
    {
        $user = auth()->user();
        $roleIds = $user->roles->pluck('id');

        if (!$generalTask->is_active || $generalTask->end_at->isPast()) {
            return redirect()->back()->with('error', 'لا يمكن إنجاز هذه المهمة');
        }

        $hasViaRoles = $generalTask->roles()->whereIn('roles.id', $roleIds)->exists();
        $hasDirect = $generalTask->assignedUsers()->where('users.id', $user->id)->exists();

        if (!$hasViaRoles && !$hasDirect) {
            return redirect()->back()->with('error', 'لا يمكنك إنجاز هذه المهمة');
        }

        $existing = GeneralTaskLog::where('general_task_id', $generalTask->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'تم إنجاز هذه المهمة بالفعل');
        }

        GeneralTaskLog::create([
            'general_task_id' => $generalTask->id,
            'user_id' => $user->id,
            'completed_at' => now(),
        ]);

        $this->notifyTaskCompleted($generalTask, $user);

        TaskUpdated::dispatch('general', 'completed', ['task_id' => $generalTask->id, 'user_id' => $user->id]);

        return redirect()->back()->with('success', 'تم إنجاز المهمة');
    }

    public function undo(GeneralTask $generalTask)
    {
        $user = auth()->user();

        GeneralTaskLog::where('general_task_id', $generalTask->id)
            ->where('user_id', $user->id)
            ->delete();

        TaskUpdated::dispatch('general', 'undone', ['task_id' => $generalTask->id, 'user_id' => $user->id]);

        return redirect()->back()->with('success', 'تم التراجع عن إنجاز المهمة');
    }

    private function notifyTaskCompleted(GeneralTask $task, User $completer): void
    {
        $recipients = collect();

        if ($task->creator) {
            $recipients->push($task->creator);
        }

        User::where('role', 'admin')->each(fn ($admin) => $recipients->push($admin));

        $recipients->unique('id')->each(function ($recipient) use ($task, $completer) {
            $recipient->notify(new TaskCompleted('general', $task->name, $completer->name, $task->id));
        });
    }
}
