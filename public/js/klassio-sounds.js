/* ============================================================
 * KlassioSounds — música por juego + efectos de acción.
 *
 * Música (loop, empieza con el primer toque del jugador):
 *   wordsearch -> /sounds/sopadeletra.wav
 *   matching   -> /sounds/conectalospuntos.wav
 *   crossword  -> /sounds/crucigrama.wav
 *   kahoot     -> /sounds/kahoot.wav
 *
 * Efectos:
 *   click      -> /sounds/clik.wav            (toques y botones)
 *   correcto   -> /sounds/correcto.wav        (respuesta correcta)
 *   error      -> /sounds/error.wav           (respuesta incorrecta)
 *   terminado  -> /sounds/juegoterminado.wav  (terminó justo al acabarse el tiempo)
 *   completado -> /sounds/juegocompletado.wav (terminó antes de tiempo)
 *   expirado   -> /sounds/juegoexpirado.wav   (no terminó la actividad)
 *
 * Uso en cada juego:
 *   <script src="/js/klassio-sounds.js"></script>
 *   <script>KlassioSounds.setup('wordsearch');</script>
 *   KlassioSounds.click() / .correcto() / .error() /
 *   .terminado() / .completado() / .expirado()
 *
 * El mute se guarda en localStorage ('klassio-sound').
 * ============================================================ */
