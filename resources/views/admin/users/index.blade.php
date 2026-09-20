@extends('admin.layouts.app')

@section('title', 'Usuarios | Klassio')

@section('page-title')
    Gestión de usuarios
@endsection

@section('page-actions')
    <a href="{{ route('admin.users.create') }}"
       class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-5 py-2.5 text-sm font-black text-white shadow-lg shadow-slate-900/20 transition hover:bg-slate-700">
        <span>➕</span>
        Nuevo usuario
    </a>
@endsection

@php
    $roleLabels = ['admin' => 'Administrador', 'teacher' => 'Maestro', 'student' => 'Estudiante'];
    $roleColors = ['admin' => 'bg-slate-900 text-white', 'teacher' => 'bg-emerald-100 text-emerald-800', 'student' => 'bg-cyan-100 text-cyan-800'];
    $statusLabels = ['active' => 'Activo', 'inactive' => 'Inactivo', 'suspended' => 'Suspendido'];
    $statusColors = ['active' => 'bg-emerald-100 text-emerald-800', 'inactive' => 'bg-slate-200 text-slate-600', 'suspended' => 'bg-red-100 text-red-800'];
@endphp

@section('content')

    <!-- Filtros -->
    <section class="mb-6 rounded-3xl border border-slate-200 bg-white p-5 shadow-lg shadow-slate-200/60">

        <form method="GET" action="{{ route('admin.users.index') }}"
              class="grid gap-4 md:grid-cols-4">

            <div class="md:col-span-2">
                <label for="search" class="mb-1 block text-xs font-black uppercase tracking-widest text-slate-500">
                    Buscar
                </label>
                <input type="text" id="search" name="search" value="{{ request('search') }}"
                       placeholder="Nombre o correo electrónico..."
                       class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-medium outline-none transition focus:border-slate-400 focus:ring-2 focus:ring-slate-200">
            </div>

            <div>
                <label for="role" class="mb-1 block text-xs font-black uppercase tracking-widest text-slate-500">
                    Rol
                </label>
                <select id="role" name="role"
                        class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-medium outline-none transition focus:border-slate-400 focus:ring-2 focus:ring-slate-200">
                    <option value="">Todos los roles</option>
                    @foreach ($roleLabels as $value => $label)
                        <option value="{{ $value }}" @selected(request('role') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status" class="mb-1 block text-xs font-black uppercase tracking-widest text-slate-500">
                    Estado
                </label>
                <select id="status" name="status"
                        class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-medium outline-none transition focus:border-slate-400 focus:ring-2 focus:ring-slate-200">
                    <option value="">Todos los estados</option>
                    @foreach ($statusLabels as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-3 md:col-span-4">
                <button type="submit"
                        class="rounded-2xl bg-slate-900 px-6 py-2.5 text-sm font-black text-white shadow transition hover:bg-slate-700">
                    Filtrar
                </button>

                @if (request()->hasAny(['search', 'role', 'status']))
                    <a href="{{ route('admin.users.index') }}"
                       class="rounded-2xl border border-slate-200 bg-white px-6 py-2.5 text-sm font-black text-slate-600 transition hover:bg-slate-50">
                        Limpiar
                    </a>
                @endif
            </div>

        </form>

    </section>

    <!-- Mensaje cuando no hay resultados -->
    @if ($users->isEmpty())
        <section class="rounded-3xl border border-slate-200 bg-white p-12 text-center shadow-lg shadow-slate-200/60">
            <div class="text-6xl">🔍</div>
            <h3 class="mt-4 text-xl font-black text-slate-900">Sin resultados</h3>
            <p class="mt-2 text-sm text-slate-500">No se encontraron usuarios con los criterios seleccionados.</p>
        </section>
    @else

        <!-- Tabla -->
        <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-lg shadow-slate-200/60">

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">

                    <thead class="bg-slate-50">
                        <tr class="text-left text-xs font-black uppercase tracking-widest text-slate-500">
                            <th class="px-6 py-4">Usuario</th>
                            <th class="px-6 py-4">Rol</th>
                            <th class="px-6 py-4">Estado</th>
                            <th class="px-6 py-4">Registro</th>
                            <th class="px-6 py-4 text-right">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">

                        @foreach ($users as $user)
                            <tr class="transition hover:bg-slate-50">

                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-900 font-black text-amber-400">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-black text-slate-900">
                                                {{ $user->name }}
                                                @if ($user->id === auth()->id())
                                                    <span class="ml-1 rounded-full bg-amber-100 px-2 py-0.5 text-xs font-black text-amber-700">Tú</span>
                                                @endif
                                            </p>
                                            <p class="truncate text-xs text-slate-500">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-black {{ $roleColors[$user->role] ?? 'bg-slate-200 text-slate-600' }}">
                                        {{ $roleLabels[$user->role] ?? ucfirst($user->role) }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-black {{ $statusColors[$user->status] ?? 'bg-slate-200 text-slate-600' }}">
                                        {{ $statusLabels[$user->status] ?? ucfirst($user->status) }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-slate-600">
                                    {{ $user->created_at?->format('d/m/Y') }}
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-3">

                                        <a href="{{ route('admin.users.show', $user->id) }}"
                                           class="rounded-xl bg-slate-100 px-4 py-2 text-xs font-black text-slate-700 transition hover:bg-slate-900 hover:text-white">
                                            Ver
                                        </a>

                                        <form action="{{ route('admin.users.update-status', $user->id) }}"
                                              method="POST">
                                            @csrf
                                            @method('PATCH')

                                            <select name="status"
                                                    onchange="this.form.submit()"
                                                    @disabled($user->id === auth()->id())
                                                    class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-bold outline-none transition focus:border-slate-400 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400">
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