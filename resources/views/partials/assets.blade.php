{{-- Recursos visuales sin Vite ni Node. --}}
@php($theme = $theme ?? 'guest')
@if ($includeBootstrap ?? true)
    {{-- Bootstrap dentro de una capa CSS: sin esto, sus reglas "sin capa" (p. ej. a { color: azul })
         pisan cualquier clase de color de Tailwind v4, que vive en la capa "utilities". --}}
    <style>
        @layer bootstrap;
        @import url("https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css") layer(bootstrap);
    </style>
@endif
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4.3.5"></script>
<style type="text/tailwindcss">
    /* Paleta verde docente ("bosque"): neutrales con base verde + verde vibrante. */
    @theme {
        --color-bosque-50: #F1F7F4;
        --color-bosque-100: #DFEFE8;
        --color-bosque-200: #C6E3D8;
        --color-bosque-700: #2A5747;
        --color-bosque-800: #16352C;
        --color-bosque-900: #0E241E;
        --color-bosque-950: #071411;
        --color-klassio-mint: #10B981;
    }
</style>
@if ($includeBootstrap ?? true)
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
@endif
<style>
    :root { --klassio-primary: #635bff; --klassio-accent: #f59e0b; }
    body { min-height: 100vh; background-image: radial-gradient(circle at top right, rgb(99 91 255 / .12), transparent 30rem); }
    a { text-decoration: none !important; }
    .btn-klassio { background: var(--klassio-primary); border-color: var(--klassio-primary); color: #fff; }
    .btn-klassio:hover { background: #5148e5; border-color: #5148e5; color: #fff; }
    .page-shell { max-width: 1180px; margin: 0 auto; padding: 2rem 1rem 3rem; }
    .page-hero { border-radius: 1.5rem; color: #fff; padding: clamp(1.5rem, 4vw, 3rem); margin-bottom: 1.5rem; box-shadow: 0 1rem 2.5rem rgb(15 23 42 / .14); }
    .surface-card { border: 0; border-radius: 1.25rem; box-shadow: 0 .75rem 1.75rem rgb(15 23 42 / .08); }
    .activity-icon { width: 3.25rem; height: 3.25rem; display: grid; place-items: center; border-radius: 1rem; font-size: 1.5rem; background: rgb(255 255 255 / .75); }
    .student-activity { max-width: 980px; margin: 0 auto; padding: 2rem 1rem 3rem; color: #1e293b; }
    .student-activity > h1 { color: var(--klassio-primary); font-weight: 800; margin-bottom: .5rem; }
    .student-activity > section { background: #fff; border-radius: 1.25rem; padding: 1.5rem; margin: 1.25rem 0; box-shadow: 0 .75rem 1.75rem rgb(15 23 42 / .08); }
    .student-activity dl { display: grid; grid-template-columns: max-content 1fr; gap: .75rem 1rem; margin: 0; }
    .student-activity dt { font-weight: 700; color: #475569; }
    .student-activity dd { margin: 0; }
    .student-activity a, .student-activity button { border-radius: .75rem; }
    .student-activity a:not(nav a) { display: inline-block; background: var(--klassio-primary); color: #fff; padding: .65rem 1rem; text-decoration: none; font-weight: 700; }
    .student-activity button { border: 0; background: var(--klassio-primary); color: #fff; padding: .65rem 1rem; font-weight: 700; margin: .25rem; }
    @if ($theme === 'teacher')
        :root { --klassio-primary: #047857; --klassio-accent: #f59e0b; --bs-link-color-rgb: 4, 120, 87; --bs-link-hover-color-rgb: 6, 95, 70; }
        body { background-color: #f6f7f6; background-image: radial-gradient(circle at top right, rgb(5 150 105 / .07), transparent 32rem); }
        .page-hero { background: linear-gradient(135deg, #047857, #059669 55%, #10B981); }
    @elseif ($theme === 'student')
        :root { --klassio-primary: #c2410c; --klassio-accent: #db2777; }
        body { background-color: #f1f5f9; background-image: radial-gradient(circle at top right, rgb(234 88 12 / .08), transparent 32rem); }
        .page-hero { background: #c2410c; }
    @elseif ($theme === 'admin')
        :root { --klassio-primary: #0f172a; --klassio-accent: #f59e0b; }
        body { background-color: #f6f7fb; background-image: radial-gradient(circle at top right, rgb(15 23 42 / .07), transparent 32rem); }
        .page-hero { background: linear-gradient(135deg, #0f172a, #1e293b 55%, #334155); }
    @endif
    /* === UTILIDAD GLOBAL DE BOTONES === */
    /* Radio consistente = botón "Negro" (0.75rem = 12px) */
    .btn-klassio-rounded,
    button.btn-klassio-rounded,
    a.btn-klassio-rounded,
    .btn-klassio { border-radius: .75rem !important; }

    /* Normaliza todos los botones submit/button/enlace-botón */
    button[type="submit"],
    button[type="button"],
    button:not([class*="rounded-"]),
    a[href*="/logout"] button,
    form[action*="/logout"] button,
    .btn-klassio { border-radius: .75rem; }

    /* Elimina esquinas cuadradas en contenedores de botones */
    form[action*="/logout"] { display: inline-flex; }
    form[action*="/logout"] > button { border-radius: .75rem; }

    /* Ajustes de lectura para el perfil infantil, sin cambiar los demás perfiles. */
    body.student-kids { font-size: 18px; }
    body.student-kids .student-activity > h1 { font-size: 2.1rem; }
    body.student-kids .student-activity a,
    body.student-kids .student-activity button { padding: .9rem 1.25rem; font-size: 1.05rem; }
    body.student-kids #timer { font-size: 1.1rem; }
    body.student-kids .klassio-toast-msg { font-size: .95rem; }
    body.student-kids :focus-visible { outline: 2px solid #ea580c; outline-offset: 2px; }

    /* Acciones de regreso: mismo marco en vistas Tailwind y Bootstrap. */
    a.klassio-back-button,
    .student-activity a.klassio-back-button {
        display: inline-flex; align-items: center; justify-content: center; gap: .5rem;
        max-width: 100%; min-height: 44px; padding: .75rem 1.25rem;
        border: 1px solid #cbd5e1; border-radius: .75rem;
        background: #fff; color: #475569; box-shadow: 0 1px 2px rgb(15 23 42 / .05);
        font-size: .875rem; font-weight: 700; line-height: 1.5; text-align: center;
        text-decoration: none; transition: background-color .15s, border-color .15s;
    }
    a.klassio-back-button:hover { background: #f8fafc; border-color: #94a3b8; color: #334155; }
    a.klassio-back-button:focus-visible { outline: 2px solid #6366f1; outline-offset: 3px; }
    ::selection { background: rgb(99 91 255 / .25); }
    :focus-visible { outline: 2px solid #6366f1; outline-offset: 2px; }
    ::-webkit-scrollbar { width: 10px; height: 10px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 8px; }
    ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
<script>
    // KLASSIO usa únicamente el tema claro. Se eliminó el selector de modo oscuro.
    // Este script limpia preferencias antiguas para que nunca vuelvan a activar el modo oscuro.
    (function () {
        try {
            document.documentElement.removeAttribute('data-theme');
            document.documentElement.classList.remove('dark');
            localStorage.removeItem('klassio-theme');
        } catch (e) {}
    })();
</script>
