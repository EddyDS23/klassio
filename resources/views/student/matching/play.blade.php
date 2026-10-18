<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Unir Conceptos — Jugar</title>
    <style>
        :root { --mt-ink: #183b3a; --mt-muted: #587371; --mt-line: #d7e8e4; --mt-soft: #f3faf7; --mt-deep: #075e58; --mt-lime: #c6f36a; }
        * { box-sizing: border-box; }
        a { text-decoration: none; }
        body.student-kids { min-height: 100vh; margin: 0; padding: 22px 16px; color: var(--mt-ink); background: radial-gradient(circle at 8% 0%, #d9f3e8 0, transparent 31%), #eff6f2; font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; font-size: 17px; }
        .wrap { width: min(1180px, 100%); margin: 0 auto; }
        .card { overflow: hidden; padding: 0; border: 1px solid var(--mt-line); border-radius: 26px; background: #fff; box-shadow: 0 24px 64px rgb(23 69 60 / .12); }
        .game-header { display: flex; align-items: center; justify-content: space-between; gap: 24px; padding: 22px 28px; color: #fff; background: radial-gradient(circle at 82% 0%, rgb(198 243 106 / .2), transparent 25%), linear-gradient(120deg, #123c3a, #087f75 74%, #109789); }
        .eyebrow { margin: 0 0 5px; color: rgb(255 255 255 / .72); font-size: .72rem; font-weight: 850; letter-spacing: .13em; text-transform: uppercase; }
        .game-header h1 { margin: 0; color: #fff; font-size: clamp(1.65rem, 4vw, 2.2rem); line-height: 1.08; letter-spacing: -.025em; }
        .activity-title { margin: 6px 0 0; color: rgb(255 255 255 / .82); font-size: .95rem; overflow-wrap: anywhere; }
        #timer { flex: 0 0 auto; min-width: 142px; padding: 10px 15px; border: 1px solid rgb(255 255 255 / .25); border-radius: 15px; background: rgb(255 255 255 / .12); text-align: center; backdrop-filter: blur(8px); }
        .timer-label { display: block; color: rgb(255 255 255 / .72); font-size: .68rem; font-weight: 800; letter-spacing: .07em; text-transform: uppercase; }
        #timer-value { display: block; margin-top: 2px; color: var(--mt-lime); font-size: 1.35rem; font-weight: 900; font-variant-numeric: tabular-nums; }
        .game-content { padding: 20px 24px 22px; }
        .scoreline { display: flex; flex-wrap: wrap; gap: 9px; margin: 0; color: var(--mt-muted); }
        .scoreline > span { display: inline-flex; align-items: baseline; gap: 6px; padding: 8px 12px; border: 1px solid var(--mt-line); border-radius: 12px; background: var(--mt-soft); }
        .scoreline strong { color: var(--mt-deep); font-weight: 900; }
        .how-to { display: flex; align-items: center; gap: 10px; margin: 12px 0 15px; padding: 10px 13px; border: 1px solid #bfe8d8; border-radius: 13px; background: #effbf5; color: #20584d; font-size: .94rem; }
        .how-to strong { flex: 0 0 auto; color: var(--mt-deep); }
        #result { margin-bottom: 12px; }
        .layout { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); align-items: stretch; gap: clamp(14px, 3vw, 28px); position: relative; padding: 17px; border: 1px solid var(--mt-line); border-radius: 20px; background: var(--mt-soft); }
        .col { display: flex; min-width: 0; flex-direction: column; gap: 9px; }
        .col-title { display: flex; align-items: center; justify-content: center; min-height: 38px; margin-bottom: 2px; color: var(--mt-ink); font-size: .9rem; font-weight: 850; letter-spacing: .04em; text-align: center; text-transform: uppercase; }
        .card-item { display: flex; min-height: 62px; align-items: center; justify-content: center; padding: 12px 15px; border: 2px solid #dbe8e3; border-radius: 14px; background: #fff; color: #294a45; font-size: .96rem; font-weight: 750; line-height: 1.35; text-align: center; overflow-wrap: anywhere; cursor: pointer; transition: border-color .15s, background .15s, color .15s, transform .12s, box-shadow .15s; user-select: none; -webkit-user-select: none; }
        .card-item.left { touch-action: none; cursor: grab; }
        .card-item.left:active { cursor: grabbing; }
        .card-item:hover:not(.matched) { transform: translateY(-1px); border-color: #77b9a6; box-shadow: 0 5px 14px rgb(24 59 58 / .08); }
        .card-item:focus-visible { outline: 3px solid #f59e0b; outline-offset: 2px; }
        .card-item.selected { border-color: #07877b; background: #e5f6ef; color: #075e58; box-shadow: 0 0 0 3px rgb(8 135 123 / .12); }
        .card-item.matched { border-color: #78bd83; background: #eaf7ec; color: #286d3a; cursor: default; }
        .card-item.wrong { border-color: #d98f88; background: #fff0ed; color: #983a34; }
        #connections { position: absolute; inset: 0; width: 100%; height: 100%; pointer-events: none; overflow: visible; }
        .connection-line { stroke: #32854a; stroke-width: 4; stroke-linecap: round; opacity: .82; }
        .connection-line.drag-line { stroke: #087f75; stroke-dasharray: 8 6; opacity: .65; }
        .result { display: none; padding: 10px 13px; border: 1px solid transparent; border-radius: 12px; font-weight: 750; text-align: center; }
        .result.ok { display: block; border-color: #b7dfbe; background: #edf8ef; color: #256c39; }
        .result.bad { display: block; border-color: #f1c6c2; background: #fff1ef; color: #9e3029; }
        .finish { display: none; margin-top: 14px; padding: 12px 14px; border: 1px solid #b7dfbe; border-radius: 13px; background: #edf8ef; color: #256c39; font-weight: 850; text-align: center; }
        .game-footer { display: flex; justify-content: flex-end; margin-top: 14px; }
        .game-footer form { margin: 0 !important; text-align: right !important; }
        .game-footer button[type="submit"] { min-height: 42px; padding: 8px 14px; border: 1px solid #e9c7c6; border-radius: 11px; background: #fff6f4; color: #9b3a35; font: inherit; font-size: .86rem; font-weight: 750; cursor: pointer; transition: background .15s, border-color .15s; }
        .game-footer button[type="submit"]:hover { border-color: #d89b97; background: #ffede9; }
        .game-footer button[type="submit"]:focus-visible { outline: 3px solid #f59e0b; outline-offset: 2px; }
        .klassio-sound-btn, .klassio-sound-fab { border: 1px solid #bfd9d0 !important; background: #fff !important; color: var(--mt-deep) !important; }
        @media (max-width: 760px) { .wrap { width: 100%; } .layout { gap: 12px; padding: 12px; } .card-item { min-height: 58px; padding: 10px 11px; } }
        @media (max-width: 640px) {
            body.student-kids { padding: 10px; font-size: 16px; }
            .card { border-radius: 20px; }
            .game-header { align-items: flex-start; gap: 10px; padding: 17px 15px; }
            .game-header h1 { font-size: clamp(1.3rem, 6vw, 1.8rem); }
            .activity-title { max-width: 210px; font-size: .82rem; }
            #timer { min-width: 98px; padding: 8px 9px; }
            #timer-value { font-size: 1.12rem; }
            .game-content { padding: 13px; }
            .layout { grid-template-columns: minmax(0, 1fr); gap: 10px; padding: 11px; }
            .col { gap: 7px; }
            .col-title { min-height: 30px; font-size: .8rem; }
            .card-item { min-height: 48px; padding: 9px 10px; font-size: .9rem; }
            .how-to { align-items: flex-start; font-size: .86rem; }
        }
        @media (prefers-reduced-motion: reduce) { *, *::before, *::after { scroll-behavior: auto !important; transition-duration: .01ms !important; } }
    </style>
</head>

<body class="student-kids">
    @include('partials.toast')
    <div class="wrap">
        <div class="card">
            <header class="game-header">
                <div>
                    <p class="eyebrow">Actividad interactiva</p>
                    <h1>🔗 Unir parejas</h1>
                    <p class="activity-title">{{ $matching->activity->title }}</p>
                </div>
                <div id="timer" aria-label="Tiempo restante">
                    <span class="timer-label">Tiempo restante</span>
                    <span id="timer-value">--:--</span>
                </div>
            </header>

            <div class="game-content">
                <p class="scoreline" aria-label="Resumen de la actividad">
                    <span>Puntos <strong><span id="points">{{ $earnedPoints }}</span> / {{ $maxScore }}</strong></span>
                    <span>Parejas <strong><span id="matched-count">{{ count($correctIds) }}</span> / {{ $items->count() }}</strong></span>
                </p>
                <p class="how-to"><strong>Cómo jugar</strong><span>Selecciona un concepto y luego su definición, o arrastra el concepto hasta su pareja. También puedes tocar las tarjetas.</span></p>

                <div id="result" class="result" role="status" aria-live="polite"></div>

                <div class="layout" aria-label="Relaciona los conceptos con sus definiciones">
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

                <div id="finish" class="finish">¡Lo lograste! 🎉</div>
                <div class="game-footer">
                    <form method="POST" action="{{ route('student.participation.abandon', $activity->id) }}"
                        onsubmit="return confirm('¿Seguro que quieres dejar el juego?');">
                        @csrf

                        <button type="submit">Dejar el juego 👋</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <form id="expire-form" method="POST" action="{{ route('student.participation.expire', $activity->id) }}"
        style="display: none;">
        @csrf
    </form>

    <script src="/js/klassio-sounds.js"></script>
    <script>
        window.KlassioSounds?.setup('matching');
        const items = @json($items->map(fn($item) => ['id' => $item->id, 'left' => $item->left_text, 'right' => $item->right_text]));
        let remainingSeconds = @json($remainingSeconds);
        let expirationQueued = false;

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
                if (expirationQueued) return;
                expirationQueued = true;
                const layout = document.querySelector('.layout');
                if (layout) layout.style.pointerEvents = 'none';
                document.querySelectorAll('button, input:not([type="hidden"]), select, textarea').forEach(control => {
                    control.disabled = true;
                });
                document.querySelectorAll('a').forEach(link => {
                    link.setAttribute('aria-disabled', 'true');
                    link.tabIndex = -1;
                    link.style.pointerEvents = 'none';
                    link.addEventListener('click', event => event.preventDefault());
                });
                document.body.setAttribute('aria-busy', 'true');
                window.KlassioSounds?.expirado();

                const submitExpiration = () => document.getElementById('expire-form').submit();
                if (window.KlassioSounds && !window.KlassioSounds.isMuted()) {
                    setTimeout(submitExpiration, 900);
                } else {
                    submitExpiration();
                }

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

        let dragLine = null;
        let dragQueued = false;
        let dragPoint = null;

        function clearDragLine() {
            if (dragLine) {
                dragLine.remove();
                dragLine = null;
            }
        }

        document.addEventListener('pointermove', (event) => {
            if (!dragLeftCard) return;
            dragPoint = { x: event.clientX, y: event.clientY };
            if (dragQueued) return;
            dragQueued = true;
            requestAnimationFrame(() => {
                dragQueued = false;
                if (!dragLeftCard || !dragPoint) return;
                const bounds = layout.getBoundingClientRect();
                const from = dragLeftCard.getBoundingClientRect();
                if (!dragLine) {
                    dragLine = document.createElementNS('http://www.w3.org/2000/svg', 'line');
                    dragLine.setAttribute('class', 'connection-line drag-line');
                    connectionLayer.appendChild(dragLine);
                }
                dragLine.setAttribute('x1', from.right - bounds.left);
                dragLine.setAttribute('y1', from.top + from.height / 2 - bounds.top);
                dragLine.setAttribute('x2', dragPoint.x - bounds.left);
                dragLine.setAttribute('y2', dragPoint.y - bounds.top);
            });
        });

        leftCards.forEach((card) => {
            if (card.dataset.matched !== undefined) {
                card.classList.add('matched');
            }
            card.addEventListener('click', () => {
                if (card.classList.contains('matched')) {
                    return;
                }
                window.KlassioSounds?.click();
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
                window.KlassioSounds?.click();
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
            clearDragLine();
            if (!dragLeftCard) return;
            const rightCard = document.elementFromPoint(event.clientX, event.clientY)?.closest('.card-item.right');
            if (rightCard && !rightCard.classList.contains('matched')) {
                suppressRightClick = true;
                window.KlassioSounds?.click();
                fetchAnswer(dragLeftCard, rightCard);
                setTimeout(() => { suppressRightClick = false; }, 0);
            }
            dragLeftCard = null;
        });

        document.addEventListener('pointerup', () => {
            clearDragLine();
            dragLeftCard = null;
        });
        document.addEventListener('pointercancel', () => {
            clearDragLine();
            dragLeftCard = null;
        });

        function showResult(message, ok) {
            if (window.KlassioToast) {
                if (ok) KlassioToast.success('¡Correcto!', message);
                else KlassioToast.error('Revisa', message);
                return;
            }
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
                        window.KlassioSounds?.correcto();

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
                            window.KlassioSounds?.completado();

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
                    window.KlassioSounds?.error();

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
        let resizeTimer = null;
        window.addEventListener('resize', () => {
            if (resizeTimer) clearTimeout(resizeTimer);
            resizeTimer = setTimeout(renderConnections, 120);
        });
    </script>
</body>

</html>
