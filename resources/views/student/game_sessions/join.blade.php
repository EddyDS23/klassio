@extends('student.layouts.app')

@section('title', 'Unirse a una sala')

@section('page-title', 'Unirse a una sala')

@section('page-actions')
    <a href="{{ route('student.dashboard') }}"
       class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-pink-600 focus-visible:ring-offset-2">
        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
        </svg>
        Inicio
    </a>
@endsection

@section('content')

    {{-- Los mensajes flash y $errors los renderiza el layout student.layouts.app. --}}

    <div class="mx-auto max-w-xl lg:max-w-4xl">

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm lg:grid lg:grid-cols-5">

            {{-- Panel informativo: solo visible en desktop, el header del formulario ya cubre móvil --}}
            <aside class="hidden flex-col justify-between bg-pink-700 p-8 text-white lg:col-span-2 lg:flex">

                <div>

                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/10 ring-1 ring-white/20">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 6.087c0-.355.186-.676.401-.867.344-.31.698-.729.698-1.22 0-.977-.894-1.5-1.849-1.5-1.32 0-2.14.665-2.758 1.435-.135.166-.35.283-.576.283h-.683a.75.75 0 0 1 0-1.5h.683c.737 0 1.193-.378 1.7-.87.67-.65 1.603-1.348 3.134-1.348 1.695 0 3.349 1.04 3.349 3 0 .914-.475 1.565-1.01 2.033a2.727 2.727 0 0 0-.59.867c-.078.294-.05.63.05.92.05.22.12.43.2.63a.75.75 0 0 1-1.38.565 6.5 6.5 0 0 1-.25-.78 3.18 3.18 0 0 1-.11-.92c0-.44.132-.832.31-1.09Zm-2.5-3.09a.75.75 0 0 1 0 1.5h-.5a.75.75 0 0 1 0-1.5h.5Zm-6 5.25c0 3.724 1.295 6.475 3.06 8.42a.75.75 0 0 1 1.265.75 8.25 8.25 0 0 1-1.32 1.946 8.25 8.25 0 0 0 3.495-.975.75.75 0 0 1 .75 1.299 9.75 9.75 0 0 1-5.25 1.234 9.75 9.75 0 0 1-5.25-1.234.75.75 0 0 1 .75-1.299 8.25 8.25 0 0 0 3.495.975 8.25 8.25 0 0 1-1.32-1.946C6.295 14.222 5 11.471 5 7.747a.75.75 0 0 1 1.5 0Zm9.75 3.66a.75.75 0 0 1 1.5 0c0 3.725-1.295 6.476-3.06 8.421a.75.75 0 0 1-1.266-.75 8.25 8.25 0 0 0 1.32-1.946c1.506-1.945 1.506-4.725 1.506-5.725Z" />
                        </svg>
                    </div>

                    <h2 class="mt-5 text-xl font-bold">Unite a la partida en 3 pasos</h2>

                    <ol class="mt-6 space-y-5">

                        <li class="flex gap-3">

                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/10 ring-1 ring-white/20">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                            </span>

                            <div>
                                <p class="text-sm font-bold">1. Mirá la pantalla</p>
                                <p class="mt-0.5 text-sm text-pink-100">Tu profesor proyecta el código de la sala en clase.</p>
                            </div>

                        </li>

                        <li class="flex gap-3">

                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/10 ring-1 ring-white/20">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                </svg>
                            </span>

                            <div>
                                <p class="text-sm font-bold">2. Escribí el código</p>
                                <p class="mt-0.5 text-sm text-pink-100">Cárgalo acá tal como lo ves en pantalla.</p>
                            </div>

                        </li>

                        <li class="flex gap-3">

                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/10 ring-1 ring-white/20">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 5.25l13.5 6.75-13.5 6.75V5.25Z" />
                                </svg>
                            </span>

                            <div>
                                <p class="text-sm font-bold">3. Entrá a jugar</p>
                                <p class="mt-0.5 text-sm text-pink-100">Pasás a la sala de espera hasta que arranque el juego.</p>
                            </div>

                        </li>

                    </ol>

                </div>

                <p class="mt-8 text-sm text-pink-100">
                    El código lo comparte tu profesor al inicio de la partida.
                </p>

            </aside>

            <div class="p-6 sm:p-8 lg:col-span-3">

            <div class="flex items-center gap-3">

                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-pink-50 text-pink-700 ring-1 ring-pink-100">

                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 6.087c0-.355.186-.676.401-.867.344-.31.698-.729.698-1.22 0-.977-.894-1.5-1.849-1.5-1.32 0-2.14.665-2.758 1.435-.135.166-.35.283-.576.283h-.683a.75.75 0 0 1 0-1.5h.683c.737 0 1.193-.378 1.7-.87.67-.65 1.603-1.348 3.134-1.348 1.695 0 3.349 1.04 3.349 3 0 .914-.475 1.565-1.01 2.033a2.727 2.727 0 0 0-.59.867c-.078.294-.05.63.05.92.05.22.12.43.2.63a.75.75 0 0 1-1.38.565 6.5 6.5 0 0 1-.25-.78 3.18 3.18 0 0 1-.11-.92c0-.44.132-.832.31-1.09Zm-2.5-3.09a.75.75 0 0 1 0 1.5h-.5a.75.75 0 0 1 0-1.5h.5Zm-6 5.25c0 3.724 1.295 6.475 3.06 8.42a.75.75 0 0 1 1.265.75 8.25 8.25 0 0 1-1.32 1.946 8.25 8.25 0 0 0 3.495-.975.75.75 0 0 1 .75 1.299 9.75 9.75 0 0 1-5.25 1.234 9.75 9.75 0 0 1-5.25-1.234.75.75 0 0 1 .75-1.299 8.25 8.25 0 0 0 3.495.975 8.25 8.25 0 0 1-1.32-1.946C6.295 14.222 5 11.471 5 7.747a.75.75 0 0 1 1.5 0Zm9.75 3.66a.75.75 0 0 1 1.5 0c0 3.725-1.295 6.476-3.06 8.421a.75.75 0 0 1-1.266-.75 8.25 8.25 0 0 0 1.32-1.946c1.506-1.945 1.506-4.725 1.506-5.725Z" />
                    </svg>

                </div>

                <div>

                    <h2 class="text-lg font-bold text-slate-900">
                        ¿Listo para jugar?
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Escribe el código que aparece en la pantalla de tu profesor.
                    </p>

                </div>

            </div>

            <form method="POST" action="{{ route('student.game-sessions.join') }}" class="mt-6">

                @csrf

                <label class="mb-2 block text-sm font-semibold text-slate-700" for="code">
                    Código de la sala
                </label>

                <input
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-center text-lg font-bold uppercase tracking-widest text-slate-900 outline-none transition placeholder:tracking-normal placeholder:text-slate-400 focus:border-pink-500 focus:bg-white focus:ring-4 focus:ring-pink-100"
                    style="text-transform: uppercase;"
                    name="code"
                    id="code"
                    placeholder="KLS482"
                    maxlength="8"
                    autocomplete="off"
                    autocapitalize="characters"
                    aria-describedby="code-help"
                    value="{{ old('code') }}"
                    required>

                <div id="code-help" class="mt-2 block text-sm text-slate-500">
                    El código distingue la sala a la que vas a entrar.
                </div>

                @error('code')
                    <p class="mt-2 text-sm font-medium text-red-600" role="alert">
                        {{ $message }}
                    </p>
                @enderror

                <button type="submit"
                    class="mt-6 inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-pink-700 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-pink-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-pink-700 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">

                    Unirme a la sala

                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>

                </button>

            </form>

            </div>

        </div>

    </div>

@endsection
