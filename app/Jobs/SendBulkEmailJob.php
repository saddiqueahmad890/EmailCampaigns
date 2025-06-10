<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendBulkEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    protected $subject;
    protected $htmlBody;

    public function __construct(public array $emailData) {}
  

    public function handle()
    {
        $to = $this->emailData['to'];
        $cc = $this->emailData['cc'] ?? null;
        $subject = $this->emailData['subject'];
        $htmlBody = $this->emailData['htmlBody'];
        $campaignId = $this->emailData['campaign_id'] ?? null;
        $trackingId = $this->emailData['tracking_id'] ?? null;

      
        $emailCount = DB::table('email_counts')->where('type', 'sendgrid')->first();
        if (!$emailCount || $emailCount->emails_remaining_today <= 0) return;

        $sendGridApiKey = env($emailCount->username);
        [$fromEmail, $fromName] = match ($emailCount->username) {
            // 'SENDGRID_API_KEY_2' => ['stabilityofpakistaneconomy@gmail.com', 'Demo'],
            'SENDGRID_API_KEY_2' => ['hamzakhanbangash08@gmail.com', 'Demoapii'],
            // 'SENDGRID_API_KEY_2' => ['info.greengen@crm-labloid.com', 'GREENGEN GROUP SRL'],

        };

        $personalization = [
            'to' => [['email' => $to]],
            'subject' => $subject,
        ];

        if (!empty($cc)) {
            $personalization['cc'] = [['email' => $cc]];
        }

        $response = Http::withToken($sendGridApiKey)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post('https://api.sendgrid.com/v3/mail/send', [
                'personalizations' => [$personalization],
                'content' => [[
                    'type' => 'text/html',
                    'value' => $htmlBody
                ]],
                'from' => ['email' => $fromEmail, 'name' => $fromName],
                'reply_to' => ['email' => 'samuele.greengen@gmail.com', 'name' => 'Samuele Greengen'],
                'custom_args' => [
                    'campaign_id' => $campaignId,
                    'recipient_email' => $to,
                    'tracking_id' => $trackingId
                ]
            ]);

        if ($response->successful()) {
            DB::table('email_counts')->where('id', $emailCount->id)->update([
                'emails_sent_today' => $emailCount->emails_sent_today + 1,
                'emails_remaining_today' => $emailCount->emails_remaining_today - 1,
                'updated_at' => now()
            ]);
        } else {
            Log::error('SendGrid email failed', [
                'response' => $response->body(),
                'to' => $to,
                'cc' => $cc
            ]);
        }
    }
}
