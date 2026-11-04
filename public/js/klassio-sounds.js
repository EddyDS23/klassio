/* ============================================================
 * KlassioSounds — música por juego + efectos de acción.
 *
 * Música (loop, empieza con el primer toque del jugador):
 *   wordsearch -> /sounds/sopadeletra.mp3
 *   matching   -> /sounds/conectalospuntos.mp3
 *   crossword  -> /sounds/crucigrama.mp3
 *   kahoot     -> /sounds/kahoot.mp3
 *
 * Efectos:
 *   click      -> /sounds/clik.mp3            (toques y botones)
 *   correcto   -> /sounds/correcto.mp3        (respuesta correcta)
 *   error      -> /sounds/error.mp3           (respuesta incorrecta)
 *   terminado  -> /sounds/juegoterminado.mp3  (terminó justo al acabarse el tiempo)
 *   completado -> /sounds/juegocompletado.mp3 (terminó antes de tiempo)
 *   expirado   -> /sounds/juegoexpirado.mp3   (no terminó la actividad)
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
        roulette: '/sounds/juegoruleta.mp3',
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

    // --- Web Audio: efectos decodificados en memoria (latencia ~10ms) ---
    var actx = null;
    var sfxGain = null;
    var sfxBuffers = {};
    var waOK = false;

    function wctx() {
        if (actx) {
            if (actx.state === 'suspended') {
                try {
                    var resume = actx.resume();
                    if (resume && resume.catch) resume.catch(function () {});
                } catch (e) {}
            }
            return actx;
        }
        try {
            var AC = window.AudioContext || window.webkitAudioContext;
            if (!AC) return null;
            actx = new AC();
            sfxGain = actx.createGain();
            sfxGain.gain.value = muted ? 0 : 1;
            sfxGain.connect(actx.destination);
            waOK = true;
        } catch (e) { actx = null; }
        return actx;
    }

    function decodeAll() {
        var c = wctx();
        if (!c) return;
        Object.keys(SFX).forEach(function (k) {
            if (sfxBuffers[k]) return;
            try {
                fetch(SFX[k]).then(function (r) {
                    if (!r.ok) throw new Error('http ' + r.status);
                    return r.arrayBuffer();
                }).then(function (buf) {
                    return c.decodeAudioData(buf);
                }).then(function (decoded) {
                    sfxBuffers[k] = decoded;
                }).catch(function () {});
            } catch (e) {}
        });
    }

    function playBuffer(name) {
        var c = wctx();
        var buf = sfxBuffers[name];
        if (!c || !buf) return false;
        try {
            var src = c.createBufferSource();
            src.buffer = buf;
            src.connect(sfxGain || c.destination);
            src.start(0);
            return true;
        } catch (e) { return false; }
    }

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

    // Precarga todo al arrancar el juego para que suene al instante:
    // efectos a memoria (Web Audio) + música en streaming.
    function preloadAll() {
        try {
            Object.keys(SFX).forEach(function (k) {
                var a = new Audio(SFX[k]);
                a.preload = 'auto';
                try { a.load(); } catch (e) {}
                sfxCache[k] = a;
            });
        } catch (e) {}
        decodeAll();
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
        // Vía rápida: buffer ya decodificado (casi sin latencia).
        if (playBuffer(name)) return;
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
            // Intenta iniciar la música de fondo inmediatamente al cargar el juego.
            // Si el navegador bloquea el autoplay, fallará silenciosamente y el
            // listener global de 'pointerdown' lo reintentará en el primer clic.
            if (!muted) {
                try {
                    var p = playMusic();
                    if (p && p.then) {
                        p.then(function () { musicStarted = true; }, function () { musicStarted = false; });
                    } else {
                        musicStarted = true;
                    }
                } catch (e) { musicStarted = false; }
            }
            // Si el alumno viene de "Iniciar / Intentar de nuevo" (bandera que
            // deja la página anterior), intentar sonar de inmediato.
            var wantAuto = false;
            try {
                wantAuto = sessionStorage.getItem('klassio-autoplay') === game;
                sessionStorage.removeItem('klassio-autoplay');
            } catch (e) {}
            if (wantAuto && !muted && !musicStarted) {
                var m = ensureMusic();
                if (m) {
                    try {
                        var p2 = m.play();
                        if (p2 && p2.then) {
                            p2.then(function () { musicStarted = true; }, function () { musicStarted = false; });
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
            if (sfxGain) sfxGain.gain.value = muted ? 0 : 1;
            Object.keys(sfxCache).forEach(function (name) { sfxCache[name].muted = muted; });
            if (!muted && music && !musicStarted) {
                playMusic();
            } else if (!muted && musicStarted && music && music.paused) {
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
                decoded: Object.keys(sfxBuffers),
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
