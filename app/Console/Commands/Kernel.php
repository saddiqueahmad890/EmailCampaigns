<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log; // make sure this is imported

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */

 
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function () {
            Log::info('Scheduler closure is running...');

            $campaigns = \App\Models\EmailCampaign::where('scheduled_date', '<=', now())
                ->get();

            Log::info('Found ' . $campaigns->count() . ' campaigns.');

            foreach ($campaigns as $campaign) {
                Log::info("📤 Dispatching campaign ID: {$campaign->id}");
                dispatch(new \App\Jobs\SendScheduledEmailCampaign($campaign));
            }
        })->everyMinute();
    }




    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
