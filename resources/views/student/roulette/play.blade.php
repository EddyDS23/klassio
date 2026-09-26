<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ruleta — Jugar</title>
    <style>
        a { text-decoration: none; }
        body {
            font-family: system-ui, sans-serif;
            background: #faf5ff;
            margin: 0;
            padding: 2rem;
            color: #1e293b;
        }

        .wrap {
            max-width: 760px;
            margin: 0 auto;
        }

        .card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: .75rem;
            padding: 2rem;
            box-shadow: 0 .75rem 1.75rem rgb(15 23 42 / .08);
        }

        h1 {
            margin: 0 0 .25rem;
            color: #a21caf;
            text-align: center;
        }

        #timer {
            color: #475569;
            font-weight: 600;
            text-align: center;
        }

        .scoreline {
            color: #475569;
            margin-bottom: 1.25rem;
            text-align: center;
        }

        .progress-bar {
            height: .55rem;
            border-radius: 999px;
            background: #f3e8ff;
            margin: 0 auto 1.5rem;
            max-width: 420px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, #d946ef, #6366f1);
            transition: width .35s ease;
            width: 0%;
        }

        /* Rueda */
        .wheel-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1.25rem;
            margin-bottom: 1.5rem;
        }

        .wheel-wrap {
            position: relative;
            width: 260px;
            height: 260px;
        }

        .pointer {
            position: absolute;
            top: -8px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 3;
            width: 0;
            height: 0;
            border-left: 16px solid transparent;
            border-right: 16px solid transparent;
            border-top: 26px solid #b91c1c;
            filter: drop-shadow(0 2px 3px rgb(0 0 0 / .35));
        }

        .wheel {
            position: relative;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 10px solid #7e22ce;
            box-shadow: 0 .6rem 1.4rem rgb(112 26 117 / .25);
            transition: transform 4.2s cubic-bezier(.12, .8, .25, 1);
            transform: rotate(0deg);
        }

        .wheel-center {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 2;
            width: 74px;
            height: 74px;
            border-radius: 50%;
            background: #7e22ce;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: .8rem;
            letter-spacing: .04em;
            box-shadow: 0 0 0 6px rgba(126, 34, 206, .15);
            cursor: pointer;
            border: 0;
            font-family: inherit;
        }

        .wheel-center:disabled {
            opacity: .6;
            cursor: default;
        }

        .how-to {
            padding: .65rem .8rem;
            border-radius: .6rem;
            background: #f3e8ff;
            color: #7e22ce;
            margin: 0 0 1rem;
            text-align: center;
            font-weight: 600;
        }

        /* Pregunta */
        #question-card {
            display: none;
            border: 2px solid #e9d5ff;
            border-radius: .75rem;
            padding: 1.25rem;
            background: #fdf4ff;
        }

        #question-card.visible {
            display: block;
        }

        #question-text {
            font-size: 1.15rem;
            font-weight: 800;
            color: #4a044e;
            margin: 0 0 .25rem;
        }

        #question-points {
            color: #a21caf;
            font-size: .85rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .options {
            display: grid;
            gap: .65rem;
        }

        .option-btn {
            display: flex;
            align-items: center;
            gap: .75rem;
            text-align: left;
            padding: .85rem 1rem;
            border-radius: .6rem;
            border: 2px solid #e2e8f0;
            background: #ffffff;
            color: #1e293b;
            font-weight: 600;
            font-family: inherit;
            font-size: 1rem;
            cursor: pointer;
            transition: border-color .15s, background .15s, transform .1s;
        }

        .option-btn:hover:not(:disabled) {
            border-color: #a21caf;
        }

        .option-key {
            flex: 0 0 auto;
            width: 2rem;
            height: 2rem;
            border-radius: .45rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            color: #fff;
            background: #a21caf;
        }

        .option-btn.correct {
            background: #dcfce7;
            border-color: #22c55e;
            color: #15803d;
        }

        .option-btn.wrong {
            background: #fee2e2;
            border-color: #ef4444;
            color: #b91c1c;
        }

        .option-btn:disabled {
            cursor: default;
        }

        .result {
            padding: .8rem;
            border-radius: .45rem;
            margin-top: 1rem;
            display: none;
            font-weight: 600;
            text-align: center;
        }

        .result.ok {
            display: block;
            background: #dcfce7;
            color: #15803d;
        }

        .result.bad {
            display: block;
            background: #fee2e2;
            color: #b91c1c;
        }

        .finish {
            display: none;
            padding: .8rem;
            border-radius: .45rem;
            background: #e9d5ff;
            color: #7e22ce;
            font-weight: 700;
            text-align: center;
            margin-top: 1rem;
        }

        form button[type="submit"] {
            border: 0;
            border-radius: .6rem;
            padding: .6rem 1rem;
            font-weight: 700;
            background: #f1f5f9;
            color: #475569;
            cursor: pointer;
        }

        @media (max-width: 640px) {
            .wheel-wrap {
                width: 210px;
                height: 210px;
            }
        }
    </style>
