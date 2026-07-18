<?php

namespace App\Http\Controllers\Admin;

use App\Events\TaskUpdated;
use App\Http\Controllers\Controller;
use App\Models\GeneralTask;
use App\Models\GeneralTaskLog;
use App\Models\User;
use App\Notifications\GeneralTaskAssigned;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class GeneralTaskController extends Controller
{
    public function index()
    {
        $tasks = GeneralTask::with('assignedUsers', 'roles', 'creator')
            ->latest()
            ->paginate(20);

        if (request()->wantsJson()) {
            return response()->json($tasks);
        }

        return Inertia::render('Admin/GeneralTasks/Index', [
            'tasks' => $tasks->items(),
            'nextPage' => $tasks->currentPage() < $tasks->lastPage() ? $tasks->currentPage() + 1 : null,
            'employees' => User::where('role', 'employee')->where('is_hired', true)->select('id', 'name', 'email')->get(),
            'roles' => Role::all(),
        ]);
    }

    public function show(Request $request, GeneralTask $generalTask)
    {
        $generalTask->load('assignedUsers', 'roles');

        $roleUserIds = User::whereHas('roles', fn ($q) => $q->whereIn('roles.id', $generalTask->roles->pluck('id')))
            ->pluck('id');

        $directUserIds = $generalTask->assignedUsers->pluck('id');

        $allUserIds = $roleUserIds->merge($directUserIds)->unique();

        $users = User::whereIn('id', $allUserIds)
            ->select('id', 'name', 'email')
            ->get()
            ->map(function ($user) use ($generalTask) {
                $log = GeneralTaskLog::where('general_task_id', $generalTask->id)
                    ->where('user_id', $user->id)
                    ->first();

                $status = 'not_done';
                if ($log) {
                    $status = $log->completed_at->lte($generalTask->end_at)
                        ? 'done_before_finish'
                        : 'done_after_finish';
                }

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'status' => $status,
                    'completed_at' => $log?->completed_at,
                ];
            });

        $sort = $request->sort ?? 'name';
        $order = $request->order ?? 'asc';
        $filter = $request->filter;

        $statusOrder = ['not_done' => 0, 'done_before_finish' => 1, 'done_after_finish' => 2];

        if ($filter && array_key_exists($filter, $statusOrder)) {
            $users = $users->where('status', $filter);
        }

        $users = $users->sortBy(function ($user) use ($sort, $order, $statusOrder) {
            if ($sort === 'status') {
                return $statusOrder[$user['status']] ?? 99;
            }
            return $user[$sort] ?? '';
        }, SORT_REGULAR, $order === 'desc');

        return Inertia::render('Admin/GeneralTasks/Show', [
            'task' => [
                'id' => $generalTask->id,
                'name' => $generalTask->name,
                'description' => $generalTask->description,
                'start_at' => $generalTask->start_at,
                'end_at' => $generalTask->end_at,
                'is_active' => $generalTask->is_active,
            ],
            'users' => $users->values()->all(),
            'assigned_users_count' => $generalTask->assignedUsers->count(),
            'roles' => $generalTask->roles,
            'sort' => $sort,
            'order' => $order,
            'filter' => $filter,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after_or_equal:start_at'],
            'is_active' => ['boolean'],
            'user_ids' => ['nullable', 'array'],
            'user_ids.*' => ['exists:users,id'],
            'role_ids' => ['nullable', 'array'],
            'role_ids.*' => ['exists:' . config('permission.table_names.roles', 'roles') . ',id'],
        ]);

        if (empty($validated['user_ids']) && empty($validated['role_ids'])) {
            return redirect()->back()->with('error', 'يجب تعيين المهمة لمستخدم واحد على الأقل أو دور واحد');
        }

        $task = GeneralTask::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'start_at' => Carbon::parse($validated['start_at']),
            'end_at' => Carbon::parse($validated['end_at']),
            'is_active' => $validated['is_active'] ?? true,
            'created_by' => auth()->id(),
        ]);

        if (!empty($validated['user_ids'])) {
            $task->assignedUsers()->attach($validated['user_ids']);
        }

        if (!empty($validated['role_ids'])) {
            $task->roles()->attach($validated['role_ids']);
        }

        $this->notifyAssignedUsers($task, 'created');

        TaskUpdated::dispatch('general', 'created', ['task_id' => $task->id]);

        return redirect()->back()->with('success', 'تم إنشاء المهمة العامة بنجاح');
    }

    public function update(Request $request, GeneralTask $generalTask)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after_or_equal:start_at'],
            'is_active' => ['boolean'],
            'user_ids' => ['nullable', 'array'],
            'user_ids.*' => ['exists:users,id'],
            'role_ids' => ['nullable', 'array'],
            'role_ids.*' => ['exists:' . config('permission.table_names.roles', 'roles') . ',id'],
        ]);

        if (empty($validated['user_ids']) && empty($validated['role_ids'])) {
            return redirect()->back()->with('error', 'يجب تعيين المهمة لمستخدم واحد على الأقل أو دور واحد');
        }

        $generalTask->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'start_at' => Carbon::parse($validated['start_at']),
            'end_at' => Carbon::parse($validated['end_at']),
            'is_active' => $validated['is_active'] ?? true,
        ]);

        $generalTask->assignedUsers()->sync($validated['user_ids'] ?? []);
        $generalTask->roles()->sync($validated['role_ids'] ?? []);

        $this->notifyAssignedUsers($generalTask, 'updated');

        TaskUpdated::dispatch('general', 'updated', ['task_id' => $generalTask->id]);

        return redirect()->back()->with('success', 'تم تحديث المهمة العامة بنجاح');
    }

    public function destroy(GeneralTask $generalTask)
    {
        $this->notifyAssignedUsers($generalTask, 'deleted');

        $generalTask->delete();

        TaskUpdated::dispatch('general', 'deleted', ['task_id' => $generalTask->id]);

        return redirect()->back()->with('success', 'تم حذف المهمة العامة بنجاح');
    }

    private function notifyAssignedUsers(GeneralTask $task, string $action): void
    {
        $roleUserIds = User::whereHas('roles', fn ($q) => $q->whereIn('roles.id', $task->roles->pluck('id')))
            ->pluck('id');

        $directUserIds = $task->assignedUsers->pluck('id');

        $allUserIds = $roleUserIds->merge($directUserIds)->unique();

        User::whereIn('id', $allUserIds)->each(function ($user) use ($task, $action) {
            $user->notify(new GeneralTaskAssigned($task, $action));
        });
    }
}
