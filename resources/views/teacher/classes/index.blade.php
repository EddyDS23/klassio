@extends('teacher.layouts.app')

@section('title', 'Mis clases | Klassio')

@section('page-title', 'Mis clases')

@section('page-actions')
    <a href="{{ route('teacher.classes.create') }}"
       class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-700 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2 sm:w-auto">
        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        Crear clase
    </a>
@endsection

@section('content')

    {{-- Los mensajes flash (éxito/error) los renderiza el layout teacher.layouts.app. --}}

    {{-- Filtro por periodo --}}
    <section class="mb-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">

        <form method="GET" action="{{ route('teacher.classes.index') }}"
              class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h2 class="text-xl font-bold text-slate-900">
                    Clases creadas
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Consulta y administra tus clases desde un solo lugar.
                </p>
            </div>

            <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-end sm:gap-3">
                <div class="min-w-0 flex-1 sm:flex-none">
                    <label for="period" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">
                        Periodo
                    </label>

                    <select id="period" name="period" data-auto-submit
                            class="w-full max-w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 sm:w-auto">
                        <option value="" @if ($selectedPeriod === null) selected @endif>Todos los periodos</option>
                        @foreach ($availablePeriods as $periodCode)
                            <option value="{{ $periodCode }}" @if ($periodCode === $selectedPeriod) selected @endif>
                                {{ \App\Support\PeriodResolver::label($periodCode) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @if ($selectedPeriod !== null)
                    <a href="{{ route('teacher.classes.index') }}"
                       class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-center text-sm font-bold text-slate-600 transition hover:bg-slate-50 hover:text-slate-900 sm:w-auto">
                        Limpiar
                    </a>
                @endif
            </div>

        </form>

    </section>


    {{-- Acordeones por periodo (paginados) --}}
    <section>
        @php
            $currentPeriod = \App\Support\PeriodResolver::current();
        @endphp

        @forelse ($periods as $group)
            <details class="group mb-4 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:border-slate-300"
                @if ($group['code'] === $currentPeriod || ($selectedPeriod === null && $periods->onFirstPage()) || $selectedPeriod === $group['code']) open @endif
            >
                <summary class="flex cursor-pointer select-none list-none items-center justify-between gap-3 px-4 py-3.5 transition hover:bg-slate-50 active:bg-slate-100 sm:gap-4 sm:px-5 sm:py-4" title="Toca para mostrar u ocultar las clases de este periodo">

                    <span class="flex min-w-0 items-center gap-2.5 sm:gap-3">
                        <span class="hidden h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 sm:flex" aria-hidden="true">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                            </svg>
                        </span>

                        <span class="truncate text-sm font-bold uppercase tracking-widest text-emerald-700">
                            {{ \App\Support\PeriodResolver::label($group['code']) }}
                        </span>

                        @if ($group['code'] === $currentPeriod)
                            <span class="shrink-0 rounded-full bg-emerald-600 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-white">
                                <span class="sm:hidden">Actual</span>
                                <span class="hidden sm:inline">Periodo actual</span>
                            </span>
                        @endif
                    </span>

                    <span class="flex shrink-0 items-center gap-1 sm:gap-2">
                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600">
                            {{ $group['classes']->count() }} {{ $group['classes']->count() === 1 ? 'clase' : 'clases' }}
                        </span>

                        <span class="flex h-8 w-8 items-center justify-center rounded-full text-slate-400 transition-colors hover:text-slate-600" aria-hidden="true">
                            <svg class="chevron h-5 w-5 transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                        </span>
                    </span>

                </summary>

                <div class="grid gap-5 border-t border-slate-100 px-4 py-5 sm:px-5 lg:grid-cols-2 xl:grid-cols-3">
                    @each('partials.class-card-teacher', $group['classes'], 'class')
                </div>
            </details>
        @empty
            <div class="rounded-2xl border border-slate-200 bg-white p-12 text-center shadow-sm">

                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-400">
                    <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.628 48.628 0 0 1 12 20.904a48.628 48.628 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                    </svg>
                </div>

                <h3 class="mt-4 text-lg font-bold text-slate-900">
                    No tienes clases creadas
                </h3>

                <p class="mx-auto mt-2 max-w-sm text-sm text-slate-500">
                    Crea tu primera clase para comenzar a organizar alumnos, actividades y juegos educativos.
                </p>

                <a href="{{ route('teacher.classes.create') }}"
                   class="mt-6 inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-700 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2">
                    Crear mi primera clase
                </a>

            </div>
        @endforelse
    </section>

    @if ($periods->hasPages())
        <div class="mt-8">
            {{ $periods->links() }}
        </div>
    @endif


    <style>
        /* Chevron del acordeon: apunta hacia abajo cerrado, gira al abrir. */
        details[open] > summary .chevron { transform: rotate(180deg); }
    </style>

    <script>
        document.querySelectorAll('[data-auto-submit]').forEach((input) => {
            input.addEventListener('change', () => input.form?.submit());
        });
    </script>


@endsection
