<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Unir Conceptos — Jugar</title>

    @include('partials.assets', ['theme' => 'student', 'includeBootstrap' => false])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; }

        /* === Internos del juego (JS modifica estas clases) === */
        .layout { position: relative; display: grid; grid-template-columns: minmax(0, 1fr); align-items: stretch; gap: 10px; padding: 12px; border: 1px solid #e2e8f0; border-radius: 1rem; background: #fff; }

        .col { display: flex; min-width: 0; flex-direction: column; gap: 8px; }
        .col-title { display: flex; align-items: center; justify-content: center; min-height: 30px; color: #64748b; font-size: .75rem; font-weight: 800; letter-spacing: .08em; text-align: center; text-transform: uppercase; }

        .card-item { display: flex; min-height: 54px; align-items: center; justify-content: center; padding: 10px 12px; border: 1.5px solid #e2e8f0; border-radius: .8rem; background: #fff; color: #334155; font-size: .9rem; font-weight: 700; line-height: 1.35; text-align: center; overflow-wrap: anywhere; cursor: pointer; user-select: none; -webkit-user-select: none; transition: border-color .15s, background .15s, color .15s, transform .12s, box-shadow .15s; }
        .card-item.left { touch-action: none; cursor: grab; }
        .card-item.left:active { cursor: grabbing; }
        .card-item:hover:not(.matched) { transform: translateY(-1px); border-color: #fcd34d; box-shadow: 0 5px 14px rgb(15 23 42 / .07); }
        .card-item:focus-visible { outline: 3px solid #d97706; outline-offset: 2px; }
        .card-item.selected { border-color: #d97706; background: #fffbeb; color: #78350f; box-shadow: 0 0 0 3px rgb(217 119 6 / .15); }
        .card-item.matched { border-color: #a7f3d0; background: #ecfdf5; color: #065f46; cursor: default; }
        .card-item.wrong { border-color: #fca5a5; background: #fef2f2; color: #991b1b; }

        #connections { position: absolute; inset: 0; width: 100%; height: 100%; pointer-events: none; overflow: visible; }
        .connection-line { stroke: #d97706; stroke-width: 4; stroke-linecap: round; opacity: .82; }
        .connection-line.drag-line { stroke: #d97706; stroke-dasharray: 8 6; opacity: .65; }

        .result { display: none; }
        .result.ok { display: block; margin-bottom: .75rem; padding: .65rem .9rem; border: 1px solid #a7f3d0; border-radius: .75rem; background: #ecfdf5; color: #065f46; font-size: .875rem; font-weight: 700; text-align: center; }
        .result.bad { display: block; margin-bottom: .75rem; padding: .65rem .9rem; border: 1px solid #fecaca; border-radius: .75rem; background: #fef2f2; color: #991b1b; font-size: .875rem; font-weight: 700; text-align: center; }

        @media (min-width: 640px) {
            .layout { grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: 20px; padding: 18px; }
            .card-item { min-height: 62px; padding: 12px 15px; font-size: .95rem; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { scroll-behavior: auto !important; transition-duration: .01ms !important; }
        }
    </style>
</head>

<body class="student-kids">
    @include('partials.toast')

    <div class="mx-auto w-full max-w-5xl px-3 py-4 sm:px-5 sm:py-6">

        {{-- Encabezado --}}
        <header class="rounded-2xl bg-amber-700 p-4 text-white shadow-sm sm:p-6">

            <div class="flex items-start justify-between gap-3">

                <div class="flex min-w-0 items-start gap-3">

                    <div class="hidden h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-white/15 ring-1 ring-white/20 sm:flex">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                        </svg>
                    </div>

                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-amber-200 sm:text-xs">
                            Actividad interactiva
                        </p>
                        <h1 class="mt-0.5 text-lg font-bold tracking-tight sm:mt-1 sm:text-2xl">
                            Unir parejas
                        </h1>
                        <p class="mt-0.5 break-words text-xs font-semibold text-amber-100 sm:text-sm">
                            {{ $matching->activity->title }}
                        </p>
                    </div>

                </div>

                <div id="timer" class="shrink-0 rounded-xl bg-white/15 px-3 py-2 text-center ring-1 ring-white/25" aria-label="Tiempo restante">
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-amber-100">
                        Tiempo
                    </span>
                    <div class="font-mono text-lg font-bold tabular-nums text-white sm:text-xl">
                        <span id="timer-value">--:--</span>
                    </div>
                </div>

            </div>

        </header>

        {{-- Resumen --}}
        <p class="mt-3 flex gap-2 sm:gap-3" aria-label="Resumen de la actividad">

            <span class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm shadow-sm sm:justify-start sm:px-4">
                <svg class="h-4 w-4 shrink-0 text-amber-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0-3.375-6.375m-7.125 0L6 18.75m4.875-9.75L9.75 2.25h4.5l-1.125 6.75h-2.25Z" />
                </svg>
                <span class="font-semibold text-slate-500">Puntos</span>
                <span class="font-bold text-amber-700"><span id="points">{{ $earnedPoints }}</span> / {{ $maxScore }}</span>
            </span>

            <span class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm shadow-sm sm:justify-start sm:px-4">
                <svg class="h-4 w-4 shrink-0 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>
                <span class="font-semibold text-slate-500">Parejas</span>
                <span class="font-bold text-emerald-700"><span id="matched-count">{{ count($correctIds) }}</span> / {{ $items->count() }}</span>
            </span>

        </p>

        {{-- Cómo jugar --}}
        <p class="mt-3 flex items-start gap-2 rounded-xl bg-amber-50 px-3.5 py-2.5 text-sm text-amber-900 ring-1 ring-amber-100">
            <svg class="mt-0.5 h-4 w-4 shrink-0 text-amber-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 0 4.51-2.104m0 0A9.04 9.04 0 0 0 12 3a9.04 9.04 0 0 0-6.76 12.986M3.75 19.5a12.06 12.06 0 0 1 4.51-2.104m0 0a6.01 6.01 0 0 0 1.5-.189m0 0a6.01 6.01 0 0 1 1.5-.189" />
            </svg>
            <span><strong class="font-bold">Cómo jugar:</strong> toca un concepto y luego su definición, o arrastra el concepto hasta su pareja.</span>
        </p>

        {{-- Resultado de la selección --}}
        <div id="result" class="result" role="status" aria-live="polite"></div>

        {{-- Tablero de parejas --}}
        <div class="layout mt-3" aria-label="Relaciona los conceptos con sus definiciones">
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

        {{-- Fin de la partida --}}
        <div id="finish" class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-center font-bold text-emerald-800" role="status" aria-live="polite" style="display: none;">
            ¡Lo lograste!
        </div>

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
