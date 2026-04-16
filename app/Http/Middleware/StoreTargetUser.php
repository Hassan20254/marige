<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class StoreTargetUser
{
    public function handle(Request $request, Closure $next)
    {
        // Check if there's a target user in the request and store it in session
        if ($request->has('target_user_id')) {
            $targetUserId = $request->input('target_user_id');
            $request->session()->put('target_user_id', $targetUserId);
            
            // Debug: Log the target user ID being stored
            \Log::info('StoreTargetUser: Storing target_user_id = ' . $targetUserId);
            \Log::info('StoreTargetUser: Request URL = ' . $request->fullUrl());
            \Log::info('StoreTargetUser: All request data = ' . json_encode($request->all()));
        } else {
            \Log::info('StoreTargetUser: No target_user_id found in request');
            \Log::info('StoreTargetUser: Request URL = ' . $request->fullUrl());
        }
        
        return $next($request);
    }
}
