<?php

namespace App\Http\Controllers;

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SendGridWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Log the incoming request for inspection
        Log::info('SendGrid Webhook Event', $request->all());

        // You can loop through events if multiple are sent
        foreach ($request->all() as $event) {
            Log::info('Processing Event', $event);
            // Do something like update status in DB
        }

        return response()->json(['status' => 'success'], 200);
    }
}
