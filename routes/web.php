<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\EmailCampaignController;
use App\Http\Controllers\GmailController;
use App\Http\Controllers\ExclusionListController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\WebhookController;

Route::get('/clear-cache', function (Request $request) {
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('config:cache');

    return response()->json(['success' => true, 'message' => 'Caches cleared successfully.']);
});

Route::get('/', [App\Http\Controllers\AdminController::class, 'index'])->name('admindashboard');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Route::get('/admin/dashboard', function () {
//     return view('dashboard.masterlayout');
// })->middleware(['auth', 'role:admin'])->name('admin.dashboard');

// Route::get('/admindashboard', [App\Http\Controllers\AdminController::class, 'index'])->name('admindashboard');

// prospottifile routes
Route::group(
    ['prefix' => "", "controller" => ExclusionListController::class],
    function () {
        Route::resource('exclusion-list', ExclusionListController::class);
        Route::post('exclusion-list/csv', [ExclusionListController::class, 'csvUploading'])->name('exclusion-list.csv');
        Route::put('/exclusion-list/{id}', [ExclusionListController::class, 'update'])->name('exclusion-list.update');
        Route::delete('/exclusion-list/{id}', [ExclusionListController::class, 'destroy'])->name('exclusion-list.destroy');
        Route::put('/exclusion-list/{id}/status', [ExclusionListController::class, 'updateStatus'])->name('exclusion-list.updateStatus');
        Route::post('exclusion-list/{id}/update-status', [ExclusionListController::class, 'updateStatus'])->name('exclusion-list.update-status');
    }
);

Route::get('/create-campaign', [EmailCampaignController::class, 'createCampaign'])->name('forms.create-campaign');
Route::post('/email-campaigns', [EmailCampaignController::class, 'store'])->name('email-campaigns.store');
Route::get('/campaign', [EmailCampaignController::class, 'showListCampaign'])->name('campaign');
Route::post('/edit-campaign/{id}', [EmailCampaignController::class, 'editCampaign'])->name('edit-campaign');
Route::get('/campaign-html/{id}', [EmailCampaignController::class, 'serveHtml'])->name('campaign.html');
Route::delete('/delete-campaign/{id}', [EmailCampaignController::class, 'deleteCampaign'])->name('delete-campaign');
Route::get('/campaign-details/{id}', [EmailCampaignController::class, 'campaignDetails'])->name('campaign.details');
Route::get('/campaigns/send', [EmailCampaignController::class, 'showForm'])->name('campaign.send.form');
Route::get('/send-email', [EmailCampaignController::class, 'sendCampaignEmail'])->name('forms.send-email');
Route::post('/check-campaign', [EmailCampaignController::class, 'checkCampaign']);
Route::post('/fetch-campaign', [EmailCampaignController::class, 'fetchCampaign']);
Route::post('/send-test-email', [EmailCampaignController::class, 'sendTestEmail'])->name('send-test-email');

// routes/web.php
Route::post('/send-emails', [EmailController::class, 'sendBulkEmails'])->name('send.emails');
Route::post('/schedule-email', [EmailCampaignController::class, 'schedule'])->name('email-campaign.schedule');
Route::post('/test-email', [EmailController::class, 'sendTestEmail'])->name('email.test');




// To download the campaign details CSV file
Route::get('/campaign/{id}/download-sent-emails', [EmailCampaignController::class, 'downloadSentEmails'])->name('campaign.download.sent');
Route::get('/campaign/{id}/download-opened-emails', [EmailCampaignController::class, 'downloadOpenedEmails'])->name('campaign.download.opened');
Route::get('/campaign/{id}/download-clicked-emails', [EmailCampaignController::class, 'downloadClickedEmails'])->name('campaign.download.clicked');
Route::get('/campaign/{id}/download-unopened-emails', [EmailCampaignController::class, 'downloadUnopenedEmails'])->name('campaign.download.unopened');

Route::post('/sendgrid/webhook', [WebhookController::class, 'handle']);
