<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $activity->title }}</title>
    @include('partials.assets', ['theme' => 'student'])
    <style>
        :root {
            --cw-ink: #183b3a;
            --cw-muted: #587371;
            --cw-line: #d7e8e4;
            --cw-surface: #fff;
            --cw-soft: #f3faf7;
            --cw-deep: #075e58;
            --cw-lime: #c6f36a;
        }
        * { box-sizing: border-box; }
        body.student-kids {
            min-height: 100vh;
            width: min(1240px, 100%);
            margin: 0 auto;
            padding: 22px 16px;
            color: var(--cw-ink);
            background: radial-gradient(circle at 8% 0%, #d9f3e8 0, transparent 31%), #eff6f2;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            font-size: 17px;
        }
        .crossword-card { overflow: hidden; border: 1px solid var(--cw-line); border-radius: 26px; background: var(--cw-surface); box-shadow: 0 24px 64px rgb(23 69 60 / .12); }
        .game-header { display: flex; align-items: center; justify-content: space-between; gap: 24px; padding: 22px 28px; color: #fff; background: radial-gradient(circle at 82% 0%, rgb(198 243 106 / .2), transparent 25%), linear-gradient(120deg, #123c3a, #087f75 74%, #109789); }
        .eyebrow { margin: 0 0 5px; color: rgb(255 255 255 / .72); font-size: .72rem; font-weight: 850; letter-spacing: .13em; text-transform: uppercase; }
        .game-header h1 { margin: 0; color: #fff; font-size: clamp(1.65rem, 4vw, 2.2rem); line-height: 1.08; letter-spacing: -.025em; }
        .game-subtitle { margin: 7px 0 0; color: rgb(255 255 255 / .84); font-size: .95rem; }
        #timer { flex: 0 0 auto; min-width: 145px; padding: 10px 15px; border: 1px solid rgb(255 255 255 / .25); border-radius: 15px; background: rgb(255 255 255 / .12); text-align: center; backdrop-filter: blur(8px); }
        .timer-label { display: block; color: rgb(255 255 255 / .72); font-size: .68rem; font-weight: 800; letter-spacing: .07em; text-transform: uppercase; }
        #timer-value { display: block; margin-top: 2px; color: var(--cw-lime); font-size: 1.35rem; font-weight: 900; font-variant-numeric: tabular-nums; }
        .game-content { padding: 20px 24px 22px; }
        .game-summary { display: flex; flex-wrap: wrap; gap: 9px; margin: 0; }
        .game-summary span { display: inline-flex; align-items: baseline; gap: 7px; padding: 8px 12px; border: 1px solid var(--cw-line); border-radius: 12px; background: var(--cw-soft); color: var(--cw-muted); }
        #score-display { color: var(--cw-deep); font-weight: 900; }
        .how-to { display: flex; align-items: center; gap: 10px; margin: 12px 0 16px; padding: 10px 13px; border: 1px solid #bfe8d8; border-radius: 13px; background: #effbf5; color: #20584d; font-size: .95rem; }
        .how-to strong { flex: 0 0 auto; color: var(--cw-deep); }
        .play-layout { display: grid; grid-template-columns: minmax(0, 1.35fr) minmax(280px, .85fr); align-items: start; gap: 16px; }
        .panel { min-width: 0; padding: 17px; border: 1px solid var(--cw-line); border-radius: 19px; background: #fff; }
        .panel-heading { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; margin-bottom: 12px; }
        .panel-heading h2, .clue-panel h2 { margin: 0; color: var(--cw-ink); font-size: 1.05rem; font-weight: 850; }
        .panel-heading p { margin: 3px 0 0; color: var(--cw-muted); font-size: .82rem; }
        .board-scroll { max-width: 100%; overflow: auto; padding: 3px 2px 10px; overscroll-behavior-inline: contain; scrollbar-color: #8ab6a4 #e6f0ec; scrollbar-width: thin; }
        .board-scroll:focus-visible { outline: 3px solid #d97706; outline-offset: 2px; }
        #crossword-grid { justify-content: start; min-width: max-content; width: max-content !important; max-width: none !important; padding: 12px; border: 1px solid #335b56; border-radius: 15px; background: #173c39; box-shadow: inset 0 0 0 1px rgb(255 255 255 / .08); }
        #crossword-grid > div { flex: none; border-radius: 4px; }
        #crossword-grid input { border: 2px solid #cfdfda !important; border-radius: 6px !important; color: #183b3a !important; background: #fff !important; outline: none; font-weight: 850; transition: transform .12s, border-color .12s, box-shadow .12s; }
        #crossword-grid input:focus { z-index: 2; border-color: #07877b !important; box-shadow: 0 0 0 3px rgb(20 184 166 / .28); transform: scale(1.08); }
        #crossword-grid input.correct { border-color: #4d9c61 !important; background: #dff3e2 !important; color: #256c39 !important; }
        #crossword-grid input.incorrect { border-color: #c74747 !important; background: #fde9e6 !important; color: #8f302c !important; }
        .clue-panel { display: grid; gap: 16px; }
        .clue-group { min-width: 0; }
        .clue-group h2 { display: flex; align-items: center; gap: 8px; padding-bottom: 9px; border-bottom: 1px solid var(--cw-line); }
        .clue-group h2 span { color: var(--cw-muted); font-size: .76rem; font-weight: 700; }
        #clues-horizontal, #clues-vertical { display: grid; gap: 7px; max-height: 280px; overflow-y: auto; margin: 10px 0 0; padding: 0 3px 0 0; list-style: none; }
        #clues-horizontal li, #clues-vertical li { margin: 0; padding: 9px 11px; border: 1px solid #e1ece8; border-radius: 11px; background: #f8fbf9; color: #294a45; font-size: .9rem; line-height: 1.4; cursor: pointer; transition: border-color .15s, background .15s, color .15s; }
        #clues-horizontal li:hover, #clues-vertical li:hover { border-color: #9bd2c0; background: #effbf5; }
        #clues-horizontal li.active, #clues-vertical li.active { border-color: #14a394; background: #e2f6ef; color: #075e58; font-weight: 800; }
        .results-panel { margin-top: 16px; }
        #results { display: grid; gap: 7px; max-height: 220px; overflow-y: auto; margin-top: 11px; }
        #results p { margin: 0; padding: 10px 12px; border: 1px solid #dbe9e3; border-radius: 11px; background: #f3faf7; color: #31534d; font-size: .9rem; font-weight: 650; line-height: 1.45; }
        .game-actions { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-top: 15px; }
        .game-actions form { margin: 0 !important; text-align: left !important; }
        .game-actions button { min-height: 42px; padding: 8px 14px; border: 1px solid #e9c7c6; border-radius: 11px; background: #fff6f4; color: #9b3a35; font: inherit; font-size: .86rem; font-weight: 750; cursor: pointer; transition: background .15s, border-color .15s; }
        .game-actions button:hover { border-color: #d89b97; background: #ffede9; }
        .game-actions button:focus-visible, .game-actions a:focus-visible, #crossword-grid input:focus-visible { outline: 3px solid #f59e0b; outline-offset: 2px; }
        .game-actions nav a { display: inline-flex; align-items: center; min-height: 42px; padding: 8px 12px; border-radius: 11px; color: var(--cw-deep); font-weight: 750; text-decoration: none; }
        .game-actions nav a:hover { background: #eff8f4; }
        #crossword-grid.empty-board { display: block; width: auto !important; min-width: 0 !important; max-width: 100% !important; padding: 18px !important; color: #fff; font-weight: 700; }
        @media (max-width: 820px) {
            .play-layout { grid-template-columns: minmax(0, 1fr); }
            .clue-panel { grid-template-columns: repeat(2, minmax(0, 1fr)); align-items: start; }
            #clues-horizontal, #clues-vertical { max-height: 220px; }
        }
        @media (max-width: 600px) {
            body.student-kids { padding: 10px; font-size: 16px; }
            .crossword-card { border-radius: 20px; }
            .game-header { align-items: flex-start; gap: 10px; padding: 17px 15px; }
            .game-header h1 { font-size: clamp(1.3rem, 6vw, 1.8rem); overflow-wrap: anywhere; }
            .game-subtitle { max-width: 220px; font-size: .84rem; }
            #timer { min-width: 102px; padding: 8px 9px; }
            #timer-value { font-size: 1.12rem; }
            .game-content { padding: 13px; }
            .game-summary { display: block; }
            .game-summary span { padding: 7px 10px; font-size: .88rem; }
            .panel { padding: 12px; }
            #crossword-grid { padding: 9px; }
            .clue-panel { grid-template-columns: minmax(0, 1fr); gap: 13px; }
            #clues-horizontal, #clues-vertical { max-height: 190px; }
            .how-to { align-items: flex-start; font-size: .86rem; }
            .game-actions { align-items: stretch; flex-direction: column; }
            .game-actions nav a { justify-content: center; width: 100%; }
            .game-actions form button { width: 100%; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { scroll-behavior: auto !important; transition-duration: .01ms !important; }
        }
    </style>
</head>

<body class="student-kids">
    @include('partials.toast')
    <main class="crossword-card">
        <header class="game-header">
            <div>
                <p class="eyebrow">Actividad interactiva</p>
                <h1>🧩 {{ $activity->title }}</h1>
                <p class="game-subtitle">¡Adivina las palabras!</p>
            </div>
            <div id="timer" aria-label="Tiempo restante">
                <span class="timer-label">Tiempo restante</span>
                <span id="timer-value">--:--</span>
            </div>
        </header>

        <div class="game-content">
            <p class="game-summary" aria-label="Puntaje de la actividad">
                <span>Puntos <strong id="score-display">0</strong></span>
            </p>
            <p class="how-to"><strong>Cómo jugar</strong><span>Haz clic en una pista y escribe la respuesta desde la primera casilla. En los cruces, elige la otra pista para cambiar de dirección; las casillas compartidas siguen disponibles. Usa las flechas para moverte.</span></p>

            <div class="play-layout">
                <section class="panel board-panel" aria-label="Tablero del crucigrama">
                    <div class="panel-heading">
                        <div>
                            <h2>Crucigrama</h2>
                            <p>Completa las casillas siguiendo las pistas.</p>
                        </div>
                    </div>
                    <div class="board-scroll" role="region" aria-label="Tablero; desplaza para ver todas las casillas" tabindex="0">
                        <div id="crossword-grid"></div>
                    </div>
                </section>

                <section class="panel clue-panel" aria-label="Pistas del crucigrama">
                    <div class="clue-group">
                        <h2>Horizontales <span>→</span></h2>
                        <ul id="clues-horizontal"></ul>
                    </div>
                    <div class="clue-group">
                        <h2>Verticales <span>↓</span></h2>
                        <ul id="clues-vertical"></ul>
                    </div>
                </section>
            </div>

            <section class="panel results-panel" aria-label="Resultados de las respuestas">
                <div class="panel-heading">
                    <div>
                        <h2>Resultados</h2>
                        <p>Revisa aquí tus respuestas y puntos obtenidos.</p>
                    </div>
                </div>
                <div id="results" aria-live="polite">
                    <p id="results-empty">Todavía no has respondido ninguna palabra.</p>
                </div>
            </section>

            <div class="game-actions">
                <form method="POST" action="{{ route('student.participation.abandon', $activity->id) }}"
                    onsubmit="return confirm('¿Seguro que quieres dejar el juego?');">
                    @csrf
                    <button type="submit">Dejar el juego 👋</button>
                </form>
                <nav>
                    <a href="{{ route('student.activities.show', $activity->id) }}">Volver a la actividad</a>
                </nav>
            </div>
        </div>
    </main>

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
        let expirationQueued = false;

        const PARTICIPATION_ID = {{ $participation->id }};
        const CSRF_TOKEN = '{{ csrf_token() }}';
        const GRID = @json($crossword->grid);
        const WORDS = @json($wordsJson);

        let remainingSeconds = @json($remainingSeconds);

        // Un dato inválido no debe impedir que se dibujen las demás palabras.
        // Se aceptan únicamente coordenadas enteras, direcciones conocidas y
        // respuestas no vacías; no se registran pistas ni respuestas en consola.
        const VALID_WORDS = (Array.isArray(WORDS) ? WORDS : []).filter((word, index) => {
            const rawRow = word?.row;
            const rawColumn = word?.column;
            const row = Number(word?.row);
            const column = Number(word?.column);
            const direction = word?.direction;
            const answer = word?.word;
            const valid = rawRow !== null && rawRow !== '' && Number.isInteger(row) && row >= 0 &&
                rawColumn !== null && rawColumn !== '' && Number.isInteger(column) && column >= 0 &&
                (direction === 'horizontal' || direction === 'vertical') &&
                typeof answer === 'string' && answer.trim().length > 0;

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
                if (expirationQueued) return;
                expirationQueued = true;
                document.querySelectorAll('button, input:not([type="hidden"]), select, textarea').forEach(control => {
                    control.disabled = true;
                });
                const grid = document.getElementById('crossword-grid');
                if (grid) grid.style.pointerEvents = 'none';
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

        const gridRows = Array.isArray(GRID) ? GRID.length : 0;
        const gridCols = Array.isArray(GRID)
            ? GRID.reduce((max, row) => Math.max(max, Array.isArray(row) ? row.length : 0), 0)
            : 0;
        // Completa o reconstruye dimensiones con los extremos de las palabras
        // válidas. Los índices son cero-based y las intersecciones comparten
        // la misma celda mediante inputRefs.
        const wordRows = VALID_WORDS.reduce((max, word) => Math.max(
            max,
            Number(word.row) + (word.direction === 'vertical' ? word.word.length : 1)
        ), 0);
        const wordCols = VALID_WORDS.reduce((max, word) => Math.max(
            max,
            Number(word.column) + (word.direction === 'horizontal' ? word.word.length : 1)
        ), 0);
        const ROWS = Math.max(gridRows, wordRows);
        const COLS = Math.max(gridCols, wordCols);

        const cellMap = Array.from({
            length: ROWS
        }, () => Array(COLS).fill(false));
        const cellNumbers = Array.from({
            length: ROWS
        }, () => Array(COLS).fill(null));
        const inputRefs = {};

        let currentDirection = 'horizontal';
        let totalScore = 0;

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
            gridElement.textContent = Array.isArray(WORDS) && WORDS.length > 0
                ? 'No hay palabras válidas para mostrar en este crucigrama.'
                : 'Este crucigrama todavía no tiene palabras disponibles.';
            gridElement.setAttribute('role', 'status');
            gridElement.style.display = 'block';
            gridElement.classList.add('empty-board');
        } else {
            // Conserva todas las columnas y permite desplazamiento horizontal
            // en móviles; no se usa content-visibility para no afectar las medidas.
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

            document.querySelectorAll('#clues li').forEach(clue => {
                clue.classList.remove('active');
            });

            const clueElement = document.getElementById(`clue-${word.id}`);
            if (clueElement) {
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
                    input.classList.remove('correct', 'incorrect');
                    if (!neededByAnotherWord) {
                        input.classList.add(result.is_correct ? 'correct' : 'incorrect');
                    }
                    input.style.background = '';
                }
            }

            // Marcar pista como respondida
            const clueElement = document.getElementById(`clue-${word.id}`);
            if (clueElement) {
                clueElement.style.textDecoration = 'line-through';
            }

            // Mostrar resultado en el panel
            const resultsElement = document.getElementById('results');
            document.getElementById('results-empty')?.remove();
            const resultItem = document.createElement('p');

            resultItem.textContent = result.is_correct ?
                `✓ "${word.clue}" — Correcto (+${result.score} pts)` :
                `✗ "${word.clue}" — Incorrecto (la respuesta era: ${result.correct_word})`;

            resultsElement.appendChild(resultItem);
            if (window.KlassioToast) {
                if (result.is_correct) {
                    KlassioToast.success('¡Correcto!', `"${word.clue}" (+${result.score} pts)`);
                } else {
                    KlassioToast.error('Incorrecto', `"${word.clue}" (era: ${result.correct_word})`);
                }
            }

            // Actualizar puntaje
            if (result.is_correct) {
                totalScore += result.score;
            }

            document.getElementById('score-display').textContent = totalScore;

            // Comprobar si se completó todo el crucigrama
            // Comprobar si se completó todo el crucigrama
            if (result.completed) {
                window.KlassioSounds?.completado();

                const completed = document.createElement('p');

                completed.textContent =
                    '¡Crucigrama completado! Redirigiendo al resultado...';

                resultsElement.appendChild(completed);

                // Deshabilitar todas las entradas
                Object.values(inputRefs).forEach(input => {
                    input.disabled = true;
                });

                // Redirigir al resultado de Participation
                setTimeout(() => {

                    window.location.href = @json(route('student.participation.result', $activity->id));

                }, 1200);
            }
        }
    </script>

</body>

</html>
