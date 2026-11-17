@extends('teacher.layouts.app')

@section('title', 'Crear sala multijugador')

@section('page-title', 'Crear sala')

@section('content')

    <div class="mx-auto max-w-5xl">

        <div class="mb-6">
            @if ($selectedClass)
                <a href="{{ route('teacher.classes.show', $selectedClass->id) }}"
                   class="inline-flex self-start items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                    Volver a {{ $selectedClass->name }}
                </a>
            @else
                <a href="{{ route('teacher.dashboard') }}"
                   class="inline-flex self-start items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                    Volver al panel
                </a>
            @endif
        </div>

        {{-- Encabezado --}}
        <section class="mb-6 rounded-2xl bg-emerald-700 p-5 text-white shadow-sm sm:p-6">

            <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">

                <div class="min-w-0">
                    <p class="text-xs font-bold uppercase tracking-widest text-emerald-200">
                        Salas multijugador
                    </p>

                    <h2 class="mt-1 text-2xl font-bold tracking-tight sm:text-3xl">
                        Crear sala
                    </h2>

                    <p class="mt-2 flex items-center gap-1.5 text-sm font-bold text-emerald-100">
                        <svg class="h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.91 11.672a.375.375 0 0 1 0 .656l-5.603 3.113A.375.375 0 0 1 9.75 15.068v-6.3a.375.375 0 0 1 .557-.328l5.603 3.113Z" />
                        </svg>
                        {{ $selectedClass ? $selectedClass->name : 'Elige una clase para comenzar' }}
                    </p>
                </div>

                <div class="hidden h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-emerald-600 ring-1 ring-emerald-500 md:flex">
                    <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.91 11.672a.375.375 0 0 1 0 .656l-5.603 3.113A.375.375 0 0 1 9.75 15.068v-6.3a.375.375 0 0 1 .557-.328l5.603 3.113Z" />
                    </svg>
                </div>

            </div>

        </section>

        {{-- Mensaje del servidor --}}
        @if (session('error'))
            <div class="mb-6 flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50 p-4 shadow-sm sm:p-5">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-700 ring-1 ring-red-200">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                </div>
                <p class="text-sm text-red-800">{{ session('error') }}</p>
            </div>
        @endif

        {{-- Errores de validación --}}
        @if ($errors->any())
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 shadow-sm sm:p-5">

                <div class="flex items-center gap-3">

                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-700 ring-1 ring-red-200">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                        </svg>
                    </div>

                    <h3 class="text-sm font-bold text-red-800">
                        Revisa los siguientes datos
                    </h3>

                </div>

                <ul class="mt-3 list-disc space-y-1 pl-6 text-sm text-red-800">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>

            </div>
        @endif

        @php
            $typeLabels = [
                'crossword' => 'Crucigrama',
                'kahoot' => 'Kahoot',
                'word_search' => 'Sopa de letras',
                'matching' => 'Relacionar',
                'roulette' => 'Ruleta',
            ];

            $selectClass = 'w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm font-bold text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100';
        @endphp

        {{-- Paso 1: clase --}}
        <section class="mb-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">

            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-sm font-bold text-emerald-700 ring-1 ring-emerald-100">
                    1
                </div>

                <div class="min-w-0">
                    <h3 class="text-lg font-bold text-slate-900">
                        Elige la clase
                    </h3>

                    <p class="mt-0.5 text-sm text-slate-600">
                        Solo se listan tus clases activas, agrupadas por periodo.
                    </p>
                </div>
            </div>

            @if (count($classGroups) === 0)
                <div class="mt-4 flex flex-col gap-3 rounded-2xl border border-amber-200 bg-amber-50 p-4 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm text-amber-900">
                        Aún no tienes clases activas. Crea una clase para poder armar su sala.
                    </p>

                    <a href="{{ route('teacher.classes.create') }}"
                       class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl border border-amber-200 bg-white px-4 py-2.5 text-sm font-bold text-amber-800 transition hover:bg-amber-100">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Crear clase
                    </a>
                </div>
            @else
                <form method="GET" action="{{ route('teacher.game-sessions.create') }}" class="mt-4">
                    <label for="class_id" class="mb-1.5 block text-sm font-bold text-slate-700">
                        Clase
                    </label>

                    <select name="class_id" id="class_id" onchange="this.form.submit()" class="{{ $selectClass }}">
                        <option value="" @selected(! $selectedClass)>
                            Elige una clase…
                        </option>

                        @if ($selectedClass && $classes->doesntContain('id', $selectedClass->id))
                            {{-- Clase llegando por ?activity_id de una clase archivada --}}
                            <option value="{{ $selectedClass->id }}" selected>
                                {{ $selectedClass->name }} (archivada)
                            </option>
                        @endif

                        @foreach ($classGroups as $group)
                            <optgroup label="{{ $group['label'] }}{{ $group['isCurrent'] ? ' · periodo en curso' : '' }}">
                                @foreach ($group['classes'] as $classItem)
                                    <option value="{{ $classItem->id }}" @selected($selectedClass?->id === $classItem->id)>
                                        {{ $classItem->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>

                    <noscript>
                        <button type="submit"
                                class="mt-3 inline-flex w-full items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-50">
                            Ver actividades de esta clase
                        </button>
                    </noscript>
                </form>
            @endif

        </section>

        {{-- Paso 2: actividad y jugadores --}}
        <section class="mb-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">

            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-sm font-bold text-emerald-700 ring-1 ring-emerald-100">
                    2
                </div>

                <div class="min-w-0">
                    <h3 class="text-lg font-bold text-slate-900">
                        Elige la actividad
                    </h3>

                    <p class="mt-0.5 text-sm text-slate-600">
                        Actividades publicadas de la clase seleccionada.
                    </p>
                </div>
            </div>

            <div class="mt-4">
                @if (! $selectedClass)
                    <div class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-emerald-700 ring-1 ring-slate-200">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                            </svg>
                        </div>

                        <p class="text-sm text-slate-600">
                            Elige primero una clase: ahí verás únicamente sus actividades publicadas.
                        </p>
                    </div>
                @elseif ($activities->isEmpty())
                    <div class="flex items-start gap-3 rounded-2xl border border-amber-200 bg-amber-50 p-4">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-amber-700 ring-1 ring-amber-200">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                            </svg>
                        </div>

                        <p class="text-sm text-amber-900">
                            No tienes actividades publicadas en <strong>{{ $selectedClass->name }}</strong>.
                            Publica una actividad de esta clase para crear su sala.
                        </p>
                    </div>
                @else
                    <form method="POST" action="{{ route('teacher.game-sessions.store') }}">
                        @csrf

                        <label for="activity_id" class="mb-1.5 block text-sm font-bold text-slate-700">
                            Actividad publicada
                        </label>

                        <select name="activity_id" id="activity_id" required class="{{ $selectClass }} mb-4">
                            @foreach ($activities as $activity)
                                @php
                                    $typeLabel = $typeLabels[$activity->type] ?? 'Ruleta';
                                @endphp

                                <option value="{{ $activity->id }}" @selected($selectedActivityId === $activity->id)>
                                    {{ $activity->title }}@unless (str_contains($activity->title, $typeLabel))
                                        · {{ $typeLabel }}
                                    @endunless
                                </option>
                            @endforeach
                        </select>

                        <label for="max_players" class="mb-1.5 block text-sm font-bold text-slate-700">
                            Máximo de jugadores
                        </label>

                        <select name="max_players" id="max_players" class="{{ $selectClass }} mb-4">
                            @foreach ([2, 4, 6, 8, 12, 16, 24, 30, 40, 50] as $max)
                                <option value="{{ $max }}" @selected($max === 4)>{{ $max }}</option>
                            @endforeach
                        </select>

                        <div class="mb-4 flex items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-emerald-700 ring-1 ring-slate-200">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                                </svg>
                            </div>

                            <p class="text-sm text-slate-600">
                                Comparte el código de sala con tus estudiantes. Podrás ver quién se une y comenzar desde el panel de la sesión.
                            </p>
                        </div>

                        <button type="submit"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-700 px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2">
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.91 11.672a.375.375 0 0 1 0 .656l-5.603 3.113A.375.375 0 0 1 9.75 15.068v-6.3a.375.375 0 0 1 .557-.328l5.603 3.113Z" />
                            </svg>
                            Crear sala y ver el código
                        </button>
                    </form>
                @endif
            </div>

        </section>

    </div>

@endsection
