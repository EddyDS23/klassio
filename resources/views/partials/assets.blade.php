{{-- Recursos visuales sin Vite ni Node. Solo Bootstrap + Tailwind por CDN. --}}
@php($theme = $theme ?? 'guest')
<link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
<link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
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
    /* ============================================================
       MODO NEGRO — se activa con html[data-theme="dark"].
       Solo re-colorea: no cambia la estructura ni oculta nada.
       ============================================================ */
    html[data-theme="dark"] {
        color-scheme: dark;
        --kd-bg: #0f172a;
        --kd-surface: #1e293b;
        --kd-surface-2: #273449;
        --kd-border: #334155;
        --kd-text: #e2e8f0;
        --kd-muted: #94a3b8;
        --kd-faint: #64748b;
    }
    html[data-theme="dark"] body {
        background-color: var(--kd-bg) !important;
        background-image: radial-gradient(circle at top right, rgb(99 91 255 / .14), transparent 30rem) !important;
        color: var(--kd-text) !important;
    }
    html[data-theme="dark"] body, html[data-theme="dark"] body * {
        transition: background-color .25s ease, border-color .25s ease;
    }

    /* ---------- Superficies claras -> superficies oscuras ---------- */
    html[data-theme="dark"] .bg-white { background-color: var(--kd-surface) !important; }
    html[data-theme="dark"] .bg-slate-50 { background-color: var(--kd-surface) !important; }
    html[data-theme="dark"] .bg-slate-100, html[data-theme="dark"] .bg-slate-200 { background-color: var(--kd-surface-2) !important; }
    html[data-theme="dark"] .bg-gray-50, html[data-theme="dark"] .bg-gray-100, html[data-theme="dark"] .bg-gray-200 { background-color: var(--kd-surface-2) !important; }
    html[data-theme="dark"] .bg-white\/90 { background-color: rgb(30 41 59 / .92) !important; }
    html[data-theme="dark"] .bg-white\/80 { background-color: rgb(2 6 23 / .45) !important; }
    html[data-theme="dark"] .bg-white\/75 { background-color: rgb(30 41 59 / .8) !important; }
    html[data-theme="dark"] .bg-white\/70 { background-color: rgb(2 6 23 / .55) !important; }
    html[data-theme="dark"] .bg-white\/15, html[data-theme="dark"] .bg-white\/20 { background-color: rgb(255 255 255 / .12) !important; }
    html[data-theme="dark"] .bg-indigo-50, html[data-theme="dark"] .bg-indigo-100 { background-color: rgb(99 102 241 / .18) !important; }
    html[data-theme="dark"] .bg-violet-50, html[data-theme="dark"] .bg-violet-100 { background-color: rgb(139 92 246 / .18) !important; }
    html[data-theme="dark"] .bg-blue-50, html[data-theme="dark"] .bg-blue-100 { background-color: rgb(59 130 246 / .18) !important; }
    html[data-theme="dark"] .bg-sky-50, html[data-theme="dark"] .bg-sky-100 { background-color: rgb(14 165 233 / .18) !important; }
    html[data-theme="dark"] .bg-emerald-50, html[data-theme="dark"] .bg-emerald-100 { background-color: rgb(16 185 129 / .16) !important; }
    html[data-theme="dark"] .bg-teal-50, html[data-theme="dark"] .bg-teal-100 { background-color: rgb(20 184 166 / .16) !important; }
    html[data-theme="dark"] .bg-cyan-50, html[data-theme="dark"] .bg-cyan-100 { background-color: rgb(6 182 212 / .16) !important; }
    html[data-theme="dark"] .bg-amber-50, html[data-theme="dark"] .bg-amber-100 { background-color: rgb(245 158 11 / .16) !important; }
    html[data-theme="dark"] .bg-amber-200 { background-color: #92400e !important; }
    html[data-theme="dark"] .bg-orange-200 { background-color: #9a3412 !important; }
    html[data-theme="dark"] .bg-orange-50, html[data-theme="dark"] .bg-orange-100 { background-color: rgb(249 115 22 / .16) !important; }
    html[data-theme="dark"] .bg-rose-50, html[data-theme="dark"] .bg-rose-100 { background-color: rgb(244 63 94 / .16) !important; }
    html[data-theme="dark"] .bg-red-50, html[data-theme="dark"] .bg-red-100 { background-color: rgb(239 68 68 / .16) !important; }
    html[data-theme="dark"] .bg-pink-50, html[data-theme="dark"] .bg-pink-100 { background-color: rgb(236 72 153 / .16) !important; }

    /* ---------- Texto ---------- */
    html[data-theme="dark"] .text-slate-900, html[data-theme="dark"] .text-slate-800 { color: #f1f5f9 !important; }
    html[data-theme="dark"] .text-slate-700 { color: #cbd5e1 !important; }
    html[data-theme="dark"] .text-slate-600, html[data-theme="dark"] .text-slate-500 { color: #aab9cc !important; }
    html[data-theme="dark"] .text-slate-400 { color: var(--kd-muted) !important; }
    html[data-theme="dark"] .text-gray-900, html[data-theme="dark"] .text-gray-800, html[data-theme="dark"] .text-gray-700 { color: #e2e8f0 !important; }
    html[data-theme="dark"] .text-gray-600, html[data-theme="dark"] .text-gray-500 { color: #aab9cc !important; }
    html[data-theme="dark"] .text-indigo-600, html[data-theme="dark"] .text-indigo-700 { color: #a5b4fc !important; }
    html[data-theme="dark"] .text-indigo-800 { color: #c7d2fe !important; }
    html[data-theme="dark"] .text-violet-600, html[data-theme="dark"] .text-violet-700 { color: #c4b5fd !important; }
    html[data-theme="dark"] .text-blue-600, html[data-theme="dark"] .text-blue-700 { color: #93c5fd !important; }
    html[data-theme="dark"] .text-sky-700 { color: #7dd3fc !important; }
    html[data-theme="dark"] .text-emerald-800 { color: #6ee7b7 !important; }
    html[data-theme="dark"] .text-emerald-700, html[data-theme="dark"] .text-emerald-600 { color: #6ee7b7 !important; }
    html[data-theme="dark"] .text-teal-800, html[data-theme="dark"] .text-teal-700, html[data-theme="dark"] .text-teal-600 { color: #5eead4 !important; }
    html[data-theme="dark"] .text-cyan-900, html[data-theme="dark"] .text-cyan-800, html[data-theme="dark"] .text-cyan-700 { color: #a5f3fc !important; }
    html[data-theme="dark"] .text-amber-800, html[data-theme="dark"] .text-amber-700, html[data-theme="dark"] .text-amber-600 { color: #fcd34d !important; }
    html[data-theme="dark"] .text-orange-600, html[data-theme="dark"] .text-orange-700 { color: #fdba74 !important; }
    html[data-theme="dark"] .text-rose-800, html[data-theme="dark"] .text-rose-700, html[data-theme="dark"] .text-rose-600 { color: #fda4af !important; }
    html[data-theme="dark"] .text-red-800, html[data-theme="dark"] .text-red-700, html[data-theme="dark"] .text-red-600 { color: #fca5a5 !important; }
    html[data-theme="dark"] .text-pink-700 { color: #f9a8d4 !important; }

    /* ---------- Bordes y divisores ---------- */
    html[data-theme="dark"] .border-slate-100, html[data-theme="dark"] .border-slate-200, html[data-theme="dark"] .border-slate-300 { border-color: var(--kd-border) !important; }
    html[data-theme="dark"] .border-gray-200, html[data-theme="dark"] .border-gray-300 { border-color: var(--kd-border) !important; }
    html[data-theme="dark"] .border-indigo-100, html[data-theme="dark"] .border-indigo-200 { border-color: rgb(99 102 241 / .35) !important; }
    html[data-theme="dark"] .border-violet-100, html[data-theme="dark"] .border-violet-200 { border-color: rgb(139 92 246 / .35) !important; }
    html[data-theme="dark"] .border-blue-100, html[data-theme="dark"] .border-blue-200 { border-color: rgb(59 130 246 / .35) !important; }
    html[data-theme="dark"] .border-emerald-100, html[data-theme="dark"] .border-emerald-200, html[data-theme="dark"] .border-emerald-300 { border-color: rgb(16 185 129 / .35) !important; }
    html[data-theme="dark"] .border-teal-100, html[data-theme="dark"] .border-teal-200 { border-color: rgb(20 184 166 / .35) !important; }
    html[data-theme="dark"] .border-cyan-100, html[data-theme="dark"] .border-cyan-200, html[data-theme="dark"] .border-cyan-300 { border-color: rgb(6 182 212 / .35) !important; }
    html[data-theme="dark"] .border-amber-100, html[data-theme="dark"] .border-amber-200 { border-color: rgb(245 158 11 / .35) !important; }
    html[data-theme="dark"] .border-orange-100, html[data-theme="dark"] .border-orange-200 { border-color: rgb(249 115 22 / .35) !important; }
    html[data-theme="dark"] .border-rose-100, html[data-theme="dark"] .border-rose-200 { border-color: rgb(244 63 94 / .35) !important; }
    html[data-theme="dark"] .border-red-100, html[data-theme="dark"] .border-red-200 { border-color: rgb(239 68 68 / .35) !important; }
    html[data-theme="dark"] .divide-slate-100 > *, html[data-theme="dark"] .divide-slate-200 > * { border-color: var(--kd-border) !important; }
    html[data-theme="dark"] hr { border-color: var(--kd-border) !important; }

    /* ---------- Anillos de foco (Tailwind v4) ---------- */
    html[data-theme="dark"] .ring-indigo-100, html[data-theme="dark"] .ring-indigo-200 { --tw-ring-color: rgb(99 102 241 / .45) !important; }
    html[data-theme="dark"] .ring-emerald-100, html[data-theme="dark"] .ring-emerald-500 { --tw-ring-color: rgb(16 185 129 / .45) !important; }
    html[data-theme="dark"] .ring-teal-100 { --tw-ring-color: rgb(20 184 166 / .45) !important; }
    html[data-theme="dark"] .ring-slate-200, html[data-theme="dark"] .ring-gray-300 { --tw-ring-color: rgb(100 116 139 / .6) !important; }

    /* ---------- Sombras: se suavizan, no se eliminan ---------- */
    html[data-theme="dark"] .shadow-sm { box-shadow: 0 .125rem .5rem rgb(0 0 0 / .45) !important; }
    html[data-theme="dark"] .shadow, html[data-theme="dark"] .shadow-md { box-shadow: 0 .5rem 1.25rem rgb(0 0 0 / .45) !important; }
    html[data-theme="dark"] .shadow-lg, html[data-theme="dark"] .shadow-xl { box-shadow: 0 .75rem 2rem rgb(0 0 0 / .5) !important; }
    html[data-theme="dark"] .shadow-slate-200\/60, html[data-theme="dark"] .shadow-slate-900\/5 { --tw-shadow-color: rgb(0 0 0 / .5) !important; }
    html[data-theme="dark"] .shadow-emerald-100, html[data-theme="dark"] .shadow-emerald-200,
    html[data-theme="dark"] .shadow-emerald-500\/20, html[data-theme="dark"] .shadow-emerald-600\/20,
    html[data-theme="dark"] .shadow-emerald-900\/10, html[data-theme="dark"] .shadow-emerald-900\/15,
    html[data-theme="dark"] .shadow-indigo-200 { --tw-shadow-color: rgb(0 0 0 / .5) !important; }

    /* ---------- Degradados: se conservan, solo se atenúan ---------- */
    html[data-theme="dark"] .bg-gradient-to-r, html[data-theme="dark"] .bg-gradient-to-br,
    html[data-theme="dark"] .bg-gradient-to-b, html[data-theme="dark"] .bg-gradient-to-tr { filter: brightness(.88) saturate(1.05); }
    html[data-theme="dark"] .page-hero { box-shadow: 0 1rem 2.5rem rgb(0 0 0 / .5); }

    /* ---------- Degradados CLAROS (stops pastel) -> degradados noche ----------
       Tailwind v4 compone los degradados con variables: al redefinirlas,
       paneles como "Juegos educativos" o las tarjetas de reportes se
       vuelven oscuros sin tocar ninguna vista. */
    html[data-theme="dark"] .from-slate-50, html[data-theme="dark"] .from-slate-100 { --tw-gradient-from: #1e293b !important; }
    html[data-theme="dark"] .to-slate-50, html[data-theme="dark"] .to-slate-100 { --tw-gradient-to: #1e293b !important; }
    html[data-theme="dark"] .from-emerald-50 { --tw-gradient-from: #064e3b !important; }
    html[data-theme="dark"] .to-emerald-50 { --tw-gradient-to: #064e3b !important; }
    html[data-theme="dark"] .from-teal-50 { --tw-gradient-from: #134e4a !important; }
    html[data-theme="dark"] .to-teal-50 { --tw-gradient-to: #134e4a !important; }
    html[data-theme="dark"] .from-cyan-50 { --tw-gradient-from: #164e63 !important; }
    html[data-theme="dark"] .to-cyan-50 { --tw-gradient-to: #164e63 !important; }
    html[data-theme="dark"] .from-sky-50 { --tw-gradient-from: #0c4a6e !important; }
    html[data-theme="dark"] .to-sky-50 { --tw-gradient-to: #0c4a6e !important; }
    html[data-theme="dark"] .from-amber-50 { --tw-gradient-from: #451a03 !important; }
    html[data-theme="dark"] .from-yellow-50, html[data-theme="dark"] .from-yellow-100 { --tw-gradient-from: #422006 !important; }
    html[data-theme="dark"] .to-yellow-50, html[data-theme="dark"] .to-yellow-100 { --tw-gradient-to: #422006 !important; }
    html[data-theme="dark"] .from-orange-50, html[data-theme="dark"] .from-orange-100 { --tw-gradient-from: #431407 !important; }
    html[data-theme="dark"] .to-orange-50, html[data-theme="dark"] .to-orange-100 { --tw-gradient-to: #431407 !important; }
    html[data-theme="dark"] .from-white, html[data-theme="dark"] .via-white, html[data-theme="dark"] .to-white { --tw-gradient-from: #1e293b !important; --tw-gradient-via: #1e293b !important; --tw-gradient-to: #1e293b !important; }

    /* ---------- Hovers que en claro son pasteles ---------- */
    html[data-theme="dark"] .hover\:bg-slate-50:hover, html[data-theme="dark"] .hover\:bg-slate-100:hover { background-color: var(--kd-surface-2) !important; }
    html[data-theme="dark"] .hover\:bg-indigo-50:hover { background-color: rgb(99 102 241 / .25) !important; }
    html[data-theme="dark"] .hover\:bg-violet-100:hover { background-color: rgb(139 92 246 / .25) !important; }
    html[data-theme="dark"] .hover\:bg-emerald-50:hover, html[data-theme="dark"] .hover\:bg-emerald-100:hover { background-color: rgb(16 185 129 / .25) !important; }
    html[data-theme="dark"] .hover\:bg-teal-100:hover { background-color: rgb(20 184 166 / .25) !important; }
    html[data-theme="dark"] .hover\:bg-cyan-100:hover { background-color: rgb(6 182 212 / .25) !important; }
    html[data-theme="dark"] .hover\:bg-amber-100:hover { background-color: rgb(245 158 11 / .25) !important; }
    html[data-theme="dark"] .hover\:bg-orange-100:hover { background-color: rgb(249 115 22 / .25) !important; }
    html[data-theme="dark"] .hover\:bg-rose-50:hover, html[data-theme="dark"] .hover\:bg-rose-100:hover { background-color: rgb(244 63 94 / .25) !important; }
    html[data-theme="dark"] .hover\:bg-red-50:hover, html[data-theme="dark"] .hover\:bg-red-100:hover { background-color: rgb(239 68 68 / .25) !important; }
    html[data-theme="dark"] .hover\:text-indigo-800:hover { color: #c7d2fe !important; }
    html[data-theme="dark"] .hover\:text-slate-800:hover { color: #f1f5f9 !important; }
    html[data-theme="dark"] .hover\:text-emerald-900:hover { color: #6ee7b7 !important; }
    html[data-theme="dark"] .hover\:text-teal-900:hover { color: #5eead4 !important; }
    html[data-theme="dark"] .hover\:border-emerald-300:hover { border-color: rgb(16 185 129 / .5) !important; }
    html[data-theme="dark"] .hover\:border-cyan-300:hover { border-color: rgb(6 182 212 / .5) !important; }
    html[data-theme="dark"] .hover\:border-rose-200:hover, html[data-theme="dark"] .hover\:border-red-300:hover { border-color: rgb(244 63 94 / .5) !important; }
    html[data-theme="dark"] .hover\:text-rose-600:hover { color: #fda4af !important; }

    /* ---------- Estructura Klassio ---------- */
    html[data-theme="dark"] header, html[data-theme="dark"] aside { background-color: var(--kd-surface) !important; border-color: var(--kd-border) !important; }
    html[data-theme="dark"] .surface-card { background-color: var(--kd-surface) !important; color: var(--kd-text) !important; border-color: var(--kd-border) !important; }
    html[data-theme="dark"] .student-activity { color: var(--kd-text) !important; }
    html[data-theme="dark"] .student-activity > h1 { color: #38bdf8 !important; }
    html[data-theme="dark"] .student-activity > section { background-color: var(--kd-surface) !important; color: var(--kd-text) !important; border-color: var(--kd-border) !important; }
    html[data-theme="dark"] .student-activity dt { color: var(--kd-muted) !important; }
    html[data-theme="dark"] .student-activity table, html[data-theme="dark"] .student-activity th, html[data-theme="dark"] .student-activity td { border-color: var(--kd-border) !important; }

    /* ---------- Formularios ---------- */
    html[data-theme="dark"] input, html[data-theme="dark"] select, html[data-theme="dark"] textarea { background-color: #0b1426 !important; color: #f1f5f9 !important; border-color: #475569 !important; }
    html[data-theme="dark"] input::placeholder, html[data-theme="dark"] textarea::placeholder { color: var(--kd-faint) !important; }
    html[data-theme="dark"] .placeholder\:text-slate-400::placeholder { color: var(--kd-faint) !important; }
    html[data-theme="dark"] input:disabled, html[data-theme="dark"] select:disabled, html[data-theme="dark"] textarea:disabled { background-color: var(--kd-surface-2) !important; color: var(--kd-muted) !important; }
    html[data-theme="dark"] select option { background-color: var(--kd-surface) !important; color: var(--kd-text) !important; }
    html[data-theme="dark"] input[type="checkbox"], html[data-theme="dark"] input[type="radio"] { accent-color: var(--klassio-primary); }
    html[data-theme="dark"] input:focus, html[data-theme="dark"] select:focus, html[data-theme="dark"] textarea:focus { border-color: #818cf8 !important; }
    html[data-theme="dark"] :focus-visible { outline: 2px solid #818cf8 !important; outline-offset: 2px; }

    /* ---------- Tablas ---------- */
    html[data-theme="dark"] table { color: var(--kd-text) !important; }
    html[data-theme="dark"] thead th { background-color: var(--kd-surface-2) !important; color: var(--kd-muted) !important; border-color: var(--kd-border) !important; }
    html[data-theme="dark"] tbody td, html[data-theme="dark"] tbody th { border-color: var(--kd-border) !important; }
    html[data-theme="dark"] tbody tr:hover td { background-color: rgb(148 163 184 / .08) !important; }

    /* ---------- Bootstrap 5 ---------- */
    html[data-theme="dark"] .card { background-color: var(--kd-surface) !important; border-color: var(--kd-border) !important; color: var(--kd-text) !important; }
    html[data-theme="dark"] .card-header, html[data-theme="dark"] .card-footer { background-color: var(--kd-surface-2) !important; border-color: var(--kd-border) !important; }
    html[data-theme="dark"] .text-secondary { color: var(--kd-muted) !important; }
    html[data-theme="dark"] .text-primary { color: #a5b4fc !important; }
    html[data-theme="dark"] .text-muted { color: var(--kd-faint) !important; }
    html[data-theme="dark"] .text-bg-light { background-color: var(--kd-surface-2) !important; color: var(--kd-text) !important; }
    html[data-theme="dark"] .text-bg-info { background-color: #0e7490 !important; color: #ecfeff !important; }
    html[data-theme="dark"] .btn-light { background-color: var(--kd-surface-2) !important; border-color: var(--kd-border) !important; color: var(--kd-text) !important; }
    html[data-theme="dark"] .btn-outline-secondary { border-color: #475569 !important; color: var(--kd-text) !important; }
    html[data-theme="dark"] .btn-outline-secondary:hover { background-color: var(--kd-surface-2) !important; border-color: #64748b !important; color: #fff !important; }
    html[data-theme="dark"] .btn-outline-light { border-color: var(--kd-border) !important; color: var(--kd-text) !important; }
    html[data-theme="dark"] .form-control, html[data-theme="dark"] .form-select { background-color: #0b1426 !important; border-color: #475569 !important; color: #f1f5f9 !important; }
    html[data-theme="dark"] .form-control:focus, html[data-theme="dark"] .form-select:focus { border-color: #818cf8 !important; box-shadow: 0 0 0 .25rem rgb(99 102 241 / .25) !important; }
    html[data-theme="dark"] .form-check-input { background-color: #0b1426 !important; border-color: #475569 !important; }
    html[data-theme="dark"] .form-check-input:checked { background-color: var(--klassio-primary) !important; border-color: var(--klassio-primary) !important; }
    html[data-theme="dark"] .input-group-text { background-color: var(--kd-surface-2) !important; border-color: #475569 !important; color: var(--kd-muted) !important; }
    html[data-theme="dark"] .dropdown-menu { background-color: var(--kd-surface) !important; border-color: var(--kd-border) !important; }
    html[data-theme="dark"] .dropdown-item { color: var(--kd-text) !important; }
    html[data-theme="dark"] .dropdown-item:hover { background-color: var(--kd-surface-2) !important; color: #fff !important; }
    html[data-theme="dark"] .modal-content { background-color: var(--kd-surface) !important; border-color: var(--kd-border) !important; color: var(--kd-text) !important; }
    html[data-theme="dark"] .modal-header, html[data-theme="dark"] .modal-footer { border-color: var(--kd-border) !important; }
    html[data-theme="dark"] .list-group-item { background-color: var(--kd-surface) !important; border-color: var(--kd-border) !important; color: var(--kd-text) !important; }
    html[data-theme="dark"] .page-link { background-color: var(--kd-surface) !important; border-color: var(--kd-border) !important; color: var(--kd-text) !important; }
    html[data-theme="dark"] .page-item.active .page-link { background-color: var(--klassio-primary) !important; border-color: var(--klassio-primary) !important; color: #fff !important; }
    html[data-theme="dark"] .alert-success { background-color: rgb(20 83 45 / .5) !important; border-color: rgb(34 197 94 / .4) !important; color: #bbf7d0 !important; }
    html[data-theme="dark"] .alert-danger { background-color: rgb(127 29 29 / .5) !important; border-color: rgb(239 68 68 / .4) !important; color: #fecaca !important; }
    html[data-theme="dark"] .alert-warning { background-color: rgb(120 53 15 / .5) !important; border-color: rgb(245 158 11 / .4) !important; color: #fde68a !important; }
    html[data-theme="dark"] .alert-info { background-color: rgb(12 74 110 / .55) !important; border-color: rgb(14 165 233 / .4) !important; color: #bae6fd !important; }
    html[data-theme="dark"] .alert-primary { background-color: rgb(49 46 129 / .55) !important; border-color: rgb(99 102 241 / .4) !important; color: #c7d2fe !important; }
    html[data-theme="dark"] .alert-secondary { background-color: var(--kd-surface-2) !important; border-color: var(--kd-border) !important; color: var(--kd-text) !important; }
    html[data-theme="dark"] .progress { background-color: var(--kd-surface-2) !important; }
    html[data-theme="dark"] .toast { background-color: var(--kd-surface) !important; border-color: var(--kd-border) !important; color: var(--kd-text) !important; }

    /* ---------- Detalles ---------- */
    html[data-theme="dark"] ::selection { background: rgb(99 91 255 / .5) !important; color: #fff !important; }
    html[data-theme="dark"] ::-webkit-scrollbar { width: 10px; height: 10px; }
    html[data-theme="dark"] ::-webkit-scrollbar-track { background: var(--kd-bg); }
    html[data-theme="dark"] ::-webkit-scrollbar-thumb { background: #334155; border-radius: 8px; }
    html[data-theme="dark"] ::-webkit-scrollbar-thumb:hover { background: #475569; }
    html[data-theme="dark"] code, html[data-theme="dark"] kbd { background-color: var(--kd-surface-2) !important; color: #fcd34d !important; border: 1px solid var(--kd-border); }
    html[data-theme="dark"] pre { background-color: #0b1426 !important; border-color: var(--kd-border) !important; color: var(--kd-text) !important; }
    .klassio-theme-btn { border: 1px solid #cbd5e1; border-radius: .75rem; background: #fff; padding: .5rem .75rem; font-size: .85rem; font-weight: 700; cursor: pointer; white-space: nowrap; transition: background-color .2s ease, transform .15s ease; }
    .klassio-theme-btn:hover { transform: translateY(-1px); }
    html[data-theme="dark"] .klassio-theme-btn { background: var(--kd-surface-2); color: #f1f5f9; border-color: #475569; }
    .klassio-theme-fab { position: fixed; right: 1rem; bottom: 1rem; z-index: 1050; border: 1px solid #cbd5e1; border-radius: 999px; background: #fff; color: #334155; padding: .55rem .9rem; font-size: .85rem; font-weight: 700; cursor: pointer; box-shadow: 0 .5rem 1.25rem rgb(15 23 42 / .25); }
    html[data-theme="dark"] .klassio-theme-fab { background: var(--kd-surface-2); color: #f1f5f9; border-color: #475569; }
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
    // Interruptor negro/blanco. Se inyecta junto a "Cerrar sesión" y,
    // si la página no tiene ese formulario (login/registro), aparece
    // como botón flotante. La elección se guarda y se sincroniza
    // entre pestañas. Sin Vite.
    (function () {
        function isDark() { return document.documentElement.getAttribute('data-theme') === 'dark'; }
        function label() { return isDark() ? '☀️ Claro' : '🌙 Negro'; }
        function paint() {
            document.querySelectorAll('.klassio-theme-btn, .klassio-theme-fab').forEach(function (b) { b.textContent = label(); });
        }
        function setDark(dark) {
            if (dark) {
                document.documentElement.setAttribute('data-theme', 'dark');
            } else {
                document.documentElement.removeAttribute('data-theme');
            }
            try { localStorage.setItem('klassio-theme', dark ? 'dark' : 'light'); } catch (e) {}
            paint();
        }
        window.__klassioTheme = { isDark: isDark, setDark: setDark, paint: paint };
        function addFab() {
            if (document.querySelector('.klassio-theme-fab')) return;
            var fab = document.createElement('button');
            fab.type = 'button';
            fab.className = 'klassio-theme-fab';
            fab.textContent = label();
            fab.setAttribute('aria-label', 'Cambiar entre modo claro y oscuro');
            fab.addEventListener('click', function () { setDark(!isDark()); });
            document.body.appendChild(fab);
        }
        function anyVisibleToggle() {
            return Array.from(
                document.querySelectorAll('.klassio-theme-btn, #theme-btn')
            ).some(function (b) { return b.getClientRects().length > 0; });
        }
        function init() {
            // Los formularios con data-theme-toggle="off" no reciben botón
            // (p. ej. la barra superior del admin, que ya lo tiene en el sidebar).
            document.querySelectorAll('form[action*="logout"]:not([data-theme-toggle="off"])').forEach(function (form) {
                if (form.parentElement && form.parentElement.querySelector('.klassio-theme-btn')) return;
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'klassio-theme-btn';
                btn.textContent = label();
                btn.addEventListener('click', function () { setDark(!isDark()); });
                btn.style.marginRight = '0.5rem';
                form.parentElement.insertBefore(btn, form);
                form.style.display = 'inline-block';
            });
            // Sin interruptor visible (login/registro o sidebar colapsado en móvil):
            // botón flotante para no perder el acceso al modo negro.
            if (!anyVisibleToggle()) {
                addFab();
            }
            paint();
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
        window.addEventListener('storage', function (e) {
            if (e.key === 'klassio-theme') {
                if (e.newValue === 'dark') {
                    document.documentElement.setAttribute('data-theme', 'dark');
                } else {
                    document.documentElement.removeAttribute('data-theme');
                }
                paint();
            }
        });
    })();
</script>
