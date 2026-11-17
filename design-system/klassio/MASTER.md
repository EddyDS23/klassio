# Design System Master File — Klassio

> **LOGIC:** When building a specific page, first check `design-system/klassio/pages/[page-name].md`.
> If that file exists, its rules **override** this Master file.
> If not, strictly follow the rules below.

---

**Project:** Klassio
**Generated:** 2026-11-15 (refactor UI área docente)
**Source of truth for tokens:** `resources/css/app.css` (`@theme`)

---

## Global Rules

### Tipografía

- **Sans (body):** `Inter` — `--font-sans`
- **Display (títulos):** `Poppins` — `--font-display`
- **Pesos:** `font-bold` para todo lo destacado. **Prohibido `font-black`.**
- Jerarquía estándar: hero `text-2xl sm:text-3xl font-bold tracking-tight`, sección `text-lg font-bold`, card título `text-sm font-bold`, label `text-xs font-bold uppercase tracking-widest`, dato pequeño `text-[10px]/text-xs font-bold uppercase tracking-wide text-slate-400`.

### Colores — Área docente (teacher/admin)

Paleta permitida: **esmeralda + slate** (y neutros `bosque-*` donde el layout ya los usa).

| Rol | Clases | Uso |
|-----|--------|-----|
| Hero / primario | `bg-emerald-700 text-white`, hover `bg-emerald-800` | Botones de acción principal, hero de página |
| Hero label | `text-emerald-200` | Eyebrow sobre el hero |
| Éxito / positivo | `bg-emerald-100 text-emerald-700` | Pills "Completó", "Esperando jugadores", chips posición 4+ |
| Neutro | `bg-slate-100 text-slate-600/700` | Pills informativas, contadores, posiciones secundarias |
| Advertencia / en curso | `bg-amber-100 text-amber-800` | **Solo** advertencias y estados "En partida" / "En progreso" |
| Destructivo | `bg-red-600 text-white` (confirmación), `border-red-200 text-red-700 bg-white` (trigger), `bg-red-50 border-red-200` (panel) | **Solo** acciones destructivas y errores |
| Oro / plata / bronce | `bg-amber-100 text-amber-700 ring-amber-200` / `bg-slate-100 text-slate-700 ring-slate-200` / `bg-orange-100 text-orange-700 ring-orange-200` | Chips de posición 1/2/3 en rankings y resultados |

**Prohibido en área docente:** `teal`, `cyan`, `blue`, `violet`, `fuchsia`, `purple`, `indigo`, `rose` y `gradient-to-*`.
**Excepciones documentadas:** brand violeta `klassio` (`#635BFF`) solo en layouts compartidos/área alumno; `gradient` en CSS de partials de assets.

### Hero de página (patrón estándar)

```blade
<section class="mb-6 rounded-2xl bg-emerald-700 p-5 text-white shadow-sm sm:p-6">
    {{-- flex flex-col gap-5 md:flex-row md:items-center md:justify-between --}}
    <p class="text-xs font-bold uppercase tracking-widest text-emerald-200">…</p>
    <h2 class="mt-1 text-2xl font-bold tracking-tight sm:text-3xl">…</h2>
    {{-- tile de ícono SOLO md+: h-16 w-16 bg-emerald-600 ring-1 ring-emerald-500, svg h-8 w-8 --}}
</section>
```

Íconos por tipo de juego (Heroicons inline, `stroke-width="1.5"` o `2`): kahoot=bolt, crossword=crucigrama (path largo), word_search=lupa, matching=link, roulette=arrow-path.

### Iconografía

- **Cero emojis.** Todos los íconos son SVG Heroicons inline con `aria-hidden="true"`.
- Sin `✓`, `★` ni caracteres decorativos: usar SVG (check, trophy).
- Un solo estilo por jerarquía (outline, stroke consistente).

### Tarjetas

```blade
<section class="mb-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
```

- Separadores internos de listas: `divide-y divide-slate-100`.
- Empty state: `rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600` con `role="status"`.

### Botones

