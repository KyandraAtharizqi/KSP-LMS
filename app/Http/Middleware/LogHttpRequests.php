<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogHttpRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Log hanya untuk route profile
        if (strpos($request->path(), 'profile') !== false) {
            Log::info('HTTP Request', [
                'uri' => $request->path(),
                'method' => $request->method(),
                'has_file' => $request->hasFile('profile_picture'),
                'content_type' => $request->header('Content-Type'),
                'post_data' => $request->post(),
                'files' => array_keys($request->allFiles()),
                'headers' => $request->headers->all()
            ]);
        }
        
        return $next($request);
    }
}
