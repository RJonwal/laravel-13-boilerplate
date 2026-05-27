<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use App\Support\BladeDirectives;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use App\Domains\Core\User\Models\User;

class AppServiceProvider extends ServiceProvider
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
        BladeDirectives::register();

        // Login rate limiter
        RateLimiter::for('login', function (Request $request) {

            $key = $request->ip().'|'.$request->email;

            $user = User::where('email', $request->email)->first();

            // Non-super-admin
            if (!$user || !$user->is_super_admin) {
                return [
                    Limit::perMinute(3)->by($key),
                    Limit::perMinutes(5, 3)->by($key)->response(function () {
                        return response()->json([
                            'message' => 'Too many login attempts. Please try again after 5 minutes.'
                        ], 429);
                    }),
                ];
            }

            // Super admin: 5 attempts per minute
            return Limit::perMinute(5,5)->by($key);
        });
    }
}
