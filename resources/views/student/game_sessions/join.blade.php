@extends('student.layouts.session')

@section('title')Unirse a una sala@endsection

@section('head')
<style>
    .session-join { max-width: 940px; margin-inline: auto; }
    .session-join .page-hero { margin-bottom: 1.5rem; }
    .join-card { overflow: hidden; border: 1px solid #d7e8e4; border-radius: 22px; box-shadow: 0 18px 48px rgb(23 69 60 / .1); }
    .join-card-body { padding: clamp(1.25rem, 4vw, 2.25rem); }
    .join-mark { display: grid; width: 54px; height: 54px; place-items: center; margin-bottom: 1rem; border-radius: 16px; background: #e7f6ed; color: #087f75; font-size: 1.55rem; }
    .join-code { min-height: 64px; border: 2px solid #cfe3dc; border-radius: 14px; font-size: clamp(1.35rem, 5vw, 1.8rem); letter-spacing: .2em; }
    .join-code:focus { border-color: #087f75; box-shadow: 0 0 0 .22rem rgb(8 127 117 / .15); }
    .join-back { min-height: 44px; }
    @media (max-width: 575px) { .session-join .page-hero { text-align: left !important; } .join-code { letter-spacing: .14em !important; } }
</style>
@endsection

@section('content')
<div class="page-shell session-join">

        {{-- Botón Volver al inicio (parte superior) --}}
        <div class="mb-4">
            <a href="{{ route('student.dashboard') }}"
                class="inline-flex items-center gap-2 text-sm font-bold text-cyan-700 transition hover:text-cyan-900">
                <span aria-hidden="true">←</span> Volver al inicio
            </a>
        </div>

        <div class="page-shell session-join">

        <div class="page-hero">
            <h1 class="font-bold mb-1">Unirse a una sala</h1>
            <p class="mb-0 opacity-75">Ingresa el código que te compartió tu profesor y únete a la partida.</p>
        </div>

        @if (session('success'))
            <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4 text-red-800">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4 text-red-800">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mx-auto grid w-full max-w-2xl justify-items-center">
            <div class="w-full">
                <div class="surface-card join-card">
                    <div class="join-card-body p-6">
                        <div class="join-mark" aria-hidden="true">🎮</div>
                        <h2 class="text-xl font-bold mb-2">¿Listo para jugar?</h2>
                        <p class="text-slate-500 mb-4">Escribe el código que aparece en la pantalla de tu profesor.</p>

                        <form method="POST" action="{{ route('student.game-sessions.join') }}">
                            @csrf

                            <label class="mb-2 block text-sm font-semibold" for="code">Código de la sala</label>
                            <input
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-100 text-center font-bold join-code mb-4"
                                style="text-transform: uppercase;"
                                name="code"
                                id="code"
                                placeholder="KLS482"
                                maxlength="8"
                                autocomplete="off"
                                autocapitalize="characters"
                                aria-describedby="code-help"
                                required>
                            <div id="code-help" class="mt-2 block text-sm text-slate-500 mb-4">El código distingue la sala a la que vas a entrar.</div>

                            <button type="submit" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-cyan-600 px-5 py-3 font-bold text-white shadow-sm transition hover:bg-cyan-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-cyan-700 disabled:cursor-not-allowed disabled:opacity-50">Unirme a la sala</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('student.dashboard') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 font-semibold text-slate-700 transition hover:bg-slate-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-cyan-700 join-back">Volver al inicio</a>
        </div>

    </div>
@endsection
