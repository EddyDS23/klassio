@extends('teacher.layouts.app')

@section('title', 'Calificación general · '.e($class->name))

@section('page-title', 'Calificaciones')

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

        {{-- Título --}}
        <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

            <div class="flex flex-wrap items-center gap-3">

                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 ring-1 ring-amber-100">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                    </svg>
                </div>

                <div>
                    <h2 class="text-xl font-bold tracking-tight text-slate-900">
                        Calificación general
                    </h2>

                    <p class="text-sm font-semibold text-slate-500">
                        {{ $class->name }}
                    </p>
                </div>

                <span class="ml-auto rounded-full bg-slate-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-slate-600">
                    Periodo {{ $class->period() }}
                </span>

            </div>

            <div class="mt-4 flex flex-wrap gap-x-6 gap-y-2 text-xs font-semibold text-slate-500">

                <span>
                    Cada unidad muestra el promedio de sus actividades.
                </span>

                <span>
                    La calificación final es el promedio de las unidades con nota.
                </span>

            </div>

        </div>


        {{-- Tabla de calificaciones --}}
        @php
$gradeClass = function ($score) {
    if ($score === null) {
        return 'bg-slate-100 text-slate-500';
    }

    if ($score >= 70) {
        return 'bg-emerald-100 text-emerald-700';
    }

    if ($score >= 50) {
        return 'bg-amber-100 text-amber-700';
    }

    return 'bg-rose-100 text-rose-700';
};

$formatScore = fn ($score) => $score === null
    ? '—'
    : number_format($score, 1, '.', '');
@endphp

        @if (empty($units))
            <div class="rounded-2xl border border-slate-200 bg-white p-10 text-center shadow-sm">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-400">
                    <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </div>

                <h3 class="mt-3 text-lg font-bold text-slate-900">
                    Aún no hay actividades publicadas
                </h3>

                <p class="mt-1 text-sm font-medium text-slate-500">
                    Publica actividades con unidades para ver la calificación general de la clase.
                </p>
            </div>
        @elseif (empty($studentRows))
            <div class="rounded-2xl border border-slate-200 bg-white p-10 text-center shadow-sm">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-400">
                    <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.628 48.628 0 0 1 12 20.904a48.628 48.628 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                    </svg>
                </div>

                <h3 class="mt-3 text-lg font-bold text-slate-900">
                    No hay alumnos inscritos
                </h3>

                <p class="mt-1 text-sm font-medium text-slate-500">
                    Comparte el código de la clase para que tus estudiantes se inscriban.
                </p>
            </div>
        @else

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                {{-- Barra de herramientas --}}
                <div
                    class="flex flex-col gap-4 border-b border-slate-100 px-4 py-4 sm:px-6 sm:py-5 lg:flex-row lg:items-center lg:justify-between">

                    <div class="relative w-full lg:max-w-xs">

                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </span>

                        <input type="text" id="grading-search" autocomplete="off"
                            placeholder="Buscar alumno por nombre o email…"
                            class="w-full rounded-xl border border-slate-300 bg-white py-2.5 pl-9 pr-3 text-sm font-semibold text-slate-800 shadow-sm placeholder:font-medium placeholder:text-slate-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100" />
                    </div>


                    <div class="flex flex-wrap items-center gap-2">

                        <span class="mr-1 text-xs font-bold uppercase tracking-wide text-slate-400">
                            Ordenar
                        </span>

                        <button type="button"
                            class="grading-sort rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-bold text-slate-600 transition hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-700"
                            data-sort="final_desc">
                            Final ↓
                        </button>

                        <button type="button"
                            class="grading-sort rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-bold text-slate-600 transition hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-700"
                            data-sort="final_asc">
                            Final ↑
                        </button>

                        <button type="button"
                            class="grading-sort rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-bold text-slate-600 transition hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-700"
                            data-sort="name">
                            A → Z
                        </button>

                    </div>

                </div>


                <div class="overflow-x-auto">

                    <table class="min-w-full">

                        <thead class="bg-slate-50">

                            <tr>

                                <th scope="col"
                                    class="min-w-0 px-4 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-500 sm:min-w-[200px] sm:px-6">
                                    Alumno
                                </th>


                                @foreach ($units as $group)
                                    <th scope="col"
                                        class="hidden min-w-[110px] px-4 py-4 text-center text-xs font-bold uppercase tracking-wide text-slate-500 sm:table-cell">

                                        <span class="block">
                                            @if ($group['number'] !== null)
                                                Unidad {{ $group['number'] }}
                                            @else
                                                {{ $group['name'] }}
                                            @endif
                                        </span>

                                        <span
                                            class="mt-0.5 block truncate text-[11px] font-semibold normal-case text-slate-400"
                                            title="{{ $group['name'] }}">
                                            {{ $group['name'] }}
                                        </span>
                                    </th>
                                @endforeach


                                <th scope="col"
                                    class="px-4 py-4 text-center text-xs font-bold uppercase tracking-wide text-slate-500 sm:px-6">
                                    Calificación
                                    final
                                </th>
                            </tr>

                        </thead>

                        <tbody id="grading-tbody" class="divide-y divide-slate-100">

                            @foreach ($studentRows as $row)

                                @php
