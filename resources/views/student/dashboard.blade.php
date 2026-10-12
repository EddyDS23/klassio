
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard - Klassio</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-50 text-slate-900 student-kids">

    <div class="flex min-h-screen flex-col lg:flex-row">


        <!-- =====================================
             BARRA LATERAL
        ====================================== -->

        <aside class="hidden w-64 shrink-0 flex-col border-r border-slate-200 bg-white lg:sticky lg:top-0 lg:flex lg:h-screen lg:overflow-y-auto">

            <!-- LOGO -->

            <div class="flex h-24 items-center gap-3 px-7">

                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-600 text-2xl font-extrabold text-white shadow-lg shadow-indigo-200">
                    K
                </div>

                <div>
                    <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">
                        Klassio
                    </h1>

                    <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-400">
                        Aprende y conecta
                    </p>
                </div>

            </div>


            <!-- NAVEGACIÓN -->

            <nav class="flex-1 px-4 py-5">

                <p class="mb-4 px-3 text-xs font-bold uppercase tracking-widest text-slate-400">
                    Menú 🎈
                </p>


                <a
                    href="{{ route('student.dashboard') }}"
                    class="mb-2 flex items-center gap-3 rounded-2xl bg-indigo-600 px-4 py-3.5 text-base font-bold text-white shadow-md shadow-indigo-200"
                >

                    <span class="text-2xl">🏠</span>

                    Inicio

                </a>


                <a
                    href="{{ route('student.classes.index') }}"
                    class="mb-2 flex items-center gap-3 rounded-2xl px-4 py-3.5 text-base font-bold text-slate-500 transition hover:bg-indigo-50 hover:text-indigo-600"
                >

                    <span class="text-2xl">📚</span>

                    Mis clases

                </a>


                <a
                    href="#juegos"
                    class="mb-2 flex items-center gap-3 rounded-2xl px-4 py-3.5 text-base font-bold text-slate-500 transition hover:bg-indigo-50 hover:text-indigo-600"
                >

                    <span class="text-2xl">🎮</span>

                    Juegos

                </a>


                <a
                    href="#actividades"
                    class="mb-2 flex items-center gap-3 rounded-2xl px-4 py-3.5 text-base font-bold text-slate-500 transition hover:bg-indigo-50 hover:text-indigo-600"
                >

                    <span class="text-2xl">📝</span>

                    Actividades

                </a>

            </nav>


            <!-- PIE DE BARRA -->

            <div class="border-t border-slate-100 p-4">

                <div class="mb-4 flex items-center gap-3 rounded-2xl bg-indigo-50 p-4">

                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-indigo-600 text-xl font-black text-white">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>

                    <div class="min-w-0">

                        <p class="truncate text-base font-black text-slate-800">
                            {{ auth()->user()->name }}
                        </p>

                        <p class="text-sm font-medium text-slate-400">
                            Estudiante ⭐
                        </p>

                    </div>

                </div>


                <!-- Cerrar sesión -->

                <form method="POST" action="/logout">

                    @csrf

                    <button
                        type="submit"
                        class="flex w-full items-center gap-3 rounded-2xl px-4 py-3.5 text-base font-bold text-slate-500 transition hover:bg-rose-50 hover:text-rose-600"
                    >

                        <span class="text-2xl">👋</span>

                        Salir

                    </button>

                </form>

            </div>

        </aside>


        <!-- =====================================
             CONTENIDO PRINCIPAL
        ====================================== -->

        <main class="min-w-0 flex-1">


            <!-- BARRA SUPERIOR -->

            <header class="sticky top-4 z-20 mx-4 mt-4 flex h-20 items-center justify-between rounded-2xl border border-slate-200 bg-white px-5 shadow-sm sm:mx-6 sm:px-8">

                <div>

                    <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">
                        Mi espacio académico
                    </p>

                    <h2 class="mt-1 text-lg font-bold text-slate-800">
                        Dashboard
                    </h2>

                </div>


                <div class="flex items-center gap-3">

                    <div class="hidden text-right sm:block">

                        <p class="text-sm font-bold text-slate-800">
                            {{ auth()->user()->name }}
                        </p>

                        <p class="text-xs text-slate-400">
                            Estudiante
                        </p>

                    </div>


                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-600 font-bold text-white">

                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}

                    </div>

                </div>

            </header>


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

                            ¿Qué vamos a jugar hoy? 🚀
                            Elige un juego y diviértete.

                        </p>


                        <div class="mt-7 flex flex-wrap gap-3">

                            <a
                                href="#juegos"
                                class="inline-flex items-center gap-2 rounded-2xl bg-white px-7 py-4 text-base font-black text-indigo-700 shadow-lg transition hover:scale-105 active:scale-95"
                            >
                                ¡A jugar! 🎮
                            </a>

                            <a
                                href="{{ route('student.classes.index') }}"
                                class="inline-flex items-center gap-2 rounded-2xl bg-white/15 px-7 py-4 text-base font-black text-white ring-2 ring-white/30 transition hover:scale-105 hover:bg-white/25 active:scale-95"
                            >
                                Mis clases 📚
                            </a>

                        </div>

                    </div>


                    <!-- Decoraciones -->

                    <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full border border-white/15"></div>

                    <div class="absolute -bottom-28 right-24 h-72 w-72 rounded-full border border-white/10"></div>

                    <div class="absolute right-8 top-8 hidden animate-bounce text-6xl sm:block">
                        🎮
                    </div>

                    <div class="absolute bottom-8 right-40 hidden text-5xl opacity-30 md:block">
                        🧩
                    </div>

                    <div class="absolute right-64 top-16 hidden text-4xl opacity-30 lg:block">
                        ⭐
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
                        class="group relative overflow-hidden rounded-3xl border-2 border-indigo-200 bg-indigo-50 p-7 transition hover:-translate-y-1 hover:shadow-xl active:scale-[0.99]"
                    >

                        <div class="flex items-start justify-between">

                            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-600 text-4xl shadow-lg shadow-indigo-200">
                                📚
                            </div>


                            <span class="text-3xl transition group-hover:translate-x-2">
                                →
                            </span>

                        </div>


                        <h3 class="mt-5 text-2xl font-black text-indigo-950">
                            Mis clases
                        </h3>


                        <p class="mt-2 text-base font-medium leading-6 text-indigo-700">
                            Mira tus clases y tus juegos. 🎒
                        </p>


                        <span class="mt-5 inline-flex items-center gap-2 rounded-2xl bg-indigo-600 px-6 py-3.5 text-base font-black text-white shadow-md transition group-hover:bg-indigo-700">
                            Ver mis clases →
                        </span>

                    </a>


                    <!-- JUEGOS EDUCATIVOS -->

                    <a
                        href="#juegos"
                        class="group relative overflow-hidden rounded-3xl border-2 border-teal-200 bg-teal-50 p-7 transition hover:-translate-y-1 hover:shadow-xl active:scale-[0.99]"
                    >

                        <div class="flex items-start justify-between">

                            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-teal-500 text-4xl shadow-lg shadow-teal-200">
                                🎮
                            </div>


                            <span class="text-3xl transition group-hover:translate-x-2">
                                →
                            </span>

                        </div>


                        <h3 class="mt-5 text-2xl font-black text-teal-950">
                            Juegos
                        </h3>


                        <p class="mt-2 text-base font-medium leading-6 text-teal-700">
                            ¡Juega y aprende! 🎉
                        </p>


                        <span class="mt-5 inline-flex items-center gap-2 rounded-2xl bg-teal-500 px-6 py-3.5 text-base font-black text-white shadow-md transition group-hover:bg-teal-600">
                            Explorar juegos →
                        </span>

                    </a>

                </section>


                <!-- =====================================
                     UNIRSE A UNA CLASE
                ====================================== -->

                <section class="rounded-3xl border-2 border-amber-200 bg-amber-50 p-6 shadow-sm sm:p-8">

                    <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">

                        <div class="max-w-xl">

                            <div class="flex items-center gap-3">

                                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-400 text-3xl shadow-md">
                                    ➕
                                </div>


                                <div>

                                    <h2 class="text-2xl font-black text-slate-900">
                                        Únete a una clase 🎈
                                    </h2>

                                    <p class="mt-1 text-base font-medium text-slate-500">
                                        ¿Tienes un código? ¡Escríbelo aquí! 👇
                                    </p>

                                </div>

                            </div>

                        </div>


                        <form
                            action="{{ route('student.class.join') }}"
                            method="POST"
                            class="w-full lg:max-w-md"
                        >

                            @csrf

                            <label
                                for="code"
                                class="mb-2 block text-sm font-bold text-slate-700"
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
                                    placeholder="ABC123"
                                    class="min-w-0 flex-1 rounded-2xl border-2 border-slate-200 bg-white px-5 py-4 text-center text-lg font-black uppercase tracking-[0.3em] text-slate-900 outline-none transition placeholder:font-normal placeholder:tracking-normal placeholder:text-slate-400 focus:border-amber-400 focus:ring-4 focus:ring-amber-100"
                                >


                                <button
                                    type="submit"
                                    class="rounded-2xl bg-amber-400 px-8 py-4 text-base font-black text-white shadow-md transition hover:bg-amber-500 focus:outline-none focus:ring-4 focus:ring-amber-200 active:scale-95"
                                >

                                    ¡Unirme! 🚀

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
                                🎮 Aprende jugando
                            </p>

                            <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-900">
                                ¡Elige tu juego! ⭐
                            </h2>

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

                        </div>


                        <!-- CONECTA LOS PUNTOS -->

                        <div class="group rounded-3xl border-b-8 border-teal-300 bg-white p-6 text-center shadow-md transition hover:-translate-y-1 hover:shadow-xl">

                            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-teal-100 text-5xl">
                                ✨
                            </div>

                            <h3 class="mt-4 text-xl font-black text-slate-900">
                                Une parejas
                            </h3>

                            <p class="mt-2 text-sm font-medium leading-6 text-slate-500">
                                ¡Une y gana puntos! ✨
                            </p>

                        </div>


                        <!-- KAHOOT -->

                        <div class="group rounded-3xl border-b-8 border-indigo-300 bg-white p-6 text-center shadow-md transition hover:-translate-y-1 hover:shadow-xl">

                            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-indigo-100 text-5xl">
                                🏆
                            </div>

                            <h3 class="mt-4 text-xl font-black text-slate-900">
                                Quiz
                            </h3>

                            <p class="mt-2 text-sm font-medium leading-6 text-slate-500">
                                ¡Responde rapidísimo! 🏆
                            </p>

                        </div>


                    </div>

                </section>


                <!-- =====================================
                     ACTIVIDADES
                ====================================== -->

                <section id="actividades" class="rounded-3xl border-2 border-indigo-100 bg-white p-6 shadow-sm sm:p-8">

                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                        <div>

                            <p class="text-sm font-black uppercase tracking-widest text-indigo-600">
                                📝 Mis tareas
                            </p>

                            <h2 class="mt-2 text-2xl font-black text-slate-900">
                                Actividades
                            </h2>

                            <p class="mt-2 text-base font-medium text-slate-500">
                                Lo que te dejó tu maestro. 📝
                            </p>

                        </div>


                        <a
                            href="{{ route('student.classes.index') }}"
                            class="inline-flex items-center justify-center rounded-2xl bg-indigo-600 px-7 py-4 text-base font-black text-white shadow-md transition hover:bg-indigo-700 active:scale-95"
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

        </main>

    </div>

</body>

</html>