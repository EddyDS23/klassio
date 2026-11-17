@extends('teacher.layouts.app')

@section('title', 'Editar clase')
@section('page-title', 'Editar clase')

@section('page-actions')
    <a href="{{ route('teacher.classes.show', $class->id) }}"
       class="inline-flex self-start items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2">
        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
        </svg>
        Volver a la clase
    </a>
@endsection

@section('content')
    <div class="mx-auto max-w-4xl">

        {{-- Formulario --}}
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">

            <div class="mb-8">

                <div class="mb-4 flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.628 48.628 0 0 1 12 20.904a48.628 48.628 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                        </svg>
                    </div>

                    <div>
                        <h2 class="text-xl font-bold text-slate-900">
                            {{ $class->name }}
                        </h2>

                        <p class="text-sm text-slate-500">
                            Información actual de la clase
                        </p>
                    </div>
                </div>

                <p class="text-sm leading-6 text-slate-500">
                    Modifica los campos que necesites y guarda los cambios cuando termines.
                </p>

            </div>


            {{-- Errores --}}
            @if ($errors->any())

                <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 p-5 text-rose-800">

                    <div class="flex items-start gap-3">
                        <svg class="mt-0.5 h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>

                        <div>
                            <h3 class="font-bold">
                                No se pudo actualizar la clase
                            </h3>

                            <p class="text-sm">
                                Revisa los siguientes datos:
                            </p>
                        </div>
                    </div>

                    <ul class="mt-4 list-disc space-y-1 pl-6 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>

                </div>

            @endif


            <form action="{{ route('teacher.classes.update', $class->id) }}" method="POST" class="space-y-6">

                @csrf
                @method('PUT')


                {{-- Nombre --}}
                <div>

                    <label for="name" class="mb-2 block text-sm font-bold text-slate-700">
                        Nombre de la clase
                    </label>

                    <input type="text" id="name" name="name"
                           value="{{ old('name', $class->name) }}"
                           placeholder="Ej. Matemáticas, Historia o Ciencias"
                           required
                           class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100">

                    <p class="mt-2 text-xs text-slate-500">
                        Utiliza un nombre que permita identificar fácilmente la clase.
                    </p>

                </div>


                {{-- Descripción --}}
                <div>

                    <label for="description" class="mb-2 block text-sm font-bold text-slate-700">
                        Descripción
                    </label>

                    <textarea id="description" name="description" rows="5"
                              placeholder="Describe brevemente de qué trata esta clase..."
                              class="w-full resize-y rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium leading-6 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100">{{ old('description', $class->description) }}</textarea>

                    <p class="mt-2 text-xs text-slate-500">
                        Puedes explicar el objetivo, los temas o las actividades de la clase.
                    </p>

                </div>


                {{-- Unidades --}}
                <div>

                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Unidades de la clase
                    </label>

                    <div id="units-rows" class="space-y-3">
                        @forelse (old('units', $class->units->map(fn ($unit) => ['id' => $unit->id, 'name' => $unit->name])->all()) as $rowUnit)
                            <div class="unit-row flex flex-col gap-2 sm:flex-row sm:items-center sm:gap-3">
                                @isset ($rowUnit['id'])
                                    <input type="hidden" name="units[][id]" value="{{ $rowUnit['id'] }}">
                                @endisset

                                <span class="shrink-0 text-xs font-bold text-emerald-700 sm:w-28">Unidad {{ $loop->iteration }}</span>

                                <div class="flex items-center gap-2 sm:gap-3">
                                    <input type="text" name="units[][name]"
                                           value="{{ collect($rowUnit)->get('name') }}"
                                           maxlength="150"
                                           placeholder="Nombre de la unidad (opcional)"
                                           class="min-w-0 w-0 flex-1 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100">
                                    <button type="button" data-remove-unit
                                            class="shrink-0 rounded-xl border border-slate-200 px-3 py-2 text-sm font-bold text-slate-500 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600"
                                            aria-label="Quitar unidad">
                                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @empty
                        @endforelse
                    </div>

                    <button type="button" id="add-unit"
                            class="mt-3 inline-flex w-full items-center justify-center gap-2 rounded-xl border border-dashed border-emerald-300 bg-emerald-50 px-4 py-2 text-sm font-bold text-emerald-700 transition hover:bg-emerald-100 sm:w-auto">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Agregar unidad
                    </button>

                    <p class="mt-2 text-xs text-slate-500">
                        Puedes renombrar o agregar unidades. Solo puedes eliminar una unidad si no tiene actividades asociadas; si intentas eliminar una con actividades, te avisará al guardar. Máximo 8.
                    </p>

                </div>


                {{-- Acciones --}}
                <div class="border-t border-slate-200 pt-6">

                    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">

                        <a href="{{ route('teacher.classes.show', $class->id) }}"
                           class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-600 transition hover:bg-slate-50 hover:text-slate-900">
                            Cancelar
                        </a>

                        <button type="submit"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-700 px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2">
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                            Guardar cambios
                        </button>

                    </div>

                </div>

            </form>

        </section>


        {{-- Nota inferior --}}
        <div class="mt-6 flex items-start gap-3 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-amber-800">

            <svg class="mt-0.5 h-5 w-5 shrink-0 text-amber-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 0 4.51-4.32M12 18h.008v.008H12V18Zm-3.6-1.29a12.04 12.04 0 0 1-3.9-4.32M5.25 9.75a6.035 6.035 0 0 1 1.5-.17m0 0a6.01 6.01 0 0 0 1.5-.17m9.75 4.67a6.01 6.01 0 0 0-1.5-.17m0 0a6.01 6.01 0 0 1-1.5-.17M9.75 5.995a6.01 6.01 0 0 1 1.5-.17m0 0a6.01 6.01 0 0 0 1.5-.17m0 0a6.01 6.01 0 0 1 1.5-.17m0 0a6.01 6.01 0 0 0 1.5-.17m0 0V4.5a2.25 2.25 0 0 0-4.5 0v.125" />
            </svg>

            <p class="text-sm leading-6">
                Los cambios se aplicarán a la información de esta clase y estarán disponibles para tus alumnos.
            </p>

        </div>

    </div>
