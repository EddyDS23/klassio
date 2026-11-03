<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Calificación general · {{ $class->name }} | Klassio</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-800">

    {{-- Barra superior --}}
    <header class="sticky top-0 z-30 border-b border-slate-200 bg-white shadow-sm">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">

            <a href="{{ route('teacher.dashboard') }}" class="flex items-center gap-3">

                <div
                    class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-600 text-xl font-black text-white shadow-lg shadow-emerald-200">
                    K
                </div>

                <div>
                    <h1 class="text-xl font-black tracking-tight text-slate-900">
                        Klassio
                    </h1>

                    <p class="text-xs font-medium text-slate-500">
                        Panel del profesor
                    </p>
                </div>
            </a>

            <div class="flex items-center gap-3">

                <span class="hidden text-sm font-semibold text-slate-600 sm:block">
                    {{ auth()->user()->name }}
                </span>

                <form action="{{ url('/logout') }}" method="POST">
                    @csrf

                    <button type="submit"
                        class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-600 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600">
                        Cerrar sesión
                    </button>
                </form>

            </div>

        </div>
    </header>


    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- Navegación --}}
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">

            <a href="{{ route('teacher.classes.show', $class->id) }}"
                class="inline-flex items-center gap-2 text-sm font-bold text-emerald-700 transition hover:text-emerald-900">

                <span class="text-lg">←</span>
                Volver a {{ $class->name }}
            </a>

            <span
                class="rounded-full bg-white px-4 py-1.5 text-xs font-black uppercase tracking-wide text-slate-500 shadow-sm ring-1 ring-slate-200">
                Periodo {{ $class->period() }}
            </span>

        </div>


        {{-- Título --}}
        <div class="mb-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">

            <div class="flex flex-wrap items-center gap-3">

                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-100 text-2xl">
                    ⭐
                </div>

                <div>
                    <h2 class="text-2xl font-black tracking-tight text-slate-900">
                        Calificación general
                    </h2>

                    <p class="text-sm font-semibold text-slate-500">
                        {{ $class->name }}
                    </p>
                </div>

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
            <div class="rounded-3xl border border-slate-200 bg-white p-10 text-center shadow-sm">
                <p class="text-4xl">🗂️</p>

                <h3 class="mt-3 text-lg font-black text-slate-900">
                    Aún no hay actividades publicadas
                </h3>

                <p class="mt-1 text-sm font-medium text-slate-500">
                    Publica actividades con unidades para ver la calificación general de la clase.
                </p>
            </div>
        @elseif (empty($studentRows))
            <div class="rounded-3xl border border-slate-200 bg-white p-10 text-center shadow-sm">
                <p class="text-4xl">🎓</p>

                <h3 class="mt-3 text-lg font-black text-slate-900">
                    No hay alumnos inscritos
                </h3>

                <p class="mt-1 text-sm font-medium text-slate-500">
                    Comparte el código de la clase para que tus estudiantes se inscriban.
                </p>
            </div>
        @else

            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

                {{-- Barra de herramientas --}}
                <div
                    class="flex flex-col gap-4 border-b border-slate-100 px-6 py-5 lg:flex-row lg:items-center lg:justify-between">

                    <div class="relative w-full lg:max-w-xs">

                        <span
                            class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-sm text-slate-400">
                            🔍
                        </span>

                        <input type="text" id="grading-search" autocomplete="off"
                            placeholder="Buscar alumno por nombre o email…"
                            class="w-full rounded-xl border border-slate-300 bg-white py-2.5 pl-9 pr-3 text-sm font-semibold text-slate-800 shadow-sm placeholder:font-medium placeholder:text-slate-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100" />
                    </div>


                    <div class="flex flex-wrap items-center gap-2">

                        <span class="mr-1 text-xs font-black uppercase tracking-wide text-slate-400">
                            Ordenar
                        </span>

                        <button type="button"
                            class="grading-sort rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-black text-slate-600 transition hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-700"
                            data-sort="final_desc">
                            Final ↓
                        </button>

                        <button type="button"
                            class="grading-sort rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-black text-slate-600 transition hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-700"
                            data-sort="final_asc">
                            Final ↑
                        </button>

                        <button type="button"
                            class="grading-sort rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-black text-slate-600 transition hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-700"
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
                                    class="min-w-[200px] px-6 py-4 text-left text-xs font-black uppercase tracking-wide text-slate-500">
                                    Alumno
                                </th>


                                @foreach ($units as $group)
                                    <th scope="col"
                                        class="min-w-[110px] px-4 py-4 text-center text-xs font-black uppercase tracking-wide text-slate-500">

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
                                    class="min-w-[130px] px-6 py-4 text-center text-xs font-black uppercase tracking-wide text-slate-500">
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
                                    aria-expanded="false"
                                    class="transition hover:bg-slate-50/60">

                                    <td class="px-6 py-4">

                                        <button type="button"
                                            class="grading-toggle inline-flex items-start gap-2 text-left"
                                            aria-label="Ver detalle de {{ $row['student']->name }}">

                                            <span
                                                class="grading-caret mt-0.5 text-xs text-slate-400 transition">▸</span>

                                            <span>
                                                <span class="block font-black text-slate-900">
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

                                        <td class="px-4 py-4 text-center">
                                            <span
                                                class="inline-flex rounded-xl px-3 py-1.5 text-sm font-black {{ $gradeClass($unitScore) }}">
                                                {{ $formatScore($unitScore) }}
                                            </span>
                                        </td>
                                    @endforeach


                                    <td class="px-6 py-4 text-center">
                                        <span
                                            class="inline-flex rounded-2xl px-4 py-2 text-base font-black shadow-sm {{ $gradeClass($final) }}">
                                            {{ $formatScore($final) }}
                                        </span>
                                    </td>

                                </tr>


                                {{-- Fila de detalle (actividades por unidad) --}}
                                <tr data-detail="{{ $row['student']->id }}"
                                    data-active-for="{{ $row['student']->id }}"
                                    class="hidden bg-slate-50/70">

                                    <td colspan="{{ count($units) + 2 }}" class="px-6 pb-6">

                                        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">

                                            @foreach ($units as $group)

                                                <div class="rounded-2xl border border-slate-200 bg-white p-4">

                                                    <div class="flex items-center justify-between gap-2">

                                                        <p class="min-w-0 truncate text-xs font-black uppercase tracking-wide text-slate-500"
                                                            title="{{ $group['name'] }}">

                                                            @if ($group['number'] !== null)
                                                                Unidad {{ $group['number'] }} · {{ $group['name'] }}
                                                            @else
                                                                {{ $group['name'] }}
                                                            @endif
                                                        </p>

                                                        <span
                                                            class="shrink-0 rounded-full px-2.5 py-1 text-[11px] font-black {{ $gradeClass($row['unitScores'][$group['key']] ?? null) }}">
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
                                                                            class="shrink-0 text-[11px] font-black uppercase tracking-wide text-slate-400">
                                                                            Sin puntaje
                                                                        </span>
                                                                    @else
                                                                        <span
                                                                            class="shrink-0 text-[11px] font-black text-emerald-700">
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
                                    <p class="text-sm font-black text-slate-500">
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
                    class="flex flex-col items-center justify-between gap-3 border-t border-slate-100 px-6 py-4 sm:flex-row">

                    <p id="grading-count-label" class="text-xs font-bold text-slate-500">

                    </p>


                    <div class="flex items-center gap-3">

                        <button type="button" id="grading-prev"
                            class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-black text-slate-600 transition hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-700 disabled:cursor-not-allowed disabled:opacity-40">
                            ← Anterior
                        </button>

                        <span id="grading-page-label" class="text-xs font-black text-slate-500">

                        </span>

                        <button type="button" id="grading-next"
                            class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-black text-slate-600 transition hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-700 disabled:cursor-not-allowed disabled:opacity-40">
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

    </main>


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

                    if (state.order === 'name') {
                        rows = rows.sort(function (a, b) {
                            return a.getAttribute('data-student-name')
                                .localeCompare(b.getAttribute('data-student-name'), 'es', { sensitivity: 'base' });
                        });
                    } else {
                        rows = rows.sort(function (a, b) {
                            return numericFinal(b) - numericFinal(a);
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

                    studentRow.setAttribute('aria-expanded', 'false');
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

                    var expanded = studentRow.getAttribute('aria-expanded') === 'true';
                    var caret = studentRow.querySelector('.grading-caret');

                    if (expanded) {
                        detailRow.classList.add('hidden');
                        caret.classList.remove('rotate-90', 'text-emerald-600');
                    } else {
                        detailRow.classList.remove('hidden');
                        caret.classList.add('rotate-90', 'text-emerald-600');
                    }

                    studentRow.setAttribute('aria-expanded', expanded ? 'false' : 'true');
                });

                render();
            })();
        </script>
    @endif

</body>

</html>
