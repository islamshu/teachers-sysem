<?php

namespace App\Notifications;

use App\Models\EmployeeInvitation;
use App\Notifications\Channels\TelegramChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EmployeeHired extends Notification
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
            'title' => 'تم توظيفك كموظف',
            'body' => 'مبروك! تم توظيفك في المدرسة كموظف',
            'url' => route('employee.invitations.index'),
        ];
    }

    public function toTelegram(object $notifiable): array
    {
        return [
            'text' => "🎉 <b>مبروك! تم توظيفك كموظف</b>\n\nتم توظيفك في المدرسة بنجاح.\n\n<a href=\"" . route('employee.invitations.index') . "\">عرض التفاصيل</a>",
        ];
    }
}
