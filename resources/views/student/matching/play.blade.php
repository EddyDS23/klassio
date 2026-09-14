<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Unir Conceptos — Jugar</title>
    <style>
        a { text-decoration: none; }
        body {
            font-family: system-ui, sans-serif;
            background: #f0fdfa;
            margin: 0;
            padding: 2rem;
            color: #1e293b;
        }

        .wrap {
            max-width: 860px;
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
            color: #0e7490;
        }

        #timer {
            color: #475569;
            font-weight: 600;
        }

        .scoreline {
            color: #475569;
            margin-bottom: 1.5rem;
        }

        .layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            align-items: start;
            position: relative;
        }

        .col {
            display: flex;
            flex-direction: column;
            gap: .6rem;
        }

        .col-title {
            color: #0e7490;
            font-weight: 700;
            margin-bottom: .25rem;
            text-align: center;
        }

        .card-item {
            padding: .9rem 1rem;
            border-radius: .45rem;
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            color: #1e293b;
            cursor: pointer;
            font-weight: 600;
            text-align: center;
            transition: border-color .15s, background .15s, transform .1s;
        }

        .card-item:hover {
            border-color: #0891b2;
        }

        .card-item.selected {
            border-color: #0891b2;
            background: #cffafe;
            color: #0e7490;
        }

        .card-item.matched {
            background: #dcfce7;
            border-color: #22c55e;
            color: #15803d;
            cursor: default;
        }

        .card-item.wrong {
            background: #fee2e2;
            border-color: #ef4444;
            color: #b91c1c;
        }

        #connections { position: absolute; inset: 0; width: 100%; height: 100%; pointer-events: none; overflow: visible; }
        .connection-line { stroke: #22c55e; stroke-width: 4; stroke-linecap: round; opacity: .9; }
        .how-to { padding: .65rem .8rem; border-radius: .6rem; background: #cffafe; color: #0e7490; margin: 0 0 1rem; }

        .result {
            padding: .8rem;
            border-radius: .45rem;
            margin-bottom: 1rem;
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
            background: #dcfce7;
            color: #15803d;
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
            .layout {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="wrap">
        <div class="card">
            <h1>Unir Conceptos</h1>
            <div id="timer">
                Tiempo restante:
                <span id="timer-value">--:--</span>
            </div>
            <p class="scoreline">
                Actividad {{ $matching->activity->title }} ·
                Puntos: <strong id="points">{{ $earnedPoints }}</strong>
                / {{ $maxScore }} · Parejas: <strong
                    id="matched-count">{{ count($correctIds) }}</strong>/{{ $items->count() }}
            </p>
            <p class="how-to">🔗 <strong>Cómo jugar:</strong> arrastra un concepto hacia su definición o selecciónalos uno después del otro.</p>

            <div id="result" class="result"></div>

            <div class="layout">
                <svg id="connections" aria-hidden="true"></svg>
                <div class="col">
                    <div class="col-title">Conceptos</div>
                    @foreach ($items as $item)
                        <div class="card-item left" data-item-id="{{ $item->id }}"
                            {{ in_array($item->id, $correctIds) ? 'data-matched' : '' }}>
                            {{ $item->left_text }}
                        </div>
                    @endforeach
                </div>

                <div class="col">
                    <div class="col-title">Definiciones</div>
                    @foreach ($rightOptions as $option)
                        <div class="card-item right" data-text="{{ $option->right_text }}">
                            {{ $option->right_text }}
                        </div>
                    @endforeach
                </div>
            </div>

            <div id="finish" class="finish">¡Completaste la actividad!</div>
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
        const items = @json($items->map(fn($item) => ['id' => $item->id, 'left' => $item->left_text, 'right' => $item->right_text]));
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
        const correctIds = @json($correctIds);
        const leftCards = Array.from(document.querySelectorAll('.card-item.left'));
        const rightCards = Array.from(document.querySelectorAll('.card-item.right'));
        const layout = document.querySelector('.layout');
        const connectionLayer = document.getElementById('connections');
        const connections = [];
        let selectedLeft = null;
        let dragLeftCard = null;
        let suppressRightClick = false;
        let points = {{ $earnedPoints }};

        function renderConnections() {
            const bounds = layout.getBoundingClientRect();
            connectionLayer.replaceChildren();
            connections.forEach(({ left, right }) => {
                const from = left.getBoundingClientRect();
                const to = right.getBoundingClientRect();
                const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
                line.setAttribute('x1', from.right - bounds.left);
                line.setAttribute('y1', from.top + from.height / 2 - bounds.top);
                line.setAttribute('x2', to.left - bounds.left);
                line.setAttribute('y2', to.top + to.height / 2 - bounds.top);
                line.setAttribute('class', 'connection-line');
                connectionLayer.appendChild(line);
            });
        }

        leftCards.forEach((card) => {
            if (card.dataset.matched !== undefined) {
                card.classList.add('matched');
            }
            card.addEventListener('click', () => {
                if (card.classList.contains('matched')) {
                    return;
                }
                leftCards.forEach((c) => c.classList.remove('selected'));
                card.classList.add('selected');
                selectedLeft = parseInt(card.dataset.itemId, 10);
            });
            card.addEventListener('pointerdown', (event) => {
                if (card.classList.contains('matched')) return;
                event.preventDefault();
                dragLeftCard = card;
                card.classList.add('selected');
            });
        });

        rightCards.forEach((card) => {
            card.addEventListener('click', () => {
                if (suppressRightClick) return;
                if (!selectedLeft) {
                    showResult('Selecciona primero un concepto de la izquierda.', false);
                    return;
                }
                if (card.classList.contains('matched')) {
                    return;
                }
                const leftCard = leftCards.find((c) => parseInt(c.dataset.itemId, 10) === selectedLeft);
                fetchAnswer(leftCard, card);
            });
        });

        layout.addEventListener('pointerup', (event) => {
            if (!dragLeftCard) return;
            const rightCard = document.elementFromPoint(event.clientX, event.clientY)?.closest('.card-item.right');
            if (rightCard && !rightCard.classList.contains('matched')) {
                suppressRightClick = true;
                fetchAnswer(dragLeftCard, rightCard);
                setTimeout(() => { suppressRightClick = false; }, 0);
            }
            dragLeftCard = null;
        });

        function showResult(message, ok) {
            const el = document.getElementById('result');
            el.textContent = message;
            el.className = 'result ' + (ok ? 'ok' : 'bad');
            setTimeout(() => {
                el.className = 'result';
            }, 2500);
        }

        function fetchAnswer(leftCard, rightCard) {

            fetch(@json(route('student.matching.answer')), {

                    method: 'POST',

                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector(
                            'meta[name="csrf-token"]'
                        ).content,
                    },

                    body: JSON.stringify({

                        matching_id: {{ $matching->id }},

                        matching_item_id: parseInt(
                            leftCard.dataset.itemId,
                            10
                        ),

                        response: rightCard.dataset.text,

                    }),

                })
                .then((response) => response.json())

                .then((data) => {

                    /*
                     * RESPUESTA CORRECTA
                     */
                    if (data.correct && !data.already_answered) {

                        leftCard.classList.remove('selected');

                        leftCard.classList.add('matched');

                        rightCard.classList.add('matched');
                        connections.push({ left: leftCard, right: rightCard });
                        renderConnections();

                        points += data.score;

                        document.getElementById('points').textContent = points;

                        showResult(
                            '¡Correcto! (+' + data.score + ' pts)',
                            true
                        );

                        updateMatched();

                        /*
                         * TERMINÓ EL MATCHING
                         */
                        if (data.completed) {

                            // Bloquear las tarjetas
                            leftCards.forEach((card) => {
                                card.style.pointerEvents = 'none';
                            });

                            rightCards.forEach((card) => {
                                card.style.pointerEvents = 'none';
                            });

                            // Mostrar mensaje de finalización
                            const finish = document.getElementById('finish');

                            finish.textContent =
                                '¡Completaste la actividad! Redirigiendo al resultado...';

                            finish.style.display = 'block';

                            /*
                             * Ir a la página de resultado.
                             */
                            setTimeout(() => {

                                window.location.href = @json(route('student.participation.result', $matching->activity_id));

                            }, 1200);

                            return;
                        }

                        return;
                    }

                    /*
                     * PAREJA YA RESUELTA
                     */
                    if (data.already_answered) {

                        leftCard.classList.remove('selected');

                        showResult(
                            'Esa pareja ya estaba resuelta.',
                            false
                        );

                        return;
                    }

                    /*
                     * RESPUESTA INCORRECTA
                     */
                    markWrong([
                        leftCard,
                        rightCard
                    ]);

                    leftCard.classList.remove('selected');

                    showResult(
                        data.error ||
                        'Esa pareja no es correcta.',
                        false
                    );

                })

                .catch(() => {

                    leftCard.classList.remove('selected');

                    showResult(
                        'Error de conexión, intenta de nuevo.',
                        false
                    );

                });
        }

        function markWrong(list) {
            list.forEach((card) => {
                card.classList.add('wrong');
                setTimeout(() => card.classList.remove('wrong'), 600);
            });
        }

        function updateMatched() {
            const matchedPairs = document.querySelectorAll('.card-item.left.matched').length;

            document.getElementById('matched-count').textContent = matchedPairs;

            if (matchedPairs === leftCards.length) {
                document.getElementById('finish').style.display = 'block';
            }
        }

        updateMatched();
        items.forEach((item) => {
            if (!correctIds.includes(item.id)) return;
            const left = leftCards.find((card) => parseInt(card.dataset.itemId, 10) === item.id);
            const right = rightCards.find((card) => card.dataset.text === item.right);
            if (left && right) {
                right.classList.add('matched');
                connections.push({ left, right });
            }
        });
        renderConnections();
        window.addEventListener('resize', renderConnections);
    </script>
</body>

</html>
