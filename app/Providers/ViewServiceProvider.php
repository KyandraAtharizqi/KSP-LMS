<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        View::composer('components.navbar', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();

                $notifikasi = Notifikasi::where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();

                $unreadCount = Notifikasi::where('user_id', $user->id)
                    ->where('dibaca', false)
                    ->count();

                $view->with(compact('notifikasi', 'unreadCount'));
            }
        });
    }
}
