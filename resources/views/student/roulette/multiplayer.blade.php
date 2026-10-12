<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ruleta — Sala {{ $session->code }}</title>
    @include('partials.assets', ['theme' => 'student'])
    @if ((bool) config('broadcasting.connections.reverb.enabled', false))
        <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
        <script>
            window.Echo = new Echo({
                broadcaster: 'reverb',
                key: @json(config('broadcasting.connections.reverb.key')),
                wsHost: @json(config('broadcasting.connections.reverb.options.host', 'localhost')),
                wsPort: @json(config('broadcasting.connections.reverb.options.port', 8080)),
                forceTLS: @json(config('broadcasting.connections.reverb.options.scheme', 'http') === 'https'),
                enabledTransports: ['ws', 'wss']
            });
        </script>
    @endif
    <style>
        .wheel-wrap { display: grid; place-items: center; padding: 1rem 0; }
        .wheel {
            width: 220px; height: 220px; border-radius: 50%;
            background: conic-gradient(#0891b2 0 25%, #2563eb 0 50%, #7c3aed 0 75%, #f59e0b 0 100%);
            display: grid; place-items: center; position: relative;
            box-shadow: 0 1rem 2.5rem rgb(15 23 42 / .25);
            transition: transform 1.2s cubic-bezier(.2, .8, .3, 1);
        }
        .wheel::after {
            content: ''; position: absolute; inset: 22%;
            background: #fff; border-radius: 50%;
            box-shadow: inset 0 0 0 .35rem #e2e8f0;
        }
        .wheel-center { position: relative; z-index: 2; font-weight: 800; font-size: 1.2rem; text-align: center; color: #0f172a; }
        .wheel.spinning { transform: rotate(1080deg); }
        .pointer { position: relative; z-index: 3; text-align: center; font-size: 2rem; line-height: 0; margin: -.6rem 0; }
        .turn-badge { background: var(--klassio-primary); color: #fff; border-radius: 1rem; padding: .5rem 1rem; font-weight: 700; }
        @keyframes pulse { 50% { transform: scale(1.04); } }
        .my-turn { animation: pulse 1.2s ease-in-out infinite; }
        .opt-btn { border: 2px solid #cbd5e1; border-radius: 1rem; padding: 1rem; font-weight: 700; background: #fff; width: 100%; }
        .opt-btn:hover { border-color: var(--klassio-primary); color: var(--klassio-primary); }
        html[data-theme="dark"] .opt-btn { background: #0f172a; color: #f1f5f9; border-color: #475569; }
    </style>
</head>

<body>

    <div class="page-shell">

        <div class="page-hero text-center">
            <h1 class="fw-bold mb-1">Ruleta multijugador</h1>
            <p class="mb-0 opacity-75">
                {{ $activity->title }} — Sala <strong>{{ $session->code }}</strong>
            </p>
        </div>

        <div class="text-center my-2">
            <span class="turn-badge" id="turn-badge">Preparando ronda...</span>
            <span class="badge text-bg-light ms-2">
                Ronda: <span id="round-timer">--</span>
            </span>
            <span class="badge text-bg-light ms-2">
                Tiempo: <span id="timer">--:--</span>
            </span>
        </div>

        <div class="row g-4">

            <div class="col-lg-7">
                <div class="card surface-card">
                    <div class="card-body p-4 text-center">

                        <div class="pointer">▼</div>

                        <div class="wheel-wrap">
                            <div class="wheel" id="wheel">
                                <div class="wheel-center">
                                    <div>RULETA</div>
                                    <small>sala {{ $session->code }}</small>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="alert alert-info mb-0" id="wait-msg">
                                Esperando a que el profesor gire la ruleta…
                            </div>
                            <div class="text-muted small mt-2" id="turn-hint">
                                Cuando el profesor gire, verás la pregunta y tendrás tiempo para responder.
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card surface-card">
                    <div class="card-body p-4">
                        <h2 class="h5 fw-bold mb-3">Marcador</h2>
                        <span class="text-muted small">
                            Respondidas: <span id="answered-count">{{ $answeredCount }}</span> / {{ $totalItems }}
                        </span>
                        <ul class="list-group list-group-flush mt-2" id="players">
                            @foreach ($state['players'] as $p)
                                <li
                                    class="list-group-item d-flex justify-content-between align-items-center"
                                    data-participation-id="{{ $p['participation_id'] }}"
                                >
                                    <span>
                                        @if ($p['participation_id'] === $participation->id)
                                            <span class="text-muted">(tú)</span>
                                        @endif
                                        {{ $p['name'] }}
                                    </span>
                                    <span class="fw-bold" data-role="score">{{ $p['score'] }} / {{ $maxScore }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="card surface-card mt-4">
                    <div class="card-body p-4">
                        <h2 class="h5 fw-bold mb-3">Acciones</h2>
                        <form method="POST" action="{{ route('student.game-sessions.leave', $session->id) }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary w-100">Abandonar partida</button>
                        </form>
                    </div>
                </div>
            </div>

        </div>

    </div>

    {{-- Modal de pregunta --}}
    <div class="modal fade" id="questionModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pregunta</h5>
                </div>
                <div class="modal-body">
                    <p class="fw-semibold fs-5" id="q-text"></p>
                    <div class="d-grid gap-2" id="q-options"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const sessionId = @json($session->id);
        const myParticipationId = @json($participation->id);
        const csrfToken = @json(csrf_token());
        const totalItems = @json($totalItems);
        const maxScore = @json($maxScore);

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

        const wheel = document.getElementById('wheel');
        const turnBadge = document.getElementById('turn-badge');
        const turnHint = document.getElementById('turn-hint');
        const waitMsg = document.getElementById('wait-msg');
        const questionModal = new bootstrap.Modal(document.getElementById('questionModal'));

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
                turnBadge.textContent = 'Esperando a los demás...';
                turnHint.textContent = 'Ya respondiste esta ronda. Espera a que el profesor gire de nuevo.';
                waitMsg.classList.remove('d-none');
                waitMsg.textContent = 'Respuesta enviada. Esperando a los demás o al final del tiempo.';
            } else if (state.roulette_item_id) {
                turnBadge.textContent = '¡Responde la pregunta!';
                turnHint.textContent = 'Tienes tiempo limitado para responder.';
                waitMsg.classList.add('d-none');
            } else {
                turnBadge.textContent = 'Esperando al profesor...';
                turnHint.textContent = 'El profesor girará la ruleta para la siguiente pregunta.';
                waitMsg.classList.remove('d-none');
                waitMsg.textContent = 'Esperando a que el profesor gire la ruleta…';
            }
        }

        function resetQuestion() {
            const body = document.querySelector('#questionModal .modal-body');
            body.innerHTML =
                '<p class="fw-semibold fs-5" id="q-text"></p>' +
                '<div class="d-grid gap-2" id="q-options"></div>';
            currentItem = null;
            questionOpen = false;
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

            rows.forEach((li) => {
                const id = Number(li.dataset.participationId);
                const p = map[id];
                if (p) {
                    li.querySelector('[data-role="score"]').textContent = p.score + ' / ' + maxScore;
                }
            });

            const me = state.players.find((p) => p.participation_id === myParticipationId);
            if (me) {
                document.getElementById('answered-count').textContent = me.answered;
            }

            if (state.status === 'finished') {
                window.location.href = resultUrl;
                return;
            }

            roundRemaining = typeof state.round_time_remaining === 'number'
                ? state.round_time_remaining
                : null;

            if (state.time_remaining !== null) {
                remainingSeconds = state.time_remaining;
            } else if (typeof state.time_elapsed === 'number') {
                remainingSeconds = null;
                elapsedSeconds = state.time_elapsed;
            }

            updateBadge(state);

            if (questionOpen && !answering && currentItem && state.roulette_item_id !== currentItem.id) {
                questionModal.hide();
                resetQuestion();

                if (!answeredThisRound) {
                    turnBadge.textContent = 'Se acabó el tiempo';
                    turnHint.textContent = 'No respondiste a tiempo; la pregunta quedó fallada.';
                    if (waitMsg) {
                        waitMsg.classList.remove('d-none');
                        waitMsg.textContent = 'No respondiste a tiempo. Espera a que el profesor gire de nuevo.';
                    }
                }
            }

            maybeAutoOpen(state);
        }

        async function refreshState() {
            try {
                const res = await fetch(stateUrl, {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin'
                });
                if (res.ok) renderState(await res.json());
            } catch (e) {}
        }

        setInterval(refreshState, 1500);
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
                    body: JSON.stringify({ session_id: sessionId })
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
                    showQuestion(data.item);
                }
            } catch (e) {
                // Silencioso: el polling reintenta.
            } finally {
                fetching = false;
            }
        }

        function showQuestion(item) {
            document.getElementById('q-text').textContent = item.question;

            const options = document.getElementById('q-options');
            options.innerHTML = '';

            item.options.forEach((opt) => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'opt-btn';
                btn.innerHTML = '<span class="badge me-2" style="background: var(--klassio-primary);">' +
                    opt.key.toUpperCase() + '</span>' + opt.text;
                btn.dataset.key = opt.key;
                btn.addEventListener('click', () => answer(btn.dataset.key, btn));
                options.appendChild(btn);
            });

            answeredThisRound = false;
            questionOpen = true;
            questionModal.show();
        }

        async function answer(key, btn) {
            if (answering || !currentItem) return;
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
                        roulette_item_id: currentItem.id,
                        response: key
                    })
                });

                const data = await res.json();

                if (!res.ok) {
                    alert(data.error || 'No fue posible registrar tu respuesta.');
                    Array.from(document.querySelectorAll('.opt-btn')).forEach((b) => b.disabled = false);
                    btn.classList.remove('disabled');
                    answering = false;
                    refreshState();
                    return;
                }

                const body = document.querySelector('#questionModal .modal-body');
                body.innerHTML =
                    '<div class="alert ' + (data.is_correct ? 'alert-success' : 'alert-danger') + ' fw-bold">' +
                    (data.is_correct ? '¡Correcto!' : 'Incorrecto') + '</div>' +
                    '<p class="mb-1"><small>Respuesta correcta: ' + data.correct_text + '</small></p>' +
                    '<p class="mb-0">Puntaje: <strong>' + data.participation_score + ' / ' + maxScore + '</strong></p>';

                answeredThisRound = true;

                setTimeout(() => {
                    questionModal.hide();
                    resetQuestion();
                    answering = false;

                    if (data.finished) {
                        window.location.href = resultUrl;
                    } else {
                        refreshState();
                    }
                }, 1600);
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
                        setTimeout(() => wheel.classList.remove('spinning'), 1200);
                        refreshState();
                    })
                    .listen('.RoundClosed', () => refreshState())
                    .listen('.TurnChanged', () => refreshState())
                    .listen('.ScoreUpdated', () => refreshState())
                    .listen('.GameFinished', () => { window.location.href = resultUrl; });
            }
        });
    </script>

</body>

</html>