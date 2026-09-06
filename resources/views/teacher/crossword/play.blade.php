<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $activity->title }}</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }

        h1 { margin-bottom: 4px; }
        h2 { margin-top: 0; color: #555; font-weight: normal; }

        /* Grid del crucigrama */
        #crossword-grid {
            display: inline-grid;
            gap: 2px;
            margin-top: 20px;
        }

        .cell {
            width: 36px;
            height: 36px;
            position: relative;
        }

        .cell input {
            width: 100%;
            height: 100%;
            text-align: center;
            text-transform: uppercase;
            font-size: 16px;
            font-weight: bold;
            border: 1px solid #999;
            box-sizing: border-box;
            cursor: pointer;
            background: #fff;
            padding: 0;
        }

        .cell input:focus {
            outline: 2px solid #2563eb;
            background: #eff6ff;
        }

        .cell input.correct {
            background: #d1fae5;
            border-color: #10b981;
            color: #065f46;
        }

        .cell input.incorrect {
            background: #fee2e2;
            border-color: #ef4444;
            color: #991b1b;
        }

        .cell input:disabled {
            cursor: default;
        }

        /* Celda bloqueada (no pertenece a ninguna palabra) */
        .cell.blocked {
            background: #1f2937;
            border: 1px solid #1f2937;
        }

        /* Número de pista en la esquina superior izquierda */
        .cell-number {
            position: absolute;
            top: 1px;
            left: 2px;
            font-size: 9px;
            color: #374151;
            pointer-events: none;
            z-index: 1;
            line-height: 1;
        }

        /* Panel de pistas */
        #clues {
            display: flex;
            gap: 40px;
            margin-top: 24px;
        }

        #clues h3 {
            margin-bottom: 8px;
        }

        #clues ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        #clues li {
            margin-bottom: 6px;
            cursor: pointer;
            padding: 4px 6px;
            border-radius: 4px;
        }

        #clues li:hover {
            background: #f3f4f6;
        }

        #clues li.active {
            background: #dbeafe;
            font-weight: bold;
        }

        #clues li.done {
            color: #10b981;
            text-decoration: line-through;
        }

        /* Resultados por palabra */
        #results {
            margin-top: 24px;
        }

        .result-item {
            padding: 6px 10px;
            margin-bottom: 4px;
            border-radius: 4px;
            font-size: 14px;
        }

        .result-correct   { background: #d1fae5; color: #065f46; }
        .result-incorrect { background: #fee2e2; color: #991b1b; }

        #score-display {
            margin-top: 16px;
            font-size: 18px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<h1>{{ $activity->title }}</h1>
<h2>Crucigrama</h2>

<div id="crossword-grid"></div>

<div id="clues">
    <div>
        <h3>Horizontales</h3>
        <ul id="clues-horizontal"></ul>
    </div>
    <div>
        <h3>Verticales</h3>
        <ul id="clues-vertical"></ul>
    </div>
</div>

<div id="results"></div>
<div id="score-display"></div>

<script>
    // -------------------------------------------------------------------------
    // Datos del servidor
    // -------------------------------------------------------------------------
    const PARTICIPATION_ID = {{ $participation->id }};
    const CSRF_TOKEN       = '{{ csrf_token() }}';

    const GRID = @json(json_decode($crossword->grid));

    const WORDS = @json($words->map(fn($w) => [
        'id'        => $w->id,
        'word'      => $w->word,
        'clue'      => $w->clue,
        'row'       => $w->row,
        'column'    => $w->column,
        'direction' => $w->direction,
        'score'     => $w->score,
        'answered'  => false,
    ]));

    // -------------------------------------------------------------------------
    // Estado
    // -------------------------------------------------------------------------
    const ROWS    = GRID.length;
    const COLS    = ROWS > 0 ? GRID[0].length : 0;

    // Mapa de qué celdas pertenecen a palabras: cellMap[r][c] = true/false
    const cellMap = Array.from({length: ROWS}, () => Array(COLS).fill(false));
    // Número de pista a mostrar en la celda
    const cellNumbers = Array.from({length: ROWS}, () => Array(COLS).fill(null));

    let totalScore = 0;

    // -------------------------------------------------------------------------
    // Construir mapa de celdas y números de pista
    // -------------------------------------------------------------------------
    let clueNumber = 1;
    const wordClueNumbers = {}; // wordId -> número de pista

    WORDS.forEach(w => {
        const len = w.word.length;
        for (let i = 0; i < len; i++) {
            const r = w.direction === 'horizontal' ? w.row           : w.row + i;
            const c = w.direction === 'horizontal' ? w.column + i    : w.column;
            cellMap[r][c] = true;
        }
        // El número va en la celda inicial
        if (cellNumbers[w.row][w.column] === null) {
            cellNumbers[w.row][w.column] = clueNumber;
            wordClueNumbers[w.id] = clueNumber;
            clueNumber++;
        } else {
            wordClueNumbers[w.id] = cellNumbers[w.row][w.column];
        }
    });

    // -------------------------------------------------------------------------
    // Renderizar grid
    // -------------------------------------------------------------------------
    const gridEl = document.getElementById('crossword-grid');
    gridEl.style.gridTemplateColumns = `repeat(${COLS}, 36px)`;
    gridEl.style.gridTemplateRows    = `repeat(${ROWS}, 36px)`;

    const inputRefs = {}; // "r-c" -> <input>

    for (let r = 0; r < ROWS; r++) {
        for (let c = 0; c < COLS; c++) {
            const cell = document.createElement('div');
            cell.classList.add('cell');

            if (! cellMap[r][c]) {
                cell.classList.add('blocked');
                gridEl.appendChild(cell);
                continue;
            }

            // Número de pista
            if (cellNumbers[r][c] !== null) {
                const numEl = document.createElement('span');
                numEl.classList.add('cell-number');
                numEl.textContent = cellNumbers[r][c];
                cell.appendChild(numEl);
            }

            const input = document.createElement('input');
            input.type      = 'text';
            input.maxLength = 1;
            input.dataset.row = r;
            input.dataset.col = c;

            input.addEventListener('input', onInput);
            input.addEventListener('keydown', onKeyDown);
            input.addEventListener('focus', onFocus);

            cell.appendChild(input);
            inputRefs[`${r}-${c}`] = input;
            gridEl.appendChild(cell);
        }
    }

    // -------------------------------------------------------------------------
    // Renderizar pistas
    // -------------------------------------------------------------------------
    const cluesH = document.getElementById('clues-horizontal');
    const cluesV = document.getElementById('clues-vertical');

    WORDS.forEach(w => {
        const li = document.createElement('li');
        li.id          = `clue-${w.id}`;
        li.textContent = `${wordClueNumbers[w.id]}. ${w.clue}`;
        li.addEventListener('click', () => focusWord(w));

        if (w.direction === 'horizontal') {
            cluesH.appendChild(li);
        } else {
            cluesV.appendChild(li);
        }
    });

    // -------------------------------------------------------------------------
    // Manejo de input
    // -------------------------------------------------------------------------
    function onInput(e) {
        const input = e.target;
        // Permitir solo letras
        input.value = input.value.replace(/[^a-záéíóúüñA-ZÁÉÍÓÚÜÑ]/g, '').toUpperCase().slice(-1);

        if (input.value) {
            moveNext(input);
        }

        // Verificar si la palabra está completa para enviar automáticamente
        const word = getWordAtCell(parseInt(input.dataset.row), parseInt(input.dataset.col));
        if (word && isWordComplete(word)) {
            submitWord(word);
        }
    }

    function onKeyDown(e) {
        const input = e.target;
        const r = parseInt(input.dataset.row);
        const c = parseInt(input.dataset.col);

        if (e.key === 'Backspace' && input.value === '') {
            movePrev(input);
        }

        if (e.key === 'ArrowRight') { e.preventDefault(); focusCell(r, c + 1); }
        if (e.key === 'ArrowLeft')  { e.preventDefault(); focusCell(r, c - 1); }
        if (e.key === 'ArrowDown')  { e.preventDefault(); focusCell(r + 1, c); }
        if (e.key === 'ArrowUp')    { e.preventDefault(); focusCell(r - 1, c); }
    }

    function onFocus(e) {
        const r = parseInt(e.target.dataset.row);
        const c = parseInt(e.target.dataset.col);
        highlightWord(r, c);
    }

    // -------------------------------------------------------------------------
    // Navegación
    // -------------------------------------------------------------------------
    let currentDirection = 'horizontal';

    function focusCell(r, c) {
        const key = `${r}-${c}`;
        if (inputRefs[key] && !inputRefs[key].disabled) {
            inputRefs[key].focus();
        }
    }

    function focusWord(word) {
        focusCell(word.row, word.column);
        currentDirection = word.direction;
    }

    function moveNext(input) {
        const r = parseInt(input.dataset.row);
        const c = parseInt(input.dataset.col);
        if (currentDirection === 'horizontal') {
            focusCell(r, c + 1);
        } else {
            focusCell(r + 1, c);
        }
    }

    function movePrev(input) {
        const r = parseInt(input.dataset.row);
        const c = parseInt(input.dataset.col);
        if (currentDirection === 'horizontal') {
            focusCell(r, c - 1);
        } else {
            focusCell(r - 1, c);
        }
    }

    function highlightWord(r, c) {
        // Quitar highlights anteriores
        Object.values(inputRefs).forEach(inp => {
            if (!inp.classList.contains('correct') && !inp.classList.contains('incorrect')) {
                inp.style.background = '';
            }
        });

        // Encontrar la palabra activa
        const word = getWordAtCell(r, c);
        if (!word) return;

        currentDirection = word.direction;

        const len = word.word.length;
        for (let i = 0; i < len; i++) {
            const wr = word.direction === 'horizontal' ? word.row           : word.row + i;
            const wc = word.direction === 'horizontal' ? word.column + i    : word.column;
            const inp = inputRefs[`${wr}-${wc}`];
            if (inp && !inp.classList.contains('correct') && !inp.classList.contains('incorrect')) {
                inp.style.background = '#fef9c3';
            }
        }

        // Resaltar pista activa
        document.querySelectorAll('#clues li').forEach(li => li.classList.remove('active'));
        const clueEl = document.getElementById(`clue-${word.id}`);
        if (clueEl) clueEl.classList.add('active');
    }

    function getWordAtCell(r, c) {
        // Preferir la dirección actual
        const primary = WORDS.find(w => {
            if (w.answered) return false;
            if (w.direction !== currentDirection) return false;
            return cellBelongsToWord(r, c, w);
        });
        if (primary) return primary;

        return WORDS.find(w => {
            if (w.answered) return false;
            return cellBelongsToWord(r, c, w);
        });
    }

    function cellBelongsToWord(r, c, word) {
        const len = word.word.length;
        for (let i = 0; i < len; i++) {
            const wr = word.direction === 'horizontal' ? word.row           : word.row + i;
            const wc = word.direction === 'horizontal' ? word.column + i    : word.column;
            if (wr === r && wc === c) return true;
        }
        return false;
    }

    function isWordComplete(word) {
        const len = word.word.length;
        for (let i = 0; i < len; i++) {
            const r = word.direction === 'horizontal' ? word.row           : word.row + i;
            const c = word.direction === 'horizontal' ? word.column + i    : word.column;
            const inp = inputRefs[`${r}-${c}`];
            if (!inp || !inp.value) return false;
        }
        return true;
    }

    function getWordResponse(word) {
        let response = '';
        const len = word.word.length;
        for (let i = 0; i < len; i++) {
            const r = word.direction === 'horizontal' ? word.row           : word.row + i;
            const c = word.direction === 'horizontal' ? word.column + i    : word.column;
            response += (inputRefs[`${r}-${c}`]?.value || '');
        }
        return response;
    }

    // -------------------------------------------------------------------------
    // Envío de respuesta
    // -------------------------------------------------------------------------
    async function submitWord(word) {
        const response = getWordResponse(word);

        let result;
        try {
            const res = await fetch(`/crossword/answer`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                },
                body: JSON.stringify({
                    participation_id:  PARTICIPATION_ID,
                    crossword_word_id: word.id,
                    response:          response,
                }),
            });
            result = await res.json();
        } catch (err) {
            console.error('Error al enviar respuesta:', err);
            return;
        }

        word.answered = true;

        const len = word.word.length;
        for (let i = 0; i < len; i++) {
            const r = word.direction === 'horizontal' ? word.row           : word.row + i;
            const c = word.direction === 'horizontal' ? word.column + i    : word.column;
            const inp = inputRefs[`${r}-${c}`];
            if (inp) {
                inp.disabled = true;
                inp.classList.add(result.is_correct ? 'correct' : 'incorrect');
                inp.style.background = '';
            }
        }

        // Marcar pista como respondida
        const clueEl = document.getElementById(`clue-${word.id}`);
        if (clueEl) clueEl.classList.add('done');

        // Mostrar resultado
        const resultsEl = document.getElementById('results');
        const item = document.createElement('div');
        item.classList.add('result-item', result.is_correct ? 'result-correct' : 'result-incorrect');
        item.textContent = result.is_correct
            ? `✓ "${word.clue}" — Correcto (+${result.score} pts)`
            : `✗ "${word.clue}" — Incorrecto (la respuesta era: ${result.correct_word})`;
        resultsEl.appendChild(item);

        if (result.is_correct) {
            totalScore += result.score;
        }

        document.getElementById('score-display').textContent = `Puntaje acumulado: ${totalScore}`;

        // Verificar si todas las palabras fueron respondidas
        if (WORDS.every(w => w.answered)) {
            const div = document.createElement('div');
            div.style.marginTop = '16px';
            div.style.fontWeight = 'bold';
            div.textContent = '¡Crucigrama completado!';
            document.getElementById('results').appendChild(div);
        }
    }
</script>

</body>
</html>