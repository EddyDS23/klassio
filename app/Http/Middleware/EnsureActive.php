<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureActive
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        if(!$user){
            return redirect('/login');
        }

        if($user->status !== 'active'){
            Auth::logout();
            return redirect('/login')->withErrors(['email'=>'Tu cuenta no esta activa']);
        }

        return $next($request);
    }
}
