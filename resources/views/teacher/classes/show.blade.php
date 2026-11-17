@extends('teacher.layouts.app')

@section('title', e($class->name))

@section('page-title', e($class->name))

@section('page-actions')
    <a href="{{ route('teacher.classes.index') }}"
       class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2 sm:w-auto">
        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
        </svg>
        Volver
    </a>

    <a href="{{ route('teacher.classes.edit', $class->id) }}"
       class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-700 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2 sm:w-auto">
        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
        </svg>
        Editar
    </a>

    @if ($class->status === 'active')
        <form action="{{ route('teacher.classes.archive', $class->id) }}" method="POST">
            @csrf
            @method('PATCH')

            <button type="submit"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-rose-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-rose-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-500 focus-visible:ring-offset-2 sm:w-auto">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M21 7.5H3m4.5 0V5.25A2.25 2.25 0 0 1 9.75 3h4.5a2.25 2.25 0 0 1 2.25 2.25V7.5" />
                </svg>
                Archivar
            </button>
        </form>
    @else
        <form action="{{ route('teacher.classes.unarchive', $class->id) }}" method="POST">
            @csrf
            @method('PATCH')

            <button type="submit"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-violet-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-violet-500 focus-visible:ring-offset-2 sm:w-auto">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 0 13.803 3.7l3.181-3.182m0-4.991v4.99" />
                </svg>
                Desarchivar
            </button>
        </form>
    @endif
@endsection

