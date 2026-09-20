<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Mis clases | Klassio</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>


<body class="min-h-screen bg-slate-50 text-slate-900">


    <!-- =====================================
         CONTENEDOR PRINCIPAL
    ====================================== -->

    <div class="flex min-h-screen flex-col lg:flex-row">


        <!-- =====================================
             BARRA LATERAL
        ====================================== -->

        <aside class="hidden w-64 shrink-0 flex-col border-r border-slate-200 bg-white lg:sticky lg:top-0 lg:flex lg:h-screen lg:overflow-y-auto">


            <!-- LOGO -->

            <div class="flex h-24 items-center gap-3 px-7">

                <div
                    class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-600 text-xl font-extrabold text-white">

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



                <!-- INICIO -->

                <a href="{{ route('student.dashboard') }}"
                    class="mb-2 flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-slate-500 transition hover:bg-slate-50 hover:text-indigo-600">

                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M3 12l9-9 9 9M5 10v10h14V10" />

                    </svg>


                    Inicio

                </a>



                <!-- MIS CLASES ACTIVO -->

                <a href="{{ route('student.classes.index') }}"
                    class="mb-2 flex items-center gap-3 rounded-xl bg-indigo-50 px-4 py-3 text-sm font-bold text-indigo-600">

                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M4 5a2 2 0 012-2h12a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V5zM8 7h8M8 11h8M8 15h5" />

                    </svg>


                    Mis clases

                </a>



                <!-- JUEGOS -->

                <a href="{{ route('student.dashboard') }}#juegos"
                    class="mb-2 flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-slate-500 transition hover:bg-slate-50 hover:text-indigo-600">

                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M6 8h12a4 4 0 014 4v3a4 4 0 01-4 4h-1l-3-3H10l-3 3H6a4 4 0 01-4-4v-3a4 4 0 014-4zM8 12v4M6 14h4M16 13h.01M19 11h.01" />

                    </svg>


                    Juegos educativos

                </a>



                <!-- ACTIVIDADES -->

                <a href="{{ route('student.dashboard') }}#actividades"
                    class="mb-2 flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-slate-500 transition hover:bg-slate-50 hover:text-indigo-600">

                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M9 5h6M9 3h6v4H9V3zM6 7h12a2 2 0 012 2v10a2 2 0 01-2 2H6a2 2 0 01-2-2V9a2 2 0 012-2zM8 12h8M8 16h5" />

                    </svg>


                    Actividades

                </a>

            </nav>



            <!-- PERFIL -->

            <div class="border-t border-slate-100 p-4">


                <div class="mb-4 flex items-center gap-3 rounded-xl bg-slate-50 p-3">


                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-100 font-bold text-indigo-600">

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



                <!-- CERRAR SESIÓN -->

                <form method="POST" action="/logout">

                    @csrf


                    <button type="submit"
                        class="flex w-full items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium text-slate-500 transition hover:bg-rose-50 hover:text-rose-600">

                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M10 17l5-5-5-5M15 12H3M21 3v18" />

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

            <header class="sticky top-4 z-20 mx-4 mt-4 flex h-20 items-center justify-between rounded-2xl border border-slate-200 bg-white px-5 shadow-sm sm:mx-6 sm:px-8">


                <div>

                    <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">

                        Mi espacio académico

                    </p>


                    <h2 class="mt-1 text-lg font-bold text-slate-800">

                        Mis clases

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



                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-600 font-bold text-white">

                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}

                    </div>

                </div>

            </header>



            <!-- CONTENIDO -->

            <div class="mx-auto max-w-7xl space-y-8 px-5 py-8 sm:px-8 lg:px-10">



                <!-- ENCABEZADO -->

                <section
                    class="relative overflow-hidden rounded-3xl bg-indigo-600 px-6 py-8 text-white shadow-sm sm:px-10 sm:py-10">


                    <div class="relative z-10 max-w-2xl">


                        <span
                            class="inline-flex rounded-full bg-white/15 px-3 py-1.5 text-[11px] font-bold uppercase tracking-widest text-indigo-100">

                            Espacio académico

                        </span>


                        <h1 class="mt-5 text-3xl font-extrabold leading-tight tracking-tight sm:text-4xl">

                            Mis clases 📚

                        </h1>


                        <p class="mt-4 max-w-lg text-sm leading-7 text-indigo-100 sm:text-base">

                            Consulta las materias en las que estás inscrito,
                            revisa su información y continúa aprendiendo.

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

                                Mi aprendizaje

                            </p>


                            <h2 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-900">

                                Clases inscritas

                            </h2>


                            <p class="mt-2 text-sm text-slate-500">

                                Selecciona una clase para consultar su contenido.

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

                                            {{ $class->status_label }}

                                        </span>

                                    </div>



                                    <!-- BOTÓN -->

                                    <a href="{{ route('student.class.show', $class->id) }}"
                                        class="mt-6 inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3.5 text-sm font-bold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-100">

                                        Ver clase

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

                                    Aún no tienes clases

                                </h3>


                                <p class="mx-auto mt-3 max-w-md text-sm leading-6 text-slate-500">

                                    Todavía no estás inscrito en ninguna clase.
                                    Puedes unirte utilizando el código proporcionado
                                    por tu maestro.

                                </p>


                                <a href="{{ route('student.dashboard') }}"
                                    class="mt-6 inline-flex items-center justify-center rounded-xl bg-indigo-600 px-6 py-3.5 text-sm font-bold text-white transition hover:bg-indigo-700">

                                    Ir al dashboard →

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

        </main>

    </div>

</body>

</html>
