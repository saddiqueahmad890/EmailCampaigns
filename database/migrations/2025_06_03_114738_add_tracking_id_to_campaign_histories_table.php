<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('campaign_histories', function (Blueprint $table) {
            $table->string('tracking_id')->nullable()->after('email_campaign_id');
        });
    }

    public function down(): void
    {
        Schema::table('campaign_histories', function (Blueprint $table) {
            $table->dropColumn('tracking_id');
        });
    }
};

