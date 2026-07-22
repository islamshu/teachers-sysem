<?php

namespace App\Notifications;

use App\Models\GeneralTask;
use App\Notifications\Channels\TelegramChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GeneralTaskAssigned extends Notification
{
    use Queueable;

    public GeneralTask $task;
    public string $action;

    public function __construct(GeneralTask $task, string $action = 'created')
    {
        $this->task = $task;
        $this->action = $action;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast', TelegramChannel::class];
    }

    public function toArray(object $notifiable): array
    {
        $title = match ($this->action) {
            'created' => 'مهمة جديدة مُعيّنة لك',
            'updated' => 'تم تحديث مهمة مُعيّنة لك',
            'deleted' => 'تم حذف مهمة مُعيّنة لك',
            default => 'تحديث على مهمة',
        };

        return [
            'title' => $title,
            'body' => "{$this->task->name}",
            'url' => route('general-tasks.user.index'),
            'general_task_id' => $this->task->id,
        ];
    }

    public function toTelegram(object $notifiable): array
    {
        $icon = match ($this->action) {
            'created' => '📋',
            'updated' => '✏️',
            'deleted' => '🗑️',
            default => '📌',
        };

        $title = match ($this->action) {
            'created' => 'مهمة جديدة مُعيّنة لك',
            'updated' => 'تم تحديث مهمة مُعيّنة لك',
            'deleted' => 'تم حذف مهمة مُعيّنة لك',
            default => 'تحديث على مهمة',
        };

        $start = \Carbon\Carbon::parse($this->task->getRawOriginal('start_at'))->format('Y-m-d H:i');
        $end = \Carbon\Carbon::parse($this->task->getRawOriginal('end_at'))->format('Y-m-d H:i');
        $dates = "من: {$start}\nإلى: {$end}";

        return [
            'text' => "{$icon} <b>{$title}</b>\n\n{$this->task->name}\n{$dates}\n\n<a href=\"" . route('general-tasks.user.index') . "\">عرض المهام</a>",
        ];
    }
}
