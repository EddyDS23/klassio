<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sopa de Letras — Jugar</title>
    <script>
        try {
            if (localStorage.getItem('klassio-theme') === 'dark') {
                document.documentElement.setAttribute('data-theme', 'dark');
            }
        } catch (e) {}
    </script>
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
            max-width: 900px;
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

        #timer { color: #475569; font-weight: 600; }

        .scoreline {
            color: #475569;
            margin-bottom: 1.5rem;
        }

        .layout {
            display: flex;
            gap: 2rem;
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .grid {
            display: inline-grid;
            gap: 2px;
            background: #e2e8f0;
            padding: 2px;
            border-radius: .4rem;
            user-select: none;
        }

        .cell {
            width: 2rem;
            height: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            color: #334155;
            font-weight: bold;
            cursor: pointer;
            border-radius: .15rem;
            touch-action: none;
        }

        .cell.selected {
            background: #0891b2;
            color: #ffffff;
            box-shadow: inset 0 0 0 2px #a5f3fc;
        }

        .cell.found {
            background: #22c55e;
            color: #ffffff;
            cursor: default;
        }

        .cell.wrong {
            background: #ef4444;
            color: #fff;
        }

        .hint {
            padding: .55rem .75rem;
            border-radius: .45rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #1e293b;
            margin-bottom: .4rem;
            display: flex;
            justify-content: space-between;
            gap: 1rem;
        }

        .hint.found {
            color: #16a34a;
            text-decoration: line-through;
        }

        .hints {
            min-width: 220px;
        }

        .hints > strong { color: #0e7490 !important; }

        .how-to { margin: 1rem 0; padding: .75rem 1rem; border: 1px solid #0891b2; border-radius: .7rem; background: #cffafe; color: #0e7490; }

        .result {
            padding: .8rem;
            border-radius: .45rem;
            margin-bottom: 1rem;
            display: none;
            font-weight: 600;
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

        .klassio-theme-btn {
            border: 1px solid #cbd5e1;
            border-radius: .6rem;
            background: #fff;
            color: #475569;
            padding: .6rem 1rem;
            font-weight: 700;
            cursor: pointer;
        }

        /* Negro solo si el alumno lo activa */
        html[data-theme="dark"] body { background: #0f172a; color: #e2e8f0; }
        html[data-theme="dark"] .card { background: #1e293b; border-color: #1e293b; box-shadow: none; }
        html[data-theme="dark"] h1 { color: #f1f5f9; }
        html[data-theme="dark"] #timer, html[data-theme="dark"] .scoreline { color: #94a3b8; }
        html[data-theme="dark"] .grid { background: #334155; }
        html[data-theme="dark"] .cell { background: #0f172a; color: #e2e8f0; }
        html[data-theme="dark"] .cell.selected { background: #38bdf8; color: #0f172a; box-shadow: inset 0 0 0 2px #e0f2fe; }
        html[data-theme="dark"] .cell.found { background: #22c55e; color: #052e16; }
        html[data-theme="dark"] .hint { background: #0f172a; border-color: #0f172a; color: #e2e8f0; }
        html[data-theme="dark"] .hint.found { color: #22c55e; }
        html[data-theme="dark"] .hints > strong { color: #94a3b8 !important; }
        html[data-theme="dark"] .how-to { background: #082f49; border-color: #0ea5e9; color: #bae6fd; }
        html[data-theme="dark"] .result.ok { background: #052e16; color: #22c55e; }
        html[data-theme="dark"] .result.bad { background: #450a0a; color: #f87171; }
        html[data-theme="dark"] .finish { background: #14532d; color: #bbf7d0; }
        html[data-theme="dark"] form button[type="submit"] { background: #334155; color: #e2e8f0; }
        html[data-theme="dark"] .klassio-theme-btn { background: #0f172a; color: #f1f5f9; border-color: #475569; }

        @media (max-width: 720px) {
            .grid {
                grid-template-columns: repeat(var(--cols), 2rem) !important;
            }
        }
    </style>
</head>

<body>
    @include('partials.toast')
    <div class="wrap">
        <div class="card">
            <h1>Sopa de Letras</h1>
            <div id="timer">
                Tiempo restante:
                <span id="timer-value">--:--</span>
            </div>
            <p class="scoreline">
                Actividad {{ $wordsearch->activity->title }} ·
                Puntos: <strong id="points">{{ $earnedPoints }}</strong>
                / {{ $maxScore }} · Palabras: <strong
                    id="words-count">{{ $foundCount }}</strong>/{{ $words->count() }}
            </p>
            <p class="how-to">👆 <strong>Cómo jugar:</strong> mantén presionada la primera letra y arrastra hasta la última letra de cada palabra.</p>

            <div id="result" class="result"></div>

            <div class="layout">
                <div class="grid" id="grid"
                    style="grid-template-columns: repeat({{ $wordsearch->columns }}, 2rem); --cols: {{ $wordsearch->columns }};">
                    @foreach ($grid as $row => $rowCells)
                        @foreach ($rowCells as $column => $letter)
                            <div class="cell" data-row="{{ $row }}" data-column="{{ $column }}"
                                id="cell-{{ $row }}-{{ $column }}">{{ $letter }}</div>
                        @endforeach
                    @endforeach
                </div>

                <div class="hints">
                    <strong style="color:#94a3b8;">Palabras a encontrar</strong>
                    @foreach ($words as $word)
                        <div class="hint {{ in_array($word->id, $foundIds) ? 'found' : '' }}"
                            data-word-id="{{ $word->id }}">
                            <span>{{ $word->word }}</span>
                            <span>{{ $word->score }} pts</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div id="finish" class="finish">¡Completaste la sopa de letras!</div>
            <div style="margin-top: 1rem; text-align: center;">
                <button type="button" id="theme-btn" class="klassio-theme-btn">🌙 Negro</button>
                <form method="POST" action="{{ route('student.participation.abandon', $activity->id) }}"
                    onsubmit="return confirm('¿Estás seguro de que quieres abandonar esta actividad?');"
                    style="display: inline;">
                    @csrf

                    <button type="submit">
                        Abandonar actividad
                    </button>
                </form>
            </div>
            <script>
                (function () {
                    var btn = document.getElementById('theme-btn');
                    function label() {
                        return document.documentElement.getAttribute('data-theme') === 'dark' ? '☀️ Claro' : '🌙 Negro';
                    }
                    btn.textContent = label();
                    btn.addEventListener('click', function () {
                        var dark = document.documentElement.getAttribute('data-theme') !== 'dark';
                        if (dark) {
                            document.documentElement.setAttribute('data-theme', 'dark');
                        } else {
                            document.documentElement.removeAttribute('data-theme');
                        }
                        try { localStorage.setItem('klassio-theme', dark ? 'dark' : 'light'); } catch (e) {}
                        btn.textContent = label();
                    });
                })();
            </script>
        </div>
    </div>

    <script src="/js/klassio-sounds.js?v=4"></script>
    <script>
        // Sonido del juego (si el archivo no carga, KS queda mudo sin romper nada).
        window.KS = window.KlassioSounds || { click: function () {}, correcto: function () {}, error: function () {}, terminado: function () {}, completado: function () {}, expirado: function () {}, startMusic: function () {}, stopMusic: function () {} };
        if (window.KlassioSounds) KlassioSounds.setup('wordsearch');
    </script>

    <form id="expire-form" method="POST" action="{{ route('student.participation.expire', $activity->id) }}"
        style="display: none;">
        @csrf
    </form>

    <script>
        const cells = Array.from(document.querySelectorAll('.cell'));
        const foundKey = @json($foundIds);
        const maxScore = {{ $maxScore }};
        let start = null;
        let points = {{ $earnedPoints }};

        cells.forEach((cell) => {
            const key = cell.dataset.row + '-' + cell.dataset.column;
            cell.dataset.found = '0';
        });

        document.querySelectorAll('.cell.found').forEach((cell) => {
            cell.dataset.found = '1';
        });

        function showResult(message, ok) {
            // Notificación en la esquina (se quita sola); con respaldo
            // al mensaje en línea si el parcial no cargó.
            if (window.KlassioToast) {
                if (ok) { KlassioToast.success('¡Correcto!', message); }
                else { KlassioToast.error('Revisa', message); }
                return;
            }
            const el = document.getElementById('result');
            el.textContent = message;
            el.className = 'result ' + (ok ? 'ok' : 'bad');
            setTimeout(() => {
                el.className = 'result';
            }, 2500);
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
                        KS.correcto();

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
                            KS.completado();
                            document.getElementById('finish').style.display = 'block';

                            setTimeout(() => {
                                window.location.href = data.redirect;
                            }, 1200);
                        }

                        /*
                         * PALABRA YA ENCONTRADA
                         */
                    } else if (data.already_found) {

                        markWrong(data.cells);
                        KS.error();

                        showResult(
                            'Esa palabra ya la encontraste.',
                            false
                        );

                        /*
                         * RESPUESTA INCORRECTA
                         */
                    } else {

                        markWrong(
                            cellsBetween(startCell, endCell)
                        );
                        KS.error();

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

            if (found === document.querySelectorAll('.hint').length) {
                document.getElementById('finish').style.display = 'block';
            }
        }

        let dragging = false;
        let end = null;

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
                KS.click();
                dragging = true;
                start = cell;
                end = cell;
                previewSelection(cell);
            });
        });

        // Vista previa limitada a 1 por frame (rAF): evita recalcular
        // decenas de veces por segundo mientras se arrastra.
        let previewQueued = false;
        let previewPoint = null;
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

        window.addEventListener('pointerup', () => {
            if (!dragging) return;
            dragging = false;
            if (start && end && start !== end) {
                fetchAnswer(start, end);
            } else {
                clearPreview();
                start = null;
            }
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

                var hints = document.querySelectorAll('.hint');
                var done = document.querySelectorAll('.hint.found');
                if (hints.length > 0 && done.length >= hints.length) {
                    KS.terminado();
                } else {
                    KS.expirado();
                }

                setTimeout(function () {
                    document.getElementById('expire-form').submit();
                }, 900);

                return;
            }

            remainingSeconds--;
        }

        let timer = setInterval(updateTimer, 1000);

        updateTimer();
    </script>
</body>

</html>
