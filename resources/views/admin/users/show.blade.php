@extends('admin.layouts.app')

@section('title', 'Detalle de usuario | Klassio')

@section('page-title')
    Detalle del usuario
@endsection

@section('page-actions')
    <a href="{{ route('admin.users.index') }}"
       class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-black text-slate-600 shadow-sm transition hover:bg-slate-50">
        <span>←</span>
        Volver
    </a>
@endsection

@php
    $roleLabels = ['admin' => 'Administrador', 'teacher' => 'Maestro', 'student' => 'Estudiante'];
    $roleColors = ['admin' => 'bg-slate-900 text-white', 'teacher' => 'bg-emerald-100 text-emerald-800', 'student' => 'bg-cyan-100 text-cyan-800'];
    $statusLabels = ['active' => 'Activo', 'inactive' => 'Inactivo', 'suspended' => 'Suspendido'];
    $statusColors = ['active' => 'bg-emerald-100 text-emerald-800', 'inactive' => 'bg-slate-200 text-slate-600', 'suspended' => 'bg-red-100 text-red-800'];
@endphp

@section('content')

    <!-- Tarjeta de perfil -->
    <section class="mb-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-lg shadow-slate-200/60 sm:p-8">

        <div class="flex flex-col gap-6 sm:flex-row sm:items-center">

            <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-3xl bg-slate-900 text-3xl font-black text-amber-400 shadow-lg">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>

            <div class="flex-1">
                <div class="flex flex-wrap items-center gap-2">
                    <h3 class="text-2xl font-black text-slate-900">
                        {{ $user->name }}
                    </h3>
                    @if ($user->id === auth()->id())
                        <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-black text-amber-700">Tú</span>
                    @endif
                </div>

                <p class="mt-1 text-sm font-medium text-slate-500">
                    {{ $user->email }}
                </p>

                <div class="mt-3 flex flex-wrap gap-2">
                    <span class="rounded-full px-3 py-1 text-xs font-black {{ $roleColors[$user->role] ?? 'bg-slate-200 text-slate-600' }}">
                        {{ $roleLabels[$user->role] ?? ucfirst($user->role) }}
                    </span>
                    <span class="rounded-full px-3 py-1 text-xs font-black {{ $statusColors[$user->status] ?? 'bg-slate-200 text-slate-600' }}">
                        {{ $statusLabels[$user->status] ?? ucfirst($user->status) }}
                    </span>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-black text-slate-600">
                        Registrado el {{ $user->created_at?->format('d/m/Y') }}
                    </span>
                </div>
            </div>

        </div>

    </section>

    <!-- Estadísticas -->
    <section class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">

        @php
            $cards = [
                ['label' => 'Clases creadas', 'value' => $statistics['classes'], 'icon' => '📚', 'bg' => 'bg-amber-100'],
                ['label' => 'Clases inscritas', 'value' => $statistics['enrolledClasses'], 'icon' => '🎓', 'bg' => 'bg-cyan-100'],
                ['label' => 'Participaciones', 'value' => $statistics['participations'], 'icon' => '🏆', 'bg' => 'bg-rose-100'],
                ['label' => 'Equipos', 'value' => $statistics['teamMemberships'], 'icon' => '👥', 'bg' => 'bg-purple-100'],
            ];
        @endphp

        @foreach ($cards as $card)
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-lg shadow-slate-200/60">
                <div class="mb-4 flex items-start justify-between">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl text-2xl {{ $card['bg'] }}">
                        {{ $card['icon'] }}
                    </div>
                    <span class="text-3xl font-black tabular-nums text-slate-900">
                        {{ number_format($card['value']) }}
                    </span>
                </div>
                <p class="text-sm font-black text-slate-500">{{ $card['label'] }}</p>
            </div>
        @endforeach

    </section>

    <!-- Gestión del estado -->
    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-lg shadow-slate-200/60 sm:p-8">

        <p class="text-xs font-black uppercase tracking-widest text-amber-600">
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
            <div class="mt-4 flex items-start gap-3 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-amber-800">
                <span class="text-xl">🔒</span>
                <p class="text-sm font-bold">
                    Esta es tu propia cuenta, por lo que no puedes modificar su estado.
                </p>
            </div>
        @else
            <form action="{{ route('admin.users.update-status', $user->id) }}" method="POST"
                  class="mt-4 flex flex-wrap items-center gap-3">
                @csrf
                @method('PATCH')

                <select name="status"
                        class="rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-bold outline-none transition focus:border-slate-400 focus:ring-2 focus:ring-slate-200">
                    @foreach ($statusLabels as $value => $label)
                        <option value="{{ $value }}" @selected($user->status === $value)>{{ $label }}</option>
                    @endforeach
                </select>

                <button type="submit"
                        class="rounded-2xl bg-slate-900 px-6 py-2.5 text-sm font-black text-white shadow transition hover:bg-slate-700">
                    Actualizar estado
                </button>
            </form>
        @endif

    </section>

@endsection