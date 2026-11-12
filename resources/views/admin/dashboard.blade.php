@extends('admin.layouts.app')

@section('title', 'Dashboard | Klassio')

@section('page-title')
    Resumen general
@endsection

@section('content')

    <!-- =====================================
         HERO BANNER EJECUTIVO
     ====================================== -->
    <section class="relative mb-8 overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 p-6 text-white shadow-xl sm:p-8 lg:p-10">
        <!-- Patrón sutil decorativo -->
        <div class="pointer-events-none absolute inset-0 opacity-10" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 24px 24px;"></div>
        <div class="pointer-events-none absolute -right-20 -top-20 h-72 w-72 rounded-full bg-indigo-500/20 blur-3xl"></div>

        <div class="relative z-10 flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
            <div class="max-w-2xl">
                <div class="mb-3 inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3.5 py-1 text-xs font-bold uppercase tracking-wider text-indigo-200 backdrop-blur-md">
                    <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                    Panel de administración
                </div>

                <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                    Bienvenido, {{ auth()->user()->name }}
                </h2>

                <p class="mt-3 text-sm leading-6 text-slate-300 sm:text-base">
                    Supervisa usuarios, clases, actividades y métricas clave de rendimiento en toda la plataforma desde un solo lugar.
                </p>
            </div>

            <div class="hidden h-20 w-20 shrink-0 items-center justify-center rounded-2xl border border-white/15 bg-white/10 text-indigo-300 backdrop-blur-md md:flex">
                <svg class="h-10 w-10" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                </svg>
            </div>
        </div>
    </section>

    <!-- =====================================
         MÉTRICAS PRINCIPALES (KPIs)
     ====================================== -->
    @php
        $cards = [
            [
                'label' => 'Usuarios totales',
                'value' => $stats['users'] ?? 0,
                'route' => route('admin.users.index'),
                'bg'    => 'bg-indigo-50 text-indigo-600 ring-indigo-100',
                'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />',
            ],
            [
                'label' => 'Estudiantes',
                'value' => $stats['students'] ?? 0,
                'route' => route('admin.users.index', ['role' => 'student']),
                'bg'    => 'bg-cyan-50 text-cyan-600 ring-cyan-100',
                'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" />',
            ],
            [
                'label' => 'Maestros',
                'value' => $stats['teachers'] ?? 0,
                'route' => route('admin.users.index', ['role' => 'teacher']),
                'bg'    => 'bg-emerald-50 text-emerald-600 ring-emerald-100',
                'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />',
            ],
            [
                'label' => 'Clases',
                'value' => $stats['classes'] ?? 0,
                'route' => route('admin.classes.index'),
                'bg'    => 'bg-amber-50 text-amber-600 ring-amber-100',
                'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />',
            ],
            [
                'label' => 'Actividades',
                'value' => $stats['activities'] ?? 0,
                'route' => route('admin.activities.index'),
                'bg'    => 'bg-purple-50 text-purple-600 ring-purple-100',
                'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />',
            ],
            [
                'label' => 'Participaciones',
                'value' => $stats['participations'] ?? 0,
                'route' => route('admin.results.index'),
                'bg'    => 'bg-rose-50 text-rose-600 ring-rose-100',
                'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0" />',
            ],
            [
                'label' => 'Usuarios activos',
                'value' => $stats['activeUsers'] ?? 0,
                'route' => route('admin.users.index'),
                'bg'    => 'bg-teal-50 text-teal-600 ring-teal-100',
                'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
            ],
            [
                'label' => 'Suspendidos',
                'value' => $stats['suspendedUsers'] ?? 0,
                'route' => route('admin.users.index'),
                'bg'    => 'bg-red-50 text-red-600 ring-red-100',
                'alert' => ($stats['suspendedUsers'] ?? 0) > 0,
                'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />',
            ],
        ];
    @endphp

    <section class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($cards as $card)
            <a href="{{ $card['route'] }}"
               class="group relative flex flex-col justify-between rounded-2xl border {{ !empty($card['alert']) ? 'border-red-200 bg-red-50/20' : 'border-slate-200/80 bg-white' }} p-5 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-indigo-200 hover:shadow-md">

                <div class="flex items-start justify-between">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl ring-1 {{ $card['bg'] }} transition-transform duration-200 group-hover:scale-105">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            {!! $card['icon'] !!}
                        </svg>
                    </div>

                    <span class="text-slate-300 transition-transform duration-200 group-hover:translate-x-0.5 group-hover:text-indigo-500">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25" />
                        </svg>
                    </span>
                </div>

                <div class="mt-5">
                    <div class="text-3xl font-extrabold tabular-nums tracking-tight text-slate-900">
                        {{ number_format($card['value']) }}
                    </div>
                    <p class="mt-1 text-xs font-bold uppercase tracking-wider text-slate-500">
                        {{ $card['label'] }}
                    </p>
                </div>
            </a>
        @endforeach
    </section>

    <!-- =====================================
         GRÁFICAS ANALÍTICAS
     ====================================== -->
    <section class="mb-8 grid gap-6 lg:grid-cols-2">

        <!-- Gráfica 1: Actividades por tipo -->
        <div class="flex flex-col justify-between rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm sm:p-7">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-widest text-indigo-600">
                        Actividades por tipo
                    </span>
                    <h3 class="mt-1 text-lg font-extrabold text-slate-900">
                        Distribución de actividades
                    </h3>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-50 text-slate-500 ring-1 ring-slate-200/70">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                    </svg>
                </div>
            </div>

            <div class="relative h-64 w-full">
                <canvas id="activityChart"></canvas>
            </div>
        </div>

        <!-- Gráfica 2: Estado de usuarios -->
        <div class="flex flex-col justify-between rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm sm:p-7">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-widest text-emerald-600">
                        Estado de usuarios
                    </span>
                    <h3 class="mt-1 text-lg font-extrabold text-slate-900">
                        Proporción según estado
                    </h3>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-50 text-slate-500 ring-1 ring-slate-200/70">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z" />
                    </svg>
                </div>
            </div>

            <div class="relative mx-auto h-64 w-full max-w-xs">
                <canvas id="userChart"></canvas>
            </div>
        </div>

    </section>

    <!-- =====================================
         ACCESOS RÁPIDOS
     ====================================== -->
    <section class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm sm:p-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-widest text-amber-600">
                    Accesos rápidos
                </span>
                <h3 class="mt-1 text-xl font-extrabold text-slate-900">
                    Áreas del sistema
                </h3>
            </div>
            <div class="hidden h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-amber-600 ring-1 ring-amber-100 sm:flex">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">

            <!-- Usuarios -->
            <a href="{{ route('admin.users.index') }}"
               class="group rounded-xl border border-slate-200/80 bg-slate-50/50 p-5 transition hover:-translate-y-0.5 hover:border-indigo-200 hover:bg-indigo-50/40 hover:shadow-sm">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600 transition group-hover:bg-indigo-600 group-hover:text-white">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                </div>
                <h4 class="mt-4 font-bold text-slate-900">Usuarios</h4>
                <p class="mt-1 text-xs leading-5 text-slate-500">Gestiona perfiles, roles y estados de cuenta.</p>
            </a>

            <!-- Clases -->
            <a href="{{ route('admin.classes.index') }}"
               class="group rounded-xl border border-slate-200/80 bg-slate-50/50 p-5 transition hover:-translate-y-0.5 hover:border-amber-200 hover:bg-amber-50/40 hover:shadow-sm">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100 text-amber-600 transition group-hover:bg-amber-500 group-hover:text-white">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                </div>
                <h4 class="mt-4 font-bold text-slate-900">Clases</h4>
                <p class="mt-1 text-xs leading-5 text-slate-500">Revisa grupos, profesores y códigos de acceso.</p>
            </a>

            <!-- Actividades -->
            <a href="{{ route('admin.activities.index') }}"
               class="group rounded-xl border border-slate-200/80 bg-slate-50/50 p-5 transition hover:-translate-y-0.5 hover:border-purple-200 hover:bg-purple-50/40 hover:shadow-sm">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100 text-purple-600 transition group-hover:bg-purple-600 group-hover:text-white">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                    </svg>
                </div>
                <h4 class="mt-4 font-bold text-slate-900">Actividades</h4>
                <p class="mt-1 text-xs leading-5 text-slate-500">Explora las actividades y retos publicados.</p>
            </a>

            <!-- Resultados -->
            <a href="{{ route('admin.results.index') }}"
               class="group rounded-xl border border-slate-200/80 bg-slate-50/50 p-5 transition hover:-translate-y-0.5 hover:border-rose-200 hover:bg-rose-50/40 hover:shadow-sm">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-rose-100 text-rose-600 transition group-hover:bg-rose-600 group-hover:text-white">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0" />
                    </svg>
                </div>
                <h4 class="mt-4 font-bold text-slate-900">Resultados</h4>
                <p class="mt-1 text-xs leading-5 text-slate-500">Consulta participaciones, métricas y puntajes.</p>
            </a>

        </div>
    </section>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    (function () {
        const activityCanvas = document.getElementById('activityChart');
        const userCanvas = document.getElementById('userChart');

        if (typeof window.Chart === 'undefined') {
            return;
        }

        if (activityCanvas) {
            const activityLabels = @json($activityTypeLabels);
            const activityData   = @json($activityTypeTotals);
            const activityColors = ['#6366f1', '#06b6d4', '#f59e0b', '#8b5cf6', '#ec4899'];

            new window.Chart(activityCanvas, {
                type: 'bar',
                data: {
                    labels: activityLabels,
                    datasets: [{
                        label: 'Actividades',
                        data: activityData,
                        backgroundColor: activityColors,
                        borderRadius: 8,
                        maxBarThickness: 44
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            padding: 12,
                            cornerRadius: 8
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: '#64748b', font: { weight: '600', size: 12 } }
                        },
                        y: {
                            beginAtZero: true,
                            border: { display: false },
                            grid: { color: '#f1f5f9' },
                            ticks: { precision: 0, color: '#64748b', font: { size: 12 } }
                        }
                    }
                }
            });
        }

        if (userCanvas) {
            const userLabels = @json($userStatusLabels);
            const userData   = @json($userStatusTotals);
            const userColors = ['#10b981', '#64748b', '#ef4444'];

            new window.Chart(userCanvas, {
                type: 'doughnut',
                data: {
                    labels: userLabels,
                    datasets: [{
                        data: userData,
                        backgroundColor: userColors,
                        borderWidth: 3,
                        borderColor: '#ffffff',
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle',
                                padding: 18,
                                color: '#475569',
                                font: { weight: '600', size: 12 }
                            }
                        },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            padding: 12,
                            cornerRadius: 8
                        }
                    }
                }
            });
        }
    })();
</script>
@endpush