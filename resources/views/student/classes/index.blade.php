@extends('student.layouts.app')

@section('title', 'Mis clases')
@section('page-heading', 'Mis clases')

@section('content')
            <div class="mx-auto max-w-7xl space-y-5 px-4! py-5! sm:px-6! sm:py-6! lg:px-8!">

                {{-- El título «Mis clases» ya lo muestra el encabezado del layout
                     compartido (page-heading): aquí solo va el_intro compacto. --}}

                <!-- ENCABEZADO -->
                <section
                    class="relative overflow-hidden rounded-2xl bg-indigo-600 px-5! py-4! text-white shadow-sm sm:px-6! sm:py-5!">

                    <div class="relative z-10 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                        <div class="flex min-w-0 flex-col gap-1.5 sm:flex-row sm:items-center sm:gap-3">

                            <span
                                class="inline-flex w-fit shrink-0 rounded-full bg-white/15 px-3! py-1 text-[11px] font-bold uppercase tracking-widest text-indigo-100">

                                🎒 Mi mochila

                            </span>

                            <p class="text-sm font-semibold text-white mb-0! sm:text-base">
                                Aquí están tus clases. ¡Toca una para jugar! 👇
                            </p>

                        </div>

                        <a href="{{ route('student.dashboard') }}"
                            class="klassio-back-button w-fit shrink-0">

                            ← Volver al dashboard

                        </a>

                    </div>

                    <!-- DECORACIONES (ocultas en móvil para no ensanchar la tarjeta) -->

                    <div class="absolute -right-16 -top-16 hidden h-44 w-44 rounded-full border border-white/15 sm:block"></div>

                    <div class="absolute -bottom-20 right-16 hidden h-52 w-52 rounded-full border border-white/10 sm:block"></div>

                    <div class="absolute right-8 top-2 hidden text-5xl opacity-20 sm:block">

                        ✦

                    </div>

                </section>



                <!-- MENSAJES -->

                @if (session('success'))
                    <div
                        class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">


                        <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />

                        </svg>


                        <p>

                            {{ session('success') }}

                        </p>

                    </div>
                @endif



                @if (session('error'))
                    <div
                        class="flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">


                        <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 6l12 12M6 18L18 6" />

                        </svg>


                        <p>

                            {{ session('error') }}

                        </p>

                    </div>
                @endif



                <!-- TÍTULO DE SECCIÓN -->

                <section class="space-y-4">


                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">


                        <div>


                            <h2 class="text-xl! mb-0! font-extrabold tracking-tight text-slate-900">

                                ¿A qué jugamos hoy?

                            </h2>


                            <p class="mt-1! text-sm font-medium text-slate-500">
                                Toca tu clase 👇

                            </p>

                        </div>


                        {{-- Filtro compacto: etiqueta encima del selector, a la derecha
                             en escritorio y a todo el ancho en móvil. La acción, el
                             parámetro GET y los valores se conservan sin cambios. --}}

                        <form method="GET" action="{{ route('student.classes.index') }}"
                            class="flex flex-col items-start gap-2 sm:flex-row sm:items-end">

                            <div class="w-full sm:w-72">
                                <label for="period"
                                    class="mb-1 block text-[11px] font-bold uppercase tracking-widest text-slate-400">
                                    Periodo
                                </label>
                                <select id="period" name="period" data-auto-submit
                                    class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3! py-2 text-sm! font-bold text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">
                                    <option value="" @if ($selectedPeriod === null) selected @endif>Todos los periodos</option>
                                    @foreach ($availablePeriods as $periodCode)
                                        <option value="{{ $periodCode }}" @if ($periodCode === $selectedPeriod) selected @endif>
                                            {{ \App\Support\PeriodResolver::label($periodCode) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            @if ($selectedPeriod !== null)
                                <a href="{{ route('student.classes.index') }}"
                                    class="inline-flex items-center justify-center rounded-xl bg-white px-3! py-2 text-sm font-bold text-slate-600 shadow-sm ring-1 ring-slate-200 transition hover:bg-indigo-50 hover:text-indigo-600">
                                    Limpiar
                                </a>
                            @endif
                        </form>

                    </div>


                    <!-- ACORDEONES POR PERIODO (paginados) -->

                    @php
                        $currentPeriod = \App\Support\PeriodResolver::current();
                    @endphp

                    @forelse ($periods as $group)
                        <details class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm mb-4"
                            @if ($group['code'] === $currentPeriod || ($selectedPeriod === null && $periods->onFirstPage()) || $selectedPeriod === $group['code']) open @endif
                        >
                            <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-5 py-4 transition hover:bg-indigo-50/40">
                                <span class="flex min-w-0 items-center gap-2.5">
                                    <span class="text-lg">📅</span>
                                    <span class="truncate text-xs font-bold uppercase tracking-widest text-indigo-600">
                                        {{ \App\Support\PeriodResolver::label($group['code']) }}
                                    </span>

                                    @if ($group['code'] === $currentPeriod)
                                        <span class="shrink-0 rounded-full bg-indigo-600 px-2.5 py-1 text-[9px] font-bold uppercase tracking-widest text-white">
                                            Periodo actual
                                        </span>
                                    @endif
                                </span>
                                <span class="shrink-0 rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-600">
                                    {{ $group['classes']->count() }} {{ $group['classes']->count() === 1 ? 'clase' : 'clases' }}
                                </span>
                            </summary>

                            <div class="grid gap-5 border-t border-slate-100 p-4 md:grid-cols-2 xl:grid-cols-3">
                                @each('partials.class-card-student', $group['classes'], 'class')
                            </div>
                        </details>
                    @empty


                        <!-- ESTADO VACÍO -->

                        <div
                            class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center">


                            <div
                                class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-50 text-3xl">

                                📚

                            </div>


                            <h3 class="mt-5 text-xl font-extrabold text-slate-900">

                                ¿Sin clases? 🎈

                            </h3>


                            <p class="mx-auto mt-3 max-w-md text-base font-medium leading-6 text-slate-500">

                                Todavía no estás inscrito en ninguna clase. Pide el código a tu maestro y únete desde el inicio. 👆

                            </p>


                            <a href="{{ route('student.dashboard') }}"
                                class="mt-6 inline-flex items-center justify-center rounded-2xl bg-indigo-600 px-7 py-4 text-base font-black text-white shadow-md transition hover:bg-indigo-700 active:scale-95">

                                Ir al inicio 🏠

                            </a>

                        </div>
                    @endforelse


                    @if ($periods->hasPages())
                        <div class="mt-6">
                            {{ $periods->links() }}
                        </div>
                    @endif

                </section>



                <!-- PIE -->

                <footer class="border-t border-slate-200 pt-6 text-center text-xs text-slate-400">

                    © {{ date('Y') }} Klassio · Plataforma educativa

                </footer>


            </div>

            <script>
                document.querySelectorAll('[data-auto-submit]').forEach((input) => {
                    input.addEventListener('change', () => input.form?.submit());
                });
            </script>
@endsection
