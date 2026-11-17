@extends('teacher.layouts.app')

@section('title', (isset($kahoot) ? 'Editar' : 'Configurar').' Kahoot — '.$activity->title)

@section('page-title', isset($kahoot) ? 'Editar Kahoot' : 'Configurar Kahoot')

@section('content')

    <div class="mx-auto max-w-5xl">

        <div class="mb-6">
            <a href="{{ route('teacher.activities.show', $activity->id) }}"
               class="inline-flex self-start items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Volver a la actividad
            </a>
        </div>

        {{-- Encabezado --}}
        <section class="mb-6 rounded-2xl bg-emerald-700 p-5 text-white shadow-sm sm:p-6">

            <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">

                <div class="min-w-0">
                    <p class="text-xs font-bold uppercase tracking-widest text-emerald-200">
                        Cuestionario interactivo
                    </p>

                    <h2 class="mt-1 text-2xl font-bold tracking-tight sm:text-3xl">
                        {{ isset($kahoot) ? 'Editar Kahoot' : 'Configurar Kahoot' }}
                    </h2>

                    <p class="mt-2 flex items-center gap-1.5 text-sm font-bold text-emerald-100">
                        <svg class="h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                        </svg>
                        {{ $activity->title }}
                    </p>
                </div>

                <div class="hidden h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-emerald-600 ring-1 ring-emerald-500 md:flex">
                    <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z" />
                    </svg>
                </div>

            </div>

        </section>

        {{-- Errores --}}
        @if ($errors->any())
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 shadow-sm sm:p-5">

                <div class="flex items-center gap-3">

                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-700 ring-1 ring-red-200">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                        </svg>
                    </div>

                    <h3 class="text-sm font-bold text-red-800">
                        Revisa los siguientes datos
                    </h3>

                </div>

                <ul class="mt-3 list-disc space-y-1 pl-6 text-sm text-red-800">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>

            </div>
        @endif

        {{-- Ayuda --}}
        <section class="mb-6 flex items-start gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">

            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                </svg>
            </div>

            <div class="min-w-0">
                <h3 class="text-sm font-bold text-slate-900">
                    Cómo configurar el Kahoot
                </h3>

                <p class="mt-1 text-sm text-slate-600">
                    Agrega las preguntas y sus opciones. Marca el círculo de la
                    respuesta correcta.
                </p>

                <p class="mt-1.5 text-xs font-bold text-slate-500">
                    Cada pregunta necesita entre 2 y 4 opciones, y exactamente
                    una debe ser correcta.
                </p>
            </div>

        </section>

        {{-- Formulario --}}
        <form method="POST" id="kahoot-form"
              action="{{ isset($kahoot) ? route('teacher.kahoot.update', $activity->id) : route('teacher.kahoot.store', $activity->id) }}">

            @csrf

            @if (isset($kahoot))
                @method('PUT')
            @endif

            <section class="mb-6 space-y-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">

                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wide text-emerald-600">
                            Banco de preguntas
                        </p>

                        <h3 class="mt-0.5 text-lg font-bold text-slate-900">
                            Preguntas del Kahoot
                        </h3>
                    </div>

                    <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-700">
                        <span data-questions-count>{{ isset($questions) && $questions->count() > 0 ? $questions->count() : 1 }}</span>
                        <span data-questions-label>{{ (isset($questions) && $questions->count() > 0 ? $questions->count() : 1) === 1 ? 'pregunta' : 'preguntas' }}</span>
                    </span>
                </div>

                <div id="questions-container" class="space-y-4">

                    @if (isset($questions) && $questions->count() > 0)
                        @foreach ($questions as $qi => $question)
                            @include('teacher.kahoot.partials.question-card', ['qi' => $qi, 'question' => $question])
                        @endforeach
                    @else
                        @include('teacher.kahoot.partials.question-card', ['qi' => 0, 'question' => null])
                    @endif

                </div>

                <button type="button" id="add-question"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-emerald-300 bg-emerald-50 px-5 py-3.5 text-sm font-bold text-emerald-700 transition hover:border-emerald-400 hover:bg-emerald-100 sm:w-auto">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Agregar pregunta
                </button>

            </section>

            {{-- Acciones finales --}}
            <section class="mb-8 flex flex-col-reverse gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between sm:p-6">

                <a href="{{ route('teacher.activities.show', $activity->id) }}"
                   class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-600 transition hover:bg-slate-50 hover:text-slate-900">
                    Cancelar
                </a>

                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-700 px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                    {{ isset($kahoot) ? 'Actualizar Kahoot' : 'Guardar Kahoot' }}
                </button>

            </section>

        </form>

    </div>

