@extends('student.layouts.app')

@section('title', 'Resultado — '.$activity->title)

@section('page-title', 'Mi resultado')

@section('page-actions')
    <div class="flex flex-wrap items-center justify-end gap-2 sm:gap-3">

        <a href="{{ route('student.class.show', $activity->class_id) }}"
           class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-600 focus-visible:ring-offset-2">
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            Mi clase
        </a>

        <a href="{{ route('student.activities.show', $activity->id) }}"
           class="inline-flex items-center gap-2 rounded-xl bg-orange-700 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-orange-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-700 focus-visible:ring-offset-2">
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.396 1.663-.98l11.282 6.35c.746.418.746 1.542 0 1.96l-11.282 6.35c-.746.417-1.663-.124-1.663-.98V5.653Z" />
            </svg>
            ¡Jugar otra vez!
        </a>

    </div>
@endsection

@section('content')

    @php
        $correctCount = collect($answers)->where('is_correct', true)->count();
        $total = count($answers);

        $typeLabels = [
            'crossword' => 'Crucigrama',
            'kahoot' => 'Kahoot',
            'word_search' => 'Sopa de letras',
            'matching' => 'Unir conceptos',
            'roulette' => 'Ruleta',
        ];

        $typeIcons = [
            'kahoot' => 'm3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z',
            'crossword' => 'M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.75m-.75 3h.75m0 0H21',
            'word_search' => 'm21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z',
            'matching' => 'M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244',
            'roulette' => 'M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99',
        ];

        $typeIcon = $typeIcons[$activity->type] ?? $typeIcons['roulette'];

        [$statusClass, $statusText] = match ($participation->status) {
            'completed' => ['bg-emerald-100 text-emerald-700', 'Completado'],
            'abandoned' => ['bg-amber-100 text-amber-800', 'Abandonado'],
            'expired' => ['bg-slate-100 text-slate-600', 'Se acabó el tiempo'],
            default => ['bg-slate-100 text-slate-600', ucfirst($participation->status)],
        };
    @endphp

    <div class="mx-auto max-w-3xl">

        {{-- Encabezado --}}
        <section class="rounded-2xl bg-orange-600 p-4 text-white shadow-sm sm:p-6">

            <div class="flex items-start justify-between gap-3">

                <div class="min-w-0">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-orange-200 sm:text-xs">
                        Resultado del intento
                    </p>

                    <h2 class="mt-1 break-words text-xl font-bold tracking-tight sm:text-2xl">
                        {{ $activity->title }}
                    </h2>

                    <p class="mt-2 flex items-start gap-1.5 text-xs font-bold text-orange-100 sm:text-sm">
                        <svg class="mt-0.5 h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $typeIcon }}" />
                        </svg>
                        <span class="min-w-0">{{ $typeLabels[$activity->type] ?? $activity->type }} · Intento #{{ $participation->attempt }}</span>
                    </p>
                </div>

                <span class="shrink-0 rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide bg-white sm:text-xs {{ $statusClass }}">
                    {{ $statusText }}
                </span>

            </div>

        </section>

        {{-- Resumen --}}
        <section class="mt-4 grid grid-cols-3 gap-2 sm:gap-3">

            <div class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm sm:p-4">
                <div class="mx-auto flex h-9 w-9 items-center justify-center rounded-xl bg-orange-50 text-orange-700 ring-1 ring-orange-100">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0-3.375-6.375m-7.125 0L6 18.75m4.875-9.75L9.75 2.25h4.5l-1.125 6.75h-2.25Z" />
                    </svg>
                </div>
                <p class="mt-2 text-center text-[10px] font-bold uppercase tracking-wide text-slate-400">
                    Puntaje
                </p>
                <p class="mt-0.5 text-center text-base font-bold text-orange-700 sm:text-lg">
                    {{ $participation->score }} / {{ $activity->max_score }}
                </p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm sm:p-4">
                <div class="mx-auto flex h-9 w-9 items-center justify-center rounded-xl bg-slate-50 text-slate-600 ring-1 ring-slate-200">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <p class="mt-2 text-center text-[10px] font-bold uppercase tracking-wide text-slate-400">
                    Tiempo
                </p>
                <p class="mt-0.5 text-center text-base font-bold text-slate-700 sm:text-lg">
                    {{ $elapsed ?? '—' }}
                </p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm sm:p-4">
                <div class="mx-auto flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                </div>
                <p class="mt-2 text-center text-[10px] font-bold uppercase tracking-wide text-slate-400">
                    Aciertos
                </p>
                <p class="mt-0.5 text-center text-base font-bold sm:text-lg {{ $correctCount === $total && $total > 0 ? 'text-emerald-700' : ($correctCount > 0 ? 'text-slate-700' : 'text-red-600') }}">
                    {{ $correctCount }} / {{ $total }}
                </p>
            </div>

        </section>

        {{-- Respuestas --}}
        <section class="mt-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">

            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-orange-50 text-orange-700 ring-1 ring-orange-100">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                </div>

                <div class="min-w-0">
                    <h3 class="text-lg font-bold text-slate-900">
                        Detalle de respuestas
                    </h3>

                    <p class="text-sm text-slate-600">
                        {{ $typeLabels[$activity->type] ?? $activity->type }} · intento #{{ $participation->attempt }}
                    </p>
                </div>
            </div>

            <div class="mt-4 grid gap-2.5 sm:grid-cols-2">

                @forelse ($answers as $answer)
                    <div class="flex items-start gap-3 rounded-xl border p-3.5 {{ $answer['is_correct'] ? 'border-emerald-200 bg-emerald-50/60' : 'border-red-200 bg-red-50/60' }}">

                        <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full {{ $answer['is_correct'] ? 'bg-emerald-600' : 'bg-red-600' }} text-white">
                            @if ($answer['is_correct'])
                                <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                            @else
                                <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                            @endif
                        </span>

                        <div class="min-w-0 flex-1">
                            <p class="break-words text-sm font-bold text-slate-900">
                                @if (isset($answer['word']))
                                    Palabra: {{ $answer['word'] }}
                                @elseif (isset($answer['clue']))
                                    {{ $answer['clue'] }}
                                @elseif (isset($answer['question']))
                                    {{ $answer['question'] }}
                                @elseif (isset($answer['left'], $answer['right']))
                                    {{ $answer['left'] }}
                                    <svg class="mx-0.5 inline h-4 w-4 shrink-0 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                                    </svg>
                                    {{ $answer['right'] }}
                                @else
                                    Elemento del juego
                                @endif
                            </p>

                            @if (isset($answer['response']))
                                <p class="mt-0.5 break-words text-xs text-slate-600">
                                    Tu respuesta: {{ $answer['response'] }}
                                    @if (isset($answer['correct']) && ! $answer['is_correct'])
                                        · Correcta: {{ $answer['correct'] }}
                                    @endif
                                </p>
                            @endif

                            <div class="mt-2 flex items-center gap-1.5">
                                <span class="rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide {{ $answer['is_correct'] ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $answer['is_correct'] ? 'Correcta' : 'Incorrecta' }}
                                </span>

                                @if (isset($answer['score']))
                                    <span class="rounded-full bg-white px-2 py-0.5 text-[10px] font-bold text-slate-600 ring-1 ring-slate-200">
                                        {{ $answer['is_correct'] ? '+'.$answer['score'] : '0' }} pts
                                    </span>
                                @endif
                            </div>
                        </div>

                    </div>
                @empty
                    <div class="col-span-full rounded-xl border border-slate-200 bg-slate-50 p-4 text-center" role="status">
                        <svg class="mx-auto h-8 w-8 text-slate-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                        </svg>
                        <p class="mt-2 text-sm text-slate-600">
                            No hay respuestas registradas para este intento.
                        </p>
                    </div>
                @endforelse

            </div>

        </section>

    </div>

@endsection
