<?php

namespace App\Notifications;

use App\Models\EmployeeInvitation;
use App\Notifications\Channels\TelegramChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EmployeeRejected extends Notification
{
    use Queueable;

    public EmployeeInvitation $invitation;

    public function __construct(EmployeeInvitation $invitation)
    {
        $this->invitation = $invitation;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast', TelegramChannel::class];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'تم رفض طلب التوظيف',
            'body' => 'تم رفض طلب التوظيف الخاص بك من المدرسة',
            'url' => route('employee.invitations.index'),
        ];
    }

    public function toTelegram(object $notifiable): array
    {
        return [
            'text' => "⚠️ <b>تم رفض طلب التوظيف</b>\n\nتم رفض طلب التوظيف الخاص بك من المدرسة.\n\n<a href=\"" . route('employee.invitations.index') . "\">عرض التفاصيل</a>",
        ];
    }
}