@endsection

@push('scripts')
    <script>
        (function () {
            var container = document.getElementById('questions-container');
            var countBadge = document.querySelector('[data-questions-count]');
            var countLabel = document.querySelector('[data-questions-label]');
            var addQuestionButton = document.getElementById('add-question');

            var trashPath = '<path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />';

            function fieldClass() {
                return 'w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100';
            }

            function labelClass() {
                return 'mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-400';
            }

            function questionCount() {
                return container.querySelectorAll('[data-question-card]').length;
            }

            function updateCount() {
                var count = questionCount();

                if (countBadge) {
                    countBadge.textContent = count;
                }

                if (countLabel) {
                    countLabel.textContent = count === 1 ? 'pregunta' : 'preguntas';
                }

                container.querySelectorAll('[data-question-card]').forEach(function (card) {
                    var toggle = card.querySelector('[data-confirm-toggle]');

                    if (toggle) {
                        toggle.disabled = count <= 1;
                    }
                });
            }

            function closePanels() {
                container.querySelectorAll('[data-confirm-panel]').forEach(function (panel) {
                    panel.classList.add('hidden');

                    var header = panel.parentElement;
                    var toggle = header ? header.querySelector('[data-confirm-toggle]') : null;

                    if (toggle) {
                        toggle.setAttribute('aria-expanded', 'false');
                    }
                });
            }

            function updateQuestionNumbers() {
                container.querySelectorAll('[data-question-card]').forEach(function (card, index) {
                    var badge = card.querySelector('[data-q-number]');

                    if (badge) {
                        badge.textContent = index + 1;
                    }
                });
            }

            function markCorrect(radio, grid) {
                var rows = grid.querySelectorAll('.option-row');
                var selected = parseInt(radio.value, 10);

                rows.forEach(function (row, i) {
                    var hidden = row.querySelector('.is-correct-input');
                    var isSelected = i === selected;

                    if (hidden) {
                        hidden.value = isSelected ? '1' : '0';
                    }

                    row.classList.toggle('border-emerald-400', isSelected);
                    row.classList.toggle('bg-emerald-50', isSelected);
                    row.classList.toggle('border-slate-200', !isSelected);
                    row.classList.toggle('bg-white', !isSelected);
                });
            }

            function updateOptionControls(grid) {
                var rows = grid.querySelectorAll('.option-row');
                var addButton = grid.parentElement.querySelector('[data-option-add]');

                // Entre 2 y 4 opciones por pregunta.
                grid.querySelectorAll('[data-option-remove]').forEach(function (button) {
                    button.disabled = rows.length <= 2;
                });

                if (addButton) {
                    addButton.disabled = rows.length >= 4;
                }
            }

            function createOptionRow(grid, safeIndex) {
                var qi = grid.id.replace('options-', '');

                var row = document.createElement('div');

                row.className = 'option-row flex items-center gap-2.5 rounded-xl border border-slate-200 bg-white px-3 py-2.5 transition';

                row.innerHTML =
                    '<input type="radio" name="questions[' + qi + '][correct]" value="' + safeIndex + '" class="h-4 w-4 shrink-0 cursor-pointer accent-emerald-600">' +
                    '<input type="hidden" name="questions[' + qi + '][options][' + safeIndex + '][is_correct]" value="0" class="is-correct-input">' +
                    '<input type="text" name="questions[' + qi + '][options][' + safeIndex + '][text]" placeholder="Opción" maxlength="255" required class="min-w-0 w-0 flex-1 rounded-lg border border-transparent bg-transparent px-2 py-1.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-emerald-300 focus:bg-white">' +
                    '<button type="button" data-option-remove title="Eliminar opción" aria-label="Eliminar esta opción" class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-slate-400 transition hover:bg-red-50 hover:text-red-600">' +
                        '<svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">' +
                            '<path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />' +
                        '</svg>' +
                    '</button>';

                return row;
            }

            function reindexOptions(grid) {
                var qi = grid.id.replace('options-', '');

                grid.querySelectorAll('.option-row').forEach(function (row, i) {
                    var radio = row.querySelector('input[type="radio"]');
                    var hidden = row.querySelector('.is-correct-input');
                    var text = row.querySelector('input[type="text"]');

                    if (radio) {
                        radio.name = 'questions[' + qi + '][correct]';
                        radio.value = i;
                    }

                    if (hidden) {
                        hidden.name = 'questions[' + qi + '][options][' + i + '][is_correct]';
                    }

                    if (text) {
                        text.name = 'questions[' + qi + '][options][' + i + '][text]';
                        text.placeholder = 'Opción ' + (i + 1);
                    }
                });

                updateOptionControls(grid);
            }

            function createQuestionCard(safeIndex) {
                var card = document.createElement('article');

                card.className = 'question-block rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5';
                card.setAttribute('data-question-card', '');
                card.id = 'question-block-' + safeIndex;

                var fc = fieldClass();
                var lc = labelClass();

                card.innerHTML =
                    '<div class="relative flex items-start justify-between gap-3 pb-4">' +
                        '<div class="flex items-center gap-3">' +
                            '<div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-sm font-bold text-emerald-700 ring-1 ring-emerald-100">' +
                                '<span data-q-number>0</span>' +
                            '</div>' +
                            '<span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-700">Pregunta</span>' +
                        '</div>' +
                        '<button type="button" data-confirm-toggle aria-expanded="false" aria-label="Eliminar esta pregunta" class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl text-slate-400 transition hover:bg-red-50 hover:text-red-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500">' +
                            '<svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">' + trashPath + '</svg>' +
                        '</button>' +
                        '<div class="absolute right-0 top-12 z-10 hidden w-64 rounded-xl border border-red-200 bg-red-50 p-3 shadow-lg" data-confirm-panel>' +
                            '<p class="text-xs font-bold text-red-800">¿Eliminar esta pregunta?</p>' +
                            '<div class="mt-2 flex gap-2">' +
                                '<button type="button" data-confirm-remove class="flex-1 rounded-lg bg-red-600 px-3 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-red-700">Sí, eliminar</button>' +
                                '<button type="button" data-confirm-cancel class="rounded-lg border border-red-200 bg-white px-3 py-2 text-xs font-bold text-slate-600 transition hover:bg-red-50">Cancelar</button>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                    '<div class="mb-4">' +
                        '<label class="' + lc + '">Enunciado</label>' +
                        '<input type="text" name="questions[' + safeIndex + '][question]" placeholder="¿Cuál es el ORM de Laravel?" maxlength="255" required class="' + fc + '">' +
                    '</div>' +
                    '<div class="mb-4 grid grid-cols-2 gap-3">' +
                        '<div>' +
                            '<label class="' + lc + '">Tiempo límite (segundos)</label>' +
                            '<input type="number" name="questions[' + safeIndex + '][time_limit]" value="20" min="5" max="120" required class="' + fc + ' font-bold">' +
                        '</div>' +
                        '<div>' +
                            '<label class="' + lc + '">Puntaje</label>' +
                            '<input type="number" name="questions[' + safeIndex + '][score]" value="100" min="1" max="1000" required class="' + fc + ' font-bold">' +
                        '</div>' +
                    '</div>' +
                    '<div class="rounded-2xl bg-slate-50 p-3 sm:p-4">' +
                        '<div class="mb-3 flex flex-wrap items-center justify-between gap-2">' +
                            '<h4 class="text-sm font-bold text-slate-900">Opciones de respuesta</h4>' +
                            '<p class="text-[11px] font-bold text-slate-500">Marca la correcta · de 2 a 4 opciones</p>' +
                        '</div>' +
                        '<div class="options-grid grid gap-3 sm:grid-cols-2" id="options-' + safeIndex + '">' +
                            '<div class="option-row flex items-center gap-2.5 rounded-xl border border-slate-200 bg-white px-3 py-2.5 transition">' +
                                '<input type="radio" name="questions[' + safeIndex + '][correct]" value="0" class="h-4 w-4 shrink-0 cursor-pointer accent-emerald-600">' +
                                '<input type="hidden" name="questions[' + safeIndex + '][options][0][is_correct]" value="0" class="is-correct-input">' +
                                '<input type="text" name="questions[' + safeIndex + '][options][0][text]" placeholder="Opción 1" maxlength="255" required class="min-w-0 w-0 flex-1 rounded-lg border border-transparent bg-transparent px-2 py-1.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-emerald-300 focus:bg-white">' +
                                '<button type="button" data-option-remove title="Eliminar opción" aria-label="Eliminar la opción 1" class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-slate-400 transition hover:bg-red-50 hover:text-red-600">' +
                                    '<svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>' +
                                '</button>' +
                            '</div>' +
                            '<div class="option-row flex items-center gap-2.5 rounded-xl border border-slate-200 bg-white px-3 py-2.5 transition">' +
                                '<input type="radio" name="questions[' + safeIndex + '][correct]" value="1" class="h-4 w-4 shrink-0 cursor-pointer accent-emerald-600">' +
                                '<input type="hidden" name="questions[' + safeIndex + '][options][1][is_correct]" value="0" class="is-correct-input">' +
                                '<input type="text" name="questions[' + safeIndex + '][options][1][text]" placeholder="Opción 2" maxlength="255" required class="min-w-0 w-0 flex-1 rounded-lg border border-transparent bg-transparent px-2 py-1.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-emerald-300 focus:bg-white">' +
                                '<button type="button" data-option-remove title="Eliminar opción" aria-label="Eliminar la opción 2" class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-slate-400 transition hover:bg-red-50 hover:text-red-600">' +
                                    '<svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>' +
                                '</button>' +
                            '</div>' +
                        '</div>' +
                        '<button type="button" data-option-add class="mt-3 inline-flex items-center gap-2 rounded-xl border border-dashed border-emerald-300 bg-white px-4 py-2 text-sm font-bold text-emerald-700 transition hover:bg-emerald-50">' +
                            '<svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>' +
                            'Agregar opción' +
                        '</button>' +
                    '</div>';

                return card;
            }

            if (addQuestionButton) {
                addQuestionButton.addEventListener('click', function () {
                    closePanels();

                    // Índice alto y único para no pisar preguntas existentes;
                    // el backend no depende del orden de los índices.
                    var safeIndex = 1000 + Math.floor(Math.random() * 9000);

                    container.appendChild(createQuestionCard(safeIndex));
                    updateQuestionNumbers();
                    updateCount();

                    var first = container.querySelector('#question-block-' + safeIndex + ' input[type="text"]');

                    if (first) {
                        first.focus();
                    }
                });
            }

            container.addEventListener('click', function (event) {
                var radio = event.target.closest('input[type="radio"]');

                if (radio) {
                    var grid = radio.closest('.options-grid');

                    if (grid) {
                        markCorrect(radio, grid);
                    }

                    return;
                }

                var optionAdd = event.target.closest('[data-option-add]');

                if (optionAdd) {
                    var optionGrid = optionAdd.closest('.rounded-2xl').querySelector('.options-grid');
                    var rows = optionGrid.querySelectorAll('.option-row');

                    if (rows.length >= 4) {
                        return;
                    }

                    // Índice alto y único por grid; reindexOptions normaliza.
                    var safeOption = 1000 + Math.floor(Math.random() * 9000);

                    var row = createOptionRow(optionGrid, safeOption);

                    optionGrid.appendChild(row);
                    reindexOptions(optionGrid);

                    var text = row.querySelector('input[type="text"]');

                    if (text) {
                        text.focus();
                    }

                    return;
                }

                var optionRemove = event.target.closest('[data-option-remove]');

                if (optionRemove) {
                    var currentGrid = optionRemove.closest('.options-grid');

                    if (currentGrid.querySelectorAll('.option-row').length <= 2) {
                        return;
                    }

                    optionRemove.closest('.option-row').remove();
                    reindexOptions(currentGrid);

                    return;
                }

                var toggle = event.target.closest('[data-confirm-toggle]');

                if (toggle) {
                    if (toggle.disabled) {
                        return;
                    }

                    var panel = toggle.parentElement.querySelector('[data-confirm-panel]');
                    var wasOpen = toggle.getAttribute('aria-expanded') === 'true';

                    closePanels();

                    if (!wasOpen && panel) {
                        panel.classList.remove('hidden');
                        toggle.setAttribute('aria-expanded', 'true');
                    }

                    return;
                }

                var cancel = event.target.closest('[data-confirm-cancel]');

                if (cancel) {
                    closePanels();
                    return;
                }

                var remove = event.target.closest('[data-confirm-remove]');

                if (remove) {
                    var card = remove.closest('[data-question-card]');

                    if (card && questionCount() > 1) {
                        card.remove();
                        updateQuestionNumbers();
                        updateCount();
                    }
                }
            });

            // Estado inicial: controles de opciones y conteo.
            container.querySelectorAll('.options-grid').forEach(updateOptionControls);
            updateQuestionNumbers();
            updateCount();
        })();
    </script>
@endpush
