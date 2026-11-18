<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $activity->title }}</title>

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

        /* === Internos del tablero (JS modifica estas clases) === */
        .play-layout { display: grid; grid-template-columns: minmax(0, 1fr); align-items: start; gap: .75rem; }

        .clue-panel { display: grid; grid-template-columns: minmax(0, 1fr); gap: .9rem; }
        .clue-group { min-width: 0; }
        .clue-group h2 { display: flex; align-items: center; gap: .5rem; padding-bottom: .55rem; border-bottom: 1px solid #e2e8f0; }

        #clues-horizontal, #clues-vertical { display: grid; gap: 7px; max-height: 240px; overflow-y: auto; margin: .6rem 0 0; padding: 0 3px 0 0; list-style: none; }
        #clues-horizontal li, #clues-vertical li { margin: 0; padding: 9px 11px; border: 1px solid #e2e8f0; border-radius: .7rem; background: #f8fafc; color: #334155; font-size: .9rem; font-weight: 600; line-height: 1.4; cursor: pointer; transition: border-color .15s, background .15s, color .15s; }
        #clues-horizontal li:hover, #clues-vertical li:hover { border-color: #fcd34d; background: #fffbeb; }
        #clues-horizontal li.active, #clues-vertical li.active { border-color: #d97706; background: #fffbeb; color: #78350f; font-weight: 800; }

        .board-scroll { max-width: 100%; overflow: auto; padding: 3px 2px 10px; overscroll-behavior-inline: contain; scrollbar-width: thin; }
        .board-scroll:focus-visible { outline: 3px solid #d97706; outline-offset: 2px; }

        #crossword-grid { justify-content: start; min-width: max-content; width: max-content !important; max-width: none !important; padding: 10px; border: 1px solid #fcd34d; border-radius: 12px; background: #fffbeb; }
        #crossword-grid > div { flex: none; border-radius: 4px; }
        #crossword-grid span { color: #92400e; font-weight: 800; }
        #crossword-grid input { border: 2px solid #e2e8f0 !important; border-radius: 6px !important; color: #1e293b !important; background: #fff !important; outline: none; font-weight: 800; transition: transform .12s, border-color .12s, box-shadow .12s; }
        #crossword-grid input:focus { z-index: 2; border-color: #d97706 !important; box-shadow: 0 0 0 3px rgb(217 119 6 / .25); transform: scale(1.08); }
        #crossword-grid input.correct { border-color: #059669 !important; background: #ecfdf5 !important; color: #065f46 !important; }
        #crossword-grid input.incorrect { border-color: #dc2626 !important; background: #fef2f2 !important; color: #991b1b !important; }
        #crossword-grid.empty-board { display: block; width: auto !important; min-width: 0 !important; max-width: 100% !important; padding: 18px !important; color: #78350f; font-weight: 700; }

        #results { display: grid; gap: 7px; max-height: 220px; overflow-y: auto; }
        #results p { margin: 0; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: .7rem; background: #f8fafc; color: #334155; font-size: .9rem; font-weight: 600; line-height: 1.45; }

        .race-chip { max-width: 100%; padding: .25rem .65rem; border: 1px solid #e2e8f0; border-radius: 999px; background: #f8fafc; color: #475569; font-size: .78rem; font-weight: 700; overflow-wrap: anywhere; }
        .race-chip.me { border-color: #fcd34d; background: #fffbeb; color: #92400e; }

        @media (min-width: 640px) {
            .clue-panel { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (min-width: 1024px) {
            .play-layout { grid-template-columns: minmax(0, 1.35fr) minmax(280px, .85fr); gap: 1rem; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { scroll-behavior: auto !important; transition-duration: .01ms !important; }
        }
    </style>
</head>

<body class="student-kids">
    @include('partials.toast')

    <div class="mx-auto w-full max-w-6xl px-3 py-4 sm:px-5 sm:py-6">

        <a href="{{ route('student.activities.show', $activity->id) }}"
            class="mb-3 inline-flex min-h-11 items-center gap-1.5 rounded-xl px-2 py-2 text-sm font-bold text-slate-600 transition hover:bg-white hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500">
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            Volver a la actividad
        </a>

        {{-- Encabezado --}}
        <header class="rounded-2xl bg-amber-700 p-4 text-white shadow-sm sm:p-6">

            <div class="flex items-start justify-between gap-3">

                <div class="flex min-w-0 items-start gap-3">

                    <div class="hidden h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-white/15 ring-1 ring-white/20 sm:flex">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 6.087c0-.355.186-.676.401-.88.398-.378.878-.58 1.402-.58h.05c1.219 0 2.25.996 2.25 2.25 0 .49-.148.935-.401 1.322-.284.437-.46.977-.46 1.546 0 .836.26 1.63.733 2.293.474.663 1.15 1.153 1.955 1.373.195.052.4.08.612.08 1.219 0 2.25.996 2.25 2.25 0 1.247-.99 2.25-2.25 2.25-.303 0-.596-.06-.86-.17-.505-.21-1.08-.327-1.68-.327-.58 0-1.14.108-1.657.313-.504.198-1.07.305-1.66.305-1.247 0-2.25-.991-2.25-2.25 0-.31.06-.605.17-.877.207-.507.327-1.08.327-1.68 0-.6-.12-1.173-.327-1.68A2.246 2.246 0 0 0 10.5 10.5c-.58 0-1.14.108-1.657.313-.504.198-1.07.305-1.66.305-1.247 0-2.25-.991-2.25-2.25 0-1.254 1.031-2.25 2.25-2.25.212 0 .417.028.612.08.804.22 1.48.71 1.955 1.373.473.663.733 1.457.733 2.293 0 .57-.176 1.11-.46 1.547" />
                        </svg>
                    </div>

                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-amber-200 sm:text-xs">
                            Actividad multijugador
                        </p>
                        <h1 class="mt-0.5 break-words text-lg font-bold tracking-tight sm:mt-1 sm:text-2xl">
                            Crucigrama
                        </h1>
                        <p class="mt-0.5 break-words text-xs font-semibold text-amber-100 sm:text-sm">
                            {{ $activity->title }}
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

        {{-- Carrera --}}
        <section class="mt-3 rounded-2xl border border-slate-200 bg-white p-3.5 shadow-sm sm:p-4" aria-label="Estado de la partida">

            <div class="flex flex-wrap items-center justify-between gap-2">

                <span class="inline-flex items-center gap-2 text-sm font-bold text-slate-800">
                    <svg class="h-4.5 w-4.5 shrink-0 text-amber-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0-3.375-6.375m-7.125 0L6 18.75m4.875-9.75L9.75 2.25h4.5l-1.125 6.75h-2.25Z" />
                    </svg>
                    Sala
                    <span class="rounded-full bg-amber-700 px-2.5 py-1 font-mono text-xs font-bold tracking-[0.15em] text-white">
                        {{ $session->code }}
                    </span>
                    <span class="font-semibold text-slate-500">{{ $activity->mode === 'team' ? 'Carrera por equipos' : 'Carrera individual' }}</span>
                </span>

                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600">
                    <strong id="race-count">—</strong> participantes
                </span>

            </div>

            <div id="race-standings" class="mt-3 flex max-h-24 flex-wrap gap-1.5 overflow-y-auto" role="list" aria-label="Clasificación en vivo; desplazable" aria-live="polite" tabindex="0"></div>

        </section>

        {{-- Resumen --}}
        <p class="mt-3 flex" aria-label="Puntaje de la actividad">
            <span class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm shadow-sm sm:justify-start sm:px-4">
                <svg class="h-4 w-4 shrink-0 text-amber-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0-3.375-6.375m-7.125 0L6 18.75m4.875-9.75L9.75 2.25h4.5l-1.125 6.75h-2.25Z" />
                </svg>
                <span class="font-semibold text-slate-500">Puntos</span>
                <span class="font-bold text-amber-700"><span id="score-display">0</span></span>
            </span>
        </p>

        {{-- Cómo jugar --}}
        <p class="mt-3 flex items-start gap-2 rounded-xl bg-amber-50 px-3.5 py-2.5 text-sm text-amber-900 ring-1 ring-amber-100">
            <svg class="mt-0.5 h-4 w-4 shrink-0 text-amber-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 0 4.51-2.104m0 0A9.04 9.04 0 0 0 12 3a9.04 9.04 0 0 0-6.76 12.986M3.75 19.5a12.06 12.06 0 0 1 4.51-2.104m0 0a6.01 6.01 0 0 0 1.5-.189m0 0a6.01 6.01 0 0 1 1.5-.189" />
            </svg>
            <span><strong class="font-bold">Cómo jugar:</strong> haz clic en una pista y escribe la respuesta desde la primera casilla. En los cruces, las casillas compartidas siguen disponibles. Usa las flechas para moverte.</span>
        </p>

        {{-- Tablero + pistas --}}
        <div class="play-layout mt-3">

            <section class="min-w-0 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm sm:p-4" aria-label="Tablero del crucigrama">
                <h2 class="text-base font-bold text-slate-900 sm:text-lg">
                    Crucigrama
                </h2>
                <p class="mt-0.5 text-xs text-slate-500">
                    Completa las casillas siguiendo las pistas.
                    <span class="lg:hidden">Desliza el tablero para ver todas las casillas.</span>
                </p>
                <div class="board-scroll mt-3" role="region" aria-label="Tablero; desplaza para ver todas las casillas" tabindex="0">
                    <div id="crossword-grid"></div>
                </div>
            </section>

            <section class="min-w-0 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm sm:p-4" aria-label="Pistas del crucigrama">
                <div class="clue-panel">
                    <div class="clue-group">
                        <h2 class="text-base font-bold text-slate-900">
                            Horizontales
                            <svg class="h-4 w-4 shrink-0 text-amber-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </h2>
                        <ul id="clues-horizontal"></ul>
                    </div>
                    <div class="clue-group">
                        <h2 class="text-base font-bold text-slate-900">
                            Verticales
                            <svg class="h-4 w-4 shrink-0 text-amber-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
                            </svg>
                        </h2>
                        <ul id="clues-vertical"></ul>
                    </div>
                </div>
            </section>

        </div>

        {{-- Resultados --}}
        <section class="mt-3 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm sm:p-4" aria-label="Resultados de las respuestas">
            <h2 class="text-base font-bold text-slate-900 sm:text-lg">
                Resultados
            </h2>
            <p class="mt-0.5 text-xs text-slate-500">
                Revisa aquí tus respuestas y puntos obtenidos.
            </p>
            <div id="results" class="mt-3" aria-live="polite">
                <p id="results-empty">Todavía no has respondido ninguna palabra.</p>
            </div>
        </section>

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

    @php
        $wordsJson = $words->map(
            fn($word) => [
                'id' => $word->id,
                'word' => $word->word,
                'clue' => $word->clue,
                'row' => $word->row,
                'column' => $word->column,
                'direction' => $word->direction,
                'score' => $word->score,
                'answered' => false,
            ],
        );
    @endphp

    <form id="expire-form" method="POST" action="{{ route('student.participation.expire', $activity->id) }}"
        style="display: none;">
        @csrf
    </form>

    <script src="/js/klassio-sounds.js"></script>
    <script>
        window.KlassioSounds?.setup('crossword');
        const MULTIPLAYER = true;
        const PARTICIPATION_ID = {{ $participation->id }};
        const SESSION_ID = {{ $session->id }};
        const CSRF_TOKEN = '{{ csrf_token() }}';
        const GRID = @json($crossword->grid);
        const WORDS = @json($wordsJson);
        const stateUrl = @json(route('student.game-sessions.state', $session->id));
        const sessionResultUrl = @json(route('student.game-sessions.result', $session->id));
        const myParticipationId = {{ $participation->id }};
        let navigationStarted = false;

        let remainingSeconds = @json($remainingSeconds);

        const VALID_WORDS = (Array.isArray(WORDS) ? WORDS : []).filter((word, index) => {
            const row = Number(word?.row);
            const column = Number(word?.column);
            const valid = word?.row !== null && word?.row !== '' && Number.isInteger(row) && row >= 0 &&
                word?.column !== null && word?.column !== '' && Number.isInteger(column) && column >= 0 &&
                (word?.direction === 'horizontal' || word?.direction === 'vertical') &&
                typeof word?.word === 'string' && word.word.trim().length > 0;

            if (!valid) {
                console.warn(`Se omitió una palabra inválida del crucigrama (índice ${index}).`);
                return false;
            }

            word.row = row;
            word.column = column;
            return true;
        });

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

        const gridRows = Array.isArray(GRID) ? GRID.length : 0;
        const gridColumns = Array.isArray(GRID)
            ? GRID.reduce((max, row) => Math.max(max, Array.isArray(row) ? row.length : 0), 0)
            : 0;
        const wordRows = VALID_WORDS.reduce((max, word) => Math.max(
            max,
            word.row + (word.direction === 'vertical' ? word.word.length : 1)
        ), 0);
        const wordColumns = VALID_WORDS.reduce((max, word) => Math.max(
            max,
            word.column + (word.direction === 'horizontal' ? word.word.length : 1)
        ), 0);
        const ROWS = Math.max(gridRows, wordRows);
        const COLS = Math.max(gridColumns, wordColumns);

        const cellMap = Array.from({
            length: ROWS
        }, () => Array(COLS).fill(false));
        const cellNumbers = Array.from({
            length: ROWS
        }, () => Array(COLS).fill(null));
        const inputRefs = {};

        let currentDirection = 'horizontal';
        let totalScore = 0;
        let teamFinishedMessageShown = false;
        const resultWordIds = new Set();

        // -------------------------------------------------------------------------
        // Construir mapa de celdas y números de pista
        // -------------------------------------------------------------------------

        let clueNumber = 1;
        const wordClueNumbers = {};

        VALID_WORDS.forEach(word => {
            const length = word.word.length;

            for (let i = 0; i < length; i++) {
                const row = word.direction === 'horizontal' ? word.row : word.row + i;
                const column = word.direction === 'horizontal' ? word.column + i : word.column;
                cellMap[row][column] = true;
            }

            if (cellNumbers[word.row][word.column] === null) {
                cellNumbers[word.row][word.column] = clueNumber;
                wordClueNumbers[word.id] = clueNumber;
                clueNumber++;
            } else {
                wordClueNumbers[word.id] = cellNumbers[word.row][word.column];
            }
        });

        // -------------------------------------------------------------------------
        // Renderizar cuadrícula
        // -------------------------------------------------------------------------

        const gridElement = document.getElementById('crossword-grid');
        const hasPlayableBoard = ROWS > 0 && COLS > 0 && VALID_WORDS.length > 0;
        if (!hasPlayableBoard) {
            gridElement.textContent = VALID_WORDS.length > 0
                ? 'No hay palabras válidas para mostrar en este crucigrama.'
                : 'Este crucigrama todavía no tiene palabras disponibles.';
            gridElement.setAttribute('role', 'status');
            gridElement.classList.add('empty-board');
        } else {
            gridElement.style.display = 'grid';
            gridElement.style.width = '100%';
            gridElement.style.gridTemplateColumns = `repeat(${COLS}, 36px)`;
            gridElement.style.gridTemplateRows = `repeat(${ROWS}, 36px)`;
            gridElement.style.gap = '2px';

            for (let row = 0; row < ROWS; row++) {
                for (let column = 0; column < COLS; column++) {

                    const cell = document.createElement('div');
                    cell.style.width = '36px';
                    cell.style.height = '36px';
                    cell.style.position = 'relative';

                // Celda bloqueada
                    if (!cellMap[row][column]) {
                        cell.style.background = '#1f2937';
                        gridElement.appendChild(cell);
                        continue;
                    }

                // Número de pista
                    if (cellNumbers[row][column] !== null) {
                        const number = document.createElement('span');
                        number.textContent = cellNumbers[row][column];
                        number.style.position = 'absolute';
                        number.style.top = '1px';
                        number.style.left = '2px';
                        number.style.fontSize = '9px';
                        number.style.zIndex = '1';
                        cell.appendChild(number);
                    }

                // Input
                    const input = document.createElement('input');
                    input.type = 'text';
                    input.maxLength = 1;
                    input.dataset.row = row;
                    input.dataset.column = column;
                    input.style.width = '36px';
                    input.style.height = '36px';
                    input.style.boxSizing = 'border-box';
                    input.style.textAlign = 'center';
                    input.style.textTransform = 'uppercase';
                    input.style.fontSize = '16px';
                    input.style.fontWeight = 'bold';

                    input.addEventListener('input', onInput);
                    input.addEventListener('keydown', onKeyDown);
                    input.addEventListener('focus', onFocus);
                    input.addEventListener('pointerdown', () => window.KlassioSounds?.click());

                    cell.appendChild(input);
                    inputRefs[`${row}-${column}`] = input;
                    gridElement.appendChild(cell);
                }
            }
        }

        // -------------------------------------------------------------------------
        // Renderizar pistas
        // -------------------------------------------------------------------------

        const cluesHorizontal = document.getElementById('clues-horizontal');
        const cluesVertical = document.getElementById('clues-vertical');

        VALID_WORDS.forEach(word => {
            const clue = document.createElement('li');
            clue.id = `clue-${word.id}`;
            clue.textContent = `${wordClueNumbers[word.id]}. ${word.clue}`;
            clue.style.cursor = 'pointer';
            clue.addEventListener('click', () => {
                window.KlassioSounds?.click();
                focusWord(word);
            });

            if (word.direction === 'horizontal') {
                cluesHorizontal.appendChild(clue);
            } else {
                cluesVertical.appendChild(clue);
            }
        });

        // -------------------------------------------------------------------------
        // Entrada de letras
        // -------------------------------------------------------------------------

        function onInput(event) {
            const input = event.target;

            input.value = input.value
                .replace(/[^a-záéíóúüñA-ZÁÉÍÓÚÜÑ]/g, '')
                .toUpperCase()
                .slice(-1);

            if (input.value) {
                moveNext(input);
            }

            const row = parseInt(input.dataset.row);
            const column = parseInt(input.dataset.column);
            const word = getWordAtCell(row, column);

            if (word && isWordComplete(word)) {
                submitWord(word);
            }
        }

        // -------------------------------------------------------------------------
        // Teclado
        // -------------------------------------------------------------------------

        function onKeyDown(event) {
            const input = event.target;
            const row = parseInt(input.dataset.row);
            const column = parseInt(input.dataset.column);

            if (event.key === 'Backspace' && input.value === '') {
                movePrevious(input);
            }

            if (event.key === 'ArrowRight') {
                event.preventDefault();
                focusCell(row, column + 1);
            }
            if (event.key === 'ArrowLeft') {
                event.preventDefault();
                focusCell(row, column - 1);
            }
            if (event.key === 'ArrowDown') {
                event.preventDefault();
                focusCell(row + 1, column);
            }
            if (event.key === 'ArrowUp') {
                event.preventDefault();
                focusCell(row - 1, column);
            }
        }

        // -------------------------------------------------------------------------
        // Focus
        // -------------------------------------------------------------------------

        function onFocus(event) {
            const row = parseInt(event.target.dataset.row);
            const column = parseInt(event.target.dataset.column);
            highlightWord(row, column);
        }

        // -------------------------------------------------------------------------
        // Navegación
        // -------------------------------------------------------------------------

        function focusCell(row, column) {
            const input = inputRefs[`${row}-${column}`];
            if (input && !input.disabled) {
                input.focus();
            }
        }

        function focusWord(word) {
            currentDirection = word.direction;
            focusCell(word.row, word.column);
        }

        function moveNext(input) {
            const row = parseInt(input.dataset.row);
            const column = parseInt(input.dataset.column);

            if (currentDirection === 'horizontal') {
                focusCell(row, column + 1);
            } else {
                focusCell(row + 1, column);
            }
        }

        function movePrevious(input) {
            const row = parseInt(input.dataset.row);
            const column = parseInt(input.dataset.column);

            if (currentDirection === 'horizontal') {
                focusCell(row, column - 1);
            } else {
                focusCell(row - 1, column);
            }
        }

        // -------------------------------------------------------------------------
        // Resaltar palabra activa
        // -------------------------------------------------------------------------

        function highlightWord(row, column) {
            Object.values(inputRefs).forEach(input => {
                if (!input.classList.contains('correct') && !input.classList.contains('incorrect')) {
                    input.style.background = '';
                }
            });

            const word = getWordAtCell(row, column);
            if (!word) return;

            currentDirection = word.direction;

            for (let i = 0; i < word.word.length; i++) {
                const wordRow = word.direction === 'horizontal' ? word.row : word.row + i;
                const wordColumn = word.direction === 'horizontal' ? word.column + i : word.column;
                const input = inputRefs[`${wordRow}-${wordColumn}`];

                if (input && !input.classList.contains('correct') && !input.classList.contains('incorrect')) {
                    input.style.background = '#fef9c3';
                }
            }

            document.querySelectorAll('#clues-horizontal li, #clues-vertical li').forEach(clue => {
                clue.classList.remove('active');
                clue.style.fontWeight = '';
            });

            const clueElement = document.getElementById(`clue-${word.id}`);
            if (clueElement) {
                clueElement.classList.add('active');
                clueElement.style.fontWeight = 'bold';
            }
        }

        // -------------------------------------------------------------------------
        // Buscar palabra en una celda
        // -------------------------------------------------------------------------

        function getWordAtCell(row, column) {
            const primary = VALID_WORDS.find(word => {
                if (word.answered) return false;
                if (word.direction !== currentDirection) return false;
                return cellBelongsToWord(row, column, word);
            });

            if (primary) return primary;

            return VALID_WORDS.find(word => {
                if (word.answered) return false;
                return cellBelongsToWord(row, column, word);
            });
        }

        function cellBelongsToWord(row, column, word) {
            for (let i = 0; i < word.word.length; i++) {
                const wordRow = word.direction === 'horizontal' ? word.row : word.row + i;
                const wordColumn = word.direction === 'horizontal' ? word.column + i : word.column;
                if (wordRow === row && wordColumn === column) return true;
            }
            return false;
        }

        // -------------------------------------------------------------------------
        // Comprobar palabra completa
        // -------------------------------------------------------------------------

        function isWordComplete(word) {
            for (let i = 0; i < word.word.length; i++) {
                const row = word.direction === 'horizontal' ? word.row : word.row + i;
                const column = word.direction === 'horizontal' ? word.column + i : word.column;
                const input = inputRefs[`${row}-${column}`];
                if (!input || !input.value) return false;
            }
            return true;
        }

        // -------------------------------------------------------------------------
        // Obtener respuesta actual del grid
        // -------------------------------------------------------------------------

        function getWordResponse(word) {
            let response = '';
            for (let i = 0; i < word.word.length; i++) {
                const row = word.direction === 'horizontal' ? word.row : word.row + i;
                const column = word.direction === 'horizontal' ? word.column + i : word.column;
                response += inputRefs[`${row}-${column}`]?.value || '';
            }
            return response;
        }

        // -------------------------------------------------------------------------
        // Enviar respuesta al servidor
        // -------------------------------------------------------------------------

        async function submitWord(word) {
            const response = getWordResponse(word);

            let result;

            try {
                const responseHttp = await fetch('{{ route('student.crossword.answer') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        participation_id: PARTICIPATION_ID,
                        session_id: SESSION_ID,
                        crossword_word_id: word.id,
                        response: response,
                    }),
                });

                result = await responseHttp.json();

                if (!responseHttp.ok) {
                    console.error('Error del servidor:', result);
                    return;
                }

            } catch (error) {
                console.error('Error al enviar respuesta:', error);
                return;
            }

            // Marcar palabra como respondida
            word.answered = true;
            window.KlassioSounds?.[result.is_correct ? 'correcto' : 'error']();

            for (let i = 0; i < word.word.length; i++) {
                const row = word.direction === 'horizontal' ? word.row : word.row + i;
                const column = word.direction === 'horizontal' ? word.column + i : word.column;
                const input = inputRefs[`${row}-${column}`];

                if (input) {
                    const neededByAnotherWord = VALID_WORDS.some(candidate =>
                        !candidate.answered && cellBelongsToWord(row, column, candidate)
                    );

                    input.disabled = !neededByAnotherWord;
                    input.classList.remove('incorrect');
                    if (!input.dataset.solvedCorrect) {
                        input.classList.remove('correct');
                    }
                    if (result.is_correct) {
                        input.dataset.solvedCorrect = 'true';
                        input.classList.add('correct');
                    } else if (!neededByAnotherWord && !input.dataset.solvedCorrect) {
                        input.classList.add('incorrect');
                    }
                    input.style.background = '';
                }
            }

            // Marcar pista como respondida
            const clueElement = document.getElementById(`clue-${word.id}`);
            if (clueElement) {
                clueElement.style.textDecoration = 'line-through';
            }

            // Actualizar puntaje
            if (result.is_correct) {
                totalScore += result.score;
            }

            document.getElementById('score-display').textContent = totalScore;
            appendWordResult(word, result.is_correct, result.score, result.correct_word);

            // Comprobar si se completó todo el crucigrama
            if (result.completed) {
                window.KlassioSounds?.[totalScore > 0 ? 'completado' : 'expirado']();

                const completed = document.createElement('p');

                completed.textContent =
                    '¡Crucigrama completado! Esperando a los demás...';

                document.getElementById('results').appendChild(completed);

                // Deshabilitar todas las entradas
                Object.values(inputRefs).forEach(input => {
                    input.disabled = true;
                });
            }
        }

        function appendWordResult(word, isCorrect, score = 0, correctWord = word.word) {
            const wordId = Number(word.id);
            if (resultWordIds.has(wordId)) return;

            document.getElementById('results-empty')?.remove();
            const resultItem = document.createElement('p');
            resultItem.textContent = isCorrect
                ? `✓ "${word.clue}" — Correcto (+${score} pts)`
                : `✗ "${word.clue}" — Incorrecto (la respuesta era: ${correctWord})`;
            document.getElementById('results').appendChild(resultItem);
            resultWordIds.add(wordId);
        }

        function syncTeamProgress(progress) {
            if (!progress || progress.participation_id !== PARTICIPATION_ID) return;

            totalScore = Number(progress.score) || 0;
            document.getElementById('score-display').textContent = totalScore;

            const completedIds = new Set(progress.completed_item_ids.map(Number));
            const incorrectIds = new Set(progress.incorrect_item_ids.map(Number));

                VALID_WORDS.forEach((word) => {
                if (!completedIds.has(Number(word.id)) || word.answered) return;

                word.answered = true;
                const isCorrect = !incorrectIds.has(Number(word.id));

                for (let offset = 0; offset < word.word.length; offset++) {
                    const row = word.direction === 'horizontal' ? word.row : word.row + offset;
                    const column = word.direction === 'horizontal' ? word.column + offset : word.column;
                    const input = inputRefs[`${row}-${column}`];

                    if (!input) continue;

                    if (isCorrect) {
                        input.value = word.word[offset].toUpperCase();
                        input.dataset.solvedCorrect = 'true';
                    }

                    const neededByAnotherWord = VALID_WORDS.some(candidate =>
                        !candidate.answered && cellBelongsToWord(row, column, candidate)
                    );

                    input.disabled = !neededByAnotherWord;
                    input.classList.remove('incorrect');
                    if (isCorrect) {
                        input.classList.add('correct');
                    } else if (!neededByAnotherWord && !input.dataset.solvedCorrect) {
                        input.classList.add('incorrect');
                    }
                    input.style.background = '';
                }

                const clueElement = document.getElementById(`clue-${word.id}`);
                if (clueElement) {
                    clueElement.style.textDecoration = 'line-through';
                }

                appendWordResult(word, isCorrect, isCorrect ? word.score : 0, word.word);
            });

            if (progress.status === 'completed') {
                Object.values(inputRefs).forEach((input) => {
                    input.disabled = true;
                });

                if (!teamFinishedMessageShown) {
                    const completed = document.createElement('p');
                    completed.textContent = 'Tu equipo completó el crucigrama. Esperando a los demás equipos...';
                    document.getElementById('results').appendChild(completed);
                    teamFinishedMessageShown = true;
                }
            }
        }

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

        const reverbEnabled = @json((bool) config('broadcasting.connections.reverb.enabled', false));
        if (window.Echo && reverbEnabled) {
            window.Echo.private('game-session.{{ $session->id }}')
                .listen('.ScoreUpdated', (e) => pollState())
                .listen('.GameFinished', (e) => pollState())
                .listen('.PlayerJoined', (e) => pollState())
                .listen('.PlayerLeft', (e) => pollState());
        }

        setupStateSync(pollState);
    </script>

</body>

</html>
