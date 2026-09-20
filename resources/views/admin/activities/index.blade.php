@extends('admin.layouts.app')

@section('title', 'Actividades | Klassio')

@section('page-title')
    Gestión de actividades
@endsection

@php
    $typeLabels = ['word_search' => 'Sopa de letras', 'crossword' => 'Crucigrama', 'matching' => 'Conecta', 'kahoot' => 'Quiz'];
    $typeColors = ['word_search' => 'bg-amber-100 text-amber-800', 'crossword' => 'bg-violet-100 text-violet-800', 'matching' => 'bg-cyan-100 text-cyan-800', 'kahoot' => 'bg-rose-100 text-rose-800'];
    $modeLabels = ['individual' => 'Individual', 'team' => 'Equipo'];
    $modeColors = ['individual' => 'bg-emerald-100 text-emerald-800', 'team' => 'bg-blue-100 text-blue-800'];
    $statusLabels = ['draft' => 'Borrador', 'published' => 'Publicada', 'closed' => 'Cerrada'];
    $statusColors = ['draft' => 'bg-slate-200 text-slate-600', 'published' => 'bg-emerald-100 text-emerald-800', 'closed' => 'bg-red-100 text-red-800'];
@endphp

@section('content')

    <!-- Filtros -->
    <section class="mb-6 rounded-3xl border border-slate-200 bg-white p-5 shadow-lg shadow-slate-200/60">

        <form method="GET" action="{{ route('admin.activities.index') }}"
              class="grid gap-4 md:grid-cols-3 lg:grid-cols-6">

            <div class="lg:col-span-2">
                <label for="search" class="mb-1 block text-xs font-black uppercase tracking-widest text-slate-500">
                    Buscar
                </label>
                <input type="text" id="search" name="search" value="{{ request('search') }}"
                       placeholder="Título de la actividad..."
                       class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-medium outline-none transition focus:border-slate-400 focus:ring-2 focus:ring-slate-200">
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
                <label for="mode" class="mb-1 block text-xs font-black uppercase tracking-widest text-slate-500">
                    Modo
                </label>
                <select id="mode" name="mode"
                        class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-medium outline-none transition focus:border-slate-400 focus:ring-2 focus:ring-slate-200">
                    <option value="">Todos los modos</option>
                    @foreach ($modeLabels as $value => $label)
                        <option value="{{ $value }}" @selected(request('mode') === $value)>{{ $label }}</option>
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

            <div>
                <label for="teacher_id" class="mb-1 block text-xs font-black uppercase tracking-widest text-slate-500">
                    Profesor
                </label>
                <select id="teacher_id" name="teacher_id"
                        class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-medium outline-none transition focus:border-slate-400 focus:ring-2 focus:ring-slate-200">
                    <option value="">Todos</option>
                    @foreach ($teachers as $teacher)
                        <option value="{{ $teacher->id }}" @selected((string) request('teacher_id') === (string) $teacher->id)>
                            {{ $teacher->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="class_id" class="mb-1 block text-xs font-black uppercase tracking-widest text-slate-500">
                    Clase
                </label>
                <select id="class_id" name="class_id"
                        class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-medium outline-none transition focus:border-slate-400 focus:ring-2 focus:ring-slate-200">
                    <option value="">Todas</option>
                    @foreach ($classes as $class)
                        <option value="{{ $class->id }}" @selected((string) request('class_id') === (string) $class->id)>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-3 lg:col-span-6">
                <button type="submit"
                        class="rounded-2xl bg-slate-900 px-6 py-2.5 text-sm font-black text-white shadow transition hover:bg-slate-700">
                    Filtrar
                </button>

                @if (request()->hasAny(['search', 'type', 'mode', 'status', 'teacher_id', 'class_id']))
                    <a href="{{ route('admin.activities.index') }}"
                       class="rounded-2xl border border-slate-200 bg-white px-6 py-2.5 text-sm font-black text-slate-600 transition hover:bg-slate-50">
                        Limpiar
                    </a>
                @endif
            </div>

        </form>

    </section>

    @if ($activities->isEmpty())
        <section class="rounded-3xl border border-slate-200 bg-white p-12 text-center shadow-lg shadow-slate-200/60">
            <div class="text-6xl">🔍</div>
            <h3 class="mt-4 text-xl font-black text-slate-900">Sin resultados</h3>
            <p class="mt-2 text-sm text-slate-500">No se encontraron actividades con los criterios seleccionados.</p>
        </section>
    @else

        <!-- Tabla -->
        <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-lg shadow-slate-200/60">

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">

                    <thead class="bg-slate-50">
                        <tr class="text-left text-xs font-black uppercase tracking-widest text-slate-500">
                            <th class="px-6 py-4">Actividad</th>
                            <th class="px-6 py-4">Clase</th>
                            <th class="px-6 py-4">Profesor</th>
                            <th class="px-6 py-4">Tipo</th>
                            <th class="px-6 py-4">Modo</th>
                            <th class="px-6 py-4">Estado</th>
                            <th class="px-6 py-4">Participaciones</th>
                            <th class="px-6 py-4 text-right">Acción</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">

                        @foreach ($activities as $activity)
                            <tr class="transition hover:bg-slate-50">

                                <td class="px-6 py-4">
                                    <p class="font-black text-slate-900">{{ $activity->title }}</p>
                                    <p class="max-w-xs truncate text-xs text-slate-500">{{ $activity->description }}</p>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="font-bold text-slate-700">{{ $activity->schoolClass?->name ?? '—' }}</span>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="font-bold text-slate-700">{{ $activity->teacher?->name ?? '—' }}</span>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-black {{ $typeColors[$activity->type] ?? 'bg-slate-200 text-slate-600' }}">
                                        {{ $typeLabels[$activity->type] ?? ucfirst($activity->type) }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-black {{ $modeColors[$activity->mode] ?? 'bg-slate-200 text-slate-600' }}">
                                        {{ $modeLabels[$activity->mode] ?? ucfirst($activity->mode) }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-black {{ $statusColors[$activity->status] ?? 'bg-slate-200 text-slate-600' }}">
                                        {{ $statusLabels[$activity->status] ?? ucfirst($activity->status) }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 font-black tabular-nums text-slate-900">
                                    {{ number_format($activity->participations_count) }}
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.activities.show', $activity->id) }}"
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
                {{ $activities->links() }}
            </div>

        </section>

    @endif

@endsection