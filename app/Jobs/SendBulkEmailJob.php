<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SendBulkEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    protected $subject;
    protected $htmlBody;

    public function __construct($email, $subject, $htmlBody) {
        $this->email = $email;
        $this->subject = $subject;
        $this->htmlBody = $htmlBody;
    }

    public function handle(): void {
        $emailCount = DB::table('email_counts')->where('type', 'sendgrid')->first();

        if (!$emailCount || $emailCount->emails_remaining_today <= 0) return;

        $sendGridApiKey = env($emailCount->username);
        [$fromEmail, $fromName] = match ($emailCount->username) {
            'SENDGRID_API_KEY_1' => ['stabilityofpakistaneconomy@gmail.com', 'Demo'],
            'SENDGRID_API_KEY_2' => ['laraveldev.crisaloid@gmail.com', 'Greengen'],
            default => ['no-reply@example.com', 'Default Sender']
        };

        $response = Http::withToken($sendGridApiKey)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post('https://api.sendgrid.com/v3/mail/send', [
                'personalizations' => [[
                    'to' => [['email' => $this->email]],
                    'subject' => $this->subject,
                ]],
                'content' => [[
                    'type' => 'text/html',
                    'value' => $this->htmlBody
                ]],
                'from' => ['email' => $fromEmail, 'name' => $fromName],
                'reply_to' => ['email' => $fromEmail, 'name' => $fromName]
            ]);

        if ($response->successful()) {
            DB::table('email_counts')->where('id', $emailCount->id)->update([
                'emails_sent_today' => $emailCount->emails_sent_today + 1,
                'emails_remaining_today' => $emailCount->emails_remaining_today - 1,
                'updated_at' => now()
            ]);
        }
    }
}
