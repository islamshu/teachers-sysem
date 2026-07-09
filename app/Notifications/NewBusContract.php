<?php

namespace App\Notifications;

use App\Models\BusContract;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewBusContract extends Notification
{
    use Queueable;

    public BusContract $contract;

    public function __construct(BusContract $contract)
    {
        $this->contract = $contract;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'عقد باص جديد',
            'body' => "تم إضافة عقد باص جديد بواسطة {$this->contract->owner_name}",
            'url' => route('admin.bus-contracts.show', $this->contract->id),
        ];
    }
}
