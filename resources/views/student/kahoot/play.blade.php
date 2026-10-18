<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $activity->title }}</title>
    <style>
        :root { --kh-ink: #183b3a; --kh-muted: #587371; --kh-line: #d7e8e4; --kh-deep: #075e58; --kh-lime: #c6f36a; }
        * { box-sizing: border-box; }
        a { text-decoration: none; }
        body.student-kids { min-height: 100vh; margin: 0; padding: 22px 16px; color: var(--kh-ink); background: radial-gradient(circle at 8% 0%, #d9f3e8 0, transparent 31%), #eff6f2; font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; font-size: 17px; }
        #screen-start, #screen-question, #screen-result, #screen-finish { width: min(760px, 100%); margin: 0 auto; padding: 24px; border: 1px solid var(--kh-line); border-radius: 24px; background: #fff; box-shadow: 0 24px 64px rgb(23 69 60 / .12); }
        #screen-start { margin-top: clamp(28px, 9vh, 90px); padding: clamp(28px, 6vw, 54px); text-align: center; }
        #screen-start::before { content: "ACTIVIDAD INTERACTIVA"; display: inline-flex; margin-bottom: 13px; color: var(--kh-deep); font-size: .7rem; font-weight: 850; letter-spacing: .13em; }
        #screen-start h1, #screen-finish h1 { margin: 0; color: var(--kh-ink); font-size: clamp(1.7rem, 4vw, 2.35rem); line-height: 1.15; overflow-wrap: anywhere; }
        #screen-start p { margin: 11px 0 20px; color: var(--kh-muted); }
        #btn-start, #btn-next, #btn-back { display: inline-flex; align-items: center; justify-content: center; min-height: 48px; padding: 12px 24px; border: 0; border-radius: 12px; background: var(--kh-deep); color: #fff; font: inherit; font-size: 1rem; font-weight: 850; cursor: pointer; transition: background .15s, transform .15s, box-shadow .15s; }
        #btn-start:hover, #btn-next:hover, #btn-back:hover { transform: translateY(-1px); background: #064e49; box-shadow: 0 8px 18px rgb(7 94 88 / .2); }
        #screen-question { display: none; }
        #progress-bar-wrap { height: 8px; margin-bottom: 10px; overflow: hidden; border-radius: 999px; background: #e4efeb; }
        #progress-bar { height: 100%; border-radius: inherit; background: #07877b; transition: width .3s; }
        #question-counter { margin-bottom: 8px; color: var(--kh-muted); font-size: .82rem; font-weight: 800; }
        #timer-wrap { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; padding: 9px 12px; border: 1px solid var(--kh-line); border-radius: 13px; background: #f5faf7; }
        #timer-bar-wrap { flex: 1; height: 9px; overflow: hidden; border-radius: 999px; background: #dce9e4; }
        #timer-bar { height: 100%; border-radius: inherit; transition: width 1s linear; }
        #timer-text { min-width: 34px; color: var(--kh-deep); font-size: 1.1rem; font-weight: 900; font-variant-numeric: tabular-nums; text-align: right; }
        #timer-text.danger { color: #b42318; }
        #question-text { display: flex; min-height: 112px; align-items: center; justify-content: center; margin-bottom: 16px; padding: clamp(18px, 4vw, 28px); border: 1px solid #d8e9e1; border-radius: 17px; background: linear-gradient(140deg, #f0faf5, #e7f4ed); color: var(--kh-ink); font-size: clamp(1.2rem, 3vw, 1.6rem); font-weight: 850; line-height: 1.35; text-align: center; overflow-wrap: anywhere; }
        #options-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
        .option-btn { display: flex; min-height: 74px; align-items: center; justify-content: center; padding: 14px; border: 2px solid transparent; border-radius: 14px; color: #fff; font: inherit; font-size: 1rem; font-weight: 800; line-height: 1.3; text-align: center; overflow-wrap: anywhere; cursor: pointer; transition: transform .12s, box-shadow .15s, opacity .2s; }
        .option-btn:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 8px 18px rgb(24 59 58 / .16); }
        .option-btn:focus-visible, #btn-start:focus-visible, #btn-next:focus-visible, #btn-back:focus-visible, #screen-question form button:focus-visible { outline: 3px solid #f59e0b; outline-offset: 3px; }
        .option-btn:disabled { cursor: default; opacity: .72; }
        .option-btn:nth-child(1) { background: #b83c37; }
        .option-btn:nth-child(2) { background: #2865a4; }
        .option-btn:nth-child(3) { background: #98700a; }
        .option-btn:nth-child(4) { background: #247346; }
        .option-btn.correct { border-color: #72d49a; box-shadow: 0 0 0 3px rgb(48 145 82 / .2); opacity: 1; }
        .option-btn.incorrect { border-color: #f1aaa4; box-shadow: 0 0 0 3px rgb(189 49 43 / .16); opacity: .58; }
        #error-msg { display: none; margin-top: 12px; padding: 11px 13px; border: 1px solid #e9b7b3; border-radius: 11px; background: #fff1ef; color: #8c302a; font-size: .9rem; text-align: center; }
        #screen-question form { margin-top: 16px !important; text-align: right !important; }
        #screen-question form button { min-height: 40px; padding: 8px 12px; border: 1px solid #e9c7c6; border-radius: 10px; background: #fff6f4; color: #9b3a35; font: inherit; font-size: .84rem; font-weight: 750; cursor: pointer; }
        #screen-question form button:hover { background: #ffede9; }
        #screen-result { display: none; margin-top: clamp(24px, 8vh, 76px); padding: clamp(24px, 5vw, 42px); text-align: center; }
        #result-icon { margin-bottom: 10px; font-size: 3.4rem; }
        #result-text { margin-bottom: 8px; color: var(--kh-ink); font-size: 1.55rem; font-weight: 900; }
        #result-score { margin-bottom: 8px; color: var(--kh-deep); font-size: 1.15rem; font-weight: 850; }
        #result-correct-text { margin-bottom: 22px; color: var(--kh-muted); font-size: .95rem; }
        #btn-next { min-width: 190px; }
        #screen-finish { display: none; margin-top: clamp(28px, 9vh, 90px); padding: clamp(28px, 6vw, 54px); text-align: center; }
        #final-score { margin: 18px 0 8px; color: var(--kh-deep); font-size: clamp(2.8rem, 9vw, 4.4rem); font-weight: 950; font-variant-numeric: tabular-nums; }
        #screen-finish p { margin: 0; color: var(--kh-muted); }
        #btn-back { margin-top: 24px; }
        .klassio-sound-btn, .klassio-sound-fab { border: 1px solid #bfd9d0 !important; background: #fff !important; color: var(--kh-deep) !important; }
        body.student-kids #question-text { font-size: clamp(1.25rem, 3vw, 1.65rem); }
        body.student-kids .option-btn { min-height: 82px; padding: 17px 14px; font-size: 1.08rem; }
        body.student-kids #timer-text { font-size: 1.35rem; }
        body.student-kids #result-text { font-size: 1.7rem; }
        @media (max-width: 600px) {
            body.student-kids { padding: 10px; font-size: 16px; }
            #screen-start, #screen-question, #screen-result, #screen-finish { padding: 17px; border-radius: 19px; }
            #screen-start, #screen-result, #screen-finish { margin-top: 5vh; }
            #options-grid { grid-template-columns: minmax(0, 1fr); gap: 8px; }
            .option-btn, body.student-kids .option-btn { min-height: 58px; padding: 12px; font-size: 1rem; }
            #question-text { min-height: 92px; }
            #screen-question form { text-align: center !important; }
            #screen-question form button { width: 100%; min-height: 44px; }
        }
        @media (prefers-reduced-motion: reduce) { *, *::before, *::after { scroll-behavior: auto !important; transition-duration: .01ms !important; } }
    </style>
</head>

<body class="student-kids">
    @include('partials.toast')

    {{-- Pantalla inicio --}}
    <div id="screen-start">
        <h1>🎯 {{ $activity->title }}</h1>
        <p>{{ count($questions) }} preguntas · Lee cada pregunta y elige una opción antes de que termine el tiempo.</p>
        <button id="btn-start" onclick="startGame()">¡A jugar! 🎮</button>
    </div>

    {{-- Pantalla pregunta --}}
    <div id="screen-question">
        <div id="progress-bar-wrap">
            <div id="progress-bar" style="width: 0%"></div>
        </div>
        <div id="question-counter"></div>

        <div id="timer-wrap">
            <div id="timer-bar-wrap">
                <div id="timer-bar" style="width: 100%"></div>
            </div>
            <div id="timer-text"></div>
        </div>

        <div id="question-text"></div>
        <div id="options-grid"></div>
        <div id="error-msg"></div>

        <form method="POST" action="{{ route('student.participation.abandon', $activity->id) }}"
            onsubmit="return confirm('¿Seguro que quieres dejar el juego?');"
            style="margin-top: 1.5rem; text-align: center;">
            @csrf

            <button type="submit">
                Dejar el juego 👋
            </button>
        </form>
    </div>

    {{-- Pantalla resultado por pregunta --}}
    <div id="screen-result">
        <div id="result-icon"></div>
        <div id="result-text"></div>
        <div id="result-score"></div>
        <div id="result-correct-text"></div>
        <button id="btn-next" onclick="nextQuestion()">Siguiente pregunta →</button>
    </div>

    {{-- Pantalla final --}}
    <div id="screen-finish">
        <h1>¡Increíble! 🎉</h1>
        <div id="final-score"></div>
        <p>Tus puntos ⭐</p>
        <a id="btn-back" href="{{ route('student.activities.show', $activity->id) }}">
            ¡Jugar otra vez! 🎮
        </a>
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
            showResult(false, 0, null, '⏰ ¡Se acabó el tiempo!');
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

            document.getElementById('result-icon').textContent =
                isCorrect ? '🎉' : '❌';

            document.getElementById('result-text').textContent =
                overrideMessage ?? (isCorrect ? '¡Bien! 🎉' : '¡Casi! 😅');

            document.getElementById('result-score').textContent =
                isCorrect ? `+${score} puntos ⭐` : 'Sin puntos';

            document.getElementById('result-correct-text').textContent =
                `Llevas: ${totalScore} ⭐`;

            const remaining = QUESTIONS.slice(currentIndex + 1)
                .filter(q => !ANSWERED_IDS.includes(q.id));

            document.getElementById('btn-next').textContent = remaining.length > 0 ?
                'Siguiente 👉' :
                'Ver mi puntaje ⭐';
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
