<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

Route::post('/sendgrid/webhook', function (Request $request) {
    foreach ($request->all() as $event) {
        Log::info('SendGrid Event:', $event);
    }
    return response()->json(['status' => 'received']);
});
