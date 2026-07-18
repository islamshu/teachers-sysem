<?php

namespace App\Console\Commands;

use App\Models\GeneralTask;
use App\Models\User;
use App\Notifications\TaskOverdue;
use Illuminate\Console\Command;

class CheckOverdueTasks extends Command
{
    protected $signature = 'tasks:check-overdue';
    protected $description = 'Check for overdue general tasks and notify creators + admins';

    public function handle(): int
    {
        $tasks = GeneralTask::active()
            ->where('end_at', '<=', now())
            ->where('end_at', '>=', now()->subDay())
            ->get();

        $notified = 0;

        foreach ($tasks as $task) {
            $roleUserIds = User::whereHas('roles', fn ($q) => $q->whereIn('roles.id', $task->roles->pluck('id')))
                ->pluck('id');

            $directUserIds = $task->assignedUsers->pluck('id');

            $allUserIds = $roleUserIds->merge($directUserIds)->unique();

            $completedCount = $task->logs()->whereIn('user_id', $allUserIds)->count();
            $totalCount = $allUserIds->count();

            if ($totalCount === 0 || $completedCount === $totalCount) {
                continue;
            }

            $recipients = collect();

            if ($task->creator) {
                $recipients->push($task->creator);
            }

            User::where('role', 'admin')->each(fn ($admin) => $recipients->push($admin));

            $recipients->unique('id')->each(function ($recipient) use ($task, $completedCount, $totalCount) {
                $recipient->notify(new TaskOverdue('general', $task->name, $completedCount, $totalCount, $task->id));
            });

            $notified++;
        }

        $this->info("Checked {$tasks->length} tasks, notified about {$notified} overdue tasks.");

        return Command::SUCCESS;
    }
}
