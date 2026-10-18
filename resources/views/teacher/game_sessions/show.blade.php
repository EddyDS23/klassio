@extends('teacher.layouts.session')

@section('title', 'Sala {{ $session->code }} — Profesor')

@section('head')
    <style>
        .teacher-session { max-width: 1120px; margin-inline: auto; }
        .teacher-session .page-hero { margin-bottom: 1.4rem; }
        .teacher-room, .teacher-roster, .teacher-control { overflow: hidden; border: 1px solid #d7e8e4; border-radius: 20px; box-shadow: 0 14px 38px rgb(23 69 60 / .08); }
        .room-code { display: inline-flex; flex-wrap: wrap; align-items: center; gap: .7rem; }
        .room-code-value { color: #075e58; font-size: clamp(1.55rem, 5vw, 2.1rem); letter-spacing: .22em !important; }
        .copy-room-code { min-height: 40px; border-radius: 10px; font-weight: 700; }
        .room-code-feedback { min-height: 1.25rem; color: #287243; font-size: .82rem; }
        .session-meta { display: flex; flex-wrap: wrap; align-items: center; gap: .65rem; }
        .room-status { padding: .5rem .7rem; border-radius: 999px; background: #e6f5eb; color: #287243; font-weight: 750; }
        .room-count { color: #45635d; font-weight: 750; }
        .roster-list .player-row { min-height: 54px; padding-block: .8rem; }
        .control-intro { padding: .9rem 1rem; border: 1px solid #c9e8d9; border-radius: 13px; background: #effbf5; color: #365f54; }
        .control-actions { display: flex; flex-wrap: wrap; align-items: end; gap: 1rem; }
        .round-seconds-input { min-height: 44px; border-radius: 10px; }
        .race-list { max-height: 360px; overflow-y: auto; }
        .race-list .player-row { padding-block: .8rem; }
        @media (max-width: 575px) { .teacher-session .page-hero { text-align: left !important; } .room-layout { align-items: flex-start !important; } .room-code { align-items: flex-start; flex-direction: column; } }
    </style>
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
@endsection

@section('content')
    @php($isRace = in_array($session->activity->type, ['crossword', 'word_search', 'matching'], true))

    <div class="page-shell teacher-session">

        <div class="page-hero">
            <p class="text-sm uppercase font-bold mb-2">{{ $session->status === 'waiting' ? 'Preparación de partida' : 'Sesión multijugador' }}</p>
            <h1 class="font-bold mb-1">{{ $session->status === 'waiting' ? 'Tu sala está lista' : 'Panel de la partida' }}</h1>
            <p class="mb-0 opacity-75">{{ $session->activity->title }}</p>
        </div>

        @if (session('error'))
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4 text-red-800">{{ session('error') }}</div>
        @endif

        <div class="surface-card teacher-room mb-4">
            <div class="room-layout flex flex-wrap items-center justify-between gap-3 p-6">
                <div>
                    <div class="text-slate-500 text-sm">Código de la sala — compártelo con tus alumnos</div>
                    <div class="room-code">
                    <div class="font-bold room-code-value" id="room-code">
                        {{ $session->code }}
                    </div>
                    @if ($session->status === 'waiting')
                        <button type="button" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 font-semibold text-slate-700 transition hover:bg-slate-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-cyan-700 copy-room-code" id="copy-room-code">Copiar código</button>
                    @endif
                    </div>
                    <div id="room-code-feedback" class="room-code-feedback" role="status" aria-live="polite"></div>
                </div>
                <div class="session-meta">
                    <span class="room-status rounded-full px-3 py-1 text-xs font-bold uppercase" id="room-status">
                        {{ $session->status }}
                    </span>
                    <div class="room-count" id="room-count">
                        {{ $players->count() }} / {{ $session->max_players }} jugadores
                    </div>
                </div>
            </div>
        </div>

        <div class="surface-card teacher-roster mb-4">
            <div class="p-6">
                <h2 class="text-lg font-bold mb-1">Participantes</h2>
                <p class="text-slate-500 mb-3">La lista se actualiza automáticamente cuando alguien entra o sale.</p>
                <ul class="divide-y divide-slate-100 roster-list" id="players">
                    @foreach ($players as $p)
                        <li class="player-row flex items-center justify-between border-b border-slate-100 last:border-0"
                            data-participation-id="{{ $p->id }}">
                            <span>{{ $p->team?->name ?? $p->student?->name }}</span>
                            <span class="text-emerald-700 text-sm">En la sala</span>
                        </li>
                    @endforeach
                </ul>
                <div id="empty-players" class="rounded-xl bg-slate-50 p-3 text-slate-500 {{ $players->isEmpty() ? '' : 'hidden' }}" role="status">
                    Comparte el código de sala para que tus estudiantes puedan unirse.
                </div>
            </div>
        </div>

        <div class="flex flex-wrap gap-2">
            @if ($session->status === 'waiting')
                <form method="POST" action="{{ route('teacher.game-sessions.start', $session->id) }}"
                    class="w-full mb-2">
                    @csrf
                            <button type="submit" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-violet-600 px-5 py-3 font-bold text-white shadow-sm transition hover:bg-violet-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-violet-700 disabled:cursor-not-allowed disabled:opacity-50" id="start-btn" @disabled(!$canStart)>
                        Comenzar partida
                    </button>
                </form>

                <p id="start-hint" class="text-slate-500 text-sm w-full mb-0 {{ $canStart ? 'hidden' : '' }}">
                    Se necesitan al menos 2 participantes activos para comenzar.
                </p>

                @if ($players->isEmpty())
                    <form method="POST" action="{{ route('teacher.game-sessions.cancel', $session->id) }}"
                        class="w-full" onsubmit="return confirm('¿Cerrar esta sala sin iniciar? Ya no aparecerá entre las partidas activas.');">
                        @csrf
                        <p class="mb-2 text-sm text-slate-500">Si no vas a usar esta sala, ciérrala para quitarla de tus partidas activas.</p>
                        <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-rose-300 bg-white px-5 py-3 font-bold text-rose-700 transition hover:bg-rose-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-rose-700">
                            Cerrar sala sin iniciar
                        </button>
                    </form>
                @endif
            @endif

            @if ($session->status === 'playing')
                @php($isKahoot = $session->activity->type === 'kahoot')

                @if ($isRace)
                    <div class="surface-card teacher-control w-full mb-2" id="race-panel">
                        <div class="p-6">
                            <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                                    <h2 class="text-lg font-bold mb-0">Progreso de la partida</h2>
                                <span class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-sm text-slate-700">
                                    Tiempo: <span id="race-timer">--</span>
                                </span>
                            </div>

                                <p class="control-intro">
                                Cada participante avanza a su ritmo. Aquí puedes seguir posiciones y tiempo restante; la sesión termina cuando todos completan o se agota el tiempo.
                            </p>

                            <h3 class="text-base font-bold">Posiciones</h3>
                            <ul class="divide-y divide-slate-100 race-list" id="race-standings"></ul>
                        </div>
                    </div>
                @else
                    <div class="surface-card teacher-control w-full mb-2" id="round-panel">
                        <div class="p-6">
                            <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                                <h2 class="text-lg font-bold mb-0">
                                    {{ $isKahoot ? 'Control del kahoot' : 'Control de la ruleta' }}
                                </h2>
                                <span class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-sm text-slate-700">
                                    Tiempo: <span id="round-timer">--</span>
                                </span>
                            </div>

                            <div id="round-question" class="mb-3">
                                <p class="text-slate-500 mb-0">
                                    {{ $isKahoot
                                        ? 'Avanza para mostrar la siguiente pregunta.'
                                        : 'Gira la ruleta para mostrar la siguiente pregunta.' }}
                                </p>
                            </div>

                            <div class="control-actions">
                                <div>
                                    <label class="mb-1 block text-sm" for="round-seconds">Segundos para
                                        responder</label>
                                    <input type="number" class="round-seconds-input w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-slate-900 shadow-sm focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-100" id="round-seconds" min="5"
                                        max="300" step="5" value="30" style="width: 8rem;">
                                </div>
                                <div>
                                    <button type="button" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-cyan-600 px-5 py-3 font-bold text-white shadow-sm transition hover:bg-cyan-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-cyan-700 disabled:cursor-not-allowed disabled:opacity-50" id="spin-btn">
                                        {{ $isKahoot ? 'Siguiente pregunta' : 'Girar la ruleta' }}
                                    </button>
                                </div>
                                <div class="text-slate-500 text-sm">
                                    Respondieron: <span id="round-answered">0</span> / <span
                                        id="round-total">{{ $players->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            @if ($session->status === 'finished')
                <a href="{{ route('teacher.game-sessions.result', $session->id) }}" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-cyan-600 px-5 py-3 font-bold text-white shadow-sm transition hover:bg-cyan-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-cyan-700 disabled:cursor-not-allowed disabled:opacity-50">
                    Ver resultados
                </a>
            @endif
        </div>

        <div class="mt-4">
            <a href="{{ route('teacher.dashboard') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 font-semibold text-slate-700 transition hover:bg-slate-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-cyan-700">Volver al panel</a>
        </div>

    </div>

    <script>
        const sessionId = @json($session->id);
        const stateUrl = @json(route('teacher.game-sessions.state', $session->id));
        const resultUrl = @json(route('teacher.game-sessions.result', $session->id));
        const spinUrl = @json(route('teacher.game-sessions.spin', $session->id));
        const csrfToken = @json(csrf_token());

        const copyRoomCodeButton = document.getElementById('copy-room-code');
        if (copyRoomCodeButton) {
            copyRoomCodeButton.addEventListener('click', async () => {
                const feedback = document.getElementById('room-code-feedback');
                try {
                    await navigator.clipboard.writeText(document.getElementById('room-code').textContent.trim());
                    feedback.textContent = 'Código copiado. Ya puedes compartirlo con el grupo.';
                } catch (error) {
                    feedback.textContent = 'No se pudo copiar automáticamente. Selecciona el código para compartirlo.';
                }
            });
        }

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
                question.className = 'font-semibold mb-2';
                question.textContent = state.current_item.question;

                const options = document.createElement('ul');
                options.className = 'mb-0';
                state.current_item.options.forEach((option, index) => {
                    const item = document.createElement('li');
                    const label = document.createElement('strong');
                    label.textContent = (option.key ? option.key.toUpperCase() : index + 1) + '. ';
                    item.append(label, document.createTextNode(option.text));
                    options.appendChild(item);
                });

                questionEl.replaceChildren(question, options);

                spinBtn.disabled = true;
                spinBtn.textContent = 'Ronda en curso';
            } else {
                const hint = document.createElement('p');
                hint.className = 'text-slate-500 mb-0';
                hint.textContent = idleHint;
                questionEl.replaceChildren(hint);
                spinBtn.disabled = false;
                spinBtn.textContent = idleButton;
            }
        }

        function renderState(state) {
            const statusEl = document.getElementById('room-status');
            statusEl.textContent = statusLabel[state.status] || state.status;

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
            list.replaceChildren();
            state.players.forEach((p) => {
                if (p.status === 'abandoned' || p.status === 'expired') return;

                const li = document.createElement('li');
                li.className = 'player-row flex items-center justify-between';

                const name = document.createElement('span');
                name.textContent = p.name + (p.score ? ' — ' + p.score + ' pts' : '');
                li.appendChild(name);

                const badge = document.createElement('span');
                if ((state.roulette_item_id || state.question_id) && p.answered_current) {
                    badge.className = 'rounded-full bg-emerald-100 px-2 py-1 text-sm font-semibold text-emerald-800';
                    badge.textContent = 'Respondió';
                } else {
                    badge.className = 'text-emerald-700 text-sm';
                    badge.textContent = 'En la sala';
                }
                li.appendChild(badge);

                list.appendChild(li);
            });

            document.getElementById('empty-players').classList.toggle('hidden', state.players.length > 0);

            if (isRace) {
                raceRemaining = typeof state.time_remaining === 'number' ?
                    state.time_remaining :
                    null;

                const standings = document.getElementById('race-standings');
                if (standings) {
                    const entries = state.players
                        .filter((p) => p.status !== 'abandoned')
                        .slice()
                        .sort((a, b) => b.score - a.score)
                        .map((p) => {
                            const suffix = p.status === 'completed' ? ' ✓' : '';
                            const item = document.createElement('li');
                            item.className = 'player-row flex justify-between items-center';
                            const name = document.createElement('span');
                            name.textContent = p.name;
                            const score = document.createElement('span');
                            score.className = 'rounded-full border border-slate-200 bg-slate-50 px-2 py-1 text-sm text-slate-700';
                            score.textContent = p.score + ' pts' + suffix;
                            item.append(name, score);
                            return item;
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
            if (!spinBtn || spinBtn.disabled) return;

            spinBtn.disabled = true;
            spinBtn.textContent = 'Girando...';

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
                    window.alert(data.error || 'No se pudo girar la ruleta.');
                } else if (data.state) {
                    renderState(data.state);
                    if (data.finished) {
                        navigateToResultsOnce();
                        return;
                    }
                }
            } catch (e) {
                window.alert('Error de conexión.');
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
    </script>
@endsection
