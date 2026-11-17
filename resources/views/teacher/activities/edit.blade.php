@extends('teacher.layouts.app')

@section('title', 'Editar actividad')

@section('page-title', 'Editar actividad')

@section('content')

    <div class="mx-auto max-w-5xl">

        <div class="mb-6">
            <a href="{{ route('teacher.activities.index', $activity->class_id) }}"
               class="inline-flex self-start items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Volver a actividades
            </a>
        </div>

        <!-- Encabezado -->
        <section class="mb-6 rounded-2xl bg-emerald-700 p-5 text-white shadow-sm sm:p-6">

            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-emerald-200">
                        Editar actividad
                    </p>

                    <h1 class="mt-1 text-2xl font-bold tracking-tight sm:text-3xl">
                        Modifica tu actividad
                    </h1>

                    <p class="mt-2 max-w-2xl text-sm leading-6 text-emerald-100">
                        Actualiza la información, configuración y fecha límite
                        de esta actividad para tus estudiantes.
                    </p>
                </div>

                <div class="hidden h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-emerald-600 ring-1 ring-emerald-500 md:flex">
                    <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                    </svg>
                </div>

            </div>

        </section>


        <!-- Errores -->
        @if ($errors->any())
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800 shadow-sm sm:p-5">

                <div class="mb-3 flex items-center gap-2">
                    <svg class="h-5 w-5 shrink-0 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>

                    <h2 class="font-bold">
                        Revisa los siguientes errores
                    </h2>
                </div>

                <ul class="list-inside list-disc space-y-1 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>

            </div>
        @endif


        <!-- Tarjeta principal -->
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <!-- Encabezado de la tarjeta -->
            <div class="border-b border-slate-100 bg-slate-50 px-4 py-4 sm:px-6 sm:py-5">

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                    <div class="min-w-0">
                        <p class="text-xs font-bold uppercase tracking-widest text-emerald-600">
                            Actividad seleccionada
                        </p>

                        <h2 class="mt-1 truncate text-lg font-bold text-slate-900 sm:text-xl">
                            {{ $activity->title }}
                        </h2>
                    </div>

                    <span class="w-fit shrink-0 rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
                        ID: {{ $activity->id }}
                    </span>

                </div>

            </div>


            <!-- Formulario -->
            <form action="{{ route('teacher.activities.update', $activity->id) }}"
                  method="POST"
                  class="space-y-6 p-4 sm:p-6">

                @csrf
                @method('PUT')


                <!-- Información general -->
                <div>
                    <div class="mb-4 flex items-center gap-3">

                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.332m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.332m0-14.25v14.25" />
                            </svg>
                        </div>

                        <div>
                            <h3 class="text-lg font-bold text-slate-900">
                                Información general
                            </h3>

                            <p class="text-sm text-slate-500">
                                Edita los datos principales de la actividad.
                            </p>
                        </div>

                    </div>


                    <div class="grid gap-4">

                        <!-- Título -->
                        <div>
                            <label for="title"
                                   class="mb-1.5 block text-sm font-bold text-slate-700">
                                Título de la actividad
                            </label>

                            <input
                                type="text"
                                id="title"
                                name="title"
                                value="{{ old('title', $activity->title) }}"
                                required
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
                                placeholder="Escribe el título de la actividad"
                            >
                        </div>


                        <!-- Descripción -->
                        <div>
                            <label for="description"
                                   class="mb-1.5 block text-sm font-bold text-slate-700">
                                Descripción
                            </label>

                            <textarea
                                id="description"
                                name="description"
                                rows="4"
                                class="w-full resize-y rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
                                placeholder="Describe lo que deben realizar los estudiantes..."
                            >{{ old('description', $activity->description) }}</textarea>
                        </div>


                        <!-- Tipo de actividad (fijo) -->
                        <div class="flex items-center justify-between gap-3 rounded-xl bg-slate-50 p-3 ring-1 ring-slate-200">

                            <div class="min-w-0">
                                <p class="text-sm font-bold text-slate-700">
                                    Tipo de actividad
                                </p>

                                <p class="mt-0.5 text-xs text-slate-500">
                                    No se puede modificar desde esta pantalla.
                                </p>
                            </div>

                            <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-white px-3 py-1.5 text-sm font-bold text-slate-700 ring-1 ring-slate-200">
                                <svg class="h-3.5 w-3.5 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                </svg>
                                @include('partials.game-type-label', ['type' => $activity->type])
                            </span>

                            <input
                                type="hidden"
                                name="type"
                                value="{{ $activity->type }}"
                            >

                        </div>

                    </div>
                </div>


                <!-- Separador -->
                <div class="border-t border-slate-100"></div>


                <!-- Configuración -->
                <div>

                    <div class="mb-4 flex items-center gap-3">

                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                        </div>

                        <div>
                            <h3 class="text-lg font-bold text-slate-900">
                                Configuración
                            </h3>

                            <p class="text-sm text-slate-500">
                                Ajusta la forma en que se realizará la actividad.
                            </p>
                        </div>

                    </div>


                    <div class="grid gap-4 sm:grid-cols-2">

                        <!-- Modo -->
                        <div>
                            <label for="mode"
                                   class="mb-1.5 block text-sm font-bold text-slate-700">
                                Modo de participación
                            </label>

                            <select
                                id="mode"
                                name="mode"
                                required
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-800 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
                            >
                                <option
                                    value="individual"
                                    {{ old('mode', $activity->mode) === 'individual' ? 'selected' : '' }}
                                >
                                    Individual
                                </option>

                                <option
                                    value="team"
                                    {{ old('mode', $activity->mode) === 'team' ? 'selected' : '' }}
                                >
                                    Equipo
                                </option>
                            </select>
                        </div>


                        <!-- Puntuación -->
                        <div>
                            <label for="max_score"
                                   class="mb-1.5 block text-sm font-bold text-slate-700">
                                Puntuación máxima
                            </label>

                            <input
                                type="number"
                                id="max_score"
                                name="max_score"
                                value="{{ old('max_score', $activity->max_score) }}"
                                min="1"
                                required
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-800 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
                                placeholder="Ej. 100"
                            >
                        </div>


                        <!-- Intentos -->
                        <div>
                            <label for="attempts"
                                   class="mb-1.5 block text-sm font-bold text-slate-700">
                                Intentos permitidos
                            </label>

                            <input
                                type="number"
                                name="attempts"
                                id="attempts"
                                min="1"
                                value="{{ old('attempts', $activity->attempts) }}"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-800 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
                                placeholder="Ej. 1"
                            >
                        </div>


                        <!-- Tiempo -->
                        <div>
                            <label for="time_limit"
                                   class="mb-1.5 block text-sm font-bold text-slate-700">
                                Límite de tiempo
                            </label>

                            <div class="relative">

                                <input
                                    type="number"
                                    id="time_limit"
                                    name="time_limit"
                                    value="{{ old('time_limit', $activity->time_limit) }}"
                                    min="1"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 pr-24 text-sm font-medium text-slate-800 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
                                    placeholder="Ej. 30"
                                >

                                <span class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">
                                    minutos
                                </span>

                            </div>
                        </div>


                        <!-- Fecha límite -->
                        <div class="sm:col-span-2">

                            <label for="due_at"
                                   class="mb-1.5 block text-sm font-bold text-slate-700">
                                Fecha límite de entrega
                            </label>

                            <input
                                type="datetime-local"
                                id="due_at"
                                name="due_at"
                                value="{{ old(
                                    'due_at',
                                    $activity->due_at
                                        ? $activity->due_at->format('Y-m-d\TH:i')
                                        : ''
                                ) }}"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-800 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
                            >

                            <p class="mt-1.5 text-xs text-slate-500">
                                Puedes dejar este campo vacío si la actividad no tiene fecha límite.
                            </p>

                        </div>

                        <!-- Unidad -->
                        <div class="sm:col-span-2">

                            <label for="unit_id"
                                   class="mb-1.5 block text-sm font-bold text-slate-700">
                                Unidad
                            </label>

                            <select id="unit_id"
                                    name="unit_id"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-800 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
                            >
                                <option value="">Sin unidad</option>
                                @forelse ($units as $unit)
                                    <option value="{{ $unit->id }}"
                                        @selected((string) old('unit_id', $activity->unit_id) === (string) $unit->id)>
                                        Unidad {{ $unit->number }}{{ filled($unit->name) ? ' — '.$unit->name : '' }}
                                    </option>
                                @empty
                                    <option value="" disabled>Esta clase no tiene unidades registradas</option>
                                @endforelse
                            </select>

                            <p class="mt-1.5 text-xs text-slate-500">
                                Puedes organizar la actividad en una unidad de la clase. Es opcional.
                            </p>

                        </div>

                    </div>
                </div>


                <!-- Botones -->
                <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">

                    <a
                        href="{{ route('teacher.activities.show', $activity->id) }}"
                        class="inline-flex w-full items-center justify-center rounded-xl border border-slate-300 bg-white px-6 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50 hover:text-slate-900 sm:w-auto"
                    >
                        Cancelar
                    </a>

                    <button
                        type="submit"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-6 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2 sm:w-auto"
                    >
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                        Guardar cambios
                    </button>

                </div>

            </form>

        </section>

    </div>

@endsection
