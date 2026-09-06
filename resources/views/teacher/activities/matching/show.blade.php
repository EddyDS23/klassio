<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Unir Conceptos #{{ $matching->id }}</title>
    <style>
        body { font-family: system-ui, sans-serif; background: #f1f5f9; margin: 0; padding: 2rem; color: #0f172a; }
        .card { background: #fff; border-radius: .75rem; box-shadow: 0 1px 3px rgba(0,0,0,.1); max-width: 760px; margin: 0 auto; padding: 2rem; }
        h1 { margin-top: 0; }
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

        <h1>Unir Conceptos #{{ $matching->id }}</h1>
        <p>
            Actividad: <strong>{{ $matching->activity->title }}</strong> ·
            {{ $items->count() }} parejas
        </p>

        <h2 style="margin-bottom:.25rem;">Parejas configuradas</h2>
        <table>
            <thead>
            <tr>
                <th>Concepto</th>
                <th>Definición</th>
                <th style="width:120px">Puntos</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($items as $item)
                <tr>
                    <td><strong>{{ $item->left_text }}</strong></td>
                    <td>{{ $item->right_text }}</td>
                    <td>{{ $item->score }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <p style="margin-top:1.5rem;">
            <a href="{{ route('matchings.play', $matching) }}">▶ Probar unir conceptos</a>
            &middot;
            <a href="{{ route('teacher.matchings.create') }}">Crear otra</a>
            &middot;
            <a href="{{ url('/') }}">Inicio</a>
        </p>
    </div>
</body>
</html>