<?php

namespace App\Http\Controllers;

use App\Models\CampaignHistory;
use App\Models\EmailCampaign;
use App\Models\emailCount;
use App\Models\TestEmail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Exception;
use Illuminate\Support\Facades\Log;
use Webklex\IMAP\Facades\Client;
use App\Helpers\EmailCampaignHelper;
use Illuminate\Support\Facades\Http;
use SendGrid\Mail\Mail;
use Illuminate\Support\Facades\DB;
use App\Helpers\sendEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class EmailCampaignController extends Controller
{
    public function schedule(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:email_campaigns,id',
            'scheduled_date' => 'required|date|after_or_equal:today',
        ]);

        $campaign = EmailCampaign::findOrFail($request->id);
        $campaign->scheduled_date = $request->scheduled_date;
        $campaign->save();

        return back()->with('success', 'Campaign scheduled successfully!');
    }

    public function createCampaign(Request $request)
    {

        return view('forms.create-campaign');
    }

    public function store(Request $request)
    {
        // Validate all required inputs
        $validated = $request->validate([
            'campaign_name' => 'required|string|max:255',
            'subject_line' => 'required|string|max:255',
            'email_body' => 'required|file',
            'leads_csv' => 'required|file',
            'column_number' => 'required'
        ]);

        // Create a new campaign with the provided name and subject
        $campaign = new EmailCampaign([
            'campaign_name' => $validated['campaign_name'],
            'subject_line' => $validated['subject_line'],
        ]);

        // Handle and store the uploaded HTML email template
        $htmlFile = $request->file('email_body');
        $htmlFileName = uniqid() . '.' . $htmlFile->getClientOriginalExtension();
        $campaign->email_body = $htmlFile->storeAs('email_templates', $htmlFileName);

        // Process the uploaded CSV file and verify the email column
        $csvFile = $request->file('leads_csv');
        $columnIndex = max(0, (int) $validated['column_number'] - 1);

        if (($handle = fopen($csvFile->getRealPath(), 'r')) !== false) {
            $header = fgetcsv($handle, 1000, ',');

            if (!isset($header[$columnIndex])) {
                fclose($handle);
                return back()->with('error', 'Invalid column number. Please check the CSV file.');
            }

            $columnName = strtolower($header[$columnIndex]);
            $hasValidEmail = $columnName === 'email';

            while (!$hasValidEmail && ($row = fgetcsv($handle, 1000, ',')) !== false) {
                if (isset($row[$columnIndex]) && filter_var($row[$columnIndex], FILTER_VALIDATE_EMAIL)) {
                    $hasValidEmail = true;
                }
            }

            fclose($handle);

            if (!$hasValidEmail) {
                return back()->with('error', 'Selected column does not contain valid emails.');
            }

            $csvFileName = uniqid() . '.' . $csvFile->getClientOriginalExtension();
            $campaign->csv_file = $csvFile->storeAs('csv_uploads', $csvFileName);
            $campaign->column = $columnIndex;
        } else {
            return back()->with('error', 'Could not open the CSV file.');
        }

        // Save the campaign to the database
        $campaign->save();

        // Redirect back with success message
        return redirect('/create-campaign')->with('success', 'Email campaign created successfully!');
    }

    public function showListCampaign()
    {

        $campaigns = EmailCampaign::all();

        // Loop through each campaign and calculate email count
        if (count($campaigns) > 0) {
            foreach ($campaigns as $campaign) {
                $emailCount = 0;
                $emailArray = [];
                $campaign->html_file_path = ''; // Default path

                // Ensure the file exists before attempting to open it
                if (Storage::exists($campaign->email_body)) {
                    // Store the file path to be passed to the view
                    $campaign->html_file_path = Storage::url($campaign->email_body);
                }

                // Ensure the file exists before attempting to open it
                if (Storage::exists($campaign->csv_file) && $campaign->column !== null) {
                    $path = Storage::path($campaign->csv_file);
                    if (($handle = fopen($path, 'r')) !== false) {
                        // fgetcsv($handle, 1000, ','); // Skip the header row

                        // Count rows with valid emails in the specified column
                        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                            if (isset($row[$campaign->column]) && filter_var($row[$campaign->column], FILTER_VALIDATE_EMAIL)) {
                                array_push($emailArray, $row[$campaign->column]);
                                $emailCount++;
                            }
                        }
                        fclose($handle);
                    }
                }
                // Attach the email count to the campaign model instance temporarily
                $campaign->email_count = $emailCount;
                $campaign->emails = $emailArray;
            }
        }

        return view('forms.campaign')->with('campaigns', $campaigns);
    }

    public function serveHtml($id)
    {
        // Find the campaign by ID
        $campaign = EmailCampaign::findOrFail($id);

        // Ensure the file exists in the private storage folder
        if (Storage::exists($campaign->email_body)) {
            // Get the content of the HTML file
            $htmlContent = Storage::get($campaign->email_body);

            // Return the content as a response with the correct headers
            return response($htmlContent, 200)
                ->header('Content-Type', 'text/html');
        }

        // Return a 404 if the file doesn't exist
        return response('File not found', 404);
    }

    public function editCampaign(Request $request, $id)
    {
        // Validate required base fields
        $request->validate([
            'campaign_name' => 'required|string|unique:email_campaigns,campaign_name,' . $id,
            'subject_line' => 'required|string|max:255',
        ]);

        // If CSV file is uploaded, validate column_number
        if ($request->hasFile('leads_csv')) {
            $request->validate([
                'column_number' => 'required|integer|min:1'
            ]);
        }

        // Get campaign or fail
        $campaign = EmailCampaign::findOrFail($id);

        // Update basic fields
        $campaign->campaign_name = $request->input('campaign_name');
        $campaign->subject_line = $request->input('subject_line');

        // Handle email_body file update
        if ($request->hasFile('email_body')) {
            if ($campaign->email_body && Storage::exists($campaign->email_body)) {
                Storage::delete($campaign->email_body);
            }

            $htmlFile = $request->file('email_body');
            $newFileName = uniqid() . '.' . $htmlFile->getClientOriginalExtension();
            $campaign->email_body = $htmlFile->storeAs('email_templates', $newFileName);
        }

        // Handle new CSV file if uploaded
        if ($request->hasFile('leads_csv')) {
            $newCsvFile = $request->file('leads_csv');
            $columnNumber = (int)$request->input('column_number') - 1;

            // Validate CSV column
            if (($handle = fopen($newCsvFile->getRealPath(), 'r')) !== false) {
                $header = fgetcsv($handle, 1000, ',');

                if (!isset($header[$columnNumber])) {
                    fclose($handle);
                    return back()->with('error', 'Invalid column number for emails. Please check the CSV file.');
                }

                $isValidColumn = strtolower($header[$columnNumber]) === 'email';
                $hasValidEmail = false;

                while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                    if (isset($row[$columnNumber]) && filter_var($row[$columnNumber], FILTER_VALIDATE_EMAIL)) {
                        $hasValidEmail = true;
                        break;
                    }
                }
                fclose($handle);

                if (!$isValidColumn && !$hasValidEmail) {
                    return back()->with('error', 'The selected column does not contain valid email addresses.');
                }

                // Delete old CSV if exists
                if ($campaign->csv_file && Storage::exists($campaign->csv_file)) {
                    Storage::delete($campaign->csv_file);
                }

                // Store new CSV file
                $newCsvFileName = uniqid() . '.' . $newCsvFile->getClientOriginalExtension();
                $storedCsvPath = $newCsvFile->storeAs('csv_uploads', $newCsvFileName);
                $campaign->csv_file = $storedCsvPath;
                $campaign->column = $columnNumber;
            } else {
                return back()->with('error', 'Unable to open the new CSV file.');
            }
        }

        // Save updated campaign
        $campaign->save();

        return back()->with('success', 'Email campaign updated successfully!');
    }

    public function deleteCampaign($id)
    {

        $campaign = EmailCampaign::findOrFail($id);

        // Delete associated test emails
        $campaign->test_emails()->delete();

        // Delete associated campaign histories
        $campaign->history()->delete();

        // Delete the HTML file if it exists
        if ($campaign->email_body && Storage::exists($campaign->email_body)) {
            Storage::delete($campaign->email_body);
        }

        // Delete the CSV file if it exists
        if ($campaign->csv_file && Storage::exists($campaign->csv_file) && $campaign->column !== null) {
            Storage::delete($campaign->csv_file);
        }

        // Delete the campaign record from the database
        $campaign->delete();

        return back()->with('success', 'Email campaign deleted successfully!');
    }

    public function showForm()
    {
        $campaigns = EmailCampaign::all();
        $smtpAccounts = emailCount::all();

        return view('forms.send-email')->with([
            'campaigns' => $campaigns,
            'smtpAccounts' => $smtpAccounts,
        ]);
    }

    public function campaignDetails(Request $request, $id)
    {
        $campaign = EmailCampaign::findOrFail($id);

        $failedEmails = [];

        // Fetch all histories for the given campaign
        $histories = CampaignHistory::where([
            'email_campaign_id' => $id,
            ['date_sent', '!=', "-"],
        ])->get();

        foreach ($histories as $history) {
            $emails = unserialize($history->emails); // Unserialize email data
            $emailCount = 0;
            $emailOpenCount = 0;
            $emailDetails = []; // Array to hold detailed email information

            foreach ($emails as $email) {
                // Update counts based on sent and opened statuses
                if (isset($email['sent_at']) && $email['sent_at'] !== null) {
                    $emailCount++;
                }
                if (isset($email['is_opened']) && $email['is_opened']) {
                    $emailOpenCount++;
                }
                // Add email details to the array
                $emailDetails[] = [
                    'email' => $email['email'],
                    'sent_date' => $email['sent_at'] ?? '-', // Default to '-' if not sent
                    'sent_status' => isset($email['opened_at']) && $email['opened_at'] !== null ? '1' : '0',
                    'click_status' => isset($email['is_clicked']) && $email['is_clicked'] ? '1' : '0',
                    'opened_status' => isset($email['is_opened']) && $email['is_opened'] ? '1' : '0',
                ];
            }
            // dd($emailDetails); 
            // Attach email statistics and details to the campaign instance temporarily
            $history->email_count = $emailCount;
            $history->email_open_count = $emailOpenCount;
            $history->email_details = $emailDetails;
            $history->failed_emails = $failedEmails;
        }

       
        $test_emails = TestEmail::where('email_campaign_id', $id)->get();

        // Return the details view with the prepared data
        return view('forms.details', compact('campaign', 'histories', 'test_emails'));
    }
    private function generateCsv(array $data, string $filename)
    {
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0",
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Email']); // Add CSV header

            foreach ($data as $row) {
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    public function checkCampaign(Request $request)
    {

        $campaignName = $request->input('campaign_name');
        $campaignId = $request->input('id');
        if ($campaignId) {
            $campaign = EmailCampaign::where([
                ['id', "!=", $campaignId],
                'campaign_name' => $campaignName,
            ])->exists();
        } else {
            $campaign = EmailCampaign::where('campaign_name', $campaignName)->exists();
        }

        if ($campaign) {
            return response()->json(['exists' => true, 'campaign' => $campaignId]);
        } else {
            return response()->json(['exists' => false]);
        }
    }
    public function fetchCampaign(Request $request)
    {
        try {
            $campaignId = $request->input('campaign_id');
            $campaign = EmailCampaign::find($campaignId);

            if (!$campaign) {
                return response()->json(['success' => false, 'message' => 'Campaign not found.']);
            }

            $emailCount = 0;
            $emailArray = [];
            $emailsToSend = [];
            $sentEmails = [];
            $campaign->html_file_path = '';

            // Get previously sent emails
            $previousHistories = CampaignHistory::where('email_campaign_id', $campaign->id)->get();

            foreach ($previousHistories as $history) {
                $emails = @unserialize($history->emails);
                if (is_array($emails)) {
                    foreach ($emails as $email) {
                        $sentEmails[] = $email['email'];
                    }
                }
            }

            $sentEmails = array_unique($sentEmails);

            // Get HTML path if it exists
            if (!empty($campaign->email_body) && Storage::disk('private')->exists($campaign->email_body)) {
                $campaign->html_file_path = Storage::disk('private')->path($campaign->email_body);
            }

            // Parse CSV file for emails
            if (!empty($campaign->csv_file) && is_numeric($campaign->column) && Storage::disk('private')->exists($campaign->csv_file)) {
                $csvPath = Storage::disk('private')->path($campaign->csv_file);
                Log::info("Reading CSV file at: " . $csvPath);

                if (($handle = fopen($csvPath, 'r')) !== false) {
                    while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                        if (!isset($row[$campaign->column])) continue;

                        $email = trim($row[$campaign->column]);

                        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            $emailArray[] = $email;
                            $emailCount++;

                            if (!in_array($email, $sentEmails)) {
                                $emailsToSend[] = $email;
                            }
                        }
                    }
                    fclose($handle);
                } else {
                    Log::error("Failed to open CSV file: $csvPath");
                }
            } else {
                Log::warning("CSV file missing or column not numeric for campaign ID: $campaignId");
            }

            // Attach parsed data to campaign object
            $campaign->email_count = $emailCount;
            $campaign->emails = $emailArray;

            $hasHistory = $previousHistories->isNotEmpty();

            return response()->json([
                'success' => true,
                'result' => view('partials.details', compact('campaign', 'hasHistory', 'sentEmails', 'emailsToSend'))->render(),
            ]);
        } catch (\Exception $e) {
            dd($e);
            Log::error('fetchCampaign Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Server error occurred while fetching the campaign.',
            ]);
        }
    }
    public function downloadClickedEmails($id)
    {
        $campaignHistory = CampaignHistory::find($id);
        $emails = [];

        $emailDetails = unserialize($campaignHistory->emails);
        foreach ($emailDetails as $email) {
            if (isset($email['is_clicked']) && $email['is_clicked']) {
                $emails[] = ['email' => $email['email']];
            }
        }

        return $this->generateCsv($emails, 'cliked_emails.csv');
    }
    public function downloadUnopenedEmails($id)
    {
        $campaignHistory = CampaignHistory::find($id);
        $emails = [];

        $emailDetails = unserialize($campaignHistory->emails);
        dd($emailDetails);
        foreach ($emailDetails as $email) {
            if (!isset($email['is_opened']) || !$email['is_opened']) {
                $emails[] = ['email' => $email['email']];
            }
        }

        return $this->generateCsv($emails, 'unopened_emails.csv');
    }
    public function downloadSentEmails($id)
    {
        $campaignHistory = CampaignHistory::find($id);
        $emails = [];

        $emailDetails = unserialize($campaignHistory->emails);
        foreach ($emailDetails as $email) {
            if (isset($email['sent_at']) && $email['sent_at'] !== null) {
                $emails[] = ['email' => $email['email']];
            }
        }

        return $this->generateCsv($emails, 'sent_emails.csv');
    }
    public function downloadOpenedEmails($id)
    {
        $campaignHistory = CampaignHistory::find($id);
        $emails = [];

        $emailDetails = unserialize($campaignHistory->emails);
        foreach ($emailDetails as $email) {
            if (isset($email['is_opened']) && $email['is_opened']) {
                $emails[] = ['email' => $email['email']];
            }
        }

        return $this->generateCsv($emails, 'opened_emails.csv');
    }
}
