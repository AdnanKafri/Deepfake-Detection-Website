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
        // Check if user is authenticated and is admin
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            // If AJAX request, return JSON response
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'غير مصرح لك بالوصول لهذه الصفحة',
                    'message' => 'تحتاج صلاحيات المدير للوصول لهذه الصفحة'
                ], 403);
            }
            
            // If regular request, redirect with error message
            return redirect()->route('dashboard')->with('error', 'غير مصرح لك بالوصول لهذه الصفحة. تحتاج صلاحيات المدير.');
        }

        return $next($request);
    }
}
