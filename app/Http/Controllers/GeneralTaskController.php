<?php

namespace App\Http\Controllers;

use App\Events\TaskUpdated;
use App\Models\GeneralTask;
use App\Models\GeneralTaskCompletion;
use App\Models\GeneralTaskLog;
use App\Models\User;
use App\Notifications\TaskCompleted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class GeneralTaskController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isTeacher() && !$user->teacherProfile) {
            return Inertia::render('GeneralTasks/Index', [
                'tasks' => collect(),
                'completedCount' => 0,
                'totalCount' => 0,
                'now' => now()->format('Y-m-d\TH:i:s'),
            ]);
        }

        if ($user->isTeacher()) {
            $isHired = $user->teacherProfile->employment_status === 'employed'
                || \App\Models\Employment::where('teacher_id', $user->teacherProfile->id)
                    ->where('status', 'hired')
                    ->exists();

            if (!$isHired) {
                return Inertia::render('GeneralTasks/Index', [
                    'tasks' => collect(),
                    'completedCount' => 0,
                    'totalCount' => 0,
                    'now' => now()->format('Y-m-d\TH:i:s'),
                ]);
            }
        }
        $roleIds = $user->roles->pluck('id');

        $assignedViaRoles = GeneralTask::available()
            ->whereHas('roles', fn ($q) => $q->whereIn('roles.id', $roleIds));

        $assignedDirectly = GeneralTask::available()
            ->whereHas('assignedUsers', fn ($q) => $q->where('users.id', $user->id));

        $taskIds = $assignedViaRoles->pluck('id')->merge($assignedDirectly->pluck('id'))->unique();

        $tasks = GeneralTask::whereIn('id', $taskIds)
            ->with('logs', 'attachments')
            ->get()
            ->map(function ($task) use ($user) {
                $log = $task->logs->where('user_id', $user->id)->first();

                $completions = [];
                if ($log) {
                    $completions = GeneralTaskCompletion::where('general_task_id', $task->id)
                        ->where('user_id', $user->id)
                        ->get()
                        ->map(fn ($c) => [
                            'attachment_id' => $c->general_task_attachment_id,
                            'file_path' => $c->file_path,
                        ])
                        ->keyBy('attachment_id')
                        ->toArray();
                }

                return [
                    'id' => $task->id,
                    'name' => $task->name,
                    'description' => $task->description,
                    'start_at' => $task->start_at,
                    'end_at' => $task->end_at,
                    'completed' => (bool) $log,
                    'completed_at' => $log?->completed_at,
                    'attachments' => $task->attachments->map(fn ($a) => [
                        'id' => $a->id,
                        'name' => $a->name,
                        'file_path' => $a->file_path,
                    ]),
                    'attachment_required' => $task->attachment_required,
                    'user_completions' => $completions,
                ];
            });

        $completedCount = $tasks->where('completed', true)->count();
        $totalCount = $tasks->count();

        return Inertia::render('GeneralTasks/Index', [
            'tasks' => $tasks,
            'completedCount' => $completedCount,
            'totalCount' => $totalCount,
            'now' => now()->format('Y-m-d\TH:i:s'),
        ]);
    }

    public function complete(Request $request, GeneralTask $generalTask)
    {
        $user = auth()->user();
        $roleIds = $user->roles->pluck('id');

        if (!$generalTask->is_active || \Carbon\Carbon::parse($generalTask->getRawOriginal('end_at'))->isPast()) {
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

        $generalTask->load('attachments');

        if ($generalTask->attachment_required) {
            foreach ($generalTask->attachments as $att) {
                if (!$request->hasFile("completion_files.{$att->id}")) {
                    return redirect()->back()->with('error', "يجب رفع مرفق \"{$att->name}\" لإتمام المهمة");
                }
            }
        }

        GeneralTaskLog::create([
            'general_task_id' => $generalTask->id,
            'user_id' => $user->id,
            'completed_at' => now(),
        ]);

        foreach ($generalTask->attachments as $att) {
            $fileKey = "completion_files.{$att->id}";
            if ($request->hasFile($fileKey)) {
                $filePath = $request->file($fileKey)->store('general-task-completions', 'public');
                GeneralTaskCompletion::create([
                    'general_task_id' => $generalTask->id,
                    'user_id' => $user->id,
                    'general_task_attachment_id' => $att->id,
                    'file_path' => $filePath,
                ]);
            }
        }

        $this->notifyTaskCompleted($generalTask, $user);

        TaskUpdated::dispatch('general', 'completed', ['task_id' => $generalTask->id, 'user_id' => $user->id]);

        return redirect()->back()->with('success', 'تم إنجاز المهمة');
    }

    public function undo(GeneralTask $generalTask)
    {
        $user = auth()->user();

        GeneralTaskCompletion::where('general_task_id', $generalTask->id)
            ->where('user_id', $user->id)
            ->each(function ($c) {
                Storage::disk('public')->delete($c->file_path);
            });

        GeneralTaskCompletion::where('general_task_id', $generalTask->id)
            ->where('user_id', $user->id)
            ->delete();

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
