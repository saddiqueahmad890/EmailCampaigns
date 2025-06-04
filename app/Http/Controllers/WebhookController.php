<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\CampaignHistory;
use Carbon\Carbon;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $events = $request->all();

        Log::info('Webhook payload:', $request->all());

    foreach ($request->all() as $event) {
        $email = $event['email'] ?? null;
        $eventType = $event['event'] ?? null;
        $campaignId = $event['campaign_id'] ?? null;
        $trackingId = $event['tracking_id'] ;
        $timestamp = $event['timestamp'] ?? now()->timestamp;

        if (!$campaignId || !$email || !$eventType || !$trackingId) {
            Log::warning('Missing data, skipping event', $event);
            continue;
        }

        Log::info("Processing event", compact('email', 'eventType', 'campaignId','trackingId'));

        $history = CampaignHistory::where('tracking_id', $trackingId)
                                  ->orderBy('id', 'desc')
                                  ->first();

        if ($history) {
            $existingEmails = unserialize($history->emails) ?? [];
            $found = false;

            foreach ($existingEmails as &$emailEntry) {
                if ($emailEntry['email'] === $email) {
                    $found = true;

                    if ($eventType === 'open') {
                        $emailEntry['is_opened'] = true;
                        $emailEntry['opened_at'] = date('Y-m-d H:i:s', $timestamp);
                    } elseif ($eventType === 'click') {
                        $emailEntry['is_clicked'] = true;
                        $emailEntry['clicked_at'] = date('Y-m-d H:i:s', $timestamp);
                        $emailEntry['clicked_url'] = $event['url'] ?? null;
                    }
                    break;
                }
            }

            if (!$found) {
                $existingEmails[] = [
                    'email' => $email,
                    'sent_at' => null,
                    'is_opened' => $eventType === 'open',
                    'opened_at' => $eventType === 'open' ? date('Y-m-d H:i:s', $timestamp) : null,
                    'is_clicked' => $eventType === 'click',
                    'clicked_at' => $eventType === 'click' ? date('Y-m-d H:i:s', $timestamp) : null,
                    'clicked_url' => $eventType === 'click' ? ($event['url'] ?? null) : null,
                ];
            }

            $history->emails = serialize($existingEmails);
            $history->no_of_emails = collect($existingEmails)->pluck('email')->unique()->count();
            $history->emails_reached = collect($existingEmails)->where('is_opened', true)->count();
            $history->no_of_clicked_emails = collect($existingEmails)->where('is_clicked', true)->count();
            $history->save();
        } else {
            $newEmailEntry = [
                'email' => $email,
                'sent_at' => null,
                'is_opened' => $eventType === 'open',
                'opened_at' => $eventType === 'open' ? date('Y-m-d H:i:s', $timestamp) : null,
                'is_clicked' => $eventType === 'click',
                'clicked_at' => $eventType === 'click' ? date('Y-m-d H:i:s', $timestamp) : null,
                'clicked_url' => $eventType === 'click' ? ($event['url'] ?? null) : null,
            ];

            CampaignHistory::create([
                'email_campaign_id' => $campaignId,
                'tracking_id'=> $trackingId,
                'emails' => serialize([$newEmailEntry]),
                'no_of_emails' => 1,
                'emails_reached' => $eventType === 'open' ? 1 : 0,
                'no_of_clicked_emails' => $eventType === 'click' ? 1 : 0,
                'date_sent' => now()->toDateTimeString(),
            ]);
        }
    }

    return response()->json(['status' => 'ok']);
    }


}
