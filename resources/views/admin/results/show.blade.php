@extends('admin.layouts.app')

@section('title', 'Detalle del resultado | Klassio')

@section('page-title')
    Detalle del resultado
@endsection

@section('page-actions')
    <a href="{{ route('admin.results.index') }}"
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
    $statusLabels = ['started' => 'En curso', 'completed' => 'Completada', 'abandoned' => 'Abandonada', 'expired' => 'Expirada'];

    $statusDotColors = [
        'started' => 'bg-amber-400',
        'completed' => 'bg-emerald-400',
        'abandoned' => 'bg-slate-400',
        'expired' => 'bg-red-400',
    ];

    if (!function_exists('admin_show_duration')) {
        function admin_show_duration($seconds): string
        {
            if ($seconds === null) {
                return '—';
            }
            $minutes = intdiv((int) $seconds, 60);
            $secs = (int) $seconds % 60;
            return $minutes > 0 ? "{$minutes}m {$secs}s" : "{$secs}s";
        }
    }
@endphp

@section('content')

    <!-- Encabezado -->
    <section class="relative mb-8 overflow-hidden rounded-2xl bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 p-6 text-white shadow-xl sm:p-8">
        <div class="pointer-events-none absolute -right-20 -top-20 h-72 w-72 rounded-full bg-indigo-500/20 blur-3xl"></div>

        <div class="relative z-10 flex flex-col gap-6 md:flex-row md:items-center md:justify-between">

            <div class="min-w-0">
                <div class="mb-3 flex flex-wrap items-center gap-2">
                    <span class="inline-flex rounded-full bg-white/10 px-3 py-1 text-xs font-bold uppercase tracking-wider text-indigo-200 ring-1 ring-white/10">
                        Resultado
                    </span>
                    <span class="rounded-full bg-white/10 px-3 py-1 font-mono text-xs font-bold text-teal-300 ring-1 ring-white/10">
                        Intento #{{ $result->attempt }}
                    </span>
                </div>

                <h2 class="text-3xl font-black tracking-tight text-white sm:text-4xl">
                    {{ $result->activity?->title ?? 'Actividad' }}
                </h2>

                <p class="mt-3 text-sm font-medium text-slate-300">
                    {{ $result->activity?->schoolClass?->name ?? '—' }} ·
                    {{ $result->activity?->teacher?->name ?? '—' }}
                </p>
            </div>

            <div class="flex shrink-0 flex-col items-start gap-3 md:items-end">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-4 py-1.5 text-xs font-bold text-white ring-1 ring-white/20">
                    <span class="h-1.5 w-1.5 rounded-full {{ $statusDotColors[$result->status] ?? 'bg-slate-400' }}" aria-hidden="true"></span>
                    {{ $statusLabels[$result->status] ?? ucfirst($result->status) }}
                </span>
                <span class="inline-flex rounded-full bg-white/10 px-4 py-1.5 text-xs font-bold text-indigo-200 ring-1 ring-white/20">
                    {{ $typeLabels[$result->activity?->type ?? ''] ?? 'Tipo no disponible' }}
                </span>
            </div>

        </div>
    </section>

    <!-- Participante -->
    <section class="mb-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">

        <p class="text-xs font-bold uppercase tracking-widest text-indigo-600">
            Participante
        </p>

        @if ($result->student_id !== null)

            <div class="mt-4 flex items-center gap-4">
                <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-indigo-600 text-2xl font-black text-white shadow-lg shadow-indigo-200">
                    {{ strtoupper(substr($result->student?->name ?? '?', 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <h3 class="truncate text-xl font-black text-slate-900">{{ $result->student?->name ?? '—' }}</h3>
                    <p class="truncate text-sm font-medium text-slate-500">{{ $result->student?->email ?? '' }}</p>
                    <span class="mt-2 inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
                        <span class="h-1.5 w-1.5 rounded-full bg-current" aria-hidden="true"></span>
                        Participación individual
                    </span>
                </div>
            </div>

        @else

            <div class="mt-4 flex items-center gap-4">
                <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-indigo-600 text-white shadow-lg shadow-indigo-200">
                    <svg class="h-7 w-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <h3 class="truncate text-xl font-black text-slate-900">{{ $result->team?->name ?? '—' }}</h3>
                    <span class="mt-2 inline-flex items-center gap-1.5 rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-700">
                        <span class="h-1.5 w-1.5 rounded-full bg-current" aria-hidden="true"></span>
                        Participación en equipo
                    </span>
                </div>
            </div>

            @if ($result->team?->members?->isNotEmpty())
                <div class="mt-5 flex flex-wrap gap-2">
                    @foreach ($result->team->members as $member)
                        <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1.5 text-xs font-bold text-slate-700">
                            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-indigo-100 text-[0.6rem] font-black text-indigo-700" aria-hidden="true">
                                {{ strtoupper(substr($member->student?->name ?? 'M', 0, 1)) }}
                            </span>
                            {{ $member->student?->name ?? 'Miembro' }}
                        </span>
                    @endforeach
                </div>
            @endif

        @endif

    </section>

    <!-- Detalles del resultado -->
    <section class="grid gap-6 lg:grid-cols-2">

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">

            <p class="text-xs font-bold uppercase tracking-widest text-indigo-600">
                Rendimiento
            </p>

            <div class="mt-4 grid gap-4 sm:grid-cols-2">

                <div class="rounded-xl bg-slate-50 p-4 text-center">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Puntaje</p>
                    <p class="mt-2 text-4xl font-black tabular-nums text-slate-900">
                        {{ $result->score ?? '—' }}
                    </p>
                    <p class="text-xs font-bold text-slate-500">de {{ $result->activity?->max_score ?? '—' }} puntos</p>
                </div>

                <div class="rounded-xl bg-slate-50 p-4 text-center">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Duración</p>
                    <p class="mt-2 text-3xl font-black tabular-nums text-slate-900">
                        {{ admin_show_duration($result->elapsed_seconds) }}
                    </p>
                </div>

            </div>

        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">

            <p class="text-xs font-bold uppercase tracking-widest text-indigo-600">
                Cronología
            </p>

            <dl class="mt-4 space-y-3 text-sm">

                <div class="flex items-center justify-between gap-4">
                    <dt class="font-bold text-slate-500">Iniciada</dt>
                    <dd class="text-right font-bold text-slate-900">
                        {{ $result->started_at?->format('d/m/Y H:i') ?? '—' }}
                    </dd>
                </div>

                <div class="flex items-center justify-between gap-4">
                    <dt class="font-bold text-slate-500">Finalizada</dt>
                    <dd class="text-right font-bold text-slate-900">
                        {{ $result->completed_at?->format('d/m/Y H:i') ?? '—' }}
                    </dd>
                </div>

                <div class="flex items-center justify-between gap-4">
                    <dt class="font-bold text-slate-500">Modo</dt>
                    <dd class="text-right font-bold text-slate-900">
                        {{ $modeLabels[$result->activity?->mode ?? ''] ?? '—' }}
                    </dd>
                </div>

            </dl>

        </div>

    </section>

@endsection
