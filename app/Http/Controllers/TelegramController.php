<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramController extends Controller
{

public function webhook(Request $request)
{
    Log::info('Telegram Webhook', $request->all());

    return response()->json([
        'ok' => true,
    ]);
}
}