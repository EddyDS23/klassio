<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sopa de Letras — Jugar</title>
    <style>
        :root {
            --ws-ink: #183b3a;
            --ws-muted: #587371;
            --ws-line: #d7e8e4;
            --ws-surface: #fff;
            --ws-soft: #f3faf7;
            --ws-deep: #075e58;
            --ws-lime: #c6f36a;
            --ws-cell-size: clamp(1.8rem, 3vw, 2.25rem);
        }

        * { box-sizing: border-box; }
        a { text-decoration: none; }
        body {
            min-height: 100vh;
            margin: 0;
            padding: 22px 16px;
            color: var(--ws-ink);
            background: radial-gradient(circle at 8% 0%, #d9f3e8 0, transparent 31%), #eff6f2;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }
        body.student-kids { font-size: 17px; }
        .wrap { width: min(1240px, 100%); margin: 0 auto; }
        .card {
            overflow: hidden;
            padding: 0;
            border: 1px solid var(--ws-line);
            border-radius: 26px;
            background: var(--ws-surface);
            box-shadow: 0 24px 64px rgb(23 69 60 / .12);
        }
        .game-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            padding: 22px 28px;
            color: #fff;
            background: radial-gradient(circle at 82% 0%, rgb(198 243 106 / .2), transparent 25%), linear-gradient(120deg, #123c3a, #087f75 74%, #109789);
        }
        .eyebrow { margin: 0 0 5px; color: rgb(255 255 255 / .72); font-size: .72rem; font-weight: 850; letter-spacing: .13em; text-transform: uppercase; }
        h1 { margin: 0; color: #fff; font-size: clamp(1.65rem, 4vw, 2.2rem); line-height: 1.08; letter-spacing: -.025em; }
        .activity-title { margin: 6px 0 0; color: rgb(255 255 255 / .82); font-size: .95rem; }
        .timer { flex: 0 0 auto; min-width: 140px; padding: 10px 15px; border: 1px solid rgb(255 255 255 / .25); border-radius: 15px; background: rgb(255 255 255 / .12); text-align: center; backdrop-filter: blur(8px); }
        .timer-label { display: block; color: rgb(255 255 255 / .72); font-size: .68rem; font-weight: 800; letter-spacing: .07em; text-transform: uppercase; }
        #timer { color: #fff; font-weight: 900; }
        #timer-value { display: block; margin-top: 2px; color: var(--ws-lime); font-size: 1.35rem; font-variant-numeric: tabular-nums; }
        .game-content { padding: 20px 24px 22px; }
        .scoreline { display: flex; flex-wrap: wrap; gap: 9px; margin: 0; color: var(--ws-muted); }
        .scoreline > span { display: inline-flex; align-items: baseline; gap: 6px; padding: 8px 12px; border: 1px solid var(--ws-line); border-radius: 12px; background: var(--ws-soft); }
        .scoreline strong { color: var(--ws-deep); font-weight: 900; }
        .how-to { display: flex; align-items: center; gap: 10px; margin: 12px 0 16px; padding: 10px 13px; border: 1px solid #bfe8d8; border-radius: 13px; background: #effbf5; color: #20584d; font-size: .95rem; }
        .how-to strong { flex: 0 0 auto; color: var(--ws-deep); }
        .layout { display: grid; grid-template-columns: minmax(0, 1fr) minmax(250px, 310px); align-items: stretch; gap: 16px; }
        .board-panel, .hints { min-width: 0; border: 1px solid var(--ws-line); border-radius: 19px; background: var(--ws-soft); }
        .board-panel { padding: 16px; }
        .section-heading { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 12px; }
        .section-heading h2, .hints-heading h2 { margin: 0; color: var(--ws-ink); font-size: 1.05rem; font-weight: 850; }
        .section-heading p { margin: 3px 0 0; color: var(--ws-muted); font-size: .82rem; }
        .mobile-scroll-hint { display: none; }
        .board-badge { flex: 0 0 auto; padding: 5px 9px; border-radius: 999px; background: #e5f4e7; color: #326d45; font-size: .72rem; font-weight: 800; }
        .grid-scroll { max-width: 100%; overflow: auto; padding: 4px 2px 8px; overscroll-behavior-inline: contain; scrollbar-color: #8ab6a4 #e6f0ec; scrollbar-width: thin; }
        .grid-scroll:focus-visible { outline: 3px solid #d97706; outline-offset: 2px; }
        .grid { display: grid; width: max-content; gap: 3px; margin: 0 auto; padding: 7px; border: 1px solid #c8ded7; border-radius: 15px; background: #dcebe5; user-select: none; }
        .cell { display: flex; width: var(--ws-cell-size); height: var(--ws-cell-size); align-items: center; justify-content: center; border: 1px solid #e1ece8; border-radius: 7px; background: #fff; color: #254e49; font-size: .9rem; font-weight: 850; cursor: pointer; touch-action: none; transition: background-color .12s, color .12s, transform .12s, box-shadow .12s; }
        .cell:focus-visible { outline: 3px solid #f59e0b; outline-offset: 1px; }
        .cell.selected { z-index: 1; transform: scale(1.04); background: #087f75; color: #fff; box-shadow: inset 0 0 0 2px #a7f3d0; }
        .cell.found { border-color: #4d9c61; background: #32854a; color: #fff; cursor: default; }
        .cell.wrong { background: #c74747; color: #fff; }
        .hints { display: flex; flex-direction: column; align-self: start; padding: 16px; background: #fff; }
        .hints-heading { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding-bottom: 11px; border-bottom: 1px solid var(--ws-line); }
        .hints-heading strong { flex: 0 0 auto; padding: 4px 8px; border-radius: 999px; background: #e9f6ed; color: #367046; font-size: .72rem; }
        .hints-list { display: grid; align-content: start; gap: 7px; max-height: min(58vh, 500px); overflow-y: auto; padding: 11px 2px 2px 0; }
        .hints-empty { margin: 0; padding: 12px 10px; border: 1px dashed #bad1c8; border-radius: 11px; color: var(--ws-muted); font-size: .86rem; }
        .hint { display: flex; align-items: center; justify-content: space-between; gap: 10px; min-height: 42px; padding: 8px 10px; border: 1px solid #e1ece8; border-radius: 11px; background: #f8fbf9; color: #294a45; font-size: .9rem; }
        .hint span:first-child { overflow-wrap: anywhere; font-weight: 750; }
        .hint span:last-child { flex: 0 0 auto; color: var(--ws-muted); font-size: .76rem; font-weight: 800; }
        .hint.found { border-color: #b7dfbe; background: #edf8ef; color: #27723d; }
        .hint.found span:first-child { text-decoration: line-through; }
        .result { display: none; margin: 0 0 12px; padding: 10px 12px; border-radius: 12px; font-weight: 700; }
        .result.ok { display: block; border: 1px solid #b7dfbe; background: #edf8ef; color: #256c39; }
        .result.bad { display: block; border: 1px solid #f1c6c2; background: #fff1ef; color: #9e3029; }
        .finish { display: none; margin-top: 14px; padding: 12px 14px; border: 1px solid #b7dfbe; border-radius: 13px; background: #edf8ef; color: #256c39; font-weight: 850; text-align: center; }
        .game-footer { display: flex; justify-content: flex-end; margin-top: 14px; }
        form button[type="submit"] { min-height: 42px; padding: 8px 14px; border: 1px solid #d8e2df; border-radius: 11px; background: #fff; color: #4d625e; font: inherit; font-size: .86rem; font-weight: 750; cursor: pointer; transition: background .15s, border-color .15s; }
        form button[type="submit"]:hover { border-color: #aabfba; background: #f6faf8; }
        form button[type="submit"]:focus-visible { outline: 3px solid #f59e0b; outline-offset: 2px; }
        @media (max-width: 860px) {
            .layout { grid-template-columns: minmax(0, 1fr) minmax(220px, 270px); gap: 12px; }
            .game-content { padding: 17px; }
            .board-panel, .hints { padding: 13px; }
        }
        @media (max-width: 680px) {
            body { padding: 10px; }
            .card { border-radius: 20px; }
            .game-header { align-items: flex-start; padding: 17px 16px; gap: 12px; }
            .timer { min-width: 104px; padding: 8px 10px; }
            #timer-value { font-size: 1.12rem; }
            .game-content { padding: 13px; }
            .layout { grid-template-columns: minmax(0, 1fr); }
            .board-panel { padding: 12px 9px; }
            .grid-scroll { margin: 0 -3px; padding: 4px 12px 12px; touch-action: pan-x; }
            .grid { margin-inline: auto; }
            .mobile-scroll-hint { display: block; margin-top: 2px; color: var(--ws-muted); font-size: .72rem; }
            .hints-list { grid-template-columns: repeat(2, minmax(0, 1fr)); max-height: 34vh; }
        }
        @media (max-width: 420px) {
            body.student-kids { font-size: 16px; }
            .game-header { gap: 8px; }
            .timer { min-width: 92px; }
            .eyebrow { font-size: .62rem; }
            .activity-title { max-width: 200px; font-size: .82rem; overflow-wrap: anywhere; }
            .scoreline { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .scoreline > span { min-width: 0; justify-content: center; padding: 7px 8px; font-size: .84rem; }
            .scoreline > span:first-child { grid-column: 1 / -1; }
            .how-to { align-items: flex-start; font-size: .86rem; }
            .hints-list { grid-template-columns: minmax(0, 1fr); }
            .hint { min-height: 40px; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { scroll-behavior: auto !important; transition-duration: .01ms !important; }
        }
    </style>
</head>

<body class="student-kids">
    @include('partials.toast')
    <div class="wrap">
        <div class="card">
            <header class="game-header">
                <div>
                    <p class="eyebrow">Actividad interactiva</p>
                    <h1>🔎 Sopa de letras</h1>
                    <p class="activity-title">{{ $wordsearch->activity->title }}</p>
                </div>
                <div class="timer" aria-label="Tiempo restante">
                    <span class="timer-label">Tiempo restante</span>
                    <div id="timer"><span id="timer-value">--:--</span></div>
                </div>
            </header>

            <div class="game-content">
                <p class="scoreline" aria-label="Resumen de la actividad">
                    <span>Puntos <strong><span id="points">{{ $earnedPoints }}</span> / {{ $maxScore }}</strong></span>
                    <span>Encontradas <strong><span id="words-count">{{ $foundCount }}</span> / {{ $words->count() }}</strong></span>
                </p>
                <p class="how-to"><strong>Cómo jugar</strong><span>Toca o haz clic en la primera letra y arrastra hasta la última.</span></p>

                <div id="result" class="result" role="status" aria-live="polite"></div>

                <div class="layout">
                    <section class="board-panel" aria-label="Tablero de sopa de letras">
                        <div class="section-heading">
                            <div>
                                <h2>Encuentra las palabras</h2>
                                <p>Selecciona letras en línea recta.</p>
                                <span class="mobile-scroll-hint">Desliza el tablero para ver todas las letras.</span>
                            </div>
                            <span class="board-badge">{{ $wordsearch->rows }} × {{ $wordsearch->columns }}</span>
                        </div>
                        <div class="grid-scroll" role="region" aria-label="Tablero; desplaza para ver las letras que no caben" tabindex="0">
                            <div class="grid" id="grid"
                                style="grid-template-columns: repeat({{ $wordsearch->columns }}, var(--ws-cell-size));">
                                @foreach ($grid as $row => $rowCells)
                                    @foreach ($rowCells as $column => $letter)
                                        <div class="cell" data-row="{{ $row }}" data-column="{{ $column }}"
                                            id="cell-{{ $row }}-{{ $column }}">{{ $letter }}</div>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>
                    </section>

                    <aside class="hints" aria-label="Palabras por encontrar">
                        <div class="hints-heading">
                            <h2>Palabras</h2>
                            <strong>{{ $words->count() }} en total</strong>
                        </div>
                        <div class="hints-list">
                            @forelse ($words as $word)
                                <div class="hint {{ in_array($word->id, $foundIds) ? 'found' : '' }}"
                                    data-word-id="{{ $word->id }}">
                                    <span>{{ $word->word }}</span>
                                    <span>{{ $word->score }} pts</span>
                                </div>
                            @empty
                                <p class="hints-empty">Aún no hay palabras disponibles para esta actividad.</p>
                            @endforelse
                        </div>
                    </aside>
                </div>

                <div id="finish" class="finish" role="status" aria-live="polite">¡Lo lograste! 🎉</div>
                <div class="game-footer">
                    <form method="POST" action="{{ route('student.participation.abandon', $activity->id) }}"
                        onsubmit="return confirm('¿Seguro que quieres dejar el juego?');">
                        @csrf
                        <button type="submit">Dejar el juego</button>
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
        window.KlassioSounds?.setup('wordsearch');
        const cells = Array.from(document.querySelectorAll('.cell'));
        const foundKey = @json($foundIds);
        const maxScore = {{ $maxScore }};
        let start = null;
        let points = {{ $earnedPoints }};
        let expirationQueued = false;

        cells.forEach((cell) => {
            const key = cell.dataset.row + '-' + cell.dataset.column;
            cell.dataset.found = '0';
        });

        document.querySelectorAll('.cell.found').forEach((cell) => {
            cell.dataset.found = '1';
        });

        function showResult(message, ok) {
            if (window.KlassioToast) {
                if (ok) KlassioToast.success('¡Encontraste una palabra!', message);
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

        let activityFinished = false;

        function finishActivity(redirectUrl = null) {
            if (activityFinished) return;
            activityFinished = true;

            window.KlassioSounds?.completado();
            const finishMessage = document.getElementById('finish');
            const grid = document.getElementById('grid');
            if (finishMessage) finishMessage.style.display = 'block';
            if (grid) grid.style.pointerEvents = 'none';

            if (typeof redirectUrl === 'string' && redirectUrl.length > 0) {
                window.setTimeout(() => {
                    window.location.href = redirectUrl;
                }, 1200);
            }
        }

        function fetchAnswer(startCell, endCell) {
            const body = {
                wordsearch_id: {{ $wordsearch->id }},
                start_row: parseInt(startCell.dataset.row, 10),
                start_column: parseInt(startCell.dataset.column, 10),
                end_row: parseInt(endCell.dataset.row, 10),
                end_column: parseInt(endCell.dataset.column, 10),
            };

            fetch(@json(route('student.wordsearch.answer')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector(
                            'meta[name="csrf-token"]'
                        ).content,
                    },
                    body: JSON.stringify(body),
                })
                .then((response) => {
                    if (!response.ok) {
                        throw new Error('HTTP ' + response.status);
                    }

                    return response.json();
                })
                .then((data) => {

                    /*
                     * PALABRA CORRECTA
                     */
                    if (data.correct && !data.already_found) {
                        window.KlassioSounds?.correcto();

                        // Marcar las celdas encontradas
                        data.cells.forEach(([r, c]) => {
                            const cell = document.getElementById(
                                'cell-' + r + '-' + c
                            );

                            if (cell) {
                                cell.classList.add('found');
                                cell.dataset.found = '1';
                            }
                        });

                        // Marcar la palabra como encontrada en la lista
                        document.querySelectorAll('.hint').forEach((hint) => {
                            const label = hint.querySelector('span');

                            if (label && label.textContent.trim() === data.word) {
                                hint.classList.add('found');
                            }
                        });

                        // Actualizar puntos
                        points += data.score;
                        document.getElementById('points').textContent = points;

                        // Actualizar número de palabras
                        document.getElementById('words-count').textContent =
                            data.total;

                        showResult(
                            'Encontraste: ' +
                            data.word +
                            ' (+' +
                            data.score +
                            ' pts)',
                            true
                        );

                        /*
                         * SI TERMINÓ LA SOPA
                         */
                        if (data.finished) {
                            finishActivity(data.redirect);
                        }

                        /*
                         * PALABRA YA ENCONTRADA
                         */
                    } else if (data.already_found) {

                        markWrong(data.cells);

                        showResult(
                            'Esa palabra ya la encontraste.',
                            false
                        );

                        /*
                         * RESPUESTA INCORRECTA
                         */
                    } else {

                        window.KlassioSounds?.error();

                        markWrong(
                            cellsBetween(startCell, endCell)
                        );

                        showResult(
                            data.error ||
                            'No corresponde a ninguna palabra.',
                            false
                        );
                    }

                    // Limpiar selección
                    start = null;

                    cells.forEach((cell) => {
                        cell.classList.remove('selected');
                    });

                    // Actualizar contador
                    updateFound(data.total);

                })
                .catch((error) => {

                    console.error('Error al responder Wordsearch:', error);

                    start = null;

                    cells.forEach((cell) => {
                        cell.classList.remove('selected');
                    });

                    showResult(
                        'Error de conexión, intenta de nuevo.',
                        false
                    );
                });
        }

        function cellsBetween(a, b) {
            const ar = parseInt(a.dataset.row, 10);
            const ac = parseInt(a.dataset.column, 10);
            const br = parseInt(b.dataset.row, 10);
            const bc = parseInt(b.dataset.column, 10);

            const dr = br - ar;
            const dc = bc - ac;

            if (dr !== 0 && dc !== 0 && Math.abs(dr) !== Math.abs(dc)) {
                return [];
            }

            const stepR = dr === 0 ? 0 : Math.sign(dr);
            const stepC = dc === 0 ? 0 : Math.sign(dc);
            const steps = Math.max(Math.abs(dr), Math.abs(dc));

            const out = [];
            for (let i = 0; i <= steps; i++) {
                out.push(document.getElementById('cell-' + (ar + stepR * i) + '-' + (ac + stepC * i)));
            }
            return out;
        }

        function markWrong(list) {
            list.forEach((cell) => {
                cell.classList.add('wrong');
                setTimeout(() => cell.classList.remove('wrong'), 600);
            });
        }

        function updateFound(count = null) {
            const found = count !== null ?
                count :
                document.querySelectorAll('.hint.found').length;

            document.getElementById('words-count').textContent = found;

            const totalHints = document.querySelectorAll('.hint').length;
            if (totalHints > 0 && found === totalHints) {
                const finishMessage = document.getElementById('finish');
                if (finishMessage) finishMessage.style.display = 'block';
            }
        }

        let dragging = false;
        let end = null;
        let previewQueued = false;
        let previewPoint = null;

        function clearPreview() {
            cells.forEach((cell) => cell.classList.remove('selected'));
        }

        function previewSelection(lastCell) {
            clearPreview();
            if (!start || !lastCell) return;
            cellsBetween(start, lastCell).forEach((cell) => cell?.classList.add('selected'));
        }

        cells.forEach((cell) => {
            cell.addEventListener('pointerdown', (event) => {
                if (cell.dataset.found === '1') return;
                event.preventDefault();
                window.KlassioSounds?.click();
                dragging = true;
                start = cell;
                end = cell;
                previewSelection(cell);
            });
        });

        document.getElementById('grid').addEventListener('pointermove', (event) => {
            if (!dragging || !start) return;
            previewPoint = { x: event.clientX, y: event.clientY };
            if (previewQueued) return;
            previewQueued = true;
            requestAnimationFrame(() => {
                previewQueued = false;
                if (!dragging || !start || !previewPoint) return;
                const target = document.elementFromPoint(previewPoint.x, previewPoint.y)?.closest('.cell');
                if (target && target.dataset.found !== '1') {
                    end = target;
                    previewSelection(target);
                }
            });
        });

        window.addEventListener('pointerup', (event) => {
            if (!dragging) return;
            dragging = false;
            const releasedOn = document.elementFromPoint(event.clientX, event.clientY)?.closest('.cell');
            if (releasedOn && releasedOn.dataset.found !== '1') end = releasedOn;
            if (start && end && start !== end) {
                fetchAnswer(start, end);
            } else {
                clearPreview();
                start = null;
            }
        });

        window.addEventListener('pointercancel', () => {
            dragging = false;
            start = null;
            end = null;
            clearPreview();
        });

        updateFound();
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
                if (expirationQueued) return;
                expirationQueued = true;
                const grid = document.getElementById('grid');
                if (grid) grid.style.pointerEvents = 'none';
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
    </script>
</body>

</html>
