<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Kahoot — Sala {{ $session->code }}</title>

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

        /* === Utilidades que el JS reutiliza (reemplazan a Bootstrap) === */
        [hidden] { display: none !important; }
        .d-none { display: none !important; }
        .fw-bold { font-weight: 700; }
        .small { font-size: .8rem; line-height: 1.45; }
        .mb-0 { margin-bottom: 0; }
        .mb-2 { margin-bottom: .5rem; }
        .alert { padding: 12px 14px; border-radius: .8rem; font-size: .9rem; font-weight: 700; text-align: center; }
        .alert-success { color: #166534; border: 1px solid #86efac; background: #dcfce7; }
        .alert-danger { color: #991b1b; border: 1px solid #fca5a5; background: #fee2e2; }

        /* === Internos del juego (JS modifica estas clases) === */
        #round-clock.is-urgent { background: #fee2e2; color: #dc2626; }

        .question-progress { height: 9px; margin: 14px 0 8px; overflow: hidden; border-radius: 999px; background: #fce7f3; }
        .question-progress > span { display: block; width: 0; height: 100%; border-radius: inherit; background: #be185d; transition: width .3s ease; }

        .answer-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
        .opt-btn { width: 100%; display: flex; min-height: 76px; align-items: center; justify-content: center; padding: 14px; border: 2px solid transparent; border-radius: .75rem; color: #fff; font: inherit; font-size: 1rem; font-weight: 700; line-height: 1.3; text-align: center; overflow-wrap: anywhere; cursor: pointer; transition: opacity .2s, filter .15s; }
        .opt-btn:nth-child(1) { background: #dc2626; }
        .opt-btn:nth-child(2) { background: #2563eb; }
        .opt-btn:nth-child(3) { background: #d97706; }
        .opt-btn:nth-child(4) { background: #059669; }
        button.opt-btn { border-radius: .9rem; }
        .opt-btn:hover:not(:disabled) { filter: brightness(1.06); }
        .opt-btn:focus-visible { outline: 3px solid #be185d; outline-offset: 3px; }
        .opt-btn:disabled, .opt-btn.disabled { cursor: default; opacity: .72; }

        #round-question-note { min-height: 1.4em; }

        @media (max-width: 560px) {
            .answer-grid { grid-template-columns: minmax(0, 1fr); gap: 8px; }
            .opt-btn { min-height: 58px; padding: 12px; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { scroll-behavior: auto !important; transition-duration: .01ms !important; }
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
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                        </svg>
                    </div>

                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-pink-200 sm:text-xs">
                            {{ $activity->mode === 'team' ? 'Actividad por equipos' : 'Partida multijugador' }}
                        </p>
                        <h1 class="mt-0.5 text-lg font-bold tracking-tight sm:mt-1 sm:text-2xl">
                            Kahoot
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
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-pink-100">
                        Tiempo restante
                    </span>
                    <div class="font-mono text-lg font-bold tabular-nums text-white sm:text-xl">
                        <span id="timer">--:--</span>
                    </div>
                    <div id="round-clock" class="text-[10px] font-semibold text-pink-100">
                        Pregunta: <span id="round-timer" class="tabular-nums">--</span>
                    </div>
                </div>

            </div>

        </header>

        {{-- Resumen --}}
        <p class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-2 sm:gap-3" aria-label="Resumen de la partida">

            <span class="inline-flex min-w-0 items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm shadow-sm sm:justify-start sm:px-4">
                <svg class="h-4 w-4 shrink-0 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>
                <span class="font-semibold text-slate-500">Respondidas</span>
                <span class="font-bold text-emerald-700"><span id="answered-count">{{ $answeredCount }}</span> / {{ $totalItems }}</span>
            </span>

            <span class="inline-flex min-w-0 items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm shadow-sm sm:justify-start sm:px-4">
                <svg class="h-4 w-4 shrink-0 text-pink-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                </svg>
                <span class="font-semibold text-slate-500">Preguntas</span>
                <span class="font-bold text-pink-700">{{ $totalItems }}</span>
            </span>

        </p>

        {{-- Cómo jugar --}}
        <p class="mt-3 flex items-start gap-2 rounded-xl bg-pink-50 px-3.5 py-2.5 text-sm text-pink-900 ring-1 ring-pink-100">
            <svg class="mt-0.5 h-4 w-4 shrink-0 text-pink-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 0 4.51-2.104m0 0A9.04 9.04 0 0 0 12 3a9.04 9.04 0 0 0-6.76 12.986M3.75 19.5a12.06 12.06 0 0 1 4.51-2.104m0 0a6.01 6.01 0 0 0 1.5-.189m0 0a6.01 6.01 0 0 1 1.5-.189" />
            </svg>
            @if ($activity->mode === 'team')
                <span><strong class="font-bold">Cómo jugar:</strong> el profesor lanza las preguntas. Quien tenga el turno responde y el equipo puede ayudarle a decidir antes de que termine el tiempo.</span>
            @else
                <span><strong class="font-bold">Cómo jugar:</strong> el profesor lanza las preguntas. Responde antes de que termine el tiempo de cada ronda.</span>
            @endif
        </p>

        {{-- Ronda --}}
        <section class="mt-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6" aria-live="polite">

            {{-- Escenario de pregunta --}}
            <div id="question-stage" hidden>
                <div class="flex items-start justify-between gap-2">
                    <span id="turn-badge" tabindex="-1" class="inline-flex max-w-full items-center rounded-full bg-pink-700 px-3.5 py-1.5 text-sm font-bold text-white">
                        Preparando...
                    </span>
                </div>

                <div class="question-progress" role="progressbar" aria-label="Preguntas respondidas"
                    aria-valuemin="0" aria-valuemax="{{ $totalItems }}" aria-valuenow="{{ $answeredCount }}">
                    <span id="question-progress-fill"></span>
                </div>

                <div id="q-text" class="mt-3 flex min-h-28 items-center justify-center rounded-xl bg-pink-50 p-5 text-center text-lg font-bold leading-snug text-slate-900 ring-1 ring-pink-100 sm:min-h-32 sm:text-xl"></div>

                <div id="q-options" class="answer-grid mt-3"></div>

                <div id="round-answer-feedback" class="mt-3" aria-live="assertive"></div>

                <p id="round-question-note" class="mt-3 mb-0 text-center text-sm font-semibold text-slate-500"></p>
            </div>

            {{-- Pantalla de espera --}}
            <div id="wait-state" class="flex min-h-64 flex-col items-center justify-center py-6 text-center" aria-live="polite">
                <div class="grid h-16 w-16 place-items-center rounded-2xl bg-pink-50 text-2xl font-extrabold text-pink-700 ring-1 ring-pink-100" aria-hidden="true">?</div>
                <h2 id="wait-title" class="mt-3 text-xl font-bold text-slate-900">
                    Preparados para jugar
                </h2>
                <p id="wait-msg" class="mt-1.5 max-w-md text-sm text-slate-500">
                    Esperando a que el profesor lance la primera pregunta…
                </p>
                <p id="turn-hint" class="mt-2 text-sm font-semibold text-slate-400">
                    Cuando empiece la ronda, la pregunta aparecerá aquí.
                </p>
            </div>

        </section>

        {{-- Marcador de la sala --}}
        <section class="mt-3 rounded-2xl border border-slate-200 bg-white p-3.5 shadow-sm sm:p-4" aria-label="Marcador de la sala">
            <div class="flex items-center justify-between gap-2">
                <h2 class="text-base font-bold text-slate-900 sm:text-lg">
                    Marcador
                </h2>
                <p class="text-xs font-semibold text-slate-500">
                    Progreso: <strong class="font-bold text-pink-700"><span id="answered-count-side">{{ $answeredCount }}</span> / {{ $totalItems }}</strong>
                </p>
            </div>
            <ul id="players" class="mt-3 grid max-h-72 gap-1.5 overflow-y-auto pr-1">
                @foreach ($state['players'] as $p)
                    <li data-participation-id="{{ $p['participation_id'] }}"
                        class="flex items-center justify-between gap-2 rounded-xl border border-slate-100 bg-slate-50 px-3 py-2 text-sm">
                        <span class="min-w-0 flex-1 truncate text-slate-700">
                            @if ($p['participation_id'] === $participation->id)
                                <strong class="font-bold text-slate-900">{{ $p['name'] }}</strong>
                                <span class="font-semibold text-slate-400">(tú)</span>
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

    <script src="/js/klassio-sounds.js"></script>
    <script>
        window.KlassioSounds?.setup('kahoot');
        const sessionId = @json($session->id);
        let soundedItemId = null;
        const myParticipationId = @json($participation->id);
        const csrfToken = @json(csrf_token());
        const totalItems = @json($totalItems);
        const maxScore = @json($maxScore);
        const sharedTeamMode = @json($activity->mode === 'team');

        const stateUrl = @json(route('student.game-sessions.state', $session->id));
        const questionUrl = @json(route('student.kahoot.question-session'));
        const answerUrl = @json(route('student.kahoot.answer-session'));
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

        const turnBadge = document.getElementById('turn-badge');
        const turnHint = document.getElementById('turn-hint');
        const waitMsg = document.getElementById('wait-msg');
        const questionStage = document.getElementById('question-stage');
        const waitState = document.getElementById('wait-state');
        const waitTitle = document.getElementById('wait-title');

        function setQuestionVisible(isVisible) {
            questionStage.hidden = !isVisible;
            waitState.hidden = isVisible;
        }

        function redirectAfterFinalResult(delay = 0) {
            if (finishRedirectQueued) return;
            finishRedirectQueued = true;
            window.setTimeout(() => {
                window.location.href = resultUrl;
            }, delay);
        }

        function formatTime(s) {
            s = Math.max(0, s);
            const m = Math.floor(s / 60).toString().padStart(2, '0');
            const sec = (s % 60).toString().padStart(2, '0');
            return m + ':' + sec;
        }

        function tick() {
            const el = document.getElementById('timer');
            if (remainingSeconds !== null) {
                el.textContent = formatTime(remainingSeconds);
                if (remainingSeconds > 0) remainingSeconds--;
            } else {
                el.textContent = formatTime(elapsedSeconds);
                elapsedSeconds++;
            }

            const rt = document.getElementById('round-timer');
            const roundClock = document.getElementById('round-clock');
            if (roundRemaining === null) {
                rt.textContent = '--';
                roundClock.classList.remove('is-urgent');
            } else {
                rt.textContent = Math.max(0, roundRemaining) + 's';
                roundClock.classList.toggle('is-urgent', roundRemaining <= 5);
                if (roundRemaining > 0) roundRemaining--;
            }
        }

        function updateBadge(state) {
            if (state.status !== 'playing') return;

            if (state.question_id && state.answered_current) {
                turnBadge.textContent = sharedTeamMode ? 'Tu equipo ya respondió' : 'Ya respondiste';
                turnHint.textContent = 'Espera a que las demás participaciones respondan o termine el tiempo.';
                waitMsg.textContent = sharedTeamMode
                    ? 'Respuesta registrada para tu equipo. No necesitan responder otra vez.'
                    : 'Respuesta registrada. No necesitas responder otra vez.';
                turnHint.textContent = 'La ronda sigue activa para las demás participaciones.';
            } else if (state.question_id) {
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
                waitTitle.textContent = 'La pregunta está por aparecer';
            } else {
                turnBadge.textContent = 'Esperando al profesor...';
                turnHint.textContent = 'El profesor abrirá la siguiente pregunta.';
                waitMsg.textContent = 'Esperando a que el profesor lance la pregunta…';
                waitTitle.textContent = 'Preparados para jugar';
            }
        }

        function resetQuestion() {
            document.getElementById('q-text').textContent = '';
            document.getElementById('q-options').replaceChildren();
            document.getElementById('round-answer-feedback').replaceChildren();
            currentItem = null;
            questionOpen = false;
            setQuestionVisible(false);
        }

        function maybeAutoOpen(state) {
            if (questionOpen || answering || fetching) return;
            if (state.status !== 'playing') return;
            if (state.question_id && !state.answered_current) {
                fetchQuestion();
            }
        }

        function renderState(state) {
            const rows = document.querySelectorAll('#players li');
            const sortedPlayers = [...state.players].sort((first, second) =>
                (Number(second.score) || 0) - (Number(first.score) || 0)
            );

            sortedPlayers.forEach((player) => {
                const li = Array.from(rows).find((row) =>
                    Number(row.dataset.participationId) === Number(player.participation_id)
                );

                if (!li) return;

                document.getElementById('players').appendChild(li);
                li.querySelector('[data-role="score"]').textContent = player.score + ' / ' + maxScore;
            });

            const me = state.players.find((p) => p.participation_id === myParticipationId);
            if (me) {
                document.getElementById('answered-count').textContent = me.answered;
                document.getElementById('answered-count-side').textContent = me.answered;
                document.getElementById('question-progress-fill').style.width = totalItems > 0
                    ? Math.min(100, (me.answered / totalItems) * 100) + '%'
                    : '0%';
                document.querySelector('.question-progress').setAttribute('aria-valuenow', me.answered);
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

            if (questionOpen && !answering && me?.answered_current
                && (me.answer_correct_current === null || me.answer_correct_current === undefined)) {
                resetQuestion();
            }

            if (questionOpen && !answering && currentItem && state.question_id !== currentItem.id) {
                resetQuestion();

                if (!answeredThisRound) {
                    turnBadge.textContent = 'Se acabó el tiempo';
                    turnHint.textContent = 'No respondiste a tiempo; la pregunta quedó fallada.';
                    if (waitMsg) {
                        waitMsg.classList.remove('d-none');
                        waitMsg.textContent = 'No respondiste a tiempo. Espera a que el profesor avance.';
                    }
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

        async function fetchQuestion() {
            if (fetching || questionOpen) return;
            fetching = true;

            try {
                const res = await fetch(questionUrl, {
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
            document.getElementById('round-question-note').textContent = sharedTeamMode
                ? canAnswer
                    ? 'Te toca elegir la respuesta. Escucha las ideas del equipo.'
                    : `Le toca responder a ${responderName || 'otro integrante'}. ¡Ayúdenle entre todos!`
                : 'Tu participación cuenta con una respuesta en esta ronda.';

            item.options.forEach((opt) => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'opt-btn';
                btn.textContent = opt.text;
                btn.disabled = !canAnswer;
                btn.dataset.optionId = opt.id;
                btn.addEventListener('click', () => answer(opt.id, btn));
                options.appendChild(btn);
            });

            answeredThisRound = false;
            questionOpen = true;
            setQuestionVisible(true);
        }

        function showRoundResult(isCorrect, score, correctText = null) {
            if (!currentItem) return;

            const itemId = currentItem.id;

            if (soundedItemId !== itemId) {
                soundedItemId = itemId;
                window.KlassioSounds?.[isCorrect ? 'correcto' : 'error']();
            }
            const feedback = document.getElementById('round-answer-feedback');
            const result = document.createElement('div');
            result.className = 'alert ' + (isCorrect ? 'alert-success' : 'alert-danger') + ' fw-bold mb-2';
            result.textContent = isCorrect ? '¡Correcto!' : 'Respuesta incorrecta';
            const children = [result];

            if (correctText) {
                const correct = document.createElement('p');
                correct.className = 'small mb-2';
                correct.textContent = 'Respuesta correcta: ' + correctText;
                children.push(correct);
            }

            const scoreLine = document.createElement('p');
            scoreLine.className = 'small mb-0';
            scoreLine.textContent = (sharedTeamMode ? 'Puntaje del equipo: ' : 'Tu puntaje: ')
                + score + ' / ' + maxScore;
            children.push(scoreLine);
            feedback.replaceChildren(...children);
            document.getElementById('round-question-note').textContent = sharedTeamMode
                ? 'La respuesta ya quedó registrada para todo el equipo.'
                : 'Tu respuesta quedó registrada.';
            Array.from(document.querySelectorAll('.opt-btn')).forEach((option) => {
                option.disabled = true;
            });
            resultDisplayedForItemId = itemId;

            window.setTimeout(() => {
                if (currentItem?.id !== itemId) return;

                answering = false;
                refreshState();
            }, 1800);
        }

        function revealTeamResult(playerState, gameState) {
            const itemIsActive = gameState.question_id !== null;
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

            showRoundResult(isCorrect, playerState.score);
        }

        function hasVisibleRoundResult(playerState, gameState) {
            if (gameState.question_id !== null) {
                return Boolean(playerState?.answered_current && gameState.current_item
                    && playerState.answer_correct_current !== null
                    && playerState.answer_correct_current !== undefined);
            }

            return Boolean(playerState?.last_round_answered && gameState.last_round_item
                && playerState.last_round_answer_correct !== null
                && playerState.last_round_answer_correct !== undefined);
        }

        async function answer(optionId, btn) {
            if (answering || !currentItem) return;
            window.KlassioSounds?.click();
            answering = true;

            Array.from(document.querySelectorAll('.opt-btn')).forEach((b) => b.disabled = true);
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
                        question_id: currentItem.id,
                        option_id: optionId
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
                    Array.from(document.querySelectorAll('.opt-btn')).forEach((b) => b.disabled = false);
                    btn.classList.remove('disabled');
                    answering = false;
                    refreshState();
                    return;
                }

                const correctText = (() => {
                    if (data.is_correct) return null;
                    const found = (currentItem.options || []).find((o) => o.id === data.correct_option_id);
                    return found ? found.text : null;
                })();

                answeredThisRound = true;
                showRoundResult(data.is_correct, data.participation_score, correctText);

                if (data.finished) {
                    window.KlassioSounds?.completado();
                    redirectAfterFinalResult(2200);
                }
            } catch (e) {
                answering = false;
                alert('Error de conexión. Intenta de nuevo.');
            }
        }

        window.addEventListener('DOMContentLoaded', () => {
            tick();
          

            const reverbEnabled = @json((bool) config('broadcasting.connections.reverb.enabled', false));

            if (window.Echo && reverbEnabled) {
                window.Echo.private('game-session.' + sessionId)
                    .listen('.RoundStarted', () => refreshState())
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

</body>

</html>
