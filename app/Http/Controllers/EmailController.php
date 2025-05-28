<?php

namespace App\Http\Controllers;

use App\Jobs\SendBulkEmailJob;
use App\Models\EmailCampaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
     

class EmailController extends Controller
{
    public function sendBulkEmails(Request $request)
    {
        $campaign = EmailCampaign::findOrFail($request->id);
        $htmlBody = Storage::get($campaign->email_body);
        $subject = $campaign->subject_line;

        $excludedEmails = DB::table('exclusion_lists')
            ->where('status', 1)
            ->pluck('email')
            ->map(fn($email) => strtolower(trim($email)))
            ->toArray();

        $emailsToSend = $this->extractValidEmails($campaign->csv_file, $campaign->column, $excludedEmails);

        foreach ($emailsToSend as $email) {
            // Dispatch the job to the queue
            SendBulkEmailJob::dispatch($email, $subject, $htmlBody);
        }

        return back()->with('success', 'Email campaign queued for sending successfully!');
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
    public function sendTestEmail(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'id' => 'required|integer|exists:email_campaigns,id',
            ]);

            $campaign = EmailCampaign::findOrFail($request->id);

            if (!Storage::exists($campaign->email_body)) {
                return back()->with('error', 'Email template not found.');
            }

            $htmlBody = Storage::get($campaign->email_body);
            $subject = $campaign->subject_line;

            // Dispatch the email job
            SendBulkEmailJob::dispatch($request->email, $subject, $htmlBody);

            return back()->with('success', 'Test email queued for sending successfully!');
        } catch (\Exception $e) {
            // Log the actual error if needed
            \Log::error('Error sending test email: ' . $e->getMessage());

            return back()->with('error', 'An error occurred while sending the test email.');
        }
    }
}
