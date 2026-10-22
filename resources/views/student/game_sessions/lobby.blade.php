@extends('student.layouts.session')

@section('title')Sala {{ $session->code }}@endsection

@section('head')
    <style>
        .session-lobby { max-width: 1040px; margin-inline: auto; }
        .session-lobby .page-hero { margin-bottom: 1.4rem; }
        .lobby-overview, .lobby-players { overflow: hidden; border: 1px solid #d7e8e4; border-radius: 20px; box-shadow: 0 14px 38px rgb(23 69 60 / .08); }
        .lobby-code { display: inline-block; padding: .2rem .55rem; border: 1px dashed #90c4b0; border-radius: 10px; background: #effbf5; color: #075e58; font-size: clamp(1.5rem, 5vw, 2rem); letter-spacing: .24em !important; }
        .lobby-count { display: inline-flex; align-items: center; gap: .45rem; padding: .5rem .75rem; border-radius: 999px; background: #eff6f2; color: #45635d; font-weight: 750; }
        .lobby-players .player-row { min-height: 54px; padding-block: .8rem; }
        .lobby-wait { border: 1px solid #c9e8d9; border-radius: 14px; background: #effbf5; color: #20584d; }
        .lobby-wait strong { color: #075e58; }
        .lobby-status { background: #e6f5eb; color: #287243; }
        @media (max-width: 575px) { .session-lobby .page-hero { text-align: left !important; } .lobby-overview-body { align-items: flex-start !important; } }
    </style>
    @if ((bool) config('broadcasting.connections.reverb.enabled', false))
        <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
        <script>
            window.Echo = new Echo({
                broadcaster: 'reverb',
                key: @json(config('broadcasting.connections.reverb.key')),
                wsHost: @json(config('broadcasting.connections.reverb.client_host', 'localhost')),
                wsPort: @json(config('broadcasting.connections.reverb.options.port', 8080)),
                forceTLS: @json(config('broadcasting.connections.reverb.options.scheme', 'http') === 'https'),
                enabledTransports: ['ws', 'wss']
            });
        </script>
    @endif
@endsection

@section('content')
<div class="page-shell session-lobby">

        {{-- Botón Volver a la actividad (parte superior) --}}
        <div class="mb-4">
            <a href="{{ route('student.activities.show', $session->activity_id) }}"
                class="inline-flex items-center gap-2 text-sm font-bold text-cyan-700 transition hover:text-cyan-900">
                <span aria-hidden="true">←</span> Volver a la actividad
            </a>
        </div>

        <div class="page-shell session-lobby">

        <div class="page-hero">
            <p class="text-sm uppercase font-bold mb-2">Tu próxima partida</p>
            <h1 class="font-bold mb-1">Sala de juego</h1>
            <p class="mb-0 opacity-75">{{ $session->activity->title }}</p>
        </div>

        @if (session('error'))
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4 text-red-800">{{ session('error') }}</div>
        @endif

        <div class="surface-card lobby-overview mb-4">
            <div class="lobby-overview-body flex flex-wrap items-center justify-between gap-3 p-6">
                <div>
                    <div class="text-slate-500 text-sm mb-2">Código para identificar tu sala</div>
                    <div class="font-bold lobby-code" id="room-code">
                        {{ $session->code }}
                    </div>
                </div>
                <div>
                    <span class="lobby-status rounded-full px-3 py-1 text-xs font-bold uppercase" id="room-status">
                        {{ $session->status }}
                    </span>
                    <div class="lobby-count mt-2" id="room-count">
                        {{ $players->count() }} / {{ $session->max_players }} jugadores
                    </div>
                </div>
            </div>
        </div>

        <div class="surface-card lobby-players mb-4">
            <div class="p-6">
                <h2 class="text-lg font-bold mb-3">Quiénes están en la sala</h2>
                <ul class="divide-y divide-slate-100" id="players">
                    @foreach ($players as $p)
                        <li class="player-row flex items-center justify-between border-b border-slate-100 last:border-0"
                            data-participation-id="{{ $p->id }}">
                            <span>
                                @if ($myParticipation?->id === $p->id)
                                    <span class="text-slate-500">(tú)</span>
                                @endif
                                {{ $p->team?->name ?? $p->student?->name }}
                            </span>
                            <span class="text-emerald-700 text-sm">En la sala</span>
                        </li>
                    @endforeach
                </ul>
                <div id="empty-players" class="rounded-xl bg-slate-50 p-3 text-slate-500 {{ $players->isEmpty() ? '' : 'hidden' }}" role="status">
                    Aún no se une nadie. Cuando entren, aparecerán aquí.
                </div>
            </div>
        </div>

        <div class="flex flex-wrap gap-2">
            @if ($session->status === 'waiting')
                <div class="lobby-wait w-full mb-2 p-4" role="status">
                    <strong>Estás dentro.</strong> La partida comenzará cuando el profesor la inicie. Puedes quedarte en esta pantalla; se actualizará automáticamente.
                </div>
            @endif

            @if ($session->status === 'playing')
                <a href="{{ route('student.game-sessions.play', $session->id) }}" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-cyan-600 px-5 py-3 font-bold text-white shadow-sm transition hover:bg-cyan-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-cyan-700 disabled:cursor-not-allowed disabled:opacity-50">
                    Entrar a la partida
                </a>
            @endif

            @if ($session->status === 'finished')
                <a href="{{ route('student.game-sessions.result', $session->id) }}" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-cyan-600 px-5 py-3 font-bold text-white shadow-sm transition hover:bg-cyan-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-cyan-700 disabled:cursor-not-allowed disabled:opacity-50">
                    Ver resultados
                </a>
            @endif

            @if (!in_array($session->status, ['finished', 'cancelled'], true))
                <form method="POST" action="{{ route('student.game-sessions.leave', $session->id) }}" class="w-full">
                    @csrf
                    <button type="submit" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 font-semibold text-slate-700 transition hover:bg-slate-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-cyan-700">Salir de la sala</button>
                </form>
            @endif
        </div>

        <div class="mt-4">
            <a href="{{ route('student.activities.show', $session->activity_id) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 font-semibold text-slate-700 transition hover:bg-slate-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-cyan-700">
                Volver a la actividad
            </a>
        </div>

    </div>
@endsection

@push('scripts')
<script>
        const sessionId = @json($session->id);
        const stateUrl = @json(route('student.game-sessions.state', $session->id));
        const playUrl = @json(route('student.game-sessions.play', $session->id));
        const resultUrl = @json(route('student.game-sessions.result', $session->id));
        const myParticipationId = @json($myParticipation?->id);
        let navigationStarted = false;
        let refreshInProgress = false;

        function navigateOnce(url) {
            if (navigationStarted) return;
            navigationStarted = true;
            window.location.href = url;
        }

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
            list.replaceChildren();
            state.players.forEach((p) => {
                if (p.status === 'abandoned' || p.status === 'expired') return;

                const li = document.createElement('li');
                li.className = 'player-row flex items-center justify-between';

                const name = document.createElement('span');
                name.textContent = (p.participation_id === myParticipationId ? '(tú) ' : '') + p.name;
                li.appendChild(name);

                const presence = document.createElement('span');
                presence.className = 'text-emerald-700 text-sm';
                presence.textContent = 'En la sala';
                li.appendChild(presence);

                list.appendChild(li);
            });

            document.getElementById('empty-players').classList.toggle('hidden', state.players.length > 0);
        }

        function setupStateSync(refreshFn) {
            let fallbackPolling = null;

            const startFallback = () => {
                if (fallbackPolling !== null) return;

                refreshFn();
                fallbackPolling = window.setInterval(refreshFn, 2000);
            };

            const stopFallback = () => {
                if (fallbackPolling === null) return;

                window.clearInterval(fallbackPolling);
                fallbackPolling = null;
            };

            const connection = window.Echo?.connector?.pusher?.connection;

            if (!connection) {
                startFallback();
                return;
            }

            connection.bind('state_change', ({
                current
            }) => {
                if (current === 'connected') {
                    stopFallback();
                    refreshFn();
                } else {
                    startFallback();
                }
            });

            if (connection.state === 'connected') {
                refreshFn();
            } else {
                startFallback();
            }
        }

        async function refresh() {
            if (refreshInProgress || navigationStarted) return;
            refreshInProgress = true;

            try {
                const res = await fetch(stateUrl, {
                    headers: {
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });
                if (!res.ok) return;
                const state = await res.json();

                renderState(state);

                if (state.status === 'playing') {
                    navigateOnce(playUrl);
                } else if (state.status === 'finished') {
                    navigateOnce(resultUrl);
                }
            } catch (e) {
                // sin red por ahora
            } finally {
                refreshInProgress = false;
            }
        }


        window.addEventListener('DOMContentLoaded', () => {
            const reverbEnabled = @json((bool) config('broadcasting.connections.reverb.enabled', false));
            if (window.Echo && reverbEnabled) {
                window.Echo.private('game-session.' + sessionId)
                    .listen('.GameStarted', (e) => {
                        navigateOnce(playUrl);
                    })
                    .listen('.GameFinished', (e) => {
                        navigateOnce(resultUrl);
                    })
                    .listen('.PlayerJoined', (e) => refresh())
                    .listen('.PlayerLeft', (e) => refresh());
            }
            setupStateSync(refresh);
        });
    </script>
@endpush
