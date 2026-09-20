<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAdminUserRequest;
use App\Http\Requests\Admin\UpdateUserStatusRequest;
use App\Models\Participation;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query()->withCount('classes');

        if ($request->has('search') && trim((string) $request->input('search')) !== '') {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $users = $query->orderByDesc('id')->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(StoreAdminUserRequest $request): RedirectResponse
    {
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'role' => $request->input('role'),
            'status' => 'active',
        ]);

        return redirect()->route('admin.users.show', $user->id)
            ->with('success', "El usuario {$user->name} fue creado correctamente.");
    }

    public function show(int $id): View
    {
        $user = User::withCount([
            'classes',
            'enrollmentClasses',
        ])->findOrFail($id);

        $statistics = [
            'classes' => $user->classes_count,
            'enrolledClasses' => $user->enrollment_classes_count,
            'participations' => Participation::where('student_id', $user->id)->count(),
            'teamMemberships' => TeamMember::where('student_id', $user->id)->count(),
        ];

        return view('admin.users.show', compact('user', 'statistics'));
    }

    public function updateStatus(UpdateUserStatusRequest $request, int $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        $status = $request->input('status');

        if ($user->id === (int) $request->user()->id) {
            return redirect()->back()->with('error', 'No puedes cambiar el estado de tu propia cuenta.');
        }

        if ($user->role === 'admin' && $status !== 'active') {
            $activeAdmins = User::query()
                ->where('role', 'admin')
                ->where('status', 'active')
                ->whereKeyNot($user->id)
                ->count();

            if ($activeAdmins === 0) {
                return redirect()->back()->with('error', 'No puedes suspender al último administrador activo del sistema.');
            }
        }

        $user->update(['status' => $status]);

        return redirect()->back()->with('success', "El estado del usuario {$user->name} fue actualizado a «{$status}».");
    }
}