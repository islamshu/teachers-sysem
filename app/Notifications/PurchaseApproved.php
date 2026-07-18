<?php

namespace App\Notifications;

use App\Models\Purchase;
use App\Notifications\Channels\TelegramChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PurchaseApproved extends Notification
{
    use Queueable;

    public Purchase $purchase;

    public function __construct(Purchase $purchase)
    {
        $this->purchase = $purchase;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast', TelegramChannel::class];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'تمت الموافقة على طلب الشراء',
            'body' => "تمت الموافقة على طلب الشراء #{$this->purchase->id}: {$this->purchase->item_name} وخصم المبلغ من رصيدك",
            'url' => route('employee.purchases.index'),
        ];
    }

    public function toTelegram(object $notifiable): array
    {
        return [
            'text' => "✅ <b>تمت الموافقة على الفاتورة</b>\n\nرقم الطلب: #{$this->purchase->id}\nالمنتج: {$this->purchase->item_name}\n\nتم خصم المبلغ من رصيدك.\n\n<a href=\"" . route('employee.purchases.index') . "\">عرض الطلبات</a>",
        ];
    }
}
