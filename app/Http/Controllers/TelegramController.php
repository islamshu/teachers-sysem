<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TelegramController extends Controller
{
    public function telegramConnect(Request $request): JsonResponse
    {
        $user = $request->user();

        $user->update([
            'telegram_link_token' => Str::random(40),
        ]);

        $botUsername = config('services.telegram.bot_username');
        $link = "https://t.me/{$botUsername}?start={$user->fresh()->telegram_link_token}";

        return response()->json(['link' => $link]);
    }

    public function webhook(Request $request)
    {
        $message = $request->input('message');

        if (!$message) {
            return response()->json(['ok' => true]);
        }

        $chatId = $message['chat']['id'];
        $text = $message['text'] ?? '';

        if (str_starts_with($text, '/start')) {
            $token = trim(str_replace('/start', '', $text));

            $user = User::where('telegram_link_token', $token)->first();

            if ($user) {
                $user->update([
                    'telegram_chat_id' => $chatId,
                    'telegram_username' => $message['chat']['username'] ?? null,
                    'telegram_linked_at' => now(),
                    'telegram_link_token' => null,
                ]);

                $this->sendMessage(
                    $chatId,
                    "تم ربط حسابك بنجاح ✅\nمرحباً {$user->name}"
                );
            }
        }

        return response()->json(['ok' => true]);
    }

    private function sendMessage(int $chatId, string $text): void
    {
        $botToken = config('services.telegram.token');

        Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $text,
        ]);
    }
}
