<?php

namespace App\Notifications;

use App\Notifications\Channels\TelegramChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TeacherRejected extends Notification
{
    use Queueable;

    public function __construct()
    {
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
            'url' => route('teacher.my-school'),
        ];
    }

    public function toTelegram(object $notifiable): array
    {
        return [
            'text' => "⚠️ <b>تم رفض طلب التوظيف</b>\n\nتم رفض طلب التوظيف الخاص بك من المدرسة.\n\n<a href=\"" . route('teacher.my-school') . "\">عرض التفاصيل</a>",
        ];
    }
}