- Primario: `rounded-xl bg-emerald-700 px-6 py-3 text-sm font-bold text-white shadow-sm hover:bg-emerald-800 focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2` (+ `disabled:cursor-not-allowed disabled:opacity-50`).
- Secundario: `rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-slate-900`.
- Destructivo (trigger, 2 pasos): `border-red-200 bg-white text-red-700 hover:bg-red-50`.
- Touch target mínimo: `min-h-11` en botones copiables/ícono; botones de texto `py-2.5/3` ya cumplen.
- Botón "Volver": `inline-flex self-start items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5` con flecha SVG hacia la izquierda.

### Confirmación destructiva (2 pasos)

Atributos fijos: `data-confirm-toggle` (con `aria-expanded` y `aria-controls`), `data-confirm-panel` (inicia `hidden`), `data-confirm-cancel`. **Prohibido `confirm()` y `alert()` nativos.** Feedback inline con `role="status" aria-live="polite"`.

### Estados de sala (pills, consistentes en dashboard + lobby)

| Estado | Clases | Texto |
|--------|--------|-------|
| waiting / starting | `bg-emerald-100 text-emerald-700` | "En espera" / "Preparando" / "Esperando jugadores" |
| playing | `bg-amber-100 text-amber-800` | "En partida" |
| finished | `bg-emerald-100 text-emerald-700` | "Finalizada" |
| cancelled | `bg-red-100 text-red-700` | "Cancelada" |

(JS del lobby cambia las clases dinámicamente con este mismo mapa.)

### Formularios

- Input estándar: `rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm font-bold text-slate-800 focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100`.
- **Fix de overflow en flex:** inputs dentro de flex/grid necesitan `min-w-0 w-0 flex-1` — `w-0` anula la contribución min-content que desborda contenedores angostos (375px).
- Labels: `text-[11px] font-bold uppercase tracking-wide text-slate-400`.

### Layout / estructura

- Área teacher/admin: `@extends('teacher.layouts.app')`, contenido en `@section('content')`, JS en `@push('scripts')` (el layout tiene `@stack('scripts')`).
- Ancho de contenido: `mx-auto max-w-5xl` (multijugador) o el ancho de la página según el patrón existente.
- Listados: **tarjetas en móvil** (`grid gap-3 sm:hidden`) + **tabla en desktop** (`hidden sm:block` con wrapper `overflow-x-auto rounded-2xl border border-slate-200`). Padding de celdas `px-4 py-3.5`.
- Código de sala centrado en móvil: `flex-col items-center text-center` → `sm:flex-row sm:text-left`, código `font-mono font-bold tracking-[0.25em] text-3xl+`.

### Spacing

- Ritmo 4/8: gaps `gap-2/3`, padding de cards `p-4 sm:p-6`, margen entre secciones `mb-4/6`.
- Sombras sutiles: `shadow-sm` en cards y botones.

---

## Anti-Patterns (Do NOT Use)

- ❌ Emojis o caracteres decorativos como íconos → SVG Heroicons con `aria-hidden="true"`.
- ❌ `font-black` → siempre `font-bold`.
- ❌ `confirm()` / `alert()` nativos → confirmación en 2 pasos con `data-confirm-*`.
- ❌ Colores fuera de la paleta en área docente (teal/cyan/blue/violet/fuchsia/purple/indigo/rose, gradientes).
- ❌ `min-w-[680px]` en tablas → patrón tarjetas-móvil + tabla-desktop.
- ❌ Inputs flex sin `min-w-0 w-0` → desborde a 375px.
- ❌ `window.alert`/spinners de texto plano sin `role="status"` para feedback async.

---

## Pre-Delivery Checklist

- [ ] Cero emojis; íconos SVG consistentes con `aria-hidden="true"`.
- [ ] Solo esmeralda/slate (+ ámbar advertencia/estado, rojo destructivo, oro/plata/bronce posiciones).
- [ ] `font-bold`, nunca `font-black`.
- [ ] Verificado a 375px y 1280px: cero overflow horizontal (chequeo Playwright).
- [ ] Render tinker limpio en todos los estados de la vista.
- [ ] Suite de tests del área afectada en verde.
- [ ] `vendor/bin/pint --dirty --format agent` passed.
- [ ] Feedback async con `role="status" aria-live="polite"`; confirmaciones destructivas en 2 pasos.
- [ ] Botones primarios `disabled` visiblemente apagados con motivo explicado (hint bajo el botón).
