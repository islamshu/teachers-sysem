<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProfileApproved extends Notification
{
    use Queueable;

    public string $profileType;

    public function __construct(string $profileType)
    {
        $this->profileType = $profileType;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        $label = $this->profileType === 'teacher' ? 'المدرس' : 'المدرسة';
        $route = $this->profileType === 'teacher' ? 'teacher-profile.edit' : 'school-profile.edit';

        return [
            'title' => 'تم اعتماد الحساب',
            'body' => "تم اعتماد حساب {$label} الخاص بك بنجاح",
            'url' => route($route),
        ];
    }
}
