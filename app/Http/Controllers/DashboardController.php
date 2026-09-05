<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function admin(): mixed
    {
        return view('admin.dashboard');
    }

    public function teacher(): mixed
    {
        return view('teacher.dashboard');
    }

    public function student(): mixed
    {
        return view('student.dashboard');
    }
}