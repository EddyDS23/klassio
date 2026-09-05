<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    public function showLogin() : mixed{

        if(Auth::check()){
            /** @var User $user */
            $user = Auth::user();
            return redirect($this->authService->redirectByRole($user->role));
        }

        return view('auth.login');
    }    

    public function login(LoginRequest $request){
        return redirect($this->authService->login($request->validated()));
    }

    public function showRegister():mixed{

        if(Auth::check()){
            /** @var User $user */
            $user = Auth::user();
            return redirect($this->authService->redirectByRole($user->role));
        }   

        return view('auth.register');

    }

    public function register(RegisterRequest $request){
        $user = $this->authService->register($request->validated());
        Auth::login($user);
        return redirect($this->authService->redirectByRole($user->role));
    }

    public function logout(Request $request){
        $this->authService->logout($request);
        return redirect('/login');
    }
}
