<?php

namespace App\Jobs;

use App\Jobs\SendBulkEmailJob;
use App\Models\EmailCampaign;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProcessScheduledEmailCampaigns implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $now = now();
        $campaigns = EmailCampaign::whereNotNull('scheduled_date')
            ->where('scheduled_date', '<=', $now)
            ->where('status', 'pending') // optional: track state
            ->get();

        foreach ($campaigns as $campaign) {
            $htmlBody = Storage::get($campaign->email_body);
            $subject = $campaign->subject_line;

            $excludedEmails = DB::table('exclusion_lists')
                ->where('status', 1)
                ->pluck('email')
                ->map(fn($email) => strtolower(trim($email)))
                ->toArray();

            $emails = $this->extractValidEmails($campaign->csv_file, $campaign->column, $excludedEmails);

            foreach ($emails as $email) {
                SendBulkEmailJob::dispatch($email, $subject, $htmlBody);
            }

            $campaign->update(['status' => 'sent']); // optional status update
        }
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

