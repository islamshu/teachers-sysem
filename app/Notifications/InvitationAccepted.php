<?php

namespace App\Notifications;

use App\Models\Employment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class InvitationAccepted extends Notification
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
            'title' => 'قبول دعوة توظيف',
            'body' => "{$this->employment->teacher->user->name} قبل دعوتك للتوظيف",
            'url' => route('school.invitations'),
            'employment_id' => $this->employment->id,
        ];
    }
}
