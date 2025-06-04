<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignHistory extends Model
{
    protected $table = 'campaign_histories';

    // Mass Assignment 
    protected $guarded = [];
    protected $fillable = [
        'email_campaign_id',
        'emails',
        'no_of_emails',
        'emails_reached',
        'date_sent',
        'tracking_id',
    ];

    public function campaigns()
    {
        return $this->belongsTo(EmailCampaign::class);
    }
}
