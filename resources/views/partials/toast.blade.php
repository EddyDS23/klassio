{{-- ============================================================
  KlassioToast — notificaciones en la esquina derecha que
  aparecen y se quitan solas. Estilo de la captura:
  tarjeta redondeada con icono, título y mensaje.

  Uso:
    @include('partials.toast')
    KlassioToast.success('Título', 'Mensaje');
    KlassioToast.error('Título', 'Mensaje');
    KlassioToast.info('Título', 'Mensaje');

  Además convierte en toasts los mensajes flash marcados con [data-toast].
  Los roles ARIA de estado/alerta no implican una notificación flotante.
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
        overflow: hidden; position: relative;
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
    .klassio-toast-dismiss {
        flex-shrink: 0; margin-left: auto; border: 0; border-radius: .4rem;
        background: transparent; color: inherit; padding: .2rem .4rem;
        font: inherit; font-weight: 700; cursor: pointer;
    }
    .klassio-toast-dismiss:focus-visible { outline: 2px solid currentColor; outline-offset: 2px; }
    html[data-theme="dark"] .klassio-toast { background: #142b24; border-color: #27634e; color: #d1fae5; }
    html[data-theme="dark"] .klassio-toast.error { background: #351b20; border-color: #7f343c; color: #fee2e2; }
    html[data-theme="dark"] .klassio-toast.info { background: #172a45; border-color: #315584; color: #dbeafe; }
    @keyframes klassio-toast-in {
        from { opacity: 0; transform: translateX(24px); }
        to { opacity: 1; transform: none; }
    }
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

        function addDismissButton(card, dismiss) {
            if (card.querySelector(':scope > .klassio-toast-dismiss')) return;
            var button = el('button', 'klassio-toast-dismiss', '×');
            button.type = 'button';
            button.setAttribute('aria-label', 'Descartar notificación');
            button.addEventListener('click', function (event) {
                event.stopPropagation();
                dismiss();
            });
            card.appendChild(button);
        }

        function show(type, title, message, timeout) {
            var card = el('div', 'klassio-toast' + (type && type !== 'success' ? ' ' + type : ''));
            card.setAttribute('role', type === 'error' ? 'alert' : 'status');
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
            addDismissButton(card, dismiss);
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
            var type = node.getAttribute('data-toast');
            var isError = type === 'error' || node.getAttribute('role') === 'alert' ||
                node.classList.contains('border-rose-200') || node.classList.contains('border-red-200') ||
                node.classList.contains('bg-rose-50') || node.classList.contains('bg-red-50');
            var isInfo = type === 'info' || node.classList.contains('border-blue-200') || node.classList.contains('bg-blue-50');
            if (isError) node.classList.add('error');
            else if (isInfo) node.classList.add('info');
            function dismiss() {
                node.classList.add('out');
                setTimeout(function () { node.remove(); }, 320);
            }
            addDismissButton(node, dismiss);
            box().appendChild(node);
            // Re-dispara la animación de entrada al moverlo.
            void node.offsetWidth;
            setTimeout(function () {
                node.classList.add('out');
                setTimeout(function () { node.remove(); }, 320);
            }, timeout || LIFE_MS);
        }

        function autoConvert() {
            // Los juegos también usan role="status" en resultados aún ocultos.
            // Solo los mensajes marcados explícitamente deben moverse a la caja.
            var nodes = document.querySelectorAll('[data-toast]');
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
