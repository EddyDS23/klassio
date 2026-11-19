@extends('teacher.layouts.app')

@section('title', 'Sala '.$session->code.' — Profesor')

@section('page-title', 'Sala '.$session->code)

@section('content')

    @php
        $isRace = in_array($session->activity->type, ['crossword', 'word_search', 'matching'], true);
        $isKahoot = $session->activity->type === 'kahoot';

        $typeIcons = [
            'kahoot' => 'm3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z',
            'crossword' => 'M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.75m-.75 3h.75m0 0H21',
            'word_search' => 'm21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z',
            'matching' => 'M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244',
            'roulette' => 'M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99',
        ];

        $typeIcon = $typeIcons[$session->activity->type] ?? $typeIcons['roulette'];
    @endphp

    <div class="mx-auto max-w-5xl">

        <div class="mb-6 flex justify-end">
            <a href="{{ route('teacher.dashboard') }}"
               class="inline-flex self-start items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Volver al panel
            </a>
        </div>

        {{-- Encabezado --}}
        <section class="mb-6 rounded-2xl bg-emerald-700 p-5 text-white shadow-sm sm:p-6">

            <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">

                <div class="min-w-0">
                    <p class="text-xs font-bold uppercase tracking-widest text-emerald-200">
                        Salas multijugador
                    </p>

                    <h2 class="mt-1 text-2xl font-bold tracking-tight sm:text-3xl">
                        {{ $session->status === 'waiting'
                            ? 'Tu sala está lista'
                            : ($session->status === 'finished' ? 'Partida finalizada' : 'Panel de la partida') }}
                    </h2>

                    <p class="mt-2 flex items-center gap-1.5 text-sm font-bold text-emerald-100">
                        <svg class="h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $typeIcon }}" />
                        </svg>
                        {{ $session->activity->title }}
                    </p>
                </div>

                <div class="hidden h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-emerald-600 ring-1 ring-emerald-500 md:flex">
                    <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $typeIcon }}" />
                    </svg>
                </div>

            </div>

        </section>

        {{-- Mensaje del servidor --}}
        @if (session('error'))
            <div class="mb-6 flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50 p-4 shadow-sm sm:p-5">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-700 ring-1 ring-red-200">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                </div>
                <p class="text-sm text-red-800">{{ session('error') }}</p>
            </div>
        @endif

        {{-- Código de la sala --}}
        <section class="mb-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">

            <div class="flex flex-col items-center gap-4 text-center sm:flex-row sm:justify-between sm:gap-3 sm:text-left">

                <div class="min-w-0">
                    <p class="text-sm text-slate-500">
                        Código de la sala — compártelo con tus alumnos
                    </p>

                    <div class="mt-2 flex flex-col items-center gap-3 sm:flex-row sm:items-center">
                        <p class="text-4xl font-bold tracking-[0.25em] text-emerald-900 sm:text-5xl" id="room-code">
                            {{ $session->code }}
                        </p>

                        @if ($session->status === 'waiting')
                            <button type="button" id="copy-room-code"
                                    class="inline-flex min-h-11 shrink-0 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2">
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.76a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" />
                                </svg>
                                Copiar código
                            </button>
                        @endif
                    </div>

                    <p id="room-code-feedback" class="mt-2 min-h-5 text-xs font-bold text-emerald-700" role="status" aria-live="polite"></p>
                </div>

                <div class="flex flex-wrap items-center justify-center gap-2 sm:justify-end">
                    <span id="room-status"
                          class="rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wide {{ $session->status === 'playing' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-700' }}">
                        {{ $session->status === 'waiting' ? 'Esperando jugadores' : ($session->status === 'playing' ? 'En partida' : 'Finalizada') }}
                    </span>

                    <span id="room-count"
                          class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700"
                          role="status" aria-atomic="true">
                        {{ $players->count() }} / {{ $session->max_players }} jugadores
                    </span>
                </div>

            </div>

        </section>

        {{-- Participantes --}}
        <section class="mb-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">

            <h3 class="text-lg font-bold text-slate-900">
                Participantes
            </h3>

            <p class="mt-0.5 text-sm text-slate-600">
                La lista se actualiza automáticamente cuando alguien entra o sale.
            </p>

            <ul id="players" class="mt-4 divide-y divide-slate-100">
                @foreach ($players as $p)
                    <li class="player-row flex items-center gap-3 py-2.5" data-participation-id="{{ $p->id }}">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-sm font-bold text-emerald-700 ring-1 ring-emerald-100">
                            {{ mb_substr($p->team?->name ?? $p->student?->name, 0, 1) }}
                        </span>

                        <span class="min-w-0 flex-1 truncate text-sm font-bold text-slate-800">
                            {{ $p->team?->name ?? $p->student?->name }}
                        </span>

                        <span class="shrink-0 text-xs font-bold text-emerald-700">
                            En la sala
                        </span>
                    </li>
                @endforeach
            </ul>

            <div id="empty-players" class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600 {{ $players->isEmpty() ? '' : 'hidden' }}" role="status">
                Comparte el código de sala para que tus estudiantes puedan unirse.
            </div>

        </section>

        {{-- Acciones de la sala --}}
        @if ($session->status === 'waiting')
            <section class="mb-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">

                <form method="POST" action="{{ route('teacher.game-sessions.start', $session->id) }}">
                    @csrf

                    <button type="submit" id="start-btn" @disabled(! $canStart)
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-700 px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.28 6.347a1.125 1.125 0 0 1 0 1.972l-11.28 6.347a1.125 1.125 0 0 1-1.667-.985V5.653Z" />
                        </svg>
                        Comenzar partida
                    </button>

                    <p id="start-hint" class="mt-3 text-center text-xs font-bold text-slate-500 sm:text-left {{ $canStart ? 'hidden' : '' }}">
                        Se necesitan al menos 2 participantes activos para comenzar.
                    </p>
                </form>

                @if ($players->isEmpty())
                    <div class="mt-4 border-t border-slate-100 pt-4">
                        <p class="text-sm text-slate-600">
                            Si no vas a usar esta sala, ciérrala para quitarla de tus partidas activas.
                        </p>

                        <form method="POST" action="{{ route('teacher.game-sessions.cancel', $session->id) }}"
                              id="cancel-form" class="mt-3">
                            @csrf

                            <div class="relative inline-block w-full sm:w-auto">
                                <button type="button" data-confirm-toggle
                                        aria-expanded="false"
                                        aria-controls="cancel-confirm"
                                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-red-200 bg-white px-5 py-2.5 text-sm font-bold text-red-700 transition hover:bg-red-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2 sm:w-auto">
                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                    </svg>
                                    Cerrar sala sin iniciar
                                </button>

                                <div id="cancel-confirm"
                                     class="mt-2 hidden w-full rounded-xl border border-red-200 bg-red-50 p-3 shadow-sm sm:absolute sm:right-0 sm:top-14 sm:z-10 sm:mt-0 sm:w-72"
                                     data-confirm-panel>
                                    <p class="text-xs font-bold text-red-800">
                                        ¿Cerrar esta sala sin iniciar? Ya no aparecerá entre las partidas activas.
                                    </p>

                                    <div class="mt-2 flex gap-2">
                                        <button type="submit"
                                                class="flex-1 rounded-lg bg-red-600 px-3 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-red-700">
                                            Sí, cerrar sala
                                        </button>

                                        <button type="button" data-confirm-cancel
                                                class="rounded-lg border border-red-200 bg-white px-3 py-2 text-xs font-bold text-slate-600 transition hover:bg-red-50">
                                            Cancelar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif

            </section>
        @endif

        {{-- Control de la partida --}}
        @if ($session->status === 'playing')

            @if ($isRace)
                <section class="mb-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6" id="race-panel">

                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <h3 class="text-lg font-bold text-slate-900">
                            Progreso de la partida
                        </h3>

                        <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700">
                            Tiempo:
                            <span id="race-timer" class="font-mono tabular-nums">--</span>
                        </span>
                    </div>

                    <p class="mt-1 text-sm text-slate-600">
                        Cada participante avanza a su ritmo. La sesión termina cuando todos completan o se agota el tiempo.
                    </p>

                    <h4 class="mt-4 text-sm font-bold text-slate-700">
                        Posiciones
                    </h4>

                    <ul id="race-standings" class="max-h-96 divide-y divide-slate-100 overflow-y-auto"></ul>

                </section>
            @else
                <section class="mb-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6" id="round-panel">

                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <h3 class="text-lg font-bold text-slate-900">
                            {{ $isKahoot ? 'Control del Kahoot' : 'Control de la ruleta' }}
                        </h3>

                        <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700">
                            Tiempo:
                            <span id="round-timer" class="font-mono tabular-nums">--</span>
                        </span>
                    </div>

                    <div id="round-question" class="mt-4">
                        <p class="text-sm text-slate-600">
                            {{ $isKahoot
                                ? 'Avanza para mostrar la siguiente pregunta.'
                                : 'Gira la ruleta para mostrar la siguiente pregunta.' }}
                        </p>
                    </div>

                    <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-end">
                        <div class="w-full sm:w-28">
                            <label for="round-seconds" class="mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-400">
                                Segundos
                            </label>

                            <input type="number" id="round-seconds" min="5" max="300" step="5" value="30"
                                   class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm font-bold text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100">
                        </div>

                        <button type="button" id="spin-btn"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-700 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 sm:w-auto">
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                @if ($isKahoot)
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z" />
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                @endif
                            </svg>
                            {{ $isKahoot ? 'Siguiente pregunta' : 'Girar la ruleta' }}
                        </button>

                        <div class="rounded-xl bg-slate-50 px-3 py-2.5 text-xs font-bold text-slate-600 ring-1 ring-slate-200 sm:ml-auto">
                            Respondieron:
                            <span id="round-answered">0</span> / <span id="round-total">{{ $players->count() }}</span>
                        </div>
                    </div>

                    <p id="spin-feedback" class="mt-2 min-h-5 text-xs font-bold text-red-700" role="status" aria-live="polite"></p>

                </section>
            @endif

        @endif

        {{-- Resultados --}}
        @if ($session->status === 'finished')
            <section class="mb-8 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
                <a href="{{ route('teacher.game-sessions.result', $session->id) }}"
                   class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-700 px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                    Ver resultados
                </a>
            </section>
        @endif

    </div>

