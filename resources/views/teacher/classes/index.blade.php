@extends('teacher.layouts.app')

@section('title', 'Mis clases')
@section('brand-url', route('teacher.dashboard'))
@section('brand-caption', 'Panel del profesor')
@section('header-context')
<span class="hidden text-sm font-semibold text-slate-600 sm:block">
    {{ auth()->user()->name }}
</span>

<form action="{{ url('/logout') }}" method="POST">
    @csrf
    <button type="submit" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-600 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600">
        Cerrar sesión
    </button>
</form>
@endsection

@section('content')
        <div class="mb-6">
            <a href="{{ route('teacher.dashboard') }}" class="klassio-back-button">
                ← Volver al dashboard
            </a>
        </div>

        {{-- Encabezado --}}
        <section
            class="mb-8 overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-700 via-teal-600 to-cyan-600 p-6 text-white shadow-xl shadow-emerald-200 sm:p-8">

            <div class="flex flex-col justify-between gap-6 sm:flex-row sm:items-center">

                <div>

                    <div
                        class="mb-3 inline-flex items-center gap-2 rounded-full bg-white/20 px-3 py-1 text-xs font-black uppercase tracking-wider backdrop-blur-sm">
                        <span>🏫</span>
                        Administración
                    </div>

                    <h2 class="text-3xl font-black tracking-tight sm:text-4xl">
                        Mis clases
                    </h2>

                    <p class="mt-3 max-w-2xl text-sm font-medium leading-6 text-emerald-50 sm:text-base">
                        Administra las clases que has creado, consulta sus códigos y organiza tus espacios de
                        aprendizaje.
                    </p>

                </div>

                <a href="{{ route('teacher.classes.create') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-black text-emerald-700 shadow-lg transition hover:bg-emerald-50">

                    <span class="text-lg">+</span>
                    Crear clase
                </a>

            </div>

        </section>

        {{-- Los mensajes flash (éxito/error) los renderiza la barra del
             layout compartido teacher.layouts.app: aquí no se repiten. --}}

        {{-- Filtro por periodo --}}
        <section class="mb-6 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <form method="GET" action="{{ route('teacher.classes.index') }}"
                class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                <div>
                    <h3 class="text-2xl font-black text-slate-900">
                        Clases creadas
                    </h3>

                    <p class="mt-1 text-sm font-medium text-slate-500">
                        Consulta y administra tus clases desde un solo lugar.
                    </p>
                </div>

                <div class="flex items-end gap-3">
                    <div>
                        <label for="period" class="mb-1 block text-xs font-black uppercase tracking-wide text-slate-400">
                            Periodo
                        </label>
                        <select id="period" name="period" data-auto-submit
                            class="rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm font-bold text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100">
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
                            class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-700">
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
                <details class="mb-4 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-lg shadow-slate-200/60"
                    @if ($group['code'] === $currentPeriod || ($selectedPeriod === null && $periods->onFirstPage()) || $selectedPeriod === $group['code']) open @endif
                >
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-6 py-4 transition hover:bg-slate-50">
                        <span class="flex min-w-0 items-center gap-3">
                            <span class="text-xl">📅</span>
                            <span class="truncate text-sm font-black uppercase tracking-widest text-emerald-700">
                                {{ \App\Support\PeriodResolver::label($group['code']) }}
                            </span>

                            @if ($group['code'] === $currentPeriod)
                                <span class="shrink-0 rounded-full bg-emerald-600 px-3 py-1 text-[10px] font-black uppercase tracking-wide text-white">
                                    Periodo actual
                                </span>
                            @endif
                        </span>
                        <span class="shrink-0 rounded-full bg-emerald-100 px-3 py-1 text-xs font-black text-emerald-700">
                            {{ $group['classes']->count() }} {{ $group['classes']->count() === 1 ? 'clase' : 'clases' }}
                        </span>
                    </summary>

                    <div class="grid gap-6 border-t border-slate-100 px-4 py-5 sm:px-6 lg:grid-cols-2 xl:grid-cols-3">
                        @each('partials.class-card-teacher', $group['classes'], 'class')
                    </div>
                </details>
            @empty
                <div
                    class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center shadow-sm">

                    <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-emerald-100 text-4xl">
                        🏫
                    </div>

                    <h4 class="mt-6 text-2xl font-black text-slate-900">
                        No tienes clases creadas
                    </h4>

                    <p class="mx-auto mt-3 max-w-md text-sm font-medium leading-6 text-slate-500">
                        Crea tu primera clase para comenzar a organizar alumnos, actividades y juegos educativos.
                    </p>

                    <a href="{{ route('teacher.classes.create') }}"
                        class="mt-6 inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-6 py-3 text-sm font-black text-white shadow-lg shadow-emerald-200 transition hover:bg-emerald-700">

                        <span class="text-lg">+</span>
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


        <script>
            document.querySelectorAll('[data-auto-submit]').forEach((input) => {
                input.addEventListener('change', () => input.form?.submit());
            });
        </script>


@endsection
