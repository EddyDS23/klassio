<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Ruleta — {{ $activity->title }}</title>

    @include('partials.assets', ['theme' => 'student', 'includeBootstrap' => false])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; }

        /* === Internos del juego (JS modifica estas clases) === */
        #timer-value.warning { color: #fde68a; }
        #timer-value.danger { color: #fecaca; }

        .progress-track { height: 9px; overflow: hidden; border-radius: 999px; background: #fce7f3; }
        .progress-fill { width: 0%; height: 100%; border-radius: inherit; background: #be185d; transition: width .35s ease; }

        .wheel-stage { position: relative; width: min(410px, 82vw); aspect-ratio: 1; display: grid; place-items: center; margin: 0 auto; }
        .wheel-shadow { position: absolute; inset: 7%; border-radius: 50%; background: rgb(190 24 93 / .2); filter: blur(24px); transform: translateY(15px); }
        .pointer { position: absolute; top: -2px; left: 50%; z-index: 20; width: 0; height: 0; transform: translateX(-50%); border-left: 19px solid transparent; border-right: 19px solid transparent; border-top: 33px solid #9d174d; filter: drop-shadow(0 5px 4px rgb(0 0 0 / .22)); }
        .pointer::after { content: ""; position: absolute; left: -9px; top: -32px; width: 18px; height: 9px; border-radius: 999px; background: #fce7f3; }
        .wheel-border { position: relative; z-index: 5; width: 100%; height: 100%; padding: 11px; border-radius: 50%; background: #be185d; box-shadow: 0 22px 42px rgb(190 24 93 / .25), inset 0 0 0 2px rgb(255 255 255 / .25); }
        .wheel { position: relative; width: 100%; height: 100%; overflow: hidden; border: 5px solid #fff; border-radius: 50%; background: #fbcfe8; transform: rotate(0deg); transition: transform 4.6s cubic-bezier(.11, .82, .19, 1); will-change: transform; }
        .wheel::after { content: ""; position: absolute; inset: 0; z-index: 3; border-radius: 50%; box-shadow: inset 0 0 0 3px rgb(255 255 255 / .5), inset 0 0 35px rgb(157 23 77 / .18); pointer-events: none; }
        .wheel-center { position: absolute; top: 50%; left: 50%; z-index: 30; width: 104px; height: 104px; transform: translate(-50%, -50%); border: 8px solid #fff; border-radius: 50%; color: #fff; background: #be185d; box-shadow: 0 9px 22px rgb(190 24 93 / .3), 0 0 0 6px rgb(190 24 93 / .17); font-size: .9rem; font-weight: 800; letter-spacing: .08em; cursor: pointer; transition: transform .15s ease, filter .15s ease; -webkit-tap-highlight-color: transparent; }
        .wheel-center:hover:not(:disabled) { transform: translate(-50%, -50%) scale(1.05); filter: brightness(1.08); }
        .wheel-center:active:not(:disabled) { transform: translate(-50%, -50%) scale(.98); }
        .wheel-center:disabled { cursor: default; opacity: .65; }
        button.wheel-center { border-radius: 50%; }
        button.option-btn { border-radius: .9rem; }
        .spin-hint { min-height: 22px; margin-top: 16px; color: #64748b; font-size: .88rem; font-weight: 600; text-align: center; }
        .spin-hint.spinning { color: #be185d; }

        .modal-backdrop { position: fixed; inset: 0; z-index: 1000; display: none; align-items: center; justify-content: center; padding: 18px; background: rgb(80 7 36 / .68); }
        .modal-backdrop.visible { display: flex; }
        .question-modal { width: min(700px, 100%); max-height: min(760px, calc(100vh - 36px)); overflow: auto; border-radius: 1.25rem; background: #fff; box-shadow: 0 30px 90px rgb(0 0 0 / .28); }
        .modal-top { padding: 18px 20px; color: #fff; background: #be185d; }
        .modal-top-row { display: flex; align-items: center; justify-content: space-between; gap: 15px; }
        .modal-label { margin: 0; font-size: .72rem; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; opacity: .8; }
        #modal-points { padding: 5px 10px; border: 1px solid rgb(255 255 255 / .25); border-radius: 999px; background: rgb(255 255 255 / .12); font-size: .76rem; font-weight: 700; }
        .modal-body { padding: 22px; }
        #question-text { margin: 0 0 20px; color: #1e293b; font-size: clamp(1.2rem, 3vw, 1.5rem); font-weight: 800; line-height: 1.35; }
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
        .answer-feedback { display: none; margin-top: 16px; padding: 12px 14px; border-radius: .8rem; font-size: .9rem; font-weight: 700; text-align: center; }
        .answer-feedback.visible { display: block; }
        .answer-feedback.ok { color: #166534; border: 1px solid #86efac; background: #dcfce7; }
        .answer-feedback.bad { color: #991b1b; border: 1px solid #fca5a5; background: #fee2e2; }
        .modal-note { margin: 16px 0 0; color: #64748b; font-size: .76rem; text-align: center; }

        .finish { display: none; margin-top: 1rem; padding: 14px 18px; border: 1px solid #a7f3d0; border-radius: .8rem; background: #ecfdf5; color: #065f46; font-weight: 700; text-align: center; }
        .finish.visible { display: block; }

        @media (max-width: 460px) {
            .wheel-stage { width: min(300px, 84vw); }
            .wheel-center { width: 84px; height: 84px; font-size: .78rem; }
            .pointer { border-left-width: 16px; border-right-width: 16px; border-top-width: 28px; }
            .option-btn { padding: 11px; }
            .option-key { flex-basis: 36px; width: 36px; height: 36px; }
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
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                    </div>

                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-pink-200 sm:text-xs">
                            Actividad interactiva
                        </p>
                        <h1 class="mt-0.5 text-lg font-bold tracking-tight sm:mt-1 sm:text-2xl">
                            Ruleta
                        </h1>
                        <p class="mt-0.5 break-words text-xs font-semibold text-pink-100 sm:text-sm">
                            {{ $activity->title }}
                        </p>
                    </div>

                </div>

                <div class="shrink-0 rounded-xl bg-white/15 px-3 py-2 text-center ring-1 ring-white/25" aria-label="Tiempo restante">
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-pink-100">
                        Tiempo
                    </span>
                    <div class="font-mono text-lg font-bold tabular-nums text-white sm:text-xl">
                        <span id="timer-value">--:--</span>
                    </div>
                </div>

            </div>

        </header>

        {{-- Resumen --}}
        <p class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-3 sm:gap-3" aria-label="Resumen de la actividad">

            <span class="inline-flex min-w-0 items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm shadow-sm sm:justify-start sm:px-4">
                <svg class="h-4 w-4 shrink-0 text-pink-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0-3.375-6.375m-7.125 0L6 18.75m4.875-9.75L9.75 2.25h4.5l-1.125 6.75h-2.25Z" />
                </svg>
                <span class="font-semibold text-slate-500">Puntos</span>
                <span class="font-bold text-pink-700"><span id="points">{{ $earnedPoints }}</span> / {{ $maxScore }}</span>
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
                <span id="game-status" class="font-bold text-slate-700">Listo</span>
            </span>

        </p>

        {{-- Progreso --}}
        <section class="mt-3 rounded-2xl border border-slate-200 bg-white p-3.5 shadow-sm sm:p-4" aria-label="Progreso de la actividad">
            <div class="flex items-center justify-between gap-2 text-xs font-bold text-slate-600 sm:text-sm">
                <span>Progreso de la actividad</span>
                <span id="progress-percent" class="font-mono tabular-nums text-pink-700">0%</span>
            </div>
            <div class="progress-track mt-2">
                <div id="progress-fill" class="progress-fill"></div>
            </div>
        </section>

        {{-- Cómo jugar --}}
        <p class="mt-3 flex items-start gap-2 rounded-xl bg-pink-50 px-3.5 py-2.5 text-sm text-pink-900 ring-1 ring-pink-100">
            <svg class="mt-0.5 h-4 w-4 shrink-0 text-pink-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 0 4.51-2.104m0 0A9.04 9.04 0 0 0 12 3a9.04 9.04 0 0 0-6.76 12.986M3.75 19.5a12.06 12.06 0 0 1 4.51-2.104m0 0a6.01 6.01 0 0 0 1.5-.189m0 0a6.01 6.01 0 0 1 1.5-.189" />
            </svg>
            <span><strong class="font-bold">Cómo jugar:</strong> pulsa GIRAR para descubrir una pregunta. Respóndela para retirar ese segmento de la ruleta y continuar con las restantes.</span>
        </p>

        {{-- Ruleta --}}
        <section class="mt-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6" aria-label="Ruleta del juego">

            <p class="text-center text-sm font-semibold text-slate-500">
                <strong id="remaining-count" class="font-bold text-pink-700">{{ $totalItems - $answeredCount }}</strong>
                <span id="remaining-text">preguntas restantes</span>
            </p>

            <div class="wheel-stage mt-4">
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

        </section>

        {{-- Fin de la partida --}}
        <div id="finish" class="finish" role="status" aria-live="polite"></div>

        <div class="mt-4 flex justify-end border-t border-slate-200 pt-4">
            <form method="POST" action="{{ route('student.participation.abandon', $activity->id) }}"
                onsubmit="return confirm('¿Seguro que quieres dejar el juego?');">
                @csrf
                <button type="submit"
                    class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-red-200 bg-white px-4 py-2.5 text-sm font-bold text-red-600 shadow-sm transition hover:bg-red-50 hover:text-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l3 3m0 0-3 3m3-3H9" />
                    </svg>
                    Dejar el juego
                </button>
            </form>
        </div>

    </div>

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

                    <span id="modal-points">
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

    {{-- Expiración --}}
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

                /*
                 * La actividad terminó por tiempo: mismo comportamiento que
                 * Crucigrama (expirado()). El módulo detiene la música
                 * ambiental y suena el efecto una sola vez.
                 */
                window.KlassioSounds?.expirado();

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
                    function () {
                        answerQuestion(
                            button,
                            option.key,
                            item.id
                        );
                    }
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

                    window.KlassioSounds?.correcto();

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

                    window.KlassioSounds?.error();
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

                    /*
                     * El sonido de cierre lo elige finishGame() según la
                     * puntuación (completado vs expirado). Evitamos un
                     * segundo disparo aquí.
                     */
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
            /*
             * La música ambiental se detiene aquí (endWith del módulo)
             * y el jingle de cierre depende de la puntuación:
             * con 0 puntos suena 'expirado'; con más de 0, 'completado'.
             */
            window.KlassioSounds?.[points > 0 ? 'completado' : 'expirado']();

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
            function () {
                spin();
            }
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

    <script src="/js/klassio-sounds.js"></script>
    <script>
        // Música ambiental de la ruleta (juegoruleta.mp3, 20% de volumen):
        // suena en bucle durante TODA la actividad. Si el navegador exige
        // interacción, arranca en el primer toque (listener del módulo).
        // Los efectos correcto / error / completado se disparan desde los
        // eventos reales del juego dentro del script principal.
        window.KlassioSounds?.setup('roulette');
    </script>

</body>

</html>
