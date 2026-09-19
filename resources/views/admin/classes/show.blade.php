@extends('admin.layouts.app')

@section('title', 'Detalle de clase | Klassio')

@section('page-title')
    Detalle de la clase
@endsection

@section('page-actions')
    <a href="{{ route('admin.classes.index') }}"
       class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-black text-slate-600 shadow-sm transition hover:bg-slate-50">
        <span>←</span>
        Volver
    </a>
@endsection

@php
    $activeStudents = $class->enrollments->where('status', 'active');
    $statusLabels = ['active' => 'Activa', 'archived' => 'Archivada'];
    $statusColors = ['active' => 'bg-emerald-100 text-emerald-800', 'archived' => 'bg-slate-200 text-slate-600'];
@endphp

@section('content')

    <!-- Encabezado de la clase -->
    <section class="page-hero mb-6">
        <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">

            <div>
                <div class="mb-3 flex flex-wrap items-center gap-2">
                    <span class="inline-flex rounded-full bg-white/20 px-3 py-1 text-xs font-black uppercase tracking-wider text-white">
                        Clase
                    </span>
                    <span class="rounded-full bg-white/20 px-3 py-1 font-mono text-xs font-black text-white">
                        {{ $class->code }}
                    </span>
                </div>

                <h2 class="text-3xl font-black tracking-tight text-white">
                    {{ $class->name }}
                </h2>

                <p class="mt-3 max-w-2xl text-sm leading-6 text-white/80">
                    {{ $class->description }}
                </p>
            </div>

            <div class="hidden flex-col items-end gap-2 md:flex">
                <span class="rounded-full px-4 py-1.5 text-xs font-black {{ $statusColors[$class->status] ?? 'bg-slate-200 text-slate-600' }}">
                    {{ $statusLabels[$class->status] ?? ucfirst($class->status) }}
                </span>
                <p class="text-sm font-black text-white">
                    Profesor: {{ $class->teacher?->name ?? '—' }}
                </p>
            </div>

        </div>
    </section>

    <!-- Estadísticas -->
    <section class="mb-6 grid gap-4 sm:grid-cols-3">

        @php
            $cards = [
                ['label' => 'Estudiantes activos', 'value' => $activeStudents->count(), 'icon' => '🎓', 'bg' => 'bg-cyan-100'],
                ['label' => 'Actividades', 'value' => $class->activities->count(), 'icon' => '🎯', 'bg' => 'bg-purple-100'],
                ['label' => 'Total de inscripciones', 'value' => $class->enrollments->count(), 'icon' => '👥', 'bg' => 'bg-amber-100'],
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

    @if ($class->enrollments->isEmpty())
        <section class="mb-6 rounded-3xl border border-slate-200 bg-white p-10 text-center shadow-lg shadow-slate-200/60">
            <div class="text-5xl">👥</div>
            <h3 class="mt-3 text-lg font-black text-slate-900">Sin estudiantes</h3>
            <p class="mt-1 text-sm text-slate-500">Aún no hay estudiantes inscritos en esta clase.</p>
        </section>
    @else

        <!-- Estudiantes -->
        <section class="mb-6 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-lg shadow-slate-200/60">

            <div class="border-b border-slate-200 px-6 py-4">
                <h3 class="text-lg font-black text-slate-900">Estudiantes inscritos</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-xs font-black uppercase tracking-widest text-slate-500">
                            <th class="px-6 py-4">Estudiante</th>
                            <th class="px-6 py-4">Correo</th>
                            <th class="px-6 py-4">Inscripción</th>
                            <th class="px-6 py-4">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($class->enrollments as $enrollment)
                            <tr class="transition hover:bg-slate-50">
                                <td class="px-6 py-4 font-black text-slate-900">
                                    {{ $enrollment->student?->name ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-slate-600">
                                    {{ $enrollment->student?->email ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-slate-600">
                                    {{ $enrollment->created_at?->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-black {{ $enrollment->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600' }}">
                                        {{ $enrollment->status === 'active' ? 'Activo' : 'Eliminado' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </section>

    @endif

    @if ($class->activities->isEmpty())
        <section class="rounded-3xl border border-slate-200 bg-white p-10 text-center shadow-lg shadow-slate-200/60">
            <div class="text-5xl">🎯</div>
            <h3 class="mt-3 text-lg font-black text-slate-900">Sin actividades</h3>
            <p class="mt-1 text-sm text-slate-500">El profesor aún no ha creado actividades en esta clase.</p>
        </section>
    @else

        <!-- Actividades -->
        <section class="rounded-3xl border border-slate-200 bg-white shadow-lg shadow-slate-200/60">

            <div class="grid gap-4 p-6 sm:grid-cols-2 lg:grid-cols-3">

                @foreach ($class->activities as $activity)
                    <a href="{{ route('admin.activities.show', $activity->id) }}"
                       class="rounded-2xl border border-slate-200 bg-white p-4 transition hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-lg">

                        <div class="flex items-start justify-between">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-100 text-2xl">
                                🎯
                            </div>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-black text-slate-600">
                                {{ ucfirst(str_replace('_', ' ', $activity->type)) }}
                            </span>
                        </div>

                        <h4 class="mt-3 font-black text-slate-900">{{ $activity->title }}</h4>
                        <p class="mt-1 line-clamp-2 text-xs leading-5 text-slate-500">{{ $activity->description }}</p>

                    </a>
                @endforeach

            </div>

        </section>

    @endif

@endsection