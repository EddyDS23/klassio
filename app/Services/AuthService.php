<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthService{
 
    public function login(array $credentials) :string{
        
        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages(["email"=>"Credenciales Invalidas"]);
        }

        /** @var User $user */
        $user = Auth::user();

        if ($user->status !== 'active') { 
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Tu cuenta no está activa.'
            ]);
        }

        request()->session()->regenerate();

        return $this->redirectByRole($user->role);

    }

    public function register(array $data ) :User{
        return User::create($data);
    }

    public function logout(Request $request) : void{
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    public function redirectByRole(string $role) : string {    
        return match ($role) {
            "admin" => route('admin.dashboard'),
            "teacher" => route('teacher.dashboard'),
            "student" => route('student.dashboard'),
            default => route('login')
        };
    }

}