<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewRegistration extends Notification
{
    use Queueable;

    public User $registeredUser;

    public function __construct(User $registeredUser)
    {
        $this->registeredUser = $registeredUser;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $roleLabel = match ($this->registeredUser->role) {
            'teacher' => 'مدرس',
            'school' => 'مدرسة',
            default => 'مستخدم',
        };

        return [
            'title' => 'تسجيل جديد',
            'body' => "{$roleLabel} جديد: {$this->registeredUser->name}",
            'url' => $this->registeredUser->role === 'teacher'
                ? route('admin.teachers.index')
                : route('admin.schools.index'),
        ];
    }
}
