@extends('admin.layouts.app')

@section('title', 'Dashboard | Klassio')

@section('page-title')
    Resumen general
@endsection

@section('content')

    <!-- Bienvenida -->
    <section class="page-hero mb-8">
        <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">

            <div>
                <span class="mb-3 inline-flex rounded-full bg-white/20 px-3 py-1 text-xs font-black uppercase tracking-wider text-white">
                    Panel de administración
                </span>

                <h2 class="text-3xl font-black tracking-tight text-white sm:text-4xl">
                    ¡Hola, {{ auth()->user()->name }}! 👋
                </h2>

                <p class="mt-3 max-w-2xl text-sm leading-6 text-white/80">
                    Controla los usuarios, las clases, las actividades y los resultados
                    de toda la plataforma desde un solo lugar.
                </p>
            </div>

            <div class="hidden h-28 w-28 items-center justify-center rounded-3xl bg-white/15 text-7xl md:flex">
                🛡️
            </div>

        </div>
    </section>

    <!-- Métricas -->
    @php
        $cards = [
            ['label' => 'Usuarios', 'value' => $stats['users'], 'icon' => '👥', 'bg' => 'bg-indigo-100'],
            ['label' => 'Estudiantes', 'value' => $stats['students'], 'icon' => '🎓', 'bg' => 'bg-cyan-100'],
            ['label' => 'Maestros', 'value' => $stats['teachers'], 'icon' => '👨‍🏫', 'bg' => 'bg-emerald-100'],
            ['label' => 'Clases', 'value' => $stats['classes'], 'icon' => '📚', 'bg' => 'bg-amber-100'],
            ['label' => 'Actividades', 'value' => $stats['activities'], 'icon' => '🎯', 'bg' => 'bg-purple-100'],
            ['label' => 'Participaciones', 'value' => $stats['participations'], 'icon' => '🏆', 'bg' => 'bg-rose-100'],
            ['label' => 'Usuarios activos', 'value' => $stats['activeUsers'], 'icon' => '✅', 'bg' => 'bg-teal-100'],
            ['label' => 'Suspendidos', 'value' => $stats['suspendedUsers'], 'icon' => '🚫', 'bg' => 'bg-red-100'],
        ];
    @endphp

    <section class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">

        @foreach ($cards as $card)
            <a href="{{ match ($card['label']) {
                'Estudiantes' => route('admin.users.index', ['role' => 'student']),
                'Maestros' => route('admin.users.index', ['role' => 'teacher']),
                'Clases' => route('admin.classes.index'),
                'Actividades' => route('admin.activities.index'),
                'Participaciones' => route('admin.results.index'),
                default => route('admin.users.index'),
            } }}"
               class="group rounded-3xl border border-slate-200 bg-white p-5 shadow-lg shadow-slate-200/60 transition duration-300 hover:-translate-y-1 hover:shadow-xl">

                <div class="mb-4 flex items-start justify-between">

                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl text-2xl {{ $card['bg'] }} transition group-hover:scale-110">
                        {{ $card['icon'] }}
                    </div>

                    <span class="text-3xl font-black tabular-nums text-slate-900">
                        {{ number_format($card['value']) }}
                    </span>

                </div>

                <p class="text-sm font-black text-slate-500">
                    {{ $card['label'] }}
                </p>

            </a>
        @endforeach

    </section>

    <!-- Gráficas -->
    <section class="mb-8 grid gap-6 lg:grid-cols-2">

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-lg shadow-slate-200/60 sm:p-8">
            <p class="text-xs font-black uppercase tracking-widest text-slate-500">
                Actividades por tipo
            </p>
            <h3 class="mt-1 text-xl font-black text-slate-900">
                Distribución de actividades
            </h3>

            <div class="mt-6">
                <canvas id="activityChart" height="120"></canvas>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-lg shadow-slate-200/60 sm:p-8">
            <p class="text-xs font-black uppercase tracking-widest text-slate-500">
                Estado de usuarios
            </p>
            <h3 class="mt-1 text-xl font-black text-slate-900">
                Proporción según estado
            </h3>

            <div class="mt-6">
                <canvas id="userChart" height="120"></canvas>
            </div>
        </div>

    </section>

    <!-- Accesos rápidos -->
    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-lg shadow-slate-200/60 sm:p-8">

        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-black uppercase tracking-widest text-amber-600">
                    Accesos rápidos
                </p>
                <h3 class="mt-1 text-2xl font-black text-slate-900">
                    Áreas del sistema
                </h3>
            </div>
            <div class="hidden rounded-2xl bg-amber-50 px-4 py-3 text-2xl sm:block">
                ⚙️
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">

            <a href="{{ route('admin.users.index') }}"
               class="rounded-2xl border border-indigo-100 bg-indigo-50 p-4 transition hover:-translate-y-0.5 hover:shadow-lg">
                <div class="text-3xl">👥</div>
                <h4 class="mt-3 font-black text-slate-900">Usuarios</h4>
                <p class="mt-1 text-xs leading-5 text-slate-600">Gestiona perfiles, roles y estados.</p>
            </a>

            <a href="{{ route('admin.classes.index') }}"
               class="rounded-2xl border border-amber-100 bg-amber-50 p-4 transition hover:-translate-y-0.5 hover:shadow-lg">
                <div class="text-3xl">📚</div>
                <h4 class="mt-3 font-black text-slate-900">Clases</h4>
                <p class="mt-1 text-xs leading-5 text-slate-600">Revisa grupos, profesores y códigos.</p>
            </a>

            <a href="{{ route('admin.activities.index') }}"
               class="rounded-2xl border border-purple-100 bg-purple-50 p-4 transition hover:-translate-y-0.5 hover:shadow-lg">
                <div class="text-3xl">🎯</div>
                <h4 class="mt-3 font-black text-slate-900">Actividades</h4>
                <p class="mt-1 text-xs leading-5 text-slate-600">Explora las actividades publicadas.</p>
            </a>

            <a href="{{ route('admin.results.index') }}"
               class="rounded-2xl border border-rose-100 bg-rose-50 p-4 transition hover:-translate-y-0.5 hover:shadow-lg">
                <div class="text-3xl">🏆</div>
                <h4 class="mt-3 font-black text-slate-900">Resultados</h4>
                <p class="mt-1 text-xs leading-5 text-slate-600">Consulta participaciones y puntajes.</p>
            </a>

        </div>

    </section>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    (function () {
        var canvasEls = document.querySelectorAll('canvas');

        if (canvasEls.length === 0 || typeof window.Chart === 'undefined') {
            return;
        }

        var activityLabels = @json($activityTypeLabels);
        var activityData = @json($activityTypeTotals);
        var activityColors = ['#f59e0b', '#7c3aed', '#0891b2', '#dc2626'];

        new window.Chart(document.getElementById('activityChart'), {
            type: 'bar',
            data: {
                labels: activityLabels,
                datasets: [{
                    label: 'Actividades',
                    data: activityData,
                    backgroundColor: activityColors,
                    borderRadius: 10
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                }
            }
        });

        var userLabels = @json($userStatusLabels);
        var userData = @json($userStatusTotals);
        var userColors = ['#10b981', '#64748b', '#ef4444'];

        new window.Chart(document.getElementById('userChart'), {
            type: 'doughnut',
            data: {
                labels: userLabels,
                datasets: [{
                    data: userData,
                    backgroundColor: userColors,
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    })();
</script>
@endpush