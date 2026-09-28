<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Ruleta — {{ $activity->title }}</title>

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
    </style>
</head>

<body>

    <main class="page">
        <section class="game">

            <!-- =====================================================
                 HEADER
            ====================================================== -->
            <header class="header">
                <div class="header-row">
                    <div>
                        <p class="eyebrow">Actividad interactiva</p>
                        <h1 class="title">Ruleta</h1>
                        <p class="activity">{{ $activity->title }}</p>
                    </div>

                    <div class="timer">
                        <span class="timer-label">Tiempo restante</span>
                        <span id="timer-value">--:--</span>
                    </div>
                </div>
            </header>

            <!-- =====================================================
                 STATS
            ====================================================== -->
            <section class="stats">
                <div class="stat">
                    <span class="stat-label">Puntaje</span>
                    <span class="stat-value">
                        <span id="points">{{ $earnedPoints }}</span> / {{ $maxScore }}
                    </span>
                </div>

                <div class="stat">
                    <span class="stat-label">Respondidas</span>
                    <span class="stat-value">
                        <span id="answered-count">{{ $answeredCount }}</span> / {{ $totalItems }}
                    </span>
                </div>

                <div class="stat">
                    <span class="stat-label">Estado</span>
                    <span id="game-status" class="stat-value">Listo</span>
                </div>
            </section>

            <!-- =====================================================
                 PROGRESS
            ====================================================== -->
            <section class="progress-area">
                <div class="progress-head">
                    <span>Progreso de la actividad</span>
                    <span id="progress-percent">0%</span>
                </div>

                <div class="progress-track">
                    <div id="progress-fill" class="progress-fill"></div>
                </div>
            </section>

            <!-- =====================================================
                 GAME
            ====================================================== -->
            <section class="main">

                <div class="instructions">
                    Gira la ruleta para descubrir una pregunta.
                    Responde para retirar ese segmento y continuar con las preguntas restantes.
                </div>

                <div class="wheel-area">

                    <div class="remaining-label">
                        <strong id="remaining-count">{{ $totalItems - $answeredCount }}</strong>
                        <span id="remaining-text">preguntas restantes</span>
                    </div>

                    <div class="wheel-stage">
                        <div class="wheel-shadow"></div>

                        <div class="pointer"></div>

                        <div class="wheel-border">
                            <div id="wheel" class="wheel"></div>
                        </div>

                        <button
                            id="spin-btn"
                            class="wheel-center"
                            type="button"
                        >
                            GIRAR
                        </button>
                    </div>

                    <div id="spin-hint" class="spin-hint">
                        Pulsa GIRAR para comenzar
                    </div>
                </div>

                <div id="finish" class="finish"></div>

                <div class="actions">
                    <form
                        method="POST"
                        action="{{ route('student.participation.abandon', $activity->id) }}"
                        onsubmit="return confirm('¿Estás seguro de que quieres abandonar esta actividad?');"
                    >
                        @csrf

                        <button type="submit" class="abandon-btn">
                            Abandonar actividad
                        </button>
                    </form>
                </div>
            </section>
        </section>
    </main>

    <!-- =========================================================
         QUESTION MODAL
         No tiene botón de cerrar intencionalmente.
         El estudiante debe responder para poder continuar.
    ========================================================== -->

    <div
        id="question-modal"
        class="modal-backdrop"
        role="dialog"
        aria-modal="true"
        aria-labelledby="question-text"
    >
        <article class="question-modal">

            <header class="modal-top">
                <div class="modal-top-row">
                    <p class="modal-label">Pregunta de la ruleta</p>

                    <span id="modal-points" class="modal-points">
                        0 pts internos
                    </span>
                </div>
            </header>

            <div class="modal-body">

                <p id="question-text"></p>

                <div id="options" class="options"></div>

                <div id="answer-feedback" class="answer-feedback"></div>

                <p class="modal-note">
                    Debes responder esta pregunta para continuar con la ruleta.
                </p>
            </div>
        </article>
    </div>

    <!-- =========================================================
         EXPIRE
    ========================================================== -->

    <form
        id="expire-form"
        method="POST"
        action="{{ route('student.participation.expire', $activity->id) }}"
        style="display:none;"
    >
        @csrf
    </form>

    <script>
        const csrfToken = document.querySelector(
            'meta[name="csrf-token"]'
        ).content;

        const activityId = {{ $activity->id }};
        const rouletteId = {{ $roulette->id }};
        const totalItems = {{ $totalItems }};

        let remainingSeconds = @json($remainingSeconds);

        /*
         * Score mostrado al estudiante:
         * es Participation.score normalizado con Activity.max_score.
         */
        let points = {{ $earnedPoints }};

        let answered = {{ $answeredCount }};

        let spinning = false;
        let awaitingAnswer = false;
        let wheelRotation = 0;
        let currentItemId = null;
        let timerInterval = null;

        const wheel = document.getElementById('wheel');
        const spinBtn = document.getElementById('spin-btn');
        const spinHint = document.getElementById('spin-hint');

        const timerValue = document.getElementById('timer-value');

        const pointsEl = document.getElementById('points');
        const answeredCountEl = document.getElementById('answered-count');
        const gameStatusEl = document.getElementById('game-status');

        const progressFill = document.getElementById('progress-fill');
        const progressPercent = document.getElementById('progress-percent');

        const remainingCountEl = document.getElementById('remaining-count');
        const remainingTextEl = document.getElementById('remaining-text');

        const finishEl = document.getElementById('finish');

        const modal = document.getElementById('question-modal');
        const questionText = document.getElementById('question-text');
        const modalPoints = document.getElementById('modal-points');
        const optionsBox = document.getElementById('options');
        const answerFeedback = document.getElementById('answer-feedback');

        /*
         * Colores suaves para los segmentos.
         * No representan preguntas específicas.
         * Representan únicamente preguntas pendientes.
         */
        const wheelColors = [
            '#f0abfc',
            '#c4b5fd',
            '#93c5fd',
            '#67e8f9',
            '#86efac',
            '#fde68a',
            '#fdba74',
            '#f9a8d4',
            '#a5b4fc',
            '#99f6e4'
        ];

        /* =========================================================
           TIMER
        ========================================================== */

        function formatTime(seconds) {
            if (seconds === null) {
                return '--:--';
            }

            const safe = Math.max(0, seconds);
            const minutes = Math.floor(safe / 60);
            const secs = safe % 60;

            return (
                String(minutes).padStart(2, '0') +
                ':' +
                String(secs).padStart(2, '0')
            );
        }

        function updateTimer() {
            if (remainingSeconds === null) {
                timerValue.textContent = '--:--';
                return;
            }

            timerValue.textContent = formatTime(remainingSeconds);

            timerValue.classList.toggle(
                'warning',
                remainingSeconds <= 60 && remainingSeconds > 20
            );

            timerValue.classList.toggle(
                'danger',
                remainingSeconds <= 20
            );

            if (remainingSeconds <= 0) {
                clearInterval(timerInterval);

                timerValue.textContent = '00:00';

                gameStatusEl.textContent = 'Tiempo agotado';
                spinBtn.disabled = true;

                document.getElementById('expire-form').submit();

                return;
            }

            remainingSeconds--;
        }

        if (remainingSeconds !== null) {
            timerInterval = setInterval(updateTimer, 1000);
        }

        updateTimer();

        /* =========================================================
           WHEEL
        ========================================================== */

        function drawWheel() {
            /*
             * La ruleta representa SOLO preguntas pendientes.
             *
             * Si hay:
             * 8 pendientes → 8 segmentos
             * 7 pendientes → 7 segmentos
             * ...
             * 1 pendiente → un único segmento/círculo
             */
            const remaining = Math.max(totalItems - answered, 0);

            if (remaining === 0) {
                wheel.style.background = 'conic-gradient(#d8b4fe 0deg 360deg)';
                return;
            }

            /*
             * Con una sola pregunta restante no mostramos una
             * división artificial: la ruleta se convierte en
             * un único sector completo.
             */
            if (remaining === 1) {
                wheel.style.background =
                    'conic-gradient(#c084fc 0deg 360deg)';
                return;
            }

            const segmentSize = 360 / remaining;
            const stops = [];

            for (let index = 0; index < remaining; index++) {
                const start = index * segmentSize;
                const end = (index + 1) * segmentSize;

                const color =
                    wheelColors[index % wheelColors.length];

                stops.push(
                    `${color} ${start}deg ${end}deg`
                );
            }

            wheel.style.background =
                `conic-gradient(${stops.join(', ')})`;
        }

        /* =========================================================
           PROGRESS
        ========================================================== */

        function updateProgress() {
            const percentage = totalItems > 0
                ? Math.min(
                    100,
                    Math.round((answered / totalItems) * 100)
                )
                : 0;

            const remaining = Math.max(
                totalItems - answered,
                0
            );

            pointsEl.textContent = points;
            answeredCountEl.textContent = answered;

            progressFill.style.width =
                `${percentage}%`;

            progressPercent.textContent =
                `${percentage}%`;

            remainingCountEl.textContent =
                remaining;

            if (remaining === 1) {
                remainingTextEl.textContent =
                    'pregunta restante';
            } else {
                remainingTextEl.textContent =
                    'preguntas restantes';
            }

            if (answered >= totalItems && totalItems > 0) {
                gameStatusEl.textContent = 'Completado';
            } else if (answered > 0) {
                gameStatusEl.textContent = 'En progreso';
            } else {
                gameStatusEl.textContent = 'Listo';
            }

            drawWheel();
        }

        /* =========================================================
           MODAL
        ========================================================== */

        function openQuestionModal(item) {
            currentItemId = item.id;
            awaitingAnswer = true;

            questionText.textContent =
                item.question;

            modalPoints.textContent =
                `${item.points} pts internos`;

            optionsBox.replaceChildren();

            answerFeedback.className =
                'answer-feedback';

            answerFeedback.textContent = '';

            item.options.forEach((option) => {
                const button =
                    document.createElement('button');

                button.type = 'button';
                button.className = 'option-btn';

                button.dataset.opt =
                    option.key;

                const key =
                    document.createElement('span');

                key.className =
                    'option-key';

                key.textContent =
                    option.key.toUpperCase();

                const text =
                    document.createElement('span');

                text.className =
                    'option-text';

                text.textContent =
                    option.text;

                button.appendChild(key);
                button.appendChild(text);

                button.addEventListener(
                    'click',
                    () => answerQuestion(
                        button,
                        option.key,
                        item.id
                    )
                );

                optionsBox.appendChild(button);
            });

            modal.classList.add('visible');

            /*
             * Evita que el estudiante interactúe con el juego
             * mientras el modal está abierto.
             */
            document.body.style.overflow = 'hidden';

            /*
             * Enfocar la primera opción mejora accesibilidad
             * y navegación por teclado.
             */
            const firstButton =
                optionsBox.querySelector('.option-btn');

            if (firstButton) {
                setTimeout(
                    () => firstButton.focus(),
                    150
                );
            }
        }

        function closeQuestionModal() {
            modal.classList.remove('visible');

            document.body.style.overflow = '';

            optionsBox.replaceChildren();

            answerFeedback.className =
                'answer-feedback';

            answerFeedback.textContent = '';

            currentItemId = null;
        }

        function setOptionsDisabled(disabled) {
            optionsBox
                .querySelectorAll('.option-btn')
                .forEach((button) => {
                    button.disabled = disabled;
                });
        }

        /* =========================================================
           ANSWER
        ========================================================== */

        async function answerQuestion(
            selectedButton,
            optionKey,
            itemId
        ) {
            if (
                !awaitingAnswer ||
                selectedButton.dataset.locked === '1'
            ) {
                return;
            }

            selectedButton.dataset.locked = '1';

            setOptionsDisabled(true);

            try {
                const response =
                    await fetch(
                        @json(route('student.roulette.answer')),
                        {
                            method: 'POST',
                            headers: {
                                'Content-Type':
                                    'application/json',

                                'Accept':
                                    'application/json',

                                'X-CSRF-TOKEN':
                                    csrfToken,
                            },

                            body: JSON.stringify({
                                roulette_id:
                                    rouletteId,

                                roulette_item_id:
                                    itemId,

                                response:
                                    optionKey,
                            }),
                        }
                    );

                const data =
                    await response.json();

                if (!response.ok) {
                    throw new Error(
                        data.error ||
                        'No se pudo registrar la respuesta.'
                    );
                }

                /*
                 * Protección contra respuestas duplicadas.
                 */
                if (data.already_answered) {
                    awaitingAnswer = false;

                    setOptionsDisabled(true);

                    answerFeedback.textContent =
                        'Esta pregunta ya había sido respondida.';

                    answerFeedback.className =
                        'answer-feedback visible bad';

                    setTimeout(() => {
                        closeQuestionModal();

                        spinBtn.disabled =
                            false;

                        gameStatusEl.textContent =
                            'En progreso';

                        spinHint.textContent =
                            'Pulsa GIRAR para continuar';
                    }, 900);

                    return;
                }

                /*
                 * Actualizar progreso.
                 */
                answered =
                    data.answered ??
                    (answered + 1);

                /*
                 * IMPORTANTE:
                 *
                 * data.score =
                 * puntos internos de la pregunta.
                 *
                 * data.participation_score =
                 * score normalizado de Participation.
                 */
                if (
                    data.participation_score !==
                    undefined
                ) {
                    points =
                        data.participation_score;
                }

                /*
                 * Si por alguna razón el backend no
                 * devuelve participation_score, no
                 * modificamos el score con los puntos
                 * internos.
                 */
                updateProgress();

                const buttons =
                    Array.from(
                        optionsBox
                            .querySelectorAll(
                                '.option-btn'
                            )
                    );

                if (data.is_correct) {
                    /*
                     * SOLO la opción seleccionada
                     * se marca como correcta.
                     */
                    selectedButton.classList.add(
                        'correct'
                    );

                    answerFeedback.textContent =
                        `¡Correcto! +${data.score} pts internos`;

                    answerFeedback.className =
                        'answer-feedback visible ok';

                } else {
                    /*
                     * La seleccionada es roja.
                     */
                    selectedButton.classList.add(
                        'wrong'
                    );

                    /*
                     * La correcta es verde.
                     */
                    buttons.forEach(
                        (button) => {
                            if (
                                button.dataset.opt ===
                                data.correct_option
                            ) {
                                button.classList.add(
                                    'correct'
                                );
                            }
                        }
                    );

                    answerFeedback.textContent =
                        data.error ||
                        `Respuesta incorrecta. La correcta es ${String(data.correct_option).toUpperCase()}.`;

                    answerFeedback.className =
                        'answer-feedback visible bad';
                }

                awaitingAnswer = false;

                /*
                 * Dejamos el resultado visible un
                 * instante dentro del modal.
                 *
                 * El modal NO se puede cerrar antes.
                 */
                await new Promise(
                    (resolve) =>
                        setTimeout(resolve, 1000)
                );

                if (data.completed) {
                    closeQuestionModal();

                    finishGame();

                    return;
                }

                /*
                 * Ahora sí desaparece la pregunta.
                 *
                 * drawWheel() ya eliminó un segmento
                 * porque answered aumentó.
                 */
                closeQuestionModal();

                spinBtn.disabled = false;

                gameStatusEl.textContent =
                    'En progreso';

                spinHint.textContent =
                    'Pulsa GIRAR para continuar';

            } catch (error) {
                delete selectedButton.dataset.locked;

                setOptionsDisabled(false);

                answerFeedback.textContent =
                    error.message ||
                    'Error de conexión. Intenta nuevamente.';

                answerFeedback.className =
                    'answer-feedback visible bad';
            }
        }

        /* =========================================================
           SPIN
        ========================================================== */

        async function spin() {
            if (
                spinning ||
                awaitingAnswer ||
                answered >= totalItems
            ) {
                return;
            }

            spinning = true;

            spinBtn.disabled = true;

            gameStatusEl.textContent =
                'Girando';

            spinHint.classList.add(
                'spinning'
            );

            spinHint.textContent =
                'La ruleta está girando...';

            try {
                /*
                 * El backend decide qué pregunta pendiente
                 * corresponde al giro.
                 */
                const response =
                    await fetch(
                        @json(route('student.roulette.spin')),
                        {
                            method: 'POST',

                            headers: {
                                'Content-Type':
                                    'application/json',

                                'Accept':
                                    'application/json',

                                'X-CSRF-TOKEN':
                                    csrfToken,
                            },

                            body: JSON.stringify({
                                activity_id:
                                    activityId,
                            }),
                        }
                    );

                if (!response.ok) {
                    throw new Error(
                        'No se pudo obtener una pregunta.'
                    );
                }

                const data =
                    await response.json();

                if (
                    data.answered !==
                    undefined
                ) {
                    answered =
                        data.answered;
                }

                updateProgress();

                /*
                 * Ya no quedan preguntas.
                 */
                if (
                    data.completed ||
                    (
                        data.item === null &&
                        answered >= totalItems
                    )
                ) {
                    finishGame();

                    return;
                }

                if (!data.item) {
                    throw new Error(
                        'No hay preguntas pendientes.'
                    );
                }

                /*
                 * Animación puramente visual.
                 *
                 * La pregunta real la determina el
                 * backend. La ruleta representa el azar,
                 * pero no expone el ID de la pregunta.
                 */
                const turns =
                    5 +
                    Math.floor(
                        Math.random() * 3
                    );

                const landing =
                    Math.floor(
                        Math.random() * 360
                    );

                wheelRotation +=
                    turns * 360 +
                    landing;

                wheel.style.transform =
                    `rotate(${wheelRotation}deg)`;

                /*
                 * Esperar a que termine exactamente la
                 * animación antes de abrir el modal.
                 */
                await new Promise(
                    (resolve) =>
                        setTimeout(
                            resolve,
                            4600
                        )
                );

                spinning = false;

                /*
                 * El modal permanece abierto hasta
                 * que se responda.
                 */
                openQuestionModal(
                    data.item
                );

                spinHint.classList.remove(
                    'spinning'
                );

                spinHint.textContent =
                    'Responde la pregunta para continuar';

                gameStatusEl.textContent =
                    'Responde';

            } catch (error) {
                spinning = false;

                spinBtn.disabled = false;

                spinHint.classList.remove(
                    'spinning'
                );

                spinHint.textContent =
                    'Pulsa GIRAR para intentarlo nuevamente';

                gameStatusEl.textContent =
                    answered > 0
                        ? 'En progreso'
                        : 'Listo';

                /*
                 * Usamos el mismo feedback del modal
                 * solamente si el modal está abierto.
                 * Normalmente este error ocurre antes
                 * de abrirlo, por lo que usamos un hint.
                 */
                alert(
                    error.message ||
                    'Error de conexión. Intenta nuevamente.'
                );
            }
        }

        /* =========================================================
           FINISH
        ========================================================== */

        function finishGame() {
            spinning = false;
            awaitingAnswer = false;

            spinBtn.disabled = true;

            gameStatusEl.textContent =
                'Completado';

            spinHint.textContent =
                'Actividad terminada';

            finishEl.textContent =
                '¡Completaste la actividad! Mostrando tu resultado...';

            finishEl.classList.add(
                'visible'
            );

            updateProgress();

            setTimeout(() => {
                window.location.href =
                    @json(route(
                        'student.participation.result',
                        $activity->id
                    ));
            }, 1400);
        }

        /* =========================================================
           EVENTS
        ========================================================== */

        spinBtn.addEventListener(
            'click',
            spin
        );

        /*
         * No cerramos el modal al hacer click fuera.
         *
         * Tampoco existe botón "X".
         *
         * El estudiante debe responder.
         */

        modal.addEventListener(
            'click',
            (event) => {
                if (
                    event.target === modal &&
                    awaitingAnswer
                ) {
                    event.preventDefault();
                }
            }
        );

        /*
         * Bloquear ESC mientras la pregunta está pendiente.
         * Así tampoco puede saltarse la pregunta con teclado.
         */
        document.addEventListener(
            'keydown',
            (event) => {
                if (
                    event.key === 'Escape' &&
                    modal.classList.contains('visible')
                ) {
                    event.preventDefault();
                }
            }
        );

        /* =========================================================
           INITIAL STATE
        ========================================================== */

        updateProgress();

        /*
         * Si al cargar ya no quedan preguntas,
         * se muestra el resultado automáticamente.
         */
        if (
            totalItems > 0 &&
            answered >= totalItems
        ) {
            finishGame();
        }
    </script>

</body>

</html>
