<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    Schema::table('email_campaigns', function (Blueprint $table) {
        $table->timestamp('sent_at')->nullable()->after('email_body');
    });
}

public function down(): void
{
    Schema::table('email_campaigns', function (Blueprint $table) {
        $table->dropColumn('sent_at');
    });
}

};
