@extends('teacher.layouts.app')

@section('title', 'Alumnos - ' . $class->name)
@section('page-title', 'Alumnos - ' . $class->name)

@section('page-actions')
    <a href="{{ route('teacher.classes.show', $class->id) }}"
        class="klassio-back-button">
        ← Volver a la clase
    </a>

    <a href="{{ route('teacher.classes.index') }}"
        class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-bold text-white shadow-md shadow-emerald-200 transition hover:bg-emerald-700">
        Mis clases
    </a>
@endsection

@push('styles')
    <style>
        /* Bootstrap CDN fuerza p-4/p-5 a 1.5rem con !important; fija el padding compacto de tarjeta a 16px. */
        #teacher-enrolled-students .enrolled-student-card { padding: 1rem !important; }
        @media (max-width: 767.98px) {
            /* Bootstrap aplica !important a px-5, p-5 y gap-4. */
            #teacher-enrolled-students { padding-inline: 1rem !important; }
            #teacher-enrolled-students .enrolled-students-section { padding: 1rem; }
            #teacher-enrolled-students .enrolled-students-list { grid-template-columns: minmax(0, 1fr); }
            #teacher-enrolled-students .enrolled-student-card { min-width: 0; padding: 1rem !important; }

            #teacher-enrolled-students .enrolled-student-profile {
                display: grid;
                grid-template-columns: 2.75rem minmax(0, 1fr);
                gap: .5rem .75rem !important;
            }
            #teacher-enrolled-students .enrolled-student-avatar { grid-column: 1; grid-row: 1 / span 2; }
            #teacher-enrolled-students .enrolled-student-details { display: contents; }
            #teacher-enrolled-students .enrolled-student-name,
            #teacher-enrolled-students .enrolled-student-email {
                grid-column: 2;
                min-width: 0;
                margin: 0 !important;
                white-space: normal;
                overflow: visible;
                text-overflow: clip;
                overflow-wrap: anywhere;
                word-break: normal;
            }
            #teacher-enrolled-students .enrolled-student-status {
                grid-column: 1 / -1;
                justify-self: start;
                max-width: 100%;
                margin-top: .25rem !important;
            }
            #teacher-enrolled-students .enrolled-student-status > span { flex-shrink: 0; }
            #teacher-enrolled-students .enrolled-student-actions {
                margin-top: .75rem !important;
                padding-top: .75rem !important;
            }
        }
    </style>
@endpush

@section('content')
        <div id="teacher-enrolled-students" class="mx-auto w-full max-w-7xl space-y-8">

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

            <!-- Contenido principal -->
            <section class="enrolled-students-section rounded-3xl border border-slate-200 bg-white p-6 shadow-sm md:p-8">

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
                            class="mt-6 klassio-back-button">
                            Volver a la clase
                        </a>

                    </div>
                @else
                    <!-- Lista de alumnos -->
                    <div class="enrolled-students-list grid gap-3 sm:grid-cols-2 xl:grid-cols-3">

                        @foreach ($students as $enrollment)
                            <article
                                class="enrolled-student-card group rounded-2xl border border-slate-200 bg-slate-50 p-4 transition hover:-translate-y-1 hover:border-emerald-300 hover:bg-white hover:shadow-lg">

                                <div class="enrolled-student-profile flex items-center gap-3">

                                    <!-- Inicial -->
                                    <div
                                        class="enrolled-student-avatar flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 text-lg font-black text-white shadow-md shadow-emerald-100">
                                        {{ strtoupper(substr($enrollment->student->name, 0, 1)) }}
                                    </div>

                                    <div class="enrolled-student-details min-w-0 flex-1">

                                        <h3 class="enrolled-student-name truncate text-base font-black text-slate-900">
                                            {{ $enrollment->student->name }}
                                        </h3>

                                        <p class="enrolled-student-email mt-0.5 break-all text-xs leading-5 text-slate-500 sm:text-sm">
                                            {{ $enrollment->student->email }}
                                        </p>

                                        <div
                                            class="enrolled-student-status mt-2 inline-flex items-center gap-2 rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-bold text-emerald-700">
                                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                            Alumno inscrito
                                        </div>

                                    </div>

                                </div>

                                <div class="enrolled-student-actions mt-3 border-t border-slate-200 pt-3">

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
                                            class="flex w-full items-center justify-center gap-2 rounded-xl border border-red-200 bg-white px-3 py-2.5 text-sm font-bold text-red-600 transition hover:border-red-300 hover:bg-red-50">
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

        </div>
@endsection
