<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DataforuserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SocialiteController;

// 1. الصفحة الرئيسية
Route::get('/', function () {
    if (!session()->has('user_id')) {
        return view('home');
    }

    $user = \App\Models\Dataforuser::find(session('user_id'));

    if ($user && $user->is_admin) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('search.index');
})->name('home');

// 2. مسارات التسجيل وتسجيل الدخول (متاحة للكل)
Route::post('/register', [DataforuserController::class, 'store'])->name('register.store');

// ============ تسجيل دخول المستخدم العادي ============
Route::get('/login', [ChatController::class, 'showUserLogin'])->name('user.login');
Route::get('/user/login', [ChatController::class, 'showUserLogin'])->name('user.login.show');
Route::post('/user/login', [ChatController::class, 'loginUser'])->name('user.login.submit');

// ============ تسجيل دخول المسؤول ============
Route::get('/admin/login', [ChatController::class, 'showAdminLogin'])->name('admin.login');
Route::post('/admin/login', [ChatController::class, 'loginAdmin'])->name('admin.login.submit');

// ============ تسجيل الخروج ============
Route::get('/logout', [ChatController::class, 'logout'])->name('logout');

// ============ تسجيل الدخول عبر Google ============
Route::get('/auth/google', [SocialiteController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [SocialiteController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// ============ إكمال التسجيل من Google ============
Route::get('/complete-google-registration', [SocialiteController::class, 'showCompleteForm'])->name('complete.google.registration');
Route::post('/complete-google-registration', [SocialiteController::class, 'storeGoogleUser'])->name('complete.google.registration.store');

// 3. المسارات المحمية (لازم يكون مسجل دخول عشان يشوفها)
Route::middleware(['check.session'])->group(function () {

    // صفحة البحث / نتائج البحث الرئيسية
    Route::get('/search', [DataforuserController::class, 'search'])->name('search.index');

    // صفحة صندوق الوارد (Inbox)
    Route::get('/inbox', [ChatController::class, 'inbox'])->name('chat.inbox');

    // صفحة الشات مع شخص معين
    Route::get('/chat/{receiverId}', [ChatController::class, 'index'])->name('chat.index');

    // جلب رسائل المحادثة بشكل Ajax
    Route::get('/chat/{receiverId}/messages', [ChatController::class, 'fetchMessages'])->name('chat.messages');

    // إرسال الرسائل (Ajax)
    Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
});

Route::middleware(['check.session', 'check.admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::post('/admin/user/{user}/toggle-subscription', [AdminController::class, 'toggleSubscription'])->name('admin.user.toggleSubscription');
});
