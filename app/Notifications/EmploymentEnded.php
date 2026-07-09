<?php

namespace App\Notifications;

use App\Models\Employment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EmploymentEnded extends Notification
{
    use Queueable;

    public Employment $employment;

    public function __construct(Employment $employment)
    {
        $this->employment = $employment;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'تم إنهاء التوظيف',
            'body' => 'تم إنهاء التعاقد معك من قبل المدرسة',
            'url' => route('teacher.my-school'),
            'employment_id' => $this->employment->id,
        ];
    }
}
