<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard - Maestro | Klassio</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-800">

    <!-- Barra superior -->
    <header class="sticky top-0 z-30 border-b border-emerald-100 bg-white shadow-sm">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">

            <a href="{{ route('teacher.classes.index') }}"
               class="flex items-center gap-3">

                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-600 text-2xl font-black text-white shadow-lg shadow-emerald-200">
                    K
                </div>

                <div>
                    <h1 class="text-xl font-black tracking-tight text-slate-900">
                        Klassio
                    </h1>

                    <p class="text-xs font-bold uppercase tracking-widest text-emerald-600">
                        Panel del maestro
                    </p>
                </div>

            </a>

            <div class="flex items-center gap-3">

                <div class="hidden text-right sm:block">
                    <p class="text-sm font-bold text-slate-800">
                        {{ auth()->user()->name }}
                    </p>

                    <p class="text-xs text-slate-500">
                        Maestro
                    </p>
                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-full bg-emerald-100 font-black text-emerald-700">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>

            </div>

        </div>
    </header>


    <!-- Contenido -->
    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        <!-- Bienvenida -->
        <section class="mb-8 overflow-hidden rounded-3xl bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 p-6 text-white shadow-xl shadow-emerald-200 sm:p-8">

            <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">

                <div>
                    <span class="mb-3 inline-flex rounded-full bg-white/20 px-3 py-1 text-xs font-black uppercase tracking-wider text-white">
                        Área del maestro
                    </span>

                    <h2 class="text-3xl font-black tracking-tight sm:text-4xl">
                        ¡Bienvenido, {{ auth()->user()->name }}! 👋
                    </h2>

                    <p class="mt-3 max-w-2xl text-sm leading-6 text-emerald-50 sm:text-base">
                        Organiza tus clases, administra a tus estudiantes y crea
                        actividades educativas para aprender de una manera más divertida.
                    </p>
                </div>

                <div class="hidden h-28 w-28 items-center justify-center rounded-3xl bg-white/15 text-7xl md:flex">
                    👨‍🏫
                </div>

            </div>

        </section>


        <!-- Mensaje de éxito -->
        @if(session('success'))
            <div class="mb-6 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800 shadow-sm">

                <span class="text-xl">✓</span>

                <div>
                    <p class="font-bold">
                        ¡Operación exitosa!
                    </p>

                    <p class="text-sm">
                        {{ session('success') }}
                    </p>
                </div>

            </div>
        @endif


        <!-- Mensaje de error -->
        @if(session('error'))
            <div class="mb-6 flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800 shadow-sm">

                <span class="text-xl">⚠️</span>

                <div>
                    <p class="font-bold">
                        Ocurrió un problema
                    </p>

                    <p class="text-sm">
                        {{ session('error') }}
                    </p>
                </div>

            </div>
        @endif


        <!-- Tarjetas principales -->
        <section class="mb-8 grid gap-6 md:grid-cols-2">

            <!-- Mis clases -->
            <a href="{{ route('teacher.classes.index') }}"
               class="group rounded-3xl border border-emerald-100 bg-white p-6 shadow-lg shadow-slate-200/60 transition duration-300 hover:-translate-y-1 hover:border-emerald-300 hover:shadow-xl">

                <div class="mb-5 flex items-start justify-between">

                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-100 text-3xl transition group-hover:bg-emerald-600">
                        📚
                    </div>

                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">
                        Administrar
                    </span>

                </div>

                <h3 class="text-2xl font-black text-slate-900">
                    Mis clases
                </h3>

                <p class="mt-2 text-sm leading-6 text-slate-500">
                    Consulta tus clases, revisa estudiantes y administra
                    las actividades de cada grupo.
                </p>

                <div class="mt-6 flex items-center gap-2 text-sm font-black text-emerald-600">
                    Ver mis clases
                    <span class="transition group-hover:translate-x-1">
                        →
                    </span>
                </div>

            </a>


            <!-- Crear clase -->
            <a href="{{ route('teacher.classes.create') }}"
               class="group rounded-3xl border border-cyan-100 bg-white p-6 shadow-lg shadow-slate-200/60 transition duration-300 hover:-translate-y-1 hover:border-cyan-300 hover:shadow-xl">

                <div class="mb-5 flex items-start justify-between">

                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-cyan-100 text-3xl transition group-hover:bg-cyan-600">
                        ➕
                    </div>

                    <span class="rounded-full bg-cyan-50 px-3 py-1 text-xs font-bold text-cyan-700">
                        Nueva clase
                    </span>

                </div>

                <h3 class="text-2xl font-black text-slate-900">
                    Crear clase
                </h3>

                <p class="mt-2 text-sm leading-6 text-slate-500">
                    Crea un nuevo grupo, agrega información y comparte
                    el código de acceso con tus estudiantes.
                </p>

                <div class="mt-6 flex items-center gap-2 text-sm font-black text-cyan-600">
                    Crear una clase
                    <span class="transition group-hover:translate-x-1">
                        →
                    </span>
                </div>

            </a>

        </section>


        <!-- Sección de herramientas -->
        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-lg shadow-slate-200/60 sm:p-8">

            <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <p class="text-xs font-black uppercase tracking-widest text-emerald-600">
                        Herramientas educativas
                    </p>

                    <h2 class="mt-1 text-2xl font-black text-slate-900">
                        Crea experiencias de aprendizaje
                    </h2>

                    <p class="mt-2 text-sm text-slate-500">
                        Utiliza actividades dinámicas para mantener motivados a tus estudiantes.
                    </p>
                </div>

                <div class="hidden rounded-2xl bg-emerald-50 px-4 py-3 text-2xl sm:block">
                    🎯
                </div>

            </div>


            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">

                <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4">
                    <div class="text-3xl">🔤</div>

                    <h3 class="mt-3 font-black text-slate-900">
                        Sopa de letras
                    </h3>

                    <p class="mt-1 text-xs leading-5 text-slate-600">
                        Refuerza conceptos y vocabulario.
                    </p>
                </div>


                <div class="rounded-2xl border border-teal-100 bg-teal-50 p-4">
                    <div class="text-3xl">🧩</div>

                    <h3 class="mt-3 font-black text-slate-900">
                        Crucigrama
                    </h3>

                    <p class="mt-1 text-xs leading-5 text-slate-600">
                        Estimula la memoria y el razonamiento.
                    </p>
                </div>


                <div class="rounded-2xl border border-cyan-100 bg-cyan-50 p-4">
                    <div class="text-3xl">🔗</div>

                    <h3 class="mt-3 font-black text-slate-900">
                        Conecta los puntos
                    </h3>

                    <p class="mt-1 text-xs leading-5 text-slate-600">
                        Desarrolla la atención y la lógica.
                    </p>
                </div>


                <div class="rounded-2xl border border-amber-100 bg-amber-50 p-4">
                    <div class="text-3xl">🏆</div>

                    <h3 class="mt-3 font-black text-slate-900">
                        Quiz
                    </h3>

                    <p class="mt-1 text-xs leading-5 text-slate-600">
                        Evalúa conocimientos de forma divertida.
                    </p>
                </div>

            </div>

        </section>


        <!-- Cerrar sesión -->
        <div class="mt-8 flex justify-center">

            <form action="{{ url('/logout') }}" method="POST">
                @csrf

                <button
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-2xl border border-red-200 bg-white px-6 py-3 text-sm font-black text-red-600 shadow-sm transition hover:border-red-300 hover:bg-red-50"
                >
                    <span>↪</span>
                    Cerrar sesión
                </button>
            </form>

        </div>

    </main>

</body>

</html>