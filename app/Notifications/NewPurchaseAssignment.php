<?php

namespace App\Notifications;

use App\Models\Purchase;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewPurchaseAssignment extends Notification
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
            'title' => 'طلب شراء جديد',
            'body' => "تم تكليفك بطلب شراء بمبلغ {$this->purchase->amount} ريال",
            'url' => route('employee.purchases.index'),
        ];
    }
}
