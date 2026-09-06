<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Services\AuthService;

class EnsureRole
{
    public function __construct(private AuthService $authService) {}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next,string $role): Response
    {

        $user = Auth::user();

        if(!$user){
            return redirect('/login');
        }

        if($user->role !== $role){
            return redirect($this->authService->redirectByRole($user->role));
        }

        return $next($request);
    }
}
