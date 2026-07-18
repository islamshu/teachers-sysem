<?php

namespace App\Notifications;

use App\Models\Employment;
use App\Notifications\Channels\TelegramChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class InterviewCompleted extends Notification
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
        $schoolName = $this->employment->school?->schoolProfile?->school_name ?? $this->employment->school?->name ?? 'مدرسة';

        return [
            'title' => 'تمت إجراء المقابلة',
            'body' => "{$schoolName} أجرت مقابلة معك بنجاح",
            'url' => route('teacher.my-school'),
            'employment_id' => $this->employment->id,
        ];
    }

    public function toTelegram(object $notifiable): array
    {
        $schoolName = $this->employment->school?->schoolProfile?->school_name ?? $this->employment->school?->name ?? 'مدرسة';

        return [
            'text' => "📝 <b>تمت إجراء المقابلة</b>\n\n{$schoolName} أجرت مقابلة معك.\n\n<a href=\"" . route('teacher.my-school') . "\">عرض النتيجة</a>",
        ];
    }
}
