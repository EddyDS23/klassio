@extends('admin.layouts.app')

@section('title', 'Usuarios | Klassio')

@section('page-title')
    Gestión de usuarios
@endsection

@section('page-actions')
    <a href="{{ route('admin.users.create') }}"
       class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM4 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 10.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
        </svg>
        Nuevo usuario
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

    <!-- Filtros -->
    <section class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

        <form method="GET" action="{{ route('admin.users.index') }}"
              class="grid gap-4 md:grid-cols-4">

            <div class="md:col-span-2">
                <label for="search" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">
                    Buscar
                </label>
                <input type="text" id="search" name="search" value="{{ request('search') }}"
                       placeholder="Nombre o correo electrónico..."
                       class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
            </div>

            <div>
                <label for="role" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">
                    Rol
                </label>
                <select id="role" name="role"
                        class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                    <option value="">Todos los roles</option>
                    @foreach ($roleLabels as $value => $label)
                        <option value="{{ $value }}" @selected(request('role') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">
                    Estado
                </label>
                <select id="status" name="status"
                        class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                    <option value="">Todos los estados</option>
                    @foreach ($statusLabels as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-3 md:col-span-4">
                <button type="submit"
                        class="rounded-xl bg-indigo-600 px-6 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
                    Filtrar
                </button>

                @if (request()->hasAny(['search', 'role', 'status']))
                    <a href="{{ route('admin.users.index') }}"
                       class="rounded-xl border border-slate-200 bg-white px-6 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
                        Limpiar
                    </a>
                @endif
            </div>

        </form>

    </section>

    <!-- Mensaje cuando no hay resultados -->
    @if ($users->isEmpty())

        <section class="rounded-2xl border border-slate-200 bg-white p-12 text-center shadow-sm">

            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-400">
                <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
            </div>

            <h3 class="mt-4 text-lg font-black text-slate-900">Sin resultados</h3>

            <p class="mx-auto mt-2 max-w-sm text-sm text-slate-500">
                No se encontraron usuarios con los criterios seleccionados.
            </p>

            @if (request()->hasAny(['search', 'role', 'status']))
                <a href="{{ route('admin.users.index') }}"
                   class="mt-5 inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M2.985 14.652 5.166 12.47m12.9-5.618h4.992m0 0V2.86m0 4.992-3.18-3.18a8.25 8.25 0 0 0-13.804 3.698" />
                    </svg>
                    Limpiar filtros
                </a>
            @endif

        </section>

    @else

        <!-- Tabla (desktop) + Tarjetas (móvil) -->
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            {{-- Tarjetas: solo móvil --}}
            <div class="space-y-3 p-4 sm:hidden">
                @foreach ($users as $user)
                    <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-100 font-black text-indigo-700">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>

                            <div class="min-w-0 flex-1">
                                <p class="truncate font-bold text-slate-900">
                                    {{ $user->name }}
                                    @if ($user->id === auth()->id())
                                        <span class="ml-1 rounded-full bg-indigo-100 px-2 py-0.5 text-xs font-bold text-indigo-700">Tú</span>
                                    @endif
                                </p>
                                <p class="truncate text-xs text-slate-500">{{ $user->email }}</p>
                            </div>
                        </div>

                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $roleColors[$user->role] ?? 'bg-slate-100 text-slate-600' }}">
                                {{ $roleLabels[$user->role] ?? ucfirst($user->role) }}
                            </span>

                            <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold {{ $statusColors[$user->status] ?? 'bg-slate-100 text-slate-600' }}">
                                <span class="h-1.5 w-1.5 rounded-full bg-current" aria-hidden="true"></span>
                                {{ $statusLabels[$user->status] ?? ucfirst($user->status) }}
                            </span>

                            <span class="text-xs text-slate-500">
                                Desde {{ $user->created_at?->format('d/m/Y') }}
                            </span>
                        </div>

                        <div class="mt-3 flex items-center gap-3 border-t border-slate-100 pt-3">
                            <a href="{{ route('admin.users.show', $user->id) }}"
                               class="flex-1 rounded-xl border border-slate-200 bg-white px-4 py-2 text-center text-xs font-bold text-slate-700 transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-1">
                                Ver
                            </a>

                            <form action="{{ route('admin.users.update-status', $user->id) }}" method="POST" class="flex-1">
                                @csrf
                                @method('PATCH')

                                <select name="status"
                                        onchange="this.form.submit()"
                                        aria-label="Cambiar estado de {{ $user->name }}"
                                        @disabled($user->id === auth()->id())
                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs font-bold text-slate-700 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400">
                                    @foreach ($statusLabels as $value => $label)
                                        <option value="{{ $value }}" @selected($user->status === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- Tabla: sm en adelante --}}
            <div class="hidden overflow-x-auto sm:block">
                <table class="min-w-full divide-y divide-slate-200 text-sm">

                    <thead class="bg-slate-50">
                        <tr class="text-center text-xs font-bold uppercase tracking-wider text-slate-500">
                            <th scope="col" class="px-6 py-4">Usuario</th>
                            <th scope="col" class="px-6 py-4">Rol</th>
                            <th scope="col" class="px-6 py-4">Estado</th>
                            <th scope="col" class="px-6 py-4">Registro</th>
                            <th scope="col" class="px-6 py-4">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 text-center">

                        @foreach ($users as $user)
                            <tr class="transition hover:bg-slate-50">

                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-100 font-black text-indigo-700">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-bold text-slate-900">
                                                {{ $user->name }}
                                                @if ($user->id === auth()->id())
                                                    <span class="ml-1 rounded-full bg-indigo-100 px-2 py-0.5 text-xs font-bold text-indigo-700">Tú</span>
                                                @endif
                                            </p>
                                            <p class="truncate text-xs text-slate-500">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="inline-block rounded-full px-3 py-1 text-xs font-bold {{ $roleColors[$user->role] ?? 'bg-slate-100 text-slate-600' }}">
                                        {{ $roleLabels[$user->role] ?? ucfirst($user->role) }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold {{ $statusColors[$user->status] ?? 'bg-slate-100 text-slate-600' }}">
                                        <span class="h-1.5 w-1.5 rounded-full bg-current" aria-hidden="true"></span>
                                        {{ $statusLabels[$user->status] ?? ucfirst($user->status) }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-slate-600">
                                    {{ $user->created_at?->format('d/m/Y') }}
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-3">

                                        <a href="{{ route('admin.users.show', $user->id) }}"
                                           class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-1">
                                            Ver
                                        </a>

                                        <form action="{{ route('admin.users.update-status', $user->id) }}"
                                              method="POST">
                                            @csrf
                                            @method('PATCH')

                                            <select name="status"
                                                    onchange="this.form.submit()"
                                                    aria-label="Cambiar estado de {{ $user->name }}"
                                                    @disabled($user->id === auth()->id())
                                                    class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-bold text-slate-700 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400">
                                                @foreach ($statusLabels as $value => $label)
                                                    <option value="{{ $value }}" @selected($user->status === $value)>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>

                                        </form>

                                    </div>
                                </td>

                            </tr>
                        @endforeach

                    </tbody>

                </table>
            </div>

            <div class="border-t border-slate-200 px-6 py-4">
                {{ $users->links() }}
            </div>

        </section>

    @endif

@endsection
