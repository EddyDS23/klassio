@extends('admin.layouts.app')

@section('title', 'Clases | Klassio')

@section('page-title')
    Gestión de clases
@endsection

@php
    $statusLabels = ['active' => 'Activa', 'archived' => 'Archivada'];
    $statusColors = ['active' => 'bg-emerald-100 text-emerald-700', 'archived' => 'bg-slate-100 text-slate-600'];
@endphp

@section('content')

    <!-- Filtros -->
    <section class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

        <form method="GET" action="{{ route('admin.classes.index') }}"
              class="grid gap-4 md:grid-cols-4">

            <div class="md:col-span-2">
                <label for="search" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">
                    Buscar
                </label>
                <input type="text" id="search" name="search" value="{{ request('search') }}"
                       placeholder="Nombre de la clase o código..."
                       class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
            </div>

            <div class="min-w-0">
                <label for="teacher_id" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">
                    Profesor
                </label>
                <select id="teacher_id" name="teacher_id"
                        class="w-full min-w-0 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                    <option value="">Todos los profesores</option>
                    @foreach ($teachers as $teacher)
                        <option value="{{ $teacher->id }}" @selected((string) request('teacher_id') === (string) $teacher->id)>
                            {{ Str::limit($teacher->name, 30) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">
                    Estado
                </label>
                <select id="status" name="status"
                        class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100">
                    <option value="">Todos los estados</option>
                    @foreach ($statusLabels as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-3 md:col-span-4">
                <button type="submit"
                        class="rounded-xl bg-indigo-600 px-6 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
                    Filtrar
                </button>

                @if (request()->hasAny(['search', 'status', 'teacher_id']))
                    <a href="{{ route('admin.classes.index') }}"
                       class="rounded-xl border border-slate-200 bg-white px-6 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
                        Limpiar
                    </a>
                @endif
            </div>

        </form>

    </section>

    @if ($classes->isEmpty())

        <!-- Mensaje cuando no hay resultados -->
        <section class="rounded-2xl border border-slate-200 bg-white p-12 text-center shadow-sm">

            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-400">
                <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
            </div>

            <h3 class="mt-4 text-lg font-black text-slate-900">Sin resultados</h3>

            <p class="mx-auto mt-2 max-w-sm text-sm text-slate-500">
                No se encontraron clases con los criterios seleccionados.
            </p>

            @if (request()->hasAny(['search', 'status', 'teacher_id']))
                <a href="{{ route('admin.classes.index') }}"
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
                @foreach ($classes as $class)
                    <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate font-bold text-slate-900">{{ $class->name }}</p>
                                <p class="line-clamp-2 text-xs leading-5 text-slate-500">{{ $class->description }}</p>
                            </div>

                            <span class="shrink-0 rounded-lg bg-indigo-50 px-2.5 py-1 font-mono text-xs font-bold text-indigo-700 ring-1 ring-indigo-100">
                                {{ $class->code }}
                            </span>
                        </div>

                        <p class="mt-2 truncate text-xs text-slate-600">
                            {{ $class->teacher?->name ?? 'Sin profesor asignado' }}
                        </p>

                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700">
                                {{ $class->active_students_count }} estudiantes
                            </span>

                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700">
                                {{ $class->activities_count }} actividades
                            </span>

                            <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold {{ $statusColors[$class->status] ?? 'bg-slate-100 text-slate-600' }}">
                                <span class="h-1.5 w-1.5 rounded-full bg-current" aria-hidden="true"></span>
                                {{ $statusLabels[$class->status] ?? ucfirst($class->status) }}
                            </span>
                        </div>

                        <div class="mt-3 border-t border-slate-100 pt-3">
                            <a href="{{ route('admin.classes.show', $class->id) }}"
                               class="block w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-center text-xs font-bold text-slate-700 transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-1">
                                Ver clase
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
                            <th scope="col" class="px-6 py-4">Clase</th>
                            <th scope="col" class="px-6 py-4">Código</th>
                            <th scope="col" class="px-6 py-4">Profesor</th>
                            <th scope="col" class="px-6 py-4">Estudiantes</th>
                            <th scope="col" class="px-6 py-4">Actividades</th>
                            <th scope="col" class="px-6 py-4">Estado</th>
                            <th scope="col" class="px-6 py-4">Acción</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 text-center">

                        @foreach ($classes as $class)
                            <tr class="transition hover:bg-slate-50">

                                <td class="px-6 py-4">
                                    <p class="font-bold text-slate-900">{{ $class->name }}</p>
                                    <p class="max-w-xs truncate text-xs text-slate-500">{{ $class->description }}</p>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="inline-block rounded-lg bg-indigo-50 px-3 py-1 font-mono text-xs font-bold text-indigo-700 ring-1 ring-indigo-100">
                                        {{ $class->code }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="font-medium text-slate-700">{{ $class->teacher?->name ?? '—' }}</span>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="font-bold tabular-nums text-slate-900">{{ $class->active_students_count }}</span>
                                </td>

                                <td class="px-6 py-4 font-bold tabular-nums text-slate-900">
                                    {{ $class->activities_count }}
                                </td>

                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold {{ $statusColors[$class->status] ?? 'bg-slate-100 text-slate-600' }}">
                                        <span class="h-1.5 w-1.5 rounded-full bg-current" aria-hidden="true"></span>
                                        {{ $statusLabels[$class->status] ?? ucfirst($class->status) }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('admin.classes.show', $class->id) }}"
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
                {{ $classes->links() }}
            </div>

        </section>

    @endif

@endsection
