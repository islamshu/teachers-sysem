<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramController extends Controller
{

public function webhook(Request $request)
{
    $message = $request->input('message');

    if (!$message) {
        return response()->json(['ok'=>true]);
    }

    $chatId = $message['chat']['id'];
    $text = $message['text'] ?? '';

    if (str_starts_with($text, '/start')) {

        $token = trim(str_replace('/start', '', $text));

        $user = User::where(
            'telegram_link_token',
            $token
        )->first();

        if ($user) {

            $user->update([
                'telegram_chat_id' => $chatId,
                'telegram_username' => $message['chat']['username'] ?? null,
                'telegram_linked_at' => now(),
                'telegram_link_token' => null,
            ]);

            $this->sendMessage(
                $chatId,
                "تم ربط حسابك بنجاح ✅"
            );
        }
    }

    return response()->json(['ok'=>true]);
}
}