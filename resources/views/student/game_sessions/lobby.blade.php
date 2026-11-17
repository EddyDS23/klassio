@extends('student.layouts.app')

@section('title', 'Sala '.$session->code.' — Estudiante')

@section('page-title', 'Sala '.$session->code)

@section('page-actions')
    <a href="{{ route('student.activities.show', $session->activity_id) }}"
       class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-pink-600 focus-visible:ring-offset-2">
        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
        </svg>
        Volver a la actividad
    </a>
@endsection

@section('content')

    @php
        $typeIcons = [
            'kahoot' => 'm3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z',
            'crossword' => 'M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.75m-.75 3h.75m0 0H21',
            'word_search' => 'm21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z',
            'matching' => 'M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244',
            'roulette' => 'M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99',
        ];

        $typeIcon = $typeIcons[$session->activity->type] ?? $typeIcons['roulette'];
    @endphp

    <div class="mx-auto max-w-5xl">

        {{-- Encabezado de la sala --}}
        <section class="mb-6 rounded-2xl bg-pink-700 p-5 text-white shadow-sm sm:p-6">

            <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">

                <div class="min-w-0">
                    <p class="text-xs font-bold uppercase tracking-widest text-pink-200">
                        Sala multijugador
                    </p>

                    <h2 class="mt-1 text-2xl font-bold tracking-tight sm:text-3xl">
                        {{ $session->status === 'waiting'
                            ? 'Tu sala está lista'
                            : ($session->status === 'finished' ? 'Partida finalizada' : 'Panel de la partida') }}
                    </h2>

                    <p class="mt-2 flex items-center gap-1.5 text-sm font-bold text-pink-100">
                        <svg class="h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $typeIcon }}" />
                        </svg>
                        {{ $session->activity->title }}
                    </p>
                </div>

                <div class="hidden h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-pink-600 ring-1 ring-pink-500 md:flex">
                    <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $typeIcon }}" />
                    </svg>
                </div>

            </div>

        </section>

        {{-- Código y estado de la sala --}}
        <section class="mb-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">

            <div class="flex flex-col items-center gap-4 text-center sm:flex-row sm:items-end sm:justify-between sm:gap-3 sm:text-left">

                <div class="min-w-0">
                    <p class="text-sm text-slate-500">
                        Código para identificar tu sala
                    </p>

                    <p class="mt-2 text-4xl font-bold tracking-[0.25em] text-pink-900 sm:text-5xl" id="room-code">
                        {{ $session->code }}
                    </p>
                </div>

                <div class="flex flex-wrap items-center justify-center gap-2 sm:justify-end">
                    <span id="room-status"
                          class="rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wide {{ $session->status === 'playing' ? 'bg-amber-100 text-amber-800' : ($session->status === 'waiting' ? 'bg-pink-100 text-pink-700' : 'bg-slate-100 text-slate-700') }}">
                        {{ $session->status }}
                    </span>

                    <span id="room-count"
                          class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700"
                          role="status" aria-atomic="true">
                        {{ $players->count() }} / {{ $session->max_players }} jugadores
                    </span>
                </div>

            </div>

        </section>

        {{-- Jugadores en la sala --}}
        <section class="mb-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">

            <h3 class="text-lg font-bold text-slate-900">
                Quiénes están en la sala
            </h3>

            <p class="mt-0.5 text-sm text-slate-600">
                La lista se actualiza automáticamente cuando alguien entra o sale.
            </p>

            <ul id="players" class="mt-4 divide-y divide-slate-100">
                @foreach ($players as $p)
                    <li class="player-row flex items-center gap-3 py-2.5" data-participation-id="{{ $p->id }}">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-pink-50 text-sm font-bold text-pink-700 ring-1 ring-pink-100">
                            {{ mb_substr($p->team?->name ?? $p->student?->name, 0, 1) }}
                        </span>

                        <span class="min-w-0 flex-1 truncate text-sm font-bold text-slate-800">
                            {{ ($myParticipation?->id === $p->id ? '(tú) ' : '') . ($p->team?->name ?? $p->student?->name) }}
                        </span>

                        <span class="shrink-0 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700">
                            En la sala
                        </span>
                    </li>
                @endforeach
            </ul>

            <div id="empty-players" class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600 {{ $players->isEmpty() ? '' : 'hidden' }}" role="status">
                Aún no se une nadie. Cuando entren, aparecerán aquí.
            </div>

        </section>

        {{-- Acciones de la sala --}}
        <section class="mb-8 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">

            <div class="flex flex-col gap-3">

                @if ($session->status === 'waiting')
                    <div class="rounded-xl border border-pink-200 bg-pink-50 p-4 text-sm text-pink-900" role="status">
                        <strong class="font-bold">Estás dentro.</strong>
                        La partida comenzará cuando el profesor la inicie. Puedes quedarte en esta pantalla; se actualizará automáticamente.
                    </div>
                @endif

                @if ($session->status === 'playing')
                    <a href="{{ route('student.game-sessions.play', $session->id) }}"
                       class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-pink-700 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-pink-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-pink-700 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.28 6.347a1.125 1.125 0 0 1 0 1.972l-11.28 6.347a1.125 1.125 0 0 1-1.667-.985V5.653Z" />
                        </svg>
                        Entrar a la partida
                    </a>
                @endif

                @if ($session->status === 'finished')
                    <a href="{{ route('student.game-sessions.result', $session->id) }}"
                       class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-pink-700 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-pink-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-pink-700 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                        Ver resultados
                    </a>
                @endif

                @if (!in_array($session->status, ['finished', 'cancelled'], true))
                    <form method="POST" action="{{ route('student.game-sessions.leave', $session->id) }}">
                        @csrf

                        <button type="submit"
                                class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm transition hover:border-red-200 hover:bg-red-50 hover:text-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2">
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                            </svg>
                            Salir de la sala
                        </button>

                    </form>
                @endif

            </div>

        </section>

    </div>

@endsection

@push('scripts')
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
                li.className = 'player-row flex items-center gap-3 py-2.5';

                const avatar = document.createElement('span');
                avatar.className = 'flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-pink-50 text-sm font-bold text-pink-700 ring-1 ring-pink-100';
                avatar.textContent = (p.name || '?').charAt(0).toUpperCase();
                li.appendChild(avatar);

                const name = document.createElement('span');
                name.className = 'min-w-0 flex-1 truncate text-sm font-bold text-slate-800';
                name.textContent = (p.participation_id === myParticipationId ? '(tú) ' : '') + p.name;
                li.appendChild(name);

                const presence = document.createElement('span');
                presence.className = 'shrink-0 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700';
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
