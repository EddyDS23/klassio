<?php

namespace App\Providers;

use App\Models\Participation;
use App\Policies\ParticipationPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Blade;

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
        // El proyecto carga Tailwind y Bootstrap desde CDN. Esta directiva
        // mantiene compatibles las vistas existentes sin iniciar Vite.
        Blade::directive('vite', fn () => "<?php echo view('partials.assets', ['theme' => request()->routeIs('admin.*') ? 'admin' : (request()->routeIs('teacher.*') ? 'teacher' : (request()->routeIs('student.*') ? 'student' : 'guest'))])->render(); ?>");

        RateLimiter::for('auth',function(Request $request){
                return Limit::perMinute(10)->by($request->ip());
        });

        Gate::policy(Participation::class, ParticipationPolicy::class);
    }
}
