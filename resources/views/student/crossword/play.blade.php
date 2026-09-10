<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $activity->title }}</title>
</head>

<body>

    <h1>{{ $activity->title }}</h1>

    <h2>Crucigrama</h2>

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

    <script>
        const PARTICIPATION_ID = {{ $participation->id }};
        const CSRF_TOKEN = '{{ csrf_token() }}';
        const GRID = @json($crossword->grid);
        const WORDS = @json($wordsJson);

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
