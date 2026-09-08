<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Crear Unir Conceptos</title>
    <style>
        body { font-family: system-ui, sans-serif; background: #f1f5f9; margin: 0; padding: 2rem; color: #0f172a; }
        .card { background: #fff; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,.1); max-width: 720px; margin: 0 auto; padding: 2rem; }
        h1 { margin-top: 0; }
        label { font-weight: 600; display: block; margin: 1rem 0 .35rem; }
        select { padding: .5rem; border: 1px solid #cbd5e1; border-radius: .4rem; min-width: 260px; }
        table { width: 100%; border-collapse: collapse; margin-top: .5rem; }
        th, td { text-align: left; padding: .45rem .6rem; border-bottom: 1px solid #e2e8f0; }
        th { background: #f8fafc; }
        input[type=text] { width: 100%; padding: .45rem; border: 1px solid #cbd5e1; border-radius: .4rem; }
        input[type=number] { width: 90px; padding: .5rem; border: 1px solid #cbd5e1; border-radius: .4rem; }
        button { cursor: pointer; border: none; border-radius: .45rem; padding: .6rem 1rem; font-size: .95rem; }
        .secondary { background: #e2e8f0; color: #0f172a; }
        .danger { background: #fee2e2; color: #b91c1c; padding: .35rem .7rem; }
        .primary { background: #2563eb; color: #fff; margin-top: 1.25rem; width: 100%; font-weight: 600; }
        .message { padding: .8rem; border-radius: .45rem; margin-bottom: 1rem; background: #dcfce7; color: #15803d; }
        .error { background: #fee2e2; color: #b91c1c; padding: .8rem; border-radius: .45rem; margin-bottom: 1rem; }
        .pair-row:first-child .remove { visibility: hidden; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Crear Unir Conceptos</h1>

        <?php if(session('status')): ?>
            <div class="message"><?php echo e(session('status')); ?></div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="error">
                <ul style="margin:0; padding-left:1.1rem;">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('teacher.matchings.store')); ?>">
            <?php echo csrf_field(); ?>

            <label for="activity_id">Actividad (matching)</label>
            <select name="activity_id" id="activity_id" required>
                <option value="">Selecciona una actividad...</option>
                <?php $__currentLoopData = $activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($activity->id); ?>">
                        #<?php echo e($activity->id); ?> — <?php echo e($activity->title); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>

            <label>Parejas (concepto → definición)</label>
            <table id="pairs-table">
                <thead>
                <tr>
                    <th>Concepto</th>
                    <th>Definición</th>
                    <th style="width:110px">Puntos</th>
                    <th style="width:80px"></th>
                </tr>
                </thead>
                <tbody id="pairs-body">
                <tr class="pair-row">
                    <td><input type="text" name="items[0][left]" placeholder="HTTP" required></td>
                    <td><input type="text" name="items[0][right]" placeholder="Protocolo de transferencia" required></td>
                    <td><input type="number" name="items[0][score]" value="10" min="1" required></td>
                    <td><button type="button" class="danger remove" title="Eliminar">Eliminar</button></td>
                </tr>
                </tbody>
            </table>

            <button type="button" class="secondary" id="add-pair" style="margin-top:.75rem;">+ Agregar pareja</button>

            <button type="submit" class="primary">Generar actividad</button>
        </form>

        <p style="margin-top:1rem;">
            <a href="<?php echo e(url('/')); ?>">← Volver al inicio</a>
        </p>
    </div>

    <script>
        const body = document.getElementById('pairs-body');
        let index = 1;

        function reindex() {
            body.querySelectorAll('tr.pair-row').forEach((row, i) => {
                row.querySelectorAll('input').forEach((input) => {
                    input.name = input.name.replace(/\[(\w+)\]\[[\w]+\]/, `[$1][${i}]`);
                });
            });
        }

        document.getElementById('add-pair').addEventListener('click', () => {
            const tr = document.createElement('tr');
            tr.className = 'pair-row';
            tr.innerHTML = `
                <td><input type="text" name="items[${index}][left]" placeholder="Concepto" required></td>
                <td><input type="text" name="items[${index}][right]" placeholder="Definición" required></td>
                <td><input type="number" name="items[${index}][score]" value="10" min="1" required></td>
                <td><button type="button" class="danger remove" title="Eliminar">Eliminar</button></td>`;
            body.appendChild(tr);
            index++;
        });

        body.addEventListener('click', (event) => {
            if (!event.target.classList.contains('remove')) {
                return;
            }
            if (body.querySelectorAll('tr.pair-row').length > 1) {
                event.target.closest('tr').remove();
                reindex();
            }
        });
    </script>
</body>
</html><?php /**PATH /var/www/html/resources/views/teacher/activities/matching/create.blade.php ENDPATH**/ ?>