<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sopa de Letras — Multijugador</title>

    @if ((bool) config('broadcasting.connections.reverb.enabled', false))
        <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
        <script>
            window.Echo = new Echo({
                broadcaster: 'reverb',
                key: @json(config('broadcasting.connections.reverb.key')),
                wsHost: @json(config('broadcasting.connections.reverb.client_host', 'localhost')),
                wsPort: @json(config('broadcasting.connections.reverb.options.port', 8080)),
                forceTLS: @json(config('broadcasting.connections.reverb.options.scheme', 'http') === 'https'),
                enabledTransports: ['ws', 'wss']
            });
        </script>
    @endif

    @include('partials.assets', ['theme' => 'student', 'includeBootstrap' => false])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; }

        /* === Internos del juego (JS modifica estas clases) === */
        :root { --ws-cell-size: clamp(1.8rem, 3vw, 2.25rem); }

        .ws-grid { display: grid; width: max-content; gap: 3px; margin: 0 auto; padding: 7px; border: 1px solid #fed7aa; border-radius: 15px; background: #ffedd5; user-select: none; }
        .cell { display: flex; width: var(--ws-cell-size); height: var(--ws-cell-size); align-items: center; justify-content: center; border: 1px solid #ffedd5; border-radius: 7px; background: #fff; color: #431407; font-size: .9rem; font-weight: 800; cursor: pointer; touch-action: none; transition: background-color .12s, color .12s, transform .12s, box-shadow .12s; }
        .cell:focus-visible { outline: 3px solid #ea580c; outline-offset: 1px; }
        .cell.selected { z-index: 1; transform: scale(1.04); background: #ea580c; color: #fff; box-shadow: inset 0 0 0 2px #fdba74; }
        .cell.found { border-color: #a7f3d0; background: #059669; color: #fff; cursor: default; }
        .cell.wrong { background: #dc2626; color: #fff; }

        .hint.found { border-color: #a7f3d0; background: #ecfdf5; color: #065f46; }
        .hint.found span:first-child { text-decoration: line-through; }

        .result { display: none; }
        .result.ok { display: block; margin-top: .75rem; padding: .65rem .9rem; border: 1px solid #a7f3d0; border-radius: .75rem; background: #ecfdf5; color: #065f46; font-size: .875rem; font-weight: 700; }
        .result.bad { display: block; margin-top: .75rem; padding: .65rem .9rem; border: 1px solid #fecaca; border-radius: .75rem; background: #fef2f2; color: #991b1b; font-size: .875rem; font-weight: 700; }

        .race-chip { max-width: 100%; padding: .25rem .65rem; border: 1px solid #e2e8f0; border-radius: 999px; background: #f8fafc; color: #475569; font-size: .78rem; font-weight: 700; overflow-wrap: anywhere; }
        .race-chip.me { border-color: #fdba74; background: #ffedd5; color: #9a3412; }

        .grid-scroll { max-width: 100%; overflow: auto; overscroll-behavior-inline: contain; scrollbar-width: thin; }
        .grid-scroll:focus-visible { outline: 3px solid #ea580c; outline-offset: 2px; }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { scroll-behavior: auto !important; transition-duration: .01ms !important; }
        }
    </style>
</head>

<body class="student-kids">

    <div class="mx-auto w-full max-w-5xl px-3 py-4 sm:px-5 sm:py-6">

        {{-- Encabezado --}}
        <header class="rounded-2xl bg-orange-700 p-4 text-white shadow-sm sm:p-6">

            <div class="flex items-start justify-between gap-3">

                <div class="flex min-w-0 items-start gap-3">

                    <div class="hidden h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-white/15 ring-1 ring-white/20 sm:flex">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </div>

                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-orange-200 sm:text-xs">
                            Actividad multijugador
                        </p>
                        <h1 class="mt-0.5 text-lg font-bold tracking-tight sm:mt-1 sm:text-2xl">
                            Sopa de letras
                        </h1>
                        <p class="mt-0.5 truncate text-xs font-semibold text-orange-100 sm:text-sm">
                            {{ $wordsearch->activity->title }}
                        </p>
                    </div>

                </div>

                <div class="shrink-0 rounded-xl bg-white/15 px-3 py-2 text-center ring-1 ring-white/25" aria-label="Tiempo restante">
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-orange-100">
                        Tiempo
                    </span>
                    <div id="timer" class="font-mono text-lg font-bold tabular-nums text-white sm:text-xl">
                        <span id="timer-value">--:--</span>
                    </div>
                </div>

            </div>

        </header>

        {{-- Carrera --}}
        <section class="mt-3 rounded-2xl border border-slate-200 bg-white p-3.5 shadow-sm sm:p-4" aria-label="Estado de la partida">

            <div class="flex flex-wrap items-center justify-between gap-2">

                <span class="inline-flex items-center gap-2 text-sm font-bold text-slate-800">
                    <svg class="h-4.5 w-4.5 shrink-0 text-orange-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0-3.375-6.375m-7.125 0L6 18.75m4.875-9.75L9.75 2.25h4.5l-1.125 6.75h-2.25Z" />
                    </svg>
                    Sala
                    <span class="rounded-full bg-orange-700 px-2.5 py-1 font-mono text-xs font-bold tracking-[0.15em] text-white">
                        {{ $session->code }}
                    </span>
                    <span class="font-semibold text-slate-500">{{ $activity->mode === 'team' ? 'Carrera por equipos' : 'Carrera individual' }}</span>
                </span>

                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600">
                    <strong id="race-count">—</strong> participantes
                </span>

            </div>

            <div id="race-standings" class="mt-3 flex max-h-24 flex-wrap gap-1.5 overflow-y-auto" role="list" aria-label="Clasificación en vivo; desplazable" tabindex="0"></div>

        </section>

        {{-- Resumen --}}
        <p class="mt-3 flex gap-2 sm:gap-3" aria-label="Resumen de la actividad">

            <span class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm shadow-sm sm:justify-start sm:px-4">
                <svg class="h-4 w-4 shrink-0 text-orange-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0-3.375-6.375m-7.125 0L6 18.75m4.875-9.75L9.75 2.25h4.5l-1.125 6.75h-2.25Z" />
                </svg>
                <span class="font-semibold text-slate-500">Puntos</span>
                <span class="font-bold text-orange-700"><span id="points">{{ $earnedPoints }}</span> / {{ $maxScore }}</span>
            </span>

            <span class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm shadow-sm sm:justify-start sm:px-4">
                <svg class="h-4 w-4 shrink-0 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>
                <span class="font-semibold text-slate-500">Encontradas</span>
                <span class="font-bold text-emerald-700"><span id="words-count">{{ $foundCount }}</span> / {{ $words->count() }}</span>
            </span>

        </p>

        {{-- Cómo jugar --}}
        <p class="mt-3 flex items-start gap-2 rounded-xl bg-orange-50 px-3.5 py-2.5 text-sm text-orange-900 ring-1 ring-orange-100">
            <svg class="mt-0.5 h-4 w-4 shrink-0 text-orange-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 0 4.51-2.104m0 0A9.04 9.04 0 0 0 12 3a9.04 9.04 0 0 0-6.76 12.986M3.75 19.5a12.06 12.06 0 0 1 4.51-2.104m0 0a6.01 6.01 0 0 0 1.5-.189m0 0a6.01 6.01 0 0 1 1.5-.189" />
            </svg>
            <span><strong class="font-bold">Cómo jugar:</strong> toca o haz clic en la primera letra y arrastra hasta la última.</span>
        </p>

        {{-- Resultado de la selección --}}
        <div id="result" class="result" role="status" aria-live="polite"></div>

        {{-- Tablero + palabras --}}
        <div class="mt-3 grid grid-cols-1 items-start gap-3 lg:grid-cols-[minmax(0,1fr)_320px]">

            <section class="min-w-0 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm sm:p-4" aria-label="Tablero de sopa de letras">

                <div class="flex items-center justify-between gap-2">
                    <div class="min-w-0">
                        <h2 class="text-base font-bold text-slate-900 sm:text-lg">
                            Encuentra las palabras
                        </h2>
                        <p class="mt-0.5 text-xs text-slate-500">
                            Selecciona letras en línea recta.
                            <span class="lg:hidden">Desliza el tablero para ver todas las letras.</span>
                        </p>
                    </div>
                    <span class="shrink-0 rounded-full bg-orange-100 px-2.5 py-1 text-xs font-bold text-orange-700">
                        {{ $wordsearch->rows }} × {{ $wordsearch->columns }}
                    </span>
                </div>

                <div class="grid-scroll mt-3 rounded-xl" role="region" aria-label="Tablero; desplaza para ver las letras que no caben" tabindex="0">
                    <div class="ws-grid" id="grid"
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

            <aside class="min-w-0 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm sm:p-4" aria-label="Palabras por encontrar">

                <div class="flex items-center justify-between gap-2 border-b border-slate-200 pb-3">
                    <h2 class="text-base font-bold text-slate-900 sm:text-lg">
                        Palabras
                    </h2>
                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600">
                        {{ $words->count() }} en total
                    </span>
                </div>

                <div class="mt-3 grid max-h-[38vh] gap-2 overflow-y-auto pr-1 sm:grid-cols-2 lg:max-h-[52vh] lg:grid-cols-1">
                    @forelse ($words as $word)
                        <div class="hint flex min-h-10 items-center justify-between gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-bold text-slate-700 {{ in_array($word->id, $foundIds) ? 'found' : '' }}"
                            data-word-id="{{ $word->id }}">
                            <span class="break-words">{{ $word->word }}</span>
                            <span class="shrink-0 text-xs font-bold text-slate-400">{{ $word->score }} pts</span>
                        </div>
                    @empty
                        <p class="col-span-full rounded-xl border border-dashed border-slate-300 px-3 py-3 text-sm text-slate-500">
                            Aún no hay palabras disponibles para esta actividad.
                        </p>
                    @endforelse
                </div>

            </aside>

        </div>

        {{-- Fin de la partida --}}
        <div id="finish" class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-center font-bold text-emerald-800" role="status" aria-live="polite" style="display: none;">
            ¡Lo lograste!
        </div>

        <div class="mt-4 flex justify-end border-t border-slate-200 pt-4">
            <form method="POST" action="{{ route('student.game-sessions.leave', $session->id) }}"
                onsubmit="return confirm('¿Estás seguro de que quieres salir de esta sala?');">
                @csrf
                <button type="submit"
                    class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-red-200 bg-white px-4 py-2.5 text-sm font-bold text-red-600 shadow-sm transition hover:bg-red-50 hover:text-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l3 3m0 0-3 3m3-3H9" />
                    </svg>
                    Salir de la sala
                </button>
            </form>
        </div>

    </div>

    <form id="expire-form" method="POST" action="{{ route('student.participation.expire', $activity->id) }}"
        style="display: none;">
        @csrf
    </form>

    @php
        $wordPositions = $words->map(fn ($word) => [
            'id' => (int) $word->id,
            'row' => (int) $word->row,
            'column' => (int) $word->column,
            'direction' => $word->direction,
            'length' => mb_strlen($word->word),
            'word' => $word->word,
        ])->values();
    @endphp

    <script src="/js/klassio-sounds.js"></script>
    <script>
        window.KlassioSounds?.setup('wordsearch');
        const MULTIPLAYER = true;
        const stateUrl = @json(route('student.game-sessions.state', $session->id));
        const sessionResultUrl = @json(route('student.game-sessions.result', $session->id));
        const myParticipationId = {{ $participation->id }};
        let navigationStarted = false;
        const wordPositions = @json($wordPositions);

        const cells = Array.from(document.querySelectorAll('.cell'));
        const foundKey = new Set(@json($foundIds).map(Number));
        const directions = {
            horizontal: [0, 1],
            vertical: [1, 0],
            diagonal: [1, 1],
            horizontal_reverse: [0, -1],
            vertical_reverse: [-1, 0],
            diagonal_reverse: [-1, -1],
            diagonal_alt: [1, -1],
            diagonal_alt_reverse: [-1, 1],
        };
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

        function markWordFound(wordId) {
            const word = wordPositions.find((candidate) => candidate.id === Number(wordId));
            const [rowStep, columnStep] = directions[word?.direction] || [];
            if (!word || rowStep === undefined || columnStep === undefined) return false;

            for (let offset = 0; offset < word.length; offset++) {
                const cell = document.getElementById(
                    `cell-${word.row + rowStep * offset}-${word.column + columnStep * offset}`
                );

                if (cell) {
                    cell.classList.add('found');
                    cell.dataset.found = '1';
                }
            }

            document.querySelector(`.hint[data-word-id="${Number(wordId)}"]`)?.classList.add('found');
            foundKey.add(Number(wordId));
            return true;
        }

        function markCellsFound(coordinates) {
            if (!Array.isArray(coordinates)) return;

            coordinates.forEach((coordinate) => {
                if (!Array.isArray(coordinate) || coordinate.length < 2) return;
                const [row, column] = coordinate;
                const cell = document.getElementById(`cell-${Number(row)}-${Number(column)}`);
                if (!cell) return;
                cell.classList.add('found');
                cell.dataset.found = '1';
            });
        }

        foundKey.forEach(markWordFound);

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
                participation_id: {{ $participation->id }},
                session_id: {{ $session->id }},
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
                            window.KlassioSounds?.completado();

                            const finish = document.getElementById('finish');

                            finish.textContent =
                                '¡Completaste la sopa de letras! Esperando a los demás...';
                            finish.style.display = 'block';
                        }

                        /*
                         * PALABRA YA ENCONTRADA
                         */
                    } else if (data.already_found) {
                        window.KlassioSounds?.error();

                        markCellsFound(data.cells);
                        const matchedWord = wordPositions.find((word) => word.word === data.word);
                        if (matchedWord) markWordFound(matchedWord.id);

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
            if (!Array.isArray(list)) return;
            list.forEach((cell) => {
                if (!(cell instanceof Element)) return;
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

        function syncTeamProgress(progress) {
            if (!progress || progress.participation_id !== myParticipationId) return;

            points = Number(progress.score) || 0;
            document.getElementById('points').textContent = points;

            progress.completed_item_ids.forEach((wordId) => {
                const normalizedId = Number(wordId);
                markWordFound(normalizedId);
            });

            updateFound(foundKey.size);

            if (progress.status === 'completed') {
                const finish = document.getElementById('finish');
                finish.textContent = 'Tu equipo completó la actividad. Esperando a los demás equipos...';
                finish.style.display = 'block';
                cells.forEach((cell) => {
                    cell.dataset.found = '1';
                });
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
                window.KlassioSounds?.click();
                dragging = true;
                start = cell;
                end = cell;
                previewSelection(cell);
            });
        });

        document.getElementById('grid').addEventListener('pointermove', (event) => {
            if (!dragging || !start) return;
            const target = document.elementFromPoint(event.clientX, event.clientY)?.closest('.cell');
            if (target && target.dataset.found !== '1') {
                end = target;
                previewSelection(target);
            }
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

                return;
            }

            remainingSeconds--;
        }

        let timer = setInterval(updateTimer, 1000);

        updateTimer();

        let pollStateInProgress = false;

        async function pollState() {
            if (pollStateInProgress) return;
            pollStateInProgress = true;

            try {
                const state = await fetch(stateUrl, {
                    headers: { 'Accept': 'application/json' }
                }).then((response) => response.json());

                syncTeamProgress(state.team_progress);

                if (state.time_remaining !== null) {
                    remainingSeconds = state.time_remaining;
                }

                const rankedPlayers = [...state.players].sort((first, second) =>
                    (Number(second.score) || 0) - (Number(first.score) || 0)
                );
                document.getElementById('race-count').textContent = rankedPlayers.length;
                const entries = rankedPlayers.map((player, index) => {
                    const me = player.participation_id === myParticipationId ? ' me' : '';
                    const chip = document.createElement('span');
                    chip.className = 'race-chip' + me;
                    chip.setAttribute('role', 'listitem');
                    chip.textContent = `${index + 1}. ${player.name}: ${player.score}${player.status === 'completed' ? ' ✓' : ''}`;
                    return chip;
                });

                document.getElementById('race-standings').replaceChildren(...entries);

                if (state.status === 'finished') {
                    if (!navigationStarted) {
                        navigationStarted = true;
                        window.location.href = sessionResultUrl;
                    }
                }
            } catch (error) {
                console.error('Error al consultar el estado:', error);
            } finally {
                pollStateInProgress = false;
            }
        }

        const reverbEnabled = @json((bool) config('broadcasting.connections.reverb.enabled', false));
        if (window.Echo && reverbEnabled) {
            window.Echo.private('game-session.{{ $session->id }}')
                .listen('.ScoreUpdated', (e) => pollState())
                .listen('.GameFinished', (e) => pollState())
                .listen('.PlayerJoined', (e) => pollState())
                .listen('.PlayerLeft', (e) => pollState());
        }

        function setupStateSync(refreshFn) {
            let fallbackPolling = null;

            const startFallback = () => {
                if (fallbackPolling !== null) return;

                refreshFn();
                fallbackPolling = window.setInterval(refreshFn, 2000);
            };

            const stopFallback = () => {
                if (fallbackPolling === null) return;

                window.clearInterval(fallbackPolling);
                fallbackPolling = null;
            };

            const connection = window.Echo?.connector?.pusher?.connection;

            if (!connection) {
                startFallback();
                return;
            }

            connection.bind('state_change', ({
                current
            }) => {
                if (current === 'connected') {
                    stopFallback();
                    refreshFn();
                } else {
                    startFallback();
                }
            });

            if (connection.state === 'connected') {
                refreshFn();
            } else {
                startFallback();
            }
        }

        setupStateSync(pollState);
    </script>
</body>

</html>
