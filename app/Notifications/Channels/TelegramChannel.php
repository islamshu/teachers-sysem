<?php

namespace App\Notifications\Channels;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramChannel
{
    protected string $botToken;
    protected PendingRequest $http;

    public function __construct()
    {
        $this->botToken = config('services.telegram.token');
        $this->http = Http::timeout(10);
    }

    public function send(object $notifiable, Notification $notification): void
    {
        $chatId = $notifiable->telegram_chat_id;

        if (!$chatId) {
            return;
        }

        $message = $notification->toTelegram($notifiable);

        if (!$message || empty($message['text'])) {
            return;
        }

        try {
            $this->http->post("https://api.telegram.org/bot{$this->botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message['text'],
                'parse_mode' => 'HTML',
            ]);
        } catch (\Exception $e) {
            Log::error('Telegram notification failed', [
                'user_id' => $notifiable->id,
                'chat_id' => $chatId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
