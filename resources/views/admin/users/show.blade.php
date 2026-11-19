@extends('admin.layouts.app')

@section('title', 'Detalle de usuario | Klassio')

@section('page-title')
    Detalle del usuario
@endsection

@section('page-actions')
    <a href="{{ route('admin.users.index') }}"
       class="inline-flex self-start items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
        </svg>
        Volver
    </a>
@endsection

@php
    $roleLabels = ['admin' => 'Administrador', 'teacher' => 'Maestro', 'student' => 'Estudiante'];
    $roleColors = [
        'admin' => 'bg-indigo-100 text-indigo-700',
        'teacher' => 'bg-slate-100 text-slate-700',
        'student' => 'bg-emerald-100 text-emerald-700',
    ];
    $statusLabels = ['active' => 'Activo', 'inactive' => 'Inactivo', 'suspended' => 'Suspendido'];
    $statusColors = [
        'active' => 'bg-emerald-100 text-emerald-700',
        'inactive' => 'bg-slate-100 text-slate-600',
        'suspended' => 'bg-red-100 text-red-700',
    ];
@endphp

@section('content')

    <!-- Tarjeta de perfil -->
    <section class="mb-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">

        <div class="flex flex-col gap-6 sm:flex-row sm:items-center">

            <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-3xl bg-indigo-600 text-3xl font-black text-white shadow-lg shadow-indigo-200">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>

            <div class="flex-1">
                <div class="flex flex-wrap items-center gap-2">
                    <h3 class="text-2xl font-black text-slate-900">
                        {{ $user->name }}
                    </h3>
                    @if ($user->id === auth()->id())
                        <span class="rounded-full bg-indigo-100 px-3 py-1 text-xs font-bold text-indigo-700">Tú</span>
                    @endif
                </div>

                <p class="mt-1 text-sm font-medium text-slate-500">
                    {{ $user->email }}
                </p>

                <div class="mt-3 flex flex-wrap gap-2">
                    <span class="inline-block rounded-full px-3 py-1 text-xs font-bold {{ $roleColors[$user->role] ?? 'bg-slate-100 text-slate-600' }}">
                        {{ $roleLabels[$user->role] ?? ucfirst($user->role) }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold {{ $statusColors[$user->status] ?? 'bg-slate-100 text-slate-600' }}">
                        <span class="h-1.5 w-1.5 rounded-full bg-current" aria-hidden="true"></span>
                        {{ $statusLabels[$user->status] ?? ucfirst($user->status) }}
                    </span>
                    <span class="inline-block rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
                        Registrado el {{ $user->created_at?->format('d/m/Y') }}
                    </span>
                </div>
            </div>

        </div>

    </section>

    <!-- Estadísticas -->
    <section class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4" aria-label="Estadísticas del usuario">

        @php
            $statIconClass = 'h-6 w-6';
            $cards = [
                [
                    'label' => 'Clases creadas',
                    'value' => $statistics['classes'],
                    'bg' => 'bg-indigo-50 text-indigo-600',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.628 48.628 0 0 1 12 20.904a48.628 48.628 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />',
                ],
                [
                    'label' => 'Clases inscritas',
                    'value' => $statistics['enrolledClasses'],
                    'bg' => 'bg-teal-50 text-teal-600',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 14.25h6.375a3.375 3.375 0 0 0 3.375-3.375V4.875a3.375 3.375 0 0 0-3.375-3.375h-6.75a3.375 3.375 0 0 0-3.375 3.375v11.25c0 3.654 2.962 6.616 6.616 6.616M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />',
                ],
                [
                    'label' => 'Participaciones',
                    'value' => $statistics['participations'],
                    'bg' => 'bg-amber-50 text-amber-600',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z" />',
                ],
                [
                    'label' => 'Equipos',
                    'value' => $statistics['teamMemberships'],
                    'bg' => 'bg-violet-50 text-violet-600',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />',
                ],
            ];
        @endphp

        @foreach ($cards as $card)
            <div class="rounded-2xl border border-slate-200 bg-white p-5 text-center shadow-sm">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl {{ $card['bg'] }}">
                    <svg class="{{ $statIconClass }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        {!! $card['icon'] !!}
                    </svg>
                </div>
                <p class="mt-4 text-3xl font-black tabular-nums text-slate-900">
                    {{ number_format($card['value']) }}
                </p>
                <p class="mt-1 text-sm font-bold text-slate-500">{{ $card['label'] }}</p>
            </div>
        @endforeach

    </section>

    <!-- Gestión del estado -->
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">

        <p class="text-xs font-bold uppercase tracking-widest text-indigo-600">
            Seguridad
        </p>
        <h3 class="mt-1 text-xl font-black text-slate-900">
            Estado de la cuenta
        </h3>

        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
            Cambia el estado de este usuario. Las cuentas suspensas o inactivas
            pierden el acceso a la plataforma de inmediato.
        </p>

        @if ($user->id === auth()->id())
            <div class="mt-4 flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4 text-amber-800" role="status">
                <svg class="mt-0.5 h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                </svg>
                <p class="text-sm font-bold">
                    Esta es tu propia cuenta, por lo que no puedes modificar su estado.
                </p>
            </div>
        @else
            <form action="{{ route('admin.users.update-status', $user->id) }}" method="POST"
                  class="mt-4 flex flex-wrap items-center gap-3">
                @csrf
                @method('PATCH')

                <label for="user-status" class="sr-only">Nuevo estado de la cuenta</label>
                <select id="user-status" name="status"
                        class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-bold text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                    @foreach ($statusLabels as $value => $label)
                        <option value="{{ $value }}" @selected($user->status === $value)>{{ $label }}</option>
                    @endforeach
                </select>

                <button type="submit"
                        class="rounded-xl bg-indigo-600 px-6 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
                    Actualizar estado
                </button>
            </form>
        @endif

    </section>

    <!-- Cambiar contraseña -->
    <section class="mt-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">

        <p class="text-xs font-bold uppercase tracking-widest text-indigo-600">
            Seguridad
        </p>
        <h3 class="mt-1 text-xl font-black text-slate-900">
            Cambiar contraseña
        </h3>

        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
            Establece una nueva contraseña para este usuario. Debe tener al menos 8 caracteres.
        </p>

        @if ($user->id === auth()->id())
            <div class="mt-4 flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4 text-amber-800" role="status">
                <p class="text-sm font-bold">
                    No puedes cambiar tu propia contraseña desde aquí. Usa tu perfil.
                </p>
            </div>
        @else
            <form action="{{ route('admin.users.update-password', $user->id) }}" method="POST"
                  class="mt-4 flex flex-col gap-4">
                @csrf
                @method('PATCH')

                <div class="flex flex-col gap-1.5">
                    <label for="password" class="text-[11px] font-bold uppercase tracking-wide text-slate-400">
                        Nueva contraseña
                    </label>
                    <input id="password" name="password" type="password" required minlength="8" maxlength="50"
                           class="rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm font-bold text-slate-800 focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">
                    @error('password')
                        <p class="text-sm font-medium text-red-600" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col gap-1.5">
                    <label for="password_confirmation" class="text-[11px] font-bold uppercase tracking-wide text-slate-400">
                        Confirmar contraseña
                    </label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required minlength="8" maxlength="50"
                           class="rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm font-bold text-slate-800 focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            class="rounded-xl bg-indigo-600 px-6 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
                        Cambiar contraseña
                    </button>
                </div>
            </form>
        @endif

    </section>

@endsection
