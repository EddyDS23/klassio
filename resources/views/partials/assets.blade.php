{{-- Recursos visuales sin Vite ni Node. --}}
@php($theme = $theme ?? 'guest')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
<script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
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
        :root { --klassio-primary: #7c3aed; --klassio-accent: #f97316; }
        body { background-color: #faf5ff; background-image: radial-gradient(circle at top right, rgb(124 58 237 / .18), transparent 32rem), radial-gradient(circle at bottom left, rgb(249 115 22 / .12), transparent 28rem); }
        .page-hero { background: linear-gradient(135deg, #6d28d9, #9333ea 55%, #f97316); }
    @elseif ($theme === 'student')
        :root { --klassio-primary: #0891b2; --klassio-accent: #8b5cf6; }
        body { background-color: #f0fdfa; background-image: radial-gradient(circle at top right, rgb(6 182 212 / .18), transparent 32rem), radial-gradient(circle at bottom left, rgb(139 92 246 / .12), transparent 28rem); }
        .page-hero { background: linear-gradient(135deg, #0891b2, #2563eb 55%, #7c3aed); }
    @elseif ($theme === 'admin')
        :root { --klassio-primary: #0f172a; --klassio-accent: #f59e0b; }
        body { background-color: #f6f7fb; background-image: radial-gradient(circle at top right, rgb(15 23 42 / .07), transparent 32rem); }
        .page-hero { background: linear-gradient(135deg, #0f172a, #1e293b 55%, #334155); }
    @endif
    /* Modo negro: solo Bootstrap + Tailwind CDN, sin Vite. Se activa con html[data-theme="dark"]. */
    html[data-theme="dark"] body { background-color: #0f172a !important; background-image: none !important; color: #e2e8f0 !important; }
    html[data-theme="dark"] .bg-white { background-color: #1e293b !important; }
    html[data-theme="dark"] .bg-slate-50 { background-color: #1e293b !important; }
    html[data-theme="dark"] .bg-slate-100 { background-color: #334155 !important; }
    html[data-theme="dark"] .text-slate-900, html[data-theme="dark"] .text-slate-800 { color: #f1f5f9 !important; }
    html[data-theme="dark"] .text-slate-600, html[data-theme="dark"] .text-slate-500 { color: #cbd5e1 !important; }
    html[data-theme="dark"] .border-slate-200, html[data-theme="dark"] .border-slate-100 { border-color: #334155 !important; }
    html[data-theme="dark"] header, html[data-theme="dark"] aside { background-color: #1e293b !important; border-color: #334155 !important; }
    html[data-theme="dark"] .surface-card, html[data-theme="dark"] .student-activity > section, html[data-theme="dark"] section { background-color: #1e293b !important; color: #e2e8f0 !important; border-color: #334155 !important; }
    html[data-theme="dark"] .student-activity { color: #e2e8f0 !important; }
    html[data-theme="dark"] .student-activity > h1 { color: #38bdf8 !important; }
    html[data-theme="dark"] input, html[data-theme="dark"] select, html[data-theme="dark"] textarea { background-color: #0f172a !important; color: #f1f5f9 !important; border-color: #475569 !important; }
    html[data-theme="dark"] table { color: #e2e8f0 !important; }
    html[data-theme="dark"] .page-hero { box-shadow: 0 1rem 2.5rem rgb(0 0 0 / .4); }
    .klassio-theme-btn { border: 1px solid #cbd5e1; border-radius: .75rem; background: #fff; padding: .5rem .75rem; font-size: .85rem; font-weight: 700; cursor: pointer; white-space: nowrap; }
    html[data-theme="dark"] .klassio-theme-btn { background: #0f172a; color: #f1f5f9; border-color: #475569; }
</style>
<script>
    // Aplica modo guardado antes de pintar (claro por defecto). Sin Vite, solo Bootstrap + Tailwind CDN.
    try {
        if (localStorage.getItem('klassio-theme') === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
        }
    } catch (e) {}
</script>
<script>
    // Botón junto a "Cerrar sesión" para que maestro y alumno elijan negro/blanco. Sin Vite.
    document.addEventListener('DOMContentLoaded', function () {
        var isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        function label() { return isDark ? '☀️ Claro' : '🌙 Negro'; }
        function toggle() {
            isDark = !isDark;
            if (isDark) {
                document.documentElement.setAttribute('data-theme', 'dark');
            } else {
                document.documentElement.removeAttribute('data-theme');
            }
            try { localStorage.setItem('klassio-theme', isDark ? 'dark' : 'light'); } catch (e) {}
            document.querySelectorAll('.klassio-theme-btn').forEach(function (b) { b.textContent = label(); });
        }
        document.querySelectorAll('form[action*="logout"]').forEach(function (form) {
            if (form.parentElement && form.parentElement.querySelector('.klassio-theme-btn')) return;
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'klassio-theme-btn';
            btn.textContent = label();
            btn.addEventListener('click', toggle);
            btn.style.marginRight = '0.5rem';
            form.parentElement.insertBefore(btn, form);
            form.style.display = 'inline-block';
        });
    });
</script>
