<?php

namespace App\Http\Controllers;

use App\Jobs\SendBulkEmailJob;
use App\Models\EmailCampaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;



class EmailController extends Controller
{
    // public function sendBulkEmails(Request $request)
    // {
    //     $campaign = EmailCampaign::findOrFail($request->id);
    //     $htmlBody = Storage::get($campaign->email_body);
    //     $subject = $campaign->subject_line;

    //     $excludedEmails = DB::table('exclusion_lists')
    //         ->where('status', 1)
    //         ->pluck('email')
    //         ->map(fn($email) => strtolower(trim($email)))
    //         ->toArray();

    //     $trackingId = $this->emailData['tracking_id'] ?? 'trk_' . bin2hex(random_bytes(8));
    //     $emailsToSend = $this->extractValidEmails($campaign->csv_file, $excludedEmails, $trackingId);




    //     dd($emailsToSend);
    //     foreach ($emailsToSend as $row) {
    //         $name = $row['name'] ?? '';
    //         $to = $row['to'] ?? '';
    //         $cc = $row['cc'] ?? '';


    //         $personalizedBody = str_replace('{{ $name }}', $name, $htmlBody);

    //         SendBulkEmailJob::dispatch([
    //             'to' => $to,
    //             'cc' => $cc,
    //             'name' => $name,
    //             'subject' => $subject,
    //             'htmlBody' => $personalizedBody,
    //             'campaign_id' => $campaign->id,
    //         ]);
    //     }



    //     return back()->with('success', 'Email campaign queued for sending successfully!');
    // }

    // protected function extractValidEmails(string $csvFile, array $excludedEmails): array
    // {
    //     $validEmails = [];

    //     if (!Storage::exists($csvFile)) return $validEmails;

    //     $path = Storage::path($csvFile);
    //     if (($handle = fopen($path, 'r')) !== false) {
    //         $headers = fgetcsv($handle, 1000, ','); // Get first row (headers)
    //         $columns = array_map('strtolower', $headers); // Normalize headers

    //         $nameIndex = array_search('name', $columns);
    //         $emailIndex = array_search('email', $columns);
    //         $ccIndex = array_search('cc', $columns);

    //         while (($row = fgetcsv($handle, 1000, ',')) !== false) {
    //             $name = $nameIndex !== false ? trim($row[$nameIndex]) : '';
    //             $to = $emailIndex !== false ? trim($row[$emailIndex]) : '';
    //             $cc = $ccIndex !== false ? trim($row[$ccIndex]) : '';

    //             // Skip if excluded or empty
    //             if (!$to && !$cc) continue;
    //             if ($to && in_array(strtolower($to), $excludedEmails)) continue;
    //             if ($cc && in_array(strtolower($cc), $excludedEmails)) continue;

    //             // Auto-adjust if only CC present
    //             if (!$to && $cc) {
    //                 $to = $cc;
    //                 $cc = null;
    //             }

    //             $validEmails[] = compact('name', 'to', 'cc');
    //         }

    //         fclose($handle);
    //     }

    //     return $validEmails;
    // }
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

    $trackingId = $this->emailData['tracking_id'] ?? 'trk_' . bin2hex(random_bytes(8));
    $emailsToSend = $this->extractValidEmails($campaign->csv_file, $excludedEmails, $trackingId);

    // dd($emailsToSend); // Remove this line

    foreach ($emailsToSend as $row) {
        $name = $row['name'] ?? '';
        $to = $row['to'] ?? '';
        $cc = $row['cc'] ?? '';
        $trackingId = $row['tracking_id'];

        // Add tracking ID to email body or headers as per your requirement
        $personalizedBody = str_replace('{{ $name }}', $name, $htmlBody);

        // Example: if you want to embed tracking ID in email, you can do something like
        $personalizedBody .= "\n<!-- Tracking ID: $trackingId -->";

        SendBulkEmailJob::dispatch([
            'to' => $to,
            'cc' => $cc,
            'name' => $name,
            'subject' => $subject,
            'htmlBody' => $personalizedBody,
            'campaign_id' => $campaign->id,
            'tracking_id' => $trackingId, // pass tracking id to job if needed
        ]);
    }

    return back()->with('success', 'Email campaign queued for sending successfully!');
}

protected function extractValidEmails(string $csvFile, array $excludedEmails, string $trackingId): array
{
    $validEmails = [];

    if (!Storage::exists($csvFile)) return $validEmails;

    $path = Storage::path($csvFile);
    if (($handle = fopen($path, 'r')) !== false) {
        $headers = fgetcsv($handle, 1000, ','); // Get first row (headers)
        $columns = array_map('strtolower', $headers); // Normalize headers

        $nameIndex = array_search('name', $columns);
        $emailIndex = array_search('email', $columns);
        $ccIndex = array_search('cc', $columns);

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            $name = $nameIndex !== false ? trim($row[$nameIndex]) : '';
            $to = $emailIndex !== false ? trim($row[$emailIndex]) : '';
            $cc = $ccIndex !== false ? trim($row[$ccIndex]) : '';

            // Skip if excluded or empty
            if (!$to && !$cc) continue;
            if ($to && in_array(strtolower($to), $excludedEmails)) continue;
            if ($cc && in_array(strtolower($cc), $excludedEmails)) continue;

            // Auto-adjust if only CC present
            if (!$to && $cc) {
                $to = $cc;
                $cc = null;
            }
            $validEmails[] = compact('name', 'to', 'cc') + ['tracking_id' => $trackingId];
        }

        fclose($handle);
    }

    return $validEmails;
}



    // public function sendTestEmail(Request $request)
    // {
    //     try {
    //         $request->validate([
    //             'email' => 'required|email',
    //             'id' => 'required|integer|exists:email_campaigns,id',
    //         ]);

    //         $campaign = EmailCampaign::findOrFail($request->id);

    //         if (!Storage::exists($campaign->email_body)) {
    //             return back()->with('error', 'Email template not found.');
    //         }

    //         $htmlBody = Storage::get($campaign->email_body);
    //         $subject = $campaign->subject_line;
    //         $email = $request->email;
    //         $name = 'He/she';

    //         // Dispatch the email job

    //             SendBulkEmailJob::dispatch([
    //                 'to' => $email,
    //                 'cc' => null, // No CC for test emails
    //                 'name' => $name,
    //                 'subject' => $subject,
    //                 'htmlBody' => $htmlBody
    //             ]);

    //         return back()->with('success', 'Test email queued for sending successfully!');
    //     } catch (\Exception $e) {
    //         \Log::error('Error sending test email: ' . $e->getMessage());

    //         return back()->with('error', 'An error occurred while sending the test email.');
    //     }
    // }

}
