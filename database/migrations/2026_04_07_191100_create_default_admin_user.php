<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('dataforusers')) {
            return;
        }

        $admin = DB::table('dataforusers')->where('email', 'admin@example.com')->first();

        if (!$admin) {
            DB::table('dataforusers')->insert([
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('Admin@2024!Secure#Pass'),
                'gender' => 'male',
                'age' => 30,
                'country' => 'Egypt',
                'city' => 'Cairo',
                'height' => 175,
                'weight' => 75,
                'skin_color' => 'قمحي',
                'status' => 'أعزب',
                'education' => 'جامعي',
                'job' => 'مدير',
                'is_subscribed' => true,
                'is_admin' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('dataforusers')->where('email', 'admin@example.com')->delete();
    }
};
