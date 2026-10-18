@extends('teacher.layouts.session')

@section('title', 'Crear sala multijugador')

@section('head')
<style>
    .session-create { max-width: 1040px; margin-inline: auto; }
    .session-create .page-hero { margin-bottom: 1.5rem; }
    .create-card { overflow: hidden; border: 1px solid #d7e8e4; border-radius: 22px; box-shadow: 0 18px 48px rgb(23 69 60 / .1); }
    .create-card-body { padding: clamp(1.25rem, 4vw, 2.25rem); }
    .create-step { display: inline-flex; align-items: center; gap: .55rem; margin-bottom: 1.2rem; color: #087f75; font-size: .8rem; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }
    .create-step span { display: grid; width: 28px; height: 28px; place-items: center; border-radius: 50%; background: #e5f4e7; }
    .create-note { padding: .9rem 1rem; border: 1px solid #c9e8d9; border-radius: 13px; background: #effbf5; color: #365f54; }
    .create-select { min-height: 48px; border-color: #d2e2dd; border-radius: 11px; }
    .create-select:focus { border-color: #087f75; box-shadow: 0 0 0 .2rem rgb(8 127 117 / .14); }
    .create-submit { min-height: 48px; }
    @media (max-width: 575px) { .session-create .page-hero { text-align: left !important; } }
</style>
@endsection

@section('content')
    <div class="page-shell session-create">

        <div class="page-hero">
            <h1 class="font-bold mb-1">Crear sala multijugador</h1>
            <p class="mb-0 opacity-75">Elige una actividad publicada y comparte el código con tus estudiantes.</p>
        </div>

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
                <div class="surface-card create-card">
                    <div class="create-card-body p-6">
                        <div class="create-step"><span>1</span> Configura la sesión</div>
                        <h2 class="text-xl font-bold mb-2">Elige qué jugarán</h2>
                        <p class="text-slate-500 mb-4">La sala usará el juego y las reglas de la actividad que selecciones.</p>

                        @if ($activities->isEmpty())
                            <div class="mb-3 rounded-xl border border-amber-200 bg-amber-50 p-4 text-amber-900">
                                Aún no tienes actividades publicadas. Publica una actividad para crear una sala.
                            </div>

                            <a href="{{ route('teacher.dashboard') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 font-semibold text-slate-700 transition hover:bg-slate-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-cyan-700">
                                Volver al panel
                            </a>
                        @else
                            <form method="POST" action="{{ route('teacher.game-sessions.store') }}">
                                @csrf

                                <label class="mb-2 block text-sm font-semibold" for="activity_id">Actividad publicada</label>
                                <select name="activity_id" id="activity_id" class="create-select w-full rounded-xl border border-slate-300 bg-white px-3 py-3 text-slate-900 shadow-sm focus:border-violet-600 focus:outline-none focus:ring-2 focus:ring-violet-100 mb-3" required>
                                    @foreach ($activities as $activity)
                                        <option value="{{ $activity->id }}"
                                            @selected($selectedActivityId === $activity->id)>
                                            {{ $activity->title }}
                                            ({{ $activity->schoolClass?->name }}) · {{ match ($activity->type) {
                                                'crossword' => 'Crucigrama',
                                                'kahoot' => 'Kahoot',
                                                'word_search' => 'Sopa de letras',
                                                'matching' => 'Relacionar',
                                                default => 'Ruleta',
                                            } }}
                                        </option>
                                    @endforeach
                                </select>

                                <label class="mb-2 block text-sm font-semibold" for="max_players">Máximo de jugadores</label>
                                <select name="max_players" id="max_players" class="create-select w-full rounded-xl border border-slate-300 bg-white px-3 py-3 text-slate-900 shadow-sm focus:border-violet-600 focus:outline-none focus:ring-2 focus:ring-violet-100 mb-4">
                                    @foreach ([2, 3, 4, 5, 6, 8] as $max)
                                        <option value="{{ $max }}" @selected($max === 4)>{{ $max }}</option>
                                    @endforeach
                                </select>

                                <div class="create-note mb-4" role="note">
                                    <strong>Antes de comenzar:</strong> comparte el código de sala con tus estudiantes. Podrás ver quién se une y comenzar desde el panel de la sesión.
                                </div>

                                <button type="submit" class="create-submit inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-violet-600 px-5 py-3 font-bold text-white shadow-sm transition hover:bg-violet-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-violet-700 disabled:cursor-not-allowed disabled:opacity-50">
                                    Crear sala y ver el código
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('teacher.dashboard') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 font-semibold text-slate-700 transition hover:bg-slate-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-cyan-700">Volver al panel</a>
        </div>

    </div>
@endsection
