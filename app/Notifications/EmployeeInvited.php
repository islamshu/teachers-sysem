<?php

namespace App\Notifications;

use App\Models\EmployeeInvitation;
use App\Notifications\Channels\TelegramChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EmployeeInvited extends Notification
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
        $schoolName = $this->invitation->school?->schoolProfile?->school_name ?? $this->invitation->school?->name ?? 'مدرسة';

        return [
            'title' => 'دعوة عمل جديدة',
            'body' => "{$schoolName} تدعوك للانضمام كموظف",
            'url' => route('employee.invitations.index'),
        ];
    }

    public function toTelegram(object $notifiable): array
    {
        $schoolName = $this->invitation->school?->schoolProfile?->school_name ?? $this->invitation->school?->name ?? 'مدرسة';

        return [
            'text' => "📨 <b>دعوة عمل جديدة</b>\n\n{$schoolName} تدعوك للانضمام كموظف.\n\n<a href=\"" . route('employee.invitations.index') . "\">عرض الدعوة</a>",
        ];
    }
}
