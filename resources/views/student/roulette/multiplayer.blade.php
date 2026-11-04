<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ruleta — Sala {{ $session->code }}</title>
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
        :root {
            --bg: #f5f1f8;
            --surface: #ffffff;
            --surface-soft: #faf7fc;
            --primary: #8e24aa;
            --primary-dark: #5d176f;
            --primary-light: #f3e5f5;
            --accent: #c026d3;
            --text: #29222d;
            --muted: #766d7b;
            --border: #e6dce9;

            --success: #15803d;
            --success-bg: #dcfce7;
            --success-border: #4ade80;

            --danger: #b91c1c;
            --danger-bg: #fee2e2;
            --danger-border: #f87171;

            --shadow: 0 24px 70px rgb(54 20 64 / 0.12);
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            min-height: 100vh;
            margin: 0;
            padding: 22px 14px;
            color: var(--text);
            background:
                radial-gradient(circle at 8% 0%, rgb(216 180 254 / .28), transparent 28%),
                radial-gradient(circle at 92% 8%, rgb(244 114 182 / .16), transparent 25%),
                var(--bg);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont,
                "Segoe UI", sans-serif;
        }

        button,
        input {
            font: inherit;
        }

        button {
            -webkit-tap-highlight-color: transparent;
        }

        .page {
            width: min(1000px, 100%);
            margin: 0 auto;
        }

        .game {
            overflow: hidden;
            border: 1px solid var(--border);
            border-radius: 30px;
            background: var(--surface);
            box-shadow: var(--shadow);
        }

        /* =========================================================
           HEADER
        ========================================================= */

        .header {
            padding: 28px 30px 24px;
            color: #fff;
            background:
                radial-gradient(circle at 80% 10%, rgb(255 255 255 / .18), transparent 22%),
                linear-gradient(135deg, #68127d, #8e24aa 55%, #b423c5);
        }

        .header-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 24px;
        }

        .eyebrow {
            margin: 0 0 6px;
            color: rgb(255 255 255 / .72);
            font-size: .72rem;
            font-weight: 850;
            letter-spacing: .13em;
            text-transform: uppercase;
        }

        .title {
            margin: 0;
            font-size: clamp(1.7rem, 4vw, 2.35rem);
            line-height: 1.05;
            letter-spacing: -.025em;
        }

        .activity {
            margin: 8px 0 0;
            color: rgb(255 255 255 / .82);
            font-size: .94rem;
        }

        .participant-count { display:inline-flex; margin-top:10px; padding:5px 10px; border:1px solid rgb(255 255 255 / .24); border-radius:999px; background:rgb(255 255 255 / .12); color:#fff; font-size:.78rem; font-weight:800; }

        .timer {
            flex: 0 0 auto;
            min-width: 145px;
            padding: 11px 16px;
            border: 1px solid rgb(255 255 255 / .22);
            border-radius: 15px;
            background: rgb(255 255 255 / .12);
            text-align: center;
            backdrop-filter: blur(10px);
        }

        .timer-label {
            display: block;
            margin-bottom: 2px;
            color: rgb(255 255 255 / .7);
            font-size: .68rem;
            font-weight: 800;
            letter-spacing: .07em;
            text-transform: uppercase;
        }

        #timer-value {
            font-size: 1.35rem;
            font-weight: 900;
            letter-spacing: .05em;
        }

        #timer-value.warning {
            color: #fde68a;
        }

        #timer-value.danger {
            color: #fecaca;
        }

        /* =========================================================
           STATS
        ========================================================= */

        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            padding: 18px 30px;
            border-bottom: 1px solid var(--border);
            background: #fff;
        }

        .stat {
            padding: 12px 15px;
            border: 1px solid var(--border);
            border-radius: 15px;
            background: var(--surface-soft);
            text-align: center;
        }

        .stat-label {
            display: block;
            margin-bottom: 3px;
            color: var(--muted);
            font-size: .69rem;
            font-weight: 800;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .stat-value {
            color: var(--primary-dark);
            font-size: 1.15rem;
            font-weight: 900;
        }

        /* =========================================================
           PROGRESS
        ========================================================= */

        .progress-area {
            padding: 18px 30px 0;
        }

        .progress-head {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 7px;
            color: var(--muted);
            font-size: .78rem;
            font-weight: 750;
        }

        .progress-track {
            height: 9px;
            overflow: hidden;
            border-radius: 999px;
            background: #eee7f1;
        }

        .progress-fill {
            width: 0%;
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, #7e22ce, #c026d3);
            transition: width .35s ease;
        }

        /* =========================================================
           GAME AREA
        ========================================================= */

        .main {
            padding: 28px 30px 30px;
        }

        .instructions {
            width: min(680px, 100%);
            margin: 0 auto 24px;
            padding: 13px 17px;
            border: 1px solid #ead7ef;
            border-radius: 14px;
            color: #6b2177;
            background: #fcf7fd;
            text-align: center;
            font-size: .9rem;
            font-weight: 650;
        }

        .wheel-area {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .remaining-label {
            margin-bottom: 15px;
            color: var(--muted);
            font-size: .85rem;
            font-weight: 750;
        }

        .remaining-label strong {
            color: var(--primary);
        }

        /* =========================================================
           WHEEL
        ========================================================= */

        .wheel-stage {
            position: relative;
            width: min(410px, 82vw);
            aspect-ratio: 1;
            display: grid;
            place-items: center;
        }

        .wheel-shadow {
            position: absolute;
            inset: 7%;
            border-radius: 50%;
            background: rgb(91 23 111 / .22);
            filter: blur(24px);
            transform: translateY(15px);
        }

        .pointer {
            position: absolute;
            top: -2px;
            left: 50%;
            z-index: 20;
            width: 0;
            height: 0;
            transform: translateX(-50%);
            border-left: 19px solid transparent;
            border-right: 19px solid transparent;
            border-top: 33px solid #481052;
            filter: drop-shadow(0 5px 4px rgb(0 0 0 / .22));
        }

        .pointer::after {
            content: "";
            position: absolute;
            left: -9px;
            top: -32px;
            width: 18px;
            height: 9px;
            border-radius: 999px;
            background: #fce7f3;
        }

        .wheel-border {
            position: relative;
            z-index: 5;
            width: 100%;
            height: 100%;
            padding: 11px;
            border-radius: 50%;
            background:
                linear-gradient(145deg, #4a0d59, #a21caf 48%, #5b126d);
            box-shadow:
                0 22px 42px rgb(76 29 94 / .25),
                inset 0 0 0 2px rgb(255 255 255 / .25);
        }

        .wheel {
            position: relative;
            width: 100%;
            height: 100%;
            overflow: hidden;
            border: 5px solid #fff;
            border-radius: 50%;
            background: #d8b4fe;
            transform: rotate(0deg);
            transition: transform 4.6s cubic-bezier(.11, .82, .19, 1);
            will-change: transform;
        }

        /*
         * Líneas visuales entre segmentos. Los segmentos reales son
         * generados dinámicamente por JavaScript según las preguntas
         * pendientes.
         */
        .wheel::after {
            content: "";
            position: absolute;
            inset: 0;
            z-index: 3;
            border-radius: 50%;
            box-shadow:
                inset 0 0 0 3px rgb(255 255 255 / .5),
                inset 0 0 35px rgb(76 29 94 / .18);
            pointer-events: none;
        }

        .wheel-center {
            position: absolute;
            top: 50%;
            left: 50%;
            z-index: 30;
            width: 104px;
            height: 104px;
            transform: translate(-50%, -50%);
            border: 8px solid #fff;
            border-radius: 50%;
            color: #fff;
            background: linear-gradient(145deg, #7e22ce, #a21caf);
            box-shadow:
                0 9px 22px rgb(76 29 94 / .3),
                0 0 0 6px rgb(126 34 206 / .17);
            font-size: .9rem;
            font-weight: 950;
            letter-spacing: .08em;
            cursor: pointer;
            transition: transform .15s ease, filter .15s ease;
        }

        .wheel-center:hover:not(:disabled) {
            transform: translate(-50%, -50%) scale(1.05);
            filter: brightness(1.08);
        }

        .wheel-center:active:not(:disabled) {
            transform: translate(-50%, -50%) scale(.98);
        }

        .wheel-center:disabled {
            cursor: default;
            opacity: .65;
        }

        .spin-hint {
            min-height: 22px;
            margin-top: 18px;
            color: var(--muted);
            font-size: .88rem;
            font-weight: 650;
            text-align: center;
        }

        .spin-hint.spinning {
            color: var(--primary);
        }

        /* =========================================================
           MODAL
        ========================================================= */

        .modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 18px;
            background: rgb(27 13 31 / .68);
            backdrop-filter: blur(7px);
        }

        .modal-backdrop.visible {
            display: flex;
            animation: backdrop-in .2s ease;
        }

        .question-modal {
            width: min(700px, 100%);
            max-height: min(760px, calc(100vh - 36px));
            overflow: auto;
            border: 1px solid rgb(255 255 255 / .45);
            border-radius: 25px;
            background: #fff;
            box-shadow: 0 30px 90px rgb(0 0 0 / .28);
            animation: modal-in .28s cubic-bezier(.2, .8, .2, 1);
        }

        .modal-top {
            padding: 20px 22px 18px;
            color: #fff;
            background: linear-gradient(135deg, #6b177f, #9c27b0);
        }

        .modal-top-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
        }

        .modal-label {
            margin: 0;
            font-size: .72rem;
            font-weight: 850;
            letter-spacing: .1em;
            text-transform: uppercase;
            opacity: .78;
        }

        .modal-points {
            padding: 5px 9px;
            border: 1px solid rgb(255 255 255 / .22);
            border-radius: 999px;
            background: rgb(255 255 255 / .12);
            font-size: .76rem;
            font-weight: 800;
        }

        .modal-body {
            padding: 24px;
        }

        #question-text {
            margin: 0 0 22px;
            color: #35133e;
            font-size: clamp(1.2rem, 3vw, 1.55rem);
            font-weight: 850;
            line-height: 1.35;
        }

        .options {
            display: grid;
            gap: 11px;
        }

        .option-btn {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 13px;
            padding: 13px 14px;
            border: 2px solid #e6dce9;
            border-radius: 15px;
            color: #342b38;
            background: #fff;
            text-align: left;
            font-size: .98rem;
            font-weight: 650;
            cursor: pointer;
            transition:
                border-color .15s ease,
                background .15s ease,
                transform .12s ease,
                box-shadow .15s ease;
        }

        .option-btn:hover:not(:disabled) {
            border-color: #c084fc;
            background: #fdfaff;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgb(76 29 94 / .08);
        }

        .option-key {
            flex: 0 0 40px;
            width: 40px;
            height: 40px;
            display: grid;
            place-items: center;
            border-radius: 11px;
            color: #fff;
            background: linear-gradient(145deg, #a21caf, #7e22ce);
            font-weight: 950;
        }

        .option-text {
            flex: 1;
            line-height: 1.35;
        }

        .option-btn.correct {
            border-color: var(--success-border);
            background: var(--success-bg);
            color: #166534;
        }

        .option-btn.correct .option-key {
            background: var(--success);
        }

        .option-btn.wrong {
            border-color: var(--danger-border);
            background: var(--danger-bg);
            color: #991b1b;
        }

        .option-btn.wrong .option-key {
            background: var(--danger);
        }

        .option-btn:disabled {
            cursor: default;
        }

        .answer-feedback {
            display: none;
            margin-top: 17px;
            padding: 13px 15px;
            border-radius: 13px;
            font-size: .9rem;
            font-weight: 800;
            text-align: center;
        }

        .answer-feedback.visible {
            display: block;
            animation: feedback-in .2s ease;
        }

        .answer-feedback.ok {
            color: #166534;
            border: 1px solid #86efac;
            background: var(--success-bg);
        }

        .answer-feedback.bad {
            color: #991b1b;
            border: 1px solid #fca5a5;
            background: var(--danger-bg);
        }

        .modal-note {
            margin: 16px 0 0;
            color: var(--muted);
            font-size: .76rem;
            text-align: center;
        }

        /* =========================================================
           COMPLETION
        ========================================================= */

        .finish {
            display: none;
            width: min(650px, 100%);
            margin: 25px auto 0;
            padding: 20px;
            border: 1px solid #d8b4fe;
            border-radius: 17px;
            color: #6b21a8;
            background: #f3e8ff;
            font-weight: 800;
            text-align: center;
        }

        .finish.visible {
            display: block;
            animation: feedback-in .25s ease;
        }

        .actions {
            display: flex;
            justify-content: center;
            margin-top: 24px;
        }

        .abandon-btn {
            border: 0;
            border-radius: 11px;
            padding: 9px 15px;
            color: #6f6672;
            background: #f0ebf2;
            font-size: .83rem;
            font-weight: 750;
            cursor: pointer;
            transition: background .15s ease;
        }

        .abandon-btn:hover {
            background: #e5dce8;
        }

        /* =========================================================
           ANIMATIONS
        ========================================================= */

        @keyframes backdrop-in {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes modal-in {
            from {
                opacity: 0;
                transform: translateY(18px) scale(.97);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes feedback-in {
            from {
                opacity: 0;
                transform: scale(.98);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* =========================================================
           RESPONSIVE
        ========================================================= */

        @media (max-width: 680px) {
            body {
                padding: 9px;
            }

            .header {
                padding: 22px 18px;
            }

            .main {
                padding: 23px 18px 24px;
            }

            .stats {
                padding: 14px 18px;
                gap: 8px;
            }

            .progress-area {
                padding-left: 18px;
                padding-right: 18px;
            }

            .header-row {
                flex-direction: column;
                gap: 14px;
            }

            .timer {
                width: 100%;
            }

            .wheel-stage {
                width: min(350px, 84vw);
            }

            .wheel-center {
                width: 92px;
                height: 92px;
            }

            .modal-body {
                padding: 19px;
            }
        }

        @media (max-width: 460px) {
            .stats {
                grid-template-columns: 1fr;
            }

            .wheel-stage {
                width: min(300px, 84vw);
            }

            .wheel-center {
                width: 80px;
                height: 80px;
                font-size: .75rem;
            }

            .pointer {
                border-left-width: 16px;
                border-right-width: 16px;
                border-top-width: 28px;
            }

            .option-btn {
                padding: 11px;
            }

            .option-key {
                flex-basis: 36px;
                width: 36px;
                height: 36px;
            }
        }

        .wheel { transition: none; }
        .wheel.spinning { animation: multiplayer-wheel-spin 4.6s cubic-bezier(.11, .82, .19, 1); }
        @keyframes multiplayer-wheel-spin { to { transform: rotate(1440deg); } }
        .round-status-panel { width: min(680px, 100%); margin: 0 auto 20px; padding: 15px 18px; border: 1px solid #ead7ef; border-radius: 15px; background: #fcf7fd; text-align: center; }
        .turn-badge { display: inline-flex; align-items: center; min-height: 34px; padding: 6px 13px; border-radius: 999px; color: #fff; background: var(--primary); font-size: .82rem; font-weight: 850; }
        .turn-hint { margin: 8px 0 0; color: var(--muted); font-size: .85rem; }
        .round-timer { margin-top: 8px; color: rgb(255 255 255 / .86); font-size: .8rem; font-weight: 800; }
        .round-timer span { font-variant-numeric: tabular-nums; }
        .round-wait-message { margin-top: 7px; color: var(--muted); font-size: .8rem; font-weight: 700; }
        .round-wait-message.spinning { color: var(--primary); }
        .multiplayer-details { display: grid; grid-template-columns: minmax(0, 1.3fr) minmax(220px, .7fr); gap: 14px; width: min(760px, 100%); margin: 28px auto 0; }
        .details-panel { padding: 17px 18px; border: 1px solid var(--border); border-radius: 16px; background: var(--surface-soft); }
        .details-panel h2 { margin: 0 0 10px; color: var(--primary-dark); font-size: .95rem; font-weight: 850; }
        #players { display: grid; gap: 7px; margin: 0; padding: 0; list-style: none; }
        #players { max-height: 300px; overflow-y: auto; padding-right: 4px; scrollbar-gutter: stable; }
        #players li { display: flex; justify-content: space-between; align-items: center; gap: 10px; padding: 9px 11px; border: 1px solid var(--border); border-radius: 11px; background: var(--surface); font-size: .85rem; }
        #players [data-role=score] { color: var(--primary-dark); font-weight: 850; white-space: nowrap; }
        .room-code { color: rgb(255 255 255 / .82); font-size: .85rem; }
        .multiplayer-abandon { width: 100%; border: 0; border-radius: 11px; padding: 10px 14px; color: #6f6672; background: #f0ebf2; font-size: .83rem; font-weight: 750; cursor: pointer; transition: background .15s ease; }
        .multiplayer-abandon:hover { background: #e5dce8; }
        .option-btn.round-option-btn:disabled { opacity: .78; }
        .modal-note { max-width: 520px; margin-right: auto; margin-left: auto; }
        .modal-backdrop[aria-hidden=true] { visibility: hidden; }
        html[data-theme=dark] body { color: #e5e7eb; background: radial-gradient(circle at 8% 0%, rgb(126 34 206 / .18), transparent 28%), #0f172a; }
        html[data-theme=dark] .game { border-color: #334155; background: #111827; }
        html[data-theme=dark] .stats { border-color: #334155; background: #111827; }
        html[data-theme=dark] .stat, html[data-theme=dark] .details-panel { border-color: #334155; color: #e5e7eb; background: #1e293b; }
        html[data-theme=dark] .stat-value, html[data-theme=dark] .details-panel h2 { color: #e9d5ff; }
        html[data-theme=dark] .progress-area { background: #111827; }
        html[data-theme=dark] .progress-track { background: #334155; }
        html[data-theme=dark] .main { background: #111827; }
        html[data-theme=dark] .instructions, html[data-theme=dark] .round-status-panel { border-color: #5b3b68; color: #e9d5ff; background: #241b2e; }
        html[data-theme=dark] .turn-hint, html[data-theme=dark] .remaining-label, html[data-theme=dark] .progress-head,
        html[data-theme=dark] .round-wait-message { color: #cbd5e1; }
        html[data-theme=dark] .details-panel #players li { border-color: #475569; color: #e2e8f0; background: #0f172a; }
        html[data-theme=dark] #players [data-role=score] { color: #e9d5ff; }
        html[data-theme=dark] .question-modal { border-color: #475569; background: #111827; }
        html[data-theme=dark] #q-text { color: #f5d0fe; }
        html[data-theme=dark] .option-btn { border-color: #475569; color: #f1f5f9; background: #0f172a; }
        html[data-theme=dark] .option-btn:hover:not(:disabled) { border-color: #d8b4fe; background: #1e1b2e; }
        html[data-theme=dark] .modal-note { color: #cbd5e1; }
        html[data-theme=dark] .answer-feedback.ok { color: #bbf7d0; background: #14532d; }
        html[data-theme=dark] .answer-feedback.bad { color: #fecaca; background: #7f1d1d; }
        @media (max-width: 680px) { .multiplayer-details { grid-template-columns: 1fr; } }
        @media (max-width: 460px) { .round-status-panel { padding: 12px; } .multiplayer-details { margin-top: 20px; } }
        @media (prefers-reduced-motion: reduce) { .wheel, .progress-fill { transition: none; animation: none !important; } }
    </style>
</head>

<body>

    <main class="page">
        <section class="game">
            <header class="header">
                <div class="header-row">
                    <div>
                        <p class="eyebrow">{{ $activity->mode === 'team' ? 'Actividad por equipos' : 'Partida multijugador' }}</p>
                        <h1 class="title">Ruleta</h1>
                        <p class="activity">{{ $activity->title }}</p>
                        <p class="room-code mb-0">Sala {{ $session->code }}</p>
                        <span class="participant-count">{{ count($state['players']) }} {{ $activity->mode === 'team' ? 'equipos' : 'participantes' }}</span>
                    </div>
                    <div class="timer">
                        <span class="timer-label">Tiempo restante</span>
                        <span id="timer-value">--:--</span>
                        <div class="round-timer">Ronda: <span id="round-timer">--</span></div>
                    </div>
                </div>
            </header>

            <section class="stats" aria-label="Progreso del equipo">
                <div class="stat">
                    <span class="stat-label">{{ $activity->mode === 'team' ? 'Puntaje del equipo' : 'Tu puntaje' }}</span>
                    <span class="stat-value"><span id="points">{{ $participation->score }}</span> / {{ $maxScore }}</span>
                </div>
                <div class="stat">
                    <span class="stat-label">Respondidas</span>
                    <span class="stat-value"><span id="answered-count">{{ $answeredCount }}</span> / {{ $totalItems }}</span>
                </div>
                <div class="stat">
                    <span class="stat-label">Estado</span>
                    <span id="game-status" class="stat-value">Esperando</span>
                </div>
            </section>

            <section class="progress-area" aria-label="Progreso de la actividad">
                <div class="progress-head">
                    <span>{{ $activity->mode === 'team' ? 'Progreso del equipo' : 'Progreso de la partida' }}</span>
                    <span id="progress-percent">{{ $totalItems > 0 ? min(100, (int) round(($answeredCount / $totalItems) * 100)) : 0 }}%</span>
                </div>
                <div class="progress-track" role="progressbar" aria-label="Preguntas respondidas"
                    aria-valuemin="0" aria-valuemax="100"
                    aria-valuenow="{{ $totalItems > 0 ? min(100, (int) round(($answeredCount / $totalItems) * 100)) : 0 }}">
                    <div id="progress-fill" class="progress-fill"
                        style="width: {{ $totalItems > 0 ? min(100, (int) round(($answeredCount / $totalItems) * 100)) : 0 }}%"></div>
                </div>
            </section>

            <section class="main">
                <div class="instructions">
                    @if ($activity->mode === 'team')
                        El profesor gira la ruleta. Cuando aparezca una pregunta, el integrante de turno responde y el equipo puede ayudarle a decidir.
                    @else
                        El profesor gira la ruleta y abre una pregunta para que respondas antes de que termine el tiempo.
                    @endif
                </div>

                <div class="round-status-panel" aria-live="polite">
                    <span class="turn-badge" id="turn-badge" tabindex="-1">Preparando ronda...</span>
                    <p id="turn-hint" class="turn-hint mb-0">Esperando a que el profesor abra la siguiente ronda.</p>
                    <p id="wait-msg" class="round-wait-message mb-0" aria-live="polite">La ruleta comenzará cuando el profesor la gire.</p>
                </div>

                <div class="wheel-area">
                    <div class="remaining-label">
                        <strong id="remaining-count">{{ max($totalItems - $answeredCount, 0) }}</strong>
                        <span id="remaining-text">preguntas restantes</span>
                    </div>
                    <div class="wheel-stage">
                        <div class="wheel-shadow"></div>
                        <div class="pointer" aria-hidden="true"></div>
                        <div class="wheel-border">
                            <div id="wheel" class="wheel" aria-hidden="true"></div>
                        </div>
                        <button id="wheel-center" class="wheel-center" type="button" disabled
                            aria-label="La ruleta la controla el profesor">
                            <span id="wheel-center-label">EN ESPERA</span>
                        </button>
                    </div>
                    <div id="spin-hint" class="spin-hint">La siguiente pregunta aparecerá aquí.</div>
                </div>

                <div class="multiplayer-details">
                    <section class="details-panel">
                        <h2>Marcador de la sala</h2>
                        <ul id="players">
                            @foreach ($state['players'] as $p)
                                <li data-participation-id="{{ $p['participation_id'] }}">
                                    <span>
                                        @if ($p['participation_id'] === $participation->id)
                                            <strong>{{ $p['name'] }}</strong>
                                            <small>({{ $activity->mode === 'team' ? 'tu equipo' : 'tú' }})</small>
                                        @else
                                            {{ $p['name'] }}
                                        @endif
                                    </span>
                                    <span data-role="score">{{ $p['score'] }} / {{ $maxScore }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                    <section class="details-panel">
                        <h2>Acciones</h2>
                        <form method="POST" action="{{ route('student.game-sessions.leave', $session->id) }}">
                            @csrf
                            <button type="submit" class="multiplayer-abandon">Salir de la sala</button>
                        </form>
                    </section>
                </div>
            </section>
        </section>
    </main>

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

            window.KlassioSounds?.startMusic();

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
                    window.KlassioSounds?.completado();
                    redirectAfterFinalResult(2200);
                }
            } catch (e) {
                answering = false;
                alert('Error de conexión. Intenta de nuevo.');
            }
        }

        window.addEventListener('DOMContentLoaded', () => {
            window.KlassioSounds?.startMusic();
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
        window.KlassioSounds?.setup('roulette');
    </script>

</body>

</html>
