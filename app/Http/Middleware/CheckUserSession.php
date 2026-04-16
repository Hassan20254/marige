<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUserSession
{
    public function handle(Request $request, Closure $next)
    {
        // Luo mfiush user_id fi alsiyon, argh'uh li safat altasjil
        if (!session()->has('user_id')) {
            return redirect()->route('user.login')->with('error', 'Yajib tasjil aldokhol awalan');
        }

        // Verify user exists in database
        $userId = session('user_id');
        $user = \App\Models\Dataforuser::find($userId);
        
        if (!$user) {
            session()->forget('user_id');
            return redirect()->route('user.login')->with('error', 'User not found');
        }

        return $next($request);
    }
}