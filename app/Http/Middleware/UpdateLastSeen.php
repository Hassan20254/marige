<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Dataforuser;
use Carbon\Carbon;

class UpdateLastSeen
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // Update last_seen and is_online only if user is logged in
        if (session('user_id')) {
            Dataforuser::where('id', session('user_id'))
                ->update([
                    'last_seen' => now(),
                    'is_online' => true
                ]);
        }
        
        return $response;
    }
}
