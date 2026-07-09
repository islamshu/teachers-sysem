<?php

namespace App\Notifications;

use App\Models\Purchase;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PurchaseCompleted extends Notification
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
            'title' => 'تم اكتمال الشراء',
            'body' => "تم اكتمال طلب الشراء بمبلغ {$this->purchase->actual_amount} ريال",
            'url' => route('admin.purchases.index'),
        ];
    }
}
