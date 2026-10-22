{{-- ============================================================
  Mobile Navigation Drawer (Hamburger Menu)
  ============================================================ --}}
@props([
    'navItems' => [],
    'userName' => '',
    'userRole' => '',
    'logoutUrl' => '/logout',
    'avatarLetter' => 'U',
    'avatarBg' => 'bg-indigo-600',
    'brandTitle' => 'Klassio',
    'brandSubtitle' => 'Panel',
    'brandIcon' => 'K',
    'brandIconBg' => 'bg-emerald-600',
])

<div id="mobile-nav-drawer" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-label="Menú de navegación">
    {{-- Overlay backdrop - light in light mode, darker in dark mode --}}
    <div class="fixed inset-0 bg-black/30 dark:bg-black/70 transition-opacity duration-300" id="mobile-nav-backdrop" aria-hidden="true"></div>

    {{-- Drawer panel --}}
    <aside class="fixed inset-y-0 left-0 z-50 w-full max-w-sm transform transition-transform duration-300 ease-out translate-x-full md:hidden" id="mobile-nav-panel" role="document">
        <div class="flex flex-col h-full bg-white dark:bg-slate-900 shadow-xl overflow-x-hidden">
            {{-- Header --}}
            <div class="flex items-center justify-between gap-3 border-b border-slate-200 dark:border-slate-700 px-4 py-4">
                <a href="{{ url('/') }}" class="flex items-center gap-3" aria-label="{{ $brandTitle }} - Inicio">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl {{ $brandIconBg }} text-xl font-black text-white shadow-lg">
                        {{ $brandIcon }}
                    </div>
                    <div>
                        <p class="text-xl font-black tracking-tight text-slate-900 dark:text-white">
                            {{ $brandTitle }}
                        </p>
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400">
                            {{ $brandSubtitle }}
                        </p>
                    </div>
                </a>

                <button
                    type="button"
                    class="flex h-10 w-10 items-center justify-center rounded-xl text-slate-500 dark:text-slate-400 transition hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-700 dark:hover:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    id="mobile-nav-close"
                    aria-label="Cerrar menú"
                >
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

