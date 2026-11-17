@extends('teacher.layouts.app')

@section('title', ($editing ? 'Editar' : 'Configurar').' Ruleta — '.$activity->title)

@section('page-title', $editing ? 'Editar Ruleta' : 'Configurar Ruleta')

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
                        Juego de ruleta
                    </p>

                    <h2 class="mt-1 text-2xl font-bold tracking-tight sm:text-3xl">
                        {{ $editing ? 'Editar Ruleta' : 'Configurar Ruleta' }}
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
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                </div>

            </div>

        </section>

        {{-- Acciones de actividad --}}
        <section class="mb-6">
            <a href="{{ route('teacher.roulette.configure', $activity->id) }}"
               class="inline-flex items-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-4 py-2.5 text-sm font-bold text-amber-800 transition hover:bg-amber-100">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
                Reiniciar configuración
            </a>
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
                    Cómo configurar la ruleta
                </h3>

                <p class="mt-1 text-sm text-slate-600">
                    Cada casillero tiene una pregunta con cuatro opciones: marca
                    la correcta y asigna los puntos.
                </p>

                <p class="mt-1.5 text-xs font-bold text-slate-500">
                    Tus alumnos giran la ruleta y responden la pregunta que les toque.
                </p>
            </div>

        </section>

        {{-- Formulario --}}
        <form id="roulette-form" method="POST"
              action="{{ $editing ? route('teacher.roulette.update', $activity->id) : route('teacher.roulette.store', $activity->id) }}">

            @csrf

            @if ($editing)
                @method('PUT')
            @endif

            <section class="mb-6 space-y-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">

                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wide text-emerald-600">
                            Contenido del juego
                        </p>

                        <h3 class="mt-0.5 text-lg font-bold text-slate-900">
                            Casilleros de la ruleta
                        </h3>
                    </div>

                    <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-700">
                        <span data-items-count>{{ $editing && $roulette ? $roulette->items()->count() : 1 }}</span>
                        <span data-items-label>{{ ($editing && $roulette ? $roulette->items()->count() : 1) === 1 ? 'casillero' : 'casilleros' }}</span>
                    </span>
                </div>

                <div id="items-container" class="space-y-4">

                    @forelse (($editing && $roulette ? $roulette->items : []) as $i => $item)
                        @include('teacher.roulette.partials.item-card', ['i' => $i, 'item' => $item])
                    @empty
                        @include('teacher.roulette.partials.item-card', ['i' => 0, 'item' => null])
                    @endforelse

                </div>

                <button type="button" id="add-item"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-emerald-300 bg-emerald-50 px-5 py-3.5 text-sm font-bold text-emerald-700 transition hover:border-emerald-400 hover:bg-emerald-100 sm:w-auto">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Agregar casillero
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
                    {{ $editing ? 'Actualizar preguntas' : 'Guardar preguntas' }}
                </button>

            </section>

        </form>

    </div>

@endsection

