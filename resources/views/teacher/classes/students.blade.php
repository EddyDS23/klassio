@extends('teacher.layouts.app')

@section('title', 'Alumnos - ' . $class->name)
@section('page-title', 'Alumnos - ' . $class->name)

@section('page-actions')
    <a href="{{ route('teacher.classes.show', $class->id) }}"
       class="inline-flex self-start items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2">
        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
        </svg>
        Volver a la clase
    </a>
@endsection

@section('content')
    <div id="teacher-enrolled-students" class="space-y-6">

        <!-- Contenido principal -->
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm md:p-8">

            <div class="mb-6 flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-5">

                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-emerald-700">
                        Lista de estudiantes
                    </p>

                    <h2 class="mt-1 text-xl font-bold text-slate-900">
                        Alumnos inscritos
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Revisa la información de cada alumno registrado en esta clase.
                    </p>
                </div>

                <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-4 py-2 text-sm font-bold text-emerald-700 ring-1 ring-emerald-100">
                    {{ $students->count() }}
                    {{ $students->count() === 1 ? 'alumno' : 'alumnos' }}
                </span>

            </div>

            @if ($students->isEmpty())

                <!-- Estado vacío -->
                <div class="rounded-2xl border border-slate-200 bg-white p-12 text-center">

                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-400">
                        <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.628 48.628 0 0 1 12 20.904a48.628 48.628 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                        </svg>
                    </div>

                    <h3 class="mt-4 text-lg font-bold text-slate-900">
                        Aún no hay alumnos inscritos
                    </h3>

                    <p class="mx-auto mt-2 max-w-sm text-sm text-slate-500">
                        Comparte el código de acceso de tu clase para que los alumnos
                        puedan unirse desde su cuenta.
                    </p>

                    <a href="{{ route('teacher.classes.show', $class->id) }}"
                       class="mt-6 inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-900">
                        Volver a la clase
                    </a>

                </div>
            @else
                <!-- Lista de alumnos -->
                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">

                    @foreach ($students as $enrollment)
                        <article
                            class="rounded-2xl border border-slate-200 bg-slate-50 p-4 transition hover:border-emerald-200 hover:bg-white hover:shadow-md"
                            data-student-card>

                            <div class="flex items-center gap-3">

                                <!-- Inicial -->
                                <div
                                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-emerald-600 text-lg font-bold text-white shadow-sm shadow-emerald-200">
                                    {{ strtoupper(substr($enrollment->student->name, 0, 1)) }}
                                </div>

                                <div class="min-w-0 flex-1">

                                    <h3 class="truncate text-base font-bold text-slate-900">
                                        {{ $enrollment->student->name }}
                                    </h3>

                                    <p class="mt-0.5 break-all text-xs leading-5 text-slate-500 sm:text-sm">
                                        {{ $enrollment->student->email }}
                                    </p>

                                </div>

                                <button type="button" data-remove-toggle
                                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl text-slate-400 transition hover:bg-red-50 hover:text-red-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500"
                                    aria-expanded="false"
                                    aria-controls="remove-confirm-{{ $enrollment->student->id }}"
                                    aria-label="Retirar a {{ $enrollment->student->name }} de la clase">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </button>

                            </div>

                            <!-- Confirmación de retirada (dos pasos, inline) -->
                            <div id="remove-confirm-{{ $enrollment->student->id }}"
                                class="mt-3 hidden rounded-xl border border-red-200 bg-red-50 p-3"
                                data-confirm-panel>

                                <p class="text-sm font-semibold text-red-800">
                                    ¿Retirar a
                                    <span class="font-bold">{{ $enrollment->student->name }}</span>
                                    de la clase?
                                </p>

                                <p class="mt-0.5 text-xs text-red-700/80">
                                    Perderá el acceso a las actividades y materiales de esta clase.
                                </p>

                                <div class="mt-3 flex gap-2">

                                    <form action="{{ route('teacher.classes.students.remove', [
                                            'id' => $class->id,
                                            'studentId' => $enrollment->student->id,
                                        ]) }}"
                                          method="POST" class="flex-1">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="flex w-full items-center justify-center gap-2 rounded-xl bg-red-600 px-3 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2">
                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                            Sí, retirar
                                        </button>
                                    </form>

                                    <button type="button" data-remove-cancel
                                        class="rounded-xl border border-red-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-red-50 hover:text-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2">
                                        Cancelar
                                    </button>

                                </div>

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

    </div>
@endsection

@push('scripts')
    @if (! $students->isEmpty())
        {{-- Confirmación en dos pasos para retirar alumnos --}}
        <script>
            (function () {
                var cards = Array.prototype.slice.call(document.querySelectorAll('[data-student-card]'));

                function closeConfirm(card) {
                    var panel = card.querySelector('[data-confirm-panel]');
                    var toggle = card.querySelector('[data-remove-toggle]');

                    if (panel) {
                        panel.classList.add('hidden');
                    }

                    if (toggle) {
                        toggle.setAttribute('aria-expanded', 'false');
                    }
                }

                cards.forEach(function (card) {
                    var toggle = card.querySelector('[data-remove-toggle]');
                    var panel = card.querySelector('[data-confirm-panel]');
                    var cancel = card.querySelector('[data-remove-cancel]');

                    if (! toggle || ! panel) {
                        return;
                    }

                    toggle.addEventListener('click', function () {
                        var wasOpen = toggle.getAttribute('aria-expanded') === 'true';

                        // Solo un confirm abierto a la vez.
                        cards.forEach(closeConfirm);

                        if (! wasOpen) {
                            panel.classList.remove('hidden');
                            toggle.setAttribute('aria-expanded', 'true');
                        }
                    });

                    if (cancel) {
                        cancel.addEventListener('click', function () {
                            closeConfirm(card);
                        });
                    }
                });
            })();
        </script>
    @endif
@endpush
