<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('emails_counts', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->string('type')->nullable();
            $table->string('username')->index();
            $table->integer('emails_sent_today')->default(0);
            $table->integer('emails_remaining_today')->default(300);
            $table->date('last_reset_date')->nullable();
            $table->timestamps(); // created_at and updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emails_counts');
    }
};
