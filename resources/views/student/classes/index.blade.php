@extends('student.layouts.app')

@section('title', 'Mis clases')
@section('page-heading', 'Mis clases')

@section('content')
            <div class="mx-auto max-w-7xl space-y-8 px-5 py-8 sm:px-8 lg:px-10">



                <!-- ENCABEZADO -->

                <section
                    class="relative overflow-hidden rounded-3xl bg-indigo-600 px-6 py-8 text-white shadow-sm sm:px-10 sm:py-10">


                    <div class="relative z-10 max-w-2xl">


                        <span
                            class="inline-flex rounded-full bg-white/15 px-3 py-1.5 text-[11px] font-bold uppercase tracking-widest text-indigo-100">

                            🎒 Mi mochila

                        </span>


                        <h1 class="mt-5 text-3xl font-extrabold leading-tight tracking-tight sm:text-4xl">

                            Mis clases 📚

                        </h1>


                        <p class="mt-4 max-w-lg text-base font-medium leading-7 text-indigo-100 sm:text-lg">
                            Aquí están tus clases. ¡Toca una para jugar! 👇

                        </p>

                    </div>



                    <!-- DECORACIONES -->

                    <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full border border-white/15"></div>

                    <div class="absolute -bottom-28 right-24 h-72 w-72 rounded-full border border-white/10"></div>

                    <div class="absolute right-10 top-10 hidden text-7xl opacity-20 sm:block">

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

                <section class="space-y-5">


                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">


                        <div>


                            <p class="text-xs font-bold uppercase tracking-widest text-indigo-600">

                                🎒 Mis clases

                            </p>


                            <h2 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-900">

                                ¿A qué jugamos hoy?

                            </h2>


                            <p class="mt-2 text-base font-medium text-slate-500">
                                Toca tu clase 👇

                            </p>

                        </div>


                        <a href="{{ route('student.dashboard') }}"
                            class="inline-flex w-fit items-center justify-center rounded-xl bg-white px-4 py-3 text-sm font-bold text-slate-600 shadow-sm ring-1 ring-slate-200 transition hover:bg-indigo-50 hover:text-indigo-600">

                            ← Volver al dashboard

                        </a>

                    </div>

                    @if ($classes->hasPages())
                        <div class="mt-8">
                            {{ $classes->links() }}
                        </div>
                    @endif



                    <!-- LISTA DE CLASES -->

                    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">


                        @forelse ($classes as $class)
                            <article
                                class="group flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">


                                <!-- CABECERA DE TARJETA -->

                                <div class="relative overflow-hidden bg-indigo-600 px-6 py-6 text-white">


                                    <div class="relative z-10">


                                        <span
                                            class="inline-flex rounded-full bg-white/15 px-3 py-1 text-[10px] font-bold uppercase tracking-widest text-indigo-100">

                                            Clase inscrita

                                        </span>


                                        <h3 class="mt-4 text-xl font-extrabold leading-tight">

                                            {{ $class->name }}

                                        </h3>

                                    </div>


                                    <div
                                        class="absolute -right-6 -top-10 h-32 w-32 rounded-full border border-white/15">
                                    </div>


                                    <div
                                        class="absolute -bottom-12 -left-8 h-32 w-32 rounded-full border border-white/10">
                                    </div>

                                </div>



                                <!-- CUERPO -->

                                <div class="flex flex-1 flex-col p-6">


                                    @if ($class->description)
                                        <p class="text-sm leading-6 text-slate-500">

                                            {{ $class->description }}

                                        </p>
                                    @else
                                        <p class="text-sm italic leading-6 text-slate-400">

                                            Sin descripción disponible.

                                        </p>
                                    @endif



                                    <!-- ESTADO -->

                                    <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4">


                                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">

                                            Estado

                                        </span>


                                        <span
                                            class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-600">

                                            {{ match ($class->status) { 'active' => 'Activa', 'archived' => 'Archivada', default => ucfirst($class->status) } }}

                                        </span>

                                    </div>



                                    <!-- BOTÓN -->

                                    <a href="{{ route('student.class.show', $class->id) }}"
                                        class="mt-6 inline-flex items-center justify-center rounded-2xl bg-indigo-600 px-5 py-4 text-base font-black text-white shadow-md transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-100 active:scale-95">

                                        ¡Entrar! 🚪

                                        <span class="ml-2 text-lg">

                                            →

                                        </span>

                                    </a>

                                </div>

                            </article>


                        @empty


                            <!-- ESTADO VACÍO -->

                            <div
                                class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center">


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

                    </div>

                </section>



                <!-- PIE -->

                <footer class="border-t border-slate-200 pt-6 text-center text-xs text-slate-400">

                    © {{ date('Y') }} Klassio · Plataforma educativa

                </footer>


            </div>
@endsection
