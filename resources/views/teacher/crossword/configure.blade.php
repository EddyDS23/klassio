<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        {{ isset($crossword) ? 'Editar' : 'Configurar' }} crucigrama
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-800">

    <!-- Encabezado -->
    <header class="sticky top-0 z-30 border-b border-slate-200 bg-white shadow-sm">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-5 py-4 lg:px-8">

            <a
                href="{{ route('teacher.activities.show', $activity->id) }}"
                class="flex items-center gap-3"
            >
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 text-xl font-black text-white shadow-lg shadow-emerald-200">
                    K
                </div>

                <div>
                    <p class="text-xl font-black tracking-tight text-slate-900">
                        Klassio
                    </p>

                    <p class="text-xs font-medium text-slate-500">
                        Configuración de actividades
                    </p>
                </div>
            </a>

            <div class="flex flex-wrap items-center gap-2">

                <a
                    href="{{ route('teacher.activities.show', $activity->id) }}"
                    class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-600 transition hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-700"
                >
                    ← Cancelar
                </a>

                <a
                    href="{{ route('teacher.activities.index', $activity->class_id) }}"
                    class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-bold text-white shadow-md shadow-emerald-200 transition hover:bg-emerald-700"
                >
                    Actividades
                </a>

            </div>
        </div>
    </header>

    <main class="mx-auto max-w-7xl space-y-8 px-5 py-8 lg:px-8">

        <!-- Hero -->
        <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-teal-600 to-cyan-700 p-7 text-white shadow-xl shadow-emerald-200 md:p-10">

            <div class="absolute -right-20 -top-24 h-72 w-72 rounded-full bg-white/10"></div>
            <div class="absolute -bottom-32 right-24 h-80 w-80 rounded-full bg-white/10"></div>

            <div class="relative z-10 max-w-3xl">

                <div class="mb-4 inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-2 text-sm font-bold text-emerald-50 backdrop-blur">
                    <span>🧩</span>
                    {{ isset($crossword) ? 'Editar actividad' : 'Nueva actividad' }}
                </div>

                <h1 class="text-3xl font-black tracking-tight md:text-4xl">
                    {{ isset($crossword) ? 'Editar crucigrama' : 'Configurar crucigrama' }}
                </h1>

                <p class="mt-3 text-base font-semibold text-emerald-50 md:text-lg">
                    {{ $activity->title }}
                </p>

                <p class="mt-3 max-w-2xl text-sm leading-6 text-emerald-50">
                    Agrega las palabras y pistas que formarán parte del crucigrama.
                    El sistema intentará cruzarlas automáticamente usando letras en común.
                </p>

            </div>
        </section>

        <!-- Mensaje de éxito -->
        @if(session('success'))
            <div class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800 shadow-sm">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-600 font-bold text-white">
                    ✓
                </div>

                <div>
                    <p class="font-bold">
                        Operación completada
                    </p>

                    <p class="mt-1 text-sm">
                        {{ session('success') }}
                    </p>
                </div>
            </div>
        @endif

        <!-- Errores -->
        @if($errors->any())
            <div class="rounded-2xl border border-red-200 bg-red-50 p-5 text-red-800 shadow-sm">

                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-red-600 font-black text-white">
                        !
                    </div>

                    <h2 class="text-lg font-black">
                        Revisa los siguientes errores
                    </h2>
                </div>

                <ul class="mt-4 list-disc space-y-1 pl-6 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>

            </div>
        @endif

        @php
            $isEditing = isset($crossword);
        @endphp

        <!-- Formulario -->
        <form
            action="{{
                $isEditing
                    ? route('teacher.crossword.update', $activity->id)
                    : route('teacher.crossword.store', $activity->id)
            }}"
            method="POST"
            class="space-y-6"
        >

            @csrf

            @if($isEditing)
                @method('PUT')
            @endif

            <!-- Encabezado de sección -->
            <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm md:p-8">

                <div class="mb-6 flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-5">

                    <div>
                        <p class="text-xs font-black uppercase tracking-widest text-emerald-600">
                            Banco de palabras
                        </p>

                        <h2 class="mt-1 text-2xl font-black text-slate-900">
                            Palabras del crucigrama
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Escribe cada palabra, agrega una pista y asigna su puntuación.
                        </p>
                    </div>

                    <div class="rounded-2xl bg-emerald-50 px-4 py-3 text-center">
                        <p class="text-2xl font-black text-emerald-700" id="word-count">
                            {{ $isEditing ? $words->count() : 2 }}
                        </p>

                        <p class="text-xs font-bold text-emerald-600">
                            Palabras
                        </p>
                    </div>

                </div>

                <!-- Tabla -->
                <div class="overflow-x-auto rounded-2xl border border-slate-200">

                    <table class="min-w-full divide-y divide-slate-200">

                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-600">
                                    Palabra
                                </th>

                                <th class="px-4 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-600">
                                    Pista
                                </th>

                                <th class="px-4 py-4 text-left text-xs font-black uppercase tracking-wider text-slate-600">
                                    Puntuación
                                </th>

                                <th class="px-4 py-4 text-center text-xs font-black uppercase tracking-wider text-slate-600">
                                    Acción
                                </th>
                            </tr>
                        </thead>

                        <tbody
                            id="words-container"
                            class="divide-y divide-slate-200 bg-white"
                        >

                            @if($isEditing && $words->count() > 0)

                                @foreach($words as $index => $word)

                                    <tr class="word-row transition hover:bg-emerald-50/40">

                                        <td class="min-w-48 px-4 py-4 align-top">
                                            <label class="mb-2 block text-xs font-bold text-slate-500">
                                                Palabra
                                            </label>

                                            <input
                                                type="text"
                                                name="words[{{ $index }}][word]"
                                                value="{{ old(
                                                    "words.$index.word",
                                                    $word->word
                                                ) }}"
                                                maxlength="20"
                                                required
                                                placeholder="Ej. MATEMÁTICAS"
                                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm font-bold uppercase text-slate-800 outline-none transition placeholder:normal-case placeholder:font-normal focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100"
                                            >
                                        </td>

                                        <td class="min-w-64 px-4 py-4 align-top">
                                            <label class="mb-2 block text-xs font-bold text-slate-500">
                                                Pista
                                            </label>

                                            <input
                                                type="text"
                                                name="words[{{ $index }}][clue]"
                                                value="{{ old(
                                                    "words.$index.clue",
                                                    $word->clue
                                                ) }}"
                                                maxlength="255"
                                                required
                                                placeholder="Escribe una pista para el alumno"
                                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100"
                                            >
                                        </td>

                                        <td class="min-w-36 px-4 py-4 align-top">
                                            <label class="mb-2 block text-xs font-bold text-slate-500">
                                                Puntos
                                            </label>

                                            <input
                                                type="number"
                                                name="words[{{ $index }}][score]"
                                                value="{{ old(
                                                    "words.$index.score",
                                                    $word->score
                                                ) }}"
                                                min="1"
                                                max="1000"
                                                required
                                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100"
                                            >
                                        </td>

                                        <td class="px-4 py-4 text-center align-middle">
                                            <button
                                                type="button"
                                                onclick="removeRow(this)"
                                                class="inline-flex items-center justify-center rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-xs font-black text-red-600 transition hover:bg-red-100"
                                            >
                                                🗑️ Eliminar
                                            </button>
                                        </td>

                                    </tr>

                                @endforeach

                            @else

                                @for($i = 0; $i < 2; $i++)

                                    <tr class="word-row transition hover:bg-emerald-50/40">

                                        <td class="min-w-48 px-4 py-4 align-top">
                                            <label class="mb-2 block text-xs font-bold text-slate-500">
                                                Palabra
                                            </label>

                                            <input
                                                type="text"
                                                name="words[{{ $i }}][word]"
                                                value="{{ old("words.$i.word") }}"
                                                maxlength="20"
                                                required
                                                placeholder="Ej. MATEMÁTICAS"
                                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm font-bold uppercase text-slate-800 outline-none transition placeholder:normal-case placeholder:font-normal focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100"
                                            >
                                        </td>

                                        <td class="min-w-64 px-4 py-4 align-top">
                                            <label class="mb-2 block text-xs font-bold text-slate-500">
                                                Pista
                                            </label>

                                            <input
                                                type="text"
                                                name="words[{{ $i }}][clue]"
                                                value="{{ old("words.$i.clue") }}"
                                                maxlength="255"
                                                required
                                                placeholder="Escribe una pista para el alumno"
                                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100"
                                            >
                                        </td>

                                        <td class="min-w-36 px-4 py-4 align-top">
                                            <label class="mb-2 block text-xs font-bold text-slate-500">
                                                Puntos
                                            </label>

                                            <input
                                                type="number"
                                                name="words[{{ $i }}][score]"
                                                value="{{ old("words.$i.score", 100) }}"
                                                min="1"
                                                max="1000"
                                                required
                                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100"
                                            >
                                        </td>

                                        <td class="px-4 py-4 text-center align-middle">
                                            <button
                                                type="button"
                                                onclick="removeRow(this)"
                                                class="inline-flex items-center justify-center rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-xs font-black text-red-600 transition hover:bg-red-100"
                                            >
                                                🗑️ Eliminar
                                            </button>
                                        </td>

                                    </tr>

                                @endfor

                            @endif

                        </tbody>

                    </table>

                </div>

                <!-- Acciones -->
                <div class="mt-6 flex flex-wrap items-center justify-between gap-4">

                    <button
                        type="button"
                        onclick="addRow()"
                        class="inline-flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm font-black text-emerald-700 transition hover:border-emerald-300 hover:bg-emerald-100"
                    >
                        <span class="text-lg">＋</span>
                        Agregar palabra
                    </button>

                    <p class="text-xs text-slate-500">
                        El crucigrama debe tener al menos 2 palabras.
                    </p>

                </div>

            </section>

            <!-- Botones finales -->
            <section class="flex flex-wrap items-center justify-between gap-4 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm md:p-8">

                <a
                    href="{{ route('teacher.activities.show', $activity->id) }}"
                    class="rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-600 transition hover:border-slate-300 hover:bg-slate-50"
                >
                    Cancelar
                </a>

                <button
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-6 py-3 text-sm font-black text-white shadow-lg shadow-emerald-200 transition hover:bg-emerald-700"
                >
                    <span>✓</span>
                    {{ $isEditing ? 'Actualizar crucigrama' : 'Guardar crucigrama' }}
                </button>

            </section>

        </form>

        <!-- Ayuda -->
        <section class="rounded-2xl border border-cyan-100 bg-cyan-50 p-5">

            <div class="flex items-start gap-3">

                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-cyan-600 text-xl text-white">
                    💡
                </div>

                <div>
                    <h3 class="font-black text-cyan-900">
                        Recomendación para crear tu crucigrama
                    </h3>

                    <p class="mt-1 text-sm leading-6 text-cyan-800">
                        Utiliza palabras relacionadas con el tema de la clase.
                        Escribe pistas claras y asigna una puntuación adecuada
                        para cada respuesta.
                    </p>
                </div>

            </div>

        </section>

    </main>

    <script>
        let rowIndex = {{ $isEditing ? $words->count() : 2 }};

        function updateWordCount() {
            const rows = document.querySelectorAll('.word-row');
            const counter = document.getElementById('word-count');

            if (counter) {
                counter.textContent = rows.length;
            }
        }

        function addRow() {
            const container = document.getElementById('words-container');

            const row = document.createElement('tr');

            row.classList.add(
                'word-row',
                'transition',
                'hover:bg-emerald-50/40'
            );

            row.innerHTML = `
                <td class="min-w-48 px-4 py-4 align-top">
                    <label class="mb-2 block text-xs font-bold text-slate-500">
                        Palabra
                    </label>

                    <input
                        type="text"
                        name="words[${rowIndex}][word]"
                        maxlength="20"
                        required
                        placeholder="Ej. MATEMÁTICAS"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm font-bold uppercase text-slate-800 outline-none transition placeholder:normal-case placeholder:font-normal focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100"
                    >
                </td>

                <td class="min-w-64 px-4 py-4 align-top">
                    <label class="mb-2 block text-xs font-bold text-slate-500">
                        Pista
                    </label>

                    <input
                        type="text"
                        name="words[${rowIndex}][clue]"
                        maxlength="255"
                        required
                        placeholder="Escribe una pista para el alumno"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100"
                    >
                </td>

                <td class="min-w-36 px-4 py-4 align-top">
                    <label class="mb-2 block text-xs font-bold text-slate-500">
                        Puntos
                    </label>

                    <input
                        type="number"
                        name="words[${rowIndex}][score]"
                        value="100"
                        min="1"
                        max="1000"
                        required
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100"
                    >
                </td>

                <td class="px-4 py-4 text-center align-middle">
                    <button
                        type="button"
                        onclick="removeRow(this)"
                        class="inline-flex items-center justify-center rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-xs font-black text-red-600 transition hover:bg-red-100"
                    >
                        🗑️ Eliminar
                    </button>
                </td>
            `;

            container.appendChild(row);

            rowIndex++;

            updateWordCount();
        }

        function removeRow(button) {
            const rows = document.querySelectorAll('.word-row');

            if (rows.length <= 2) {
                alert('El crucigrama debe tener al menos 2 palabras.');
                return;
            }

            const row = button.closest('tr');

            if (row) {
                row.remove();
            }

            updateWordCount();
        }

        updateWordCount();
    </script>

</body>

</html>