@endsection

@push('scripts')
    <script>
        const unitsRows = document.getElementById('units-rows');
        const addUnit = document.getElementById('add-unit');
        const MAX_UNITS = 8;
        const REMOVE_ICON = '<svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>';

        function unitRow() {
            const row = document.createElement('div');
            row.className = 'unit-row flex flex-col gap-2 sm:flex-row sm:items-center sm:gap-3';

            const label = document.createElement('span');
            label.className = 'shrink-0 text-xs font-bold text-emerald-700 sm:w-28';
            label.textContent = 'Unidad';

            const fieldWrap = document.createElement('div');
            fieldWrap.className = 'flex items-center gap-2 sm:gap-3';

            const input = document.createElement('input');
            input.type = 'text';
            input.name = 'units[][name]';
            input.maxLength = 150;
            input.placeholder = 'Nombre de la unidad (opcional)';
            input.className = 'min-w-0 w-0 flex-1 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100';

            const remove = document.createElement('button');
            remove.type = 'button';
            remove.setAttribute('data-remove-unit', '');
            remove.setAttribute('aria-label', 'Quitar unidad');
            remove.className = 'shrink-0 rounded-xl border border-slate-200 px-3 py-2 text-sm font-bold text-slate-500 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600';
            remove.innerHTML = REMOVE_ICON;

            fieldWrap.append(input, remove);
            row.append(label, fieldWrap);

            return row;
        }

        function renumberUnits() {
            unitsRows.querySelectorAll('.unit-row').forEach((row, index) => {
                row.querySelector('span').textContent = 'Unidad ' + (index + 1);
            });

            addUnit.disabled = unitsRows.querySelectorAll('.unit-row').length >= MAX_UNITS;
        }

        addUnit?.addEventListener('click', () => {
            if (unitsRows.querySelectorAll('.unit-row').length >= MAX_UNITS) return;

            unitsRows.appendChild(unitRow());
            renumberUnits();
        });

        unitsRows?.addEventListener('click', (event) => {
            const button = event.target.closest('[data-remove-unit]');

            if (!button) return;

            button.closest('.unit-row').remove();
            renumberUnits();
        });

        renumberUnits();
    </script>
@endpush