@push('scripts')
    <script>
        (function () {
            var container = document.getElementById('items-container');
            var countBadge = document.querySelector('[data-items-count]');
            var countLabel = document.querySelector('[data-items-label]');
            var addItem = document.getElementById('add-item');
            var form = document.getElementById('roulette-form');

            function itemCount() {
                return container.querySelectorAll('[data-item-card]').length;
            }

            function updateCount() {
                var count = itemCount();

                if (countBadge) {
                    countBadge.textContent = count;
                }

                if (countLabel) {
                    countLabel.textContent = count === 1 ? 'casillero' : 'casilleros';
                }

                container.querySelectorAll('[data-item-card]').forEach(function (card) {
                    var toggle = card.querySelector('[data-confirm-toggle]');

                    if (toggle) {
                        toggle.disabled = count <= 1;
                    }
                });
            }

            function closePanels() {
                container.querySelectorAll('[data-confirm-panel]').forEach(function (panel) {
                    panel.classList.add('hidden');

                    var card = panel.closest('[data-item-card]');
                    var toggle = card ? card.querySelector('[data-confirm-toggle]') : null;

                    if (toggle) {
                        toggle.setAttribute('aria-expanded', 'false');
                    }
                });
            }

            function createCard(index) {
                var card = document.createElement('article');

                card.className = 'rounded-2xl border border-slate-200 bg-white p-4 shadow-sm';
                card.setAttribute('data-item-card', '');

                var fieldClass = 'w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100';
                var labelClass = 'mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-400';

                var html =
                    '<div class="relative flex items-start justify-between gap-3">' +
                        '<span class="mt-0.5 rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-700" data-item-badge>Casillero</span>' +
                        '<button type="button" data-confirm-toggle aria-expanded="false" aria-label="Eliminar este casillero" class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl text-slate-400 transition hover:bg-red-50 hover:text-red-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500">' +
                            '<svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">' +
                                '<path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />' +
                            '</svg>' +
                        '</button>' +
                        '<div class="absolute right-0 top-12 z-10 hidden w-64 rounded-xl border border-red-200 bg-red-50 p-3 shadow-lg" data-confirm-panel>' +
                            '<p class="text-xs font-bold text-red-800">¿Eliminar este casillero?</p>' +
                            '<div class="mt-2 flex gap-2">' +
                                '<button type="button" data-confirm-remove class="flex-1 rounded-lg bg-red-600 px-3 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-red-700">Sí, eliminar</button>' +
                                '<button type="button" data-confirm-cancel class="rounded-lg border border-red-200 bg-white px-3 py-2 text-xs font-bold text-slate-600 transition hover:bg-red-50">Cancelar</button>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                    '<div class="mt-3">' +
                        '<label class="' + labelClass + '">Pregunta</label>' +
                        '<input type="text" name="items[' + index + '][question]" placeholder="¿Qué es HTTP?" required class="' + fieldClass + '">' +
                    '</div>' +
                    '<div class="mt-3 grid grid-cols-2 gap-3">' +
                        '<div><label class="' + labelClass + '">Opción A</label><input type="text" name="items[' + index + '][option_a]" placeholder="Opción A" required class="' + fieldClass + '"></div>' +
                        '<div><label class="' + labelClass + '">Opción B</label><input type="text" name="items[' + index + '][option_b]" placeholder="Opción B" required class="' + fieldClass + '"></div>' +
                        '<div><label class="' + labelClass + '">Opción C</label><input type="text" name="items[' + index + '][option_c]" placeholder="Opción C" required class="' + fieldClass + '"></div>' +
                        '<div><label class="' + labelClass + '">Opción D</label><input type="text" name="items[' + index + '][option_d]" placeholder="Opción D" required class="' + fieldClass + '"></div>' +
                    '</div>' +
                    '<div class="mt-3 grid grid-cols-2 gap-3">' +
                        '<div><label class="' + labelClass + '">Correcta</label>' +
                            '<select name="items[' + index + '][correct_option]" required class="' + fieldClass + ' font-bold">' +
                                '<option value="a">A</option>' +
                                '<option value="b">B</option>' +
                                '<option value="c">C</option>' +
                                '<option value="d">D</option>' +
                            '</select>' +
                        '</div>' +
                        '<div><label class="' + labelClass + '">Puntos</label><input type="number" name="items[' + index + '][points]" value="10" min="1" required class="' + fieldClass + ' font-bold"></div>' +
                    '</div>';

                card.innerHTML = html;

                return card;
            }

            function reindex() {
                container.querySelectorAll('[data-item-card]').forEach(function (card, i) {
                    card.querySelectorAll('input, select').forEach(function (el) {
                        el.name = el.name.replace(
                            /items\[\d+\]\[([a-z_]+)\]/,
                            'items[' + i + '][$1]'
                        );
                    });

                    var badge = card.querySelector('[data-item-badge]');

                    if (badge) {
                        badge.textContent = 'Casillero ' + (i + 1);
                    }
                });
            }

            if (addItem) {
                addItem.addEventListener('click', function () {
                    closePanels();

                    // Índice alto y único para no pisar filas existentes;
                    // reindex() normaliza al final.
                    var safeIndex = 1000 + Math.floor(Math.random() * 9000);

                    container.appendChild(createCard(safeIndex));
                    reindex();
                    updateCount();

                    var inputs = container.querySelectorAll('[data-item-card] input[type="text"]');
                    var last = inputs[inputs.length - 1];

                    if (last) {
                        last.focus();
                    }
                });
            }

            // Confirmación en dos pasos con delegación (las tarjetas se
            // crean dinámicamente).
            container.addEventListener('click', function (event) {
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
                    var card = remove.closest('[data-item-card]');

                    if (card && itemCount() > 1) {
                        card.remove();
                        reindex();
                        updateCount();
                    }
                }
            });

            if (form) {
                form.addEventListener('submit', function () {
                    closePanels();

                    var submitButton = form.querySelector('button[type="submit"]');

                    if (submitButton) {
                        submitButton.disabled = true;
                        submitButton.classList.add('cursor-wait', 'opacity-75');
                    }
                });
            }

            updateCount();
        })();
    </script>
@endpush