$final = $row['final'];
@endphp

                                <tr data-student="{{ $row['student']->id }}"
                                    data-student-name="{{ $row['student']->name }}"
                                    data-student-email="{{ $row['student']->email }}"
                                    data-final="{{ $final ?? '' }}"
                                    class="transition hover:bg-slate-50/60">

                                    <td class="px-4 py-4 sm:px-6">

                                        <button type="button"
                                            class="grading-toggle inline-flex items-start gap-2 text-left"
                                            aria-expanded="false"
                                            aria-controls="grading-detail-{{ $row['student']->id }}"
                                            aria-label="Ver detalle de {{ $row['student']->name }}">

                                            <span
                                                class="grading-caret mt-0.5 text-slate-400 transition">
                                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                                </svg>
                                            </span>

                                            <span>
                                                <span class="block font-bold text-slate-900">
                                                    {{ $row['student']->name }}
                                                </span>

                                                <span class="mt-0.5 block text-xs font-medium text-slate-400">
                                                    {{ $row['student']->email }}
                                                </span>
                                            </span>
                                        </button>
                                    </td>


                                    @foreach ($units as $group)
                                        @php
$unitScore = $row['unitScores'][$group['key']] ?? null;
@endphp

                                        <td class="hidden px-4 py-4 text-center sm:table-cell">
                                            <span
                                                class="inline-flex rounded-xl px-3 py-1.5 text-sm font-bold {{ $gradeClass($unitScore) }}">
                                                {{ $formatScore($unitScore) }}
                                            </span>
                                        </td>
                                    @endforeach


                                    <td class="px-4 py-4 text-center sm:px-6">
                                        <span
                                            class="inline-flex rounded-2xl px-4 py-2 text-base font-bold shadow-sm {{ $gradeClass($final) }}">
                                            {{ $formatScore($final) }}
                                        </span>
                                    </td>

                                </tr>


                                {{-- Fila de detalle (actividades por unidad) --}}
                                <tr data-detail="{{ $row['student']->id }}"
                                    id="grading-detail-{{ $row['student']->id }}"
                                    data-active-for="{{ $row['student']->id }}"
                                    class="hidden bg-slate-50/70">

                                    <td colspan="{{ count($units) + 2 }}" class="px-4 pb-6 sm:px-6">

                                        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">

                                            @foreach ($units as $group)

                                                <div class="rounded-2xl border border-slate-200 bg-white p-4">

                                                    <div class="flex items-center justify-between gap-2">

                                                        <p class="min-w-0 truncate text-xs font-bold uppercase tracking-wide text-slate-500"
                                                            title="{{ $group['name'] }}">

                                                            @if ($group['number'] !== null)
                                                                Unidad {{ $group['number'] }} · {{ $group['name'] }}
                                                            @else
                                                                {{ $group['name'] }}
                                                            @endif
                                                        </p>

                                                        <span
                                                            class="shrink-0 rounded-full px-2.5 py-1 text-[11px] font-bold {{ $gradeClass($row['unitScores'][$group['key']] ?? null) }}">
                                                            {{ $formatScore($row['unitScores'][$group['key']] ?? null) }}
                                                        </span>

                                                    </div>


                                                    <div class="mt-3 space-y-1.5">

                                                        @if (empty($group['activities']))
                                                            <p class="text-xs font-medium text-slate-400">
                                                                Sin actividades publicadas.
                                                            </p>
                                                        @else

                                                            @foreach ($group['activities'] as $activity)

                                                                @php
