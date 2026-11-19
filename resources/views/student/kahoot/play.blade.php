<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $activity->title }}</title>

    @include('partials.assets', ['theme' => 'student', 'includeBootstrap' => false])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; }

        /* === Pantallas (JS alterna display) === */
        #screen-question, #screen-result, #screen-finish { display: none; }
        #error-msg { display: none; }

        /* === Internos del juego (JS modifica estas clases) === */
        #timer-text.danger { color: #dc2626; }

        #options-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
        .option-btn { display: flex; min-height: 76px; align-items: center; justify-content: center; padding: 14px; border: 2px solid transparent; border-radius: .75rem; color: #fff; font: inherit; font-size: 1rem; font-weight: 700; line-height: 1.3; text-align: center; overflow-wrap: anywhere; cursor: pointer; transition: opacity .2s; }
        .option-btn:nth-child(1) { background: #dc2626; }
        .option-btn:nth-child(2) { background: #2563eb; }
        .option-btn:nth-child(3) { background: #d97706; }
        .option-btn:nth-child(4) { background: #059669; }
        .option-btn:hover:not(:disabled) { filter: brightness(1.06); }
        .option-btn:focus-visible { outline: 3px solid #be185d; outline-offset: 3px; }
        .option-btn:disabled { cursor: default; opacity: .72; }
        .option-btn.correct { border-color: #6ee7b7; box-shadow: 0 0 0 3px rgb(5 150 105 / .25); opacity: 1; }
        .option-btn.incorrect { border-color: #fca5a5; box-shadow: 0 0 0 3px rgb(220 38 38 / .2); opacity: .6; }

        #result-icon svg { display: block; width: 4rem; height: 4rem; margin: 0 auto; }
        #result-icon .result-icon-ok { color: #059669; }
        #result-icon .result-icon-bad { color: #dc2626; }

        @media (max-width: 600px) {
            #options-grid { grid-template-columns: minmax(0, 1fr); gap: 8px; }
            .option-btn { min-height: 58px; padding: 12px; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { scroll-behavior: auto !important; transition-duration: .01ms !important; }
        }
    </style>
</head>

<body class="student-kids">
    @include('partials.toast')

    <div class="mx-auto w-full max-w-2xl px-3 py-5 sm:px-5 sm:py-8">

        {{-- Pantalla inicio --}}
        <div id="screen-start" class="rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-sm sm:p-10">
            <p class="text-[10px] font-bold uppercase tracking-widest text-pink-700 sm:text-xs">
                Actividad interactiva
            </p>
            <h1 class="mt-1.5 break-words text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                {{ $activity->title }}
            </h1>
            <p class="mx-auto mt-2 max-w-md text-sm font-semibold text-slate-500">
                {{ count($questions) }} preguntas · Lee cada pregunta y elige una opción antes de que termine el tiempo.
            </p>
            <button id="btn-start" onclick="startGame()"
                class="mt-6 inline-flex min-h-12 items-center justify-center rounded-xl bg-pink-700 px-8 text-base font-bold text-white shadow-sm transition hover:bg-pink-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-pink-500 focus-visible:ring-offset-2">
                ¡A jugar!
            </button>
        </div>

        {{-- Pantalla pregunta --}}
        <div id="screen-question" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">

            <div id="progress-bar-wrap" class="h-2 overflow-hidden rounded-full bg-pink-100">
                <div id="progress-bar" class="h-full rounded-full bg-pink-700" style="width: 0%"></div>
            </div>
            <p id="question-counter" class="mt-2 text-xs font-bold text-slate-500"></p>

            <div id="timer-wrap" class="mt-3 flex items-center gap-3 rounded-xl bg-slate-50 px-3 py-2 ring-1 ring-slate-200">
                <div id="timer-bar-wrap" class="h-2 flex-1 overflow-hidden rounded-full bg-slate-200">
                    <div id="timer-bar" class="h-full rounded-full" style="width: 100%"></div>
                </div>
                <div id="timer-text" class="min-w-[2.5rem] text-right font-mono text-lg font-bold tabular-nums text-pink-700"></div>
            </div>

            <div id="question-text" class="mt-4 flex min-h-28 items-center justify-center rounded-xl bg-pink-50 p-5 text-center text-lg font-bold leading-snug text-slate-900 ring-1 ring-pink-100 sm:min-h-32 sm:text-xl"></div>

            <div id="options-grid" class="mt-3"></div>

            <div id="error-msg" class="mt-3 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-center text-sm font-bold text-red-700"></div>

            <div class="mt-5 flex justify-center border-t border-slate-200 pt-4 sm:justify-end">
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

        {{-- Pantalla resultado por pregunta --}}
        <div id="screen-result" class="rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-sm sm:p-8">
            <div id="result-icon"></div>
            <div id="result-text" class="mt-2 text-2xl font-bold text-slate-900"></div>
            <div id="result-score" class="mt-1 text-lg font-bold text-pink-700"></div>
            <div id="result-correct-text" class="mt-1 text-sm font-semibold text-slate-500"></div>
            <button id="btn-next" onclick="nextQuestion()"
                class="mt-6 inline-flex min-h-12 min-w-48 items-center justify-center rounded-xl bg-pink-700 px-8 text-base font-bold text-white shadow-sm transition hover:bg-pink-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-pink-500 focus-visible:ring-offset-2">
                Siguiente
            </button>
        </div>

        {{-- Pantalla final --}}
        <div id="screen-finish" class="rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-sm sm:p-10">
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                ¡Increíble!
            </h1>
            <div id="final-score" class="mt-4 font-mono text-6xl font-extrabold tabular-nums text-pink-700 sm:text-7xl"></div>
            <p class="mt-1 text-sm font-semibold text-slate-500">
                Tus puntos
            </p>
            <a id="btn-back" href="{{ route('student.activities.show', $activity->id) }}"
                class="mt-6 inline-flex min-h-12 items-center justify-center rounded-xl border border-slate-200 bg-white px-8 text-base font-bold text-pink-700 shadow-sm transition hover:bg-pink-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-pink-500 focus-visible:ring-offset-2">
                ¡Jugar otra vez!
            </a>
        </div>

    </div>

    <script src="/js/klassio-sounds.js"></script>
    <script>
        window.KlassioSounds?.setup('kahoot');
        // -------------------------------------------------------------------------
        // Datos del servidor
        // -------------------------------------------------------------------------
        const PARTICIPATION_ID = {{ $participation->id }};
        const CSRF_TOKEN = '{{ csrf_token() }}';
        const ANSWER_URL = '{{ route('student.kahoot.answer') }}';
        const QUESTIONS = @json($questions);
        const ANSWERED_IDS = @json($answeredIds);

        // -------------------------------------------------------------------------
        // Estado
        // -------------------------------------------------------------------------
        let currentIndex = 0;
        let totalScore = 0;
        let timerInterval = null;
        let timeLeft = 0;
        let answered = false;

        // Saltar preguntas ya respondidas
        while (currentIndex < QUESTIONS.length && ANSWERED_IDS.includes(QUESTIONS[currentIndex].id)) {
            currentIndex++;
        }

        // -------------------------------------------------------------------------
        // Inicio
        // -------------------------------------------------------------------------
        function startGame() {
            window.KlassioSounds?.click();
            if (currentIndex >= QUESTIONS.length) {
                showFinish();
                return;
            }
            window.KlassioSounds?.startMusic();
            document.getElementById('screen-start').style.display = 'none';
            document.getElementById('screen-question').style.display = 'block';
            showQuestion(currentIndex);
        }

        // -------------------------------------------------------------------------
        // Mostrar pregunta
        // -------------------------------------------------------------------------
        function showQuestion(index) {
            answered = false;
            hideError();

            const q = QUESTIONS[index];
            const total = QUESTIONS.length;

            document.getElementById('progress-bar').style.width =
                `${(index / total) * 100}%`;
            document.getElementById('question-counter').textContent =
                `Pregunta ${index + 1} de ${total}`;
            document.getElementById('question-text').textContent = q.question;

            const grid = document.getElementById('options-grid');
            grid.replaceChildren();
            q.options.forEach(opt => {
                const btn = document.createElement('button');
                btn.classList.add('option-btn');
                btn.textContent = opt.text;
                btn.dataset.optionId = opt.id;
                btn.onclick = () => {
                    window.KlassioSounds?.click();
                    submitAnswer(opt.id, q.id);
                };
                grid.appendChild(btn);
            });

            startTimer(q.time_limit, q.id);
        }

        // -------------------------------------------------------------------------
        // Temporizador
        // -------------------------------------------------------------------------
        function startTimer(seconds, questionId) {
            clearInterval(timerInterval);
            timeLeft = seconds;

            const timerText = document.getElementById('timer-text');
            const timerBar = document.getElementById('timer-bar');

            timerText.textContent = timeLeft;
            timerText.classList.remove('danger');
            timerBar.style.width = '100%';
            timerBar.style.background = '#34d399';

            timerInterval = setInterval(() => {
                timeLeft--;

                timerText.textContent = timeLeft;
                timerBar.style.width = `${(timeLeft / seconds) * 100}%`;

                if (timeLeft <= 5) {
                    timerText.classList.add('danger');
                    timerBar.style.background = '#f87171';
                }

                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    if (!answered) {
                        timeExpired();
                    }
                }
            }, 1000);
        }

        // -------------------------------------------------------------------------
        // Tiempo expirado
        // -------------------------------------------------------------------------
        function timeExpired() {
            answered = true;
            disableOptions();
            showResult(false, 0, null, '¡Se acabó el tiempo!');
        }

        // -------------------------------------------------------------------------
        // Enviar respuesta — con manejo completo de errores HTTP
        // -------------------------------------------------------------------------
        async function submitAnswer(optionId, questionId) {
            if (answered) return;
            answered = true;
            clearInterval(timerInterval);
            disableOptions();
            hideError();

            let res, result;

            try {
                res = await fetch(ANSWER_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        participation_id: PARTICIPATION_ID,
                        question_id: questionId,
                        option_id: optionId,
                    }),
                });
            } catch (networkErr) {
                // Error de red (sin conexión, timeout, etc.)
                showError('Error de conexión. Intenta de nuevo.');
                answered = false;
                enableOptions();
                return;
            }

            try {
                result = await res.json();
            } catch (parseErr) {
                showError('Respuesta inesperada del servidor.');
                answered = false;
                enableOptions();
                return;
            }

            // Manejar errores HTTP (4xx, 5xx)
            if (!res.ok) {
                if (res.status === 409) {
                    // Ya fue respondida — avanzar silenciosamente
                    nextQuestion();
                    return;
                }

                if (res.status === 403) {
                    showError('No tienes permiso para responder esta pregunta.');
                    return;
                }

                // Cualquier otro error del servidor
                const msg = result?.message || result?.error || 'Error del servidor.';
                showError(msg);
                return;
            }

            // Respuesta exitosa — resaltar opciones
            window.KlassioSounds?.[result.is_correct ? 'correcto' : 'error']();
            if (result.completed) window.KlassioSounds?.completado();
            const buttons = document.querySelectorAll('.option-btn');
            buttons.forEach(btn => {
                const bid = parseInt(btn.dataset.optionId);
                if (bid === result.correct_option_id) {
                    btn.classList.add('correct');
                } else if (bid === optionId && !result.is_correct) {
                    btn.classList.add('incorrect');
                }
            });

            if (result.is_correct) {
                totalScore += result.score;
            }

            showResult(
                result.is_correct,
                result.score,
                result.correct_option_id,
                null
            );

            /*
             * Si fue la última pregunta,
             * mostrar brevemente el resultado y después
             * redirigir a la página de resultados.
             */
            if (result.completed) {

                setTimeout(() => {

                    window.location.href = @json(route('student.participation.result', $activity->id));

                }, 1200);

                return;
            }
        }

        // -------------------------------------------------------------------------
        // Helpers de opciones
        // -------------------------------------------------------------------------
        function disableOptions() {
            document.querySelectorAll('.option-btn').forEach(btn => btn.disabled = true);
        }

        function enableOptions() {
            document.querySelectorAll('.option-btn').forEach(btn => btn.disabled = false);
        }

        // -------------------------------------------------------------------------
        // Helpers de error inline
        // -------------------------------------------------------------------------
        function showError(msg) {
            const el = document.getElementById('error-msg');
            el.textContent = msg;
            el.style.display = 'block';
        }

        function hideError() {
            const el = document.getElementById('error-msg');
            el.style.display = 'none';
            el.textContent = '';
        }

        // -------------------------------------------------------------------------
        // Mostrar resultado de la pregunta
        // -------------------------------------------------------------------------
        function showResult(isCorrect, score, correctOptionId, overrideMessage) {
            document.getElementById('screen-question').style.display = 'none';
            document.getElementById('screen-result').style.display = 'block';

            document.getElementById('result-icon').innerHTML =
                isCorrect
                    ? '<svg class="result-icon-ok" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>'
                    : '<svg class="result-icon-bad" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>';

            document.getElementById('result-text').textContent =
                overrideMessage ?? (isCorrect ? '¡Correcto!' : '¡Casi!');

            document.getElementById('result-score').textContent =
                isCorrect ? `+${score} puntos` : 'Sin puntos';

            document.getElementById('result-correct-text').textContent =
                `Llevas: ${totalScore} puntos`;

            const remaining = QUESTIONS.slice(currentIndex + 1)
                .filter(q => !ANSWERED_IDS.includes(q.id));

            document.getElementById('btn-next').textContent = remaining.length > 0 ?
                'Siguiente' :
                'Ver mi puntaje';
        }

        // -------------------------------------------------------------------------
        // Siguiente pregunta
        // -------------------------------------------------------------------------
        function nextQuestion() {
            window.KlassioSounds?.click();
            currentIndex++;

            while (currentIndex < QUESTIONS.length && ANSWERED_IDS.includes(QUESTIONS[currentIndex].id)) {
                currentIndex++;
            }

            document.getElementById('screen-result').style.display = 'none';

            if (currentIndex >= QUESTIONS.length) {
                showFinish();
                return;
            }

            document.getElementById('screen-question').style.display = 'block';
            showQuestion(currentIndex);
        }

        // -------------------------------------------------------------------------
        // Pantalla final
        // -------------------------------------------------------------------------
        function showFinish() {
            document.getElementById('screen-start').style.display = 'none';
            document.getElementById('screen-question').style.display = 'none';
            document.getElementById('screen-result').style.display = 'none';
            document.getElementById('screen-finish').style.display = 'block';
            document.getElementById('final-score').textContent = totalScore;
        }
    </script>

</body>

</html>
