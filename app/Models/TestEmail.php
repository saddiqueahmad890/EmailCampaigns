<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestEmail extends Model
{
    public function campaigns()
    {
        return $this->belongsTo(EmailCampaign::class);
    }
}
