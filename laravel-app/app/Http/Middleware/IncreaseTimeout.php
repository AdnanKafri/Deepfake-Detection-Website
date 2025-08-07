<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IncreaseTimeout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // زيادة timeout للتحليل الصوتي
        if ($request->is('api/analyze') && $request->file_type === 'audio') {
            set_time_limit(300); // 5 دقائق للصوت
            ini_set('max_execution_time', 300);
            ini_set('memory_limit', '512M');
        } else {
            set_time_limit(120); // دقيقتان للباقي
            ini_set('max_execution_time', 120);
            ini_set('memory_limit', '256M');
        }

        return $next($request);
    }
} 