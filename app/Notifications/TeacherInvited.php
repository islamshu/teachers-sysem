<?php

namespace App\Notifications;

use App\Models\Employment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TeacherInvited extends Notification
{
    use Queueable;

    public Employment $employment;

    public function __construct(Employment $employment)
    {
        $this->employment = $employment;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $schoolName = $this->employment->school?->schoolProfile?->school_name ?? $this->employment->school?->name ?? 'مدرسة';

        return [
            'title' => 'دعوة توظيف جديدة',
            'body' => "{$schoolName} تدعوك للانضمام إليها",
            'url' => route('teacher.invitations'),
            'employment_id' => $this->employment->id,
        ];
    }
}
