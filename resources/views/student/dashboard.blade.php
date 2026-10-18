@extends('student.layouts.app')

@section('title', 'Dashboard')
@section('page-heading', 'Dashboard')

@section('content')
            <div class="mx-auto max-w-7xl space-y-8 px-5 py-8 sm:px-8 lg:px-10">


                <!-- =====================================
                     BIENVENIDA
                ====================================== -->

                <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-600 via-violet-600 to-fuchsia-500 px-6 py-10 text-white shadow-xl shadow-indigo-200 sm:px-10 sm:py-12">

                    <div class="relative z-10 max-w-2xl">

                        <span class="inline-flex items-center gap-2 rounded-full bg-white/20 px-4 py-2 text-xs font-black uppercase tracking-widest text-white">
                            🎮 ¡Hora de jugar!
                        </span>


                        <h1 class="mt-5 text-4xl font-black leading-tight tracking-tight sm:text-5xl lg:text-6xl">

                            ¡Hola,
                            {{ auth()->user()->name }}! 👋

                        </h1>


                        <p class="mt-4 max-w-lg text-base font-medium leading-7 text-indigo-100 sm:text-lg">

                            ¿Qué vamos a jugar hoy? 🚀 Elige un juego y diviértete.

                        </p>

                        <div class="mt-7 flex flex-wrap gap-3">
                            <a href="#juegos" class="inline-flex items-center gap-2 rounded-2xl bg-white px-7 py-4 text-base font-black text-indigo-700 shadow-lg transition hover:scale-105 active:scale-95">¡A jugar! 🎮</a>
                            <a href="{{ route('student.classes.index') }}" class="inline-flex items-center gap-2 rounded-2xl bg-white/15 px-7 py-4 text-base font-black text-white ring-2 ring-white/30 transition hover:scale-105 hover:bg-white/25 active:scale-95">Mis clases 📚</a>
                        </div>

                    </div>


                    <!-- Decoraciones -->

                    <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full border border-white/15"></div>

                    <div class="absolute -bottom-28 right-24 h-72 w-72 rounded-full border border-white/10"></div>

                    <div class="absolute right-10 top-10 hidden text-7xl opacity-20 sm:block">
                        ✦
                    </div>

                </section>


                <!-- =====================================
                     MENSAJES DE LARAVEL
                ====================================== -->

                @if(session('success'))

                    <div class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">

                        <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>

                        <p>{{ session('success') }}</p>

                    </div>

                @endif


                @if(session('error'))

                    <div class="flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">

                        <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M6 18L18 6"/>
                        </svg>

                        <p>{{ session('error') }}</p>

                    </div>

                @endif


                <!-- =====================================
                     ACCIONES PRINCIPALES
                ====================================== -->

                <section class="grid gap-5 md:grid-cols-2">


                    <!-- MIS CLASES -->

                    <a
                        href="{{ route('student.classes.index') }}"
                        class="group relative overflow-hidden rounded-2xl border border-indigo-100 bg-indigo-50 p-6 transition hover:-translate-y-1 hover:shadow-lg"
                    >

                        <div class="flex items-start justify-between">

                            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-600 text-white">

                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 5a2 2 0 012-2h12a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V5zM8 7h8M8 11h8M8 15h5"/>
                                </svg>

                            </div>


                            <span class="text-2xl text-indigo-300 transition group-hover:translate-x-1">
                                →
                            </span>

                        </div>


                        <h3 class="mt-5 text-xl font-extrabold text-indigo-950">
                            Mis clases
                        </h3>


                        <p class="mt-2 text-sm leading-6 text-indigo-700">
                            Consulta las materias en las que estás inscrito
                            y revisa tu espacio académico.
                        </p>


                        <span class="mt-5 inline-block text-sm font-bold text-indigo-600">
                            Ver mis clases →
                        </span>

                    </a>


                    <!-- JUEGOS EDUCATIVOS -->

                    <a
                        href="#juegos"
                        class="group relative overflow-hidden rounded-2xl border border-teal-100 bg-teal-50 p-6 transition hover:-translate-y-1 hover:shadow-lg"
                    >

                        <div class="flex items-start justify-between">

                            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-teal-500 text-white">

                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 8h12a4 4 0 014 4v3a4 4 0 01-4 4h-1l-3-3H10l-3 3H6a4 4 0 01-4-4v-3a4 4 0 014-4zM8 12v4M6 14h4M16 13h.01M19 11h.01"/>
                                </svg>

                            </div>


                            <span class="text-2xl text-teal-300 transition group-hover:translate-x-1">
                                →
                            </span>

                        </div>


                        <h3 class="mt-5 text-xl font-extrabold text-teal-950">
                            Juegos educativos
                        </h3>


                        <p class="mt-2 text-sm leading-6 text-teal-700">
                            Diviértete mientras aprendes con retos,
                            actividades y juegos interactivos.
                        </p>


                        <span class="mt-5 inline-block text-sm font-bold text-teal-600">
                            Explorar juegos →
                        </span>

                    </a>

                <!-- UNIRSE A UNA SALA -->

                    <a
                        href="{{ route('student.game-sessions.join-page') }}"
                        class="group relative overflow-hidden rounded-2xl border border-amber-100 bg-amber-50 p-6 transition hover:-translate-y-1 hover:shadow-lg md:col-span-2"
                    >

                        <div class="flex items-start justify-between">

                            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-500 text-white">

                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 8h12a4 4 0 014 4v3a4 4 0 01-4 4h-1l-3-3H10l-3 3H6a4 4 0 01-4-4v-3a4 4 0 014-4zM8 12v4M6 14h4M16 13h.01M19 11h.01"/>
                                </svg>

                            </div>


                            <span class="text-2xl text-amber-300 transition group-hover:translate-x-1">
                                →
                            </span>

                        </div>


                        <h3 class="mt-5 text-xl font-extrabold text-amber-950">
                            Unirse a una sala multijugador
                        </h3>


                        <p class="mt-2 text-sm leading-6 text-amber-700">
                            ¿Tu profesor te compartió un código de sala?
                            Entra y juega en tiempo real con tus compañeros.
                        </p>


                        <span class="mt-5 inline-block text-sm font-bold text-amber-600">
                            Entrar con código →
                        </span>

                    </a>

                </section>

                <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">

                    <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">

                        <div class="max-w-xl">

                            <div class="flex items-center gap-3">

                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 text-amber-600">

                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4v16M4 12h16"/>
                                    </svg>

                                </div>


                                <div>

                                    <h2 class="text-xl font-extrabold text-slate-900">
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
                                    class="min-w-0 flex-1 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-semibold uppercase tracking-widest text-slate-900 outline-none transition placeholder:font-normal placeholder:tracking-normal placeholder:text-slate-400 focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100"
                                >


                                <button
                                    type="submit"
                                    class="rounded-xl bg-indigo-600 px-6 py-3.5 text-sm font-bold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200"
                                >

                                    Unirse →

                                </button>

                            </div>


                            @error('code')

                                <p class="mt-2 text-sm font-medium text-rose-600">
                                    {{ $message }}
                                </p>

                            @enderror

                        </form>

                    </div>

                </section>


                <!-- =====================================
                     JUEGOS EDUCATIVOS
                ====================================== -->

                <section id="juegos" class="scroll-mt-6 space-y-5">

                    <div class="flex items-end justify-between gap-4">

                        <div>

                            <p class="text-sm font-black uppercase tracking-widest text-indigo-600">
                                🎮 ¡A jugar!
                            </p>

                            <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-900">
                                ¡Elige tu juego! ⭐
                            </h2>

                            <p class="mt-2 text-sm text-slate-500">
                                Descubre los retos que Klassio tiene para ti.
                            </p>

                        </div>

                    </div>


                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">


                        <!-- SOPA DE LETRAS -->

                        <div class="group rounded-3xl border-b-8 border-rose-300 bg-white p-6 text-center shadow-md transition hover:-translate-y-1 hover:shadow-xl">

                            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-rose-100 text-5xl">
                                🔎
                            </div>

                            <h3 class="mt-4 text-xl font-black text-slate-900">
                                Sopa de letras
                            </h3>

                            <p class="mt-2 text-sm font-medium leading-6 text-slate-500">
                                ¡Busca las palabras! 🔎
                            </p>

                            <span class="mt-4 inline-block text-xs font-bold text-rose-500">
                                Juego educativo
                            </span>

                        </div>


                        <!-- CRUCIGRAMA -->

                        <div class="group rounded-3xl border-b-8 border-amber-300 bg-white p-6 text-center shadow-md transition hover:-translate-y-1 hover:shadow-xl">

                            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-amber-100 text-5xl">
                                🧩
                            </div>

                            <h3 class="mt-4 text-xl font-black text-slate-900">
                                Crucigrama
                            </h3>

                            <p class="mt-2 text-sm font-medium leading-6 text-slate-500">
                                ¡Adivina con las pistas! 🧩
                            </p>

                            <span class="mt-4 inline-block text-xs font-bold text-amber-600">
                                Juego educativo
                            </span>

                        </div>


                        <!-- CONECTA LOS PUNTOS -->

                        <div class="group rounded-3xl border-b-8 border-teal-300 bg-white p-6 text-center shadow-md transition hover:-translate-y-1 hover:shadow-xl">

                            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-teal-100 text-5xl">
                                ✨
                            </div>

                            <h3 class="mt-4 text-xl font-black text-slate-900">
                                Conecta los puntos
                            </h3>

                            <p class="mt-2 text-sm font-medium leading-6 text-slate-500">
                                ¡Une parejas y gana puntos! ✨
                            </p>

                            <span class="mt-4 inline-block text-xs font-bold text-teal-600">
                                Juego educativo
                            </span>

                        </div>


                        <!-- KAHOOT -->

                        <div class="group rounded-3xl border-b-8 border-indigo-300 bg-white p-6 text-center shadow-md transition hover:-translate-y-1 hover:shadow-xl">

                            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-indigo-100 text-5xl">
                                🏆
                            </div>

                            <h3 class="mt-4 text-xl font-black text-slate-900">
                                Quiz tipo Kahoot
                            </h3>

                            <p class="mt-2 text-sm font-medium leading-6 text-slate-500">
                                ¡Responde rapidísimo! 🏆
                            </p>

                            <span class="mt-4 inline-block text-xs font-bold text-indigo-600">
                                Juego educativo
                            </span>

                        </div>


                    </div>

                </section>


                <!-- =====================================
                     ACTIVIDADES
                ====================================== -->

                <section id="actividades" class="rounded-3xl border-2 border-indigo-100 bg-white p-6 shadow-sm sm:p-8">

                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                        <div>

                            <p class="text-xs font-bold uppercase tracking-widest text-indigo-600">
                                Tu aprendizaje
                            </p>

                            <h2 class="mt-2 text-2xl font-extrabold text-slate-900">
                                Actividades
                            </h2>

                            <p class="mt-2 text-sm text-slate-500">
                                Aquí podrás consultar las actividades asignadas por tus maestros.
                            </p>

                        </div>


                        <a
                            href="{{ route('student.classes.index') }}"
                            class="inline-flex items-center justify-center rounded-xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-indigo-50 hover:text-indigo-600"
                        >

                            Ver mis clases →

                        </a>

                    </div>

                </section>


                <!-- PIE -->

                <footer class="border-t border-slate-200 pt-6 text-center text-xs text-slate-400">

                    © {{ date('Y') }} Klassio · Plataforma educativa

                </footer>


            </div>
@endsection
