<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sopa de Letras — Jugar</title>
    <style>
        body {
            font-family: system-ui, sans-serif;
            background: #0f172a;
            margin: 0;
            padding: 2rem;
            color: #e2e8f0;
        }

        .wrap {
            max-width: 900px;
            margin: 0 auto;
        }

        .card {
            background: #1e293b;
            border-radius: .75rem;
            padding: 2rem;
        }

        h1 {
            margin: 0 0 .25rem;
        }

        .scoreline {
            color: #94a3b8;
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
            background: #334155;
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
            background: #0f172a;
            font-weight: bold;
            cursor: pointer;
            border-radius: .15rem;
        }

        .cell.selected {
            background: #38bdf8;
            color: #0f172a;
        }

        .cell.found {
            background: #22c55e;
            color: #052e16;
            cursor: default;
        }

        .cell.wrong {
            background: #ef4444;
            color: #fff;
        }

        .hint {
            padding: .55rem .75rem;
            border-radius: .45rem;
            background: #0f172a;
            margin-bottom: .4rem;
            display: flex;
            justify-content: space-between;
            gap: 1rem;
        }

        .hint.found {
            color: #22c55e;
            text-decoration: line-through;
        }

        .hints {
            min-width: 220px;
        }

        .result {
            padding: .8rem;
            border-radius: .45rem;
            margin-bottom: 1rem;
            display: none;
            font-weight: 600;
        }

        .result.ok {
            display: block;
            background: #052e16;
            color: #22c55e;
        }

        .result.bad {
            display: block;
            background: #450a0a;
            color: #f87171;
        }

        .finish {
            display: none;
            padding: .8rem;
            border-radius: .45rem;
            background: #14532d;
            color: #bbf7d0;
            font-weight: 700;
            text-align: center;
            margin-top: 1rem;
        }

        @media (max-width: 720px) {
            .grid {
                grid-template-columns: repeat(var(--cols), 2rem) !important;
            }
        }
    </style>
</head>

<body>
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
        </div>
    </div>

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

        cells.forEach((cell) => {
            cell.addEventListener('click', () => {
                if (cell.dataset.found === '1') {
                    return;
                }
                if (start === null) {
                    start = cell;
                    cell.classList.add('selected');
                } else {
                    cellsBetween(start, cell).forEach((c) => c.classList.remove('selected'));
                    cell.classList.add('selected');
                    fetchAnswer(start, cell);
                }
            });
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

                document.getElementById('expire-form').submit();

                return;
            }

            remainingSeconds--;
        }

        let timer = setInterval(updateTimer, 1000);

        updateTimer();
    </script>
</body>

</html>
