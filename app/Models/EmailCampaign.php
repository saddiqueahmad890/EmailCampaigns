<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailCampaign extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'email_campaigns';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $guarded = ['id'];
    
      public function test_emails()
    {
        return $this->hasMany(TestEmail::class);
    }

    public function history()
    {
        return $this->hasMany(CampaignHistory::class);
    }
}
