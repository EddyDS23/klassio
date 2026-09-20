@extends('admin.layouts.app')

@section('title', 'Detalle del resultado | Klassio')

@section('page-title')
    Detalle del resultado
@endsection

@section('page-actions')
    <a href="{{ route('admin.results.index') }}"
       class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-black text-slate-600 shadow-sm transition hover:bg-slate-50">
        <span>←</span>
        Volver
    </a>
@endsection

@php
    $typeLabels = ['word_search' => 'Sopa de letras', 'crossword' => 'Crucigrama', 'matching' => 'Conecta', 'kahoot' => 'Quiz'];
    $modeLabels = ['individual' => 'Individual', 'team' => 'Equipo'];
    $statusLabels = ['started' => 'En curso', 'completed' => 'Completada', 'abandoned' => 'Abandonada', 'expired' => 'Expirada'];
    $statusColors = ['started' => 'bg-amber-100 text-amber-800', 'completed' => 'bg-emerald-100 text-emerald-800', 'abandoned' => 'bg-slate-200 text-slate-600', 'expired' => 'bg-red-100 text-red-800'];

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
    <section class="page-hero mb-6">
        <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">

            <div>
                <div class="mb-3 flex flex-wrap items-center gap-2">
                    <span class="inline-flex rounded-full bg-white/20 px-3 py-1 text-xs font-black uppercase tracking-wider text-white">
                        Resultado
                    </span>
                    <span class="rounded-full bg-white/20 px-3 py-1 text-xs font-black text-white">
                        Intento #{{ $result->attempt }}
                    </span>
                </div>

                <h2 class="text-3xl font-black tracking-tight text-white">
                    {{ $result->activity?->title ?? 'Actividad' }}
                </h2>

                <p class="mt-3 text-sm font-black text-white/80">
                    {{ $result->activity?->schoolClass?->name }} ·
                    {{ $result->activity?->teacher?->name }}
                </p>
            </div>

            <div class="hidden flex-col items-end gap-2 md:flex">
                <span class="rounded-full px-4 py-1.5 text-xs font-black {{ $statusColors[$result->status] ?? 'bg-slate-200 text-slate-600' }}">
                    {{ $statusLabels[$result->status] ?? ucfirst($result->status) }}
                </span>
                <span class="rounded-full bg-white/20 px-4 py-1.5 text-xs font-black text-white">
                    {{ $typeLabels[$result->activity?->type ?? ''] ?? '' }}
                </span>
            </div>

        </div>
    </section>

    <!-- Participante -->
    <section class="mb-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-lg shadow-slate-200/60 sm:p-8">

        <p class="text-xs font-black uppercase tracking-widest text-amber-600">
            Participante
        </p>

        @if ($result->student_id !== null)

            <div class="mt-4 flex items-center gap-4">
                <div class="flex h-16 w-16 items-center justify-center rounded-3xl bg-cyan-100 text-2xl font-black text-cyan-800">
                    {{ strtoupper(substr($result->student?->name ?? '?', 0, 1)) }}
                </div>
                <div>
                    <h3 class="text-xl font-black text-slate-900">{{ $result->student?->name ?? '—' }}</h3>
                    <p class="text-sm font-medium text-slate-500">{{ $result->student?->email ?? '' }}</p>
                    <span class="mt-2 inline-flex rounded-full bg-cyan-100 px-3 py-1 text-xs font-black text-cyan-800">
                        Participación individual
                    </span>
                </div>
            </div>

        @else

            <div class="mt-4 flex items-center gap-4">
                <div class="flex h-16 w-16 items-center justify-center rounded-3xl bg-blue-100 text-2xl font-black text-blue-800">
                    👥
                </div>
                <div>
                    <h3 class="text-xl font-black text-slate-900">{{ $result->team?->name ?? '—' }}</h3>
                    <span class="mt-2 inline-flex rounded-full bg-blue-100 px-3 py-1 text-xs font-black text-blue-800">
                        Participación en equipo
                    </span>
                </div>
            </div>

            @if ($result->team?->members?->isNotEmpty())
                <div class="mt-5 flex flex-wrap gap-2">
                    @foreach ($result->team->members as $member)
                        <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1.5 text-xs font-black text-slate-700">
                            {{ $member->student?->name ?? 'Miembro' }}
                        </span>
                    @endforeach
                </div>
            @endif

        @endif

    </section>

    <!-- Detalles del resultado -->
    <section class="grid gap-6 lg:grid-cols-2">

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-lg shadow-slate-200/60 sm:p-8">

            <p class="text-xs font-black uppercase tracking-widest text-amber-600">
                Rendimiento
            </p>

            <div class="mt-4 grid gap-4 sm:grid-cols-2">

                <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-xs font-black uppercase tracking-widest text-slate-500">Puntaje</p>
                    <p class="mt-2 text-4xl font-black tabular-nums text-slate-900">
                        {{ $result->score ?? '—' }}
                    </p>
                    <p class="text-xs font-bold text-slate-500">de {{ $result->activity?->max_score ?? '—' }} puntos</p>
                </div>

                <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-xs font-black uppercase tracking-widest text-slate-500">Duración</p>
                    <p class="mt-2 text-3xl font-black tabular-nums text-slate-900">
                        {{ admin_show_duration($result->elapsed_seconds) }}
                    </p>
                </div>

            </div>

        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-lg shadow-slate-200/60 sm:p-8">

            <p class="text-xs font-black uppercase tracking-widest text-amber-600">
                Cronología
            </p>

            <dl class="mt-4 space-y-3 text-sm">

                <div class="flex items-center justify-between gap-4">
                    <dt class="font-black text-slate-500">Iniciada</dt>
                    <dd class="text-right font-bold text-slate-900">
                        {{ $result->started_at?->format('d/m/Y H:i') ?? '—' }}
                    </dd>
                </div>

                <div class="flex items-center justify-between gap-4">
                    <dt class="font-black text-slate-500">Finalizada</dt>
                    <dd class="text-right font-bold text-slate-900">
                        {{ $result->completed_at?->format('d/m/Y H:i') ?? '—' }}
                    </dd>
                </div>

                <div class="flex items-center justify-between gap-4">
                    <dt class="font-black text-slate-500">Modo</dt>
                    <dd class="text-right font-bold text-slate-900">
                        {{ $modeLabels[$result->activity?->mode ?? ''] ?? '' }}
                    </dd>
                </div>

            </dl>

        </div>

    </section>

@endsection