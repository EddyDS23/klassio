<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sopa de Letras #<?php echo e($wordsearch->id); ?></title>
    <style>
        body { font-family: system-ui, sans-serif; background: #f1f5f9; margin: 0; padding: 2rem; color: #0f172a; }
        .card { background: #fff; border-radius: .75rem; box-shadow: 0 1px 3px rgba(0,0,0,.1); max-width: 860px; margin: 0 auto; padding: 2rem; }
        h1 { margin-top: 0; }
        .grid { display: inline-grid; gap: 2px; background: #e2e8f0; padding: 2px; border-radius: .4rem; }
        .cell { width: 2rem; height: 2rem; display: flex; align-items: center; justify-content: center; background: #fff; font-weight: 700; }
        table { width: 100%; border-collapse: collapse; margin-top: 1.5rem; }
        th, td { text-align: left; padding: .45rem .6rem; border-bottom: 1px solid #e2e8f0; }
        th { background: #f8fafc; }
        .message { padding: .8rem; border-radius: .45rem; margin-bottom: 1rem; background: #dcfce7; color: #15803d; }
    </style>
</head>
<body>
    <div class="card">
        <?php if(session('status')): ?>
            <div class="message"><?php echo e(session('status')); ?></div>
        <?php endif; ?>

        <h1>Sopa de Letras #<?php echo e($wordsearch->id); ?></h1>
        <p>
            Actividad: <strong><?php echo e($wordsearch->activity->title); ?></strong> ·
            Grid <?php echo e($wordsearch->rows); ?> x <?php echo e($wordsearch->columns); ?>

        </p>

        <div class="grid" style="grid-template-columns: repeat(<?php echo e($wordsearch->columns); ?>, 2rem);">
            <?php $__currentLoopData = $grid; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row => $rowCells): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $__currentLoopData = $rowCells; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column => $letter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="cell"><?php echo e($letter); ?></div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <h2 style="margin-bottom:.25rem;">Palabras colocadas (<?php echo e($words->count()); ?>)</h2>
        <table>
            <thead>
            <tr>
                <th>Palabra</th>
                <th>Fila</th>
                <th>Columna</th>
                <th>Dirección</th>
                <th>Puntos</th>
            </tr>
            </thead>
            <tbody>
            <?php $__currentLoopData = $words; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><strong><?php echo e($word->word); ?></strong></td>
                    <td><?php echo e($word->row); ?></td>
                    <td><?php echo e($word->column); ?></td>
                    <td><?php echo e($word->direction); ?></td>
                    <td><?php echo e($word->score); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>

        <p style="margin-top:1.5rem;">
            <a href="<?php echo e(route('wordsearches.play', $wordsearch)); ?>">▶ Probar sopa de letras</a>
            &middot;
            <a href="<?php echo e(route('teacher.wordsearches.create')); ?>">Crear otra</a>
            &middot;
            <a href="<?php echo e(url('/')); ?>">Inicio</a>
        </p>
    </div>
</body>
</html><?php /**PATH /var/www/html/resources/views/teacher/activities/wordsearch/show.blade.php ENDPATH**/ ?>