<article class="question-block rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5"
         data-question-card id="question-block-{{ $qi }}">

    <div class="relative flex items-start justify-between gap-3 pb-4">

        <div class="flex items-center gap-3">

            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-sm font-bold text-emerald-700 ring-1 ring-emerald-100">
                <span data-q-number>{{ $qi + 1 }}</span>
            </div>

            <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-700">
                Pregunta
            </span>
        </div>

        <button type="button" data-confirm-toggle
                aria-expanded="false"
                aria-label="Eliminar esta pregunta"
                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl text-slate-400 transition hover:bg-red-50 hover:text-red-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500">
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
            </svg>
        </button>

        <div class="absolute right-0 top-12 z-10 hidden w-64 rounded-xl border border-red-200 bg-red-50 p-3 shadow-lg" data-confirm-panel>
            <p class="text-xs font-bold text-red-800">
                ¿Eliminar esta pregunta?
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

    {{-- Enunciado --}}
    <div class="mb-4">
        <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-400">
            Enunciado
        </label>

        <input type="text" name="questions[{{ $qi }}][question]"
               value="{{ $question ? old("questions.$qi.question", $question->question) : old("questions.$qi.question") }}"
               placeholder="¿Cuál es el ORM de Laravel?" maxlength="255" required
               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100">
    </div>

    {{-- Tiempo y puntaje --}}
    <div class="mb-4 grid grid-cols-2 gap-3">

        <div>
            <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-400">
                Tiempo límite (segundos)
            </label>

            <input type="number" name="questions[{{ $qi }}][time_limit]"
                   value="{{ $question ? old("questions.$qi.time_limit", $question->time_limit) : old("questions.$qi.time_limit", 20) }}"
                   min="5" max="120" required
                   class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm font-bold text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100">
        </div>

        <div>
            <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-400">
                Puntaje
            </label>

            <input type="number" name="questions[{{ $qi }}][score]"
                   value="{{ $question ? old("questions.$qi.score", $question->score) : old("questions.$qi.score", 100) }}"
                   min="1" max="1000" required
                   class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm font-bold text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100">
        </div>

    </div>

    {{-- Opciones --}}
    <div class="rounded-2xl bg-slate-50 p-3 sm:p-4">

        <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
            <h4 class="text-sm font-bold text-slate-900">
                Opciones de respuesta
            </h4>

            <p class="text-[11px] font-bold text-slate-500">
                Marca la correcta · de 2 a 4 opciones
            </p>
        </div>

        <div class="options-grid grid gap-3 sm:grid-cols-2" id="options-{{ $qi }}">

            @php
            $cardOptions = $question ? $question->options->all() : [null, null];
        @endphp

        @foreach ($cardOptions as $oi => $option)
                <div class="option-row flex items-center gap-2.5 rounded-xl border px-3 py-2.5 transition {{ $option && $option->is_correct ? 'border-emerald-400 bg-emerald-50' : 'border-slate-200 bg-white' }}">

                    <input type="radio" name="questions[{{ $qi }}][correct]"
                           value="{{ $oi }}"
                           {{ $option && $option->is_correct ? 'checked' : '' }}
                           class="h-4 w-4 shrink-0 cursor-pointer accent-emerald-600">

                    <input type="hidden" name="questions[{{ $qi }}][options][{{ $oi }}][is_correct]"
                           value="{{ $option && $option->is_correct ? '1' : '0' }}"
                           class="is-correct-input">

                    <input type="text" name="questions[{{ $qi }}][options][{{ $oi }}][text]"
                           value="{{ $option ? old("questions.$qi.options.$oi.text", $option->text) : old("questions.$qi.options.$oi.text") }}"
                           placeholder="Opción {{ $oi + 1 }}" maxlength="255" required
                           class="min-w-0 w-0 flex-1 rounded-lg border border-transparent bg-transparent px-2 py-1.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-emerald-300 focus:bg-white">

                    <button type="button" data-option-remove
                            title="Eliminar opción"
                            aria-label="Eliminar la opción {{ $oi + 1 }}"
                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-slate-400 transition hover:bg-red-50 hover:text-red-600">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>

                </div>
            @endforeach

        </div>

        <button type="button" data-option-add
                class="mt-3 inline-flex items-center gap-2 rounded-xl border border-dashed border-emerald-300 bg-white px-4 py-2 text-sm font-bold text-emerald-700 transition hover:bg-emerald-50">
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Agregar opción
        </button>

    </div>

</article>
