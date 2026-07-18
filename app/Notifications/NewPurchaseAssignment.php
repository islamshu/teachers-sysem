<?php

namespace App\Notifications;

use App\Models\Purchase;
use App\Notifications\Channels\TelegramChannel;
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
        return ['database', 'broadcast', TelegramChannel::class];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'طلب شراء جديد',
            'body' => "تم تكليفك بطلب شراء بمبلغ {$this->purchase->amount} ريال",
            'url' => route('employee.purchases.index'),
        ];
    }

    public function toTelegram(object $notifiable): array
    {
        return [
            'text' => "🛒 <b>طلب شراء جديد</b>\n\nتم تكليفك بطلب شراء.\nالمبلغ: {$this->purchase->amount} ريال\n\n<a href=\"" . route('employee.purchases.index') . "\">عرض الطلبات</a>",
        ];
    }
}