@endsection

@push('scripts')
    @if ((bool) config('broadcasting.connections.reverb.enabled', false))
        <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
        <script>
            window.Echo = new Echo({
                broadcaster: 'reverb',
                key: @json(config('broadcasting.connections.reverb.key')),
                wsHost: @json(config('broadcasting.connections.reverb.client_host', 'localhost')),
                wsPort: @json(config('broadcasting.connections.reverb.options.port', 8080)),
                forceTLS: @json(config('broadcasting.connections.reverb.options.scheme', 'http') === 'https'),
                enabledTransports: ['ws', 'wss']
            });
        </script>
    @endif

    <script>
        (function () {
            const sessionId = @json($session->id);
            const stateUrl = @json(route('teacher.game-sessions.state', $session->id));
            const resultUrl = @json(route('teacher.game-sessions.result', $session->id));
            const spinUrl = @json(route('teacher.game-sessions.spin', $session->id));
            const csrfToken = @json(csrf_token());

            const isKahoot = @json($session->activity->type === 'kahoot');
            const isRace = @json($isRace);
            const idleButton = isKahoot ? 'Siguiente pregunta' : 'Girar la ruleta';
            const idleHint = isKahoot ?
                'Avanza para mostrar la siguiente pregunta.' :
                'Gira la ruleta para mostrar la siguiente pregunta.';

            const statusLabel = {
                waiting: 'Esperando jugadores',
                starting: 'Iniciando',
                playing: 'En partida',
                finished: 'Finalizada',
                cancelled: 'Cancelada'
            };

            const statusPillClass = {
                waiting: 'bg-emerald-100 text-emerald-700',
                starting: 'bg-emerald-100 text-emerald-700',
                playing: 'bg-amber-100 text-amber-800',
                finished: 'bg-emerald-100 text-emerald-700',
                cancelled: 'bg-red-100 text-red-700'
            };

            let roundRemaining = null;
            let roundOpen = false;
            let raceRemaining = null;
            let refreshInProgress = false;
            let navigationStarted = false;

            function navigateToResultsOnce() {
                if (navigationStarted) return;
                navigationStarted = true;
                window.location.href = resultUrl;
            }

            function formatTime(s) {
                s = Math.max(0, s);
                const m = Math.floor(s / 60).toString().padStart(2, '0');
                const sec = (s % 60).toString().padStart(2, '0');
                return m + ':' + sec;
            }

            function playerRow(name, badgeHtml, badgeIsHtml) {
                const li = document.createElement('li');
                li.className = 'player-row flex items-center gap-3 py-2.5';

                const avatar = document.createElement('span');
                avatar.className = 'flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-sm font-bold text-emerald-700 ring-1 ring-emerald-100';
                avatar.textContent = (name || '?').charAt(0).toUpperCase();
                li.appendChild(avatar);

                const nameEl = document.createElement('span');
                nameEl.className = 'min-w-0 flex-1 truncate text-sm font-bold text-slate-800';
                nameEl.textContent = name;
                li.appendChild(nameEl);

                const badge = document.createElement('span');
                badge.className = 'shrink-0';

                if (badgeIsHtml) {
                    badge.innerHTML = badgeHtml;
                } else {
                    badge.textContent = badgeHtml;
                }

                li.appendChild(badge);

                return li;
            }

            function renderRound(state) {
                const panel = document.getElementById('round-panel');
                if (!panel) return;

                const spinBtn = document.getElementById('spin-btn');
                const questionEl = document.getElementById('round-question');
                const answeredEl = document.getElementById('round-answered');
                const totalEl = document.getElementById('round-total');

                if (state.status !== 'playing') {
                    return;
                }

                roundOpen = state.roulette_item_id !== null || state.question_id !== null;
                roundRemaining = typeof state.round_time_remaining === 'number' ?
                    state.round_time_remaining :
                    null;

                totalEl.textContent = state.players.filter(
                    (p) => p.status === 'started'
                ).length;

                const answered = state.players.filter((p) => p.answered_current).length;
                answeredEl.textContent = answered;

                if (roundOpen && state.current_item) {
                    const question = document.createElement('p');
                    question.className = 'text-base font-bold text-slate-900';
                    question.textContent = state.current_item.question;

                    const options = document.createElement('ul');
                    options.className = 'mt-3 grid gap-2 sm:grid-cols-2';

                    state.current_item.options.forEach((option, index) => {
                        const item = document.createElement('li');
                        item.className = 'flex items-center gap-2.5 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5';

                        const key = document.createElement('span');
                        key.className = 'flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-700';
                        key.textContent = option.key ? option.key.toUpperCase() : index + 1;

                        const text = document.createElement('span');
                        text.className = 'min-w-0 flex-1 text-sm text-slate-800';
                        text.textContent = option.text;

                        item.append(key, text);
                        options.appendChild(item);
                    });

                    questionEl.replaceChildren(question, options);

                    spinBtn.disabled = true;
                    spinBtn.textContent = 'Ronda en curso';
                } else {
                    const hint = document.createElement('p');
                    hint.className = 'text-sm text-slate-600';
                    hint.textContent = idleHint;
                    questionEl.replaceChildren(hint);
                    spinBtn.disabled = false;
                    spinBtn.textContent = idleButton;
                }
            }

            function renderState(state) {
                const statusEl = document.getElementById('room-status');
                statusEl.textContent = statusLabel[state.status] || state.status;
                statusEl.className = 'rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wide ' +
                    (statusPillClass[state.status] || 'bg-slate-100 text-slate-700');

                const countEl = document.getElementById('room-count');
                countEl.textContent = state.players.length + ' / ' + state.max_players + ' jugadores';

                const startBtn = document.getElementById('start-btn');
                const startHint = document.getElementById('start-hint');

                if (startBtn && state.status === 'waiting') {
                    const activePlayers = state.players.filter(
                        (p) => !['abandoned', 'expired'].includes(p.status)
                    ).length;

                    const canStart = activePlayers >= 2;

                    startBtn.disabled = !canStart;

                    if (startHint) {
                        startHint.classList.toggle('hidden', canStart);
                    }
                }

                const list = document.getElementById('players');
                const rows = [];

                state.players.forEach((p) => {
                    if (p.status === 'abandoned' || p.status === 'expired') return;

                    if ((state.roulette_item_id || state.question_id) && p.answered_current) {
                        rows.push(playerRow(p.name, '<span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-700">Respondió</span>', true));
                    } else {
                        rows.push(playerRow(p.name, 'En la sala', false));
                    }
                });

                list.replaceChildren(...rows);

                document.getElementById('empty-players').classList.toggle('hidden', state.players.length > 0);

                if (isRace) {
                    raceRemaining = typeof state.time_remaining === 'number' ?
                        state.time_remaining :
                        null;

                    const standings = document.getElementById('race-standings');
                    if (standings) {
                        const entries = [];

                        state.players
                            .filter((p) => p.status !== 'abandoned')
                            .slice()
                            .sort((a, b) => b.score - a.score)
                            .forEach((p, index) => {
                                const li = document.createElement('li');
                                li.className = 'player-row flex items-center gap-3 py-2.5';

                                const rank = document.createElement('span');
                                rank.className = 'flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-600';
                                rank.textContent = index + 1;

                                const name = document.createElement('span');
                                name.className = 'min-w-0 flex-1 truncate text-sm font-bold text-slate-800';
                                name.textContent = p.name;

                                li.append(rank, name);

                                const score = document.createElement('span');
                                score.className = 'shrink-0 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-700';
                                score.textContent = p.score + ' pts';
                                li.appendChild(score);

                                if (p.status === 'completed') {
                                    const done = document.createElement('span');
                                    done.className = 'shrink-0 rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-700';
                                    done.textContent = 'Completado';
                                    li.appendChild(done);
                                }

                                entries.push(li);
                            });

                        standings.replaceChildren(...entries);
                    }
                }

                renderRound(state);
            }

            function setupStateSync(refreshFn) {
                let fallbackPolling = null;

                const startFallback = () => {
                    if (fallbackPolling !== null) return;

                    // Recupera el estado inmediatamente y continúa consultándolo
                    // mientras Reverb no esté conectado.
                    refreshFn();
                    fallbackPolling = window.setInterval(refreshFn, 2000);
                };

                const stopFallback = () => {
                    if (fallbackPolling === null) return;

                    window.clearInterval(fallbackPolling);
                    fallbackPolling = null;
                };

                const connection = window.Echo?.connector?.pusher?.connection;

                // Sin Echo o sin conexión WebSocket, conservar el comportamiento
                // de respaldo que ya tenía la vista.
                if (!connection) {
                    startFallback();
                    return;
                }

                connection.bind('state_change', ({
                    current
                }) => {
                    if (current === 'connected') {
                        stopFallback();
                        refreshFn();
                    } else {
                        startFallback();
                    }
                });

                if (connection.state === 'connected') {
                    refreshFn();
                } else {
                    startFallback();
                }
            }

            function tick() {
                const el = document.getElementById('round-timer');
                if (el) {
                    if (roundOpen && roundRemaining !== null) {
                        el.textContent = formatTime(roundRemaining);
                        if (roundRemaining > 0) roundRemaining--;
                    } else {
                        el.textContent = '--';
                    }
                }

                const raceEl = document.getElementById('race-timer');
                if (raceEl) {
                    raceEl.textContent = raceRemaining !== null ? formatTime(raceRemaining) : '--';
                    if (raceRemaining !== null && raceRemaining > 0) raceRemaining--;
                }
            }

            async function spin() {
                const spinBtn = document.getElementById('spin-btn');
                const feedback = document.getElementById('spin-feedback');
                if (!spinBtn || spinBtn.disabled) return;

                spinBtn.disabled = true;
                spinBtn.textContent = 'Girando…';
                if (feedback) {
                    feedback.textContent = '';
                }

                const seconds = Number(document.getElementById('round-seconds').value) || 30;

                try {
                    const res = await fetch(spinUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            seconds: seconds
                        })
                    });
                    const data = await res.json();
                    if (!res.ok) {
                        if (feedback) {
                            feedback.textContent = data.error || 'No se pudo iniciar la ronda.';
                        }
                        spinBtn.disabled = false;
                        spinBtn.textContent = idleButton;
                    } else if (data.state) {
                        renderState(data.state);
                        if (data.finished) {
                            navigateToResultsOnce();
                            return;
                        }
                    }
                } catch (e) {
                    if (feedback) {
                        feedback.textContent = 'Error de conexión. Vuelve a intentarlo.';
                    }
                    spinBtn.disabled = false;
                    spinBtn.textContent = idleButton;
                }
            }

            async function refresh() {
                if (refreshInProgress || navigationStarted) return;
                refreshInProgress = true;

                try {
                    const res = await fetch(stateUrl, {
                        headers: {
                            'Accept': 'application/json'
                        },
                        credentials: 'same-origin'
                    });
                    if (!res.ok) return;
                    const state = await res.json();

                    renderState(state);

                    if (state.status === 'finished') {
                        navigateToResultsOnce();
                    }
                } catch (e) {
                    // sin red por ahora
                } finally {
                    refreshInProgress = false;
                }
            }

            setInterval(tick, 1000);

            window.addEventListener('DOMContentLoaded', () => {
                tick();

                const spinBtn = document.getElementById('spin-btn');
                if (spinBtn) {
                    spinBtn.addEventListener('click', spin);
                }

                // Confirmación en dos pasos para cerrar la sala.
                const cancelForm = document.getElementById('cancel-form');
                if (cancelForm) {
                    const toggle = cancelForm.querySelector('[data-confirm-toggle]');
                    const panel = cancelForm.querySelector('[data-confirm-panel]');

                    if (toggle && panel) {
                        toggle.addEventListener('click', () => {
                            const wasOpen = toggle.getAttribute('aria-expanded') === 'true';

                            panel.classList.toggle('hidden', wasOpen);
                            toggle.setAttribute('aria-expanded', wasOpen ? 'false' : 'true');
                        });

                        cancelForm.querySelector('[data-confirm-cancel]')?.addEventListener('click', () => {
                            panel.classList.add('hidden');
                            toggle.setAttribute('aria-expanded', 'false');
                        });
                    }
                }

                const copyRoomCodeButton = document.getElementById('copy-room-code');
                if (copyRoomCodeButton) {
                    copyRoomCodeButton.addEventListener('click', async () => {
                        const feedback = document.getElementById('room-code-feedback');
                        try {
                            await navigator.clipboard.writeText(document.getElementById('room-code').textContent.trim());
                            if (feedback) {
                                feedback.textContent = 'Código copiado. Ya puedes compartirlo con el grupo.';
                            }
                        } catch (error) {
                            if (feedback) {
                                feedback.textContent = 'No se pudo copiar automáticamente. Selecciona el código para compartirlo.';
                            }
                        }
                    });
                }

                const reverbEnabled = @json((bool) config('broadcasting.connections.reverb.enabled', false));
                if (window.Echo && reverbEnabled) {
                    setupStateSync(refresh);
                    window.Echo.private('game-session.' + sessionId)
                        .listen('.GameFinished', (e) => {
                            navigateToResultsOnce();
                        })
                        .listen('.PlayerJoined', (e) => refresh())
                        .listen('.PlayerLeft', (e) => refresh())
                        .listen('.RoundStarted', (e) => refresh())
                        .listen('.RoundClosed', (e) => refresh())
                        .listen('.ScoreUpdated', (e) => refresh())
                        .listen('.TurnChanged', (e) => refresh());
                } else {
                    setupStateSync(refresh);
                }
            });
        })();
    </script>
@endpush
