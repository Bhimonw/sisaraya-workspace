<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateLastSeen
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            // Update last_seen_at every 1 minute to avoid too many DB writes
            // Using DB query for better performance
            $lastUpdate = $user->last_seen_at;
            
            if (!$lastUpdate || $lastUpdate->diffInMinutes(now()) >= 1) {
                \DB::table('users')
                    ->where('id', $user->id)
                    ->update(['last_seen_at' => now()]);
                
                // Refresh the model instance
                $user->last_seen_at = now();
            }
        }
        
        return $next($request);
    }
}
