<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EmailCampaign;
use App\Http\Controllers\EmailController;
use Illuminate\Http\Request;
use Carbon\Carbon;
class SendScheduledEmails extends Command
{
    protected $signature = 'emails:send-scheduled';
    protected $description = 'Send scheduled campaign emails';

    public function handle()
    {
        $now = Carbon::now();

        $campaigns = EmailCampaign::whereDate('scheduled_date', $now->toDateString())
            ->whereTime('scheduled_date', '<=', $now->toTimeString())
            ->whereNull('sent_at')
            ->get();

        foreach ($campaigns as $campaign) {
            // Option 1: Call controller method directly
            $controller = new \App\Http\Controllers\EmailController;
            $request = new Request(['id' => $campaign->id]);
            $controller->sendBulkEmails($request);

            // Mark campaign as sent
            $campaign->sent_at = now();
            $campaign->save();

            $this->info("Dispatched campaign: {$campaign->campaign_name}");
        }
    }
}
