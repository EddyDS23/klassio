<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kahoot — Sala {{ $session->code }}</title>
    @include('partials.assets', ['theme' => 'student'])
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
    <style>
        :root { --kh-ink: #183b3a; --kh-muted: #587371; --kh-line: #d7e8e4; --kh-deep: #075e58; }
        body.student-kids { min-height: 100vh; color: var(--kh-ink); background: radial-gradient(circle at 8% 0%, #d9f3e8 0, transparent 31%), #eff6f2; }
        .page-shell { width: min(1180px, calc(100% - 28px)); margin: 0 auto; padding: 22px 0 38px; }
        .kahoot-header { display:flex; align-items:center; justify-content:space-between; gap:16px; margin-bottom:18px; }
        .kahoot-title { min-width:0; }
        .kahoot-title h1 { margin:0; color:var(--kh-ink); font-size:clamp(1.3rem, 3vw, 1.9rem); font-weight:900; overflow-wrap:anywhere; }
        .kahoot-title p { margin:.25rem 0 0; color:var(--kh-muted); }
        .session-pill { flex:none; padding:8px 12px; border:1px solid var(--kh-line); border-radius:12px; background:#fff; color:var(--kh-deep); font-weight:850; }
        .session-meta { display:flex; flex:none; flex-wrap:wrap; justify-content:flex-end; gap:7px; }
        .session-meta span { padding:7px 10px; border:1px solid var(--kh-line); border-radius:11px; background:#fff; color:var(--kh-deep); font-size:.82rem; font-weight:800; }
        .kahoot-layout { display:grid; grid-template-columns:minmax(0, 1fr) 300px; gap:18px; align-items:start; }
        .game-panel, .side-panel { border:1px solid var(--kh-line); border-radius:20px; background:#fff; box-shadow:0 18px 45px rgb(23 69 60 / .09); }
        .game-panel { min-height:440px; padding:clamp(16px, 3vw, 28px); }
        .side-panel { padding:20px; }
        .game-topline { display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:10px; }
        .turn-badge { display:inline-flex; align-items:center; max-width:100%; background:#e4f4eb; color:var(--kh-deep); border:1px solid #cce7d8; border-radius:999px; padding:.5rem .9rem; font-weight:850; }
        .round-clock { flex:none; padding:7px 11px; border-radius:11px; background:#f4f8f6; color:var(--kh-deep); font-weight:900; font-variant-numeric:tabular-nums; }
        .round-clock.is-urgent { background:#fff0ed; color:#a5362f; }
        .question-progress { height:8px; margin:14px 0 8px; overflow:hidden; border-radius:999px; background:#e4efeb; }
        .question-progress > span { display:block; width:0; height:100%; border-radius:inherit; background:#07877b; transition:width .3s ease; }
        .question-count { margin:0 0 12px; color:var(--kh-muted); font-size:.84rem; font-weight:800; }
        .question-card { display:flex; min-height:126px; align-items:center; justify-content:center; margin-bottom:16px; padding:clamp(18px, 4vw, 30px); border:1px solid #d8e9e1; border-radius:17px; background:linear-gradient(140deg,#f0faf5,#e7f4ed); color:var(--kh-ink); font-size:clamp(1.2rem, 3vw, 1.65rem); font-weight:850; line-height:1.35; text-align:center; overflow-wrap:anywhere; }
        .answer-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:10px; }
        .turn-badge {
            background: var(--klassio-primary);
            color: #fff;
            border-radius: 1rem;
            padding: .5rem 1rem;
            font-weight: 700;
        }

        .opt-btn { display:flex; min-height:76px; align-items:center; justify-content:center; width:100%; padding:14px; border:2px solid transparent; border-radius:14px; color:#fff; font:inherit; font-size:1rem; font-weight:800; line-height:1.3; text-align:center; overflow-wrap:anywhere; transition:transform .12s, box-shadow .15s, opacity .2s; }
        .opt-btn:nth-child(1) { background:#b83c37; }
        .opt-btn:nth-child(2) { background:#2865a4; }
        .opt-btn:nth-child(3) { background:#98700a; }
        .opt-btn:nth-child(4) { background:#247346; }
        .opt-btn:hover:not(:disabled) { transform:translateY(-2px); box-shadow:0 8px 18px rgb(24 59 58 / .16); }
        .opt-btn:focus-visible, .leave-button:focus-visible { outline:3px solid #f59e0b; outline-offset:3px; }

        .opt-btn:disabled {
            cursor: not-allowed;
            opacity: .72;
        }

        #round-answer-feedback { margin-top:14px; }
        #round-question-note { min-height:1.4em; color:var(--kh-muted); }
        .wait-state { display:flex; min-height:340px; flex-direction:column; align-items:center; justify-content:center; padding:24px; text-align:center; }
        .wait-icon { display:grid; width:66px; height:66px; place-items:center; margin-bottom:14px; border-radius:20px; background:#e4f4eb; color:var(--kh-deep); font-size:2rem; font-weight:900; }
        .wait-state h2 { margin:0 0 7px; font-size:1.3rem; font-weight:900; }
        .wait-state p { max-width:440px; margin:0; color:var(--kh-muted); }
        .side-panel h2 { margin:0 0 12px; color:var(--kh-ink); font-size:1.05rem; font-weight:900; }
        .score-summary { display:flex; justify-content:space-between; gap:12px; margin-bottom:8px; color:var(--kh-muted); font-size:.86rem; }
        #players .list-group-item { display:flex; justify-content:space-between; align-items:center; gap:12px; padding:11px 0; border-color:#edf3f0; background:transparent; color:var(--kh-ink); }
        #players { max-height:300px; overflow-y:auto; scrollbar-gutter:stable; }
        #players [data-role="score"] { flex:none; color:var(--kh-deep); font-variant-numeric:tabular-nums; }
        .leave-button { min-height:44px; border:1px solid #e9c7c6; border-radius:11px; background:#fff6f4; color:#9b3a35; font:inherit; font-weight:750; }
        html[data-theme="dark"] body.student-kids { --kh-ink:#e6f1ee; --kh-muted:#a4bab4; --kh-line:#334a45; color:var(--kh-ink); background:radial-gradient(circle at 8% 0%,#193d35 0,transparent 34%),#101d1a; }
        html[data-theme="dark"] .game-panel, html[data-theme="dark"] .side-panel, html[data-theme="dark"] .session-pill, html[data-theme="dark"] .session-meta span { background:#172622; border-color:var(--kh-line); color:var(--kh-ink); }
        html[data-theme="dark"] .kahoot-title h1, html[data-theme="dark"] .side-panel h2 { color:var(--kh-ink); }
        html[data-theme="dark"] .question-card { background:linear-gradient(140deg,#203b34,#1b302b); border-color:#38564d; color:var(--kh-ink); }
        html[data-theme="dark"] .round-clock { background:#223630; color:#c4f2d9; }
        html[data-theme="dark"] .round-clock.is-urgent { background:#482b29; color:#ffc2b9; }
        html[data-theme="dark"] #players .list-group-item { color:var(--kh-ink); }
        html[data-theme="dark"] .leave-button { background:#382321; border-color:#69413c; color:#ffc2b9; }
        [hidden] { display:none !important; }
        @media (max-width:800px) { .kahoot-layout { grid-template-columns:minmax(0,1fr); } .game-panel { min-height:0; } }
        @media (max-width:560px) { .page-shell { width:calc(100% - 20px); padding-top:12px; } .kahoot-header { align-items:flex-start; flex-wrap:wrap; } .session-meta { width:100%; justify-content:flex-start; } .session-pill { padding:7px 9px; font-size:.82rem; } .game-panel,.side-panel { border-radius:16px; } .game-topline { align-items:flex-start; flex-direction:column; } .answer-grid { grid-template-columns:minmax(0,1fr); gap:8px; } .opt-btn { min-height:58px; padding:12px; } .question-card { min-height:96px; } }
        @media (prefers-reduced-motion:reduce) { *,*::before,*::after { scroll-behavior:auto !important; transition-duration:.01ms !important; } }
    </style>
</head>

<body class="student-kids">

    <div class="page-shell">

        <header class="kahoot-header">
            <div class="kahoot-title">
                <h1>{{ $activity->title }}</h1>
                <p>Kahoot multijugador · Código de sala {{ $session->code }}</p>
            </div>
            <div class="session-meta">
                <span>Sala {{ $session->code }}</span>
                <span>{{ count($state['players']) }} participantes</span>
            </div>
        </header>

        <div class="kahoot-layout">
            <main class="game-panel" aria-live="polite">
                <section id="question-stage" hidden>
                    <div class="game-topline">
                        <span class="turn-badge" id="turn-badge">Preparando...</span>
                        <span class="round-clock" id="round-clock">Pregunta: <span id="round-timer">--</span></span>
                    </div>
                    <div class="question-progress" role="progressbar" aria-label="Preguntas respondidas"
                        aria-valuemin="0" aria-valuemax="{{ $totalItems }}" aria-valuenow="{{ $answeredCount }}">
                        <span id="question-progress-fill"></span>
                    </div>
                    <p class="question-count"><span id="answered-count">{{ $answeredCount }}</span> de {{ $totalItems }} preguntas respondidas · Tiempo de partida: <span id="timer">--:--</span></p>
                    <div class="question-card" id="q-text"></div>
                    <div class="answer-grid" id="q-options"></div>
                    <div id="round-answer-feedback" aria-live="assertive"></div>
                    <p class="text-center mt-3 mb-0" id="round-question-note"></p>
                </section>
                <section class="wait-state" id="wait-state" aria-live="polite">
                    <div class="wait-icon" aria-hidden="true">?</div>
                    <h2 id="wait-title">Preparados para jugar</h2>
                    <p id="wait-msg">Esperando a que el profesor lance la primera pregunta…</p>
                    <p class="mt-2" id="turn-hint">Cuando empiece la ronda, la pregunta aparecerá aquí.</p>
                </section>
            </main>

            <aside>
                <section class="side-panel">
                    <h2>Marcador</h2>
                    <div class="score-summary"><span>Progreso</span><strong><span id="answered-count-side">{{ $answeredCount }}</span> / {{ $totalItems }}</strong></div>
                    <ul class="list-group list-group-flush" id="players">
                        @foreach ($state['players'] as $p)
                            <li class="list-group-item" data-participation-id="{{ $p['participation_id'] }}">
                                <span>@if ($p['participation_id'] === $participation->id)<strong>{{ $p['name'] }}</strong> <span class="text-muted">(tú)</span>@else{{ $p['name'] }}@endif</span>
                                <span data-role="score">{{ $p['score'] }} / {{ $maxScore }}</span>
                            </li>
                        @endforeach
                    </ul>
                </section>
                <section class="side-panel mt-3">
                    <h2>Salir de la partida</h2>
                    <form method="POST" action="{{ route('student.game-sessions.leave', $session->id) }}">
                        @csrf
                        <button type="submit" class="leave-button w-100">Abandonar partida</button>
                    </form>
                </section>
            </aside>
        </div>

    </div>

    <script>
        const sessionId = @json($session->id);
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
