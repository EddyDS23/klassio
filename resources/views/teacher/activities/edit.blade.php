<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Editar actividad | Klassio</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-800">

    <!-- Barra superior -->
    <header class="border-b border-emerald-100 bg-white shadow-sm">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">

            <a href="{{ route('teacher.activities.index', $activity->class_id) }}"
               class="flex items-center gap-3">

                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-600 text-xl font-black text-white shadow-lg shadow-emerald-200">
                    K
                </div>

                <div>
                    <p class="text-xl font-black tracking-tight text-slate-900">
                        Klassio
                    </p>

                    <p class="text-xs font-semibold uppercase tracking-wider text-emerald-600">
                        Panel del profesor
                    </p>
                </div>
            </a>

            <span class="hidden rounded-full bg-emerald-100 px-4 py-2 text-sm font-bold text-emerald-700 sm:inline-flex">
                Profesor
            </span>
        </div>
    </header>


    <!-- Contenido principal -->
    <main class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">

        <!-- Encabezado -->
        <section class="mb-8 overflow-hidden rounded-3xl bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 p-6 text-white shadow-xl shadow-emerald-200 sm:p-8">

            <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">

                <div>
                    <div class="mb-3 inline-flex items-center gap-2 rounded-full bg-white/20 px-3 py-1 text-xs font-bold uppercase tracking-wider text-white">
                        <span>✏️</span>
                        Editar actividad
                    </div>

                    <h1 class="text-3xl font-black tracking-tight sm:text-4xl">
                        Modifica tu actividad
                    </h1>

                    <p class="mt-2 max-w-2xl text-sm leading-6 text-emerald-50 sm:text-base">
                        Actualiza la información, configuración y fecha límite
                        de esta actividad para tus estudiantes.
                    </p>
                </div>

                <div class="hidden h-24 w-24 items-center justify-center rounded-3xl bg-white/15 text-6xl md:flex">
                    📝
                </div>

            </div>
        </section>


        <!-- Mensaje de éxito -->
        @if(session('success'))
            <div class="mb-6 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800 shadow-sm">
                <div class="text-xl">✓</div>

                <div>
                    <p class="font-bold">
                        ¡Cambios guardados!
                    </p>

                    <p class="text-sm">
                        {{ session('success') }}
                    </p>
                </div>
            </div>
        @endif


        <!-- Errores -->
        @if($errors->any())
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-5 text-red-800 shadow-sm">

                <div class="mb-3 flex items-center gap-2">
                    <span class="text-xl">⚠️</span>

                    <h2 class="font-black">
                        Revisa los siguientes errores
                    </h2>
                </div>

                <ul class="list-inside list-disc space-y-1 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>

            </div>
        @endif


        <!-- Tarjeta principal -->
        <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl shadow-slate-200/60">

            <!-- Encabezado de la tarjeta -->
            <div class="border-b border-slate-100 bg-slate-50 px-6 py-5 sm:px-8">

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-emerald-600">
                            Actividad seleccionada
                        </p>

                        <h2 class="mt-1 text-xl font-black text-slate-900 sm:text-2xl">
                            {{ $activity->title }}
                        </h2>
                    </div>

                    <span class="w-fit rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">
                        ID: {{ $activity->id }}
                    </span>

                </div>
            </div>


            <!-- Formulario -->
            <form action="{{ route('teacher.activities.update', $activity->id) }}"
                  method="POST"
                  class="space-y-8 p-6 sm:p-8">

                @csrf
                @method('PUT')


                <!-- Información general -->
                <div>
                    <div class="mb-5 flex items-center gap-3">

                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-lg">
                            📚
                        </div>

                        <div>
                            <h3 class="text-lg font-black text-slate-900">
                                Información general
                            </h3>

                            <p class="text-sm text-slate-500">
                                Edita los datos principales de la actividad.
                            </p>
                        </div>

                    </div>


                    <div class="grid gap-6">

                        <!-- Título -->
                        <div>
                            <label for="title"
                                   class="mb-2 block text-sm font-bold text-slate-700">
                                Título de la actividad
                            </label>

                            <input
                                type="text"
                                id="title"
                                name="title"
                                value="{{ old('title', $activity->title) }}"
                                required
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100"
                                placeholder="Escribe el título de la actividad"
                            >
                        </div>


                        <!-- Descripción -->
                        <div>
                            <label for="description"
                                   class="mb-2 block text-sm font-bold text-slate-700">
                                Descripción
                            </label>

                            <textarea
                                id="description"
                                name="description"
                                rows="5"
                                class="w-full resize-y rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100"
                                placeholder="Describe lo que deben realizar los estudiantes..."
                            >{{ old('description', $activity->description) }}</textarea>
                        </div>


                        <!-- Tipo de actividad -->
                        <div class="rounded-2xl border border-cyan-100 bg-cyan-50 p-4">

                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                                <div>
                                    <label class="block text-sm font-bold text-slate-700">
                                        Tipo de actividad
                                    </label>

                                    <p class="mt-1 text-xs text-slate-500">
                                        El tipo de actividad no se puede modificar desde esta pantalla.
                                    </p>
                                </div>

                                <span class="w-fit rounded-full bg-cyan-100 px-4 py-2 text-sm font-black capitalize text-cyan-700">
                                    {{ $activity->type }}
                                </span>

                            </div>

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

                    <div class="mb-5 flex items-center gap-3">

                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-100 text-lg">
                            ⚙️
                        </div>

                        <div>
                            <h3 class="text-lg font-black text-slate-900">
                                Configuración
                            </h3>

                            <p class="text-sm text-slate-500">
                                Ajusta la forma en que se realizará la actividad.
                            </p>
                        </div>

                    </div>


                    <div class="grid gap-6 sm:grid-cols-2">

                        <!-- Modo -->
                        <div>
                            <label for="mode"
                                   class="mb-2 block text-sm font-bold text-slate-700">
                                Modo de participación
                            </label>

                            <select
                                id="mode"
                                name="mode"
                                required
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100"
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
                                   class="mb-2 block text-sm font-bold text-slate-700">
                                Puntuación máxima
                            </label>

                            <input
                                type="number"
                                id="max_score"
                                name="max_score"
                                value="{{ old('max_score', $activity->max_score) }}"
                                min="1"
                                required
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100"
                                placeholder="Ej. 100"
                            >
                        </div>


                        <!-- Intentos -->
                        <div>
                            <label for="attempts"
                                   class="mb-2 block text-sm font-bold text-slate-700">
                                Intentos permitidos
                            </label>

                            <input
                                type="number"
                                name="attempts"
                                id="attempts"
                                min="1"
                                value="{{ old('attempts', $activity->attempts) }}"
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100"
                                placeholder="Ej. 1"
                            >
                        </div>


                        <!-- Tiempo -->
                        <div>
                            <label for="time_limit"
                                   class="mb-2 block text-sm font-bold text-slate-700">
                                Límite de tiempo
                            </label>

                            <div class="relative">

                                <input
                                    type="number"
                                    id="time_limit"
                                    name="time_limit"
                                    value="{{ old('time_limit', $activity->time_limit) }}"
                                    min="1"
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 pr-24 text-sm font-medium text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100"
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
                                   class="mb-2 block text-sm font-bold text-slate-700">
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
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100"
                            >

                            <p class="mt-2 text-xs text-slate-500">
                                Puedes dejar este campo vacío si la actividad no tiene fecha límite.
                            </p>

                        </div>

                    </div>
                </div>


                <!-- Botones -->
                <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-6 sm:flex-row sm:justify-end">

                    <a
                        href="{{ route('teacher.activities.show', $activity->id) }}"
                        class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-6 py-3 text-sm font-bold text-slate-600 transition hover:border-slate-300 hover:bg-slate-50"
                    >
                        Cancelar
                    </a>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-6 py-3 text-sm font-black text-white shadow-lg shadow-emerald-200 transition hover:bg-emerald-700 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-emerald-200"
                    >
                        <span>✓</span>
                        Guardar cambios
                    </button>

                </div>

            </form>

        </section>


        <!-- Volver -->
        <div class="mt-6 text-center">

            <a
                href="{{ route('teacher.activities.index', $activity->class_id) }}"
                class="inline-flex items-center gap-2 text-sm font-bold text-emerald-700 transition hover:text-emerald-900"
            >
                <span>←</span>
                Volver a actividades
            </a>

        </div>

    </main>

</body>

</html>