<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sopa de Letras #{{ $wordsearch->id }}</title>
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
        @if (session('status'))
            <div class="message">{{ session('status') }}</div>
        @endif

        <h1>Sopa de Letras #{{ $wordsearch->id }}</h1>
        <p>
            Actividad: <strong>{{ $wordsearch->activity->title }}</strong> ·
            Grid {{ $wordsearch->rows }} x {{ $wordsearch->columns }}
        </p>

        <div class="grid" style="grid-template-columns: repeat({{ $wordsearch->columns }}, 2rem);">
            @foreach ($grid as $row => $rowCells)
                @foreach ($rowCells as $column => $letter)
                    <div class="cell">{{ $letter }}</div>
                @endforeach
            @endforeach
        </div>

        <h2 style="margin-bottom:.25rem;">Palabras colocadas ({{ $words->count() }})</h2>
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
            @foreach ($words as $word)
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
            <a href="{{ route('wordsearches.play', $wordsearch) }}">▶ Probar sopa de letras</a>
            &middot;
            <a href="{{ route('teacher.wordsearches.create') }}">Crear otra</a>
            &middot;
            <a href="{{ url('/') }}">Inicio</a>
        </p>
    </div>
</body>
</html>