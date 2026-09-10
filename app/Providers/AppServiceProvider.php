<?php

namespace App\Providers;

use App\Models\Participation;
use App\Policies\ParticipationPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

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
        RateLimiter::for('auth',function(Request $request){
                return Limit::perMinute(10)->by($request->ip());
        });

        Gate::policy(Participation::class, ParticipationPolicy::class);
    }
}
