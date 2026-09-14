
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard - Klassio</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-50 text-slate-900">

    <div class="flex min-h-screen flex-col lg:flex-row">


        <!-- =====================================
             BARRA LATERAL
        ====================================== -->

        <aside class="hidden w-64 shrink-0 flex-col border-r border-slate-200 bg-white lg:flex">

            <!-- LOGO -->

            <div class="flex h-24 items-center gap-3 px-7">

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-600 text-xl font-extrabold text-white">
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

                <p class="mb-4 px-3 text-[11px] font-bold uppercase tracking-widest text-slate-400">
                    Menú principal
                </p>


                <a
                    href="{{ route('student.dashboard') }}"
                    class="mb-2 flex items-center gap-3 rounded-xl bg-indigo-50 px-4 py-3 text-sm font-bold text-indigo-600"
                >

                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l9-9 9 9M5 10v10h14V10"/>
                    </svg>

                    Inicio

                </a>


                <a
                    href="{{ route('student.classes.index') }}"
                    class="mb-2 flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-slate-500 transition hover:bg-slate-50 hover:text-indigo-600"
                >

                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 5a2 2 0 012-2h12a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V5zM8 7h8M8 11h8M8 15h5"/>
                    </svg>

                    Mis clases

                </a>


                <a
                    href="#juegos"
                    class="mb-2 flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-slate-500 transition hover:bg-slate-50 hover:text-indigo-600"
                >

                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 8h12a4 4 0 014 4v3a4 4 0 01-4 4h-1l-3-3H10l-3 3H6a4 4 0 01-4-4v-3a4 4 0 014-4zM8 12v4M6 14h4M16 13h.01M19 11h.01"/>
                    </svg>

                    Juegos educativos

                </a>


                <a
                    href="#actividades"
                    class="mb-2 flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-slate-500 transition hover:bg-slate-50 hover:text-indigo-600"
                >

                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5h6M9 3h6v4H9V3zM6 7h12a2 2 0 012 2v10a2 2 0 01-2 2H6a2 2 0 01-2-2V9a2 2 0 012-2zM8 12h8M8 16h5"/>
                    </svg>

                    Actividades

                </a>

            </nav>


            <!-- PIE DE BARRA -->

            <div class="border-t border-slate-100 p-4">

                <div class="mb-4 flex items-center gap-3 rounded-xl bg-slate-50 p-3">

                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-100 font-bold text-indigo-600">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>

                    <div class="min-w-0">

                        <p class="truncate text-sm font-bold text-slate-800">
                            {{ auth()->user()->name }}
                        </p>

                        <p class="text-xs text-slate-400">
                            Estudiante
                        </p>

                    </div>

                </div>


                <!-- Cerrar sesión -->

                <form method="POST" action="/logout">

                    @csrf

                    <button
                        type="submit"
                        class="flex w-full items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium text-slate-500 transition hover:bg-rose-50 hover:text-rose-600"
                    >

                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 17l5-5-5-5M15 12H3M21 3v18"/>
                        </svg>

                        Cerrar sesión

                    </button>

                </form>

            </div>

        </aside>


        <!-- =====================================
             CONTENIDO PRINCIPAL
        ====================================== -->

        <main class="min-w-0 flex-1">


            <!-- BARRA SUPERIOR -->

            <header class="flex h-20 items-center justify-between border-b border-slate-200 bg-white px-5 sm:px-8">

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

                <section class="relative overflow-hidden rounded-3xl bg-indigo-600 px-6 py-8 text-white shadow-sm sm:px-10 sm:py-10">

                    <div class="relative z-10 max-w-2xl">

                        <span class="inline-flex rounded-full bg-white/15 px-3 py-1.5 text-[11px] font-bold uppercase tracking-widest text-indigo-100">
                            ¡Bienvenido a Klassio!
                        </span>


                        <h1 class="mt-5 text-3xl font-extrabold leading-tight tracking-tight sm:text-4xl lg:text-5xl">

                            Hola,
                            {{ auth()->user()->name }} 👋

                        </h1>


                        <p class="mt-4 max-w-lg text-sm leading-7 text-indigo-100 sm:text-base">

                            Tu aprendizaje comienza aquí.
                            Explora tus clases, participa en actividades
                            y aprende jugando con tus compañeros.

                        </p>

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

                </section>


                <!-- =====================================
                     UNIRSE A UNA CLASE
                ====================================== -->

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

                            <p class="text-xs font-bold uppercase tracking-widest text-indigo-600">
                                Aprende jugando
                            </p>

                            <h2 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-900">
                                Juegos educativos
                            </h2>

                            <p class="mt-2 text-sm text-slate-500">
                                Descubre los retos que Klassio tiene para ti.
                            </p>

                        </div>

                    </div>


                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">


                        <!-- SOPA DE LETRAS -->

                        <div class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">

                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-rose-100 text-rose-500 text-2xl">
                                🔎
                            </div>

                            <h3 class="mt-4 font-extrabold text-slate-900">
                                Sopa de letras
                            </h3>

                            <p class="mt-2 text-sm leading-6 text-slate-500">
                                Encuentra las palabras y pon a prueba tu concentración.
                            </p>

                            <span class="mt-4 inline-block text-xs font-bold text-rose-500">
                                Juego educativo
                            </span>

                        </div>


                        <!-- CRUCIGRAMA -->

                        <div class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">

                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 text-amber-600 text-2xl">
                                🧩
                            </div>

                            <h3 class="mt-4 font-extrabold text-slate-900">
                                Crucigrama
                            </h3>

                            <p class="mt-2 text-sm leading-6 text-slate-500">
                                Resuelve pistas y descubre nuevas palabras.
                            </p>

                            <span class="mt-4 inline-block text-xs font-bold text-amber-600">
                                Juego educativo
                            </span>

                        </div>


                        <!-- CONECTA LOS PUNTOS -->

                        <div class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">

                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-teal-100 text-teal-600 text-2xl">
                                ✨
                            </div>

                            <h3 class="mt-4 font-extrabold text-slate-900">
                                Conecta los puntos
                            </h3>

                            <p class="mt-2 text-sm leading-6 text-slate-500">
                                Une los puntos y completa nuevos desafíos.
                            </p>

                            <span class="mt-4 inline-block text-xs font-bold text-teal-600">
                                Juego educativo
                            </span>

                        </div>


                        <!-- KAHOOT -->

                        <div class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">

                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600 text-2xl">
                                🏆
                            </div>

                            <h3 class="mt-4 font-extrabold text-slate-900">
                                Quiz tipo Kahoot
                            </h3>

                            <p class="mt-2 text-sm leading-6 text-slate-500">
                                Responde preguntas y demuestra lo que sabes.
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

                <section id="actividades" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">

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

        </main>

    </div>

</body>

</html>