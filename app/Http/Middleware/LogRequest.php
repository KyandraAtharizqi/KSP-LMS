<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogRequest
{
    public function handle(Request $request, Closure $next)
    {
        Log::info('Request:', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'inputs' => $request->all(),
            'hasMethodField' => $request->has('_method'),
            'methodField' => $request->get('_method'),
            'originalMethod' => $request->getMethod(),
            'session' => $request->hasSession() ? $request->session()->all() : 'No session',
        ]);

        return $next($request);
    }
}
