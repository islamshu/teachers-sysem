<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyTaskLog;
use App\Models\FixedTask;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class FixedTaskController extends Controller
{
    public function index()
    {
        $tasks = FixedTask::with('roles', 'creator')
            ->latest()
            ->paginate(20);

        if (request()->wantsJson()) {
            return response()->json($tasks);
        }

        return Inertia::render('Admin/FixedTasks/Index', [
            'tasks' => $tasks->items(),
            'nextPage' => $tasks->currentPage() < $tasks->lastPage() ? $tasks->currentPage() + 1 : null,
            'roles' => Role::all(),
        ]);
    }

    public function progress(Request $request)
    {
        $date = $request->date ? Carbon::parse($request->date) : Carbon::today();

        $roles = Role::with(['permissions', 'users'])->get();

        $tasksByRole = FixedTask::active()
            ->with(['roles', 'logs' => fn ($q) => $q->whereDate('date', $date)])
            ->get()
            ->groupBy(fn ($task) => $task->roles->pluck('id')->first())
            ->map(function ($tasks, $roleId) use ($roles, $date) {
                $role = $roles->firstWhere('id', $roleId);
                if (!$role) return null;

                $users = $role->users()->select('id', 'name', 'email')->get();

                $taskData = $tasks->map(function ($task) use ($users, $date) {
                    $completedUsers = $task->logs->whereIn('user_id', $users->pluck('id'));
                    return [
                        'id' => $task->id,
                        'name' => $task->name,
                        'description' => $task->description,
                        'completed_count' => $completedUsers->count(),
                        'total_users' => $users->count(),
                        'completed_users' => $completedUsers->values()->map(fn ($log) => [
                            'id' => $log->user_id,
                            'name' => $users->firstWhere('id', $log->user_id)?->name,
                            'completed_at' => $log->completed_at,
                        ]),
                    ];
                });

                return [
                    'role' => ['id' => $role->id, 'name' => $role->name],
                    'users' => $users,
                    'tasks' => $taskData,
                ];
            })
            ->filter()
            ->values();

        $summary = [
            'total_tasks' => $tasksByRole->sum(fn ($r) => $r['tasks']->count()),
            'total_users' => $tasksByRole->sum(fn ($r) => $r['users']->count()),
            'total_completions' => $tasksByRole->sum(fn ($r) => $r['tasks']->sum('completed_count')),
        ];

        return Inertia::render('Admin/FixedTasks/Progress', [
            'rolesData' => $tasksByRole,
            'summary' => $summary,
            'date' => $date->format('Y-m-d'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
            'role_ids' => ['required', 'array', 'min:1'],
            'role_ids.*' => ['exists:' . config('permission.table_names.roles', 'roles') . ',id'],
        ]);

        $task = FixedTask::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
            'created_by' => auth()->id(),
        ]);

        $task->roles()->attach($validated['role_ids']);

        return redirect()->back()->with('success', 'تم إنشاء المهمة اليومية بنجاح');
    }

    public function update(Request $request, FixedTask $fixedTask)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
            'role_ids' => ['required', 'array', 'min:1'],
            'role_ids.*' => ['exists:' . config('permission.table_names.roles', 'roles') . ',id'],
        ]);

        $fixedTask->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        $fixedTask->roles()->sync($validated['role_ids']);

        return redirect()->back()->with('success', 'تم تحديث المهمة اليومية بنجاح');
    }

    public function destroy(FixedTask $fixedTask)
    {
        $fixedTask->delete();

        return redirect()->back()->with('success', 'تم حذف المهمة اليومية بنجاح');
    }
}
