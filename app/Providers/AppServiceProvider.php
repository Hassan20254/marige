<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. ضبط الـ HTTPS للموقع في بيئة الإنتاج
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // 2. تنفيذ الـ migration أوتوماتيكياً عند تشغيل السيرفر
        // ملاحظة: بعد التأكد من عمل الموقع بنجاح، يفضل حذف هذا السطر
        try {
            Artisan::call('migrate --force');
        } catch (\Exception $e) {
            // تجاهل أي خطأ إذا كانت الجداول موجودة بالفعل
        }
    }
}