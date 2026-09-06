<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $editing ? 'Editar' : 'Configurar' }} Sopa de Letras</title>
    <style>
        body { font-family: system-ui, sans-serif; background: #f1f5f9; margin: 0; padding: 2rem; color: #0f172a; }
        .card { background: #fff; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,.1); max-width: 760px; margin: 0 auto; padding: 2rem; }
        h1 { margin-top: 0; }
        label { font-weight: 600; display: block; margin: 1rem 0 .35rem; }
        input[type=number] { width: 90px; padding: .5rem; border: 1px solid #cbd5e1; border-radius: .4rem; }
        table { width: 100%; border-collapse: collapse; margin-top: .5rem; }
        th, td { text-align: left; padding: .45rem .6rem; border-bottom: 1px solid #e2e8f0; }
        th { background: #f8fafc; }
        input[type=text] { width: 100%; padding: .45rem; border: 1px solid #cbd5e1; border-radius: .4rem; }
        button { cursor: pointer; border: none; border-radius: .45rem; padding: .6rem 1rem; font-size: .95rem; }
        .secondary { background: #e2e8f0; color: #0f172a; }
        .danger { background: #fee2e2; color: #b91c1c; padding: .35rem .7rem; }
        .primary { background: #2563eb; color: #fff; margin-top: 1.25rem; width: 100%; font-weight: 600; }
        .message { padding: .8rem; border-radius: .45rem; margin-bottom: 1rem; background: #dcfce7; color: #15803d; }
        .error { background: #fee2e2; color: #b91c1c; padding: .8rem; border-radius: .45rem; margin-bottom: 1rem; }
        .grid { display: inline-grid; gap: 2px; background: #e2e8f0; padding: 2px; border-radius: .4rem; }
        .cell { width: 1.8rem; height: 1.8rem; display: flex; align-items: center; justify-content: center; background: #fff; font-weight: 700; }
        .summary { margin-top: 2rem; }
        .row-form:first-child .remove { visibility: hidden; }
    </style>
</head>
<body>
    <div class="card">
        <h1>{{ $editing ? 'Editar Sopa de Letras' : 'Configurar Sopa de Letras' }}</h1>
        <p>
            Actividad: <strong>{{ $activity->title }}</strong> ·
            <a href="{{ route('teacher.word-search.configure', $activity->id) }}">Reiniciar configuración</a>
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
                ? route('teacher.word-search.update', $activity->id)
                : route('teacher.word-search.store', $activity->id) }}"
        >
            @csrf
            @if ($editing)
                @method('PUT')
            @endif

            <div style="display:flex; gap:2rem;">
                <div>
                    <label for="rows">Filas</label>
                    <input type="number" name="rows" id="rows"
                           value="{{ old('rows', $wordsearch->rows ?? 10) }}" min="2" max="30" required>
                </div>
                <div>
                    <label for="columns">Columnas</label>
                    <input type="number" name="columns" id="columns"
                           value="{{ old('columns', $wordsearch->columns ?? 10) }}" min="2" max="30" required>
                </div>
            </div>

            <label>Palabras</label>
            <table id="words-table">
                <thead>
                <tr>
                    <th>Palabra</th>
                    <th style="width:130px">Puntos</th>
                    <th style="width:80px"></th>
                </tr>
                </thead>
                <tbody id="words-body">
                @forelse ($wordsearch->words ?? [] as $i => $word)
                    <tr class="row-form">
                        <td><input type="text" name="words[{{ $i }}][word]" value="{{ $word->word }}" required></td>
                        <td><input type="number" name="words[{{ $i }}][score]" value="{{ $word->score }}" min="1" required></td>
                        <td><button type="button" class="danger remove" title="Eliminar">Eliminar</button></td>
                    </tr>
                @empty
                    <tr class="row-form">
                        <td><input type="text" name="words[0][word]" placeholder="HTTP" required></td>
                        <td><input type="number" name="words[0][score]" value="10" min="1" required></td>
                        <td><button type="button" class="danger remove" title="Eliminar">Eliminar</button></td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            <button type="button" class="secondary" id="add-word" style="margin-top:.75rem;">+ Agregar palabra</button>

            <button type="submit" class="primary">{{ $editing ? 'Actualizar sopa' : 'Guardar sopa' }}</button>
        </form>

        @if ($wordsearch)
            <div class="summary">
                <h2 style="margin-bottom:.5rem;">Vista previa ({{ $wordsearch->rows }} x {{ $wordsearch->columns }})</h2>
                <div class="grid" style="grid-template-columns: repeat({{ $wordsearch->columns }}, 1.8rem);">
                    @foreach ($wordsearch->grid as $rowCells)
                        @foreach ($rowCells as $letter)
                            <div class="cell">{{ $letter }}</div>
                        @endforeach
                    @endforeach
                </div>

                <h3 style="margin-bottom:.25rem;">Palabras colocadas ({{ $wordsearch->words()->count() }})</h3>
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
                    @foreach ($wordsearch->words()->orderBy('word')->get() as $word)
                        <tr>
                            <td><strong>{{ $word->word }}</strong></td>
                            <td>{{ $word->row }}</td>
                            <td>{{ $word->column }}</td>
                            <td>{{ $word->direction }}</td>
                            <td>{{ $word->score }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <p style="margin-top:1.5rem;">
                    <a href="{{ route('student.word-search.play', $activity->id) }}">▶ Probar sopa de letras</a>
                </p>
            </div>
        @endif

        <p style="margin-top:1rem;">
            <a href="{{ url('/') }}">← Volver al inicio</a>
        </p>
    </div>

    <script>
        const body = document.getElementById('words-body');
        let index = body.querySelectorAll('tr.row-form').length;

        function reindex() {
            body.querySelectorAll('tr.row-form').forEach((row, i) => {
                row.querySelectorAll('input').forEach((input) => {
                    input.name = input.name.replace(/\[(\w+)\]\[[\w]+\]/, `[$1][${i}]`);
                });
            });
        }

        document.getElementById('add-word').addEventListener('click', () => {
            const tr = document.createElement('tr');
            tr.className = 'row-form';
            tr.innerHTML = `
                <td><input type="text" name="words[${index}][word]" placeholder="Laravel" required></td>
                <td><input type="number" name="words[${index}][score]" value="10" min="1" required></td>
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