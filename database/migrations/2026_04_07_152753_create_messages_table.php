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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            // الشخص اللي بعت الرسالة
            $table->foreignId('sender_id')->constrained('dataforusers')->onDelete('cascade');
            // الشخص اللي هيستلم الرسالة
            $table->foreignId('receiver_id')->constrained('dataforusers')->onDelete('cascade');
            // نص الرسالة
            $table->text('body');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
