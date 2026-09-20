<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $editing ? 'Editar' : 'Configurar' }} Sopa de Letras | Klassio</title>

    @include('partials.assets')

    <style>
        body {
            background:
                radial-gradient(circle at top left, rgba(16, 185, 129, .12), transparent 32%),
                radial-gradient(circle at bottom right, rgba(6, 182, 212, .12), transparent 32%),
                #f8fafc;
        }

        .glass-card {
            background: rgba(255, 255, 255, .94);
            border: 1px solid rgba(226, 232, 240, .9);
            box-shadow: 0 18px 45px rgba(15, 23, 42, .08);
        }

        .form-input {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: .85rem;
            padding: .75rem .9rem;
            background: #fff;
            color: #0f172a;
            outline: none;
            transition: .2s ease;
        }

        .form-input:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, .13);
        }

        .number-input {
            width: 110px;
        }

        .table-wrapper {
            overflow-x: auto;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
        }

        .words-table {
            width: 100%;
            min-width: 560px;
            border-collapse: collapse;
        }

        .words-table th {
            background: #f0fdf4;
            color: #065f46;
            font-size: .8rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .04em;
            padding: 1rem;
            text-align: left;
        }

        .words-table td {
            padding: .85rem 1rem;
            border-top: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .words-table tr:hover td {
            background: #f8fafc;
        }

        .preview-grid {
            display: inline-grid;
            gap: 3px;
            background: #cbd5e1;
            padding: 4px;
            border-radius: 1rem;
            max-width: 100%;
            overflow: auto;
        }

        .preview-cell {
            width: 2rem;
            height: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            color: #0f766e;
            font-size: .9rem;
            font-weight: 800;
            border-radius: .35rem;
        }

        .preview-cell:hover {
            background: #d1fae5;
        }

        .remove-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .35rem;
            border-radius: .7rem;
            padding: .55rem .8rem;
            background: #fff1f2;
            color: #be123c;
            font-size: .8rem;
            font-weight: 800;
            transition: .2s ease;
        }

        .remove-button:hover {
            background: #ffe4e6;
        }

        .row-form:first-child .remove {
            visibility: hidden;
        }

        /* Títulos larguísimos (p. ej. el nombre de la actividad) sin romper el layout */
        section p.max-w-xs, aside p {
            overflow-wrap: anywhere;
        }

        /* ---------- Modo negro para los componentes propios de esta vista ---------- */
        html[data-theme="dark"] .glass-card {
            background: rgb(30 41 59 / .94);
            border-color: #334155;
            box-shadow: 0 18px 45px rgb(0 0 0 / .45);
        }
        html[data-theme="dark"] .form-input {
            background: #0b1426;
            border-color: #475569;
            color: #f1f5f9;
        }
        html[data-theme="dark"] .form-input::placeholder { color: #64748b; }
        html[data-theme="dark"] .form-input:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 4px rgb(16 185 129 / .25);
        }
        html[data-theme="dark"] .table-wrapper { border-color: #334155; }
        html[data-theme="dark"] .words-table th { background: #273449; color: #6ee7b7; }
        html[data-theme="dark"] .words-table td { border-color: #334155; color: #e2e8f0; }
        html[data-theme="dark"] .words-table tr:hover td { background: rgb(148 163 184 / .08); }
        html[data-theme="dark"] .preview-grid { background: #334155; }
        html[data-theme="dark"] .preview-cell { background: #0f172a; color: #5eead4; }
        html[data-theme="dark"] .preview-cell:hover { background: rgb(16 185 129 / .25); }
        html[data-theme="dark"] .remove-button { background: rgb(244 63 94 / .15); color: #fda4af; }
        html[data-theme="dark"] .remove-button:hover { background: rgb(244 63 94 / .28); }

        @media (max-width: 640px) {
            .preview-cell {
                width: 1.65rem;
                height: 1.65rem;
                font-size: .75rem;
            }
        }
    </style>
</head>

<body class="min-h-screen text-slate-800">

    <!-- Barra superior -->
    <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/90 backdrop-blur">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ route('teacher.dashboard') }}" class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 text-xl font-black text-white shadow-lg shadow-emerald-200">
                    K
                </div>

                <div>
                    <div class="text-xl font-black tracking-tight text-slate-900">
                        Klassio
                    </div>
                    <div class="text-xs font-semibold text-slate-500">
                        Panel del profesor
                    </div>
                </div>
            </a>

            <div class="hidden items-center gap-3 sm:flex">
                <span class="rounded-full bg-emerald-50 px-4 py-2 text-sm font-bold text-emerald-700">
                    Configuración de actividad
                </span>

                <form method="POST" action="{{ url('/logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-bold text-slate-600 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600"
                    >
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">

        <!-- Volver -->
        <div class="mb-6">
            <a
                href="{{ route('teacher.activities.show', $activity->id) }}"
                class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-black text-slate-600 shadow-sm transition hover:border-emerald-200 hover:bg-emerald-50 hover:text-emerald-700"
            >
                ← Volver a la actividad
            </a>
        </div>

        <!-- Encabezado -->
        <section class="mb-8 overflow-hidden rounded-3xl bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 p-6 text-white shadow-xl shadow-emerald-200 sm:p-8">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <div class="mb-3 inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-xs font-bold uppercase tracking-wider text-emerald-50">
                        <span>✦</span>
                        Sopa de letras
                    </div>

                    <h1 class="text-3xl font-black tracking-tight sm:text-4xl">
                        {{ $editing ? 'Editar sopa de letras' : 'Configurar sopa de letras' }}
                    </h1>

                    <p class="mt-2 max-w-2xl text-sm leading-6 text-emerald-50 sm:text-base">
                        Personaliza el tamaño de la cuadrícula, agrega palabras y asigna los puntos de tu actividad.
                    </p>
                </div>

                <div class="rounded-2xl border border-white/20 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs font-semibold uppercase tracking-wider text-emerald-100">
                        Actividad
                    </p>
                    <p class="mt-1 max-w-xs text-lg font-black">
                        {{ $activity->title }}
                    </p>
                </div>
            </div>
        </section>

        <!-- Mensajes -->
        @if (session('status'))
            <div class="mb-6 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">
                <span class="text-xl">✓</span>
                <p class="font-semibold">{{ session('status') }}</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 p-5 text-rose-800">
                <div class="mb-2 flex items-center gap-2 font-black">
                    <span>⚠</span>
                    Revisa los siguientes datos
                </div>

                <ul class="list-disc space-y-1 pl-6 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_300px]">

            <!-- Formulario principal -->
            <section class="glass-card rounded-3xl p-5 sm:p-8">

                <div class="mb-7 flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-black text-slate-900">
                            Datos de la sopa
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Define las dimensiones y las palabras que deberán encontrar los alumnos.
                        </p>
                    </div>

                    <div class="hidden rounded-2xl bg-emerald-50 p-3 text-2xl sm:block">
                        🔎
                    </div>
                </div>

                <form
                    method="POST"
                    action="{{ $editing
                        ? route('teacher.wordsearch.update', $activity->id)
                        : route('teacher.wordsearch.store', $activity->id) }}"
                >
                    @csrf

                    @if ($editing)
                        @method('PUT')
                    @endif

                    <!-- Dimensiones -->
                    <div class="mb-8 rounded-2xl border border-slate-200 bg-slate-50 p-4 sm:p-5">
                        <div class="mb-4">
                            <h3 class="font-black text-slate-900">
                                Tamaño de la cuadrícula
                            </h3>
                            <p class="mt-1 text-xs text-slate-500">
                                Selecciona un valor entre 2 y 30 para filas y columnas.
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-6">
                            <div>
                                <label for="rows" class="mb-2 block text-sm font-bold text-slate-700">
                                    Filas
                                </label>

                                <input
                                    type="number"
                                    name="rows"
                                    id="rows"
                                    class="form-input number-input"
                                    value="{{ old('rows', $wordsearch->rows ?? 10) }}"
                                    min="2"
                                    max="30"
                                    required
                                >
                            </div>

                            <div>
                                <label for="columns" class="mb-2 block text-sm font-bold text-slate-700">
                                    Columnas
                                </label>

                                <input
                                    type="number"
                                    name="columns"
                                    id="columns"
                                    class="form-input number-input"
                                    value="{{ old('columns', $wordsearch->columns ?? 10) }}"
                                    min="2"
                                    max="30"
                                    required
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Palabras -->
                    <div class="mb-4 flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                        <div>
                            <h3 class="text-lg font-black text-slate-900">
                                Palabras de la actividad
                            </h3>
                            <p class="mt-1 text-sm text-slate-500">
                                Escribe cada palabra y define cuántos puntos vale.
                            </p>
                        </div>

                        <span class="w-fit rounded-full bg-cyan-50 px-3 py-1 text-xs font-black text-cyan-700">
                            Mínimo 1 palabra
                        </span>
                    </div>

                    <div class="table-wrapper">
                        <table class="words-table" id="words-table">
                            <thead>
                                <tr>
                                    <th>Palabra</th>
                                    <th style="width: 145px;">Puntos</th>
                                    <th style="width: 110px;">Acción</th>
                                </tr>
                            </thead>

                            <tbody id="words-body">
                                @forelse ($wordsearch->words ?? [] as $i => $word)
                                    <tr class="row-form">
                                        <td>
                                            <input
                                                type="text"
                                                name="words[{{ $i }}][word]"
                                                value="{{ $word->word }}"
                                                class="form-input"
                                                placeholder="Ej. Laravel"
                                                required
                                            >
                                        </td>

                                        <td>
                                            <input
                                                type="number"
                                                name="words[{{ $i }}][score]"
                                                value="{{ $word->score }}"
                                                class="form-input"
                                                min="1"
                                                required
                                            >
                                        </td>

                                        <td>
                                            <button
                                                type="button"
                                                class="remove remove-button"
                                                title="Eliminar palabra"
                                            >
                                                <span>×</span>
                                                Eliminar
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="row-form">
                                        <td>
                                            <input
                                                type="text"
                                                name="words[0][word]"
                                                class="form-input"
                                                placeholder="Ej. HTTP"
                                                required
                                            >
                                        </td>

                                        <td>
                                            <input
                                                type="number"
                                                name="words[0][score]"
                                                value="10"
                                                class="form-input"
                                                min="1"
                                                required
                                            >
                                        </td>

                                        <td>
                                            <button
                                                type="button"
                                                class="remove remove-button"
                                                title="Eliminar palabra"
                                            >
                                                <span>×</span>
                                                Eliminar
                                            </button>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <button
                            type="button"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-black text-emerald-700 transition hover:bg-emerald-100"
                            id="add-word"
                        >
                            <span class="text-lg">+</span>
                            Agregar palabra
                        </button>

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-3 text-sm font-black text-white shadow-lg shadow-emerald-200 transition hover:from-emerald-700 hover:to-teal-700"
                        >
                            <span>✓</span>
                            {{ $editing ? 'Actualizar sopa' : 'Guardar sopa' }}
                        </button>
                    </div>
                </form>
            </section>

            <!-- Panel lateral -->
            <aside class="space-y-5">

                <div class="glass-card rounded-3xl p-5">
                    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-2xl">
                        💡
                    </div>

                    <h3 class="font-black text-slate-900">
                        Recomendaciones
                    </h3>

                    <ul class="mt-3 space-y-3 text-sm leading-6 text-slate-600">
                        <li class="flex gap-2">
                            <span class="font-black text-emerald-600">✓</span>
                            Usa palabras relacionadas con el tema de la clase.
                        </li>

                        <li class="flex gap-2">
                            <span class="font-black text-emerald-600">✓</span>
                            Evita palabras demasiado largas para cuadrículas pequeñas.
                        </li>

                        <li class="flex gap-2">
                            <span class="font-black text-emerald-600">✓</span>
                            Asigna más puntos a las palabras difíciles.
                        </li>
                    </ul>
                </div>

                <div class="rounded-3xl border border-cyan-200 bg-gradient-to-br from-cyan-50 to-teal-50 p-5">
                    <div class="mb-3 text-2xl">📘</div>

                    <h3 class="font-black text-cyan-900">
                        Actividad actual
                    </h3>

                    <p class="mt-2 text-sm leading-6 text-cyan-800">
                        {{ $activity->title }}
                    </p>

                    <a
                        href="{{ route('teacher.wordsearch.configure', $activity->id) }}"
                        class="mt-4 inline-flex text-sm font-black text-teal-700 underline decoration-teal-300 underline-offset-4 hover:text-teal-900"
                    >
                        Reiniciar configuración
                    </a>
                </div>
            </aside>
        </div>

        <!-- Vista previa -->
        @if ($wordsearch)
            <section class="glass-card mt-8 rounded-3xl p-5 sm:p-8">
                <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div class="mb-2 inline-flex rounded-full bg-indigo-50 px-3 py-1 text-xs font-black text-indigo-700">
                            Vista previa
                        </div>

                        <h2 class="text-2xl font-black text-slate-900">
                            Cuadrícula de la sopa
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Dimensiones: {{ $wordsearch->rows }} filas × {{ $wordsearch->columns }} columnas
                        </p>
                    </div>

                    <div class="rounded-2xl bg-slate-100 px-4 py-3 text-sm font-black text-slate-700">
                        {{ $wordsearch->words()->count() }} palabras
                    </div>
                </div>

                <div class="mb-8 overflow-x-auto rounded-2xl bg-slate-50 p-4">
                    <div
                        class="preview-grid"
                        style="grid-template-columns: repeat({{ $wordsearch->columns }}, 2rem);"
                    >
                        @foreach ($wordsearch->grid as $rowCells)
                            @foreach ($rowCells as $letter)
                                <div class="preview-cell">
                                    {{ $letter }}
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                </div>

                <div class="mb-4">
                    <h3 class="text-lg font-black text-slate-900">
                        Palabras colocadas
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Consulta la posición y dirección de cada palabra dentro de la cuadrícula.
                    </p>
                </div>

                <div class="table-wrapper">
                    <table class="words-table">
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
                                    <td>
                                        <span class="font-black text-slate-900">
                                            {{ $word->word }}
                                        </span>
                                    </td>

                                    <td>{{ $word->row }}</td>
                                    <td>{{ $word->column }}</td>

                                    <td>
                                        <span class="rounded-full bg-cyan-50 px-3 py-1 text-xs font-bold text-cyan-700">
                                            {{ app(\App\Services\WordsearchService::class)->directionLabel($word->direction) }}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="font-black text-emerald-700">
                                            {{ $word->score }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endif

        <!-- Navegación inferior -->
        <div class="mt-8 flex flex-col items-center gap-3">
            <span class="text-center text-xs font-semibold text-slate-400">
                Klassio · Herramientas educativas
            </span>
        </div>
    </main>

    <script>
        const body = document.getElementById('words-body');
        let index = body.querySelectorAll('tr.row-form').length;

        function reindex() {
            body.querySelectorAll('tr.row-form').forEach((row, i) => {
                row.querySelectorAll('input').forEach((input) => {
                    input.name = input.name.replace(
                        /words\[\d+\]\]\[?|\[\d+\]\[(word|score)\]/g,
                        ''
                    );
                });

                const wordInput = row.querySelector('input[type="text"]');
                const scoreInput = row.querySelector('input[type="number"]');

                if (wordInput) {
                    wordInput.name = `words[${i}][word]`;
                }

                if (scoreInput) {
                    scoreInput.name = `words[${i}][score]`;
                }
            });
        }

        document.getElementById('add-word').addEventListener('click', () => {
            const tr = document.createElement('tr');

            tr.className = 'row-form';

            tr.innerHTML = `
                <td>
                    <input
                        type="text"
                        name="words[${index}][word]"
                        class="form-input"
                        placeholder="Ej. Laravel"
                        required
                    >
                </td>

                <td>
                    <input
                        type="number"
                        name="words[${index}][score]"
                        class="form-input"
                        value="10"
                        min="1"
                        required
                    >
                </td>

                <td>
                    <button
                        type="button"
                        class="remove remove-button"
                        title="Eliminar palabra"
                    >
                        <span>×</span>
                        Eliminar
                    </button>
                </td>
            `;

            body.appendChild(tr);
            index++;
        });

        body.addEventListener('click', (event) => {
            const button = event.target.closest('.remove');

            if (!button) {
                return;
            }

            if (body.querySelectorAll('tr.row-form').length > 1) {
                button.closest('tr').remove();
                reindex();
            }
        });
    </script>
</body>
</html>
