@extends('admin.layouts.app')

@section('title', 'Detalle de actividad | Klassio')

@section('page-title')
    Detalle de la actividad
@endsection

@section('page-actions')
    <a href="{{ route('admin.activities.index') }}"
       class="inline-flex self-start items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
        </svg>
        Volver
    </a>
@endsection

@php
    $typeLabels = ['word_search' => 'Sopa de letras', 'crossword' => 'Crucigrama', 'matching' => 'Conecta', 'kahoot' => 'Quiz', 'roulette' => 'Ruleta'];
    $modeLabels = ['individual' => 'Individual', 'team' => 'Equipo'];
    $statusLabels = ['draft' => 'Borrador', 'published' => 'Publicada', 'closed' => 'Cerrada'];
@endphp

@section('content')

    <!-- Encabezado de la actividad -->
    <section class="relative mb-8 overflow-hidden rounded-2xl bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 p-6 text-white shadow-xl sm:p-8">
        <div class="pointer-events-none absolute -right-20 -top-20 h-72 w-72 rounded-full bg-indigo-500/20 blur-3xl"></div>

        <div class="relative z-10 flex flex-col gap-6 md:flex-row md:items-center md:justify-between">

            <div class="min-w-0">
                <div class="mb-3 flex flex-wrap items-center gap-2">
                    <span class="inline-flex rounded-full bg-white/10 px-3 py-1 text-xs font-bold uppercase tracking-wider text-indigo-200 ring-1 ring-white/10">
                        {{ $typeLabels[$activity->type] ?? ucfirst($activity->type) }}
                    </span>
                    <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-bold text-teal-300 ring-1 ring-white/10">
                        {{ $modeLabels[$activity->mode] ?? ucfirst($activity->mode) }}
                    </span>
                </div>

                <h2 class="text-3xl font-black tracking-tight text-white sm:text-4xl">
                    {{ $activity->title }}
                </h2>

                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300">
                    {{ $activity->description }}
                </p>
            </div>

            <div class="flex shrink-0 flex-col items-start gap-3 md:items-end">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-4 py-1.5 text-xs font-bold text-white ring-1 ring-white/20">
                    <span class="h-1.5 w-1.5 rounded-full {{ $activity->status === 'published' ? 'bg-emerald-400' : ($activity->status === 'closed' ? 'bg-red-400' : 'bg-slate-400') }}" aria-hidden="true"></span>
                    {{ $statusLabels[$activity->status] ?? ucfirst($activity->status) }}
                </span>

                <div class="flex items-center gap-2 text-sm font-bold text-white">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white/10 ring-1 ring-white/20">
                        <svg class="h-4 w-4 text-indigo-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                        </svg>
                    </span>
                    <span class="max-w-52 truncate">
                        {{ $activity->schoolClass?->name ?? 'Sin clase asignada' }}
                    </span>
                </div>
            </div>

        </div>
    </section>

    <!-- Detalles rápidos -->
    <section class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4" aria-label="Detalles de la actividad">

        @php
            $statIconClass = 'h-6 w-6';
            $detailCards = [
                [
                    'label' => 'Clase',
                    'value' => $activity->schoolClass?->name ?? '—',
                    'bg' => 'bg-amber-50 text-amber-600',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />',
                ],
                [
                    'label' => 'Profesor',
                    'value' => $activity->teacher?->name ?? '—',
                    'bg' => 'bg-teal-50 text-teal-600',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.628 48.628 0 0 1 12 20.904a48.628 48.628 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />',
                ],
                [
                    'label' => 'Puntaje máximo',
                    'value' => $activity->max_score,
                    'bg' => 'bg-indigo-50 text-indigo-600',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />',
                ],
                [
                    'label' => 'Intentos',
                    'value' => $activity->attempts ?? '—',
                    'bg' => 'bg-violet-50 text-violet-600',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M2.985 14.652 5.166 12.47m12.9-5.618h4.992m0 0V2.86m0 4.992-3.18-3.18a8.25 8.25 0 0 0-13.804 3.698" />',
                ],
            ];
        @endphp

        @foreach ($detailCards as $card)
            <div class="rounded-2xl border border-slate-200 bg-white p-5 text-center shadow-sm">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl {{ $card['bg'] }}">
                    <svg class="{{ $statIconClass }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        {!! $card['icon'] !!}
                    </svg>
                </div>
                <p class="mt-4 truncate text-3xl font-black tabular-nums text-slate-900">
                    {{ $card['value'] }}
                </p>
                <p class="mt-1 text-sm font-bold text-slate-500">{{ $card['label'] }}</p>
            </div>
        @endforeach

    </section>

    <!-- Límite de tiempo y fecha límite -->
    <div class="mb-6 grid gap-4 sm:grid-cols-2">

        <div class="rounded-2xl border border-slate-200 bg-white p-5 text-center shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Límite de tiempo</p>
            <p class="mt-2 text-xl font-black text-slate-900">
                {{ $activity->time_limit ? $activity->time_limit . ' min' : 'Sin límite' }}
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 text-center shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Fecha límite</p>
            <p class="mt-2 text-xl font-black text-slate-900">
                {{ $activity->due_at ? $activity->due_at->format('d/m/Y H:i') : 'Sin fecha' }}
            </p>
        </div>

    </div>

    @if ($activity->teams->isNotEmpty())

        <!-- Equipos -->
        <section class="mb-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">

            <h3 class="text-lg font-black text-slate-900">Equipos ({{ $activity->teams->count() }})</h3>

            <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">

                @foreach ($activity->teams as $team)
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <div class="flex items-center justify-between gap-2">
                            <h4 class="flex min-w-0 items-center gap-2 font-bold text-slate-900">
                                <svg class="h-5 w-5 shrink-0 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                                </svg>
                                <span class="truncate">{{ $team->name }}</span>
                            </h4>
                            <span class="shrink-0 rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-700">
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

        <!-- Sin participaciones -->
        <section class="rounded-2xl border border-slate-200 bg-white p-10 text-center shadow-sm">

            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-400">
                <svg class="h-7 w-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0" />
                </svg>
            </div>

            <h3 class="mt-3 text-lg font-black text-slate-900">Sin participaciones</h3>
            <p class="mt-1 text-sm text-slate-500">Aún no hay resultados registrados para esta actividad.</p>

        </section>

    @else

        <!-- Participaciones -->
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-200 px-6 py-4">
                <h3 class="text-lg font-black text-slate-900">
                    Participaciones ({{ $activity->participations->count() }})
                </h3>
            </div>

            {{-- Tarjetas: solo móvil --}}
            <div class="space-y-3 p-4 sm:hidden">
                @foreach ($activity->participations as $participation)
                    <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-xs font-black text-indigo-700">
                                {{ strtoupper(substr($participation->student?->name ?? $participation->team?->name ?? '?', 0, 1)) }}
                            </div>

                            <div class="min-w-0 flex-1">
                                <p class="truncate font-bold text-slate-900">
                                    {{ $participation->student?->name ?? $participation->team?->name ?? '—' }}
                                </p>
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-bold {{ $participation->student_id !== null ? 'bg-slate-100 text-slate-600' : 'bg-indigo-50 text-indigo-700' }}">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current" aria-hidden="true"></span>
                                    {{ $participation->student_id !== null ? 'Individual' : 'Equipo' }}
                                </span>
                            </div>

                            <div class="shrink-0 text-right">
                                <p class="text-lg font-black tabular-nums text-slate-900">{{ $participation->score ?? '—' }} <span class="text-xs font-medium text-slate-500">pts</span></p>
                                <p class="text-xs text-slate-500">Intento #{{ $participation->attempt }}</p>
                            </div>
                        </div>

                        <div class="mt-3 flex items-center justify-between border-t border-slate-100 pt-3">
                            <span class="text-xs font-bold text-slate-600">Estado</span>

                            <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold bg-slate-100 text-slate-700">
                                <span class="h-1.5 w-1.5 rounded-full bg-current" aria-hidden="true"></span>
                                {{ ucfirst($participation->status) }}
                            </span>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- Tabla: sm en adelante --}}
            <div class="hidden overflow-x-auto sm:block">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr class="text-center text-xs font-bold uppercase tracking-wider text-slate-500">
                            <th scope="col" class="px-6 py-4">Participante</th>
                            <th scope="col" class="px-6 py-4">Intento</th>
                            <th scope="col" class="px-6 py-4">Estado</th>
                            <th scope="col" class="px-6 py-4">Puntaje</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-center">
                        @foreach ($activity->participations as $participation)
                            <tr class="transition hover:bg-slate-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-3">
                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-xs font-black text-indigo-700">
                                            {{ strtoupper(substr($participation->student?->name ?? $participation->team?->name ?? '?', 0, 1)) }}
                                        </div>
                                        <span class="font-bold text-slate-900">
                                            {{ $participation->student?->name ?? $participation->team?->name ?? '—' }}
                                        </span>
                                        <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold {{ $participation->student_id !== null ? 'bg-slate-100 text-slate-600' : 'bg-indigo-50 text-indigo-700' }}">
                                            <span class="h-1.5 w-1.5 rounded-full bg-current" aria-hidden="true"></span>
                                            {{ $participation->student_id !== null ? 'Individual' : 'Equipo' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-bold tabular-nums text-slate-900">#{{ $participation->attempt }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold bg-slate-100 text-slate-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-current" aria-hidden="true"></span>
                                        {{ ucfirst($participation->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-bold tabular-nums text-slate-900">
                                    {{ $participation->score ?? '—' }} <span class="text-xs font-medium text-slate-500">pts</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </section>

    @endif

@endsection
