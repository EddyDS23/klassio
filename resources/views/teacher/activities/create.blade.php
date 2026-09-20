<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Crear actividad | Klassio</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-800">

    <div class="min-h-screen">

        <!-- ENCABEZADO DEL PROFESOR -->
        <header class="sticky top-0 z-30 border-b border-emerald-200 bg-white shadow-sm">

            <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-5 py-5 sm:px-8">

                <div class="flex items-center gap-3">

                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-600 text-2xl font-black text-white shadow-lg">
                        K
                    </div>

                    <div>
                        <p class="text-xs font-black uppercase tracking-wide text-emerald-600">
                            Klassio · Panel del profesor
                        </p>

                        <h1 class="mt-1 text-2xl font-black text-slate-900">
                            Crear actividad
                        </h1>
                    </div>

                </div>

                <div class="hidden rounded-full bg-emerald-100 px-4 py-2 text-sm font-bold text-emerald-700 sm:block">
                    👨‍🏫 Profesor
                </div>

            </div>

        </header>

        <main class="mx-auto max-w-6xl px-5 py-8 sm:px-8">

            <!-- Volver -->
            <div class="mb-6">
                <a href="{{ route('teacher.activities.index', $class->id) }}"
                    class="inline-flex items-center gap-2 text-sm font-bold text-emerald-700 transition hover:text-emerald-900">
                    <span>←</span>
                    Volver a actividades
                </a>
            </div>

            <!-- BANNER -->
            <section class="relative mb-8 overflow-hidden rounded-3xl bg-gradient-to-r from-emerald-700 via-teal-600 to-cyan-600 p-6 text-white shadow-xl sm:p-8">

                <div class="relative z-10">

                    <span class="inline-flex rounded-full bg-white/20 px-4 py-2 text-sm font-bold backdrop-blur">
                        📝 Nueva actividad
                    </span>

                    <h2 class="mt-4 text-3xl font-black sm:text-4xl">
                        Crea una actividad para tus estudiantes
                    </h2>

                    <p class="mt-3 max-w-2xl text-sm leading-6 text-emerald-50 sm:text-base">
                        Diseña una experiencia de aprendizaje divertida para la clase:
                        {{ $class->name }}.
                    </p>

                </div>

                <div class="absolute -right-6 -top-8 text-[150px] opacity-20">
                    🎓
                </div>

            </section>

            <!-- ERRORES -->
            @if ($errors->any())

                <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-5 text-red-700">

                    <div class="mb-3 flex items-center gap-2 font-black">
                        <span>⚠️</span>
                        Revisa los siguientes errores:
                    </div>

                    <ul class="list-inside list-disc space-y-1 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>

                </div>

            @endif

            <!-- FORMULARIO -->
            <form action="{{ route('teacher.activities.store', $class->id) }}"
                  method="POST"
                  class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">

                @csrf

                <!-- INFORMACIÓN GENERAL -->
                <div class="mb-8">

                    <div class="mb-6 flex items-center gap-3">

                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-2xl">
                            📚
                        </div>

                        <div>
                            <h2 class="text-xl font-black text-slate-900">
                                Información general
                            </h2>

                            <p class="text-sm text-slate-500">
                                Datos principales de la actividad.
                            </p>
                        </div>

                    </div>

                    <div class="grid gap-6">

                        <!-- CLASE -->
                        <div class="rounded-2xl bg-emerald-50 p-4">

                            <p class="text-xs font-black uppercase tracking-wide text-emerald-600">
                                Clase seleccionada
                            </p>

                            <p class="mt-1 text-lg font-black text-emerald-900">
                                {{ $class->name }}
                            </p>

                        </div>

                        <!-- TÍTULO -->
                        <div>
                            <label for="title"
                                   class="mb-2 block text-sm font-bold text-slate-700">
                                Título de la actividad
                            </label>

                            <input type="text"
                                   id="title"
                                   name="title"
                                   value="{{ old('title') }}"
                                   required
                                   placeholder="Ej. Repaso de matemáticas"
                                   class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100">
                        </div>

                        <!-- DESCRIPCIÓN -->
                        <div>
                            <label for="description"
                                   class="mb-2 block text-sm font-bold text-slate-700">
                                Descripción
                            </label>

                            <textarea id="description"
                                      name="description"
                                      rows="4"
                                      placeholder="Explica a tus estudiantes qué deben realizar..."
                                      class="w-full resize-y rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100">{{ old('description') }}</textarea>
                        </div>

                    </div>

                </div>

                <div class="my-8 border-t border-slate-200"></div>

                <!-- CONFIGURACIÓN -->
                <div class="mb-8">

                    <div class="mb-6 flex items-center gap-3">

                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-cyan-100 text-2xl">
                            ⚙️
                        </div>

                        <div>
                            <h2 class="text-xl font-black text-slate-900">
                                Configuración
                            </h2>

                            <p class="text-sm text-slate-500">
                                Define cómo funcionará la actividad.
                            </p>
                        </div>

                    </div>

                    <div class="grid gap-6 md:grid-cols-2">

                        <!-- TIPO -->
                        <div>
                            <label for="type"
                                   class="mb-2 block text-sm font-bold text-slate-700">
                                Tipo de actividad
                            </label>

                            <select id="type"
                                    name="type"
                                    required
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-800 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100">

                                <option value="">Seleccionar actividad</option>

                                <option value="word_search" {{ old('type') === 'word_search' ? 'selected' : '' }}>
                                    🔎 Sopa de letras
                                </option>

                                <option value="crossword" {{ old('type') === 'crossword' ? 'selected' : '' }}>
                                    🧩 Crucigrama
                                </option>

                                <option value="matching" {{ old('type') === 'matching' ? 'selected' : '' }}>
                                    🔗 Relacionar
                                </option>

                                <option value="kahoot" {{ old('type') === 'kahoot' ? 'selected' : '' }}>
                                    🏆 Kahoot
                                </option>

                            </select>
                        </div>

                        <!-- MODO -->
                        <div>
                            <label for="mode"
                                   class="mb-2 block text-sm font-bold text-slate-700">
                                Modo de participación
                            </label>

                            <select id="mode"
                                    name="mode"
                                    required
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-800 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100">

                                <option value="individual" {{ old('mode') === 'individual' ? 'selected' : '' }}>
                                    👤 Individual
                                </option>

                                <option value="team" {{ old('mode') === 'team' ? 'selected' : '' }}>
                                    👥 Por equipos
                                </option>

                            </select>
                        </div>

                        <!-- PUNTUACIÓN -->
                        <div>
                            <label for="max_score"
                                   class="mb-2 block text-sm font-bold text-slate-700">
                                Puntuación máxima
                            </label>

                            <input type="number"
                                   id="max_score"
                                   name="max_score"
                                   value="{{ old('max_score') }}"
                                   min="1"
                                   required
                                   placeholder="Ej. 100"
                                   class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-800 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100">
                        </div>

                        <!-- INTENTOS -->
                        <div>
                            <label for="attempts"
                                   class="mb-2 block text-sm font-bold text-slate-700">
                                Intentos permitidos
                            </label>

                            <input type="number"
                                   name="attempts"
                                   id="attempts"
                                   min="1"
                                   value="{{ old('attempts') }}"
                                   placeholder="Ej. 3"
                                   class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100">
                        </div>

                        <!-- TIEMPO -->
                        <div>
                            <label for="time_limit"
                                   class="mb-2 block text-sm font-bold text-slate-700">
                                Tiempo límite en segundos
                            </label>

                            <input type="number"
                                   id="time_limit"
                                   name="time_limit"
                                   value="{{ old('time_limit') }}"
                                   min="1"
                                   max="60000"
                                   placeholder="Ej. 600"
                                   class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100">
                        </div>

                        <!-- FECHA -->
                        <div>
                            <label for="due_at"
                                   class="mb-2 block text-sm font-bold text-slate-700">
                                Fecha límite
                            </label>

                            <input type="datetime-local"
                                   id="due_at"
                                   name="due_at"
                                   value="{{ old('due_at') }}"
                                   class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-800 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100">
                        </div>

                    </div>

                </div>

                <!-- BOTONES -->
                <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-6 sm:flex-row sm:justify-end">

                    <a href="{{ route('teacher.activities.index', $class->id) }}"
                       class="rounded-xl border border-slate-300 px-6 py-3 text-center font-bold text-slate-700 transition hover:bg-slate-100">
                        Cancelar
                    </a>

                    <button type="submit"
                            class="rounded-xl bg-emerald-600 px-6 py-3 font-black text-white shadow-md transition hover:bg-emerald-700 hover:shadow-lg">
                        Crear actividad →
                    </button>

                </div>

            </form>

        </main>

    </div>

</body>

</html>