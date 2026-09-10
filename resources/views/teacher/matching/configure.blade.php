<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $editing ? 'Editar' : 'Configurar' }} Unir Conceptos</title>
    <style>
        body { font-family: system-ui, sans-serif; background: #f1f5f9; margin: 0; padding: 2rem; color: #0f172a; }
        .card { background: #fff; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,.1); max-width: 760px; margin: 0 auto; padding: 2rem; }
        h1 { margin-top: 0; }
        label { font-weight: 600; display: block; margin: 1rem 0 .35rem; }
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
        .summary { margin-top: 2rem; }
        .row-form:first-child .remove { visibility: hidden; }
    </style>
</head>
<body>
    <div class="card">
        <h1>{{ $editing ? 'Editar Unir Conceptos' : 'Configurar Unir Conceptos' }}</h1>
        <p>
            Actividad: <strong>{{ $activity->title }}</strong> ·
            <a href="{{ route('teacher.matching.configure', $activity->id) }}">Reiniciar configuración</a>
        </p>

        @if (session('status'))
            <div class="message">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="error">
                <ul style="margin:0; padding-left:1.1rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            method="POST"
            action="{{ $editing
                ? route('teacher.matching.update', $activity->id)
                : route('teacher.matching.store', $activity->id) }}"
        >
            @csrf
            @if ($editing)
                @method('PUT')
            @endif

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
                @forelse (($editing && $matching ? $matching->items : []) as $i => $item)
                    <tr class="row-form">
                        <td><input type="text" name="items[{{ $i }}][left]" value="{{ $item->left_text }}" required></td>
                        <td><input type="text" name="items[{{ $i }}][right]" value="{{ $item->right_text }}" required></td>
                        <td><input type="number" name="items[{{ $i }}][score]" value="{{ $item->score }}" min="1" required></td>
                        <td><button type="button" class="danger remove" title="Eliminar">Eliminar</button></td>
                    </tr>
                @empty
                    <tr class="row-form">
                        <td><input type="text" name="items[0][left]" placeholder="HTTP" required></td>
                        <td><input type="text" name="items[0][right]" placeholder="Protocolo de transferencia" required></td>
                        <td><input type="number" name="items[0][score]" value="10" min="1" required></td>
                        <td><button type="button" class="danger remove" title="Eliminar">Eliminar</button></td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            <button type="button" class="secondary" id="add-pair" style="margin-top:.75rem;">+ Agregar pareja</button>

            <button type="submit" class="primary">{{ $editing ? 'Actualizar actividad' : 'Guardar actividad' }}</button>
        </form>

        @if ($editing && $matching)
            <div class="summary">
                <h2 style="margin-bottom:.5rem;">Parejas configuradas ({{ $matching->items()->count() }})</h2>
                <table>
                    <thead>
                    <tr>
                        <th>Concepto</th>
                        <th>Definición</th>
                        <th style="width:120px">Puntos</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($matching->items()->orderBy('left_text')->get() as $item)
                        <tr>
                            <td><strong>{{ $item->left_text }}</strong></td>
                            <td>{{ $item->right_text }}</td>
                            <td>{{ $item->score }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

            </div>
        @endif

        <p style="margin-top:1rem;">
            <a href="{{ route('teacher.activities.show', $activity->id) }}">← Volver a la actividad</a>
        </p>
    </div>

    <script>
        const body = document.getElementById('pairs-body');
        let index = body.querySelectorAll('tr.row-form').length;

        function reindex() {
            body.querySelectorAll('tr.row-form').forEach((row, i) => {
                row.querySelectorAll('input').forEach((input) => {
                    input.name = input.name.replace(/\[(\w+)\]\[[\w]+\]/, `[$1][${i}]`);
                });
            });
        }

        document.getElementById('add-pair').addEventListener('click', () => {
            const tr = document.createElement('tr');
            tr.className = 'row-form';
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
            if (body.querySelectorAll('tr.row-form').length > 1) {
                event.target.closest('tr').remove();
                reindex();
            }
        });
    </script>
</body>
</html>