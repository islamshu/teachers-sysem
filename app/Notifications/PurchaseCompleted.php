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
        $employee = $this->purchase->employees->first();
        return [
            'title' => 'طلب شراء بانتظار المراجعة',
            'body' => "الموظف " . ($employee ? $employee->name : '') . " أكمل طلب الشراء #{$this->purchase->id}: {$this->purchase->item_name} بمبلغ {$this->purchase->actual_amount} ريال - بانتظار الموافقة",
            'url' => route('admin.purchases.show', $this->purchase->id),
        ];
    }
}
