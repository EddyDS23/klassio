@extends('admin.layouts.app')

@section('title', 'Resultados | Klassio')

@section('page-title')
    Resultados de participación
@endsection

@php
    $typeLabels = ['word_search' => 'Sopa de letras', 'crossword' => 'Crucigrama', 'matching' => 'Conecta', 'kahoot' => 'Quiz', 'roulette' => 'Ruleta'];
    $statusLabels = ['started' => 'En curso', 'completed' => 'Completada', 'abandoned' => 'Abandonada', 'expired' => 'Expirada'];
    $statusColors = [
        'started' => 'bg-amber-100 text-amber-700',
        'completed' => 'bg-emerald-100 text-emerald-700',
        'abandoned' => 'bg-slate-100 text-slate-600',
        'expired' => 'bg-red-100 text-red-700',
    ];
    $filterKeys = ['activity_id', 'type', 'status'];

    if (!function_exists('admin_format_duration')) {
        function admin_format_duration($seconds): string
        {
            if ($seconds === null) {
                return '—';
            }
            $minutes = intdiv((int) $seconds, 60);
            $secs = (int) $seconds % 60;
            return $minutes > 0 ? "{$minutes}m {$secs}s" : "{$secs}s";
        }
    }
@endphp

@section('content')

    <!-- Filtros -->
    <section class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

        <form method="GET" action="{{ route('admin.results.index') }}"
              class="grid gap-4 md:grid-cols-3">

            <div class="min-w-0">
                <label for="activity_id" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">
                    Actividad
                </label>
                <select id="activity_id" name="activity_id"
                        class="w-full min-w-0 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                    <option value="">Todas las actividades</option>
                    @foreach ($activities as $activity)
                        <option value="{{ $activity->id }}" @selected((string) request('activity_id') === (string) $activity->id)>
                            {{ Str::limit($activity->title, 30) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="min-w-0">
                <label for="type" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">
                    Tipo
                </label>
                <select id="type" name="type"
                        class="w-full min-w-0 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                    <option value="">Todos los tipos</option>
                    @foreach ($typeLabels as $value => $label)
                        <option value="{{ $value }}" @selected(request('type') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="min-w-0">
                <label for="status" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">
                    Estado
                </label>
                <select id="status" name="status"
                        class="w-full min-w-0 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                    <option value="">Todos los estados</option>
                    @foreach ($statusLabels as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-3 md:col-span-3">
                <button type="submit"
                        class="rounded-xl bg-indigo-600 px-6 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
                    Filtrar
                </button>

                @if (request()->hasAny($filterKeys))
                    <a href="{{ route('admin.results.index') }}"
                       class="rounded-xl border border-slate-200 bg-white px-6 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
                        Limpiar
                    </a>
                @endif
            </div>

        </form>

    </section>

    @if ($results->isEmpty())

        <!-- Mensaje cuando no hay resultados -->
        <section class="rounded-2xl border border-slate-200 bg-white p-12 text-center shadow-sm">

            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-400">
                <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
            </div>

            <h3 class="mt-4 text-lg font-black text-slate-900">Sin resultados</h3>

            <p class="mx-auto mt-2 max-w-sm text-sm text-slate-500">
                No se encontraron participaciones con los criterios seleccionados.
            </p>

            @if (request()->hasAny($filterKeys))
                <a href="{{ route('admin.results.index') }}"
                   class="mt-5 inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M2.985 14.652 5.166 12.47m12.9-5.618h4.992m0 0V2.86m0 4.992-3.18-3.18a8.25 8.25 0 0 0-13.804 3.698" />
                    </svg>
                    Limpiar filtros
                </a>
            @endif

        </section>

    @else

        <!-- Tabla (desktop) + Tarjetas (móvil) -->
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            {{-- Tarjetas: solo móvil --}}
            <div class="space-y-3 p-4 sm:hidden">
                @foreach ($results as $result)
                    <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate font-bold text-slate-900">{{ $result->activity?->title ?? '—' }}</p>
                                <p class="truncate text-xs text-slate-500">{{ $result->activity?->schoolClass?->name ?? '' }}</p>
                            </div>

                            <div class="shrink-0 text-right">
                                <p class="text-lg font-black tabular-nums text-slate-900">{{ $result->score ?? '—' }} <span class="text-xs font-medium text-slate-500">pts</span></p>
                                <p class="text-xs text-slate-500">Intento #{{ $result->attempt }}</p>
                            </div>
                        </div>

                        <div class="mt-3 flex items-center gap-3">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-xs font-black text-indigo-700">
                                {{ strtoupper(substr($result->student?->name ?? $result->team?->name ?? '?', 0, 1)) }}
                            </div>

                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-bold text-slate-900">
                                    {{ $result->student?->name ?? $result->team?->name ?? '—' }}
                                </p>
                                @if ($result->student_id !== null)
                                    <p class="text-xs font-bold text-slate-500">Individual</p>
                                @else
                                    <p class="text-xs font-bold text-indigo-600">Equipo</p>
                                    @if ($result->team?->members?->isNotEmpty())
                                        <p class="truncate text-xs text-slate-500">
                                            {{ $result->team->members->pluck('student.name')->filter()->implode(', ') }}
                                        </p>
                                    @endif
                                @endif
                            </div>

                            <span class="shrink-0 text-xs text-slate-500">
                                {{ admin_format_duration($result->elapsed_seconds) }}
                            </span>
                        </div>

                        <div class="mt-3 flex items-center gap-3 border-t border-slate-100 pt-3">
                            <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold {{ $statusColors[$result->status] ?? 'bg-slate-100 text-slate-600' }}">
                                <span class="h-1.5 w-1.5 rounded-full bg-current" aria-hidden="true"></span>
                                {{ $statusLabels[$result->status] ?? ucfirst($result->status) }}
                            </span>

                            <a href="{{ route('admin.results.show', $result->id) }}"
                               class="ml-auto rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-1">
                                Ver
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- Tabla: sm en adelante --}}
            <div class="hidden overflow-x-auto sm:block">
                <table class="min-w-full divide-y divide-slate-200 text-sm">

                    <thead class="bg-slate-50">
                        <tr class="text-center text-xs font-bold uppercase tracking-wider text-slate-500">
                            <th scope="col" class="px-6 py-4">Actividad</th>
                            <th scope="col" class="px-6 py-4">Participante</th>
                            <th scope="col" class="px-6 py-4">Estado</th>
                            <th scope="col" class="px-6 py-4">Puntaje</th>
                            <th scope="col" class="px-6 py-4">Intento</th>
                            <th scope="col" class="px-6 py-4">Duración</th>
                            <th scope="col" class="px-6 py-4">Acción</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 text-center">

                        @foreach ($results as $result)
                            <tr class="transition hover:bg-slate-50">

                                <td class="px-6 py-4">
                                    <p class="font-bold text-slate-900">{{ $result->activity?->title ?? '—' }}</p>
                                    <p class="mx-auto max-w-xs truncate text-xs text-slate-500">
                                        {{ $result->activity?->schoolClass?->name ?? '' }}
                                    </p>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-xs font-black text-indigo-700">
                                            {{ strtoupper(substr($result->student?->name ?? $result->team?->name ?? '?', 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <span class="block truncate font-bold text-slate-900">
                                                {{ $result->student?->name ?? $result->team?->name ?? '—' }}
                                            </span>
                                            @if ($result->student_id !== null)
                                                <span class="text-xs font-bold text-slate-500">Individual</span>
                                            @else
                                                <span class="text-xs font-bold text-indigo-600">Equipo</span>
                                            @endif
                                        </div>
                                    </div>
                                    @if ($result->student_id === null && $result->team?->members?->isNotEmpty())
                                        <p class="mx-auto mt-1 max-w-xs truncate text-xs text-slate-500">
                                            {{ $result->team->members->pluck('student.name')->filter()->implode(', ') }}
                                        </p>
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold {{ $statusColors[$result->status] ?? 'bg-slate-100 text-slate-600' }}">
                                        <span class="h-1.5 w-1.5 rounded-full bg-current" aria-hidden="true"></span>
                                        {{ $statusLabels[$result->status] ?? ucfirst($result->status) }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 font-bold tabular-nums text-slate-900">
                                    {{ $result->score ?? '—' }} <span class="text-xs font-medium text-slate-500">pts</span>
                                </td>

                                <td class="px-6 py-4 font-bold tabular-nums text-slate-900">
                                    #{{ $result->attempt }}
                                </td>

                                <td class="px-6 py-4 text-slate-600">
                                    {{ admin_format_duration($result->elapsed_seconds) }}
                                </td>

                                <td class="px-6 py-4">
                                    <a href="{{ route('admin.results.show', $result->id) }}"
                                       class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-1">
                                        Ver
                                    </a>
                                </td>

                            </tr>
                        @endforeach

                    </tbody>

                </table>
            </div>

            <div class="border-t border-slate-200 px-6 py-4">
                {{ $results->links() }}
            </div>

        </section>

    @endif

@endsection