$scoreRaw = $row['activityRaw'][$activity['id']] ?? null;
@endphp

                                                                <div
                                                                    class="flex items-center justify-between gap-2 rounded-xl bg-slate-50 px-3 py-2">

                                                                    <span
                                                                        class="min-w-0 flex-1 truncate text-xs font-semibold text-slate-700"
                                                                        title="{{ $activity['title'] }}">
                                                                        {{ $activity['title'] }}
                                                                    </span>

                                                                    @if ($scoreRaw === null)
                                                                        <span
                                                                            class="shrink-0 text-[11px] font-bold uppercase tracking-wide text-slate-400">
                                                                            Sin puntaje
                                                                        </span>
                                                                    @else
                                                                        <span
                                                                            class="shrink-0 text-[11px] font-bold text-emerald-700">
                                                                            {{ $scoreRaw }} / {{ $activity['max_score'] }}
                                                                        </span>
                                                                    @endif

                                                                </div>

                                                            @endforeach

                                                        @endif

                                                    </div>

                                                </div>

                                            @endforeach

                                        </div>

                                    </td>
                                </tr>

                            @endforeach


                            {{-- Fila "sin resultados" (buscador) --}}
                            <tr id="grading-empty" class="hidden">
                                <td colspan="{{ count($units) + 2 }}" class="px-6 py-8 text-center">
                                    <p class="text-sm font-bold text-slate-500">
                                        No se encontraron alumnos
                                    </p>

                                    <p class="mt-1 text-xs font-medium text-slate-400">
                                        Prueba con otro nombre o correo.
                                    </p>
                                </td>
                            </tr>

                        </tbody>

                    </table>

                </div>


                {{-- Paginación --}}
                <div
                    class="flex flex-col items-center justify-between gap-3 border-t border-slate-100 px-4 py-4 sm:flex-row sm:px-6">

                    <p id="grading-count-label" class="text-xs font-bold text-slate-500">

                    </p>


                    <div class="flex items-center gap-3">

                        <button type="button" id="grading-prev"
                            class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-bold text-slate-600 transition hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-700 disabled:cursor-not-allowed disabled:opacity-40">
                            ← Anterior
                        </button>

                        <span id="grading-page-label" class="text-xs font-bold text-slate-500">

                        </span>

                        <button type="button" id="grading-next"
                            class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-bold text-slate-600 transition hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-700 disabled:cursor-not-allowed disabled:opacity-40">
                            Siguiente →
                        </button>

                    </div>

                </div>

            </div>

        @endif


        {{-- Nota de cálculo --}}
        <p class="mt-8 text-center text-xs font-semibold text-slate-400">
            La nota de cada actividad es la última jugada terminada del alumno o equipo
            (completada, abandonada o expirada); la actividad vencida sin jugada cuenta como 0.
        </p>

@endsection

