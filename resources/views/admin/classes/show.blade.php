@extends('admin.layouts.app')

@section('title', 'Detalle de clase | Klassio')

@section('page-title')
    Detalle de la clase
@endsection

@section('page-actions')
    <a href="{{ route('admin.classes.index') }}"
       class="inline-flex self-start items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
        </svg>
        Volver
    </a>
@endsection

@php
    $activeStudents = $class->enrollments->where('status', 'active');
    $statusLabels = ['active' => 'Activa', 'archived' => 'Archivada'];
@endphp

@section('content')

    <!-- Encabezado de la clase -->
    <section class="relative mb-8 overflow-hidden rounded-2xl bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 p-6 text-white shadow-xl sm:p-8">
        <div class="pointer-events-none absolute -right-20 -top-20 h-72 w-72 rounded-full bg-indigo-500/20 blur-3xl"></div>

        <div class="relative z-10 flex flex-col gap-6 md:flex-row md:items-center md:justify-between">

            <div class="min-w-0">
                <div class="mb-3 flex flex-wrap items-center gap-2">
                    <span class="inline-flex rounded-full bg-white/10 px-3 py-1 text-xs font-bold uppercase tracking-wider text-indigo-200 ring-1 ring-white/10">
                        Clase
                    </span>
                    <span class="rounded-full bg-white/10 px-3 py-1 font-mono text-xs font-bold text-teal-300 ring-1 ring-white/10">
                        {{ $class->code }}
                    </span>
                </div>

                <h2 class="text-3xl font-black tracking-tight text-white sm:text-4xl">
                    {{ $class->name }}
                </h2>

                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300">
                    {{ $class->description }}
                </p>
            </div>

            <div class="flex shrink-0 flex-col items-start gap-3 md:items-end">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-4 py-1.5 text-xs font-bold text-white ring-1 ring-white/20">
                    <span class="h-1.5 w-1.5 rounded-full {{ $class->status === 'active' ? 'bg-emerald-400' : 'bg-slate-400' }}" aria-hidden="true"></span>
                    {{ $statusLabels[$class->status] ?? ucfirst($class->status) }}
                </span>

                <div class="flex items-center gap-2 text-sm font-bold text-white">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white/10 ring-1 ring-white/20">
                        <svg class="h-4 w-4 text-indigo-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                    </span>
                    <span class="max-w-52 truncate">
                        {{ $class->teacher?->name ?? 'Sin profesor asignado' }}
                    </span>
                </div>
            </div>

        </div>
    </section>

    <!-- Estadísticas -->
    <section class="mb-6 grid gap-4 sm:grid-cols-3" aria-label="Estadísticas de la clase">

        @php
            $statIconClass = 'h-6 w-6';
            $cards = [
                [
                    'label' => 'Estudiantes activos',
                    'value' => $activeStudents->count(),
                    'bg' => 'bg-teal-50 text-teal-600',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 14.25h6.375a3.375 3.375 0 0 0 3.375-3.375V4.875a3.375 3.375 0 0 0-3.375-3.375h-6.75a3.375 3.375 0 0 0-3.375 3.375v11.25c0 3.654 2.962 6.616 6.616 6.616M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />',
                ],
                [
                    'label' => 'Actividades',
                    'value' => $class->activities->count(),
                    'bg' => 'bg-indigo-50 text-indigo-600',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M14.25 6.087c0-.355.186-.676.401-.959.221-.29.349-.634.349-1.003 0-1.036-1.007-1.875-2.25-1.875s-2.25.84-2.25 1.875c0 .369.128.713.349 1.003.215.283.401.604.401.959v0a.64.64 0 0 1-.657.643 48.39 48.39 0 0 1-3.409-.042.75.75 0 0 1-.707-.632 48.264 48.264 0 0 1-.034-2.897.75.75 0 0 1 .515-.707 48.334 48.334 0 0 1 2.887-.085v0c.355 0 .676-.186.959-.401.29-.221.634-.349 1.003-.349 1.036 0 1.875-1.007 1.875-2.25S12.535 1.5 11.5 1.5s-1.875 1.007-1.875 2.25c0 .369.128.713.349 1.003.215.283.401.604.401.959v0c0 .355-.186.676-.401.959-.221.29-.349.634-.349 1.003 0 1.036-1.007 1.875-2.25 1.875s-1.875-1.007-1.875-2.25c0-.369.128-.713.349-1.003.215-.283.401-.604.401-.959v0a.64.64 0 0 0-.657-.643 48.39 48.39 0 0 0-3.409.042.75.75 0 0 0-.707.632 48.264 48.264 0 0 0-.034 2.897.75.75 0 0 0 .515.707c.438.154.902.226 1.397.226v0c.466 0 .872-.054 1.21-.14v5.628c0 1.499 1.193 2.755 2.691 2.755h.002c.325 0 .591.266.591.591v4.875c0 .414.336.75.75.75h.006a.75.75 0 0 0 .75-.75v-4.875c0-.325.266-.591.591-.591h.002c1.498 0 2.691-1.256 2.691-2.755V9.75c.338.086.744.14 1.21.14v0c.495 0 .959-.072 1.397-.226a.75.75 0 0 0 .515-.707 48.264 48.264 0 0 0-.034-2.897.75.75 0 0 0-.707-.632 48.39 48.39 0 0 0-3.409-.042.64.64 0 0 0-.657.643Z" />',
                ],
                [
                    'label' => 'Total de inscripciones',
                    'value' => $class->enrollments->count(),
                    'bg' => 'bg-violet-50 text-violet-600',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />',
                ],
            ];
        @endphp

        @foreach ($cards as $card)
            <div class="rounded-2xl border border-slate-200 bg-white p-5 text-center shadow-sm">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl {{ $card['bg'] }}">
                    <svg class="{{ $statIconClass }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        {!! $card['icon'] !!}
                    </svg>
                </div>
                <p class="mt-4 text-3xl font-black tabular-nums text-slate-900">
                    {{ number_format($card['value']) }}
                </p>
                <p class="mt-1 text-sm font-bold text-slate-500">{{ $card['label'] }}</p>
            </div>
        @endforeach

    </section>

    @if ($class->enrollments->isEmpty())

        <!-- Sin estudiantes -->
        <section class="mb-6 rounded-2xl border border-slate-200 bg-white p-10 text-center shadow-sm">

            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-violet-50 text-violet-400">
                <svg class="h-7 w-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                </svg>
            </div>

            <h3 class="mt-3 text-lg font-black text-slate-900">Sin estudiantes</h3>
            <p class="mt-1 text-sm text-slate-500">Aún no hay estudiantes inscritos en esta clase.</p>

        </section>

    @else

        <!-- Estudiantes -->
        <section class="mb-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-200 px-6 py-4">
                <h3 class="text-lg font-black text-slate-900">Estudiantes inscritos</h3>
            </div>

            {{-- Tarjetas: solo móvil --}}
            <div class="space-y-3 p-4 sm:hidden">
                @foreach ($class->enrollments as $enrollment)
                    <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-xs font-black text-indigo-700">
                                {{ strtoupper(substr($enrollment->student?->name ?? '?', 0, 1)) }}
                            </div>

                            <div class="min-w-0 flex-1">
                                <p class="truncate font-bold text-slate-900">{{ $enrollment->student?->name ?? '—' }}</p>
                                <p class="truncate text-xs text-slate-500">{{ $enrollment->student?->email ?? '—' }}</p>
                            </div>
                        </div>

                        <div class="mt-3 flex items-center justify-between border-t border-slate-100 pt-3">
                            <span class="text-xs text-slate-500">
                                Inscrito el {{ $enrollment->created_at?->format('d/m/Y') }}
                            </span>

                            <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold {{ $enrollment->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                <span class="h-1.5 w-1.5 rounded-full bg-current" aria-hidden="true"></span>
                                {{ $enrollment->status === 'active' ? 'Activo' : 'Eliminado' }}
                            </span>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- Tabla: sm en adelante --}}
            <div class="hidden overflow-x-auto sm:block">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr class="text-center text-xs font-bold uppercase tracking-wider text-slate-500">
                            <th scope="col" class="px-6 py-4">Estudiante</th>
                            <th scope="col" class="px-6 py-4">Correo</th>
                            <th scope="col" class="px-6 py-4">Inscripción</th>
                            <th scope="col" class="px-6 py-4">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-center">
                        @foreach ($class->enrollments as $enrollment)
                            <tr class="transition hover:bg-slate-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-3">
                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-xs font-black text-indigo-700">
                                            {{ strtoupper(substr($enrollment->student?->name ?? '?', 0, 1)) }}
                                        </div>
                                        <span class="font-bold text-slate-900">
                                            {{ $enrollment->student?->name ?? '—' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-slate-600">
                                    {{ $enrollment->student?->email ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-slate-600">
                                    {{ $enrollment->created_at?->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold {{ $enrollment->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                        <span class="h-1.5 w-1.5 rounded-full bg-current" aria-hidden="true"></span>
                                        {{ $enrollment->status === 'active' ? 'Activo' : 'Eliminado' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </section>

    @endif

    @if ($class->activities->isEmpty())

        <!-- Sin actividades -->
        <section class="rounded-2xl border border-slate-200 bg-white p-10 text-center shadow-sm">

            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-400">
                <svg class="h-7 w-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 6.087c0-.355.186-.676.401-.959.221-.29.349-.634.349-1.003 0-1.036-1.007-1.875-2.25-1.875s-2.25.84-2.25 1.875c0 .369.128.713.349 1.003.215.283.401.604.401.959v0a.64.64 0 0 1-.657.643 48.39 48.39 0 0 1-3.409-.042.75.75 0 0 1-.707-.632 48.264 48.264 0 0 1-.034-2.897.75.75 0 0 1 .515-.707 48.334 48.334 0 0 1 2.887-.085v0c.355 0 .676-.186.959-.401.29-.221.634-.349 1.003-.349 1.036 0 1.875-1.007 1.875-2.25S12.535 1.5 11.5 1.5s-1.875 1.007-1.875 2.25c0 .369.128.713.349 1.003.215.283.401.604.401.959v0c0 .355-.186.676-.401.959-.221.29-.349.634-.349 1.003 0 1.036-1.007 1.875-2.25 1.875s-1.875-1.007-1.875-2.25c0-.369.128-.713.349-1.003.215-.283.401-.604.401-.959v0a.64.64 0 0 0-.657-.643 48.39 48.39 0 0 0-3.409.042.75.75 0 0 0-.707.632 48.264 48.264 0 0 0-.034 2.897.75.75 0 0 0 .515.707c.438.154.902.226 1.397.226v0c.466 0 .872-.054 1.21-.14v5.628c0 1.499 1.193 2.755 2.691 2.755h.002c.325 0 .591.266.591.591v4.875c0 .414.336.75.75.75h.006a.75.75 0 0 0 .75-.75v-4.875c0-.325.266-.591.591-.591h.002c1.498 0 2.691-1.256 2.691-2.755V9.75c.338.086.744.14 1.21.14v0c.495 0 .959-.072 1.397-.226a.75.75 0 0 0 .515-.707 48.264 48.264 0 0 0-.034-2.897.75.75 0 0 0-.707-.632 48.39 48.39 0 0 0-3.409-.042.64.64 0 0 0-.657.643Z" />
                </svg>
            </div>

            <h3 class="mt-3 text-lg font-black text-slate-900">Sin actividades</h3>
            <p class="mt-1 text-sm text-slate-500">El profesor aún no ha creado actividades en esta clase.</p>

        </section>

    @else

        <!-- Actividades -->
        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="grid gap-4 p-6 sm:grid-cols-2 lg:grid-cols-3">

                @foreach ($class->activities as $activity)
                    <a href="{{ route('admin.activities.show', $activity->id) }}"
                       class="rounded-xl border border-slate-200 bg-white p-4 transition hover:-translate-y-0.5 hover:border-indigo-200 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">

                        <div class="flex items-start justify-between gap-2">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 6.087c0-.355.186-.676.401-.959.221-.29.349-.634.349-1.003 0-1.036-1.007-1.875-2.25-1.875s-2.25.84-2.25 1.875c0 .369.128.713.349 1.003.215.283.401.604.401.959v0a.64.64 0 0 1-.657.643 48.39 48.39 0 0 1-3.409-.042.75.75 0 0 1-.707-.632 48.264 48.264 0 0 1-.034-2.897.75.75 0 0 1 .515-.707 48.334 48.334 0 0 1 2.887-.085v0c.355 0 .676-.186.959-.401.29-.221.634-.349 1.003-.349 1.036 0 1.875-1.007 1.875-2.25S12.535 1.5 11.5 1.5s-1.875 1.007-1.875 2.25c0 .369.128.713.349 1.003.215.283.401.604.401.959v0c0 .355-.186.676-.401.959-.221.29-.349.634-.349 1.003 0 1.036-1.007 1.875-2.25 1.875s-1.875-1.007-1.875-2.25c0-.369.128-.713.349-1.003.215-.283.401-.604.401-.959v0a.64.64 0 0 0-.657-.643 48.39 48.39 0 0 0-3.409.042.75.75 0 0 0-.707.632 48.264 48.264 0 0 0-.034 2.897.75.75 0 0 0 .515.707c.438.154.902.226 1.397.226v0c.466 0 .872-.054 1.21-.14v5.628c0 1.499 1.193 2.755 2.691 2.755h.002c.325 0 .591.266.591.591v4.875c0 .414.336.75.75.75h.006a.75.75 0 0 0 .75-.75v-4.875c0-.325.266-.591.591-.591h.002c1.498 0 2.691-1.256 2.691-2.755V9.75c.338.086.744.14 1.21.14v0c.495 0 .959-.072 1.397-.226a.75.75 0 0 0 .515-.707 48.264 48.264 0 0 0-.034-2.897.75.75 0 0 0-.707-.632 48.39 48.39 0 0 0-3.409-.042.64.64 0 0 0-.657.643Z" />
                                </svg>
                            </div>
                            <span class="inline-block rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
                                {{ ucfirst(str_replace('_', ' ', $activity->type)) }}
                            </span>
                        </div>

                        <h4 class="mt-3 truncate font-bold text-slate-900">{{ $activity->title }}</h4>
                        <p class="mt-1 line-clamp-2 text-xs leading-5 text-slate-500">{{ $activity->description }}</p>

                    </a>
                @endforeach

            </div>

        </section>

    @endif

@endsection
