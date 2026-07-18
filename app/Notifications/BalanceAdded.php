<?php

namespace App\Notifications;

use App\Notifications\Channels\TelegramChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BalanceAdded extends Notification
{
    use Queueable;

    public float $amount;

    public function __construct(float $amount)
    {
        $this->amount = $amount;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast', TelegramChannel::class];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'تم إضافة رصيد',
            'body' => "تم إضافة {$this->amount} ريال إلى رصيدك",
            'url' => '/purchases',
        ];
    }

    public function toTelegram(object $notifiable): array
    {
        return [
            'text' => "💰 <b>تم إضافة رصيد</b>\n\nتم إضافة {$this->amount} ريال إلى رصيدك.\n\n<a href=\"/purchases\">عرض المشتريات</a>",
        ];
    }
}
