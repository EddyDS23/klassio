@extends('teacher.layouts.app')

@section('title', 'Editar clase')
@section('page-title', 'Editar clase')

@section('content')
        <div class="mx-auto max-w-4xl">

            {{-- Navegación --}}
            <div class="mb-6 flex flex-wrap items-center gap-x-5 gap-y-2">

                <a href="{{ route('teacher.classes.show', $class->id) }}"
                    class="klassio-back-button">

                    <span class="text-lg">←</span>
                    Volver a la clase
                </a>

                <a href="{{ route('teacher.classes.index') }}"
                    class="klassio-back-button">
                    Volver a mis clases
                </a>

            </div>


            {{-- Encabezado --}}
            <section class="mb-8 overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-700 via-teal-600 to-cyan-600 p-6 text-white shadow-xl shadow-emerald-200 sm:p-8">

                <div class="flex items-center justify-between gap-5">

                    <div class="min-w-0">

                        <div class="mb-3 inline-flex items-center gap-2 rounded-full bg-white/20 px-3 py-1 text-xs font-black uppercase tracking-wider backdrop-blur-sm">
                            <span>✏️</span>
                            Editar clase
                        </div>

                        <h2 class="break-words text-3xl font-black tracking-tight sm:text-4xl">
                            Editar clase
                        </h2>

                        <p class="mt-3 text-sm font-medium leading-6 text-emerald-50 sm:text-base">
                            Actualiza la información de tu clase para mantenerla organizada y clara para tus alumnos.
                        </p>

                    </div>

                    <div class="hidden h-24 w-24 shrink-0 items-center justify-center rounded-3xl bg-white/15 text-5xl shadow-inner backdrop-blur-sm sm:flex">
                        🏫
                    </div>

                </div>

            </section>


            {{-- Formulario --}}
            <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">

                <div class="mb-8">

                    <div class="mb-4 flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-2xl">
                            📚
                        </div>

                        <div>
                            <h3 class="text-2xl font-black text-slate-900">
                                {{ $class->name }}
                            </h3>

                            <p class="text-sm font-medium text-slate-500">
                                Información actual de la clase
                            </p>
                        </div>
                    </div>

                    <p class="text-sm font-medium leading-6 text-slate-500">
                        Modifica los campos que necesites y guarda los cambios cuando termines.
                    </p>

                </div>


                {{-- Errores --}}
                @if ($errors->any())

                    <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 p-5 text-rose-800">

                        <div class="flex items-center gap-3">
                            <span class="text-2xl">⚠️</span>

                            <div>
                                <h4 class="font-black">
                                    No se pudo actualizar la clase
                                </h4>

                                <p class="text-sm font-medium">
                                    Revisa los siguientes datos:
                                </p>
                            </div>
                        </div>

                        <ul class="mt-4 list-disc space-y-1 pl-6 text-sm font-medium">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>

                    </div>

                @endif


                <form action="{{ route('teacher.classes.update', $class->id) }}"
                    method="POST"
                    class="space-y-6">

                    @csrf
                    @method('PUT')


                    {{-- Nombre --}}
                    <div>

                        <label for="name"
                            class="mb-2 block text-sm font-black text-slate-700">
                            Nombre de la clase
                        </label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $class->name) }}"
                            placeholder="Ej. Matemáticas, Historia o Ciencias"
                            required
                            class="w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100"
                        >

                        <p class="mt-2 text-xs font-medium text-slate-500">
                            Utiliza un nombre que permita identificar fácilmente la clase.
                        </p>

                    </div>


                    {{-- Descripción --}}
                    <div>

                        <label for="description"
                            class="mb-2 block text-sm font-black text-slate-700">
                            Descripción
                        </label>

                        <textarea
                            id="description"
                            name="description"
                            rows="5"
                            placeholder="Describe brevemente de qué trata esta clase..."
                            class="w-full resize-y rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm font-medium leading-6 text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100"
                        >{{ old('description', $class->description) }}</textarea>

                        <p class="mt-2 text-xs font-medium text-slate-500">
                            Puedes explicar el objetivo, los temas o las actividades de la clase.
                        </p>

                    </div>


                    {{-- Unidades --}}
                    <div>

                        <label class="mb-2 block text-sm font-black text-slate-700">
                            Unidades de la clase
                        </label>

                        <div id="units-rows" class="space-y-3">
                            @forelse (old('units', $class->units->map(fn ($unit) => ['id' => $unit->id, 'name' => $unit->name])->all()) as $rowUnit)
                                <div class="unit-row flex items-center gap-3">
                                    @isset ($rowUnit['id'])
                                        <input type="hidden" name="units[][id]" value="{{ $rowUnit['id'] }}">
                                    @endisset

                                    <span
                                        class="w-28 shrink-0 text-xs font-black text-emerald-700"
                                    >Unidad {{ $loop->iteration }}</span>
                                    <input
                                        type="text"
                                        name="units[][name]"
                                        value="{{ collect($rowUnit)->get('name') }}"
                                        maxlength="150"
                                        placeholder="Nombre de la unidad (opcional)"
                                        class="flex-1 rounded-2xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm font-medium text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100"
                                    >
                                    <button
                                        type="button"
                                        data-remove-unit
                                        class="shrink-0 rounded-xl border border-slate-200 px-3 py-2 text-sm font-bold text-slate-500 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600"
                                    >✕</button>
                                </div>
                            @empty
                            @endforelse
                        </div>

                        <button
                            type="button"
                            id="add-unit"
                            class="mt-3 rounded-xl border border-dashed border-emerald-300 bg-emerald-50 px-4 py-2 text-sm font-bold text-emerald-700 transition hover:bg-emerald-100"
                        >
                            + Agregar unidad
                        </button>

                        <p class="mt-2 text-xs font-medium text-slate-500">
                            Puedes renombrar o agregar unidades. Solo puedes eliminar una unidad si no tiene actividades asociadas; si intentas eliminar una con actividades, te avisará al guardar. Máximo 8.
                        </p>

                    </div>


                    {{-- Acciones --}}
                    <div class="border-t border-slate-200 pt-6">

                        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">

                            <a href="{{ route('teacher.classes.show', $class->id) }}"
                                class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-black text-slate-600 transition hover:border-slate-400 hover:bg-slate-50">

                                Cancelar
                            </a>

                            <button type="submit"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-6 py-3 text-sm font-black text-white shadow-lg shadow-emerald-200 transition hover:bg-emerald-700 hover:shadow-emerald-300">

                                <span>✓</span>
                                Guardar cambios
                            </button>

                        </div>

                    </div>

                </form>

            </section>


            {{-- Nota inferior --}}
            <div class="mt-6 flex items-start gap-3 rounded-2xl border border-cyan-200 bg-cyan-50 p-4 text-cyan-800">

                <span class="text-xl">💡</span>

                <p class="text-sm font-medium leading-6">
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

        function unitRow() {
            const row = document.createElement('div');
            row.className = 'unit-row flex items-center gap-3';

            const label = document.createElement('span');
            label.className = 'w-28 shrink-0 text-xs font-black text-emerald-700';
            label.textContent = 'Unidad';

            const input = document.createElement('input');
            input.type = 'text';
            input.name = 'units[][name]';
            input.maxLength = 150;
            input.placeholder = 'Nombre de la unidad (opcional)';
            input.className = 'flex-1 rounded-2xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm font-medium text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100';

            const remove = document.createElement('button');
            remove.type = 'button';
            remove.setAttribute('data-remove-unit', '');
            remove.className = 'shrink-0 rounded-xl border border-slate-200 px-3 py-2 text-sm font-bold text-slate-500 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600';
            remove.textContent = '✕';

            row.append(label, input, remove);

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
