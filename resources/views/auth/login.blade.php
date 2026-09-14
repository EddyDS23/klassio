
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Iniciar sesión - Klassio</title>
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
                    El aprendizaje
                    <span class="block text-amber-300">
                        nunca fue tan divertido.
                    </span>
                </h1>

                <p class="mt-6 max-w-md text-base leading-8 text-indigo-100">
                    Conecta con tus compañeros, descubre nuevas actividades
                    y aprende jugando en Klassio.
                </p>

            </div>


            <div class="relative z-10 flex items-center gap-3 text-sm text-indigo-100">

                <span class="h-2 w-2 rounded-full bg-teal-300"></span>

                Tu espacio educativo, en un solo lugar.

            </div>


            <!-- Decoraciones sutiles -->

            <div class="absolute -right-24 top-24 h-72 w-72 rounded-full border border-white/20"></div>

            <div class="absolute -bottom-32 -left-20 h-80 w-80 rounded-full border border-white/10"></div>

        </section>


        <!-- FORMULARIO DE LOGIN -->

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
                        BIENVENIDO DE NUEVO
                    </p>

                    <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">
                        Iniciar sesión
                    </h2>

                    <p class="mt-3 text-sm leading-6 text-slate-500">
                        Ingresa a tu cuenta para continuar aprendiendo.
                    </p>

                </div>


                <!-- ERRORES DE LARAVEL -->

                @if($errors->any())

                    <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700" role="alert">

                        <p class="font-bold">
                            Revisa los siguientes datos:
                        </p>

                        <ul class="mt-2 list-inside list-disc">

                            @foreach($errors->all() as $error)

                                <li>{{ $error }}</li>

                            @endforeach

                        </ul>

                    </div>

                @endif


                <!-- FORMULARIO -->

                <form method="POST" action="/login" class="space-y-6">

                    @csrf


                    <div>

                        <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">
                            Correo electrónico
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="ejemplo@correo.com"
                            autocomplete="email"
                            required
                            class="block w-full rounded-xl border border-slate-200 bg-white px-4 py-3.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                        >

                    </div>


                    <div>

                        <label for="password" class="mb-2 block text-sm font-semibold text-slate-700">
                            Contraseña
                        </label>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Ingresa tu contraseña"
                            autocomplete="current-password"
                            required
                            class="block w-full rounded-xl border border-slate-200 bg-white px-4 py-3.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                        >

                    </div>


                    <!-- BOTÓN -->

                    <button
                        type="submit"
                        class="flex w-full items-center justify-center gap-3 rounded-xl bg-indigo-600 px-5 py-4 text-sm font-bold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200 active:scale-[0.99]"
                    >

                        Entrar a Klassio

                        <span class="text-xl">→</span>

                    </button>

                </form>


                <!-- REGISTRO -->

                <p class="mt-8 text-center text-sm text-slate-500">

                    ¿Aún no tienes una cuenta?

                    <a
                        href="/register"
                        class="font-bold text-indigo-600 transition hover:text-indigo-800"
                    >
                        Regístrate aquí
                    </a>

                </p>


                <p class="mt-12 text-center text-xs text-slate-400">
                    © {{ date('Y') }} Klassio · Plataforma educativa
                </p>

            </div>

        </section>

    </main>

</body>

</html>
