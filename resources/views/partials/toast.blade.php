{{-- ============================================================
  KlassioToast — notificaciones en la esquina derecha que
  aparecen y se quitan solas. Estilo de la captura:
  tarjeta redondeada con icono, título y mensaje.

  Uso:
    @include('partials.toast')
    KlassioToast.success('Título', 'Mensaje');
    KlassioToast.error('Título', 'Mensaje');
    KlassioToast.info('Título', 'Mensaje');

  Además convierte solo en toasts los bloques con [data-toast],
  [role="alert"] o [role="status"] (mensajes flash del servidor).
  ============================================================ --}}
<div id="klassio-toasts" aria-live="polite" aria-atomic="false"></div>
<style>
    #klassio-toasts {
        position: fixed; top: 1rem; right: 1rem; z-index: 1080;
        display: flex; flex-direction: column; gap: .6rem;
        width: min(22rem, calc(100vw - 2rem));
        pointer-events: none;
    }
    #klassio-toasts > * { pointer-events: auto; }
    .klassio-toast {
        display: flex; gap: .7rem; align-items: flex-start;
        background: #f0fdf4; border: 1px solid #a7f3d0; color: #065f46;
        border-radius: 1rem; padding: .9rem 1rem;
        box-shadow: 0 .75rem 2rem rgb(15 23 42 / .18);
        cursor: pointer; overflow: hidden; position: relative;
        animation: klassio-toast-in .3s ease;
    }
    .klassio-toast.error { background: #fef2f2; border-color: #fecaca; color: #991b1b; }
    .klassio-toast.info { background: #eff6ff; border-color: #bfdbfe; color: #1e40af; }
    .klassio-toast-body { min-width: 0; }
    .klassio-toast-title { font-weight: 800; font-size: .95rem; }
    .klassio-toast-msg { font-size: .85rem; margin-top: .15rem; line-height: 1.45; overflow-wrap: anywhere; }
    .klassio-toast-icon {
        flex-shrink: 0; width: 1.9rem; height: 1.9rem; border-radius: 999px;
        display: grid; place-items: center; font-weight: 900;
        background: rgb(16 185 129 / .15);
    }
    .klassio-toast.error .klassio-toast-icon { background: rgb(239 68 68 / .15); }
    .klassio-toast.info .klassio-toast-icon { background: rgb(59 130 246 / .15); }
    .klassio-toast.out { opacity: 0; transform: translateX(12px); transition: opacity .3s ease, transform .3s ease; }
    @keyframes klassio-toast-in {
        from { opacity: 0; transform: translateX(24px); }
        to { opacity: 1; transform: none; }
    }
    html[data-theme="dark"] .klassio-toast { background: #052e16; border-color: rgb(34 197 94 / .4); color: #bbf7d0; }
    html[data-theme="dark"] .klassio-toast.error { background: #450a0a; border-color: rgb(239 68 68 / .4); color: #fecaca; }
    html[data-theme="dark"] .klassio-toast.info { background: #172554; border-color: rgb(59 130 246 / .4); color: #bfdbfe; }
</style>
<script>
    (function () {
        var BOX_ID = 'klassio-toasts';
        var LIFE_MS = 4500;

        function box() {
            var b = document.getElementById(BOX_ID);
            if (!b) {
                b = document.createElement('div');
                b.id = BOX_ID;
                b.setAttribute('aria-live', 'polite');
                document.body.appendChild(b);
            }
            return b;
        }

        function el(tag, cls, text) {
            var n = document.createElement(tag);
            if (cls) n.className = cls;
            if (text !== undefined && text !== null) n.textContent = text;
            return n;
        }

        function show(type, title, message, timeout) {
            var card = el('div', 'klassio-toast' + (type && type !== 'success' ? ' ' + type : ''));
            card.setAttribute('role', 'status');
            var icon = el('div', 'klassio-toast-icon', type === 'error' ? '✕' : (type === 'info' ? 'i' : '✓'));
            var body = el('div', 'klassio-toast-body');
            if (title) body.appendChild(el('div', 'klassio-toast-title', title));
            if (message) body.appendChild(el('div', 'klassio-toast-msg', message));
            card.appendChild(icon);
            card.appendChild(body);

            var gone = false;
            function dismiss() {
                if (gone) return;
                gone = true;
                card.classList.add('out');
                setTimeout(function () { card.remove(); }, 320);
            }
            card.addEventListener('click', dismiss);
            box().appendChild(card);
            setTimeout(dismiss, timeout || LIFE_MS);
            return dismiss;
        }

        // Convierte un bloque estático (p. ej. flash del servidor) en toast:
        // lo mueve a la esquina, lo anima y lo quita solo.
        function toastify(node, timeout) {
            if (!node || node.dataset.klassioToastified) return;
            node.dataset.klassioToastified = '1';
            node.classList.add('klassio-toast');
            node.addEventListener('click', function () {
                node.classList.add('out');
                setTimeout(function () { node.remove(); }, 320);
            });
            box().appendChild(node);
            // Re-dispara la animación de entrada al moverlo.
            void node.offsetWidth;
            setTimeout(function () {
                node.classList.add('out');
                setTimeout(function () { node.remove(); }, 320);
            }, timeout || LIFE_MS);
        }

        function autoConvert() {
            // Avisos del servidor (tienen esos colores en todo el proyecto)
            // + cualquier bloque marcado con [data-toast] o rol de alerta.
            var sel = '[data-toast], [role="alert"], [role="status"],' +
                'div.border-emerald-200.bg-emerald-50,' +
                'div.border-rose-200.bg-rose-50,' +
                'div.border-red-200.bg-red-50,' +
                'div.border-red-200.bg-rose-50';
            var nodes = document.querySelectorAll(sel);
            for (var i = 0; i < nodes.length; i++) {
                // Los que ya viven en la caja no se tocan.
                if (nodes[i].parentElement && nodes[i].parentElement.id === BOX_ID) continue;
                var t = parseInt(nodes[i].getAttribute('data-toast-time') || '', 10);
                toastify(nodes[i], isNaN(t) ? undefined : t);
            }
        }

        window.KlassioToast = {
            show: show,
            success: function (t, m, ms) { return show('success', t, m, ms); },
            error: function (t, m, ms) { return show('error', t, m, ms); },
            info: function (t, m, ms) { return show('info', t, m, ms); },
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', autoConvert);
        } else {
            autoConvert();
        }
    })();
</script>
