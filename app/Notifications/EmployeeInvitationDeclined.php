<?php

namespace App\Notifications;

use App\Models\EmployeeInvitation;
use App\Notifications\Channels\TelegramChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EmployeeInvitationDeclined extends Notification
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
            'title' => 'تم رفض دعوة العمل',
            'body' => "{$this->invitation->employee->name} رفض دعوتك للعمل",
            'url' => route('employee.invitations.index'),
        ];
    }

    public function toTelegram(object $notifiable): array
    {
        return [
            'text' => "❌ <b>تم رفض دعوة العمل</b>\n\n{$this->invitation->employee->name} رفض دعوتك للعمل.\n\n<a href=\"" . route('employee.invitations.index') . "\">عرض الدعوات</a>",
        ];
    }
}
