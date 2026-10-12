<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sala {{ $session->code }} — Profesor</title>
    @include('partials.assets', ['theme' => 'teacher'])
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
</head>

<body>

    @php($isRace = in_array($session->activity->type, ['crossword', 'word_search', 'matching'], true))

    <div class="page-shell">

        <div class="page-hero text-center">
            <h1 class="fw-bold mb-1">Sala de juego</h1>
            <p class="mb-0 opacity-75">{{ $session->activity->title }}</p>
        </div>

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card surface-card mb-4">
            <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <div class="text-muted small">Código de la sala — compártelo con tus alumnos</div>
                    <div class="fw-bold" style="font-size: 2rem; letter-spacing: .35em;" id="room-code">
                        {{ $session->code }}
                    </div>
                </div>
                <div>
                    <span class="badge text-bg-secondary text-uppercase" id="room-status">
                        {{ $session->status }}
                    </span>
                    <div class="text-muted small mt-1" id="room-count">
                        {{ $players->count() }} / {{ $session->max_players }} jugadores
                    </div>
                </div>
            </div>
        </div>

        <div class="card surface-card mb-4">
            <div class="card-body p-4">
                <h2 class="h5 fw-bold mb-3">Jugadores</h2>
                <ul class="list-group list-group-flush" id="players">
                    @foreach ($players as $p)
                        <li
                            class="list-group-item d-flex align-items-center justify-content-between"
                            data-participation-id="{{ $p->id }}"
                        >
                            <span>{{ $p->team?->name ?? $p->student?->name }}</span>
                            <span class="text-success small">En la sala</span>
                        </li>
                    @endforeach
                </ul>
                <div id="empty-players" class="text-muted {{ $players->isEmpty() ? '' : 'd-none' }}">
                    Aún no hay jugadores en la sala.
                </div>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2">
            @if ($session->status === 'waiting')
                <form method="POST" action="{{ route('teacher.game-sessions.start', $session->id) }}" class="w-100 mb-2">
                    @csrf
                    <button
                        type="submit"
                        class="btn btn-klassio w-100"
                        id="start-btn"
                        @disabled(! $canStart)
                    >
                        Iniciar partida
                    </button>
                </form>

                @if (! $canStart)
                    <p class="text-muted small w-100 mb-0">
                        Se necesitan al menos 2 jugadores para iniciar la partida.
                    </p>
                @endif
            @endif

            @if ($session->status === 'playing')
                @php($isKahoot = $session->activity->type === 'kahoot')

                @if ($isRace)
                    <div class="card surface-card w-100 mb-2" id="race-panel">
                        <div class="card-body p-4">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                                <h2 class="h5 fw-bold mb-0">Carrera libre en curso</h2>
                                <span class="badge text-bg-light">
                                    Tiempo: <span id="race-timer">--</span>
                                </span>
                            </div>

                            <p class="text-muted">
                                Cada jugador resuelve el juego a su ritmo. La partida termina cuando
                                todos terminan o se agota el tiempo de la actividad.
                            </p>

                            <h3 class="h6 fw-bold">Posiciones</h3>
                            <ul class="list-group list-group-flush" id="race-standings"></ul>
                        </div>
                    </div>
                @else
                    <div class="card surface-card w-100 mb-2" id="round-panel">
                        <div class="card-body p-4">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                                <h2 class="h5 fw-bold mb-0">
                                    {{ $isKahoot ? 'Control del kahoot' : 'Control de la ruleta' }}
                                </h2>
                                <span class="badge text-bg-light">
                                    Tiempo: <span id="round-timer">--</span>
                                </span>
                            </div>

                            <div id="round-question" class="mb-3">
                                <p class="text-muted mb-0">
                                    {{ $isKahoot
                                        ? 'Avanza para mostrar la siguiente pregunta.'
                                        : 'Gira la ruleta para mostrar la siguiente pregunta.' }}
                                </p>
                            </div>

                            <div class="d-flex flex-wrap align-items-end gap-3">
                                <div>
                                    <label class="form-label small mb-1" for="round-seconds">Segundos para responder</label>
                                    <input
                                        type="number"
                                        class="form-control"
                                        id="round-seconds"
                                        min="5"
                                        max="300"
                                        step="5"
                                        value="30"
                                        style="width: 8rem;"
                                    >
                                </div>
                                <div>
                                    <button type="button" class="btn btn-klassio" id="spin-btn">
                                        {{ $isKahoot ? 'Siguiente pregunta' : 'Girar la ruleta' }}
                                    </button>
                                </div>
                                <div class="text-muted small">
                                    Respondieron: <span id="round-answered">0</span> / <span id="round-total">{{ $players->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            @if ($session->status === 'finished')
                <a href="{{ route('teacher.game-sessions.result', $session->id) }}" class="btn btn-klassio w-100">
                    Ver resultados
                </a>
            @endif
        </div>

        <div class="mt-4">
            <a href="{{ route('teacher.dashboard') }}" class="btn btn-outline-secondary">Volver al panel</a>
        </div>

    </div>

    <script>
        const sessionId = @json($session->id);
        const stateUrl = @json(route('teacher.game-sessions.state', $session->id));
        const resultUrl = @json(route('teacher.game-sessions.result', $session->id));
        const spinUrl = @json(route('teacher.game-sessions.spin', $session->id));
        const csrfToken = @json(csrf_token());

        const isKahoot = @json($session->activity->type === 'kahoot');
        const isRace = @json($isRace);
        const idleButton = isKahoot ? 'Siguiente pregunta' : 'Girar la ruleta';
        const idleHint = isKahoot
            ? 'Avanza para mostrar la siguiente pregunta.'
            : 'Gira la ruleta para mostrar la siguiente pregunta.';

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
            roundRemaining = typeof state.round_time_remaining === 'number'
                ? state.round_time_remaining
                : null;

            totalEl.textContent = state.players.filter(
                (p) => p.status === 'started'
            ).length;

            const answered = state.players.filter((p) => p.answered_current).length;
            answeredEl.textContent = answered;

            if (roundOpen && state.current_item) {
                const question = document.createElement('p');
                question.className = 'fw-semibold mb-2';
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
                hint.className = 'text-muted mb-0';
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

            const list = document.getElementById('players');
            list.replaceChildren();
            state.players.forEach((p) => {
                if (p.status === 'abandoned' || p.status === 'expired') return;

                const li = document.createElement('li');
                li.className = 'list-group-item d-flex align-items-center justify-content-between';

                const name = document.createElement('span');
                name.textContent = p.name + (p.score ? ' — ' + p.score + ' pts' : '');
                li.appendChild(name);

                const badge = document.createElement('span');
                if ((state.roulette_item_id || state.question_id) && p.answered_current) {
                    badge.className = 'badge text-bg-success';
                    badge.textContent = 'Respondió';
                } else {
                    badge.className = 'text-success small';
                    badge.textContent = 'En la sala';
                }
                li.appendChild(badge);

                list.appendChild(li);
            });

            document.getElementById('empty-players').classList.toggle('d-none', state.players.length > 0);

            if (isRace) {
                raceRemaining = typeof state.time_remaining === 'number'
                    ? state.time_remaining
                    : null;

                const standings = document.getElementById('race-standings');
                if (standings) {
                    const entries = state.players
                        .filter((p) => p.status !== 'abandoned')
                        .slice()
                        .sort((a, b) => b.score - a.score)
                        .map((p) => {
                            const suffix = p.status === 'completed' ? ' ✓' : '';
                            const item = document.createElement('li');
                            item.className = 'list-group-item d-flex justify-content-between align-items-center';
                            const name = document.createElement('span');
                            name.textContent = p.name;
                            const score = document.createElement('span');
                            score.className = 'badge text-bg-light';
                            score.textContent = p.score + ' pts' + suffix;
                            item.append(name, score);
                            return item;
                        });
                    standings.replaceChildren(...entries);
                }
            }

            renderRound(state);
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
                    body: JSON.stringify({ seconds: seconds })
                });
                const data = await res.json();
                if (!res.ok) {
                    alert(data.error || 'No se pudo girar la ruleta.');
                } else if (data.state) {
                    renderState(data.state);
                    if (data.finished) {
                        window.location.href = resultUrl;
                        return;
                    }
                }
            } catch (e) {
                alert('Error de conexión.');
            }
        }

        async function refresh() {
            try {
                const res = await fetch(stateUrl, {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin'
                });
                if (!res.ok) return;
                const state = await res.json();

                renderState(state);

                if (state.status === 'finished') {
                    window.location.href = resultUrl;
                }
            } catch (e) {
                // sin red por ahora
            }
        }

        setInterval(refresh, 2000);
        setInterval(tick, 1000);

        window.addEventListener('DOMContentLoaded', () => {
            tick();
            refresh();

            const spinBtn = document.getElementById('spin-btn');
            if (spinBtn) {
                spinBtn.addEventListener('click', spin);
            }

            const reverbEnabled = @json((bool) config('broadcasting.connections.reverb.enabled', false));
            if (window.Echo && reverbEnabled) {
                window.Echo.private('game-session.' + sessionId)
                    .listen('.GameFinished', (e) => { window.location.href = resultUrl; })
                    .listen('.PlayerJoined', (e) => refresh())
                    .listen('.PlayerLeft', (e) => refresh())
                    .listen('.RoundStarted', (e) => refresh())
                    .listen('.RoundClosed', (e) => refresh())
                    .listen('.ScoreUpdated', (e) => refresh())
                    .listen('.TurnChanged', (e) => refresh());
            }
        });
    </script>

</body>

</html>
