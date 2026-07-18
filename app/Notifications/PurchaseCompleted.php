<?php

namespace App\Notifications;

use App\Models\Purchase;
use App\Notifications\Channels\TelegramChannel;
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
        return ['database', 'broadcast', TelegramChannel::class];
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

    public function toTelegram(object $notifiable): array
    {
        $employee = $this->purchase->employees->first();

        return [
            'text' => "📋 <b>طلب شراء بانتظار المراجعة</b>\n\nالموظف: " . ($employee ? $employee->name : 'غير معروف') . "\nرقم الطلب: #{$this->purchase->id}\nالمنتج: {$this->purchase->item_name}\nالمبلغ: {$this->purchase->actual_amount} ريال\n\n<a href=\"" . route('admin.purchases.show', $this->purchase->id) . "\">مراجعة الطلب</a>",
        ];
    }
}
