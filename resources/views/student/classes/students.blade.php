<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Compañeros | Klassio</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-800">

    <div class="flex min-h-screen">

        <!-- SIDEBAR -->
        <aside class="hidden w-72 flex-col bg-white shadow-xl lg:flex">

            <div class="flex items-center gap-3 border-b border-slate-200 px-6 py-6">

                <div
                    class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-600 text-2xl font-black text-white shadow-lg">
                    K
                </div>

                <div>
                    <h1 class="text-2xl font-black tracking-tight text-indigo-700">
                        Klassio
                    </h1>

                    <p class="text-xs font-medium text-slate-500">
                        Aprende jugando
                    </p>
                </div>

            </div>

            <nav class="flex-1 space-y-2 px-4 py-6">

                <a href="{{ route('student.dashboard') }}"
                    class="flex items-center gap-3 rounded-xl px-4 py-3 font-semibold text-slate-600 transition hover:bg-indigo-50 hover:text-indigo-700">
                    <span class="text-xl">🏠</span>
                    Inicio
                </a>

                <a href="{{ route('student.classes.index') }}"
                    class="flex items-center gap-3 rounded-xl bg-indigo-600 px-4 py-3 font-semibold text-white shadow-md">
                    <span class="text-xl">📚</span>
                    Mis clases
                </a>

                <a href="{{ route('student.classes.index') }}"
                    class="flex items-center gap-3 rounded-xl px-4 py-3 font-semibold text-slate-600 transition hover:bg-indigo-50 hover:text-indigo-700">
                    <span class="text-xl">🎮</span>
                    Juegos educativos
                </a>

            </nav>

            <div class="border-t border-slate-200 p-4">

                <div class="mb-4 rounded-2xl bg-indigo-50 p-4">

                    <p class="text-xs font-semibold uppercase tracking-wide text-indigo-500">
                        Estudiante
                    </p>

                    <p class="mt-1 truncate font-bold text-indigo-800">
                        {{ auth()->user()->name }}
                    </p>

                </div>

                <form method="POST" action="{{ url('/logout') }}">
                    @csrf

                    <button type="submit"
                        class="flex w-full items-center justify-center gap-2 rounded-xl border border-red-200 px-4 py-3 font-semibold text-red-600 transition hover:bg-red-50">
                        <span>↪</span>
                        Cerrar sesión
                    </button>
                </form>

            </div>

        </aside>

        <!-- CONTENIDO PRINCIPAL -->
        <main class="flex-1">

            <!-- HEADER -->
            <header class="border-b border-slate-200 bg-white px-5 py-5 shadow-sm sm:px-8">

                <div class="mx-auto flex max-w-7xl items-center justify-between gap-4">

                    <div>
                        <p class="text-sm font-semibold text-indigo-600">
                            Comunidad de la clase
                        </p>

                        <h2 class="mt-1 text-2xl font-black text-slate-900 sm:text-3xl">
                            Compañeros
                        </h2>
                    </div>

                    <a href="{{ route('student.class.show', $class->id) }}"
                        class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm transition hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-700">
                        ← Volver a la clase
                    </a>

                    @if ($students->hasPages())
                        <div class="mt-8">
                            {{ $students->links() }}
                        </div>
                    @endif

                </div>

            </header>

            <div class="mx-auto max-w-7xl space-y-8 px-5 py-8 sm:px-8">

                <!-- BANNER -->
                <section
                    class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-indigo-700 via-indigo-600 to-violet-600 p-6 text-white shadow-xl sm:p-8">

                    <div class="relative z-10 max-w-3xl">

                        <div
                            class="mb-4 inline-flex items-center gap-2 rounded-full bg-white/20 px-4 py-2 text-sm font-bold backdrop-blur">
                            👥 Comunidad estudiantil
                        </div>

                        <h1 class="text-3xl font-black sm:text-4xl">
                            Compañeros de {{ $class->name }}
                        </h1>

                        <p class="mt-3 text-sm leading-6 text-indigo-100 sm:text-base">
                            Conoce a los estudiantes que forman parte de esta clase
                            y comparte el aprendizaje con ellos.
                        </p>

                    </div>

                    <div class="absolute -right-8 -top-10 text-[150px] opacity-20">
                        🎓
                    </div>

                    <div class="absolute -bottom-16 right-24 h-40 w-40 rounded-full bg-white/10"></div>
                    <div class="absolute -right-20 bottom-0 h-56 w-56 rounded-full bg-white/10"></div>

                </section>

                <!-- LISTA DE COMPAÑEROS -->
                <section class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">

                    <div class="mb-6 flex items-center gap-3">

                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-100 text-2xl">
                            🧑‍🎓
                        </div>

                        <div>
                            <h2 class="text-xl font-black text-slate-900 sm:text-2xl">
                                Lista de compañeros
                            </h2>

                            <p class="text-sm text-slate-500">
                                Estudiantes inscritos en esta clase.
                            </p>
                        </div>

                    </div>

                    @if ($students->isEmpty())

                        <!-- ESTADO VACÍO -->
                        <div
                            class="rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 px-6 py-12 text-center">

                            <div class="text-5xl">
                                👤
                            </div>

                            <h3 class="mt-4 text-lg font-black text-slate-800">
                                No hay compañeros todavía
                            </h3>

                            <p class="mt-2 text-sm text-slate-500">
                                Cuando otros estudiantes se unan a esta clase,
                                aparecerán aquí.
                            </p>

                        </div>
                    @else
                        <!-- CONTADOR -->
                        <div
                            class="mb-5 inline-flex rounded-full bg-indigo-50 px-4 py-2 text-sm font-bold text-indigo-700">
                            👥 {{ $students->count() }} compañero(s)
                        </div>

                        <!-- TARJETAS -->
                        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">

                            @foreach ($students as $enrollment)
                                <article
                                    class="group rounded-2xl border border-slate-200 bg-slate-50 p-5 transition hover:-translate-y-1 hover:border-indigo-300 hover:bg-indigo-50 hover:shadow-md">

                                    <div class="flex items-center gap-4">

                                        <div
                                            class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-indigo-600 text-xl font-black text-white shadow-md">
                                            {{ strtoupper(substr($enrollment->student->name, 0, 1)) }}
                                        </div>

                                        <div class="min-w-0">

                                            <h3 class="truncate font-black text-slate-800 group-hover:text-indigo-800">
                                                {{ $enrollment->student->name }}
                                            </h3>

                                            <p class="mt-1 break-all text-sm text-slate-500">
                                                {{ $enrollment->student->email }}
                                            </p>

                                        </div>

                                    </div>

                                    <div class="mt-4 border-t border-slate-200 pt-3">

                                        <span class="text-xs font-bold uppercase tracking-wide text-indigo-600">
                                            Estudiante de Klassio
                                        </span>

                                    </div>

                                </article>
                            @endforeach

                        </div>

                    @endif

                </section>

                <!-- BOTÓN INFERIOR -->
                <div class="flex justify-center">

                    <a href="{{ route('student.class.show', $class->id) }}"
                        class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-6 py-3 font-bold text-white shadow-md transition hover:bg-indigo-700">
                        ← Regresar a la clase
                    </a>

                </div>

            </div>

        </main>

    </div>

</body>

</html>
