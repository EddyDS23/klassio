<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $activity->title }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: sans-serif;
            background: #1e1b4b;
            color: #fff;
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        }

        #screen-start,
        #screen-question,
        #screen-result,
        #screen-finish {
            max-width: 680px;
            margin: 0 auto;
        }

        /* Pantalla de inicio */
        #screen-start {
            text-align: center;
            padding-top: 60px;
        }

        #screen-start h1 {
            font-size: 28px;
            margin-bottom: 8px;
        }

        #screen-start p {
            color: #a5b4fc;
            margin-bottom: 32px;
        }

        #btn-start {
            background: #4f46e5;
            color: #fff;
            border: none;
            padding: 14px 40px;
            font-size: 18px;
            border-radius: 8px;
            cursor: pointer;
        }

        #btn-start:hover {
            background: #4338ca;
        }

        /* Pantalla de pregunta */
        #screen-question {
            display: none;
        }

        #progress-bar-wrap {
            background: #312e81;
            border-radius: 4px;
            height: 6px;
            margin-bottom: 12px;
        }

        #progress-bar {
            height: 100%;
            background: #818cf8;
            border-radius: 4px;
            transition: width 0.3s;
        }

        #question-counter {
            font-size: 13px;
            color: #a5b4fc;
            margin-bottom: 6px;
        }

        #timer-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 16px;
        }

        #timer-bar-wrap {
            flex: 1;
            background: #312e81;
            border-radius: 4px;
            height: 10px;
        }

        #timer-bar {
            height: 100%;
            background: #34d399;
            border-radius: 4px;
            transition: width 1s linear;
        }

        #timer-text {
            font-size: 20px;
            font-weight: bold;
            min-width: 30px;
            text-align: right;
        }

        #timer-text.danger {
            color: #f87171;
        }

        #question-text {
            font-size: 22px;
            font-weight: bold;
            background: #312e81;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            min-height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #options-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .option-btn {
            padding: 18px 12px;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: transform 0.1s, opacity 0.2s;
            color: #fff;
        }

        .option-btn:hover:not(:disabled) {
            transform: scale(1.02);
        }

        .option-btn:disabled {
            cursor: default;
            opacity: 0.7;
        }

        .option-btn:nth-child(1) {
            background: #dc2626;
        }

        .option-btn:nth-child(2) {
            background: #2563eb;
        }

        .option-btn:nth-child(3) {
            background: #ca8a04;
        }

        .option-btn:nth-child(4) {
            background: #16a34a;
        }

        .option-btn.correct {
            outline: 4px solid #34d399;
            opacity: 1;
        }

        .option-btn.incorrect {
            outline: 4px solid #f87171;
            opacity: 0.5;
        }

        /* Pantalla de resultado por pregunta */
        #screen-result {
            display: none;
            text-align: center;
            padding-top: 40px;
        }

        #result-icon {
            font-size: 64px;
            margin-bottom: 12px;
        }

        #result-text {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        #result-score {
            font-size: 18px;
            color: #a5b4fc;
            margin-bottom: 8px;
        }

        #result-correct-text {
            font-size: 15px;
            color: #86efac;
            margin-bottom: 32px;
        }

        #btn-next {
            background: #4f46e5;
            color: #fff;
            border: none;
            padding: 12px 32px;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
        }

        /* Error inline */
        #error-msg {
            display: none;
            background: #7f1d1d;
            color: #fca5a5;
            padding: 10px 14px;
            border-radius: 6px;
            margin-top: 12px;
            font-size: 14px;
            text-align: center;
        }

        /* Pantalla final */
        #screen-finish {
            display: none;
            text-align: center;
            padding-top: 60px;
        }

        #screen-finish h1 {
            font-size: 32px;
            margin-bottom: 12px;
        }

        #final-score {
            font-size: 48px;
            font-weight: bold;
            color: #818cf8;
            margin: 20px 0;
        }

        #screen-finish p {
            color: #a5b4fc;
        }

        #btn-back {
            margin-top: 32px;
            background: #4f46e5;
            color: #fff;
            border: none;
            padding: 12px 32px;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
    </style>
</head>

<body>

    {{-- Pantalla inicio --}}
    <div id="screen-start">
        <h1>{{ $activity->title }}</h1>
        <p>{{ count($questions) }} preguntas · Responde una a la vez</p>
        <button id="btn-start" onclick="startGame()">Iniciar</button>
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
        <h1>¡Kahoot completado!</h1>
        <div id="final-score"></div>
        <p>Puntaje acumulado</p>
        <a id="btn-back" href="{{ route('student.activities.show', $activity->id) }}">
            Volver a la actividad
        </a>
    </div>

    <script>
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
            if (currentIndex >= QUESTIONS.length) {
                showFinish();
                return;
            }
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
            grid.innerHTML = '';
            q.options.forEach(opt => {
                const btn = document.createElement('button');
                btn.classList.add('option-btn');
                btn.textContent = opt.text;
                btn.dataset.optionId = opt.id;
                btn.onclick = () => submitAnswer(opt.id, q.id);
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
            showResult(false, 0, null, '⏰ Tiempo agotado');
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
                overrideMessage ?? (isCorrect ? '¡Correcto!' : 'Incorrecto');

            document.getElementById('result-score').textContent =
                isCorrect ? `+${score} puntos` : 'Sin puntos';

            document.getElementById('result-correct-text').textContent =
                `Puntaje acumulado: ${totalScore}`;

            const remaining = QUESTIONS.slice(currentIndex + 1)
                .filter(q => !ANSWERED_IDS.includes(q.id));

            document.getElementById('btn-next').textContent = remaining.length > 0 ?
                'Siguiente pregunta →' :
                'Ver resultados';
        }

        // -------------------------------------------------------------------------
        // Siguiente pregunta
        // -------------------------------------------------------------------------
        function nextQuestion() {
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
