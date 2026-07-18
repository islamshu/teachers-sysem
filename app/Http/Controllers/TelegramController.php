<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramController extends Controller
{
   public function webhook(Request $request)
{
    file_put_contents(
        storage_path('telegram.txt'),
        json_encode($request->all(), JSON_PRETTY_PRINT) . PHP_EOL,
        FILE_APPEND
    );

    return response()->json(['ok' => true]);
}
}
