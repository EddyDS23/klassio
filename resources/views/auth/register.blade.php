<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear cuenta | KLASSIO</title>
    @include('partials.assets')
    <style>
        #klassio-register {
            --register-surface: #fff;
            --register-ink: #0f172a;
            --register-muted: #64748b;
            --register-border: #cbd5e1;
            --register-soft: #f8fafc;
            --register-link: #5148e5;
            --register-error: #be123c;
            margin: 0;
            color: var(--register-ink);
            background-color: #f6f7fb;
            background-image: radial-gradient(ellipse at top left, rgb(99 91 255 / .12), transparent 55%), radial-gradient(ellipse at bottom right, rgb(245 158 11 / .1), transparent 50%);
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }
        #klassio-register *, #klassio-register *::before, #klassio-register *::after { box-sizing: border-box; }
        #klassio-register .register-shell {
            display: grid;
            place-items: center;
            min-height: 100vh;
            min-height: 100svh;
            padding: clamp(1rem, 3vw, 2.5rem) 1rem;
        }
        #klassio-register .register-card {
            width: 100%; max-width: 450px; min-width: 0;
            padding: clamp(1.5rem, 5vw, 2.5rem);
            border-radius: 1.5rem;
            background: var(--register-surface);
            box-shadow: 0 20px 60px rgb(15 23 42 / .12);
            border: 1px solid rgba(15, 23, 42, 0.06);
        }
        #klassio-register .register-heading { text-align: center; }
        #klassio-register .register-brand { display: inline-flex; align-items: center; gap: .75rem; margin-bottom: 1.5rem; font-size: 1.5rem; font-weight: 800; letter-spacing: .04em; }
        #klassio-register .register-logo { display: grid; place-items: center; flex-shrink: 0; width: 52px; height: 52px; border-radius: 1rem; background: var(--klassio-primary); color: #fff; font-size: 1.75rem; box-shadow: 0 8px 24px rgb(99 91 255 / .25); }
        #klassio-register h1 { margin: 0; color: var(--register-ink); font-size: clamp(1.75rem, 5vw, 2rem); font-weight: 800; line-height: 1.2; letter-spacing: -.025em; }
        #klassio-register .register-subtitle { margin: .75rem 0 0; color: var(--register-muted); font-size: .9375rem; line-height: 1.6; }
        #klassio-register .register-form { display: grid; grid-template-columns: minmax(0, 1fr); gap: 1.25rem; margin-top: 2rem; }
        #klassio-register .register-field { min-width: 0; }
        #klassio-register label { display: block; margin-bottom: .5rem; color: var(--register-ink); font-size: .875rem; font-weight: 600; }
        #klassio-register .register-control {
            display: block; width: 100%; min-width: 0; min-height: 52px;
            padding: .875rem 1rem; border: 2px solid var(--register-border); border-radius: .875rem;
            background: var(--register-surface); color: var(--register-ink);
            font: inherit; font-size: 1rem; line-height: 1.5;
            transition: border-color .15s, box-shadow .15s, transform .1s;
        }
        #klassio-register .register-control::placeholder { color: var(--register-muted); opacity: 1; }
        #klassio-register .register-control:hover { border-color: #94a3b8; }
        #klassio-register .register-control:focus-visible { border-color: var(--klassio-primary); outline: none; box-shadow: 0 0 0 3px rgb(99 91 255 / .15); }
        #klassio-register .register-control[aria-invalid="true"] { border-color: var(--register-error); background-color: rgb(190 18 60 / .04); }
        #klassio-register .register-password { position: relative; }
        #klassio-register .register-password .register-control { padding-right: 5.75rem; }
        #klassio-register .register-password-toggle {
            position: absolute; top: 4px; right: 4px; bottom: 4px;
            display: flex; align-items: center; justify-content: center; min-width: 4.5rem;
            padding: .375rem .75rem; border: 0; border-radius: .75rem;
            background: transparent; color: var(--register-link); font-size: .875rem; font-weight: 700; cursor: pointer;
        }
        #klassio-register .register-password-toggle[hidden] { display: none; }
        #klassio-register .register-password-toggle:hover { background: var(--register-soft); }
        #klassio-register .register-errors { margin-top: .5rem; color: var(--register-error); font-size: .8125rem; line-height: 1.5; overflow-wrap: anywhere; }
        #klassio-register .register-errors p { margin: 0; }
        #klassio-register .register-errors p + p { margin-top: .25rem; }
        #klassio-register .register-notice { margin-top: 1.5rem; padding: .875rem 1.25rem; border: 1px solid var(--register-error); border-radius: .875rem; background-color: rgb(190 18 60 / .04); color: var(--register-error); font-size: .875rem; line-height: 1.6; overflow-wrap: anywhere; }
        #klassio-register .register-notice p { margin: 0; }
        #klassio-register .register-submit {
            display: flex; align-items: center; justify-content: center; gap: .5rem;
            width: 100%; min-height: 52px; margin-top: .5rem; padding: 1rem 1.25rem;
            border: 1px solid transparent; border-radius: .875rem;
            background: var(--klassio-primary); color: #fff; font-size: 1rem; font-weight: 700;
            cursor: pointer; transition: background-color .15s, transform .1s;
        }
        #klassio-register .register-submit:hover { background: #5148e5; transform: translateY(-1px); }
        #klassio-register .register-submit:active { background: #4338ca; transform: translateY(0); }
        #klassio-register .register-footer { margin: 2rem 0 0; padding-top: 1.5rem; border-top: 1px solid var(--register-border); color: var(--register-muted); font-size: .875rem; line-height: 1.7; text-align: center; }
        #klassio-register .register-footer a { color: var(--register-link); font-weight: 700; }
        #klassio-register .register-footer a:hover { text-decoration: underline !important; }
        #klassio-register button:focus-visible, #klassio-register a:focus-visible { outline: 2px solid var(--klassio-primary); outline-offset: 3px; }
        html[data-theme="dark"] #klassio-register {
            --register-surface: #1e293b;
            --register-ink: #e2e8f0;
            --register-muted: #94a3b8;
            --register-border: #475569;
            --register-soft: #334155;
            --register-link: #a5b4fc;
            --register-error: #fda4af;
        }
        html[data-theme="dark"] #klassio-register .register-card { border-color: #334155; box-shadow: 0 20px 60px rgb(0 0 0 / .25); }
    </style>
