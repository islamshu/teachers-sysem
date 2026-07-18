<?php

namespace App\Notifications;

use App\Models\EmployeeInvitation;
use App\Notifications\Channels\TelegramChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EmployeeInterviewCompleted extends Notification
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
            'title' => 'تمت إجراء المقابلة',
            'body' => "{$schoolName} أجرت مقابلة عمل معك",
            'url' => route('employee.invitations.index'),
        ];
    }

    public function toTelegram(object $notifiable): array
    {
        $schoolName = $this->invitation->school?->schoolProfile?->school_name ?? $this->invitation->school?->name ?? 'مدرسة';

        return [
            'text' => "📝 <b>تمت إجراء المقابلة</b>\n\n{$schoolName} أجرت مقابلة عمل معك.\n\n<a href=\"" . route('employee.invitations.index') . "\">عرض النتيجة</a>",
        ];
    }
}
