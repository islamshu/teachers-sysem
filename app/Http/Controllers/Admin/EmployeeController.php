<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\DailyTaskLog;
use App\Models\FixedTask;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = User::where('role', 'employee')
            ->with('branches', 'roles', 'school')
            ->latest()
            ->paginate(10);

        if (request()->wantsJson()) {
            return response()->json($employees);
        }

        return Inertia::render('Admin/Employees/Index', [
            'employees' => $employees->items(),
            'nextPage' => $employees->currentPage() < $employees->lastPage() ? $employees->currentPage() + 1 : null,
            'branches' => Branch::all(),
            'roles' => Role::all(),
            'schools' => User::where('role', 'school')->with('schoolProfile')->orderBy('name')->get(),
        ]);
    }

    public function tasksReport(Request $request, User $user)
    {
        if ($user->role !== 'employee') {
            return redirect()->back()->with('error', 'المستخدم ليس موظفاً');
        }

        $date = $request->date ? Carbon::parse($request->date) : Carbon::today();
        $roleIds = $user->roles->pluck('id');

        $tasks = FixedTask::active()
            ->whereHas('roles', fn ($q) => $q->whereIn('roles.id', $roleIds))
            ->with(['roles'])
            ->get()
            ->map(function ($task) use ($user, $date) {
                $log = DailyTaskLog::where('fixed_task_id', $task->id)
                    ->where('user_id', $user->id)
                    ->whereDate('date', $date)
                    ->first();

                return [
                    'id' => $task->id,
                    'name' => $task->name,
                    'description' => $task->description,
                    'roles' => $task->roles->map(fn ($r) => ['id' => $r->id, 'name' => $r->name]),
                    'completed' => (bool) $log,
                    'completed_at' => $log?->completed_at,
                ];
            });

        return response()->json([
            'employee' => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email],
            'date' => $date->format('Y-m-d'),
            'tasks' => $tasks,
            'completed_count' => $tasks->where('completed', true)->count(),
            'total_count' => $tasks->count(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'school_id' => ['required', 'exists:users,id'],
            'branch_ids' => ['required', 'array', 'min:1'],
            'branch_ids.*' => ['exists:branches,id'],
            'role_ids' => ['required', 'array', 'min:1'],
            'role_ids.*' => ['exists:roles,id'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'employee',
            'school_id' => $validated['school_id'],
        ]);

        $user->branches()->attach($validated['branch_ids']);

        $roleNames = Role::whereIn('id', $validated['role_ids'])->pluck('name');
        $user->assignRole($roleNames);

        return redirect()->back()->with('success', 'تم إنشاء الموظف بنجاح');
    }

    public function update(Request $request, User $user)
    {
        if ($user->role !== 'employee') {
            return redirect()->back()->with('error', 'المستخدم ليس موظفاً');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:8'],
            'school_id' => ['required', 'exists:users,id'],
            'branch_ids' => ['required', 'array', 'min:1'],
            'branch_ids.*' => ['exists:branches,id'],
            'role_ids' => ['required', 'array', 'min:1'],
            'role_ids.*' => ['exists:roles,id'],
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'school_id' => $validated['school_id'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        $user->branches()->sync($validated['branch_ids']);

        $roleNames = Role::whereIn('id', $validated['role_ids'])->pluck('name');
        $user->syncRoles($roleNames);

        return redirect()->back()->with('success', 'تم تحديث بيانات الموظف بنجاح');
    }

    public function destroy(User $user)
    {
        if ($user->role !== 'employee') {
            return redirect()->back()->with('error', 'المستخدم ليس موظفاً');
        }

        $user->branches()->detach();
        $user->delete();

        return redirect()->back()->with('success', 'تم حذف الموظف بنجاح');
    }
}
