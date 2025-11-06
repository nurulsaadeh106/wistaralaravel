<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    public function handle(Request $request)
    {
        $message = $request->input('message', '(kosong)');
        return response()->json([
            'reply' => "Halo! Kamu mengirim pesan: $message"
        ]);
    }
}
