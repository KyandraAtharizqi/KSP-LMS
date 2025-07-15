<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 👇 Set login page view
        Fortify::loginView(fn () => view('pages.login'));

        // 👇 Use 'registration_id' instead of 'email'
        Fortify::username(fn () => 'registration_id');

        // 👇 Custom login logic using registration_id
        Fortify::authenticateUsing(function (Request $request) {
            Validator::make($request->all(), [
                'registration_id' => ['required', 'string'],
                'password' => ['required', 'string'],
            ], [
                'registration_id.required' => 'Nomor Registrasi harus diisi.',
                'password.required' => 'Kata sandi harus diisi.',
            ])->validate();

            $user = User::where('registration_id', $request->registration_id)
                ->where('is_active', true)
                ->first();

            if ($user && Hash::check($request->password, $user->password)) {
                return $user;
            }

            return null;
        });

        // ✅ Hooks for profile, password, and registration actions
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        // ✅ Rate limiter for login
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->registration_id . $request->ip());
        });

        // ✅ Rate limiter for 2FA (if used)
        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
