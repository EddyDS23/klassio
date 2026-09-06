<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $activity->title }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: sans-serif; background: #1e1b4b; color: #fff; margin: 0; padding: 20px; min-height: 100vh; }

        #screen-start, #screen-question, #screen-result, #screen-finish {
            max-width: 680px;
            margin: 0 auto;
        }

        /* Pantalla de inicio */
        #screen-start { text-align: center; padding-top: 60px; }
        #screen-start h1 { font-size: 28px; margin-bottom: 8px; }
        #screen-start p { color: #a5b4fc; margin-bottom: 32px; }
        #btn-start {
            background: #4f46e5;
            color: #fff;
            border: none;
            padding: 14px 40px;
            font-size: 18px;
            border-radius: 8px;
            cursor: pointer;
        }
        #btn-start:hover { background: #4338ca; }

        /* Pantalla de pregunta */
        #screen-question { display: none; }

        #progress-bar-wrap {
            background: #312e81;
            border-radius: 4px;
            height: 6px;
            margin-bottom: 12px;
        }
        #progress-bar { height: 100%; background: #818cf8; border-radius: 4px; transition: width 0.3s; }

        #question-counter { font-size: 13px; color: #a5b4fc; margin-bottom: 6px; }

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
        #timer-bar { height: 100%; background: #34d399; border-radius: 4px; transition: width 1s linear; }
        #timer-text { font-size: 20px; font-weight: bold; min-width: 30px; text-align: right; }
        #timer-text.danger { color: #f87171; }

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
        .option-btn:hover:not(:disabled) { transform: scale(1.02); }
        .option-btn:disabled { cursor: default; opacity: 0.7; }

        .option-btn:nth-child(1) { background: #dc2626; }
        .option-btn:nth-child(2) { background: #2563eb; }
        .option-btn:nth-child(3) { background: #ca8a04; }
        .option-btn:nth-child(4) { background: #16a34a; }

        .option-btn.correct   { outline: 4px solid #34d399; }
        .option-btn.incorrect { outline: 4px solid #f87171; opacity: 0.5; }

        /* Pantalla de resultado por pregunta */
        #screen-result { display: none; text-align: center; padding-top: 40px; }
        #result-icon { font-size: 64px; margin-bottom: 12px; }
        #result-text { font-size: 24px; font-weight: bold; margin-bottom: 8px; }
        #result-score { font-size: 18px; color: #a5b4fc; margin-bottom: 8px; }
        #result-correct-text { font-size: 15px; color: #86efac; margin-bottom: 32px; }
        #btn-next {
            background: #4f46e5;
            color: #fff;
            border: none;
            padding: 12px 32px;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
        }

        /* Pantalla final */
        #screen-finish { display: none; text-align: center; padding-top: 60px; }
        #screen-finish h1 { font-size: 32px; margin-bottom: 12px; }
        #final-score { font-size: 48px; font-weight: bold; color: #818cf8; margin: 20px 0; }
        #screen-finish p { color: #a5b4fc; }
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

{{-- ----------------------------- Pantalla inicio ----------------------------- --}}
<div id="screen-start">
    <h1>{{ $activity->title }}</h1>
    <p>{{ count($questions) }} preguntas · Responde una a la vez</p>
    <button id="btn-start" onclick="startGame()">Iniciar</button>
</div>

{{-- ----------------------------- Pantalla pregunta ----------------------------- --}}
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
</div>

{{-- ----------------------------- Pantalla resultado ----------------------------- --}}
<div id="screen-result">
    <div id="result-icon"></div>
    <div id="result-text"></div>
    <div id="result-score"></div>
    <div id="result-correct-text"></div>
    <button id="btn-next" onclick="nextQuestion()">Siguiente pregunta →</button>
</div>

{{-- ----------------------------- Pantalla final ----------------------------- --}}
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
    const CSRF_TOKEN       = '{{ csrf_token() }}';
    const ANSWER_URL       = '{{ route('student.kahoot.answer') }}';

    const QUESTIONS    = @json($questions);
    const ANSWERED_IDS = @json($answeredIds);

    // -------------------------------------------------------------------------
    // Estado del juego
    // -------------------------------------------------------------------------
    let currentIndex = 0;
    let totalScore   = 0;
    let timerInterval = null;
    let timeLeft      = 0;
    let answered      = false;

    // Saltar las ya respondidas al inicio
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
        document.getElementById('screen-start').style.display    = 'none';
        document.getElementById('screen-question').style.display = 'block';
        showQuestion(currentIndex);
    }

    // -------------------------------------------------------------------------
    // Mostrar pregunta
    // -------------------------------------------------------------------------
    function showQuestion(index) {
        answered = false;
        const q  = QUESTIONS[index];
        const total = QUESTIONS.length;

        // Progreso
        document.getElementById('progress-bar').style.width =
            `${(index / total) * 100}%`;
        document.getElementById('question-counter').textContent =
            `Pregunta ${index + 1} de ${total}`;

        // Texto
        document.getElementById('question-text').textContent = q.question;

        // Opciones
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

        // Temporizador
        startTimer(q.time_limit, q.id);
    }

    // -------------------------------------------------------------------------
    // Temporizador
    // -------------------------------------------------------------------------
    function startTimer(seconds, questionId) {
        clearInterval(timerInterval);
        timeLeft = seconds;

        const timerText = document.getElementById('timer-text');
        const timerBar  = document.getElementById('timer-bar');

        timerText.textContent = timeLeft;
        timerText.classList.remove('danger');
        timerBar.style.width = '100%';
        timerBar.style.background = '#34d399';

        timerInterval = setInterval(() => {
            timeLeft--;

            timerText.textContent = timeLeft;
            timerBar.style.width  = `${(timeLeft / seconds) * 100}%`;

            if (timeLeft <= 5) {
                timerText.classList.add('danger');
                timerBar.style.background = '#f87171';
            }

            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                if (!answered) {
                    timeExpired(questionId);
                }
            }
        }, 1000);
    }

    // -------------------------------------------------------------------------
    // Tiempo expirado sin respuesta
    // -------------------------------------------------------------------------
    function timeExpired(questionId) {
        answered = true;
        disableOptions();
        showResult(false, 0, null, '⏰ Tiempo agotado');
    }

    // -------------------------------------------------------------------------
    // Enviar respuesta
    // -------------------------------------------------------------------------
    async function submitAnswer(optionId, questionId) {
        if (answered) return;
        answered = true;
        clearInterval(timerInterval);

        disableOptions();

        let result;
        try {
            const res = await fetch(ANSWER_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                },
                body: JSON.stringify({
                    participation_id: PARTICIPATION_ID,
                    question_id:      questionId,
                    option_id:        optionId,
                }),
            });
            result = await res.json();
        } catch (err) {
            console.error('Error al enviar respuesta:', err);
            return;
        }

        if (result.error) {
            // Ya fue respondida (edge case)
            nextQuestion();
            return;
        }

        // Resaltar correcta e incorrecta
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
    }

    function disableOptions() {
        document.querySelectorAll('.option-btn').forEach(btn => {
            btn.disabled = true;
        });
    }

    // -------------------------------------------------------------------------
    // Mostrar resultado de la pregunta
    // -------------------------------------------------------------------------
    function showResult(isCorrect, score, correctOptionId, overrideMessage) {
        document.getElementById('screen-question').style.display = 'none';
        document.getElementById('screen-result').style.display   = 'block';

        document.getElementById('result-icon').textContent =
            isCorrect ? '🎉' : '❌';

        document.getElementById('result-text').textContent =
            overrideMessage ?? (isCorrect ? '¡Correcto!' : 'Incorrecto');

        document.getElementById('result-score').textContent =
            isCorrect ? `+${score} puntos` : 'Sin puntos';

        document.getElementById('result-correct-text').textContent =
            `Puntaje acumulado: ${totalScore}`;

        // Si es la última pregunta, cambiar botón
        const remaining = QUESTIONS.slice(currentIndex + 1)
            .filter(q => !ANSWERED_IDS.includes(q.id));

        const btnNext = document.getElementById('btn-next');
        btnNext.textContent = remaining.length > 0
            ? 'Siguiente pregunta →'
            : 'Ver resultados';
    }

    // -------------------------------------------------------------------------
    // Siguiente pregunta
    // -------------------------------------------------------------------------
    function nextQuestion() {
        currentIndex++;

        // Saltar ya respondidas
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
        document.getElementById('screen-start').style.display    = 'none';
        document.getElementById('screen-question').style.display = 'none';
        document.getElementById('screen-result').style.display   = 'none';
        document.getElementById('screen-finish').style.display   = 'block';
        document.getElementById('final-score').textContent       = totalScore;
    }
</script>

</body>
</html>