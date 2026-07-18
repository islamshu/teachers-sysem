<?php

namespace App\Notifications;

use App\Models\Purchase;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PurchaseRejected extends Notification
{
    use Queueable;

    public Purchase $purchase;

    public function __construct(Purchase $purchase)
    {
        $this->purchase = $purchase;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'تم رفض طلب الشراء',
            'body' => "تم رفض طلب الشراء #{$this->purchase->id}: {$this->purchase->item_name}. السبب: {$this->purchase->rejection_reason}",
            'url' => route('employee.purchases.index'),
        ];
    }
}
