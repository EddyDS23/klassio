@php
    $typeLabels = [
        'kahoot' => 'Kahoot',
        'crossword' => 'Crucigrama',
        'word_search' => 'Sopa de letras',
        'matching' => 'Unir conceptos',
        'roulette' => 'Ruleta',
    ];

    $typePillClasses = [
        'crossword' => 'bg-amber-100 text-amber-700',
        'word_search' => 'bg-orange-100 text-orange-700',
        'matching' => 'bg-amber-100 text-amber-700',
        'kahoot' => 'bg-pink-100 text-pink-700',
        'roulette' => 'bg-pink-100 text-pink-700',
    ];
@endphp

<article class="group flex flex-col rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-1 hover:border-orange-200 hover:shadow-md">

    <div class="flex items-center justify-between gap-2">
        <span class="inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wide {{ $typePillClasses[$activity->type] ?? 'bg-slate-100 text-slate-600' }}">
            {{ $typeLabels[$activity->type] ?? $activity->type }}
        </span>

        <span class="text-xs font-bold text-slate-500">
            {{ $activity->max_score }} pts
        </span>
    </div>

    <h4 class="mt-2 break-words text-base font-bold text-slate-900">
        {{ $activity->title }}
    </h4>

    @if ($activity->description)
        <p class="mt-1 line-clamp-2 text-sm leading-6 text-slate-500">
            {{ $activity->description }}
        </p>
    @endif

    <div class="mt-auto flex items-center justify-between gap-2 pt-3">

        <span class="inline-flex min-w-0 items-center gap-1.5 text-xs font-semibold text-slate-500">
            <svg class="h-4 w-4 shrink-0 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            <span class="truncate">
                {{ $activity->time_limit ? gmdate('i:s', $activity->time_limit).' min' : 'Sin tiempo' }}
            </span>
        </span>

        <a href="{{ route('student.activities.show', $activity->id) }}"
           class="inline-flex min-h-9 shrink-0 items-center justify-center gap-1.5 rounded-lg bg-orange-700 px-3.5 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-orange-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-700 focus-visible:ring-offset-2">
            Jugar
            <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
            </svg>
        </a>

    </div>

</article>
