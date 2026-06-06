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
        $school = $this->employment->school->schoolProfile;

        return [
            'title' => 'دعوة توظيف جديدة',
            'body' => "{$school->school_name} تدعوك للانضمام إليها",
            'url' => route('teacher.invitations'),
            'employment_id' => $this->employment->id,
        ];
    }
}
