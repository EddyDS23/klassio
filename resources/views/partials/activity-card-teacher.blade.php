@php
    $typeIcons = [
        'kahoot' => 'M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z',
        'crossword' => 'M14.25 6.087c0-.355.186-.676.401-.959.221-.29.349-.634.349-1.003 0-1.036-1.007-1.875-2.25-1.875s-2.25.84-2.25 1.875c0 .369.128.713.349 1.003.215.283.401.604.401.959v0a.64.64 0 0 1-.657.643 48.39 48.39 0 0 1-3.409-.042.75.75 0 0 1-.707-.632 48.264 48.264 0 0 1-.034-2.897.75.75 0 0 1 .515-.707 48.334 48.334 0 0 1 2.887-.085v0c.355 0 .676-.186.959-.401.29-.221.634-.349 1.003-.349 1.036 0 1.875-1.007 1.875-2.25S12.535 1.5 11.5 1.5s-1.875 1.007-1.875 2.25c0 .369.128.713.349 1.003.215.283.401.604.401.959v0c0 .355-.186.676-.401.959-.221.29-.349.634-.349 1.003 0 1.036-1.007 1.875-2.25 1.875s-2.25-.84-2.25-1.875c0-.369.128-.713.349-1.003.215-.283.401-.604.401-.959v0a.64.64 0 0 0-.657-.643 48.39 48.39 0 0 0-3.409.042.75.75 0 0 0-.707.632 48.264 48.264 0 0 0-.034 2.897.75.75 0 0 0 .515.707c.438.154.902.226 1.397.226v0c.466 0 .872-.054 1.21-.14v5.628c0 1.499 1.193 2.755 2.691 2.755h.002c.325 0 .591.266.591.591v4.875c0 .414.336.75.75.75h.006a.75.75 0 0 0 .75-.75v-4.875c0-.325.266-.591.591-.591h.002c1.498 0 2.691-1.256 2.691-2.755V9.75c.338.086.744.14 1.21.14v0c.495 0 .959-.072 1.397-.226a.75.75 0 0 0 .515-.707 48.264 48.264 0 0 0-.034-2.897.75.75 0 0 0-.707-.632 48.39 48.39 0 0 0-3.409-.042.64.64 0 0 0-.657.643Z',
        'word_search' => 'm21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z',
        'matching' => 'M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.242',
        'roulette' => 'M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 0 13.803 3.7l3.181-3.182m0-4.991v4.99',
    ];

    $typeIcon = $typeIcons[$activity->type] ?? $typeIcons['word_search'];

    // Ruta de configuración según el tipo (editar si ya tiene contenido, configurar si no).
    $configRoute = match ($activity->type) {
        'crossword' => ($activity->crossword && $activity->crossword->words()->exists())
            ? 'teacher.crossword.edit'
            : 'teacher.crossword.configure',
        'kahoot' => ($activity->kahoot && $activity->kahoot->questions()->exists())
            ? 'teacher.kahoot.edit'
            : 'teacher.kahoot.configure',
        'matching' => ($activity->matching && $activity->matching->items()->exists())
            ? 'teacher.matching.edit'
            : 'teacher.matching.configure',
        'roulette' => ($activity->roulette && $activity->roulette->items()->exists())
            ? 'teacher.roulette.edit'
            : 'teacher.roulette.configure',
        default => null,
    };
@endphp

<article class="flex flex-col rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:border-emerald-200 hover:shadow-md">

    {{-- Cabecera: icono del tipo + título + estado --}}
    <div class="flex items-start gap-3 border-b border-slate-100 p-4">

        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100">
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $typeIcon }}" />
            </svg>
        </div>

        <div class="min-w-0 flex-1">
            <h3 class="break-words text-base font-bold tracking-tight text-slate-900">
                {{ $activity->title }}
            </h3>

            <p class="mt-0.5 text-xs font-bold text-emerald-700">
                @include('partials.game-type-label', ['type' => $activity->type])
            </p>
        </div>

        @if ($activity->status === 'draft')
            <span class="shrink-0 rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-bold text-amber-700">
                Borrador
            </span>
        @else
            <span class="shrink-0 rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-bold text-emerald-700">
                {{ ucfirst($activity->status) }}
            </span>
        @endif

    </div>


    {{-- Datos en chips compactos --}}
    <div class="flex flex-wrap items-center gap-2 p-4">

        <span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-50 px-2.5 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200">
            <svg class="h-3.5 w-3.5 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
            </svg>
            <span class="capitalize">{{ $activity->mode }}</span>
        </span>

        <span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-50 px-2.5 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200">
            <svg class="h-3.5 w-3.5 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
            </svg>
            {{ $activity->max_score }} pts
        </span>

        <span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-50 px-2.5 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200">
            <svg class="h-3.5 w-3.5 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            {{ $activity->time_limit }} seg
        </span>

        @if ($activity->due_at)
            <span class="inline-flex items-center gap-1.5 rounded-lg bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-700 ring-1 ring-amber-200">
                <svg class="h-3.5 w-3.5 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                </svg>
                {{ $activity->due_at }}
            </span>
        @endif

    </div>


    {{-- Acciones --}}
    <div class="mt-auto border-t border-slate-100 p-4 pt-3">

        <a href="{{ route('teacher.activities.show', $activity->id) }}"
           class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-700 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-emerald-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2">
            Ver actividad
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
            </svg>
        </a>

        @if ($activity->status === 'draft')

            <div class="mt-2 grid grid-cols-2 gap-2">

                <a href="{{ route('teacher.activities.edit', $activity->id) }}"
                   class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50 hover:text-slate-900">
                    <svg class="h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                    </svg>
                    Editar
                </a>

                @if ($configRoute)
                    <a href="{{ route($configRoute, $activity->id) }}"
                       class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2.5 text-sm font-bold text-emerald-700 transition hover:bg-emerald-100">
                        <svg class="h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        Configurar
                    </a>
                @endif

            </div>

        @endif

    </div>

</article>
