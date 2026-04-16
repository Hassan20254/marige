<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Dataforuser;

class UpdateUserLastSeen
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is logged in
        if (session('user_id')) {
            $userId = session('user_id');
            
            // Update user's last_seen and is_online status
            Dataforuser::where('id', $userId)->update([
                'last_seen' => now(),
                'is_online' => true
            ]);
        }
        
        return $next($request);
    }
}
