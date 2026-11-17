@extends('teacher.layouts.app')

@section('title', ($editing ? 'Editar' : 'Configurar').' sopa de letras — '.$activity->title)

@section('page-title', $editing ? 'Editar sopa de letras' : 'Configurar sopa de letras')

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
                        Juego de palabras
                    </p>

                    <h2 class="mt-1 text-2xl font-bold tracking-tight sm:text-3xl">
                        {{ $editing ? 'Editar sopa de letras' : 'Configurar sopa de letras' }}
                    </h2>

                    <p class="mt-2 flex items-center gap-1.5 text-sm font-bold text-emerald-100">
                        <svg class="h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                        {{ $activity->title }}
                    </p>
                </div>

                <div class="hidden h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-emerald-600 ring-1 ring-emerald-500 md:flex">
                    <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
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
                    Cómo configurar la sopa
                </h3>

                <p class="mt-1 text-sm text-slate-600">
                    Define el tamaño de la cuadrícula (entre 2 y 30), agrega las
                    palabras y asigna los puntos de cada una.
                </p>

                <p class="mt-1.5 text-xs font-bold text-slate-500">
                    La sopa necesita al menos 1 palabra. El sistema las coloca
                    automáticamente en la cuadrícula.
                </p>
            </div>

        </section>

        {{-- Formulario --}}
        <form method="POST"
              action="{{ $editing ? route('teacher.wordsearch.update', $activity->id) : route('teacher.wordsearch.store', $activity->id) }}">

            @csrf

            @if ($editing)
                @method('PUT')
            @endif

            <div class="mb-6 space-y-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">

                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wide text-emerald-600">
                            Configuración
                        </p>

                        <h3 class="mt-0.5 text-lg font-bold text-slate-900">
                            Datos de la sopa
                        </h3>
                    </div>

                    <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-700">
                        <span data-words-count>{{ $editing && $wordsearch ? $wordsearch->words()->count() : 1 }}</span>
                        <span data-words-label>{{ ($editing && $wordsearch ? $wordsearch->words()->count() : 1) === 1 ? 'palabra' : 'palabras' }}</span>
                    </span>
                </div>

                {{-- Dimensiones --}}
                <div class="grid grid-cols-2 gap-3">

                    <div>
                        <label for="rows" class="mb-1.5 block text-sm font-bold text-slate-700">
                            Filas
                        </label>

                        <input type="number" name="rows" id="rows"
                               value="{{ old('rows', $wordsearch->rows ?? 10) }}"
                               min="2" max="30" required
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-bold text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100">
                    </div>

                    <div>
                        <label for="columns" class="mb-1.5 block text-sm font-bold text-slate-700">
                            Columnas
                        </label>

                        <input type="number" name="columns" id="columns"
                               value="{{ old('columns', $wordsearch->columns ?? 10) }}"
                               min="2" max="30" required
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-bold text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100">
                    </div>

                </div>

                {{-- Palabras --}}
                <div>
                    <p class="mb-2 text-sm font-bold text-slate-700">
                        Palabras de la actividad
                    </p>

                    <div id="words-container" class="space-y-3">

                        @forelse ($wordsearch->words ?? [] as $i => $word)
                            <article class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm" data-word-card>

                                <div class="flex flex-wrap items-end gap-3">

                                    <div class="min-w-0 flex-1">
                                        <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-400">
                                            Palabra
                                        </label>

                                        <input type="text" name="words[{{ $i }}][word]"
                                               value="{{ old("words.$i.word", $word->word) }}"
                                               maxlength="20" required
                                               placeholder="Ej. MATEMÁTICAS"
                                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm font-bold uppercase text-slate-800 outline-none transition placeholder:font-normal placeholder:normal-case placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100">
                                    </div>

                                    <div class="w-28">
                                        <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-400">
                                            Puntos
                                        </label>

                                        <input type="number" name="words[{{ $i }}][score]"
                                               value="{{ old("words.$i.score", $word->score) }}"
                                               min="1" required
                                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm font-bold text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100">
                                    </div>

                                    <div class="relative">
                                        <button type="button" data-confirm-toggle
                                                aria-expanded="false"
                                                aria-label="Eliminar esta palabra"
                                                class="flex h-11 w-11 items-center justify-center rounded-xl text-slate-400 transition hover:bg-red-50 hover:text-red-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500">
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </button>

                                        <div class="absolute right-0 top-12 z-10 hidden w-64 rounded-xl border border-red-200 bg-red-50 p-3 shadow-lg" data-confirm-panel>
                                            <p class="text-xs font-bold text-red-800">
                                                ¿Eliminar esta palabra?
                                            </p>

                                            <div class="mt-2 flex gap-2">
                                                <button type="button" data-confirm-remove
                                                        class="flex-1 rounded-lg bg-red-600 px-3 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-red-700">
                                                    Sí, eliminar
                                                </button>

                                                <button type="button" data-confirm-cancel
                                                        class="rounded-lg border border-red-200 bg-white px-3 py-2 text-xs font-bold text-slate-600 transition hover:bg-red-50">
                                                    Cancelar
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </article>
                        @empty
                            <article class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm" data-word-card>

                                <div class="flex flex-wrap items-end gap-3">

                                    <div class="min-w-0 flex-1">
                                        <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-400">
                                            Palabra
                                        </label>

                                        <input type="text" name="words[0][word]"
                                               value="{{ old('words.0.word') }}"
                                               maxlength="20" required
                                               placeholder="Ej. MATEMÁTICAS"
                                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm font-bold uppercase text-slate-800 outline-none transition placeholder:font-normal placeholder:normal-case placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100">
                                    </div>

                                    <div class="w-28">
                                        <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-400">
                                            Puntos
                                        </label>

                                        <input type="number" name="words[0][score]"
                                               value="{{ old('words.0.score', 10) }}"
                                               min="1" required
                                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm font-bold text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100">
                                    </div>

                                    <div class="relative">
                                        <button type="button" data-confirm-toggle disabled
                                                aria-expanded="false"
                                                aria-label="Eliminar esta palabra"
                                                class="flex h-11 w-11 items-center justify-center rounded-xl text-slate-300 transition hover:bg-red-50 hover:text-red-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:bg-transparent disabled:hover:text-slate-300">
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </button>

                                        <div class="absolute right-0 top-12 z-10 hidden w-64 rounded-xl border border-red-200 bg-red-50 p-3 shadow-lg" data-confirm-panel>
                                            <p class="text-xs font-bold text-red-800">
                                                ¿Eliminar esta palabra?
                                            </p>

                                            <div class="mt-2 flex gap-2">
                                                <button type="button" data-confirm-remove
                                                        class="flex-1 rounded-lg bg-red-600 px-3 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-red-700">
                                                    Sí, eliminar
                                                </button>

                                                <button type="button" data-confirm-cancel
                                                        class="rounded-lg border border-red-200 bg-white px-3 py-2 text-xs font-bold text-slate-600 transition hover:bg-red-50">
                                                    Cancelar
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </article>
                        @endforelse

                    </div>

                    <button type="button" id="add-word"
                            class="mt-3 inline-flex w-full items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-emerald-300 bg-emerald-50 px-5 py-3.5 text-sm font-bold text-emerald-700 transition hover:border-emerald-400 hover:bg-emerald-100 sm:w-auto">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Agregar palabra
                    </button>
                </div>

            </div>

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
                    {{ $editing ? 'Actualizar sopa' : 'Guardar sopa' }}
                </button>

            </section>

        </form>

        {{-- Vista previa (solo al editar) --}}
        @if ($editing && $wordsearch)
            <section class="mb-8">

                <h3 class="mb-3 flex flex-wrap items-center gap-2 text-lg font-bold text-slate-900">
                    Cuadrícula de la sopa
                    <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-bold text-emerald-700">
                        {{ $wordsearch->rows }} × {{ $wordsearch->columns }}
                    </span>
                </h3>

                <div class="mb-4 overflow-x-auto rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="preview-grid inline-grid gap-[3px] rounded-xl bg-slate-200 p-1"
                         style="grid-template-columns: repeat({{ $wordsearch->columns }}, 2rem);">
                        @foreach ($wordsearch->grid as $rowCells)
                            @foreach ($rowCells as $letter)
                                <div class="flex h-8 w-8 items-center justify-center rounded-md bg-white text-sm font-bold text-slate-700">
                                    {{ $letter }}
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                </div>

                <h3 class="mb-3 text-lg font-bold text-slate-900">
                    Palabras colocadas
                </h3>

                {{-- Tarjetas (móvil) --}}
                <div class="grid gap-3 sm:hidden">
                    @foreach ($wordsearch->words()->orderBy('word')->get() as $word)
                        <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                            <p class="text-sm font-bold uppercase text-slate-900">{{ $word->word }}</p>

                            <div class="mt-3 grid grid-cols-3 gap-2">
                                <div class="rounded-lg bg-slate-50 px-2 py-1.5 ring-1 ring-slate-200">
                                    <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Posición</p>
                                    <p class="mt-0.5 text-xs font-bold text-slate-700">{{ $word->row }}, {{ $word->column }}</p>
                                </div>

                                <div class="rounded-lg bg-slate-50 px-2 py-1.5 ring-1 ring-slate-200">
                                    <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Dirección</p>
                                    <p class="mt-0.5 text-xs font-bold text-slate-700">{{ app(\App\Services\WordsearchService::class)->directionLabel($word->direction) }}</p>
                                </div>

                                <div class="rounded-lg bg-slate-50 px-2 py-1.5 ring-1 ring-slate-200">
                                    <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Puntos</p>
                                    <p class="mt-0.5 text-xs font-bold text-emerald-700">{{ $word->score }}</p>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                {{-- Tabla (desktop) --}}
                <div class="hidden overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm sm:block">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="whitespace-nowrap px-5 py-3.5 font-bold">Palabra</th>
                                <th class="whitespace-nowrap px-5 py-3.5 font-bold">Fila</th>
                                <th class="whitespace-nowrap px-5 py-3.5 font-bold">Columna</th>
                                <th class="whitespace-nowrap px-5 py-3.5 font-bold">Dirección</th>
                                <th class="whitespace-nowrap px-5 py-3.5 font-bold">Puntos</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @foreach ($wordsearch->words()->orderBy('word')->get() as $word)
                                <tr class="transition hover:bg-emerald-50/40">
                                    <td class="whitespace-nowrap px-5 py-3.5 font-bold uppercase text-slate-900">{{ $word->word }}</td>
                                    <td class="whitespace-nowrap px-5 py-3.5 text-slate-600">{{ $word->row }}</td>
                                    <td class="whitespace-nowrap px-5 py-3.5 text-slate-600">{{ $word->column }}</td>
                                    <td class="whitespace-nowrap px-5 py-3.5">
                                        <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-700">
                                            {{ app(\App\Services\WordsearchService::class)->directionLabel($word->direction) }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-3.5 font-bold text-emerald-700">{{ $word->score }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </section>
        @endif

    </div>

@endsection

@push('scripts')
    <script>
        (function () {
            var container = document.getElementById('words-container');
            var countBadge = document.querySelector('[data-words-count]');
            var countLabel = document.querySelector('[data-words-label]');
            var addWord = document.getElementById('add-word');

            var trashPath = '<path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />';
            var plusPath = '<path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />';

            function cardCount() {
                return container.querySelectorAll('[data-word-card]').length;
            }

            function updateCount() {
                var count = cardCount();

                if (countBadge) {
                    countBadge.textContent = count;
                }

                if (countLabel) {
                    countLabel.textContent = count === 1 ? 'palabra' : 'palabras';
                }

                // La sopa necesita al menos una palabra: se desactiva el
                // borrado cuando solo queda una tarjeta.
                container.querySelectorAll('[data-word-card]').forEach(function (card) {
                    var toggle = card.querySelector('[data-confirm-toggle]');

                    if (toggle) {
                        toggle.disabled = count <= 1;
                    }
                });
            }

            function closePanels() {
                container.querySelectorAll('[data-confirm-panel]').forEach(function (panel) {
                    panel.classList.add('hidden');

                    var toggle = cardPanelToggle(panel);

                    if (toggle) {
                        toggle.setAttribute('aria-expanded', 'false');
                    }
                });
            }

            function cardPanelToggle(panel) {
                var card = panel.closest('[data-word-card]');

                return card ? card.querySelector('[data-confirm-toggle]') : null;
            }

            function createCard(index) {
                var card = document.createElement('article');

                card.className = 'rounded-2xl border border-slate-200 bg-white p-3 shadow-sm';
                card.setAttribute('data-word-card', '');

                card.innerHTML =
                    '<div class="flex flex-wrap items-end gap-3">' +
                        '<div class="min-w-0 flex-1">' +
                            '<label class="mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-400">Palabra</label>' +
                            '<input type="text" name="words[' + index + '][word]" maxlength="20" required placeholder="Ej. MATEMÁTICAS" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm font-bold uppercase text-slate-800 outline-none transition placeholder:font-normal placeholder:normal-case placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100">' +
                        '</div>' +
                        '<div class="w-28">' +
                            '<label class="mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-400">Puntos</label>' +
                            '<input type="number" name="words[' + index + '][score]" value="10" min="1" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm font-bold text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100">' +
                        '</div>' +
                        '<div class="relative">' +
                            '<button type="button" data-confirm-toggle aria-expanded="false" aria-label="Eliminar esta palabra" class="flex h-11 w-11 items-center justify-center rounded-xl text-slate-400 transition hover:bg-red-50 hover:text-red-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500">' +
                                '<svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">' + trashPath + '</svg>' +
                            '</button>' +
                            '<div class="absolute right-0 top-12 z-10 hidden w-64 rounded-xl border border-red-200 bg-red-50 p-3 shadow-lg" data-confirm-panel>' +
                                '<p class="text-xs font-bold text-red-800">¿Eliminar esta palabra?</p>' +
                                '<div class="mt-2 flex gap-2">' +
                                    '<button type="button" data-confirm-remove class="flex-1 rounded-lg bg-red-600 px-3 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-red-700">Sí, eliminar</button>' +
                                    '<button type="button" data-confirm-cancel class="rounded-lg border border-red-200 bg-white px-3 py-2 text-xs font-bold text-slate-600 transition hover:bg-red-50">Cancelar</button>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                    '</div>';

                return card;
            }

            function reindex() {
                container.querySelectorAll('[data-word-card]').forEach(function (card, i) {
                    var wordInput = card.querySelector('input[type="text"]');
                    var scoreInput = card.querySelector('input[type="number"]');

                    if (wordInput) {
                        wordInput.name = 'words[' + i + '][word]';
                    }

                    if (scoreInput) {
                        scoreInput.name = 'words[' + i + '][score]';
                    }
                });
            }

            if (addWord) {
                addWord.addEventListener('click', function () {
                    closePanels();

                    // Se usa un índice alto y único para no pisar filas
                    // existentes; reindex() normaliza al final.
                    var safeIndex = 1000 + Math.floor(Math.random() * 9000);

                    container.appendChild(createCard(safeIndex));
                    reindex();
                    updateCount();

                    var inputs = container.querySelectorAll('[data-word-card] input[type="text"]');
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
                    var card = remove.closest('[data-word-card]');

                    if (card && cardCount() > 1) {
                        card.remove();
                        reindex();
                        updateCount();
                    }
                }
            });

            updateCount();
        })();
    </script>
@endpush
