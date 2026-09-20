<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        {{ isset($kahoot) ? 'Editar' : 'Configurar' }} Kahoot — {{ $activity->title }}
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
                    <span>🎮</span>
                    {{ isset($kahoot) ? 'Editar actividad' : 'Nueva actividad' }}
                </div>

                <h1 class="text-3xl font-black tracking-tight md:text-4xl">
                    {{ isset($kahoot) ? 'Editar Kahoot' : 'Configurar Kahoot' }}
                </h1>

                <p class="mt-3 text-base font-semibold text-emerald-50 md:text-lg">
                    {{ $activity->title }}
                </p>

                <p class="mt-3 max-w-2xl text-sm leading-6 text-emerald-50">
                    Agrega preguntas y opciones para crear una actividad interactiva
                    para tus alumnos.
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

        <!-- Indicaciones -->
        <section class="rounded-2xl border border-cyan-100 bg-cyan-50 p-5">

            <div class="flex items-start gap-3">

                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-cyan-600 text-xl text-white">
                    💡
                </div>

                <div>
                    <h2 class="font-black text-cyan-900">
                        ¿Cómo configurar tu Kahoot?
                    </h2>

                    <p class="mt-1 text-sm leading-6 text-cyan-800">
                        Agrega las preguntas y sus opciones. Marca con el círculo
                        la respuesta correcta.
                    </p>

                    <p class="mt-2 text-xs font-bold text-cyan-700">
                        Cada pregunta necesita entre 2 y 4 opciones y exactamente
                        una debe ser correcta.
                    </p>
                </div>

            </div>

        </section>

        @if(isset($kahoot))
            <form
                method="POST"
                action="{{ route('teacher.kahoot.update', $activity->id) }}"
                id="kahoot-form"
                class="space-y-6"
            >
                @method('PUT')
        @else
            <form
                method="POST"
                action="{{ route('teacher.kahoot.store', $activity->id) }}"
                id="kahoot-form"
                class="space-y-6"
            >
        @endif

            @csrf

            <!-- Contenedor de preguntas -->
            <section class="space-y-6">

                <div class="flex flex-wrap items-center justify-between gap-4">

                    <div>
                        <p class="text-xs font-black uppercase tracking-widest text-emerald-600">
                            Banco de preguntas
                        </p>

                        <h2 class="mt-1 text-2xl font-black text-slate-900">
                            Preguntas del Kahoot
                        </h2>
                    </div>

                    <div class="rounded-2xl bg-white px-4 py-3 text-center shadow-sm ring-1 ring-slate-200">
                        <p
                            id="question-count"
                            class="text-2xl font-black text-emerald-700"
                        >
                            {{ isset($questions) && $questions->count() > 0 ? $questions->count() : 1 }}
                        </p>

                        <p class="text-xs font-bold text-slate-500">
                            Preguntas
                        </p>
                    </div>

                </div>

                <div id="questions-container" class="space-y-6">

                    @if(isset($questions) && $questions->count() > 0)

                        @foreach($questions as $qi => $question)

                            <article
                                class="question-block rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-emerald-200 hover:shadow-md md:p-8"
                                id="question-block-{{ $qi }}"
                            >

                                <div class="question-header mb-6 flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-5">

                                    <div class="flex items-center gap-3">

                                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-100 font-black text-emerald-700">
                                            <span class="q-number">
                                                {{ $qi + 1 }}
                                            </span>
                                        </div>

                                        <div>
                                            <p class="text-xs font-black uppercase tracking-widest text-emerald-600">
                                                Pregunta
                                            </p>

                                            <h3 class="text-lg font-black text-slate-900">
                                                Pregunta <span class="q-number">{{ $qi + 1 }}</span>
                                            </h3>
                                        </div>

                                    </div>

                                    <button
                                        type="button"
                                        class="rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-sm font-black text-red-600 transition hover:bg-red-100"
                                        onclick="removeQuestion(this)"
                                    >
                                        🗑️ Eliminar pregunta
                                    </button>

                                </div>

                                <!-- Enunciado -->
                                <div class="mb-6">

                                    <label class="mb-2 block text-sm font-black text-slate-700">
                                        Enunciado de la pregunta
                                    </label>

                                    <input
                                        type="text"
                                        name="questions[{{ $qi }}][question]"
                                        value="{{ old("questions.$qi.question", $question->question) }}"
                                        placeholder="¿Cuál es el ORM de Laravel?"
                                        maxlength="255"
                                        required
                                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100"
                                    >

                                </div>

                                <!-- Tiempo y puntaje -->
                                <div class="mb-6 grid gap-4 sm:grid-cols-2">

                                    <div>
                                        <label class="mb-2 block text-sm font-black text-slate-700">
                                            Tiempo límite
                                            <span class="font-medium text-slate-400">
                                                (segundos)
                                            </span>
                                        </label>

                                        <input
                                            type="number"
                                            name="questions[{{ $qi }}][time_limit]"
                                            value="{{ old("questions.$qi.time_limit", $question->time_limit) }}"
                                            min="5"
                                            max="120"
                                            required
                                            class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100"
                                        >
                                    </div>

                                    <div>
                                        <label class="mb-2 block text-sm font-black text-slate-700">
                                            Puntaje
                                        </label>

                                        <input
                                            type="number"
                                            name="questions[{{ $qi }}][score]"
                                            value="{{ old("questions.$qi.score", $question->score) }}"
                                            min="1"
                                            max="1000"
                                            required
                                            class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100"
                                        >
                                    </div>

                                </div>

                                <!-- Opciones -->
                                <div class="rounded-2xl bg-slate-50 p-4 md:p-5">

                                    <div class="mb-4">
                                        <h4 class="text-sm font-black text-slate-900">
                                            Opciones de respuesta
                                        </h4>

                                        <p class="mt-1 text-xs text-slate-500">
                                            Marca el círculo de la respuesta correcta.
                                        </p>
                                    </div>

                                    <div
                                        class="options-grid grid gap-3 sm:grid-cols-2"
                                        id="options-{{ $qi }}"
                                    >

                                        @foreach($question->options as $oi => $option)

                                            <div
                                                class="option-row flex items-center gap-3 rounded-2xl border px-3 py-3 transition {{ $option->is_correct ? 'option-correct border-emerald-400 bg-emerald-50' : 'border-slate-200 bg-white' }}"
                                                id="option-{{ $qi }}-{{ $oi }}"
                                            >

                                                <input
                                                    type="radio"
                                                    name="questions[{{ $qi }}][correct]"
                                                    value="{{ $oi }}"
                                                    {{ $option->is_correct ? 'checked' : '' }}
                                                    onchange="markCorrect(this, {{ $qi }})"
                                                    class="h-5 w-5 shrink-0 cursor-pointer accent-emerald-600"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="questions[{{ $qi }}][options][{{ $oi }}][is_correct]"
                                                    value="{{ $option->is_correct ? '1' : '0' }}"
                                                    class="is-correct-input"
                                                >

                                                <input
                                                    type="text"
                                                    name="questions[{{ $qi }}][options][{{ $oi }}][text]"
                                                    value="{{ old("questions.$qi.options.$oi.text", $option->text) }}"
                                                    placeholder="Opción {{ $oi + 1 }}"
                                                    maxlength="255"
                                                    required
                                                    class="min-w-0 flex-1 rounded-xl border border-transparent bg-transparent px-2 py-2 text-sm text-slate-800 outline-none placeholder:text-slate-400 focus:border-emerald-300 focus:bg-white"
                                                >

                                                <button
                                                    type="button"
                                                    onclick="removeOption(this, {{ $qi }})"
                                                    title="Eliminar opción"
                                                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-sm font-black text-red-500 transition hover:bg-red-100 hover:text-red-700"
                                                >
                                                    ✕
                                                </button>

                                            </div>

                                        @endforeach

                                    </div>

                                    <button
                                        type="button"
                                        class="mt-4 inline-flex items-center gap-2 rounded-xl border border-dashed border-emerald-300 bg-white px-4 py-2 text-sm font-black text-emerald-700 transition hover:bg-emerald-50"
                                        onclick="addOption({{ $qi }})"
                                    >
                                        <span class="text-lg">＋</span>
                                        Agregar opción
                                    </button>

                                </div>

                            </article>

                        @endforeach

                    @else

                        <!-- Pregunta inicial -->
                        <article
                            class="question-block rounded-3xl border border-slate-200 bg-white p-6 shadow-sm md:p-8"
                            id="question-block-0"
                        >

                            <div class="question-header mb-6 flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-5">

                                <div class="flex items-center gap-3">

                                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-100 font-black text-emerald-700">
                                        <span class="q-number">1</span>
                                    </div>

                                    <div>
                                        <p class="text-xs font-black uppercase tracking-widest text-emerald-600">
                                            Pregunta
                                        </p>

                                        <h3 class="text-lg font-black text-slate-900">
                                            Pregunta <span class="q-number">1</span>
                                        </h3>
                                    </div>

                                </div>

                                <button
                                    type="button"
                                    class="rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-sm font-black text-red-600 transition hover:bg-red-100"
                                    onclick="removeQuestion(this)"
                                >
                                    🗑️ Eliminar pregunta
                                </button>

                            </div>

                            <div class="mb-6">

                                <label class="mb-2 block text-sm font-black text-slate-700">
                                    Enunciado de la pregunta
                                </label>

                                <input
                                    type="text"
                                    name="questions[0][question]"
                                    value="{{ old('questions.0.question') }}"
                                    placeholder="¿Cuál es el ORM de Laravel?"
                                    maxlength="255"
                                    required
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100"
                                >

                            </div>

                            <div class="mb-6 grid gap-4 sm:grid-cols-2">

                                <div>
                                    <label class="mb-2 block text-sm font-black text-slate-700">
                                        Tiempo límite
                                        <span class="font-medium text-slate-400">
                                            (segundos)
                                        </span>
                                    </label>

                                    <input
                                        type="number"
                                        name="questions[0][time_limit]"
                                        value="{{ old('questions.0.time_limit', 20) }}"
                                        min="5"
                                        max="120"
                                        required
                                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100"
                                    >
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-black text-slate-700">
                                        Puntaje
                                    </label>

                                    <input
                                        type="number"
                                        name="questions[0][score]"
                                        value="{{ old('questions.0.score', 100) }}"
                                        min="1"
                                        max="1000"
                                        required
                                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100"
                                    >
                                </div>

                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4 md:p-5">

                                <div class="mb-4">
                                    <h4 class="text-sm font-black text-slate-900">
                                        Opciones de respuesta
                                    </h4>

                                    <p class="mt-1 text-xs text-slate-500">
                                        Marca el círculo de la respuesta correcta.
                                    </p>
                                </div>

                                <div
                                    class="options-grid grid gap-3 sm:grid-cols-2"
                                    id="options-0"
                                >

                                    <div
                                        class="option-row flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-3 py-3 transition"
                                        id="option-0-0"
                                    >
                                        <input
                                            type="radio"
                                            name="questions[0][correct]"
                                            value="0"
                                            onchange="markCorrect(this, 0)"
                                            class="h-5 w-5 shrink-0 cursor-pointer accent-emerald-600"
                                        >

                                        <input
                                            type="hidden"
                                            name="questions[0][options][0][is_correct]"
                                            value="0"
                                            class="is-correct-input"
                                        >

                                        <input
                                            type="text"
                                            name="questions[0][options][0][text]"
                                            placeholder="Opción 1"
                                            maxlength="255"
                                            required
                                            class="min-w-0 flex-1 rounded-xl border border-transparent bg-transparent px-2 py-2 text-sm text-slate-800 outline-none placeholder:text-slate-400 focus:border-emerald-300 focus:bg-white"
                                        >

                                        <button
                                            type="button"
                                            onclick="removeOption(this, 0)"
                                            title="Eliminar opción"
                                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-sm font-black text-red-500 transition hover:bg-red-100 hover:text-red-700"
                                        >
                                            ✕
                                        </button>
                                    </div>

                                    <div
                                        class="option-row flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-3 py-3 transition"
                                        id="option-0-1"
                                    >
                                        <input
                                            type="radio"
                                            name="questions[0][correct]"
                                            value="1"
                                            onchange="markCorrect(this, 0)"
                                            class="h-5 w-5 shrink-0 cursor-pointer accent-emerald-600"
                                        >

                                        <input
                                            type="hidden"
                                            name="questions[0][options][1][is_correct]"
                                            value="0"
                                            class="is-correct-input"
                                        >

                                        <input
                                            type="text"
                                            name="questions[0][options][1][text]"
                                            placeholder="Opción 2"
                                            maxlength="255"
                                            required
                                            class="min-w-0 flex-1 rounded-xl border border-transparent bg-transparent px-2 py-2 text-sm text-slate-800 outline-none placeholder:text-slate-400 focus:border-emerald-300 focus:bg-white"
                                        >

                                        <button
                                            type="button"
                                            onclick="removeOption(this, 0)"
                                            title="Eliminar opción"
                                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-sm font-black text-red-500 transition hover:bg-red-100 hover:text-red-700"
                                        >
                                            ✕
                                        </button>
                                    </div>

                                </div>

                                <button
                                    type="button"
                                    class="mt-4 inline-flex items-center gap-2 rounded-xl border border-dashed border-emerald-300 bg-white px-4 py-2 text-sm font-black text-emerald-700 transition hover:bg-emerald-50"
                                    onclick="addOption(0)"
                                >
                                    <span class="text-lg">＋</span>
                                    Agregar opción
                                </button>

                            </div>

                        </article>

                    @endif

                </div>

                <!-- Agregar pregunta -->
                <button
                    type="button"
                    onclick="addQuestion()"
                    class="flex w-full items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-emerald-300 bg-emerald-50 px-5 py-4 text-sm font-black text-emerald-700 transition hover:border-emerald-400 hover:bg-emerald-100"
                >
                    <span class="text-xl">＋</span>
                    Agregar pregunta
                </button>

            </section>

            <!-- Acciones finales -->
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
                    {{ isset($kahoot) ? 'Actualizar Kahoot' : 'Guardar Kahoot' }}
                </button>

            </section>

        </form>

    </main>

    <script>
        // Índice global para evitar colisiones de nombres
        let questionIndex = {{ isset($questions) && $questions->count() > 0 ? $questions->count() : 1 }};

        // optionCount[qi] = cantidad de opciones creadas para cada pregunta
        const optionCount = {};

        @if(isset($questions) && $questions->count() > 0)

            @foreach($questions as $qi => $question)
                optionCount[{{ $qi }}] = {{ $question->options->count() }};
            @endforeach

        @else

            optionCount[0] = 2;

        @endif

        function updateQuestionCount() {
            const count = document.querySelectorAll('.question-block').length;
            const counter = document.getElementById('question-count');

            if (counter) {
                counter.textContent = count;
            }
        }

        function markCorrect(radio, qi) {
            const grid = document.getElementById(`options-${qi}`);

            if (!grid) {
                return;
            }

            const rows = grid.querySelectorAll('.option-row');

            rows.forEach((row, i) => {
                const hidden = row.querySelector('.is-correct-input');
                const isSelected = i === parseInt(radio.value);

                if (hidden) {
                    hidden.value = isSelected ? '1' : '0';
                }

                row.classList.toggle('option-correct', isSelected);
                row.classList.toggle('border-emerald-400', isSelected);
                row.classList.toggle('bg-emerald-50', isSelected);
                row.classList.toggle('border-slate-200', !isSelected);
                row.classList.toggle('bg-white', !isSelected);
            });
        }

        function addOption(qi) {
            const grid = document.getElementById(`options-${qi}`);

            if (!grid) {
                return;
            }

            const currentCount = grid.querySelectorAll('.option-row').length;

            if (currentCount >= 4) {
                alert('Cada pregunta puede tener máximo 4 opciones.');
                return;
            }

            const oi = optionCount[qi] ?? currentCount;

            optionCount[qi] = oi + 1;

            const div = document.createElement('div');

            div.classList.add(
                'option-row',
                'flex',
                'items-center',
                'gap-3',
                'rounded-2xl',
                'border',
                'border-slate-200',
                'bg-white',
                'px-3',
                'py-3',
                'transition'
            );

            div.id = `option-${qi}-${oi}`;

            div.innerHTML = `
                <input
                    type="radio"
                    name="questions[${qi}][correct]"
                    value="${oi}"
                    onchange="markCorrect(this, ${qi})"
                    class="h-5 w-5 shrink-0 cursor-pointer accent-emerald-600"
                >

                <input
                    type="hidden"
                    name="questions[${qi}][options][${oi}][is_correct]"
                    value="0"
                    class="is-correct-input"
                >

                <input
                    type="text"
                    name="questions[${qi}][options][${oi}][text]"
                    placeholder="Opción ${currentCount + 1}"
                    maxlength="255"
                    required
                    class="min-w-0 flex-1 rounded-xl border border-transparent bg-transparent px-2 py-2 text-sm text-slate-800 outline-none placeholder:text-slate-400 focus:border-emerald-300 focus:bg-white"
                >

                <button
                    type="button"
                    onclick="removeOption(this, ${qi})"
                    title="Eliminar opción"
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-sm font-black text-red-500 transition hover:bg-red-100 hover:text-red-700"
                >
                    ✕
                </button>
            `;

            grid.appendChild(div);
        }

        function removeOption(button, qi) {
            const grid = document.getElementById(`options-${qi}`);

            if (!grid) {
                return;
            }

            const rows = grid.querySelectorAll('.option-row');

            if (rows.length <= 2) {
                alert('Cada pregunta necesita al menos 2 opciones.');
                return;
            }

            button.closest('.option-row').remove();

            reindexOptions(qi);
        }

        function reindexOptions(qi) {
            const grid = document.getElementById(`options-${qi}`);

            if (!grid) {
                return;
            }

            const rows = grid.querySelectorAll('.option-row');

            rows.forEach((row, i) => {
                const radio = row.querySelector('input[type="radio"]');
                const hidden = row.querySelector('.is-correct-input');
                const text = row.querySelector('input[type="text"]');
                const removeButton = row.querySelector('button');

                if (radio) {
                    radio.name = `questions[${qi}][correct]`;
                    radio.value = i;
                    radio.onchange = function () {
                        markCorrect(this, qi);
                    };
                }

                if (hidden) {
                    hidden.name = `questions[${qi}][options][${i}][is_correct]`;
                }

                if (text) {
                    text.name = `questions[${qi}][options][${i}][text]`;
                    text.placeholder = `Opción ${i + 1}`;
                }

                if (removeButton) {
                    removeButton.onclick = function () {
                        removeOption(this, qi);
                    };
                }

                row.id = `option-${qi}-${i}`;
            });

            optionCount[qi] = rows.length;
        }

        function addQuestion() {
            const qi = questionIndex;

            optionCount[qi] = 2;

            const container = document.getElementById('questions-container');

            const block = document.createElement('article');

            block.classList.add(
                'question-block',
                'rounded-3xl',
                'border',
                'border-slate-200',
                'bg-white',
                'p-6',
                'shadow-sm',
                'md:p-8'
            );

            block.id = `question-block-${qi}`;

            const questionNumber =
                container.querySelectorAll('.question-block').length + 1;

            block.innerHTML = `
                <div class="question-header mb-6 flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-5">

                    <div class="flex items-center gap-3">

                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-100 font-black text-emerald-700">
                            <span class="q-number">${questionNumber}</span>
                        </div>

                        <div>
                            <p class="text-xs font-black uppercase tracking-widest text-emerald-600">
                                Pregunta
                            </p>

                            <h3 class="text-lg font-black text-slate-900">
                                Pregunta <span class="q-number">${questionNumber}</span>
                            </h3>
                        </div>

                    </div>

                    <button
                        type="button"
                        class="rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-sm font-black text-red-600 transition hover:bg-red-100"
                        onclick="removeQuestion(this)"
                    >
                        🗑️ Eliminar pregunta
                    </button>

                </div>

                <div class="mb-6">

                    <label class="mb-2 block text-sm font-black text-slate-700">
                        Enunciado de la pregunta
                    </label>

                    <input
                        type="text"
                        name="questions[${qi}][question]"
                        placeholder="Escribe la pregunta..."
                        maxlength="255"
                        required
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100"
                    >

                </div>

                <div class="mb-6 grid gap-4 sm:grid-cols-2">

                    <div>
                        <label class="mb-2 block text-sm font-black text-slate-700">
                            Tiempo límite
                            <span class="font-medium text-slate-400">
                                (segundos)
                            </span>
                        </label>

                        <input
                            type="number"
                            name="questions[${qi}][time_limit]"
                            value="20"
                            min="5"
                            max="120"
                            required
                            class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100"
                        >
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-black text-slate-700">
                            Puntaje
                        </label>

                        <input
                            type="number"
                            name="questions[${qi}][score]"
                            value="100"
                            min="1"
                            max="1000"
                            required
                            class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100"
                        >
                    </div>

                </div>

                <div class="rounded-2xl bg-slate-50 p-4 md:p-5">

                    <div class="mb-4">
                        <h4 class="text-sm font-black text-slate-900">
                            Opciones de respuesta
                        </h4>

                        <p class="mt-1 text-xs text-slate-500">
                            Marca el círculo de la respuesta correcta.
                        </p>
                    </div>

                    <div
                        class="options-grid grid gap-3 sm:grid-cols-2"
                        id="options-${qi}"
                    >

                        <div
                            class="option-row flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-3 py-3 transition"
                            id="option-${qi}-0"
                        >
                            <input
                                type="radio"
                                name="questions[${qi}][correct]"
                                value="0"
                                onchange="markCorrect(this, ${qi})"
                                class="h-5 w-5 shrink-0 cursor-pointer accent-emerald-600"
                            >

                            <input
                                type="hidden"
                                name="questions[${qi}][options][0][is_correct]"
                                value="0"
                                class="is-correct-input"
                            >

                            <input
                                type="text"
                                name="questions[${qi}][options][0][text]"
                                placeholder="Opción 1"
                                maxlength="255"
                                required
                                class="min-w-0 flex-1 rounded-xl border border-transparent bg-transparent px-2 py-2 text-sm text-slate-800 outline-none placeholder:text-slate-400 focus:border-emerald-300 focus:bg-white"
                            >

                            <button
                                type="button"
                                onclick="removeOption(this, ${qi})"
                                title="Eliminar opción"
                                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-sm font-black text-red-500 transition hover:bg-red-100 hover:text-red-700"
                            >
                                ✕
                            </button>
                        </div>

                        <div
                            class="option-row flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-3 py-3 transition"
                            id="option-${qi}-1"
                        >
                            <input
                                type="radio"
                                name="questions[${qi}][correct]"
                                value="1"
                                onchange="markCorrect(this, ${qi})"
                                class="h-5 w-5 shrink-0 cursor-pointer accent-emerald-600"
                            >

                            <input
                                type="hidden"
                                name="questions[${qi}][options][1][is_correct]"
                                value="0"
                                class="is-correct-input"
                            >

                            <input
                                type="text"
                                name="questions[${qi}][options][1][text]"
                                placeholder="Opción 2"
                                maxlength="255"
                                required
                                class="min-w-0 flex-1 rounded-xl border border-transparent bg-transparent px-2 py-2 text-sm text-slate-800 outline-none placeholder:text-slate-400 focus:border-emerald-300 focus:bg-white"
                            >

                            <button
                                type="button"
                                onclick="removeOption(this, ${qi})"
                                title="Eliminar opción"
                                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-sm font-black text-red-500 transition hover:bg-red-100 hover:text-red-700"
                            >
                                ✕
                            </button>
                        </div>

                    </div>

                    <button
                        type="button"
                        class="mt-4 inline-flex items-center gap-2 rounded-xl border border-dashed border-emerald-300 bg-white px-4 py-2 text-sm font-black text-emerald-700 transition hover:bg-emerald-50"
                        onclick="addOption(${qi})"
                    >
                        <span class="text-lg">＋</span>
                        Agregar opción
                    </button>

                </div>
            `;

            container.appendChild(block);

            questionIndex++;

            updateQuestionNumbers();
            updateQuestionCount();
        }

        function removeQuestion(button) {
            const container = document.getElementById('questions-container');

            if (container.querySelectorAll('.question-block').length <= 1) {
                alert('El Kahoot necesita al menos una pregunta.');
                return;
            }

            button.closest('.question-block').remove();

            updateQuestionNumbers();
            updateQuestionCount();
        }

        function updateQuestionNumbers() {
            const blocks = document.querySelectorAll('.question-block');

            blocks.forEach((block, index) => {
                const numbers = block.querySelectorAll('.q-number');

                numbers.forEach((element) => {
                    element.textContent = index + 1;
                });
            });
        }

        updateQuestionNumbers();
        updateQuestionCount();
    </script>

</body>

</html>