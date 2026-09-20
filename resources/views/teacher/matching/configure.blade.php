<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        {{ $editing ? 'Editar' : 'Configurar' }} Unir Conceptos | Klassio
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-800">

    <!-- Fondo decorativo -->
    <div class="fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -left-24 -top-24 h-80 w-80 rounded-full bg-emerald-200/40 blur-3xl"></div>
        <div class="absolute right-0 top-20 h-96 w-96 rounded-full bg-cyan-200/30 blur-3xl"></div>
        <div class="absolute bottom-0 left-1/3 h-80 w-80 rounded-full bg-teal-200/20 blur-3xl"></div>
    </div>

    <!-- Barra superior -->
    <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/90 shadow-sm backdrop-blur">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">

            <a href="{{ route('teacher.activities.show', $activity->id) }}"
               class="flex items-center gap-3">

                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 text-xl font-black text-white shadow-lg shadow-emerald-500/20">
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

            <div class="hidden items-center gap-2 rounded-full bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700 sm:flex">
                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                Configuración de actividad
            </div>

            <form method="POST" action="{{ url('/logout') }}">
                @csrf

                <button
                    type="submit"
                    class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-600 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600"
                >
                    Cerrar sesión
                </button>
            </form>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        <!-- Encabezado -->
        <section class="mb-8 overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-teal-600 to-cyan-700 p-6 text-white shadow-xl shadow-emerald-900/10 sm:p-8">

            <div class="flex flex-col justify-between gap-6 lg:flex-row lg:items-center">

                <div class="max-w-3xl">
                    <div class="mb-4 inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1.5 text-sm font-semibold text-emerald-50 ring-1 ring-white/20">
                        <span>🔗</span>
                        Juego educativo
                    </div>

                    <h1 class="text-3xl font-black tracking-tight sm:text-4xl">
                        {{ $editing ? 'Editar Unir Conceptos' : 'Configurar Unir Conceptos' }}
                    </h1>

                    <p class="mt-3 max-w-2xl text-sm leading-6 text-emerald-50 sm:text-base">
                        Crea parejas de conceptos y definiciones para que tus alumnos
                        relacionen correctamente cada elemento.
                    </p>
                </div>

                <div class="rounded-3xl bg-white/10 p-5 text-center ring-1 ring-white/20 backdrop-blur">
                    <div class="text-4xl">🧩</div>
                    <p class="mt-2 text-sm font-bold text-white">
                        Actividad de relación
                    </p>
                    <p class="mt-1 text-xs text-emerald-100">
                        Concepto → Definición
                    </p>
                </div>

            </div>
        </section>

        <!-- Información de actividad -->
        <section class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">

                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                        Actividad actual
                    </p>

                    <h2 class="mt-1 text-xl font-black text-slate-900">
                        {{ $activity->title }}
                    </h2>
                </div>

                <a
                    href="{{ route('teacher.matching.configure', $activity->id) }}"
                    class="inline-flex items-center justify-center rounded-xl border border-amber-200 bg-amber-50 px-4 py-2.5 text-sm font-bold text-amber-700 transition hover:bg-amber-100"
                >
                    ↻ Reiniciar configuración
                </a>

            </div>
        </section>

        <!-- Mensaje de éxito -->
        @if ($message = session('success') ?? session('status'))
            <div id="save-feedback" role="status" class="fixed right-4 top-4 z-50 flex max-w-md items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800 shadow-xl shadow-emerald-900/15">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-100">
                    ✓
                </div>

                <div>
                    <p class="font-bold">Operación realizada</p>
                    <p class="mt-1 text-sm">
                        {{ $message }}
                    </p>
                </div>
            </div>
        @endif

        <!-- Errores -->
        @if ($errors->any())
            <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 p-5 text-rose-800 shadow-sm">
                <div class="flex items-center gap-2">
                    <span class="text-xl">⚠️</span>
                    <h3 class="font-black">
                        Revisa la información
                    </h3>
                </div>

                <ul class="mt-3 list-disc space-y-1 pl-6 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulario principal -->
        <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl shadow-slate-900/5">

            <div class="border-b border-slate-200 bg-slate-50 px-5 py-5 sm:px-8">
                <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                    <div>
                        <h2 class="text-xl font-black text-slate-900">
                            Parejas de conceptos
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Agrega cada concepto junto con su definición y asigna los puntos.
                        </p>
                    </div>

                    <span class="inline-flex w-fit items-center rounded-full bg-emerald-100 px-3 py-1.5 text-xs font-bold text-emerald-700">
                        Mínimo 1 pareja
                    </span>
                </div>
            </div>

                <form
                id="matching-form"
                method="POST"
                action="{{ $editing
                    ? route('teacher.matching.update', $activity->id)
                    : route('teacher.matching.store', $activity->id) }}"
                class="p-5 sm:p-8"
            >
                @csrf

                @if ($editing)
                    @method('PUT')
                @endif

                <div class="mb-5 rounded-2xl border border-cyan-100 bg-cyan-50 p-4">
                    <div class="flex items-start gap-3">
                        <div class="text-xl">💡</div>

                        <div>
                            <p class="font-bold text-cyan-900">
                                Recomendación
                            </p>

                            <p class="mt-1 text-sm leading-6 text-cyan-800">
                                Utiliza conceptos claros y definiciones precisas.
                                Por ejemplo: <strong>HTTP</strong> → <strong>Protocolo de transferencia</strong>.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Tabla para escritorio -->
                <div class="hidden overflow-x-auto rounded-2xl border border-slate-200 md:block">
                    <table class="w-full min-w-[760px] border-collapse">
                        <thead>
                            <tr class="bg-slate-100 text-left text-xs font-black uppercase tracking-wider text-slate-500">
                                <th class="px-4 py-4">Concepto</th>
                                <th class="px-4 py-4">Definición</th>
                                <th class="w-32 px-4 py-4">Puntos</th>
                                <th class="w-28 px-4 py-4 text-center">Acción</th>
                            </tr>
                        </thead>

                        <tbody id="pairs-body" class="divide-y divide-slate-200">
                            @forelse (($editing && $matching ? $matching->items : []) as $i => $item)
                                <tr class="row-form transition hover:bg-emerald-50/40">
                                    <td class="p-3">
                                        <input
                                            type="text"
                                            name="items[{{ $i }}][left]"
                                            value="{{ $item->left_text }}"
                                            placeholder="Concepto"
                                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm font-medium text-slate-800 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10"
                                        >
                                    </td>

                                    <td class="p-3">
                                        <input
                                            type="text"
                                            name="items[{{ $i }}][right]"
                                            value="{{ $item->right_text }}"
                                            placeholder="Definición"
                                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm font-medium text-slate-800 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10"
                                        >
                                    </td>

                                    <td class="p-3">
                                        <input
                                            type="number"
                                            name="items[{{ $i }}][score]"
                                            value="{{ $item->score }}"
                                            min="1"
                                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm font-bold text-slate-800 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10"
                                        >
                                    </td>

                                    <td class="p-3 text-center">
                                        <button
                                            type="button"
                                            class="remove rounded-xl bg-rose-50 px-3 py-2 text-xs font-bold text-rose-600 transition hover:bg-rose-100"
                                            title="Eliminar pareja"
                                        >
                                            Eliminar
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr class="row-form transition hover:bg-emerald-50/40">
                                    <td class="p-3">
                                        <input
                                            type="text"
                                            name="items[0][left]"
                                            placeholder="HTTP"
                                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm font-medium text-slate-800 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10"
                                        >
                                    </td>

                                    <td class="p-3">
                                        <input
                                            type="text"
                                            name="items[0][right]"
                                            placeholder="Protocolo de transferencia"
                                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm font-medium text-slate-800 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10"
                                        >
                                    </td>

                                    <td class="p-3">
                                        <input
                                            type="number"
                                            name="items[0][score]"
                                            value="10"
                                            min="1"
                                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm font-bold text-slate-800 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10"
                                        >
                                    </td>

                                    <td class="p-3 text-center">
                                        <button
                                            type="button"
                                            class="remove rounded-xl bg-rose-50 px-3 py-2 text-xs font-bold text-rose-600 transition hover:bg-rose-100"
                                            title="Eliminar pareja"
                                        >
                                            Eliminar
                                        </button>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Tarjetas para móvil -->
                <div id="mobile-pairs" class="space-y-4 md:hidden">
                    @forelse (($editing && $matching ? $matching->items : []) as $i => $item)
                        <div class="mobile-row row-form rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <div class="mb-3 flex items-center justify-between">
                                <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-black text-emerald-700">
                                    Pareja {{ $i + 1 }}
                                </span>

                                <button
                                    type="button"
                                    class="remove rounded-lg bg-rose-50 px-3 py-1.5 text-xs font-bold text-rose-600"
                                >
                                    Eliminar
                                </button>
                            </div>

                            <label class="mb-1 block text-sm font-bold text-slate-700">
                                Concepto
                            </label>

                            <input
                                type="text"
                                name="items[{{ $i }}][left]"
                                value="{{ $item->left_text }}"
                                class="mb-3 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10"
                            >

                            <label class="mb-1 block text-sm font-bold text-slate-700">
                                Definición
                            </label>

                            <input
                                type="text"
                                name="items[{{ $i }}][right]"
                                value="{{ $item->right_text }}"
                                class="mb-3 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10"
                            >

                            <label class="mb-1 block text-sm font-bold text-slate-700">
                                Puntos
                            </label>

                            <input
                                type="number"
                                name="items[{{ $i }}][score]"
                                value="{{ $item->score }}"
                                min="1"
                                class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10"
                            >
                        </div>
                    @empty
                        <div class="mobile-row row-form rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <div class="mb-3 flex items-center justify-between">
                                <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-black text-emerald-700">
                                    Pareja 1
                                </span>

                                <button
                                    type="button"
                                    class="remove rounded-lg bg-rose-50 px-3 py-1.5 text-xs font-bold text-rose-600"
                                >
                                    Eliminar
                                </button>
                            </div>

                            <label class="mb-1 block text-sm font-bold text-slate-700">
                                Concepto
                            </label>

                            <input
                                type="text"
                                name="items[0][left]"
                                placeholder="HTTP"
                                class="mb-3 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10"
                            >

                            <label class="mb-1 block text-sm font-bold text-slate-700">
                                Definición
                            </label>

                            <input
                                type="text"
                                name="items[0][right]"
                                placeholder="Protocolo de transferencia"
                                class="mb-3 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10"
                            >

                            <label class="mb-1 block text-sm font-bold text-slate-700">
                                Puntos
                            </label>

                            <input
                                type="number"
                                name="items[0][score]"
                                value="10"
                                min="1"
                                class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10"
                            >
                        </div>
                    @endforelse
                </div>

                <!-- Botón agregar -->
                <button
                    type="button"
                    id="add-pair"
                    class="mt-5 inline-flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-black text-emerald-700 transition hover:bg-emerald-100"
                >
                    <span class="text-lg">+</span>
                    Agregar pareja
                </button>

                <!-- Acciones -->
                <div class="mt-8 flex flex-col-reverse gap-3 border-t border-slate-200 pt-6 sm:flex-row sm:items-center sm:justify-between">

                    <a
                        href="{{ route('teacher.activities.show', $activity->id) }}"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-600 transition hover:bg-slate-50"
                    >
                        ← Volver a la actividad
                    </a>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-3 text-sm font-black text-white shadow-lg shadow-emerald-600/20 transition hover:from-emerald-700 hover:to-teal-700"
                    >
                        <span>✓</span>
                        {{ $editing ? 'Actualizar actividad' : 'Guardar actividad' }}
                    </button>

                </div>
            </form>
        </section>

        <!-- Resumen de parejas configuradas -->
        @if ($editing && $matching)
            <section class="mt-8 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-lg shadow-slate-900/5">

                <div class="border-b border-slate-200 bg-slate-50 px-5 py-5 sm:px-8">
                    <div class="flex flex-col justify-between gap-2 sm:flex-row sm:items-center">
                        <div>
                            <h2 class="text-xl font-black text-slate-900">
                                Parejas configuradas
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                Consulta las relaciones guardadas actualmente.
                            </p>
                        </div>

                        <span class="inline-flex w-fit rounded-full bg-teal-100 px-3 py-1.5 text-xs font-black text-teal-700">
                            {{ $matching->items()->count() }} parejas
                        </span>
                    </div>
                </div>

                <div class="overflow-x-auto p-5 sm:p-8">
                    <table class="w-full min-w-[560px] border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 text-left text-xs font-black uppercase tracking-wider text-slate-500">
                                <th class="px-4 py-3">Concepto</th>
                                <th class="px-4 py-3">Definición</th>
                                <th class="px-4 py-3">Puntos</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @foreach ($matching->items()->orderBy('left_text')->get() as $item)
                                <tr class="transition hover:bg-emerald-50/40">
                                    <td class="px-4 py-4 text-sm font-black text-slate-800">
                                        {{ $item->left_text }}
                                    </td>

                                    <td class="px-4 py-4 text-sm text-slate-600">
                                        {{ $item->right_text }}
                                    </td>

                                    <td class="px-4 py-4">
                                        <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-black text-amber-700">
                                            {{ $item->score }} pts
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endif

    </main>

    <script>
        const body = document.getElementById('pairs-body');
        const mobileBody = document.getElementById('mobile-pairs');

        // La validación del navegador corre ANTES del submit, así que lo oculto
        // debe desactivarse desde el inicio, no solo al enviar.
        function syncVisibleInputs() {
            const isDesktop = window.matchMedia('(min-width: 768px)').matches;
            if (isDesktop) {
                mobileBody.querySelectorAll('input').forEach((input) => {
                    input.disabled = true;
                    input.removeAttribute('required');
                });
                body.querySelectorAll('input').forEach((input) => {
                    input.disabled = false;
                    input.setAttribute('required', 'required');
                });
            } else {
                body.querySelectorAll('input').forEach((input) => {
                    input.disabled = true;
                    input.removeAttribute('required');
                });
                mobileBody.querySelectorAll('input').forEach((input) => {
                    input.disabled = false;
                    input.setAttribute('required', 'required');
                });
            }
        }

        syncVisibleInputs();
        window.matchMedia('(min-width: 768px)').addEventListener('change', syncVisibleInputs);

        document.getElementById('matching-form').addEventListener('submit', () => {
            // Refuerzo: solo enviar los inputs visibles.
            syncVisibleInputs();
            const submitButton = document.querySelector('#matching-form button[type="submit"]');
            submitButton.disabled = true;
            submitButton.classList.add('cursor-wait', 'opacity-75');
            submitButton.innerHTML = '<span>⏳</span> Guardando…';
        });

        const saveFeedback = document.getElementById('save-feedback');
        if (saveFeedback) {
            setTimeout(() => saveFeedback.remove(), 5000);
        }

        let index = body.querySelectorAll('tr.row-form').length;

        function reindex() {
            body.querySelectorAll('tr.row-form').forEach((row, i) => {
                row.querySelectorAll('input').forEach((input) => {
                    input.name = input.name.replace(
                        /items\[\d+\]\[(left|right|score)\]/,
                        `items[${i}][$1]`
                    );
                });
            });

            mobileBody.querySelectorAll('.row-form').forEach((row, i) => {
                row.querySelectorAll('input').forEach((input) => {
                    input.name = input.name.replace(
                        /items\[\d+\]\[(left|right|score)\]/,
                        `items[${i}][$1]`
                    );
                });

                const badge = row.querySelector('span');
                if (badge) {
                    badge.textContent = `Pareja ${i + 1}`;
                }
            });
        }

        function createDesktopRow(currentIndex) {
            const tr = document.createElement('tr');

            tr.className = 'row-form transition hover:bg-emerald-50/40';

            tr.innerHTML = `
                <td class="p-3">
                    <input
                        type="text"
                        name="items[${currentIndex}][left]"
                        placeholder="Concepto"
                        class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm font-medium text-slate-800 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10"
                    >
                </td>

                <td class="p-3">
                    <input
                        type="text"
                        name="items[${currentIndex}][right]"
                        placeholder="Definición"
                        class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm font-medium text-slate-800 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10"
                    >
                </td>

                <td class="p-3">
                    <input
                        type="number"
                        name="items[${currentIndex}][score]"
                        value="10"
                        min="1"
                        class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm font-bold text-slate-800 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10"
                    >
                </td>

                <td class="p-3 text-center">
                    <button
                        type="button"
                        class="remove rounded-xl bg-rose-50 px-3 py-2 text-xs font-bold text-rose-600 transition hover:bg-rose-100"
                        title="Eliminar pareja"
                    >
                        Eliminar
                    </button>
                </td>
            `;

            return tr;
        }

        function createMobileRow(currentIndex) {
            const wrapper = document.createElement('div');

            wrapper.className = 'mobile-row row-form rounded-2xl border border-slate-200 bg-slate-50 p-4';

            wrapper.innerHTML = `
                <div class="mb-3 flex items-center justify-between">
                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-black text-emerald-700">
                        Pareja ${currentIndex + 1}
                    </span>

                    <button
                        type="button"
                        class="remove rounded-lg bg-rose-50 px-3 py-1.5 text-xs font-bold text-rose-600"
                    >
                        Eliminar
                    </button>
                </div>

                <label class="mb-1 block text-sm font-bold text-slate-700">
                    Concepto
                </label>

                <input
                    type="text"
                    name="items[${currentIndex}][left]"
                    placeholder="Concepto"
                    class="mb-3 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10"
                >

                <label class="mb-1 block text-sm font-bold text-slate-700">
                    Definición
                </label>

                <input
                    type="text"
                    name="items[${currentIndex}][right]"
                    placeholder="Definición"
                    class="mb-3 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10"
                >

                <label class="mb-1 block text-sm font-bold text-slate-700">
                    Puntos
                </label>

                <input
                    type="number"
                    name="items[${currentIndex}][score]"
                    value="10"
                    min="1"
                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10"
                >
            `;

            return wrapper;
        }

        document.getElementById('add-pair').addEventListener('click', () => {
            body.appendChild(createDesktopRow(index));
            mobileBody.appendChild(createMobileRow(index));

            index++;
            reindex();
            syncVisibleInputs();
        });

        function removePair(target) {
            const desktopRows = Array.from(body.querySelectorAll('tr.row-form'));
            const mobileRows = Array.from(mobileBody.querySelectorAll('.row-form'));

            if (desktopRows.length <= 1) {
                alert('Debes conservar al menos una pareja.');
                return;
            }

            const desktopRow = target.closest('tr');
            const mobileRow = target.closest('.mobile-row');

            if (desktopRow) {
                const idx = desktopRows.indexOf(desktopRow);
                desktopRow.remove();
                if (mobileRows[idx]) {
                    mobileRows[idx].remove();
                }
            } else if (mobileRow) {
                const idx = mobileRows.indexOf(mobileRow);
                mobileRow.remove();
                if (desktopRows[idx]) {
                    desktopRows[idx].remove();
                }
            }

            reindex();
            syncVisibleInputs();
        }

        body.addEventListener('click', (event) => {
            if (event.target.classList.contains('remove')) {
                removePair(event.target);
            }
        });

        mobileBody.addEventListener('click', (event) => {
            if (event.target.classList.contains('remove')) {
                removePair(event.target);
            }
        });
    </script>

</body>
</html>
