<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Alumnos - {{ $class->name }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-800">

    <!-- Barra superior -->
    <header class="border-b border-slate-200 bg-white shadow-sm">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-5 py-4 lg:px-8">

            <a href="{{ route('teacher.classes.index') }}" class="flex items-center gap-3">
                <div
                    class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 text-xl font-black text-white shadow-lg shadow-emerald-200">
                    K
                </div>

                <div>
                    <p class="text-xl font-black tracking-tight text-slate-900">
                        Klassio
                    </p>

                    <p class="text-xs font-medium text-slate-500">
                        Panel del profesor
                    </p>
                </div>
            </a>

            <div class="flex flex-wrap items-center gap-2">

                <a href="{{ route('teacher.classes.show', $class->id) }}"
                    class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-600 transition hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-700">
                    ← Volver a la clase
                </a>

                <a href="{{ route('teacher.classes.index') }}"
                    class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-bold text-white shadow-md shadow-emerald-200 transition hover:bg-emerald-700">
                    Mis clases
                </a>

            </div>
        </div>
    </header>

    <main class="mx-auto max-w-7xl space-y-8 px-5 py-8 lg:px-8">

        <!-- Encabezado -->
        <section
            class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-teal-600 to-cyan-700 p-7 text-white shadow-xl shadow-emerald-200 md:p-10">

            <div class="absolute -right-16 -top-20 h-64 w-64 rounded-full bg-white/10"></div>
            <div class="absolute -bottom-28 right-24 h-72 w-72 rounded-full bg-white/10"></div>

            <div class="relative z-10 max-w-3xl">

                <div
                    class="mb-4 inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-2 text-sm font-bold text-emerald-50 backdrop-blur">
                    <span>👥</span>
                    Gestión de alumnos
                </div>

                <h1 class="text-3xl font-black tracking-tight md:text-4xl">
                    Alumnos de {{ $class->name }}
                </h1>

                <p class="mt-3 max-w-2xl text-sm leading-6 text-emerald-50 md:text-base">
                    Consulta los alumnos inscritos en esta clase y administra
                    cuidadosamente sus accesos al grupo.
                </p>

                <div class="mt-6 inline-flex items-center gap-3 rounded-2xl bg-white/15 px-4 py-3 backdrop-blur">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 text-lg">
                        🎓
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-emerald-100">
                            Total de alumnos
                        </p>

                        <p class="text-2xl font-black">
                            {{ $students->count() }}
                        </p>
                    </div>
                </div>

            </div>
        </section>

        <!-- Mensajes -->
        @if (session('success'))
            <div
                class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800 shadow-sm">
                <div
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-600 font-bold text-white">
                    ✓
                </div>

                <div>
                    <p class="font-bold">
                        Operación completada
                    </p>

                    <p class="mt-1 text-sm">
                        {{ session('success') }}
                    </p>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800 shadow-sm">
                <div
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-red-600 font-bold text-white">
                    !
                </div>

                <div>
                    <p class="font-bold">
                        No se pudo completar la operación
                    </p>

                    <p class="mt-1 text-sm">
                        {{ session('error') }}
                    </p>
                </div>
            </div>
        @endif

        <!-- Contenido principal -->
        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm md:p-8">

            <div class="mb-6 flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-5">

                <div>
                    <p class="text-xs font-black uppercase tracking-widest text-emerald-600">
                        Lista de estudiantes
                    </p>

                    <h2 class="mt-1 text-2xl font-black text-slate-900">
                        Alumnos inscritos
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Revisa la información de cada alumno registrado en esta clase.
                    </p>
                </div>

                <div class="rounded-2xl bg-emerald-50 px-4 py-3 text-center">
                    <p class="text-2xl font-black text-emerald-700">
                        {{ $students->count() }}
                    </p>

                    <p class="text-xs font-bold text-emerald-600">
                        Inscritos
                    </p>
                </div>

            </div>

            @if ($students->isEmpty())

                <!-- Estado vacío -->
                <div class="rounded-3xl border-2 border-dashed border-slate-200 bg-slate-50 px-6 py-14 text-center">

                    <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-emerald-100 text-4xl">
                        🎒
                    </div>

                    <h3 class="mt-5 text-xl font-black text-slate-900">
                        Aún no hay alumnos inscritos
                    </h3>

                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                        Comparte el código de acceso de tu clase para que los alumnos
                        puedan unirse desde su cuenta.
                    </p>

                    <a href="{{ route('teacher.classes.show', $class->id) }}"
                        class="mt-6 inline-flex items-center justify-center rounded-xl bg-emerald-600 px-5 py-3 text-sm font-bold text-white shadow-md shadow-emerald-200 transition hover:bg-emerald-700">
                        Volver a la clase
                    </a>

                </div>
            @else
                <!-- Lista de alumnos -->
                <div class="grid gap-4 md:grid-cols-2">

                    @foreach ($students as $enrollment)
                        <article
                            class="group rounded-2xl border border-slate-200 bg-slate-50 p-5 transition hover:-translate-y-1 hover:border-emerald-300 hover:bg-white hover:shadow-lg">

                            <div class="flex items-start gap-4">

                                <!-- Inicial -->
                                <div
                                    class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 text-xl font-black text-white shadow-md shadow-emerald-100">
                                    {{ strtoupper(substr($enrollment->student->name, 0, 1)) }}
                                </div>

                                <div class="min-w-0 flex-1">

                                    <h3 class="truncate text-base font-black text-slate-900">
                                        {{ $enrollment->student->name }}
                                    </h3>

                                    <p class="mt-1 break-all text-sm text-slate-500">
                                        {{ $enrollment->student->email }}
                                    </p>

                                    <div
                                        class="mt-3 inline-flex items-center gap-2 rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">
                                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                        Alumno inscrito
                                    </div>

                                </div>

                            </div>

                            <div class="mt-5 border-t border-slate-200 pt-4">

                                <form
                                    action="{{ route('teacher.classes.students.remove', [
                                        'id' => $class->id,
                                        'studentId' => $enrollment->student->id,
                                    ]) }}"
                                    method="POST"
                                    onsubmit="return confirm('¿Seguro que deseas retirar a este alumno de la clase?');">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                        class="flex w-full items-center justify-center gap-2 rounded-xl border border-red-200 bg-white px-4 py-3 text-sm font-bold text-red-600 transition hover:border-red-300 hover:bg-red-50">
                                        <span>🗑️</span>
                                        Retirar alumno
                                    </button>
                                </form>

                            </div>

                        </article>
                    @endforeach

                </div>

            @endif

            @if ($students->hasPages())
                <div class="mt-8">
                    {{ $students->links() }}
                </div>
            @endif


        </section>

        <!-- Ayuda inferior -->
        <section class="rounded-2xl border border-cyan-100 bg-cyan-50 p-5">

            <div class="flex items-start gap-3">

                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-cyan-600 text-xl text-white">
                    💡
                </div>

                <div>
                    <h3 class="font-black text-cyan-900">
                        Recomendación
                    </h3>

                    <p class="mt-1 text-sm leading-6 text-cyan-800">
                        Antes de retirar a un alumno, verifica que sea la persona correcta.
                        Esta acción puede afectar su acceso a las actividades y materiales
                        de la clase.
                    </p>
                </div>

            </div>

        </section>

    </main>

</body>

</html>
