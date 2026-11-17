<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Crear cuenta - Klassio</title>
    @include('partials.assets')

</head>

<body class="min-h-screen bg-slate-50 text-slate-900">

    <main class="grid min-h-screen lg:grid-cols-2">

        <!-- PANEL DE PRESENTACIÓN -->

        <section class="relative hidden overflow-hidden bg-indigo-600 p-12 text-white lg:flex lg:flex-col lg:justify-between">

            <div class="relative z-10 flex items-center gap-3">

                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-2xl font-extrabold text-indigo-600">
                    K
                </div>

                <span class="text-3xl font-extrabold tracking-tight">
                    Klassio
                </span>

            </div>


            <div class="relative z-10 max-w-lg">

                <span class="mb-5 inline-block rounded-full bg-white/15 px-4 py-2 text-xs font-bold tracking-widest text-indigo-100">
                    APRENDE · JUEGA · CONECTA
                </span>

                <h1 class="text-5xl font-extrabold leading-tight tracking-tight xl:text-6xl">
                    Crea tu cuenta y
                    <span class="block text-amber-300">
                        empieza a jugar.
                    </span>
                </h1>

                <p class="mt-6 max-w-md text-base leading-8 text-indigo-100">
                    Únete a tu clase con un código, participa en los juegos
                    y compite sanamente con tus compañeros.
                </p>


                <!-- BENEFICIOS -->

                <ul class="mt-8 space-y-4 text-sm leading-6 text-indigo-100">

                    <li class="flex items-center gap-3">

                        <span class="flex h-8 w-8 flex-none items-center justify-center rounded-lg bg-white/10 text-teal-300" aria-hidden="true">
                            <svg class="h-4.5 w-4.5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10.75 16.82A7.462 7.462 0 0 1 15 9.717V9a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v.717A7.462 7.462 0 0 1 8.25 16.82v1.268a7.455 7.455 0 0 1-4.375-2.947v1.9A7.44 7.44 0 0 0 9.5 20a7.44 7.44 0 0 0 5.625-2.958v-1.9A7.455 7.455 0 0 1 10.75 18.09v-1.27ZM12 7a1 1 0 1 1-2 0 1 1 0 0 1 2 0Z" />
                                <path fill-rule="evenodd" d="M15 11a4.5 4.5 0 1 0 0 9 4.5 4.5 0 0 0 0-9Zm2.05 4.55a.75.75 0 0 0-1.1-1.02l-1.45 1.56-.57-.61a.75.75 0 1 0-1.1 1.02l1.12 1.21a.75.75 0 0 0 1.1 0l2-2.16Z" clip-rule="evenodd" />
                            </svg>
                        </span>

                        Únete con el código de tu clase en segundos.

                    </li>

                    <li class="flex items-center gap-3">

                        <span class="flex h-8 w-8 flex-none items-center justify-center rounded-lg bg-white/10 text-amber-300" aria-hidden="true">
                            <svg class="h-4.5 w-4.5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 8.75a1.25 1.25 0 1 0 0 2.5 1.25 1.25 0 0 0 0-2.5Z" />
                                <path fill-rule="evenodd" d="M9.24 1.732a1.5 1.5 0 0 1 1.52 0l6.495 3.75a1.5 1.5 0 0 1 .75 1.299v7.438a1.5 1.5 0 0 1-.75 1.3l-6.495 3.749a1.5 1.5 0 0 1-1.52 0l-6.495-3.75a1.5 1.5 0 0 1-.75-1.3V6.781a1.5 1.5 0 0 1 .75-1.3L9.24 1.733ZM10 6.25a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5Z" clip-rule="evenodd" />
                            </svg>
                        </span>

                        Sopas de letras, crucigramas, kahoot y más.

                    </li>

                    <li class="flex items-center gap-3">

                        <span class="flex h-8 w-8 flex-none items-center justify-center rounded-lg bg-white/10 text-pink-300" aria-hidden="true">
                            <svg class="h-4.5 w-4.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M13.457 10.024a.75.75 0 0 1-.22.53l-2.25 2.25a.75.75 0 1 1-1.06-1.06l.97-.97H5.25a.75.75 0 0 1 0-1.5h5.646l-.97-.97a.75.75 0 1 1 1.06-1.06l2.25 2.25a.75.75 0 0 1 .22.53Z" clip-rule="evenodd" />
                                <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm4.25-8a4.25 4.25 0 1 1-8.5 0 4.25 4.25 0 0 1 8.5 0Z" clip-rule="evenodd" />
                            </svg>
                        </span>

                        Sube en el ranking, solo o con tu equipo.

                    </li>

                </ul>

            </div>


            <div class="relative z-10 flex items-center gap-3 text-sm text-indigo-100">

                <span class="h-2 w-2 rounded-full bg-pink-300"></span>

                Tu primera actividad te está esperando.

            </div>


            <!-- Decoraciones sutiles -->

            <div class="absolute -right-24 top-24 h-72 w-72 rounded-full border border-white/20"></div>

            <div class="absolute -bottom-32 -left-20 h-80 w-80 rounded-full border border-white/10"></div>

        </section>


        <!-- FORMULARIO DE REGISTRO -->

        <section class="flex min-h-screen items-center justify-center px-6 py-12 sm:px-12">

            <div class="w-full max-w-md">

                <!-- LOGO PARA CELULAR -->

                <div class="mb-12 flex items-center justify-center gap-3 lg:hidden">

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-600 text-xl font-extrabold text-white">
                        K
                    </div>

                    <span class="text-2xl font-extrabold tracking-tight">
                        Klassio
                    </span>

                </div>


                <!-- ENCABEZADO -->

                <div class="mb-9">

                    <p class="mb-3 text-xs font-bold tracking-widest text-indigo-600">
                        ÚNETE A LA CLASE
                    </p>

                    <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">
                        Crear cuenta
                    </h2>

                    <p class="mt-3 text-sm leading-6 text-slate-500">
                        Regístrate para comenzar tu experiencia educativa.
                    </p>

                </div>


                <!-- ERRORES DE LARAVEL -->

                @if ($errors->any())

                    <div
                        id="register-error-summary"
                        tabindex="-1"
                        role="alert"
                        aria-labelledby="register-error-summary-title"
                        class="mb-6 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700 outline-none"
                    >

                        <p id="register-error-summary-title" class="font-bold">
                            Revisa los siguientes datos:
                        </p>

                        <ul class="mt-2 list-inside list-disc">

                            @foreach ($errors->all() as $error)

                                <li>{{ $error }}</li>

                            @endforeach

                        </ul>

                        <ul class="mt-3 space-y-1">

                            @foreach (['name' => 'register-name', 'email' => 'register-email', 'password' => 'register-password', 'password_confirmation' => 'register-password-confirmation'] as $field => $anchor)

                                @if ($errors->has($field))

                                    <li>
                                        <a href="#{{ $anchor }}" class="font-semibold underline underline-offset-2 transition hover:text-rose-900">
                                            {{ $errors->first($field) }}
                                        </a>
                                    </li>

                                @endif

                            @endforeach

                        </ul>

                    </div>

                @endif


                <!-- FORMULARIO -->

                <form
                    method="POST"
                    action="/register"
                    class="space-y-6"
                    novalidate
                    data-register-form
                >

                    @csrf


                    <div>

                        <label for="register-name" class="mb-2 block text-sm font-semibold text-slate-700">
                            Nombre completo
                        </label>

                        <input
                            type="text"
                            id="register-name"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Tu nombre completo"
                            autocomplete="name"
                            required
                            aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}"
                            class="block w-full rounded-xl border bg-white px-4 py-3.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:ring-4 @if($errors->has('name')) border-rose-400 focus:border-rose-500 focus:ring-rose-100 @else border-slate-200 focus:border-indigo-500 focus:ring-indigo-100 @endif"
                        >

                        @if ($errors->has('name'))

                            <p id="register-name-error" class="mt-2 text-xs font-semibold text-rose-600">
                                {{ $errors->first('name') }}
                            </p>

                        @endif

                    </div>


                    <div>

                        <label for="register-email" class="mb-2 block text-sm font-semibold text-slate-700">
                            Correo electrónico
                        </label>

                        <input
                            type="email"
                            id="register-email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="ejemplo@correo.com"
                            autocomplete="email"
                            required
                            aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                            class="block w-full rounded-xl border bg-white px-4 py-3.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:ring-4 @if($errors->has('email')) border-rose-400 focus:border-rose-500 focus:ring-rose-100 @else border-slate-200 focus:border-indigo-500 focus:ring-indigo-100 @endif"
                        >

                        @if ($errors->has('email'))

                            <p id="register-email-error" class="mt-2 text-xs font-semibold text-rose-600">
                                {{ $errors->first('email') }}
                            </p>

                        @endif

                    </div>


                    <div>

                        <label for="register-password" class="mb-2 block text-sm font-semibold text-slate-700">
                            Contraseña
                        </label>

                        <div class="relative">

                            <input
                                type="password"
                                id="register-password"
                                name="password"
                                placeholder="Mínimo 8 caracteres"
                                autocomplete="new-password"
                                required
                                aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                                @if ($errors->has('password')) aria-describedby="register-password-error" @endif
                                class="block w-full rounded-xl border bg-white px-4 py-3.5 pr-24 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:ring-4 @if($errors->has('password')) border-rose-400 focus:border-rose-500 focus:ring-rose-100 @else border-slate-200 focus:border-indigo-500 focus:ring-indigo-100 @endif"
                            >

                            <button
                                type="button"
                                class="absolute inset-y-1.5 right-1.5 flex w-20 items-center justify-center gap-1.5 rounded-lg text-xs font-bold text-indigo-600 transition hover:bg-indigo-50"
                                data-password-toggle
                                aria-controls="register-password"
                                aria-pressed="false"
                                aria-label="Mostrar contraseña"
                            >

                                <svg class="h-5 w-5" data-icon="show" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M10 1c3.66 0 6.898 2.073 8.636 5.287l.012.022a3.2 3.2 0 0 1 0 2.93l-.012.023C16.898 12.472 13.66 14.545 10 14.545c-3.66 0-6.898-2.073-8.636-5.283l-.012-.022a3.2 3.2 0 0 1 0-2.93l.012-.023C3.102 3.073 6.34 1 10 1Zm0 3a3.727 3.727 0 1 0 0 7.455A3.727 3.727 0 0 0 10 4Zm0 5.727a2 2 0 1 1 0-4 2 2 0 0 1 0 4Z" clip-rule="evenodd" />
                                </svg>

                                <svg class="hidden h-5 w-5" data-icon="hide" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M3.28 2.22a.75.75 0 0 0-1.06 1.06l14.5 14.5a.75.75 0 1 0 1.06-1.06l-2.808-2.808c1.405-1.122 2.569-2.616 3.356-4.297C16.42 6.28 13.478 3 10 3c-1.558 0-3.003.575-4.192 1.537L3.28 2.22ZM8.58 6.519l4.901 4.9c.18-.28.28-.61.28-.966a3.76 3.76 0 0 0-3.727-3.727c-.356 0-.686.1-.967.28l-.487-.487ZM9.026 12.47l2.504 2.503a8.213 8.213 0 0 1-1.53.171c-3.48 0-6.421-3.28-7.328-6.617a.75.75 0 0 1 0-.489 14.3 14.3 0 0 1 1.465-2.702l2.19 2.19a3.727 3.727 0 0 0 2.699 4.944Z" clip-rule="evenodd" />
                                </svg>

                                <span data-toggle-text>Mostrar</span>

                            </button>

                        </div>

                        @if ($errors->has('password'))

                            <p id="register-password-error" class="mt-2 text-xs font-semibold text-rose-600">
                                {{ $errors->first('password') }}
                            </p>

                        @endif

                    </div>


                    <div>

                        <label for="register-password-confirmation" class="mb-2 block text-sm font-semibold text-slate-700">
                            Confirmar contraseña
                        </label>

                        <div class="relative">

                            <input
                                type="password"
                                id="register-password-confirmation"
                                name="password_confirmation"
                                placeholder="Repite tu contraseña"
                                autocomplete="new-password"
                                required
                                aria-invalid="{{ $errors->has('password_confirmation') ? 'true' : 'false' }}"
                                @if ($errors->has('password_confirmation')) aria-describedby="register-password-confirmation-error" @endif
                                class="block w-full rounded-xl border bg-white px-4 py-3.5 pr-24 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:ring-4 @if($errors->has('password_confirmation')) border-rose-400 focus:border-rose-500 focus:ring-rose-100 @else border-slate-200 focus:border-indigo-500 focus:ring-indigo-100 @endif"
                            >

                            <button
                                type="button"
                                class="absolute inset-y-1.5 right-1.5 flex w-20 items-center justify-center gap-1.5 rounded-lg text-xs font-bold text-indigo-600 transition hover:bg-indigo-50"
                                data-password-toggle
                                aria-controls="register-password-confirmation"
                                aria-pressed="false"
                                aria-label="Mostrar confirmación de contraseña"
                            >

                                <svg class="h-5 w-5" data-icon="show" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M10 1c3.66 0 6.898 2.073 8.636 5.287l.012.022a3.2 3.2 0 0 1 0 2.93l-.012.023C16.898 12.472 13.66 14.545 10 14.545c-3.66 0-6.898-2.073-8.636-5.283l-.012-.022a3.2 3.2 0 0 1 0-2.93l.012-.023C3.102 3.073 6.34 1 10 1Zm0 3a3.727 3.727 0 1 0 0 7.455A3.727 3.727 0 0 0 10 4Zm0 5.727a2 2 0 1 1 0-4 2 2 0 0 1 0 4Z" clip-rule="evenodd" />
                                </svg>

                                <svg class="hidden h-5 w-5" data-icon="hide" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M3.28 2.22a.75.75 0 0 0-1.06 1.06l14.5 14.5a.75.75 0 1 0 1.06-1.06l-2.808-2.808c1.405-1.122 2.569-2.616 3.356-4.297C16.42 6.28 13.478 3 10 3c-1.558 0-3.003.575-4.192 1.537L3.28 2.22ZM8.58 6.519l4.901 4.9c.18-.28.28-.61.28-.966a3.76 3.76 0 0 0-3.727-3.727c-.356 0-.686.1-.967.28l-.487-.487ZM9.026 12.47l2.504 2.503a8.213 8.213 0 0 1-1.53.171c-3.48 0-6.421-3.28-7.328-6.617a.75.75 0 0 1 0-.489 14.3 14.3 0 0 1 1.465-2.702l2.19 2.19a3.727 3.727 0 0 0 2.699 4.944Z" clip-rule="evenodd" />
                                </svg>

                                <span data-toggle-text>Mostrar</span>

                            </button>

                        </div>

                        @if ($errors->has('password_confirmation'))

                            <p id="register-password-confirmation-error" class="mt-2 text-xs font-semibold text-rose-600">
                                {{ $errors->first('password_confirmation') }}
                            </p>

                        @endif

                    </div>


                    <!-- BOTÓN -->

                    <button
                        type="submit"
                        class="flex w-full items-center justify-center gap-3 rounded-xl bg-indigo-600 px-5 py-4 text-sm font-bold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200 active:scale-[0.99]"
                    >

                        Crear mi cuenta

                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.638L10.23 5.29a.75.75 0 1 1 1.04-1.08l5.5 5.25a.75.75 0 0 1 0 1.08l-5.5 5.25a.75.75 0 1 1-1.04-1.08l4.158-3.96H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd" />
                        </svg>

                    </button>

                </form>


                <!-- INICIO DE SESIÓN -->

                <p class="mt-8 text-center text-sm text-slate-500">

                    ¿Ya tienes una cuenta?

                    <a
                        href="/login"
                        class="font-bold text-indigo-600 transition hover:text-indigo-800"
                    >
                        Inicia sesión aquí
                    </a>

                </p>


                <p class="mt-12 text-center text-xs text-slate-400">
                    © {{ date('Y') }} Klassio · Plataforma educativa
                </p>

            </div>

        </section>

    </main>


    <script>
        // Toggle mostrar/ocultar contraseña (SVG + texto accesible).
        document.querySelectorAll('[data-password-toggle]').forEach(function (button) {
            var input = document.getElementById(button.getAttribute('aria-controls'));
            var iconShow = button.querySelector('[data-icon="show"]');
            var iconHide = button.querySelector('[data-icon="hide"]');
            var toggleText = button.querySelector('[data-toggle-text]');
            var labelBase = button.getAttribute('aria-label');

            button.addEventListener('click', function () {
                var showPassword = input.type === 'password';

                input.type = showPassword ? 'text' : 'password';
                button.setAttribute('aria-pressed', String(showPassword));
                button.setAttribute('aria-label', (showPassword ? 'Ocultar ' : 'Mostrar ') + labelBase.replace(/^Mostrar /, ''));
                toggleText.textContent = showPassword ? 'Ocultar' : 'Mostrar';
                iconShow.classList.toggle('hidden', showPassword);
                iconHide.classList.toggle('hidden', !showPassword);
            });
        });

        // Al fallar la validación, el resumen de errores recibe el foco (teclado y lectores de pantalla).
        (function () {
            var summary = document.getElementById('register-error-summary');
            if (summary) {
                summary.focus();
            }
        })();
    </script>

</body>

</html>