</head>

<body>
    <div class="wrap">
        <div class="card">
            <h1>🎡 Ruleta</h1>
            <div id="timer">
                Tiempo restante:
                <span id="timer-value">--:--</span>
            </div>
            <p class="scoreline">
                Actividad {{ $activity->title }} ·
                Puntos: <strong id="points">{{ $earnedPoints }}</strong>
                / {{ $maxScore }} · Casilleros: <strong
                    id="answered-count">{{ $answeredCount }}</strong>/{{ $totalItems }}
            </p>

            <div class="progress-bar">
                <div id="progress-fill" class="progress-fill"></div>
            </div>

            <p class="how-to">🎡 <strong>Cómo jugar:</strong> gira la ruleta y responde la pregunta que aparezca. ¡Cada casillero cuenta una sola vez!</p>

            <div class="wheel-section">
                <div class="wheel-wrap">
                    <div class="pointer"></div>
                    <div id="wheel" class="wheel"></div>
                    <button id="spin-btn" class="wheel-center" type="button">GIRAR</button>
                </div>
                <div id="spin-hint" style="font-size:.9rem; color:#475569;">Pulsa GIRAR para obtener tu pregunta</div>
            </div>

            <div id="question-card">
                <p id="question-text"></p>
                <p id="question-points"></p>
                <div id="options" class="options"></div>
            </div>

            <div id="result" class="result"></div>
            <div id="finish" class="finish"></div>

            <form method="POST" action="{{ route('student.participation.abandon', $activity->id) }}"
                onsubmit="return confirm('¿Estás seguro de que quieres abandonar esta actividad?');"
                style="margin-top: 1rem; text-align: center;">
                @csrf

                <button type="submit">
                    Abandonar actividad
                </button>
            </form>
        </div>
    </div>

    <form id="expire-form" method="POST" action="{{ route('student.participation.expire', $activity->id) }}"
        style="display: none;">
        @csrf
    </form>

    <script>
        let remainingSeconds = @json($remainingSeconds);

        const timerValue = document.getElementById('timer-value');

        function updateTimer() {
            if (remainingSeconds === null) {
                timerValue.textContent = '--:--';
                return;
            }

            const minutes = Math.floor(remainingSeconds / 60);
            const seconds = remainingSeconds % 60;

            timerValue.textContent =
                String(minutes).padStart(2, '0') + ':' +
                String(seconds).padStart(2, '0');

            if (remainingSeconds <= 0) {
                clearInterval(timer);

                timerValue.textContent = '00:00';

                document.getElementById('expire-form').submit();

                return;
            }

            remainingSeconds--;
        }

        let timer = setInterval(updateTimer, 1000);

        updateTimer();

        const totalItems = {{ $totalItems }};
        const answeredCount = {{ $answeredCount }};
        const earnStart = {{ $earnedPoints }};

        if (totalItems > 0 && answeredCount >= totalItems) {
            window.location.href = @json(route('student.participation.result', $activity->id));
        }

        const wheel = document.getElementById('wheel');
        const spinBtn = document.getElementById('spin-btn');
        const spinHint = document.getElementById('spin-hint');
        const questionCard = document.getElementById('question-card');
        const questionText = document.getElementById('question-text');
        const questionPoints = document.getElementById('question-points');
        const optionsBox = document.getElementById('options');
        const resultEl = document.getElementById('result');
        const finishEl = document.getElementById('finish');

        let points = earnStart;
        let answered = answeredCount;
        let spinning = false;
        let awaitingAnswer = false;
        let wheelRotation = 0;

        const SEGMENT_COLORS = [
            '#fda4af', '#a5f3fc', '#fde68a', '#c4b5fd',
            '#86efac', '#fcd9a8', '#93c5fd', '#f9a8d4'
        ];

        function drawWheel() {
            const count = totalItems > 0 ? totalItems : 1;
            const step = 360 / count;
            const stops = [];

            for (let i = 0; i < count; i++) {
                const color = SEGMENT_COLORS[i % SEGMENT_COLORS.length];
                stops.push(color + ' ' + (i * step) + 'deg ' + ((i + 1) * step) + 'deg');
            }

            wheel.style.background = 'conic-gradient(' + stops.join(', ') + ')';
        }

        function spinWheel() {
            spinning = true;
            awaitingAnswer = false;
            spinBtn.disabled = true;
            resultEl.className = 'result';
            optionsBox.replaceChildren();

            const turns = 5 + Math.floor(Math.random() * 3);
            const extra = Math.random() * 360;

            wheelRotation += turns * 360 + extra;

            wheel.style.transform = 'rotate(' + wheelRotation + 'deg)';
        }

        function showResult(message, ok) {
            resultEl.textContent = message;
            resultEl.className = 'result ' + (ok ? 'ok' : 'bad');
        }

        function updateProgress() {
            const pct = totalItems > 0 ? Math.round((answered / totalItems) * 100) : 0;
            document.getElementById('progress-fill').style.width = pct + '%';
            document.getElementById('answered-count').textContent = answered;
            document.getElementById('points').textContent = points;
        }

        function showQuestion(it) {
            questionText.textContent = it.question;
            questionPoints.textContent = 'Pregunta · ' + it.points + ' pts';
            optionsBox.replaceChildren();

            it.options.forEach((opt) => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'option-btn';
                btn.dataset.opt = opt.key;

                const key = document.createElement('span');
                key.className = 'option-key';
                key.textContent = opt.key.toUpperCase();

                btn.appendChild(key);
                btn.appendChild(document.createTextNode(' ' + opt.text));

                btn.addEventListener('click', () => answerOption(btn, opt.key, it.id));

                optionsBox.appendChild(btn);
            });

            questionCard.classList.add('visible');
            questionCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        spinBtn.addEventListener('click', () => {
            if (spinning || awaitingAnswer) return;

            spinWheel();
            spinHint.textContent = 'Girando…';

            fetch(@json(route('student.roulette.spin')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        activity_id: {{ $activity->id }},
                    }),
                })
                .then((response) => response.json())
                .then((data) => {
                    spinning = false;
                    answered = data.answered;
                    spinBtn.disabled = false;
                    updateProgress();

                    if (data.completed || (data.item === null && answered >= totalItems)) {
                        questionCard.classList.remove('visible');
                        spinHint.textContent = 'Ya respondiste todos los casilleros.';
                        finishEl.textContent = '¡Completaste la actividad! Redirigiendo al resultado...';
                        finishEl.style.display = 'block';
                        setTimeout(() => {
                            window.location.href = @json(route('student.participation.result', $activity->id));
                        }, 1400);
                        return;
                    }

                    if (data.item === null) {
                        spinHint.textContent = 'Pulsa GIRAR para obtener tu pregunta';
                        showResult('No hay casilleros pendientes.', false);
                        return;
                    }

                    spinning = false;
                    awaitWindow(data.item.id, data.item);
                })
                .catch(() => {
                    spinning = false;
                    spinBtn.disabled = false;
                    spinHint.textContent = 'Pulsa GIRAR para obtener tu pregunta';
                    showResult('Error de conexión, intenta de nuevo.', false);
                });
        });

        let currentItemId = null;
        let optionBtns = [];

        function awaitWindow(itemId, item) {
            awaitingAnswer = true;
            spinBtn.disabled = false;
            currentItemId = itemId;
            optionBtns = [];

            setTimeout(() => {
                showQuestion(item);
                spinHint.textContent = 'Elige tu respuesta';
            }, 1300);

            setTimeout(() => {
                spinBtn.disabled = true;
            }, 700);
        }

        function answerOption(btn, optionKey, itemId) {
            if (btn.dataset.locked !== undefined) return;
            btn.dataset.locked = '1';

            optionBtns = Array.from(optionsBox.querySelectorAll('.option-btn'));

            optionBtns.forEach((b) => {
                b.disabled = true;
            });

            fetch(@json(route('student.roulette.answer')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        roulette_id: {{ $roulette->id }},
                        roulette_item_id: itemId,
                        response: optionKey,
                    }),
                })
                .then((response) => response.json())
                .then((data) => {
                    answered = data.answered;
                    points += data.score;
                    updateProgress();

                    if (data.already_answered) {
                        awaitingAnswer = false;
                        spinBtn.disabled = false;
                        showResult('Ese casillero ya estaba resuelto.', false);
                        spinHint.textContent = 'Pulsa GIRAR para continuar';
                        return;
                    }

                    if (data.is_correct) {
                        optionBtns.forEach((b) => {
                            b.classList.add('correct');
                        });
                        showResult('¡Correcto! (+' + data.score + ' pts)', true);
                    } else {
                        optionBtns.forEach((b) => {
                            if (b.dataset.opt === data.correct_option) {
                                b.classList.add('correct');
                            } else if (b.dataset.opt === optionKey) {
                                b.classList.add('wrong');
                            }
                        });
                        showResult(
                            data.error ||
                            'Respuesta incorrecta. La correcta es ' +
                            data.correct_option.toUpperCase() + '.',
                            false
                        );
                    }

                    awaitingAnswer = false;
                    spinBtn.disabled = false;
                    spinHint.textContent = 'Pulsa GIRAR para continuar';

                    if (data.completed) {
                        questionCard.classList.remove('visible');
                        finishEl.textContent = '¡Completaste la actividad! Redirigiendo al resultado...';
                        finishEl.style.display = 'block';
                        setTimeout(() => {
                            window.location.href = @json(route('student.participation.result', $activity->id));
                        }, 1400);
                    }
                })
                .catch(() => {
                    optionBtns.forEach((b) => {
                        delete b.dataset.locked;
                        b.disabled = false;
                    });
                    showResult('Error de conexión, intenta de nuevo.', false);
                });
        }

        drawWheel();
        updateProgress();
    </script>
</body>

</html>