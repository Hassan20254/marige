<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUserSession
{
    public function handle(Request $request, Closure $next)
    {
        // لو مفيش user_id في السيشن، ارجعه لصفحة التسجيل أو الهوم
        if (!session()->has('user_id')) {
            return redirect()->route('home')->with('error', 'يجب تسجيل الدخول أولاً');
        }

        return $next($request);
    }
}