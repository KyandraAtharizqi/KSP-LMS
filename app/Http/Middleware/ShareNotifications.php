<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Notifikasi;

class ShareNotifications
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            view()->share('notifikasi', Notifikasi::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get());

            view()->share('unreadCount', Notifikasi::where('user_id', $user->id)
                ->where('dibaca', false)
                ->count());
        }

        return $next($request);
    }
}
