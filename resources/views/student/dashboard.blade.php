@extends('student.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Inicio')

@section('content')
    <div class="space-y-8">

                <!-- =====================================
                     BIENVENIDA
                 ====================================== -->

                <section class="rounded-2xl bg-orange-600 p-6 text-white shadow-xl shadow-orange-200 sm:p-8">

                    <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">

                        <div class="max-w-2xl">

                            <span class="mb-3 inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-xs font-bold uppercase tracking-wider text-white ring-1 ring-white/20">
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z" />
                                </svg>
                                ¡Listo para jugar!
                            </span>

                            <h2 class="text-2xl font-bold tracking-tight sm:text-4xl">
                                ¡Hola, {{ auth()->user()->name }}!
                            </h2>

                            <p class="mt-3 text-sm leading-6 text-orange-50 sm:text-base">
                                Bienvenido a tu espacio. Únete a una sala con el código
                                de tu profesor o revisa tus clases para empezar a jugar.
                            </p>

                        </div>

                        <div class="hidden h-20 w-20 shrink-0 items-center justify-center rounded-2xl bg-white/15 ring-1 ring-white/20 md:flex">
                            <svg class="h-10 w-10 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 6.087c0-.355.186-.676.401-.867.344-.31.698-.729.698-1.22 0-.977-.894-1.5-1.849-1.5-1.32 0-2.14.665-2.758 1.435-.135.166-.35.283-.576.283h-.683a.75.75 0 0 1 0-1.5h.683c.737 0 1.193-.378 1.7-.87.67-.65 1.603-1.348 3.134-1.348 1.695 0 3.349 1.04 3.349 3 0 .914-.475 1.565-1.01 2.033a2.727 2.727 0 0 0-.59.867c-.078.294-.05.63.05.92.05.22.12.43.2.63a.75.75 0 0 1-1.38.565 6.5 6.5 0 0 1-.25-.78 3.18 3.18 0 0 1-.11-.92c0-.44.132-.832.31-1.09Zm-2.5-3.09a.75.75 0 0 1 0 1.5h-.5a.75.75 0 0 1 0-1.5h.5Zm-6 5.25c0 3.724 1.295 6.475 3.06 8.42a.75.75 0 0 1 1.265.75 8.25 8.25 0 0 1-1.32 1.946 8.25 8.25 0 0 0 3.495-.975.75.75 0 0 1 .75 1.299 9.75 9.75 0 0 1-5.25 1.234 9.75 9.75 0 0 1-5.25-1.234.75.75 0 0 1 .75-1.299 8.25 8.25 0 0 0 3.495.975 8.25 8.25 0 0 1-1.32-1.946C6.295 14.222 5 11.471 5 7.747a.75.75 0 0 1 1.5 0Zm9.75 3.66a.75.75 0 0 1 1.5 0c0 3.725-1.295 6.476-3.06 8.421a.75.75 0 0 1-1.266-.75 8.25 8.25 0 0 0 1.32-1.946c1.506-1.945 1.506-4.725 1.506-5.725Z" />
                            </svg>
                        </div>

                    </div>

                </section>

                <!-- =====================================
                     ACCIONES PRINCIPALES
                 ====================================== -->

                <section class="grid gap-4 md:grid-cols-2">

                    <!-- MIS CLASES -->

                    <a href="{{ route('student.classes.index') }}"
                       class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-orange-200 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-600 focus-visible:ring-offset-2">

                        <div class="mb-5 flex items-start justify-between">

                            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-orange-50 text-orange-700 ring-1 ring-orange-100 transition group-hover:bg-orange-600 group-hover:text-white group-hover:ring-orange-600">

                                <svg class="h-7 w-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.628 48.628 0 0 1 12 20.904a48.628 48.628 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                                </svg>

                            </div>

                            <span class="rounded-full bg-orange-50 px-3 py-1 text-xs font-bold text-orange-700 ring-1 ring-orange-100">
                                Ver clases
                            </span>

                        </div>

                        <h3 class="text-2xl font-bold text-slate-900">
                            Mis clases
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-slate-500">
                            Consulta las materias en las que estás inscrito y
                            revisa tu espacio académico.
                        </p>

                        <div class="mt-6 flex items-center gap-2 text-sm font-bold text-orange-700">
                            Ver mis clases
                            <svg class="h-4 w-4 transition group-hover:translate-x-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </div>

                    </a>

                    <!-- UNIRSE A UNA SALA MULTIJUGADOR -->

                    <a href="{{ route('student.game-sessions.join-page') }}"
                       class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-pink-200 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-pink-600 focus-visible:ring-offset-2">

                        <div class="mb-5 flex items-start justify-between">

                            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-pink-50 text-pink-700 ring-1 ring-pink-100 transition group-hover:bg-pink-600 group-hover:text-white group-hover:ring-pink-600">

                                <svg class="h-7 w-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                                </svg>

                            </div>

                            <span class="rounded-full bg-pink-50 px-3 py-1 text-xs font-bold text-pink-700 ring-1 ring-pink-100">
                                Multijugador
                            </span>

                        </div>

                        <h3 class="text-2xl font-bold text-slate-900">
                            Unirse a una sala multijugador
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-slate-500">
                            ¿Tu profesor te compartió un código de sala? Entra y
                            juega en tiempo real con tus compañeros.
                        </p>

                        <div class="mt-6 flex items-center gap-2 text-sm font-bold text-pink-700">
                            Entrar con código
                            <svg class="h-4 w-4 transition group-hover:translate-x-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </div>

                    </a>

                </section>

                <!-- =====================================
                     UNIRSE A UNA CLASE
                 ====================================== -->

                <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">

                    <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">

                        <div class="max-w-xl">

                            <div class="flex items-center gap-3">

                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-orange-700">

                                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16M4 12h16" />
                                    </svg>

                                </div>

                                <div>

                                    <h2 class="text-lg font-bold text-slate-900">
                                        Unirse a una clase
                                    </h2>

                                    <p class="mt-1 text-sm text-slate-500">
                                        ¿Tienes un código de tu maestro?
                                    </p>

                                </div>

                            </div>

                            <p class="mt-4 text-sm leading-6 text-slate-500">

                                Ingresa el código proporcionado por tu maestro
                                para unirte a una nueva clase.

                            </p>

                        </div>


                        <form
                            action="{{ route('student.class.join') }}"
                            method="POST"
                            class="w-full lg:max-w-md"
                        >

                            @csrf

                            <label
                                for="code"
                                class="mb-2 block text-sm font-semibold text-slate-700"
                            >
                                Código de la clase
                            </label>


                            <div class="flex flex-col gap-3 sm:flex-row">

                                <input
                                    type="text"
                                    id="code"
                                    name="code"
                                    value="{{ old('code') }}"
                                    maxlength="6"
                                    required
                                    placeholder="Ej. ABC123"
                                    class="min-w-0 flex-1 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-semibold uppercase tracking-widest text-slate-900 outline-none transition placeholder:font-normal placeholder:tracking-normal placeholder:text-slate-400 focus:border-orange-500 focus:bg-white focus:ring-4 focus:ring-orange-100"
                                >


                                <button
                                    type="submit"
                                    class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-orange-700 px-6 py-3.5 text-sm font-bold text-white transition hover:bg-orange-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-700 focus-visible:ring-offset-2"
                                >

                                    Unirse

                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                    </svg>

                                </button>

                            </div>


                            @error('code')

                                <p class="mt-2 text-sm font-medium text-red-600" role="alert">
                                    {{ $message }}
                                </p>

                            @enderror

                        </form>

                    </div>

                </section>

            </div>

@endsection
