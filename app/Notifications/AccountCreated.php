<?php

namespace App\Notifications;

use App\Notifications\Channels\TelegramChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AccountCreated extends Notification
{
    use Queueable;

    public string $password;

    public function __construct(string $password)
    {
        $this->password = $password;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast', TelegramChannel::class];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'تم إنشاء حسابك',
            'body' => 'تم إنشاء حسابك بنجاح. يمكنك تسجيل الدخول bằng البريد الإلكتروني وكلمة المرور.',
            'url' => '/login',
        ];
    }

    public function toTelegram(object $notifiable): array
    {
        return [
            'text' => "👤 <b>تم إنشاء حسابك</b>\n\nتم إنشاء حسابك بنجاح.\nالبريد الإلكتروني: {$notifiable->email}\nكلمة المرور: {$this->password}\n\nيمكنك تسجيل الدخول من خلال الرابط أدناه.\n\n<a href=\"/login\">تسجيل الدخول</a>",
        ];
    }
}