{{-- Navigation items --}}
            <nav class="flex-1 overflow-y-auto px-3 py-4" aria-label="Navegación principal">
                <ul class="space-y-1.5" role="list">
                    @foreach ($navItems as $item)
                        @php
                            $isExact = $item['exact'] ?? false;
                            $routeMatches = $isExact
                                ? request()->routeIs($item['pattern'])
                                : request()->routeIs($item['pattern'] . '*');
                            $isActive = $routeMatches;
                        @endphp
                        <li>
                            <a href="{{ route($item['route']) }}{{ ($item['fragment'] ?? '') ? '#' . ($item['fragment'] ?? '') : '' }}"
                               class="flex items-center gap-3 rounded-xl px-4 py-3.5 text-sm font-bold transition
                                         {{ $isActive ? 'bg-slate-100 dark:bg-slate-800 text-indigo-600 dark:text-indigo-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                               aria-current="{{ $isActive ? 'page' : 'false' }}"
                               data-fragment="{{ $item['fragment'] ?? '' }}">
                                <span class="text-lg shrink-0" aria-hidden="true">{{ $item['icon'] }}</span>
                                <span class="truncate">{{ $item['label'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>

            {{-- User info and logout --}}
            <div class="border-t border-slate-200 dark:border-slate-700 px-4 py-4">
                <div class="mb-3 flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $avatarBg }} font-bold text-white">
                        {{ $avatarLetter }}
                    </div>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-bold text-slate-900 dark:text-white">{{ $userName }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $userRole }}</p>
                    </div>
                </div>

                <form action="{{ $logoutUrl }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="flex w-full items-center gap-3 rounded-xl bg-red-50 dark:bg-red-900/20 px-4 py-3 text-sm font-bold text-red-600 dark:text-red-400 transition hover:bg-red-100 dark:hover:bg-red-900/30 hover:text-red-700 dark:hover:text-red-300">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </div>
    </aside>
</div>

{{-- JavaScript para el menú móvil --}}
<script>
(function () {
    var drawer = document.getElementById('mobile-nav-drawer');
    var panel = document.getElementById('mobile-nav-panel');
    var backdropEl = document.getElementById('mobile-nav-backdrop');
    var openBtn = document.getElementById('mobile-nav-open');
    var closeBtn = document.getElementById('mobile-nav-close');

    if (!drawer || !panel || !backdropEl || !openBtn || !closeBtn) return;

    var isOpen = false;
    var lastFocusedElement = null;
    var closeTimer = null;
    var trapFocusHandler = null;

    function isMobile() {
        return window.innerWidth < 768;
    }

    function trapFocus(element) {
        var focusableElements = element.querySelectorAll(
            'a[href], button, textarea, input, select, [tabindex]:not([tabindex="-1"])'
        );
        var firstElement = focusableElements[0];
        var lastElement = focusableElements[focusableElements.length - 1];

        trapFocusHandler = function handleTab(e) {
            if (e.key !== 'Tab') return;

            if (e.shiftKey) {
                if (document.activeElement === firstElement) {
                    e.preventDefault();
                    lastElement.focus();
                }
            } else {
                if (document.activeElement === lastElement) {
                    e.preventDefault();
                    firstElement.focus();
                }
            }
        };
        element.addEventListener('keydown', trapFocusHandler);
    }

    function removeTrapFocus(element) {
        if (trapFocusHandler) {
            element.removeEventListener('keydown', trapFocusHandler);
            trapFocusHandler = null;
        }
    }

    function open() {
        // El menú es exclusivo de teléfonos (< 768 px)
        if (!isMobile() || isOpen) return;
        isOpen = true;
        lastFocusedElement = document.activeElement;

        if (closeTimer) {
            clearTimeout(closeTimer);
            closeTimer = null;
        }

        drawer.classList.remove('hidden');
        // Forzar reflow para la animación
        void drawer.offsetWidth;
        panel.classList.remove('translate-x-full');
        document.body.style.overflow = 'hidden';
        openBtn.setAttribute('aria-expanded', 'true');

        // Foco en el botón de cerrar para accesibilidad
        setTimeout(function () {
            closeBtn.focus();
        }, 100);

        trapFocus(panel);
    }

    function close() {
        if (!isOpen) return;
        isOpen = false;
        panel.classList.add('translate-x-full');
        document.body.style.overflow = '';
        openBtn.setAttribute('aria-expanded', 'false');
        removeTrapFocus(panel);

        if (closeTimer) {
            clearTimeout(closeTimer);
        }
        closeTimer = setTimeout(function () {
            drawer.classList.add('hidden');
            closeTimer = null;
            if (lastFocusedElement) {
                lastFocusedElement.focus();
            }
        }, 300);
    }

    // Event listeners
    openBtn.addEventListener('click', open);
    closeBtn.addEventListener('click', close);
    backdropEl.addEventListener('click', close);

    // Cerrar al navegar
    panel.querySelectorAll('a[href]').forEach(function (link) {
        link.addEventListener('click', close);
    });

    // Cerrar con Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && isOpen) {
            close();
        }
    });

    // Cerrar al redimensionar a tablet/desktop
    var resizeTimer;
    window.addEventListener('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            if (window.innerWidth >= 768 && isOpen) {
                close();
            }
        }, 100);
    });

    // Actualizar estado activo basado en hash (para enlaces con fragment)
    function updateActiveByHash() {
        var hash = window.location.hash.slice(1); // quitar el #
        panel.querySelectorAll('a[href*="#"]').forEach(function (link) {
            var href = link.getAttribute('href');
            var hashMatch = href.match(/#(.+)$/);
            if (hashMatch) {
                var linkFragment = hashMatch[1];
                var isActive = hash === linkFragment;
                if (isActive) {
                    link.classList.add('bg-slate-100', 'dark:bg-slate-800', 'text-indigo-600', 'dark:text-indigo-400');
                    link.classList.remove('text-slate-600', 'dark:text-slate-400', 'hover:bg-slate-100', 'dark:hover:bg-slate-800');
                    link.setAttribute('aria-current', 'page');
                } else {
                    link.classList.remove('bg-slate-100', 'dark:bg-slate-800', 'text-indigo-600', 'dark:text-indigo-400');
                    link.classList.add('text-slate-600', 'dark:text-slate-400', 'hover:bg-slate-100', 'dark:hover:bg-slate-800');
                    link.setAttribute('aria-current', 'false');
                }
            }
        });
    }

    // Actualizar al cargar y al cambiar hash
    updateActiveByHash();
    window.addEventListener('hashchange', updateActiveByHash);
})();
</script>