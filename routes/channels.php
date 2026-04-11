<?php

use Illuminate\Support\Facades\Broadcast;

// بنعرف قناة خاصة لكل مستخدم بناءً على الـ ID بتاعه
Broadcast::channel('chat.{id}', function ($user, $id) {
    // جلب الـ ID من السيشن اليدوية اللي إحنا عملناها
    $sessionUserId = session('user_id');

    // بنسمح للمستخدم يدخل القناة لو الـ ID في السيشن هو نفسه الـ ID المطلوب للقناة
    return (int) $sessionUserId === (int) $id;
});