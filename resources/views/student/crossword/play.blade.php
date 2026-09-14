<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $activity->title }}</title>
    @include('partials.assets', ['theme' => 'student'])
    <style>
        body { max-width: 1120px; margin: 0 auto; padding: 2rem 1rem 3rem; color: #172554; }
        body > h1 { display: inline-block; margin: 0; font-size: clamp(2rem, 5vw, 3.25rem); font-weight: 800; color: #0f766e; }
        body > h2 { margin: .5rem 0 1.5rem; color: #475569; font-size: 1.2rem; }
        #timer { float: right; margin-top: .25rem; padding: .75rem 1rem; border-radius: 1rem; background: #fff; box-shadow: 0 .5rem 1.5rem rgb(15 23 42 / .1); font-weight: 700; color: #0f766e; }
        #timer-value { color: #7c3aed; font-variant-numeric: tabular-nums; }
        body > p { max-width: 700px; color: #475569; }
        body > section { background: #fff; border-radius: 1.25rem; padding: 1.5rem; margin: 1.25rem 0; box-shadow: 0 .75rem 1.75rem rgb(15 23 42 / .08); }
        section h2 { margin-top: 0; color: #0f766e; font-weight: 800; }
        #crossword-grid { max-width: 100%; overflow: auto; padding: .75rem; border-radius: 1rem; background: #0f172a; box-shadow: inset 0 0 0 1px rgb(255 255 255 / .1); }
        #crossword-grid input { border: 2px solid #cbd5e1; border-radius: .35rem; color: #0f172a; outline: none; transition: transform .15s, border-color .15s; }
        #crossword-grid input:focus { border-color: #14b8a6; box-shadow: 0 0 0 3px rgb(20 184 166 / .25); transform: scale(1.05); }
        #clues-horizontal, #clues-vertical { padding-left: 1.25rem; }
        #clues-horizontal li, #clues-vertical li { margin: .6rem 0; padding: .65rem .8rem; border-radius: .75rem; background: #f0fdfa; color: #334155; }
        #results p { padding: .75rem 1rem; border-radius: .75rem; background: #eff6ff; margin: .5rem 0; }
        button { border: 0; border-radius: .8rem; padding: .75rem 1rem; font-weight: 700; background: #e11d48; color: #fff; cursor: pointer; }
        nav a { display: inline-block; color: #0f766e; font-weight: 700; text-decoration: none; padding: .7rem 1rem; }
        @media (max-width: 600px) { #timer { float: none; display: inline-block; margin-bottom: 1rem; } body > section { padding: 1rem; } }
    </style>
</head>

<body>

    <h1>{{ $activity->title }}</h1>

    <h2>Crucigrama</h2>
    <div id="timer">
        Tiempo restante:
        <span id="timer-value">--:--</span>
    </div>

    <p>
        Completa las palabras utilizando las pistas.
    </p>

    <p>
        Puntaje:
        <strong id="score-display">0</strong>
    </p>

    <hr>

    <section>
        <h2>Crucigrama</h2>

        <div id="crossword-grid"></div>
    </section>

    <section>
        <h2>Horizontales</h2>

        <ul id="clues-horizontal"></ul>

        <h2>Verticales</h2>

        <ul id="clues-vertical"></ul>
    </section>

    <section>
        <h2>Resultados</h2>

        <div id="results"></div>
    </section>

    <hr>

    <form method="POST" action="{{ route('student.participation.abandon', $activity->id) }}"
        onsubmit="return confirm('¿Estás seguro de que quieres abandonar esta actividad?');"
        style="margin: 1rem 0; text-align: center;">
        @csrf

        <button type="submit">
            Abandonar actividad
        </button>
    </form>


    <nav>
        <a href="{{ route('student.activities.show', $activity->id) }}">
            Volver a la actividad
        </a>
    </nav>

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

    <script>
        const PARTICIPATION_ID = {{ $participation->id }};
        const CSRF_TOKEN = '{{ csrf_token() }}';
        const GRID = @json($crossword->grid);
        const WORDS = @json($wordsJson);

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

        const ROWS = GRID.length;
        const COLS = ROWS > 0 ? GRID[0].length : 0;

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

        WORDS.forEach(word => {
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
        gridElement.style.display = 'inline-grid';
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

                cell.appendChild(input);
                inputRefs[`${row}-${column}`] = input;
                gridElement.appendChild(cell);
            }
        }

        // -------------------------------------------------------------------------
        // Renderizar pistas
        // -------------------------------------------------------------------------

        const cluesHorizontal = document.getElementById('clues-horizontal');
        const cluesVertical = document.getElementById('clues-vertical');

        WORDS.forEach(word => {
            const clue = document.createElement('li');
            clue.id = `clue-${word.id}`;
            clue.textContent = `${wordClueNumbers[word.id]}. ${word.clue}`;
            clue.style.cursor = 'pointer';
            clue.addEventListener('click', () => focusWord(word));

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
            const primary = WORDS.find(word => {
                if (word.answered) return false;
                if (word.direction !== currentDirection) return false;
                return cellBelongsToWord(row, column, word);
            });

            if (primary) return primary;

            return WORDS.find(word => {
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

            for (let i = 0; i < word.word.length; i++) {
                const row = word.direction === 'horizontal' ? word.row : word.row + i;
                const column = word.direction === 'horizontal' ? word.column + i : word.column;
                const input = inputRefs[`${row}-${column}`];

                if (input) {
                    input.disabled = true;
                    input.classList.add(result.is_correct ? 'correct' : 'incorrect');
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
            const resultItem = document.createElement('p');

            resultItem.textContent = result.is_correct ?
                `✓ "${word.clue}" — Correcto (+${result.score} pts)` :
                `✗ "${word.clue}" — Incorrecto (la respuesta era: ${result.correct_word})`;

            resultsElement.appendChild(resultItem);

            // Actualizar puntaje
            if (result.is_correct) {
                totalScore += result.score;
            }

            document.getElementById('score-display').textContent = totalScore;

            // Comprobar si se completó todo el crucigrama
            // Comprobar si se completó todo el crucigrama
            if (result.completed) {

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