@push('scripts')
    @if (! empty($units) && ! empty($studentRows))
        {{-- Buscador, orden y paginación de la tabla (del lado del cliente) --}}
        <script>
            (function () {
                var body = document.getElementById('grading-tbody');
                var searchInput = document.getElementById('grading-search');
                var sortButtons = Array.prototype.slice.call(document.querySelectorAll('.grading-sort'));
                var prevButton = document.getElementById('grading-prev');
                var nextButton = document.getElementById('grading-next');
                var pageLabel = document.getElementById('grading-page-label');
                var countLabel = document.getElementById('grading-count-label');
                var emptyRow = document.getElementById('grading-empty');

                var leadRows = Array.prototype.slice.call(body.querySelectorAll('tr[data-student]'));
                var perPage = 10;

                var state = { query: '', order: 'final_desc', page: 1 };

                function numericFinal(row) {
                    var value = parseFloat(row.getAttribute('data-final'));
                    return isNaN(value) ? -1 : value;
                }

                function matches(studentRow) {
                    if (state.query === '') {
                        return true;
                    }

                    var haystack = (
                        studentRow.getAttribute('data-student-name') + ' ' +
                        studentRow.getAttribute('data-student-email')
                    ).toLowerCase();

                    return haystack.indexOf(state.query) !== -1;
                }

                function visibleRows() {
                    var rows = leadRows.filter(matches);
                    var byName = function (a, b) {
                        return a.getAttribute('data-student-name')
                            .localeCompare(b.getAttribute('data-student-name'), 'es', { sensitivity: 'base' });
                    };

                    if (state.order === 'name') {
                        rows = rows.sort(byName);
                    } else {
                        rows = rows.sort(function (a, b) {
                            var diff = numericFinal(b) - numericFinal(a);

                            if (diff !== 0) {
                                return diff;
                            }

                            // Empate: desempata por nombre para un orden estable.
                            return byName(a, b);
                        });

                        if (state.order === 'final_asc') {
                            rows = rows.reverse();
                        }
                    }

                    return rows;
                }

                function collapseDetail(studentRow) {
                    var detailRow = body.querySelector(
                        'tr[data-detail="' + studentRow.getAttribute('data-student') + '"]'
                    );

                    if (detailRow) {
                        detailRow.classList.add('hidden');
                    }

                    var toggle = studentRow.querySelector('.grading-toggle');

                    if (toggle) {
                        toggle.setAttribute('aria-expanded', 'false');
                    }

                    var caret = studentRow.querySelector('.grading-caret');

                    if (caret) {
                        caret.classList.remove('rotate-90', 'text-emerald-600');
                    }
                }

                function updateSortButtons() {
                    sortButtons.forEach(function (button) {
                        var active = button.getAttribute('data-sort') === state.order;

                        button.classList.toggle('border-emerald-500', active);
                        button.classList.toggle('bg-emerald-50', active);
                        button.classList.toggle('text-emerald-700', active);
                    });
                }

                function render() {
                    var rows = visibleRows();
                    var totalPages = Math.max(1, Math.ceil(rows.length / perPage));

                    if (state.page > totalPages) {
                        state.page = totalPages;
                    }

                    var pageRows = rows.slice((state.page - 1) * perPage, state.page * perPage);
                    var pageSet = new Set(pageRows);

                    leadRows.forEach(function (studentRow) {
                        studentRow.classList.toggle('hidden', ! pageSet.has(studentRow));
                        collapseDetail(studentRow);
                    });

                    // Reordenar físicamente las filas en el tbody según el orden
                    // activo; cada detalle viaja pegado a su alumno.
                    rows.forEach(function (studentRow) {
                        var detailRow = body.querySelector(
                            'tr[data-detail="' + studentRow.getAttribute('data-student') + '"]'
                        );

                        body.insertBefore(studentRow, emptyRow);

                        if (detailRow) {
                            body.insertBefore(detailRow, emptyRow);
                        }
                    });

                    body.appendChild(emptyRow);

                    emptyRow.classList.toggle('hidden', rows.length !== 0);

                    prevButton.disabled = state.page <= 1;
                    nextButton.disabled = state.page >= totalPages;

                    pageLabel.textContent = 'Página ' + state.page + ' de ' + totalPages;
                    countLabel.textContent = 'Mostrando ' + pageRows.length + ' de ' + rows.length + ' alumnos';

                    updateSortButtons();
                }

                searchInput.addEventListener('input', function () {
                    state.query = searchInput.value.trim().toLowerCase();
                    state.page = 1;
                    render();
                });

                sortButtons.forEach(function (button) {
                    button.addEventListener('click', function () {
                        state.order = button.getAttribute('data-sort');
                        state.page = 1;
                        render();
                    });
                });

                prevButton.addEventListener('click', function () {
                    state.page = Math.max(1, state.page - 1);
                    render();
                });

                nextButton.addEventListener('click', function () {
                    state.page = state.page + 1;
                    render();
                });

                body.addEventListener('click', function (event) {
                    var toggle = event.target.closest('.grading-toggle');

                    if (! toggle) {
                        return;
                    }

                    var studentRow = toggle.closest('tr[data-student]');
                    var detailRow = body.querySelector(
                        'tr[data-detail="' + studentRow.getAttribute('data-student') + '"]'
                    );

                    if (! detailRow) {
                        return;
                    }

                    var expanded = toggle.getAttribute('aria-expanded') === 'true';
                    var caret = studentRow.querySelector('.grading-caret');

                    if (expanded) {
                        detailRow.classList.add('hidden');
                        caret.classList.remove('rotate-90', 'text-emerald-600');
                    } else {
                        detailRow.classList.remove('hidden');
                        caret.classList.add('rotate-90', 'text-emerald-600');
                    }

                    toggle.setAttribute('aria-expanded', expanded ? 'false' : 'true');
                });

                render();
            })();
        </script>
    @endif
@endpush
