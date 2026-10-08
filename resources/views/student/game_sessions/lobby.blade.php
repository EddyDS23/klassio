<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sala {{ $session->code }}</title>
    @include('partials.assets', ['theme' => 'student'])
    @if ((bool) config('broadcasting.connections.reverb.enabled', false))
        <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
        <script>
            window.Echo = new Echo({
                broadcaster: 'reverb',
                key: @json(config('broadcasting.connections.reverb.key')),
                wsHost: @json(config('broadcasting.connections.reverb.options.host', 'localhost')),
                wsPort: @json(config('broadcasting.connections.reverb.options.port', 8080)),
                forceTLS: @json(config('broadcasting.connections.reverb.options.scheme', 'http') === 'https'),
                enabledTransports: ['ws', 'wss']
            });
        </script>
    @endif
</head>

<body>

    <div class="page-shell">

        <div class="page-hero text-center">
            <h1 class="fw-bold mb-1">Sala de juego</h1>
            <p class="mb-0 opacity-75">{{ $session->activity->title }}</p>
        </div>

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card surface-card mb-4">
            <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <div class="text-muted small">Código de la sala</div>
                    <div class="fw-bold" style="font-size: 2rem; letter-spacing: .35em;" id="room-code">
                        {{ $session->code }}
                    </div>
                </div>
                <div>
                    <span class="badge text-bg-secondary text-uppercase" id="room-status">
                        {{ $session->status }}
                    </span>
                    <div class="text-muted small mt-1" id="room-count">
                        {{ $players->count() }} / {{ $session->max_players }} jugadores
                    </div>
                </div>
            </div>
        </div>

        <div class="card surface-card mb-4">
            <div class="card-body p-4">
                <h2 class="h5 fw-bold mb-3">Jugadores</h2>
                <ul class="list-group list-group-flush" id="players">
                    @foreach ($players as $p)
                        <li
                            class="list-group-item d-flex align-items-center justify-content-between"
                            data-participation-id="{{ $p->id }}"
                        >
                            <span>
                                @if ($myParticipation?->id === $p->id)
                                    <span class="text-muted">(tú)</span>
                                @endif
                                {{ $p->student?->name }}
                            </span>
                            <span class="text-success small">Conectado</span>
                        </li>
                    @endforeach
                </ul>
                <div id="empty-players" class="text-muted {{ $players->isEmpty() ? '' : 'd-none' }}">
                    Aún no hay jugadores en la sala.
                </div>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2">
            @if ($isHost && $session->status === 'waiting')
                <form method="POST" action="{{ route('student.game-sessions.start', $session->id) }}" class="w-100 mb-2">
                    @csrf
                    <button
                        type="submit"
                        class="btn btn-klassio w-100"
                        id="start-btn"
                        @disabled(! $canStart)
                    >
                        Iniciar partida
                    </button>
                </form>
            @endif

            @if ($session->status === 'playing')
                <a href="{{ route('student.game-sessions.play', $session->id) }}" class="btn btn-klassio w-100">
                    Entrar a la partida
                </a>
            @endif

            @if ($session->status === 'finished')
                <a href="{{ route('student.game-sessions.result', $session->id) }}" class="btn btn-klassio w-100">
                    Ver resultados
                </a>
            @endif

            @if (! in_array($session->status, ['finished', 'cancelled'], true))
                <form method="POST" action="{{ route('student.game-sessions.leave', $session->id) }}" class="w-100">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary w-100">Salir de la sala</button>
                </form>
            @endif
        </div>

        <div class="mt-4">
            <a href="{{ route('student.activities.show', $session->activity_id) }}" class="btn btn-outline-secondary">
                Volver a la actividad
            </a>
        </div>

    </div>

    <script>
        const sessionId = @json($session->id);
        const stateUrl = @json(route('student.game-sessions.state', $session->id));
        const playUrl = @json(route('student.game-sessions.play', $session->id));
        const resultUrl = @json(route('student.game-sessions.result', $session->id));
        const myParticipationId = @json($myParticipation?->id);

        const statusLabel = {
            waiting: 'Esperando jugadores',
            starting: 'Iniciando',
            playing: 'En partida',
            finished: 'Finalizada',
            cancelled: 'Cancelada'
        };

        function renderState(state) {
            const statusEl = document.getElementById('room-status');
            statusEl.textContent = statusLabel[state.status] || state.status;

            const countEl = document.getElementById('room-count');
            countEl.textContent = state.players.length + ' / ' + state.max_players + ' jugadores';

            const list = document.getElementById('players');
            list.innerHTML = '';
            state.players.forEach((p) => {
                if (p.status === 'abandoned' || p.status === 'expired') return;

                const li = document.createElement('li');
                li.className = 'list-group-item d-flex align-items-center justify-content-between';

                const name = document.createElement('span');
                name.textContent = (p.participation_id === myParticipationId ? '(tú) ' : '') + p.name;
                li.appendChild(name);

                const badge = document.createElement('span');
                badge.className = 'text-success small';
                badge.textContent = 'Conectado';
                li.appendChild(badge);

                list.appendChild(li);
            });

            document.getElementById('empty-players').classList.toggle('d-none', state.players.length > 0);
        }

        async function refresh() {
            try {
                const res = await fetch(stateUrl, {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin'
                });
                if (!res.ok) return;
                const state = await res.json();

                renderState(state);

                if (state.status === 'playing') {
                    window.location.href = playUrl;
                } else if (state.status === 'finished') {
                    window.location.href = resultUrl;
                }
            } catch (e) {
                // sin red por ahora
            }
        }

        setInterval(refresh, 2000);

        window.addEventListener('DOMContentLoaded', () => {
            const reverbEnabled = @json((bool) config('broadcasting.connections.reverb.enabled', false));
            if (window.Echo && reverbEnabled) {
                window.Echo.private('game-session.' + sessionId)
                    .listen('.GameStarted', (e) => { window.location.href = playUrl; })
                    .listen('.GameFinished', (e) => { window.location.href = resultUrl; })
                    .listen('.PlayerJoined', (e) => refresh())
                    .listen('.PlayerLeft', (e) => refresh());
            }
        });
    </script>

</body>

</html>