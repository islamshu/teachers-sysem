<?php

namespace App\Notifications;

use App\Notifications\Channels\TelegramChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskCompleted extends Notification
{
    use Queueable;

    public string $taskType;
    public string $taskName;
    public string $completerName;
    public int $taskId;

    public function __construct(string $taskType, string $taskName, string $completerName, int $taskId)
    {
        $this->taskType = $taskType;
        $this->taskName = $taskName;
        $this->completerName = $completerName;
        $this->taskId = $taskId;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast', TelegramChannel::class];
    }

    public function toArray(object $notifiable): array
    {
        $label = $this->taskType === 'fixed' ? 'اليومية' : 'العامة';

        return [
            'title' => "تم إنجاز مهمة {$label}",
            'body' => "{$this->completerName} أكمل المهمة: {$this->taskName}",
            'url' => '/tasks',
        ];
    }

    public function toTelegram(object $notifiable): array
    {
        $label = $this->taskType === 'fixed' ? 'اليومية' : 'العامة';
        $icon = $this->taskType === 'fixed' ? '📌' : '📋';

        return [
            'text' => "{$icon} <b>تم إنجاز مهمة {$label}</b>\n\nالمهمة: {$this->taskName}\nأكملها: {$this->completerName}\n\n<a href=\"/tasks\">عرض المهام</a>",
        ];
    }
}
