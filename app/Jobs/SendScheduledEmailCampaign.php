<?php

namespace App\Jobs;

use App\Models\EmailCampaign;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class SendScheduledEmailCampaign implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public EmailCampaign $campaign;

    public function __construct(EmailCampaign $campaign)
    {
        $this->campaign = $campaign;
    }

    public function handle(): void
    {
        Log::info("Started sending campaign ID: {$this->campaign->id}");

        $excludedEmails = DB::table('exclusion_lists')
            ->where('status', 1)
            ->pluck('email')
            ->map(fn($email) => strtolower(trim($email)))
            ->toArray();

        $emailsToSend = $this->extractValidEmails(
            $this->campaign->csv_file,
            $this->campaign->column,
            $excludedEmails
        );

        Log::info("Extracted " . count($emailsToSend) . " valid emails.");


        $subject = $this->campaign->subject_line;
        $htmlBody = Storage::get($this->campaign->email_body);

        foreach ($emailsToSend as $email) {
            \App\Jobs\SendBulkEmailJob::dispatch($email, $subject, $htmlBody);
        }
        Log::info("Campaign ID {$this->campaign->id} marked as sent.");

        // Mark campaign as sent so we don't send again
        $this->campaign->update(['sent_at' => now()]);
    }

    protected function extractValidEmails(string $csvFile, ?int $columnIndex, array $excludedEmails): array
    {
        $validEmails = [];

        if (!Storage::exists($csvFile) || is_null($columnIndex)) {
            return $validEmails;
        }

        $path = Storage::path($csvFile);
        if (($handle = fopen($path, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                $email = $row[$columnIndex] ?? null;
                if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $normalized = strtolower(trim($email));
                    if (!in_array($normalized, $excludedEmails)) {
                        $validEmails[] = $normalized;
                    }
                }
            }
            fclose($handle);
        }

        return $validEmails;
    }
}