</head>
<body id="klassio-register">
    <main class="register-shell">
        <section class="register-card rounded-2xl" aria-labelledby="register-title">
            <header class="register-heading">
                <div class="register-brand">
                    <span class="register-logo" aria-hidden="true">K</span>
                    <span>KLASSIO</span>
                </div>
                <h1 id="register-title">Crear cuenta</h1>
                <p class="register-subtitle">Regístrate para comenzar tu experiencia educativa</p>
            </header>

            @if ($errors->any())
                <div class="register-notice" role="alert">
                    <p>Revisa los campos indicados para continuar.</p>
                    @foreach ($errors->getMessages() as $field => $messages)
                        @unless (in_array($field, ['name', 'email', 'password', 'password_confirmation', 'role'], true))
                            @foreach ($messages as $message)
                                <p>{{ $message }}</p>
                            @endforeach
                        @endunless
                    @endforeach
                </div>
            @endif

            <form method="POST" action="/register" class="register-form">
                @csrf

                <div class="register-field">
                    <label for="register-name">Nombre completo</label>
                    <input type="text" id="register-name" name="name" value="{{ old('name') }}"
                        class="register-control" autocomplete="name" placeholder="Tu nombre completo"
                        aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}"
                        @if ($errors->has('name')) aria-describedby="register-name-error" @endif>
                    @if ($errors->has('name'))
                        <div id="register-name-error" class="register-errors">
                            @foreach ($errors->get('name') as $message)
                                <p>{{ $message }}</p>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="register-field">
                    <label for="register-email">Correo electrónico</label>
                    <input type="email" id="register-email" name="email" value="{{ old('email') }}"
                        class="register-control" autocomplete="email" placeholder="nombre@ejemplo.com"
                        aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                        @if ($errors->has('email')) aria-describedby="register-email-error" @endif>
                    @if ($errors->has('email'))
                        <div id="register-email-error" class="register-errors">
                            @foreach ($errors->get('email') as $message)
                                <p>{{ $message }}</p>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="register-field">
                    <label for="register-password">Contraseña</label>
                    <div class="register-password">
                        <input type="password" id="register-password" name="password"
                            class="register-control" autocomplete="new-password"
                            aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                            @if ($errors->has('password')) aria-describedby="register-password-error" @endif>
                        <button type="button" class="register-password-toggle" hidden data-password-toggle
                            data-password-label="contraseña" aria-controls="register-password" aria-pressed="false"
                            aria-label="Mostrar contraseña">Mostrar</button>
                    </div>
                    @if ($errors->has('password'))
                        <div id="register-password-error" class="register-errors">
                            @foreach ($errors->get('password') as $message)
                                <p>{{ $message }}</p>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="register-field">
                    <label for="register-password-confirmation">Confirmar contraseña</label>
                    <div class="register-password">
                        <input type="password" id="register-password-confirmation" name="password_confirmation"
                            class="register-control" autocomplete="new-password"
                            aria-invalid="{{ $errors->has('password_confirmation') ? 'true' : 'false' }}"
                            @if ($errors->has('password_confirmation')) aria-describedby="register-password-confirmation-error" @endif>
                        <button type="button" class="register-password-toggle" hidden data-password-toggle
                            data-password-label="confirmación de contraseña" aria-controls="register-password-confirmation" aria-pressed="false"
                            aria-label="Mostrar confirmación de contraseña">Mostrar</button>
                    </div>
                    @if ($errors->has('password_confirmation'))
                        <div id="register-password-confirmation-error" class="register-errors">
                            @foreach ($errors->get('password_confirmation') as $message)
                                <p>{{ $message }}</p>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="register-field">
                    <label for="register-role">Rol del usuario</label>
                    <select id="register-role" name="role" class="register-control"
                        aria-invalid="{{ $errors->has('role') ? 'true' : 'false' }}"
                        @if ($errors->has('role')) aria-describedby="register-role-error" @endif>
                        <option value="teacher" {{ old('role') == 'teacher' ? 'selected' : '' }}>Maestro</option>
                        <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Estudiante</option>
                    </select>
                    @if ($errors->has('role'))
                        <div id="register-role-error" class="register-errors">
                            @foreach ($errors->get('role') as $message)
                                <p>{{ $message }}</p>
                            @endforeach
                        </div>
                    @endif
                </div>

                <button type="submit" class="register-submit">Crear cuenta <span aria-hidden="true">→</span></button>
            </form>

            <p class="register-footer">¿Ya tienes una cuenta? <a href="/login">Iniciar sesión</a></p>
        </section>
    </main>

    <script>
        document.querySelectorAll('#klassio-register [data-password-toggle]').forEach(function (button) {
            var input = document.getElementById(button.getAttribute('aria-controls'));
            button.hidden = false;
            button.addEventListener('click', function () {
                var showPassword = input.type === 'password';
                input.type = showPassword ? 'text' : 'password';
                button.textContent = showPassword ? 'Ocultar' : 'Mostrar';
                button.setAttribute('aria-pressed', String(showPassword));
                button.setAttribute('aria-label', (showPassword ? 'Ocultar ' : 'Mostrar ') + button.getAttribute('data-password-label'));
            });
        });
    </script>
</body>
</html>
