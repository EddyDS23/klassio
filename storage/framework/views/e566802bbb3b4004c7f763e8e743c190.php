<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Unir Conceptos — Jugar</title>
    <style>
        body { font-family: system-ui, sans-serif; background: #0f172a; margin: 0; padding: 2rem; color: #e2e8f0; }
        .wrap { max-width: 860px; margin: 0 auto; }
        .card { background: #1e293b; border-radius: .75rem; padding: 2rem; }
        h1 { margin: 0 0 .25rem; }
        .scoreline { color: #94a3b8; margin-bottom: 1.5rem; }
        .layout { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; align-items: start; }
        .col { display: flex; flex-direction: column; gap: .6rem; }
        .col-title { color: #94a3b8; font-weight: 700; margin-bottom: .25rem; text-align: center; }
        .card-item {
            padding: .9rem 1rem; border-radius: .45rem; background: #0f172a; cursor: pointer;
            border: 2px solid transparent; font-weight: 600; text-align: center; transition: border-color .15s, background .15s, transform .1s;
        }
        .card-item:hover { border-color: #38bdf8; }
        .card-item.selected { border-color: #38bdf8; background: #0c4a6e; }
        .card-item.matched { background: #14532d; border-color: #22c55e; color: #bbf7d0; cursor: default; }
        .card-item.wrong { background: #7f1d1d; border-color: #ef4444; color: #fecaca; }
        .result { padding: .8rem; border-radius: .45rem; margin-bottom: 1rem; display: none; font-weight: 600; text-align: center; }
        .result.ok { display: block; background: #052e16; color: #22c55e; }
        .result.bad { display: block; background: #450a0a; color: #f87171; }
        .finish { display: none; padding: .8rem; border-radius: .45rem; background: #14532d; color: #bbf7d0; font-weight: 700; text-align: center; margin-top: 1rem; }
        @media (max-width: 640px) {
            .layout { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <h1>Unir Conceptos</h1>
        <p class="scoreline">
            Actividad <?php echo e($matching->activity->title); ?> ·
            Puntos: <strong id="points"><?php echo e($earnedPoints); ?></strong>
            / <?php echo e($maxScore); ?> · Parejas: <strong id="matched-count"><?php echo e(count($correctIds)); ?></strong>/<?php echo e($items->count()); ?>

        </p>

        <div id="result" class="result"></div>

        <div class="layout">
            <div class="col">
                <div class="col-title">Conceptos</div>
                <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="card-item left" data-item-id="<?php echo e($item->id); ?>" <?php echo e(in_array($item->id, $correctIds) ? 'data-matched' : ''); ?>>
                        <?php echo e($item->left_text); ?>

                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div class="col">
                <div class="col-title">Definiciones</div>
                <?php $__currentLoopData = $rightOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="card-item right" data-text="<?php echo e($option->right_text); ?>">
                        <?php echo e($option->right_text); ?>

                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <div id="finish" class="finish">¡Completaste la actividad!</div>
    </div>
</div>

<script>
    const items = <?php echo json_encode($items->map(fn ($item) => ['id' => $item->id, 'left' => $item->left_text, 'right' => $item->right_text])) ?>;
    const correctIds = <?php echo json_encode($correctIds, 15, 512) ?>;
    const leftCards = Array.from(document.querySelectorAll('.card-item.left'));
    const rightCards = Array.from(document.querySelectorAll('.card-item.right'));
    let selectedLeft = null;
    let points = <?php echo e($earnedPoints); ?>;

    leftCards.forEach((card) => {
        if (card.dataset.matched !== undefined) {
            card.classList.add('matched');
        }
        card.addEventListener('click', () => {
            if (card.classList.contains('matched')) {
                return;
            }
            leftCards.forEach((c) => c.classList.remove('selected'));
            card.classList.add('selected');
            selectedLeft = parseInt(card.dataset.itemId, 10);
        });
    });

    rightCards.forEach((card) => {
        card.addEventListener('click', () => {
            if (!selectedLeft) {
                showResult('Selecciona primero un concepto de la izquierda.', false);
                return;
            }
            if (card.classList.contains('matched')) {
                return;
            }
            const leftCard = leftCards.find((c) => parseInt(c.dataset.itemId, 10) === selectedLeft);
            fetchAnswer(leftCard, card);
        });
    });

    function showResult(message, ok) {
        const el = document.getElementById('result');
        el.textContent = message;
        el.className = 'result ' + (ok ? 'ok' : 'bad');
        setTimeout(() => { el.className = 'result'; }, 2500);
    }

    function fetchAnswer(leftCard, rightCard) {
        fetch(<?php echo json_encode(route('matchings.answer', $matching), 512) ?>, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                matching_item_id: parseInt(leftCard.dataset.itemId, 10),
                response: rightCard.dataset.text,
            }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.correct && !data.already_answered) {
                    leftCard.classList.remove('selected');
                    leftCard.classList.add('matched');
                    rightCard.classList.add('matched');
                    points += data.score;
                    document.getElementById('points').textContent = points;
                    showResult('¡Correcto! (+' + data.score + ' pts)', true);
                    updateMatched();
                } else if (data.already_answered) {
                    leftCard.classList.remove('selected');
                    showResult('Esa pareja ya estaba resuelta.', false);
                } else {
                    markWrong([leftCard, rightCard]);
                    leftCard.classList.remove('selected');
                    showResult(data.error || 'Esa pareja no es correcta.', false);
                }
            })
            .catch(() => {
                leftCard.classList.remove('selected');
                showResult('Error de conexión, intenta de nuevo.', false);
            });
    }

    function markWrong(list) {
        list.forEach((card) => {
            card.classList.add('wrong');
            setTimeout(() => card.classList.remove('wrong'), 600);
        });
    }

    function updateMatched() {
        document.getElementById('matched-count').textContent = document.querySelectorAll('.card-item.matched').length;
        if (document.querySelectorAll('.card-item.matched').length === leftCards.length) {
            document.getElementById('finish').style.display = 'block';
        }
    }

    updateMatched();
</script>
</body>
</html><?php /**PATH /var/www/html/resources/views/student/activities/matching/play.blade.php ENDPATH**/ ?>