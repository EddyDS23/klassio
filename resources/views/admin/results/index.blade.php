@extends('admin.layouts.app')

@section('title', 'Resultados | Klassio')

@section('page-title')
    Resultados de participación
@endsection

@php
    $typeLabels = ['word_search' => 'Sopa de letras', 'crossword' => 'Crucigrama', 'matching' => 'Conecta', 'kahoot' => 'Quiz'];
    $statusLabels = ['started' => 'En curso', 'completed' => 'Completada', 'abandoned' => 'Abandonada', 'expired' => 'Expirada'];
    $statusColors = ['started' => 'bg-amber-100 text-amber-800', 'completed' => 'bg-emerald-100 text-emerald-800', 'abandoned' => 'bg-slate-200 text-slate-600', 'expired' => 'bg-red-100 text-red-800'];

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
    <section class="mb-6 rounded-3xl border border-slate-200 bg-white p-5 shadow-lg shadow-slate-200/60">

        <form method="GET" action="{{ route('admin.results.index') }}"
              class="grid gap-4 md:grid-cols-3">

            <div>
                <label for="activity_id" class="mb-1 block text-xs font-black uppercase tracking-widest text-slate-500">
                    Actividad
                </label>
                <select id="activity_id" name="activity_id"
                        class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-medium outline-none transition focus:border-slate-400 focus:ring-2 focus:ring-slate-200">
                    <option value="">Todas las actividades</option>
                    @foreach ($activities as $activity)
                        <option value="{{ $activity->id }}" @selected((string) request('activity_id') === (string) $activity->id)>
                            {{ $activity->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="type" class="mb-1 block text-xs font-black uppercase tracking-widest text-slate-500">
                    Tipo
                </label>
                <select id="type" name="type"
                        class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-medium outline-none transition focus:border-slate-400 focus:ring-2 focus:ring-slate-200">
                    <option value="">Todos los tipos</option>
                    @foreach ($typeLabels as $value => $label)
                        <option value="{{ $value }}" @selected(request('type') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status" class="mb-1 block text-xs font-black uppercase tracking-widest text-slate-500">
                    Estado
                </label>
                <select id="status" name="status"
                        class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-medium outline-none transition focus:border-slate-400 focus:ring-2 focus:ring-slate-200">
                    <option value="">Todos los estados</option>
                    @foreach ($statusLabels as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-3 md:col-span-3">
                <button type="submit"
                        class="rounded-2xl bg-slate-900 px-6 py-2.5 text-sm font-black text-white shadow transition hover:bg-slate-700">
                    Filtrar
                </button>

                @if (request()->hasAny(['activity_id', 'type', 'status']))
                    <a href="{{ route('admin.results.index') }}"
                       class="rounded-2xl border border-slate-200 bg-white px-6 py-2.5 text-sm font-black text-slate-600 transition hover:bg-slate-50">
                        Limpiar
                    </a>
                @endif
            </div>

        </form>

    </section>

    @if ($results->isEmpty())
        <section class="rounded-3xl border border-slate-200 bg-white p-12 text-center shadow-lg shadow-slate-200/60">
            <div class="text-6xl">🔍</div>
            <h3 class="mt-4 text-xl font-black text-slate-900">Sin resultados</h3>
            <p class="mt-2 text-sm text-slate-500">No se encontraron participaciones con los criterios seleccionados.</p>
        </section>
    @else

        <!-- Tabla -->
        <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-lg shadow-slate-200/60">

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">

                    <thead class="bg-slate-50">
                        <tr class="text-left text-xs font-black uppercase tracking-widest text-slate-500">
                            <th class="px-6 py-4">Actividad</th>
                            <th class="px-6 py-4">Participante</th>
                            <th class="px-6 py-4">Estado</th>
                            <th class="px-6 py-4">Puntaje</th>
                            <th class="px-6 py-4">Intento</th>
                            <th class="px-6 py-4">Duración</th>
                            <th class="px-6 py-4 text-right">Acción</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">

                        @foreach ($results as $result)
                            <tr class="transition hover:bg-slate-50">

                                <td class="px-6 py-4">
                                    <p class="font-black text-slate-900">{{ $result->activity?->title ?? '—' }}</p>
                                    <p class="max-w-xs truncate text-xs text-slate-500">
                                        {{ $result->activity?->schoolClass?->name ?? '' }}
                                    </p>
                                </td>

                                <td class="px-6 py-4">
                                    @if ($result->student_id !== null)
                                        <div class="flex items-center gap-2">
                                            <span class="font-black text-slate-900">{{ $result->student?->name ?? '—' }}</span>
                                            <span class="rounded-full bg-cyan-100 px-2 py-0.5 text-xs font-black text-cyan-800">
                                                Individual
                                            </span>
                                        </div>
                                    @else
                                        <div class="flex items-center gap-2">
                                            <span class="font-black text-slate-900">{{ $result->team?->name ?? '—' }}</span>
                                            <span class="rounded-full bg-blue-100 px-2 py-0.5 text-xs font-black text-blue-800">
                                                Equipo
                                            </span>
                                        </div>
                                        @if ($result->team?->members?->isNotEmpty())
                                            <p class="mt-1 max-w-xs truncate text-xs text-slate-500">
                                                {{ $result->team->members->pluck('student.name')->filter()->implode(', ') }}
                                            </p>
                                        @endif
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-black {{ $statusColors[$result->status] ?? 'bg-slate-200 text-slate-600' }}">
                                        {{ $statusLabels[$result->status] ?? ucfirst($result->status) }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 font-black tabular-nums text-slate-900">
                                    {{ $result->score ?? '—' }} <span class="text-xs text-slate-500">pts</span>
                                </td>

                                <td class="px-6 py-4 font-black tabular-nums text-slate-900">
                                    #{{ $result->attempt }}
                                </td>

                                <td class="px-6 py-4 text-slate-600">
                                    {{ admin_format_duration($result->elapsed_seconds) }}
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.results.show', $result->id) }}"
                                       class="rounded-xl bg-slate-100 px-4 py-2 text-xs font-black text-slate-700 transition hover:bg-slate-900 hover:text-white">
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