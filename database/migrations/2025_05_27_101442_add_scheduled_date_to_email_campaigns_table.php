<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('email_campaigns', function (Blueprint $table) {
            $table->dateTime('scheduled_date')->nullable()->after('csv_file');
        });
    }

    public function down()
    {
        Schema::table('email_campaigns', function (Blueprint $table) {
            $table->dropColumn('scheduled_date');
        });
    }
};