(function () {
    'use strict';

    var MUSIC = {
        wordsearch: '/sounds/sopadeletra.mp3',
        matching: '/sounds/conectalospuntos.mp3',
        crossword: '/sounds/crucigrama.mp3',
        kahoot: '/sounds/kahoot.mp3',
    };

    var SFX = {
        click: '/sounds/clik.mp3',
        correcto: '/sounds/correcto.mp3',
        error: '/sounds/error.mp3',
        terminado: '/sounds/juegoterminado.mp3',
        completado: '/sounds/juegocompletado.mp3',
        expirado: '/sounds/juegoexpirado.mp3',
    };

    var MUSIC_VOLUME = 0.3;

    var muted = false;
    try { muted = localStorage.getItem('klassio-sound') === 'off'; } catch (e) {}

    var game = null;
    var music = null;
    var musicStarted = false;
    var sfxCache = {};
    var ended = false;

    function paintButtons() {
        var label = muted ? '🔇 Silencio' : '🔊 Sonido';
        var btns = document.querySelectorAll('[data-sound-toggle]');
        for (var i = 0; i < btns.length; i++) btns[i].textContent = label;
    }

    function ensureStyle() {
        if (document.getElementById('klassio-sounds-style')) return;
        var st = document.createElement('style');
        st.id = 'klassio-sounds-style';
        st.textContent =
            '.klassio-sound-btn{border:1px solid #cbd5e1;border-radius:.6rem;background:#fff;color:#475569;' +
            'padding:.6rem 1rem;font-weight:700;cursor:pointer;margin-left:.5rem;white-space:nowrap;}' +
            'html[data-theme="dark"] .klassio-sound-btn{background:#0f172a;color:#f1f5f9;border-color:#475569;}' +
            '.klassio-sound-fab{position:fixed;right:1rem;bottom:4.5rem;z-index:1050;border:1px solid #cbd5e1;' +
            'border-radius:999px;background:#fff;color:#475569;padding:.55rem .9rem;font-size:.85rem;font-weight:700;' +
            'cursor:pointer;box-shadow:0 .5rem 1.25rem rgb(15 23 42 / .25);}' +
            'html[data-theme="dark"] .klassio-sound-fab{background:#0f172a;color:#f1f5f9;border-color:#475569;}';
        document.head.appendChild(st);
    }

    function addButton() {
        ensureStyle();
        if (document.querySelector('[data-sound-toggle]')) { paintButtons(); return; }
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.setAttribute('data-sound-toggle', '1');
        btn.setAttribute('aria-label', 'Activar o silenciar sonido');
        // Sin listener directo: el delegado global de [data-sound-toggle]
        // lo atiende (si hubiera dos, se cancelarían entre sí).
        var anchor = document.getElementById('theme-btn') || document.querySelector('.klassio-theme-btn');
        if (anchor && anchor.parentElement) {
            btn.className = 'klassio-sound-btn';
            anchor.parentElement.insertBefore(btn, anchor.nextSibling);
        } else {
            btn.className = 'klassio-sound-fab';
            document.body.appendChild(btn);
        }
        paintButtons();
    }

    // Precarga todo al arrancar el juego para que suene al instante.
    function preloadAll() {
        try {
            Object.keys(SFX).forEach(function (k) {
                var a = new Audio(SFX[k]);
                a.preload = 'auto';
                try { a.load(); } catch (e) {}
                sfxCache[k] = a;
            });
        } catch (e) {}
    }

    function ensureMusic() {
        if (!game || !MUSIC[game]) return null;
        if (!music) {
            music = new Audio(MUSIC[game]);
            music.loop = true;
            music.preload = 'auto';
            music.volume = MUSIC_VOLUME;
        }
        music.muted = muted;
        return music;
    }

    function playMusic() {
        if (musicStarted || muted) return;
        var m = ensureMusic();
        if (!m) return;
        musicStarted = true;
        try {
            var p = m.play();
            if (p && p.catch) p.catch(function () { musicStarted = false; });
        } catch (e) { musicStarted = false; }
    }

    function stopMusic() {
        musicStarted = false;
        if (music) { try { music.pause(); music.currentTime = 0; } catch (e) {} }
    }

    function sfx(name) {
        if (muted || !SFX[name]) return;
        try {
            var a = sfxCache[name];
            if (!a) {
                a = new Audio(SFX[name]);
                a.preload = 'auto';
                sfxCache[name] = a;
            } else {
                try { a.pause(); a.currentTime = 0; } catch (e) {}
            }
            a.muted = false;
            var p = a.play();
            if (p && p.catch) p.catch(function () {});
        } catch (e) {}
    }

    // Final del juego: frena la música y suena el cierre (una sola vez).
    function endWith(kind) {
        if (ended) return;
        ended = true;
        stopMusic();
        sfx(kind);
    }

    var api = {
        setup: function (gameName) {
            game = gameName;
            ensureMusic();
            preloadAll();
            addButton();
            // Si el alumno viene de "Iniciar / Intentar de nuevo" (bandera que
            // deja la página anterior), intentar sonar de inmediato. Si el
            // navegador lo bloquea, el primer toque lo arranca igual.
            var wantAuto = false;
            try {
                wantAuto = sessionStorage.getItem('klassio-autoplay') === game;
                sessionStorage.removeItem('klassio-autoplay');
            } catch (e) {}
            if (wantAuto && !muted) {
                var m = ensureMusic();
                if (m) {
                    try {
                        var p = m.play();
                        if (p && p.then) {
                            p.then(function () { musicStarted = true; }, function () { musicStarted = false; });
                        } else {
                            musicStarted = true;
                        }
                    } catch (e) { musicStarted = false; }
                }
            }
        },
        // Marca intención de sonar (la lee setup() en la página del juego).
        prime: function (gameName) {
            try { sessionStorage.setItem('klassio-autoplay', gameName); } catch (e) {}
        },
        isMuted: function () { return muted; },
        setMuted: function (m) {
            muted = !!m;
            try { localStorage.setItem('klassio-sound', muted ? 'off' : 'on'); } catch (e) {}
            if (music) music.muted = muted;
            if (!muted && musicStarted && music && music.paused) {
                try { var p = music.play(); if (p && p.catch) p.catch(function () {}); } catch (e) {}
            }
            paintButtons();
        },
        toggle: function () { api.setMuted(!muted); return !muted; },
        // Diagnóstico (consola): KlassioSounds.state()
        state: function () {
            return {
                game: game, muted: muted, musicStarted: musicStarted,
                musicPlaying: !!(music && !music.paused && !music.ended),
            };
        },
        startMusic: playMusic,
        stopMusic: stopMusic,
        click: function () { sfx('click'); },
        correcto: function () { sfx('correcto'); },
        error: function () { sfx('error'); },
        terminado: function () { endWith('terminado'); },
        completado: function () { endWith('completado'); },
        expirado: function () { endWith('expirado'); },
    };

    // La música arranca con el primer gesto (lo exige el navegador).
    document.addEventListener('pointerdown', function () { playMusic(); }, { capture: true });

    document.addEventListener('click', function (e) {
        var t = e.target && e.target.closest ? e.target.closest('[data-sound-toggle]') : null;
        if (t) api.toggle();
    });

    window.KlassioSounds = api;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', paintButtons);
    } else {
        paintButtons();
    }
})();
