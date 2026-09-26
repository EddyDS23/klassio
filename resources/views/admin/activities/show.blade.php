@extends('admin.layouts.app')

@section('title', 'Detalle de actividad | Klassio')

@section('page-title')
    Detalle de la actividad
@endsection

@section('page-actions')
    <a href="{{ route('admin.activities.index') }}"
       class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-black text-slate-600 shadow-sm transition hover:bg-slate-50">
        <span>←</span>
        Volver
    </a>
@endsection

@php
    $typeLabels = ['word_search' => 'Sopa de letras', 'crossword' => 'Crucigrama', 'matching' => 'Conecta', 'kahoot' => 'Quiz', 'roulette' => 'Ruleta'];
    $modeLabels = ['individual' => 'Individual', 'team' => 'Equipo'];
    $statusLabels = ['draft' => 'Borrador', 'published' => 'Publicada', 'closed' => 'Cerrada'];
    $statusColors = ['draft' => 'bg-slate-200 text-slate-600', 'published' => 'bg-emerald-100 text-emerald-800', 'closed' => 'bg-red-100 text-red-800'];
@endphp

@section('content')

    <!-- Encabezado de la actividad -->
    <section class="page-hero mb-6">
        <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">

            <div>
                <div class="mb-3 flex flex-wrap items-center gap-2">
                    <span class="inline-flex rounded-full bg-white/20 px-3 py-1 text-xs font-black uppercase tracking-wider text-white">
                        {{ $typeLabels[$activity->type] ?? ucfirst($activity->type) }}
                    </span>
                    <span class="rounded-full bg-white/20 px-3 py-1 text-xs font-black text-white">
                        {{ $modeLabels[$activity->mode] ?? ucfirst($activity->mode) }}
                    </span>
                </div>

                <h2 class="text-3xl font-black tracking-tight text-white">
                    {{ $activity->title }}
                </h2>

                <p class="mt-3 max-w-2xl text-sm leading-6 text-white/80">
                    {{ $activity->description }}
                </p>
            </div>

            <div class="hidden flex-col items-end gap-2 md:flex">
                <span class="rounded-full px-4 py-1.5 text-xs font-black {{ $statusColors[$activity->status] ?? 'bg-slate-200 text-slate-600' }}">
                    {{ $statusLabels[$activity->status] ?? ucfirst($activity->status) }}
                </span>
                <p class="text-sm font-black text-white">
                    {{ $activity->schoolClass?->name }}
                </p>
            </div>

        </div>
    </section>

    <!-- Detalles rápidos -->
    <section class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">

        @php
            $detailCards = [
                ['label' => 'Clase', 'value' => $activity->schoolClass?->name ?? '—', 'icon' => '📚', 'bg' => 'bg-amber-100'],
                ['label' => 'Profesor', 'value' => $activity->teacher?->name ?? '—', 'icon' => '👨‍🏫', 'bg' => 'bg-emerald-100'],
                ['label' => 'Puntaje máximo', 'value' => $activity->max_score, 'icon' => '⭐', 'bg' => 'bg-indigo-100'],
                ['label' => 'Intentos', 'value' => $activity->attempts ?? '—', 'icon' => '🔁', 'bg' => 'bg-cyan-100'],
            ];
        @endphp

        @foreach ($detailCards as $card)
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-lg shadow-slate-200/60">
                <div class="mb-4 flex items-start justify-between">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl text-2xl {{ $card['bg'] }}">
                        {{ $card['icon'] }}
                    </div>
                    <span class="max-w-32 truncate text-right text-3xl font-black tabular-nums text-slate-900">
                        {{ $card['value'] }}
                    </span>
                </div>
                <p class="text-sm font-black text-slate-500">{{ $card['label'] }}</p>
            </div>
        @endforeach

    </section>

    <div class="mb-6 grid gap-4 sm:grid-cols-2">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-lg shadow-slate-200/60">
            <p class="text-xs font-black uppercase tracking-widest text-slate-500">Límite de tiempo</p>
            <p class="mt-2 text-xl font-black text-slate-900">
                {{ $activity->time_limit ? $activity->time_limit . ' min' : 'Sin límite' }}
            </p>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-lg shadow-slate-200/60">
            <p class="text-xs font-black uppercase tracking-widest text-slate-500">Fecha límite</p>
            <p class="mt-2 text-xl font-black text-slate-900">
                {{ $activity->due_at ? $activity->due_at->format('d/m/Y H:i') : 'Sin fecha' }}
            </p>
        </div>
    </div>

    @if ($activity->teams->isNotEmpty())

        <!-- Equipos -->
        <section class="mb-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-lg shadow-slate-200/60 sm:p-8">

            <h3 class="text-lg font-black text-slate-900">Equipos ({{ $activity->teams->count() }})</h3>

            <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">

                @foreach ($activity->teams as $team)
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <div class="flex items-center justify-between">
                            <h4 class="font-black text-slate-900">👥 {{ $team->name }}</h4>
                            <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-black text-blue-800">
                                {{ $team->members->count() }} miembros
                            </span>
                        </div>
                        <p class="mt-3 text-sm text-slate-600">
                            {{ $team->members->pluck('student.name')->filter()->implode(', ') ?: 'Sin miembros' }}
                        </p>
                    </div>
                @endforeach

            </div>

        </section>

    @endif

    @if ($activity->participations->isEmpty())
        <section class="rounded-3xl border border-slate-200 bg-white p-10 text-center shadow-lg shadow-slate-200/60">
            <div class="text-5xl">🏆</div>
            <h3 class="mt-3 text-lg font-black text-slate-900">Sin participaciones</h3>
            <p class="mt-1 text-sm text-slate-500">Aún no hay resultados registrados para esta actividad.</p>
        </section>
    @else

        <!-- Participaciones -->
        <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-lg shadow-slate-200/60">

            <div class="border-b border-slate-200 px-6 py-4">
                <h3 class="text-lg font-black text-slate-900">
                    Participaciones ({{ $activity->participations->count() }})
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-xs font-black uppercase tracking-widest text-slate-500">
                            <th class="px-6 py-4">Participante</th>
                            <th class="px-6 py-4">Intento</th>
                            <th class="px-6 py-4">Estado</th>
                            <th class="px-6 py-4">Puntaje</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($activity->participations as $participation)
                            <tr class="transition hover:bg-slate-50">
                                <td class="px-6 py-4">
                                    @if ($participation->student_id !== null)
                                        <span class="font-black text-slate-900">{{ $participation->student?->name ?? '—' }}</span>
                                        <span class="rounded-full bg-cyan-100 px-2 py-0.5 text-xs font-black text-cyan-800">Individual</span>
                                    @else
                                        <span class="font-black text-slate-900">{{ $participation->team?->name ?? '—' }}</span>
                                        <span class="rounded-full bg-blue-100 px-2 py-0.5 text-xs font-black text-blue-800">Equipo</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-black tabular-nums text-slate-900">#{{ $participation->attempt }}</td>
                                <td class="px-6 py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-black bg-slate-100 text-slate-700">
                                        {{ ucfirst($participation->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-black tabular-nums text-slate-900">
                                    {{ $participation->score ?? '—' }} <span class="text-xs text-slate-500">pts</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </section>

    @endif

@endsection