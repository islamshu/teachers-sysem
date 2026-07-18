<?php

namespace App\Notifications;

use App\Notifications\Channels\TelegramChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProfileRejected extends Notification
{
    use Queueable;

    public string $profileType;

    public function __construct(string $profileType)
    {
        $this->profileType = $profileType;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast', TelegramChannel::class];
    }

    public function toArray(object $notifiable): array
    {
        $label = $this->profileType === 'teacher' ? 'المدرس' : 'المدرسة';
        $route = $this->profileType === 'teacher' ? 'teacher-profile.edit' : 'school-profile.edit';

        return [
            'title' => 'تم رفض الحساب',
            'body' => "تم رفض حساب {$label} الخاص بك، يرجى مراجعة البيانات وإعادة التقديم",
            'url' => route($route),
        ];
    }

    public function toTelegram(object $notifiable): array
    {
        $label = $this->profileType === 'teacher' ? 'المدرس' : 'المدرسة';
        $route = $this->profileType === 'teacher' ? 'teacher-profile.edit' : 'school-profile.edit';

        return [
            'text' => "⚠️ <b>تم رفض الحساب</b>\n\nتم رفض حساب {$label} الخاص بك.\nيرجى مراجعة البيانات وإعادة التقديم.\n\n<a href=\"" . route($route) . "\">تعديل الملف الشخصي</a>",
        ];
    }
}
