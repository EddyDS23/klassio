<article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm" data-item-card>

    <div class="relative flex items-start justify-between gap-3">

        <span class="mt-0.5 rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-700" data-item-badge>
            Casillero {{ $i + 1 }}
        </span>

        <button type="button" data-confirm-toggle
                aria-expanded="false"
                aria-label="Eliminar este casillero"
                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl text-slate-400 transition hover:bg-red-50 hover:text-red-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500">
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
            </svg>
        </button>

        <div class="absolute right-0 top-12 z-10 hidden w-64 rounded-xl border border-red-200 bg-red-50 p-3 shadow-lg" data-confirm-panel>
            <p class="text-xs font-bold text-red-800">
                ¿Eliminar este casillero?
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

    <div class="mt-3">
        <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-400">
            Pregunta
        </label>

        <input type="text" name="items[{{ $i }}][question]"
               value="{{ $item ? old("items.$i.question", $item->question) : old('items.0.question') }}"
               placeholder="¿Qué es HTTP?" required
               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100">
    </div>

    <div class="mt-3 grid grid-cols-2 gap-3">

        <div>
            <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-400">
                Opción A
            </label>

            <input type="text" name="items[{{ $i }}][option_a]"
                   value="{{ $item ? old("items.$i.option_a", $item->option_a) : old('items.0.option_a') }}"
                   placeholder="Opción A" required
                   class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100">
        </div>

        <div>
            <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-400">
                Opción B
            </label>

            <input type="text" name="items[{{ $i }}][option_b]"
                   value="{{ $item ? old("items.$i.option_b", $item->option_b) : old('items.0.option_b') }}"
                   placeholder="Opción B" required
                   class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100">
        </div>

        <div>
            <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-400">
                Opción C
            </label>

            <input type="text" name="items[{{ $i }}][option_c]"
                   value="{{ $item ? old("items.$i.option_c", $item->option_c) : old('items.0.option_c') }}"
                   placeholder="Opción C" required
                   class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100">
        </div>

        <div>
            <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-400">
                Opción D
            </label>

            <input type="text" name="items[{{ $i }}][option_d]"
                   value="{{ $item ? old("items.$i.option_d", $item->option_d) : old('items.0.option_d') }}"
                   placeholder="Opción D" required
                   class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100">
        </div>

    </div>

    <div class="mt-3 grid grid-cols-2 gap-3">

        <div>
            <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-400">
                Correcta
            </label>

            <select name="items[{{ $i }}][correct_option]" required
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm font-bold text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100">
                @foreach (['a', 'b', 'c', 'd'] as $key)
                    <option value="{{ $key }}" @selected(($item ? $item->correct_option : 'a') === $key)>
                        {{ strtoupper($key) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-400">
                Puntos
            </label>

            <input type="number" name="items[{{ $i }}][points]"
                   value="{{ $item ? old("items.$i.points", $item->points) : old('items.0.points', 10) }}"
                   min="1" required
                   class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm font-bold text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100">
        </div>

    </div>

</article>
