<?php

namespace App\Notifications;

use App\Notifications\Channels\TelegramChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskOverdue extends Notification
{
    use Queueable;

    public string $taskType;
    public string $taskName;
    public int $completedCount;
    public int $totalCount;
    public int $taskId;

    public function __construct(string $taskType, string $taskName, int $completedCount, int $totalCount, int $taskId)
    {
        $this->taskType = $taskType;
        $this->taskName = $taskName;
        $this->completedCount = $completedCount;
        $this->totalCount = $totalCount;
        $this->taskId = $taskId;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast', TelegramChannel::class];
    }

    public function toArray(object $notifiable): array
    {
        $label = $this->taskType === 'fixed' ? 'اليومية' : 'العامة';
        $pending = $this->totalCount - $this->completedCount;

        return [
            'title' => "مهمة {$label} منتهية",
            'body' => "المهمة: {$this->taskName} — {$pending} من {$this->totalCount} لم يكملوا",
            'url' => '/tasks',
        ];
    }

    public function toTelegram(object $notifiable): array
    {
        $label = $this->taskType === 'fixed' ? 'اليومية' : 'العامة';
        $icon = $this->taskType === 'fixed' ? '⏰' : '📋';
        $pending = $this->totalCount - $this->completedCount;

        return [
            'text' => "{$icon} <b>مهمة {$label} منتهية الوقت</b>\n\nالمهمة: {$this->taskName}\nمكتمل: {$this->completedCount} / {$this->totalCount}\nلم يكملوا: {$pending}\n\n<a href=\"/tasks\">عرض المهام</a>",
        ];
    }
}
