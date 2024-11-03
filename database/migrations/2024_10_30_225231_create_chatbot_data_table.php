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
        Schema::create('chatbot_data', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users');
            $table->string('notifbot_code')->nullable()->unique();
            $table->string('bale_notifbot_id')->nullable()->unique();
            $table->string('telegram_notifbot_id')->nullable()->unique();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_data');
    }
};
