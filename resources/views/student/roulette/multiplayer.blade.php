<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Ruleta — Sala {{ $session->code }}</title>

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

    @include('partials.assets', ['theme' => 'student', 'includeBootstrap' => false])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; }

        /* === Internos del juego (JS modifica estas clases) === */
        #timer-value.warning { color: #fde68a; }
        #timer-value.danger { color: #fecaca; }
        #wait-msg.spinning { color: #be185d; }

        .progress-track { height: 9px; overflow: hidden; border-radius: 999px; background: #fce7f3; }
        .progress-fill { width: 0%; height: 100%; border-radius: inherit; background: #be185d; transition: width .35s ease; }

        .wheel-stage { position: relative; width: min(410px, 82vw); aspect-ratio: 1; display: grid; place-items: center; margin: 0 auto; }
        .wheel-shadow { position: absolute; inset: 7%; border-radius: 50%; background: rgb(190 24 93 / .2); filter: blur(24px); transform: translateY(15px); }
        .pointer { position: absolute; top: -2px; left: 50%; z-index: 20; width: 0; height: 0; transform: translateX(-50%); border-left: 19px solid transparent; border-right: 19px solid transparent; border-top: 33px solid #9d174d; filter: drop-shadow(0 5px 4px rgb(0 0 0 / .22)); }
        .pointer::after { content: ""; position: absolute; left: -9px; top: -32px; width: 18px; height: 9px; border-radius: 999px; background: #fce7f3; }
        .wheel-border { position: relative; z-index: 5; width: 100%; height: 100%; padding: 11px; border-radius: 50%; background: #be185d; box-shadow: 0 22px 42px rgb(190 24 93 / .25), inset 0 0 0 2px rgb(255 255 255 / .25); }
        .wheel { position: relative; width: 100%; height: 100%; overflow: hidden; border: 5px solid #fff; border-radius: 50%; background: #fbcfe8; }
        .wheel::after { content: ""; position: absolute; inset: 0; z-index: 3; border-radius: 50%; box-shadow: inset 0 0 0 3px rgb(255 255 255 / .5), inset 0 0 35px rgb(157 23 77 / .18); pointer-events: none; }
        .wheel.spinning { animation: multiplayer-wheel-spin 4.6s cubic-bezier(.11, .82, .19, 1); }
        @keyframes multiplayer-wheel-spin { to { transform: rotate(1440deg); } }
        .wheel-center { position: absolute; top: 50%; left: 50%; z-index: 30; width: 104px; height: 104px; transform: translate(-50%, -50%); border: 8px solid #fff; border-radius: 50%; background: #9d174d; box-shadow: 0 9px 22px rgb(190 24 93 / .3), 0 0 0 6px rgb(190 24 93 / .17); }
        .wheel-center:disabled { cursor: default; }
        button.wheel-center { border-radius: 50%; }
        button.option-btn { border-radius: .9rem; }

        .modal-backdrop { position: fixed; inset: 0; z-index: 1000; display: none; align-items: center; justify-content: center; padding: 18px; background: rgb(80 7 36 / .68); }
        .modal-backdrop.visible { display: flex; }
        .modal-backdrop[aria-hidden="true"] { visibility: hidden; }
        .question-modal { width: min(700px, 100%); max-height: min(760px, calc(100vh - 36px)); overflow: auto; border-radius: 1.25rem; background: #fff; box-shadow: 0 30px 90px rgb(0 0 0 / .28); }
        .modal-top { padding: 18px 20px; color: #fff; background: #be185d; }
        .modal-top-row { display: flex; align-items: center; justify-content: space-between; gap: 15px; }
        .modal-label { margin: 0; font-size: .72rem; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; opacity: .8; }
        .modal-points { padding: 5px 10px; border: 1px solid rgb(255 255 255 / .25); border-radius: 999px; background: rgb(255 255 255 / .12); font-size: .76rem; font-weight: 700; }
        .modal-body { padding: 22px; }
        #q-text { margin: 0 0 20px; color: #1e293b; font-size: clamp(1.2rem, 3vw, 1.5rem); font-weight: 800; line-height: 1.35; }
        .options { display: grid; gap: 11px; }
        .option-btn { width: 100%; display: flex; align-items: center; gap: 13px; padding: 13px 14px; border: 2px solid #e2e8f0; border-radius: .9rem; color: #334155; background: #fff; text-align: left; font-size: .95rem; font-weight: 600; font-family: inherit; cursor: pointer; transition: border-color .15s ease, background .15s ease; }
        .option-btn:hover:not(:disabled) { border-color: #f472b6; background: #fdf2f8; }
        .option-btn:focus-visible { outline: 3px solid #be185d; outline-offset: 2px; }
        .option-key { flex: 0 0 40px; width: 40px; height: 40px; display: grid; place-items: center; border-radius: .7rem; color: #fff; background: #be185d; font-weight: 800; }
        .option-text { flex: 1; line-height: 1.35; }
        .option-btn.correct { border-color: #4ade80; background: #dcfce7; color: #166534; }
        .option-btn.correct .option-key { background: #15803d; }
        .option-btn.wrong { border-color: #f87171; background: #fee2e2; color: #991b1b; }
        .option-btn.wrong .option-key { background: #b91c1c; }
        .option-btn:disabled { cursor: default; }
        .option-btn.round-option-btn:disabled { opacity: .78; }
        .answer-feedback { display: none; margin-top: 16px; padding: 12px 14px; border-radius: .8rem; font-size: .9rem; font-weight: 700; text-align: center; }
        .answer-feedback.visible { display: block; }
        .answer-feedback.ok { color: #166534; border: 1px solid #86efac; background: #dcfce7; }
        .answer-feedback.bad { color: #991b1b; border: 1px solid #fca5a5; background: #fee2e2; }
        .modal-note { max-width: 520px; margin: 16px auto 0; color: #64748b; font-size: .76rem; text-align: center; }

        @media (max-width: 460px) {
            .wheel-stage { width: min(300px, 84vw); }
            .wheel-center { width: 84px; height: 84px; font-size: .78rem; }
            .pointer { border-left-width: 16px; border-right-width: 16px; border-top-width: 28px; }
            .option-btn { padding: 11px; }
            .option-key { flex-basis: 36px; width: 36px; height: 36px; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { scroll-behavior: auto !important; transition-duration: .01ms !important; }
            .wheel, .progress-fill { animation: none !important; }
        }
    </style>
</head>

<body class="student-kids">

    <div class="mx-auto w-full max-w-3xl px-3 py-4 sm:px-5 sm:py-6">

        {{-- Encabezado --}}
        <header class="rounded-2xl bg-pink-700 p-4 text-white shadow-sm sm:p-6">

            <div class="flex items-start justify-between gap-3">

                <div class="flex min-w-0 items-start gap-3">

                    <div class="hidden h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-white/15 ring-1 ring-white/20 sm:flex">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                    </div>

                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-pink-200 sm:text-xs">
                            {{ $activity->mode === 'team' ? 'Actividad por equipos' : 'Partida multijugador' }}
                        </p>
                        <h1 class="mt-0.5 text-lg font-bold tracking-tight sm:mt-1 sm:text-2xl">
                            Ruleta
                        </h1>
                        <p class="mt-0.5 break-words text-xs font-semibold text-pink-100 sm:text-sm">
                            {{ $activity->title }}
                        </p>
                        <p class="mt-1.5 flex flex-wrap items-center gap-1.5">
                            <span class="inline-flex items-center rounded-full bg-white/15 px-2.5 py-1 font-mono text-xs font-bold tracking-[0.15em] ring-1 ring-white/25">
                                {{ $session->code }}
                            </span>
                            <span class="text-xs font-semibold text-pink-100">
                                {{ count($state['players']) }} {{ $activity->mode === 'team' ? 'equipos' : 'participantes' }}
                            </span>
                        </p>
                    </div>

                </div>

                <div class="shrink-0 rounded-xl bg-white/15 px-3 py-2 text-center ring-1 ring-white/25" aria-label="Tiempo restante">
                    <span class="timer-label block text-[10px] font-bold uppercase tracking-wider text-pink-100">
                        Tiempo restante
                    </span>
                    <div class="font-mono text-lg font-bold tabular-nums text-white sm:text-xl">
                        <span id="timer-value">--:--</span>
                    </div>
                    <div class="text-[10px] font-semibold text-pink-100">
                        Ronda: <span id="round-timer" class="tabular-nums">--</span>
                    </div>
                </div>

            </div>

        </header>

        {{-- Resumen --}}
        <p class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-3 sm:gap-3" aria-label="Resumen de la partida">

            <span class="inline-flex min-w-0 items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm shadow-sm sm:justify-start sm:px-4">
                <svg class="h-4 w-4 shrink-0 text-pink-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0-3.375-6.375m-7.125 0L6 18.75m4.875-9.75L9.75 2.25h4.5l-1.125 6.75h-2.25Z" />
                </svg>
                <span class="font-semibold text-slate-500">{{ $activity->mode === 'team' ? 'Equipo' : 'Puntos' }}</span>
                <span class="font-bold text-pink-700"><span id="points">{{ $participation->score }}</span> / {{ $maxScore }}</span>
            </span>

            <span class="inline-flex min-w-0 items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm shadow-sm sm:justify-start sm:px-4">
                <svg class="h-4 w-4 shrink-0 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>
                <span class="font-semibold text-slate-500">Respondidas</span>
                <span class="font-bold text-emerald-700"><span id="answered-count">{{ $answeredCount }}</span> / {{ $totalItems }}</span>
            </span>

            <span class="inline-flex min-w-0 items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm shadow-sm sm:justify-start sm:px-4">
                <svg class="h-4 w-4 shrink-0 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                </svg>
                <span class="font-semibold text-slate-500">Estado</span>
                <span id="game-status" class="font-bold text-slate-700">Esperando</span>
            </span>

        </p>

        {{-- Progreso --}}
        <section class="mt-3 rounded-2xl border border-slate-200 bg-white p-3.5 shadow-sm sm:p-4" aria-label="Progreso de la partida">
            <div class="flex items-center justify-between gap-2 text-xs font-bold text-slate-600 sm:text-sm">
                <span>{{ $activity->mode === 'team' ? 'Progreso del equipo' : 'Progreso de la partida' }}</span>
                <span id="progress-percent" class="font-mono tabular-nums text-pink-700">{{ $totalItems > 0 ? min(100, (int) round(($answeredCount / $totalItems) * 100)) : 0 }}%</span>
            </div>
            <div class="progress-track mt-2" role="progressbar" aria-label="Preguntas respondidas"
                aria-valuemin="0" aria-valuemax="100"
                aria-valuenow="{{ $totalItems > 0 ? min(100, (int) round(($answeredCount / $totalItems) * 100)) : 0 }}">
                <div id="progress-fill" class="progress-fill"
                    style="width: {{ $totalItems > 0 ? min(100, (int) round(($answeredCount / $totalItems) * 100)) : 0 }}%"></div>
            </div>
        </section>

        {{-- Cómo jugar --}}
        <p class="mt-3 flex items-start gap-2 rounded-xl bg-pink-50 px-3.5 py-2.5 text-sm text-pink-900 ring-1 ring-pink-100">
            <svg class="mt-0.5 h-4 w-4 shrink-0 text-pink-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 0 4.51-2.104m0 0A9.04 9.04 0 0 0 12 3a9.04 9.04 0 0 0-6.76 12.986M3.75 19.5a12.06 12.06 0 0 1 4.51-2.104m0 0a6.01 6.01 0 0 0 1.5-.189m0 0a6.01 6.01 0 0 1 1.5-.189" />
            </svg>
            @if ($activity->mode === 'team')
                <span><strong class="font-bold">Cómo jugar:</strong> el profesor gira la ruleta. Cuando aparezca una pregunta, el integrante de turno responde y el equipo puede ayudarle a decidir.</span>
            @else
                <span><strong class="font-bold">Cómo jugar:</strong> el profesor gira la ruleta y abre una pregunta para que respondas antes de que termine el tiempo.</span>
            @endif
        </p>

        {{-- Estado de la ronda --}}
        <section class="mt-3 rounded-2xl border border-slate-200 bg-white p-3.5 text-center shadow-sm sm:p-4" aria-live="polite">
            <span id="turn-badge" tabindex="-1" class="inline-flex items-center rounded-full bg-pink-700 px-3.5 py-1.5 text-sm font-bold text-white">
                Preparando ronda...
            </span>
            <p id="turn-hint" class="mt-2 text-sm text-slate-500">
                Esperando a que el profesor abra la siguiente ronda.
            </p>
            <p id="wait-msg" class="mt-1 text-xs font-bold text-slate-400" aria-live="polite">
                La ruleta comenzará cuando el profesor la gire.
            </p>
        </section>

        {{-- Ruleta --}}
        <section class="mt-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6" aria-label="Ruleta del juego">

            <p class="text-center text-sm font-semibold text-slate-500">
                <strong id="remaining-count" class="font-bold text-pink-700">{{ max($totalItems - $answeredCount, 0) }}</strong>
                <span id="remaining-text">preguntas restantes</span>
            </p>

            <div class="wheel-stage mt-4">
                <div class="wheel-shadow"></div>

                <div class="pointer" aria-hidden="true"></div>

                <div class="wheel-border">
                    <div id="wheel" class="wheel" aria-hidden="true"></div>
                </div>

                <button id="wheel-center" class="wheel-center" type="button" disabled
                    aria-label="La ruleta la controla el profesor">
                    <span id="wheel-center-label" class="text-[.8rem] font-bold tracking-wide text-white">EN ESPERA</span>
                </button>
            </div>

            <div id="spin-hint" class="mt-4 text-center text-sm font-semibold text-slate-500">
                La siguiente pregunta aparecerá aquí.
            </div>

        </section>

        {{-- Marcador de la sala --}}
        <section class="mt-3 rounded-2xl border border-slate-200 bg-white p-3.5 shadow-sm sm:p-4" aria-label="Marcador de la sala">
            <h2 class="text-base font-bold text-slate-900 sm:text-lg">
                Marcador de la sala
            </h2>
            <ul id="players" class="mt-3 grid max-h-72 gap-1.5 overflow-y-auto pr-1">
                @foreach ($state['players'] as $p)
                    <li data-participation-id="{{ $p['participation_id'] }}"
                        class="flex items-center justify-between gap-2 rounded-xl border border-slate-100 bg-slate-50 px-3 py-2 text-sm">
                        <span class="min-w-0 flex-1 truncate text-slate-700">
                            @if ($p['participation_id'] === $participation->id)
                                <strong class="font-bold text-slate-900">{{ $p['name'] }}</strong>
                                <span class="font-semibold text-slate-400">({{ $activity->mode === 'team' ? 'tu equipo' : 'tú' }})</span>
                            @else
                                {{ $p['name'] }}
                            @endif
                        </span>
                        <span data-role="score" class="shrink-0 font-bold tabular-nums text-pink-700">{{ $p['score'] }} / {{ $maxScore }}</span>
                    </li>
                @endforeach
            </ul>
        </section>

        <div class="mt-4 flex justify-end border-t border-slate-200 pt-4">
            <form method="POST" action="{{ route('student.game-sessions.leave', $session->id) }}"
                onsubmit="return confirm('¿Estás seguro de que quieres salir de esta sala?');">
                @csrf
                <button type="submit"
                    class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-red-200 bg-white px-4 py-2.5 text-sm font-bold text-red-600 shadow-sm transition hover:bg-red-50 hover:text-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l3 3m0 0-3 3m3-3H9" />
                    </svg>
                    Salir de la sala
                </button>
            </form>
        </div>

    </div>

    {{-- Modal de pregunta --}}
    <div id="questionModal" class="modal-backdrop" role="dialog" aria-modal="true"
        aria-labelledby="q-text" aria-hidden="true">
        <article class="question-modal" tabindex="-1">
            <header class="modal-top">
                <div class="modal-top-row">
                    <p class="modal-label">Pregunta de la ruleta</p>
                    <span class="modal-points">Ronda por equipos</span>
                </div>
            </header>
            <div class="modal-body">
                <p id="q-text"></p>
                <div id="q-options" class="options"></div>
                <div id="round-answer-feedback" class="answer-feedback" aria-live="polite"></div>
                <p class="modal-note" id="round-question-note"></p>
            </div>
        </article>
    </div>

    <script>
        const sessionId = @json($session->id);
        const myParticipationId = @json($participation->id);
        const csrfToken = @json(csrf_token());
        const totalItems = @json($totalItems);
        const maxScore = @json($maxScore);
        const sharedTeamMode = @json($activity->mode === 'team');

        const stateUrl = @json(route('student.game-sessions.state', $session->id));
        const spinUrl = @json(route('student.roulette.spin-session'));
        const answerUrl = @json(route('student.roulette.answer-session'));
        const resultUrl = @json(route('student.game-sessions.result', $session->id));

        let remainingSeconds = @json($remainingSeconds ?? null);
        let elapsedSeconds = @json($state['time_elapsed'] ?? 0);
        let roundRemaining = @json($state['round_time_remaining'] ?? null);
        let currentItem = null;
        let questionOpen = false;
        let answering = false;
        let fetching = false;
        let answeredThisRound = false;
        let resultDisplayedForItemId = null;
        let finishRedirectQueued = false;

        const wheel = document.getElementById('wheel');
        const turnBadge = document.getElementById('turn-badge');
        const turnHint = document.getElementById('turn-hint');
        const waitMsg = document.getElementById('wait-msg');
        const wheelCenterLabel = document.getElementById('wheel-center-label');
        const spinHint = document.getElementById('spin-hint');
        const questionModal = document.getElementById('questionModal');
        const wheelColors = ['#f0abfc', '#c4b5fd', '#93c5fd', '#67e8f9', '#86efac', '#fde68a', '#fdba74', '#f9a8d4', '#a5b4fc', '#99f6e4'];

        function redirectAfterFinalResult(delay = 0) {
            if (finishRedirectQueued) return;
            finishRedirectQueued = true;
            window.setTimeout(() => {
                window.location.href = resultUrl;
            }, delay);
        }

        questionModal.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                event.preventDefault();
                return;
            }

            if (event.key !== 'Tab') return;

            const options = Array.from(questionModal.querySelectorAll('.round-option-btn:not(:disabled)'));
            if (options.length === 0) {
                event.preventDefault();
                questionModal.querySelector('.question-modal').focus();
                return;
            }

            const firstOption = options[0];
            const lastOption = options[options.length - 1];

            if (event.shiftKey && document.activeElement === firstOption) {
                event.preventDefault();
                lastOption.focus();
            } else if (!event.shiftKey && document.activeElement === lastOption) {
                event.preventDefault();
                firstOption.focus();
            }
        });

        function formatTime(s) {
            s = Math.max(0, s);
            const m = Math.floor(s / 60).toString().padStart(2, '0');
            const sec = (s % 60).toString().padStart(2, '0');
            return m + ':' + sec;
        }

        function tick() {
            const el = document.getElementById('timer-value');
            const timerLabel = document.querySelector('.timer-label');
            if (remainingSeconds !== null) {
                el.textContent = formatTime(remainingSeconds);
                timerLabel.textContent = 'Tiempo restante';
                el.classList.toggle('warning', remainingSeconds <= 60 && remainingSeconds > 20);
                el.classList.toggle('danger', remainingSeconds <= 20);
                if (remainingSeconds > 0) remainingSeconds--;
            } else {
                el.textContent = formatTime(elapsedSeconds);
                timerLabel.textContent = 'Tiempo transcurrido';
                el.classList.remove('warning', 'danger');
                elapsedSeconds++;
            }

            const rt = document.getElementById('round-timer');
            if (roundRemaining === null) {
                rt.textContent = '--';
            } else {
                rt.textContent = Math.max(0, roundRemaining) + 's';
                if (roundRemaining > 0) roundRemaining--;
            }
        }

        function updateBadge(state) {
            if (state.status !== 'playing') return;

            if (state.roulette_item_id && state.answered_current) {
                turnBadge.textContent = sharedTeamMode ? 'Tu equipo ya respondió' : 'Ya respondiste';
                turnHint.textContent = 'Espera a que los demás respondan o termine el tiempo.';
                waitMsg.classList.remove('spinning');
                waitMsg.textContent = sharedTeamMode
                    ? 'Respuesta registrada para tu equipo. No necesitan responder otra vez.'
                    : 'Respuesta registrada. No necesitas responder otra vez.';
                wheelCenterLabel.textContent = 'RESPONDIDA';
                spinHint.textContent = 'Tu equipo ya respondió esta pregunta.';
            } else if (state.roulette_item_id) {
                const canAnswer = !sharedTeamMode || state.can_answer_current === true;
                const responderName = state.current_responder_name;
                turnBadge.textContent = canAnswer
                    ? '¡Te toca responder!'
                    : responderName
                        ? 'Turno de '+responderName
                        : 'Esperando a un integrante del equipo';
                turnHint.textContent = canAnswer
                    ? 'Tu equipo puede ayudarte a elegir la respuesta.'
                    : `Mira la pregunta y ayuda a ${responderName || 'tu equipo'} a decidir.`;
                waitMsg.classList.add('spinning');
                waitMsg.textContent = canAnswer
                    ? 'La pregunta está lista para que respondas.'
                    : `La persona que responde ahora es ${responderName || 'otro integrante'}.`;
                wheelCenterLabel.textContent = canAnswer ? 'TU TURNO' : 'EN TURNO';
                spinHint.textContent = canAnswer
                    ? 'Elige una opción en la pregunta.'
                    : 'Ayuda a tu compañero a decidir.';
            } else {
                turnBadge.textContent = 'Esperando al profesor...';
                turnHint.textContent = 'El profesor girará la ruleta para la siguiente pregunta.';
                waitMsg.classList.remove('spinning');
                waitMsg.textContent = 'Esperando a que el profesor gire la ruleta…';
                wheelCenterLabel.textContent = 'EN ESPERA';
                spinHint.textContent = 'La siguiente pregunta aparecerá aquí.';
            }
        }

        function updateProgress(playerState, state) {
            if (!playerState) return;

            const answered = Math.min(Number(playerState.answered) || 0, totalItems);
            const score = Number(playerState.score) || 0;
            const remaining = Math.max(totalItems - answered, 0);
            const percentage = totalItems > 0
                ? Math.min(100, Math.round((answered / totalItems) * 100))
                : 0;

            document.getElementById('points').textContent = score;
            document.getElementById('answered-count').textContent = answered;
            document.getElementById('remaining-count').textContent = remaining;
            document.getElementById('remaining-text').textContent = remaining === 1
                ? 'pregunta restante'
                : 'preguntas restantes';
            document.getElementById('progress-percent').textContent = percentage + '%';
            const progressFill = document.getElementById('progress-fill');
            progressFill.style.width = percentage + '%';
            progressFill.parentElement.setAttribute('aria-valuenow', percentage);

            const status = document.getElementById('game-status');
            if (state.status === 'finished' || (totalItems > 0 && answered >= totalItems)) {
                status.textContent = 'Completado';
            } else if (state.roulette_item_id && playerState.answered_current) {
                status.textContent = 'Respondida';
            } else if (state.roulette_item_id) {
                status.textContent = state.can_answer_current === true || !sharedTeamMode
                    ? 'Tu turno'
                    : 'En equipo';
            } else if (answered > 0) {
                status.textContent = 'En progreso';
            } else {
                status.textContent = 'Esperando';
            }

            drawWheel(remaining);
        }

        function drawWheel(remaining) {
            if (remaining === 0) {
                wheel.style.background = 'conic-gradient(#d8b4fe 0deg 360deg)';
                return;
            }

            if (remaining === 1) {
                wheel.style.background = 'conic-gradient(#c084fc 0deg 360deg)';
                return;
            }

            const segmentSize = 360 / remaining;
            const stops = Array.from({ length: remaining }, (_, index) => {
                const start = index * segmentSize;
                const end = (index + 1) * segmentSize;
                const color = wheelColors[index % wheelColors.length];

                return `${color} ${start}deg ${end}deg`;
            });

            wheel.style.background = `conic-gradient(${stops.join(', ')})`;
        }

        function showQuestionModal() {
            questionModal.classList.add('visible');
            questionModal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';

            const firstOption = questionModal.querySelector('.round-option-btn:not(:disabled)');
            (firstOption || questionModal.querySelector('.question-modal')).focus();
        }

        function hideQuestionModal() {
            questionModal.classList.remove('visible');
            questionModal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
            turnBadge.focus();
        }

        function resetQuestion() {
            document.getElementById('q-text').textContent = '';
            document.getElementById('q-options').replaceChildren();
            const feedback = document.getElementById('round-answer-feedback');
            feedback.className = '';
            feedback.replaceChildren();
            currentItem = null;
            questionOpen = false;
        }

        function closeAsAlreadyAnswered() {
            answeredThisRound = true;
            hideQuestionModal();
            resetQuestion();
            turnBadge.textContent = sharedTeamMode ? 'Tu equipo ya respondió' : 'Ya respondiste';
            turnHint.textContent = 'Espera a que las demás participaciones respondan o termine el tiempo.';
            waitMsg.textContent = sharedTeamMode
                ? 'Respuesta registrada para tu equipo. No necesitan responder otra vez.'
                : 'Respuesta registrada para tu participación. No necesitas responder otra vez.';
        }

        function maybeAutoOpen(state) {
            if (questionOpen || answering || fetching) return;
            if (state.status !== 'playing') return;
            if (state.roulette_item_id && !state.answered_current) {
                fetchRoundItem();
            }
        }

        function renderState(state) {
            document.getElementById('answered-count').textContent = '0';

            const rows = document.querySelectorAll('#players li');
            const map = {};
            state.players.forEach((p) => {
                map[p.participation_id] = p;
            });

            [...state.players]
                .sort((first, second) => (Number(second.score) || 0) - (Number(first.score) || 0))
                .forEach((player) => {
                    const li = Array.from(rows).find((row) =>
                        Number(row.dataset.participationId) === Number(player.participation_id)
                    );

                    if (!li) return;

                    document.getElementById('players').appendChild(li);
                    li.querySelector('[data-role="score"]').textContent = player.score + ' / ' + maxScore;
            });

            const me = state.players.find((p) => p.participation_id === myParticipationId);
            if (me) {
                updateProgress(me, state);
            }

            if (state.status === 'finished') {
                if (hasVisibleRoundResult(me, state)) {
                    revealTeamResult(me, state);
                    redirectAfterFinalResult(sharedTeamMode ? 2200 : 0);
                } else {
                    redirectAfterFinalResult();
                }
                return;
            }

            roundRemaining = typeof state.round_time_remaining === 'number' ?
                state.round_time_remaining :
                null;

            if (state.time_remaining !== null) {
                remainingSeconds = state.time_remaining;
            } else if (typeof state.time_elapsed === 'number') {
                remainingSeconds = null;
                elapsedSeconds = state.time_elapsed;
            }

            updateBadge(state);

            if (questionOpen && !answering && me?.answered_current) {
                if (me.answer_correct_current === null || me.answer_correct_current === undefined) {
                    closeAsAlreadyAnswered();
                }
            }

            if (questionOpen && !answering && currentItem && state.roulette_item_id !== currentItem.id) {
                hideQuestionModal();
                resetQuestion();

                if (!answeredThisRound) {
                    turnBadge.textContent = 'Se acabó el tiempo';
                    turnHint.textContent = 'No respondiste a tiempo; la pregunta quedó fallada.';
                    waitMsg.textContent = 'No respondiste a tiempo. Espera a que el profesor gire de nuevo.';
                }
            }

            revealTeamResult(me, state);

            maybeAutoOpen(state);
        }

        async function refreshState() {
            try {
                const res = await fetch(stateUrl, {
                    headers: {
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });
                if (res.ok) renderState(await res.json());
            } catch (e) {}
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

        
        setInterval(tick, 1000);

        async function fetchRoundItem() {
            if (fetching || questionOpen) return;
            fetching = true;

            try {
                const res = await fetch(spinUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        session_id: sessionId
                    })
                });

                const data = await res.json();

                if (!res.ok) return;

                if (data.finished) {
                    window.location.href = resultUrl;
                    return;
                }

                if (data.already_answered) {
                    closeAsAlreadyAnswered();
                    refreshState();
                    return;
                }

                if (data.item) {
                    currentItem = data.item;
                    showQuestion(data.item, data.can_answer !== false, data.current_responder_name);
                }
            } catch (e) {
                // Silencioso: el polling reintenta.
            } finally {
                fetching = false;
            }
        }

        function showQuestion(item, canAnswer = true, responderName = null) {
            document.getElementById('q-text').textContent = item.question;

            const options = document.getElementById('q-options');
            options.replaceChildren();
            document.getElementById('round-answer-feedback').replaceChildren();
            document.getElementById('round-answer-feedback').className = '';
            document.getElementById('round-question-note').textContent =
                sharedTeamMode
                    ? canAnswer
                        ? 'Te toca elegir la respuesta. Escucha las ideas del equipo.'
                        : `Le toca responder a ${responderName || 'otro integrante'}. ¡Ayúdenle entre todos!`
                    : 'Tu participación cuenta con una respuesta en esta ronda.';

            item.options.forEach((opt) => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'option-btn round-option-btn';
                const badge = document.createElement('span');
                badge.className = 'option-key round-option-key';
                badge.textContent = opt.key.toUpperCase();
                const text = document.createElement('span');
                text.className = 'option-text';
                text.textContent = opt.text;
                btn.disabled = !canAnswer;
                btn.append(badge, text);
                btn.dataset.key = opt.key;
                btn.addEventListener('click', () => answer(btn.dataset.key, btn));
                options.appendChild(btn);
            });

            answeredThisRound = false;
            questionOpen = true;
            showQuestionModal();
        }

        function showRoundResult(isCorrect, score, correctText = null) {
            if (!currentItem) return;

            const itemId = currentItem.id;
            const feedback = document.getElementById('round-answer-feedback');
            feedback.className = 'answer-feedback visible ' + (isCorrect ? 'ok' : 'bad');
            const result = document.createElement('p');
            result.className = 'mb-1';
            result.textContent = isCorrect ? '¡Correcto!' : 'Respuesta incorrecta';
            const children = [result];

            if (correctText) {
                const correct = document.createElement('p');
                correct.className = 'mb-1 small';
                correct.textContent = 'Respuesta correcta: ' + correctText;
                children.push(correct);
            }

            const scoreLine = document.createElement('p');
            scoreLine.className = 'mb-0 small';
            scoreLine.textContent = (sharedTeamMode ? 'Puntaje del equipo: ' : 'Tu puntaje: ')
                + score + ' / ' + maxScore;
            children.push(scoreLine);
            feedback.replaceChildren(...children);
            document.getElementById('round-question-note').textContent = sharedTeamMode
                ? 'La respuesta ya quedó registrada para todo el equipo.'
                : 'Tu respuesta quedó registrada.';
            Array.from(document.querySelectorAll('.round-option-btn')).forEach((option) => {
                option.disabled = true;
            });
            resultDisplayedForItemId = itemId;

            window.setTimeout(() => {
                if (currentItem?.id !== itemId) return;

                hideQuestionModal();
                resetQuestion();
                answering = false;
                refreshState();
            }, 1800);
        }

        function revealTeamResult(playerState, gameState) {
            const itemIsActive = gameState.roulette_item_id !== null;
            const item = itemIsActive ? gameState.current_item : gameState.last_round_item;
            const hasAnswered = itemIsActive
                ? playerState?.answered_current
                : playerState?.last_round_answered;
            const isCorrect = itemIsActive
                ? playerState?.answer_correct_current
                : playerState?.last_round_answer_correct;

            if (
                answering
                || !hasAnswered
                || isCorrect === null
                || isCorrect === undefined
                || !item
                || resultDisplayedForItemId === item.id
            ) {
                return;
            }

            if (!questionOpen) {
                currentItem = item;
                showQuestion(currentItem, false, playerState.current_responder_name);
            }

            if (currentItem?.id !== item.id) return;

            showRoundResult(
                isCorrect,
                playerState.score
            );
        }

        function hasVisibleRoundResult(playerState, gameState) {
            if (gameState.roulette_item_id !== null) {
                return Boolean(playerState?.answered_current && gameState.current_item
                    && playerState.answer_correct_current !== null
                    && playerState.answer_correct_current !== undefined);
            }

            return Boolean(playerState?.last_round_answered && gameState.last_round_item
                && playerState.last_round_answer_correct !== null
                && playerState.last_round_answer_correct !== undefined);
        }

        async function answer(key, btn) {
            if (answering || !currentItem) return;
            answering = true;

            Array.from(document.querySelectorAll('.round-option-btn')).forEach((b) => b.disabled = true);
            btn.classList.add('disabled');

            try {
                const res = await fetch(answerUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        session_id: sessionId,
                        roulette_item_id: currentItem.id,
                        response: key
                    })
                });

                const data = await res.json();

                if (!res.ok) {
                    if (data.not_your_turn || data.error === 'Ya respondiste esta pregunta.') {
                        answering = false;
                        refreshState();
                        return;
                    }

                    alert(data.error || 'No fue posible registrar tu respuesta.');
                    Array.from(document.querySelectorAll('.round-option-btn')).forEach((b) => b.disabled = false);
                    btn.classList.remove('disabled');
                    answering = false;
                    refreshState();
                    return;
                }

                answeredThisRound = true;
                window.KlassioSounds?.[data.is_correct ? 'correcto' : 'error']();
                showRoundResult(data.is_correct, data.participation_score, data.correct_text);

                if (data.finished) {
                    /*
                     * Música ambiental detenida (endWith del módulo); el
                     * jingle depende de la puntuación final de la sesión.
                     */
                    window.KlassioSounds?.[(Number(data.participation_score) || 0) > 0 ? 'completado' : 'expirado']();
                    redirectAfterFinalResult(2200);
                }
            } catch (e) {
                answering = false;
                alert('Error de conexión. Intenta de nuevo.');
            }
        }

        window.addEventListener('DOMContentLoaded', () => {
            tick();
            refreshState();

            const reverbEnabled = @json((bool) config('broadcasting.connections.reverb.enabled', false));

            if (window.Echo && reverbEnabled) {
                window.Echo.private('game-session.' + sessionId)
                    .listen('.RoundStarted', () => {
                        wheel.classList.add('spinning');
                        setTimeout(() => wheel.classList.remove('spinning'), 4600);
                        refreshState();
                    })
                    .listen('.RoundClosed', () => refreshState())
                    .listen('.TurnChanged', () => refreshState())
                    .listen('.ScoreUpdated', () => refreshState())
                    .listen('.GameFinished', () => {
                        refreshState();
                        redirectAfterFinalResult(sharedTeamMode ? 2200 : 0);
                    });
            }
            setupStateSync(refreshState);
        });
    </script>

    <script src="/js/klassio-sounds.js"></script>
    <script>
        // Música ambiental de la ruleta (juegoruleta.mp3, 20% de volumen):
        // suena en bucle durante TODA la sesión, girada o en respuesta.
        // En esta vista el profesor gira; los eventos correcto / error /
        // completado del script principal disparan los efectos.
        window.KlassioSounds?.setup('roulette');
    </script>

</body>

</html>
