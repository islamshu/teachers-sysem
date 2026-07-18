<?php

namespace App\Notifications;

use App\Models\Employment;
use App\Notifications\Channels\TelegramChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TeacherDeclined extends Notification
{
    use Queueable;

    public Employment $employment;

    public function __construct(Employment $employment)
    {
        $this->employment = $employment;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast', TelegramChannel::class];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'تم رفض الدعوة',
            'body' => "{$this->employment->teacher->user->name} رفض دعوتك للتوظيف",
            'url' => route('school.invitations'),
            'employment_id' => $this->employment->id,
        ];
    }

    public function toTelegram(object $notifiable): array
    {
        return [
            'text' => "❌ <b>تم رفض الدعوة</b>\n\n{$this->employment->teacher->user->name} رفض دعوتك للتوظيف.\n\n<a href=\"" . route('school.invitations') . "\">عرض الدعوات</a>",
        ];
    }
}
