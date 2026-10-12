# Reglas del multijugador — Klassio

Reglas definidas para el modo multijugador de Klassio (según el
Roadmap_Multiplayer_Klassio). Se definen por juego antes de implementar.

## Modelo

- `Activity` = contenido/configuración del juego.
- `GameSession` = una partida concreta (sala con código, estado, turnos, resultado).
- `Participation` = un jugador dentro de esa partida (`game_session_id`).

## Reglas generales de sala

| Regla | Valor |
| --- | --- |
| Mínimo para iniciar | 2 jugadores |
| Máximo | configurable en la sala (2–16), por defecto 4 |
| Quién crea la sala | el profesor dueño de la actividad (`teacher.game-sessions`) |
| Quién inicia | solo el profesor dueño de la actividad |
| Entrada de alumnos | solo con el código único `KLS###` en `student/game-sessions/join` |
| Estados de sala | `waiting`, `starting`, `playing`, `finished`, `cancelled` |
| Jugador duplicado | rechazado |
| Sala llena | rechaza nuevos jugadores |
| Sala en `playing`/`finished` | no acepta nuevos jugadores |
| Salir | marca `Participation` como `abandoned`; si quedan <2 activos la partida termina |
| Sala vacía en `waiting` | se cancela |

Autoridad: el servidor valida rondas, puntaje y finalización. El frontend solo muestra.

## Ruleta (implementada)

- Dinámica: **competitiva simultánea por rondas** (sin turnos).
- **El profesor gira la ruleta**: `POST /teacher/game-sessions/{id}/spin` selecciona un ítem pendiente aleatorio y abre la ronda (`state.roulette_item_id`, `state.round_started_at`, `state.round_seconds`). Solo el profesor dueño puede girar.
- El profesor configura los **segundos para responder** antes de girar (`round_seconds`, entre 5 y 300; por defecto 30).
- **Todos los jugadores ven y responden la misma pregunta a la vez** (`POST /roulette/spin-session` solo lee la pregunta de la ronda actual para cualquier jugador; es idempotente).
- Cada jugador responde cada ítem una sola vez (por `Participation`, protegido con verificación de duplicados).
- La ronda se cierra cuando **todos los jugadores activos respondieron** el ítem o cuando se agota el tiempo (`round_seconds`).
- **Quien no responde a tiempo falla**: al cerrarse la ronda se registra una respuesta con `response = '(sin respuesta)'`, `is_correct = false` y `score = 0`.
- Tras cerrarse la ronda, el profesor debe **volver a girar** para la siguiente pregunta.
- Un ítem no se repite (se registra en `state.round_item_ids`).
- La partida termina cuando se agotan los ítems (tras cerrar la última ronda) o cuando expira `Activity.time_limit`.
- Puntaje calculado en el servidor con `RouletteService::checkAnswer` + `ParticipationService::syncScore` (normaliza a `Activity.max_score`).
- Resultado general: `winner` (máximo puntaje único) o `draw` (empate en el máximo).
- Resultado individual: aciertos, errores, puntaje y duración por `Participation`.
- Tiempo: reloj del servidor (límite de `Activity.time_limit` si existe); si expira, la partida finaliza con el puntaje actual.

## Kahoot (implementado)

- Dinámica: **competitiva simultánea por rondas**, igual que la ruleta.
- **El profesor avanza la partida**: `POST /teacher/game-sessions/{id}/spin` selecciona una pregunta pendiente aleatoria y abre la ronda (`state.kahoot_question_id`, `state.round_started_at`, `state.round_seconds`). Solo el profesor dueño puede avanzar.
- El profesor configura los **segundos para responder** antes de avanzar (`round_seconds`, entre 5 y 300; por defecto 30).
- **Todos los jugadores ven y responden la misma pregunta a la vez** (`POST /kahoot/question-session` solo lee la pregunta de la ronda actual; es idempotente). Las opciones se envían sin revelar cuál es la correcta.
- Cada jugador responde cada pregunta una sola vez (clave única `participation_id + question_id`).
- La ronda se cierra cuando **todos responden** o cuando **se agota el tiempo**.
- **Quien no responde a tiempo falla**: se registra una respuesta con `option_id = NULL`, `is_correct = false` y `score = 0` (`POST /kahoot/answer-session`).
- Tras cerrarse la ronda, el profesor debe **avanzar** para la siguiente pregunta.
- Una pregunta no se repite (se registra en `state.round_question_ids`).
- La partida termina cuando se agotan las preguntas (tras cerrar la última ronda) o cuando expira `Activity.time_limit`.
- Puntaje en el servidor con `KahootService::validateAnswer` + `ParticipationService::syncScore` (normaliza a `Activity.max_score`).

## Crucigrama, sopa de letras y unir conceptos (implementado)

Estos tres juegos se juegan en **carrera libre con tiempo**, no por rondas
controladas por el profesor.

- Dinámica: **competitiva simultánea sin rondas**. El profesor solo **inicia** la
  partida (y fija `Activity.time_limit`); cada jugador resuelve todo el juego a su
  ritmo y gana quien más puntaje obtiene.
- **Solo hay un botón de inicio**: `POST /teacher/game-sessions/{id}/spin` se rechaza
  para estos tipos con `422` ("Este juego se juega en carrera libre: no hay rondas
  que avanzar.").
- **Cada jugador resuelve el mismo contenido de forma independiente**; no ven la
  pantalla de los demás, solo las posiciones en vivo.
- **Se reutilizan los endpoints de un jugador** para responder:
  - Crucigrama: `POST /crossword/answer` (`participation_id` de la sala).
  - Sopa de letras: `POST /wordsearch/answer`.
  - Unir conceptos: `POST /matching/answer`.
  La respuesta se registra contra la `Participation` de la sala
  (`game_session_id`) y el puntaje se sincroniza con `ParticipationService::syncScore`.
- Al completar todo el juego, la `Participation` pasa a `completed`.
- La carrera termina cuando **todos los jugadores activos completaron** el juego o
  cuando **expira `Activity.time_limit`**. Si `time_limit` es `null`, solo termina
  cuando todos completan (o quedan <2 activos).
- Resultado general: `winner` (máximo puntaje único) o `draw` (empate). El ranking
  considera participaciones `started`, `completed` y `expired`.
- El profesor ve un panel de **carrera en curso** con posiciones en vivo y el tiempo
  restante (`teacher/game-sessions/{id}`), sin control de rondas.
- Las páginas de juego son `student/game-sessions/{id}/{crossword,wordsearch,matching}`
  (`student.{crossword,wordsearch,matching}.play-session`).

## Eventos realtime

Canal privado `game-session.{id}` (solo participantes y maestro de la actividad):

- `PlayerJoined`, `PlayerLeft`
- `GameStarted`, `GameFinished`
- `TurnChanged`, `ScoreUpdated`
- `RoundStarted` (el profesor giró), `RoundClosed` (se agotó el tiempo o todos respondieron)

Los clientes usan Laravel Echo cuando Reverb está habilitado; hay polling de
`GET /student/game-sessions/{id}/state` como respaldo si no hay servidor realtime.