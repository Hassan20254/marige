<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Dataforuser;

class CheckAdminSession
{
    public function handle(Request $request, Closure $next)
    {
        $userId = session('user_id');

        if (!$userId) {
            return redirect()->route('home')->with('error', 'يجب تسجيل الدخول أولاً');
        }

        $user = Dataforuser::find($userId);

        if (!$user || !$user->is_admin) {
            return redirect()->route('home')->with('error', 'لا تملك صلاحيات المشرف');
        }

        return $next($request);
    }
}
