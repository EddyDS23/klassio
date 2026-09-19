@extends('admin.layouts.app')

@section('title', 'Clases | Klassio')

@section('page-title')
    Gestión de clases
@endsection

@php
    $statusLabels = ['active' => 'Activa', 'archived' => 'Archivada'];
    $statusColors = ['active' => 'bg-emerald-100 text-emerald-800', 'archived' => 'bg-slate-200 text-slate-600'];
@endphp

@section('content')

    <!-- Filtros -->
    <section class="mb-6 rounded-3xl border border-slate-200 bg-white p-5 shadow-lg shadow-slate-200/60">

        <form method="GET" action="{{ route('admin.classes.index') }}"
              class="grid gap-4 md:grid-cols-4">

            <div class="md:col-span-2">
                <label for="search" class="mb-1 block text-xs font-black uppercase tracking-widest text-slate-500">
                    Buscar
                </label>
                <input type="text" id="search" name="search" value="{{ request('search') }}"
                       placeholder="Nombre de la clase o código..."
                       class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-medium outline-none transition focus:border-slate-400 focus:ring-2 focus:ring-slate-200">
            </div>

            <div>
                <label for="teacher_id" class="mb-1 block text-xs font-black uppercase tracking-widest text-slate-500">
                    Profesor
                </label>
                <select id="teacher_id" name="teacher_id"
                        class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-medium outline-none transition focus:border-slate-400 focus:ring-2 focus:ring-slate-200">
                    <option value="">Todos los profesores</option>
                    @foreach ($teachers as $teacher)
                        <option value="{{ $teacher->id }}" @selected((string) request('teacher_id') === (string) $teacher->id)>
                            {{ $teacher->name }}
                        </option>
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

            <div class="flex items-end gap-3 md:col-span-4">
                <button type="submit"
                        class="rounded-2xl bg-slate-900 px-6 py-2.5 text-sm font-black text-white shadow transition hover:bg-slate-700">
                    Filtrar
                </button>

                @if (request()->hasAny(['search', 'status', 'teacher_id']))
                    <a href="{{ route('admin.classes.index') }}"
                       class="rounded-2xl border border-slate-200 bg-white px-6 py-2.5 text-sm font-black text-slate-600 transition hover:bg-slate-50">
                        Limpiar
                    </a>
                @endif
            </div>

        </form>

    </section>

    @if ($classes->isEmpty())
        <section class="rounded-3xl border border-slate-200 bg-white p-12 text-center shadow-lg shadow-slate-200/60">
            <div class="text-6xl">🔍</div>
            <h3 class="mt-4 text-xl font-black text-slate-900">Sin resultados</h3>
            <p class="mt-2 text-sm text-slate-500">No se encontraron clases con los criterios seleccionados.</p>
        </section>
    @else

        <!-- Tabla -->
        <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-lg shadow-slate-200/60">

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">

                    <thead class="bg-slate-50">
                        <tr class="text-left text-xs font-black uppercase tracking-widest text-slate-500">
                            <th class="px-6 py-4">Clase</th>
                            <th class="px-6 py-4">Código</th>
                            <th class="px-6 py-4">Profesor</th>
                            <th class="px-6 py-4">Estudiantes</th>
                            <th class="px-6 py-4">Actividades</th>
                            <th class="px-6 py-4">Estado</th>
                            <th class="px-6 py-4 text-right">Acción</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">

                        @foreach ($classes as $class)
                            <tr class="transition hover:bg-slate-50">

                                <td class="px-6 py-4">
                                    <p class="font-black text-slate-900">{{ $class->name }}</p>
                                    <p class="max-w-xs truncate text-xs text-slate-500">{{ $class->description }}</p>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="rounded-xl bg-slate-100 px-3 py-1 font-mono text-xs font-black text-slate-700">
                                        {{ $class->code }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="font-bold text-slate-700">{{ $class->teacher?->name ?? '—' }}</span>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="font-black tabular-nums text-slate-900">{{ $class->active_students_count }}</span>
                                    <span class="font-black text-xs text-slate-500">/ {{ $class->enrollments_count ?? 0 }}</span>
                                </td>

                                <td class="px-6 py-4 font-black tabular-nums text-slate-900">
                                    {{ $class->activities_count }}
                                </td>

                                <td class="px-6 py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-black {{ $statusColors[$class->status] ?? 'bg-slate-200 text-slate-600' }}">
                                        {{ $statusLabels[$class->status] ?? ucfirst($class->status) }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.classes.show', $class->id) }}"
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
                {{ $classes->links() }}
            </div>

        </section>

    @endif

@endsection