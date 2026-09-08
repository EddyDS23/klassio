<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Sopa de Letras — Jugar</title>
    <style>
        body { font-family: system-ui, sans-serif; background: #0f172a; margin: 0; padding: 2rem; color: #e2e8f0; }
        .wrap { max-width: 900px; margin: 0 auto; }
        .card { background: #1e293b; border-radius: .75rem; padding: 2rem; }
        h1 { margin: 0 0 .25rem; }
        .scoreline { color: #94a3b8; margin-bottom: 1.5rem; }
        .layout { display: flex; gap: 2rem; align-items: flex-start; flex-wrap: wrap; }
        .grid { display: inline-grid; gap: 2px; background: #334155; padding: 2px; border-radius: .4rem; user-select: none; }
        .cell {
            width: 2rem; height: 2rem; display: flex; align-items: center; justify-content: center;
            background: #0f172a; font-weight: bold; cursor: pointer; border-radius: .15rem;
        }
        .cell.selected { background: #38bdf8; color: #0f172a; }
        .cell.found { background: #22c55e; color: #052e16; cursor: default; }
        .cell.wrong { background: #ef4444; color: #fff; }
        .hint { padding: .55rem .75rem; border-radius: .45rem; background: #0f172a; margin-bottom: .4rem; display: flex; justify-content: space-between; gap: 1rem; }
        .hint.found { color: #22c55e; text-decoration: line-through; }
        .hints { min-width: 220px; }
        .result { padding: .8rem; border-radius: .45rem; margin-bottom: 1rem; display: none; font-weight: 600; }
        .result.ok { display: block; background: #052e16; color: #22c55e; }
        .result.bad { display: block; background: #450a0a; color: #f87171; }
        .finish { display: none; padding: .8rem; border-radius: .45rem; background: #14532d; color: #bbf7d0; font-weight: 700; text-align: center; margin-top: 1rem; }
        @media (max-width: 720px) {
            .grid { grid-template-columns: repeat(var(--cols), 2rem) !important; }
        }
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <h1>Sopa de Letras</h1>
        <p class="scoreline">
            Actividad <?php echo e($wordsearch->activity->title); ?> ·
            Puntos: <strong id="points"><?php echo e($earnedPoints); ?></strong>
            / <?php echo e($maxScore); ?> · Palabras: <strong id="words-count"><?php echo e($foundCount); ?></strong>/<?php echo e($words->count()); ?>

        </p>

        <div id="result" class="result"></div>

        <div class="layout">
            <div class="grid" id="grid" style="grid-template-columns: repeat(<?php echo e($wordsearch->columns); ?>, 2rem); --cols: <?php echo e($wordsearch->columns); ?>;">
                <?php $__currentLoopData = $grid; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row => $rowCells): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $__currentLoopData = $rowCells; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column => $letter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div
                            class="cell"
                            data-row="<?php echo e($row); ?>"
                            data-column="<?php echo e($column); ?>"
                            id="cell-<?php echo e($row); ?>-<?php echo e($column); ?>"
                        ><?php echo e($letter); ?></div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div class="hints">
                <strong style="color:#94a3b8;">Palabras a encontrar</strong>
                <?php $__currentLoopData = $words; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="hint <?php echo e(in_array($word->id, $foundIds) ? 'found' : ''); ?>" data-word-id="<?php echo e($word->id); ?>">
                        <span><?php echo e($word->word); ?></span>
                        <span><?php echo e($word->score); ?> pts</span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <div id="finish" class="finish">¡Completaste la sopa de letras!</div>
    </div>
</div>

<script>
    const cells = Array.from(document.querySelectorAll('.cell'));
    const foundKey = <?php echo json_encode($foundIds, 15, 512) ?>;
    const maxScore = <?php echo e($maxScore); ?>;
    let start = null;
    let points = 0;

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
        setTimeout(() => { el.className = 'result'; }, 2500);
    }

    function fetchAnswer(startCell, endCell) {
        const body = {
            start_row: parseInt(startCell.dataset.row, 10),
            start_column: parseInt(startCell.dataset.column, 10),
            end_row: parseInt(endCell.dataset.row, 10),
            end_column: parseInt(endCell.dataset.column, 10),
        };

        fetch(<?php echo json_encode(route('wordsearches.answer', $wordsearch), 512) ?>, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify(body),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.correct && !data.already_found) {
                    data.cells.forEach(([r, c]) => {
                        const cell = document.getElementById('cell-' + r + '-' + c);
                        cell.classList.add('found');
                        cell.dataset.found = '1';
                    });

                    document.querySelectorAll('.hint').forEach((hint) => {
                        const label = hint.querySelector('span');
                        if (label && label.textContent === data.word) {
                            hint.classList.add('found');
                        }
                    });

                    points += data.score;
                    document.getElementById('points').textContent = points;
                    showResult('Encontraste: ' + data.word + ' (+' + data.score + ' pts)', true);
                } else if (data.already_found) {
                    markWrong(data.cells);
                    showResult('Esa palabra ya la encontraste.', false);
                } else {
                    markWrong(cellsBetween(startCell, endCell));
                    showResult(data.error || 'No corresponde a ninguna palabra.', false);
                }

                start = null;
                cells.forEach((cell) => cell.classList.remove('selected'));
                updateFound();
            })
            .catch(() => {
                start = null;
                cells.forEach((cell) => cell.classList.remove('selected'));
                showResult('Error de conexión, intenta de nuevo.', false);
            });
    }

    function cellsBetween(a, b) {
        const ar = parseInt(a.dataset.row, 10);
        const ac = parseInt(a.dataset.column, 10);
        const br = parseInt(b.dataset.row, 10);
        const bc = parseInt(b.dataset.column, 10);

        const out = [];
        if (ar === br) {
            for (let c = Math.min(ac, bc); c <= Math.max(ac, bc); c++) {
                out.push(document.getElementById('cell-' + ar + '-' + c));
            }
        } else if (ac === bc) {
            for (let r = Math.min(ar, br); r <= Math.max(ar, br); r++) {
                out.push(document.getElementById('cell-' + r + '-' + ac));
            }
        }
        return out;
    }

    function markWrong(list) {
        list.forEach((cell) => {
            cell.classList.add('wrong');
            setTimeout(() => cell.classList.remove('wrong'), 600);
        });
    }

    function updateFound() {
        const found = Array.from(document.querySelectorAll('.cell')).filter((c) => c.dataset.found === '1').length;
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
</script>
</body>
</html><?php /**PATH /var/www/html/resources/views/student/activities/wordsearch/play.blade.php ENDPATH**/ ?>