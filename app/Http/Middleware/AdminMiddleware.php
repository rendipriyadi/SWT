<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('dashboard')->with('error', 'Anda harus login terlebih dahulu.');
        }

        // Check if user is admin (you can modify this logic based on your user structure)
        // For now, we'll check if the user email contains 'admin' or has a specific role
        $user = auth()->user();
        
        // Simple admin check - you can modify this based on your needs
        if (!str_contains(strtolower($user->email), 'admin') && $user->email !== 'admin@siemens.com') {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
        }

        return $next($request);
    }
}