@section('content')

    {{-- Los mensajes flash (éxito/error) los renderiza el layout teacher.layouts.app. --}}

    {{-- Tarjeta de identidad de la clase --}}
    <section class="mb-6">

        <div class="rounded-2xl border border-emerald-100 bg-emerald-50/50 p-6">

            <div class="flex flex-wrap items-center gap-x-3 gap-y-2">

                <h2 class="break-words text-xl font-bold tracking-tight text-slate-900 sm:text-2xl">
                    {{ $class->name }}
                </h2>

                @if ($class->status === 'active')
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-200">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500" aria-hidden="true"></span>
                        Clase activa
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200">
                        <span class="h-1.5 w-1.5 rounded-full bg-slate-400" aria-hidden="true"></span>
                        Clase archivada
                    </span>
                @endif

                <span class="text-xs font-medium text-slate-500">
                    Creada el {{ $class->created_at->format('d/m/Y') }}
                </span>

            </div>

            <p class="mt-4 leading-7 text-slate-600">
                {{ $class->description ?: 'Sin descripción disponible para esta clase.' }}
            </p>

        </div>

    </section>


    {{-- Código de acceso: franja compacta --}}
    <section class="mb-6">

        <div class="rounded-2xl bg-emerald-700 p-5 text-white shadow-sm sm:p-6">

            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

                <div class="min-w-0">
                    <p class="text-center text-xs font-bold uppercase tracking-widest text-emerald-200 sm:text-left">
                        Acceso de estudiantes
                    </p>

                    <div class="mx-auto mt-2 flex w-full items-center justify-center rounded-xl bg-white px-4 py-3 shadow-sm sm:mx-0 sm:w-auto sm:justify-start sm:py-2.5">
                        <p class="break-all text-center font-mono text-3xl font-bold tracking-[0.2em] text-emerald-700">
                            {{ $class->code }}
                        </p>
                    </div>

                    <p class="mt-3 text-center text-xs text-emerald-100 sm:hidden">
                        Compártelo con tus estudiantes para que ingresen a la clase
                    </p>
                </div>

                <form action="{{ route('teacher.classes.regenerate-code', $class->id) }}" method="POST" class="shrink-0">
                    @csrf
                    @method('PATCH')

                    <button type="submit"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white ring-1 ring-white/40 transition hover:bg-emerald-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-white sm:w-auto">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 0 13.803 3.7l3.181-3.182m0-4.991v4.99" />
                        </svg>
                        Regenerar código
                    </button>

                </form>

            </div>

        </div>

    </section>


    {{-- Accesos rápidos: tiles compactos con datos --}}
    <section>

        <div class="grid gap-3 md:grid-cols-3">

            <a href="{{ route('teacher.classes.students', $class->id) }}"
               class="group flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-emerald-200 hover:shadow-md">

                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100 transition group-hover:bg-emerald-600 group-hover:text-white group-hover:ring-emerald-600">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                    </svg>
                </div>

                <div class="min-w-0 flex-1">
                    <p class="font-bold text-slate-900">
                        Alumnos
                    </p>

                    <p class="text-xs text-slate-500">
                        {{ $class->enrollments()->count() }} {{ $class->enrollments()->count() === 1 ? 'inscrito' : 'inscritos' }}
                    </p>
                </div>

                <svg class="h-5 w-5 shrink-0 text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>

            </a>


            <a href="{{ route('teacher.activities.index', $class->id) }}"
               class="group flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-violet-200 hover:shadow-md">

                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-700 ring-1 ring-violet-100 transition group-hover:bg-violet-600 group-hover:text-white group-hover:ring-violet-600">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 6.087c0-.355.186-.676.401-.959.221-.29.349-.634.349-1.003 0-1.036-1.007-1.875-2.25-1.875s-2.25.84-2.25 1.875c0 .369.128.713.349 1.003.215.283.401.604.401.959v0a.64.64 0 0 1-.657.643 48.39 48.39 0 0 1-3.409-.042.75.75 0 0 1-.707-.632 48.264 48.264 0 0 1-.034-2.897.75.75 0 0 1 .515-.707 48.334 48.334 0 0 1 2.887-.085v0c.355 0 .676-.186.959-.401.29-.221.634-.349 1.003-.349 1.036 0 1.875-1.007 1.875-2.25S12.535 1.5 11.5 1.5s-1.875 1.007-1.875 2.25c0 .369.128.713.349 1.003.215.283.401.604.401.959v0c0 .355-.186.676-.401.959-.221.29-.349.634-.349 1.003 0 1.036-1.007 1.875-2.25 1.875s-1.875-1.007-1.875-2.25c0-.369.128-.713.349-1.003.215-.283.401-.604.401-.959v0a.64.64 0 0 0-.657-.643 48.39 48.39 0 0 0-3.409.042.75.75 0 0 0-.707.632 48.264 48.264 0 0 0-.034 2.897.75.75 0 0 0 .515.707c.438.154.902.226 1.397.226v0c.466 0 .872-.054 1.21-.14v5.628c0 1.499 1.193 2.755 2.691 2.755h.002c.325 0 .591.266.591.591v4.875c0 .414.336.75.75.75h.006a.75.75 0 0 0 .75-.75v-4.875c0-.325.266-.591.591-.591h.002c1.498 0 2.691-1.256 2.691-2.755V9.75c.338.086.744.14 1.21.14v0c.495 0 .959-.072 1.397-.226a.75.75 0 0 0 .515-.707 48.264 48.264 0 0 0-.034-2.897.75.75 0 0 0-.707-.632 48.39 48.39 0 0 0-3.409-.042.64.64 0 0 0-.657.643Z" />
                    </svg>
                </div>

                <div class="min-w-0 flex-1">
                    <p class="font-bold text-slate-900">
                        Actividades
                    </p>

                    <p class="text-xs text-slate-500">
                        {{ $class->activities()->count() }} {{ $class->activities()->count() === 1 ? 'creada' : 'creadas' }}
                    </p>
                </div>

                <svg class="h-5 w-5 shrink-0 text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-violet-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>

            </a>


            <a href="{{ route('teacher.activities.create', $class->id) }}"
               class="group flex items-center gap-4 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-emerald-300 hover:shadow-md">

                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-600 text-white transition group-hover:bg-emerald-700">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </div>

                <div class="min-w-0 flex-1">
                    <p class="font-bold text-emerald-800">
                        Crear actividad
                    </p>

                    <p class="truncate text-xs text-emerald-700/70">
                        Nueva para esta clase
                    </p>
                </div>

                <svg class="h-5 w-5 shrink-0 text-emerald-400 transition group-hover:translate-x-0.5 group-hover:text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>

            </a>

        </div>

    </section>

@endsection
