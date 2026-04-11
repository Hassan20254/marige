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
        Schema::create('dataforusers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->integer('age')->nullable();
            $table->string('gender')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->integer('height')->nullable();
            $table->integer('weight')->nullable();
            $table->string('skin_color')->nullable();
            $table->string('status')->nullable(); // الحالة الاجتماعية
            $table->string('education')->nullable();
            $table->string('job')->nullable();

            // أهم حقل للتحكم في رسايل البنات (شرط العميل)
            $table->boolean('is_subscribed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dataforusers');
    }
};
