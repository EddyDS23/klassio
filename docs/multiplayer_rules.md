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

Los botones de salida dentro de Crossword, Wordsearch y Matching envían
`POST /student/game-sessions/{id}/leave` con el identificador de la sala y
token CSRF. El abandono conserva respuestas y puntaje ya persistidos. Una
participación terminal no cambia de estado; las solicitudes repetidas se
rechazan por autorización o se responden sin volver a modificarla.

Autoridad: el servidor valida rondas, puntaje y finalización. El frontend solo muestra.

## Actividades convencionales en equipos

- Todos los integrantes de un equipo comparten una sola `Participation` por
  intento convencional; las respuestas y el puntaje pertenecen a ese intento.
- La calificación y el ranking del equipo usan su mejor intento completado:
  mayor puntaje primero y, en empate, menor tiempo. Si puntaje y tiempo también
  empatan, se muestra el intento más reciente.
- Los intentos abandonados o expirados se muestran como resultado únicamente
  cuando el equipo no tiene ningún intento completado.
- La tabla del profesor muestra el intento activo mientras el equipo juega;
  después muestra el mejor intento completado (o el último intento terminal si
  nunca completó uno).
- Las participaciones vinculadas a `GameSession` corresponden al multijugador
  por sala y quedan excluidas de resultados convencionales, reportes y rankings.
- El puntaje de una actividad convencional en equipo es compartido. El modelo
  actual no registra qué integrante aportó cada respuesta, así que no se
  atribuyen respuestas ni calificaciones individuales a sus miembros.

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
- El Kahoot convencional permanece en `POST /student/kahoot/answer`: solo acepta
  una participación activa sin `game_session_id`, valida propietario/equipo y
  tiempo antes de persistir, y verifica que pregunta y opción pertenezcan al
  Kahoot y pregunta correspondientes. Una participación de sala solo responde
  mediante `POST /student/kahoot/answer-session`.

## Crucigrama, sopa de letras y unir conceptos (implementado)

Estos tres juegos se juegan en **carrera libre con tiempo**, no por rondas
controladas por el profesor.

- Dinámica: **competitiva simultánea sin rondas**. El profesor solo **inicia** la
  partida (y fija `Activity.time_limit`); cada participación (jugador o equipo)
  resuelve el juego a su ritmo y gana quien más puntaje obtiene.
- **Solo hay un botón de inicio**: `POST /teacher/game-sessions/{id}/spin` se rechaza
  para estos tipos con `422` ("Este juego se juega en carrera libre: no hay rondas
  que avanzar.").
- Cada participante representa a un jugador individual o a un equipo. Los integrantes
  del mismo equipo comparten respuestas, puntaje y progreso; los equipos rivales
  solo ven las posiciones en vivo, nunca las respuestas de otro equipo.
- En modo equipo, varios integrantes pueden unirse a la misma participación de sala.
  La sala registra la presencia de cada integrante por separado, mientras que el
  equipo conserva una sola participación y ocupa una plaza del cupo configurado.
- Cuando un integrante sale, solo se retira su presencia. La participación del equipo
  sigue activa mientras quede al menos un integrante presente; cuando sale el último,
  se abandona la participación del equipo. Un integrante puede volver a unirse con el
  código mientras la participación de su equipo siga activa.
- Para equipos, el endpoint de estado entrega al integrante autenticado el
  progreso de su propia participación en Crucigrama, Sopa de letras y Matching.
  Los IDs de respuestas de los demás equipos no se incluyen.
- **Se reutilizan los endpoints de un jugador** para responder:
  - Crucigrama: `POST /crossword/answer` (`participation_id` de la sala).
  - Sopa de letras: `POST /wordsearch/answer`.
  - Unir conceptos: `POST /matching/answer`.
  La respuesta se registra contra la `Participation` de la sala
  (`game_session_id`) y el puntaje se sincroniza con `ParticipationService::syncScore`.
- Al completar todo el juego, la `Participation` pasa a `completed`.
- Los servicios de Wordsearch y Matching serializan la comprobación y escritura
  por participación. Wordsearch también mantiene la clave única
  `participation_id + word_id`; Matching permite guardar intentos incorrectos
  repetidos, pero un acierto previo no vuelve a crear una fila ni sumar puntos.
- La carrera termina cuando **todos los jugadores activos completaron** el juego o
  cuando **expira `Activity.time_limit`**. Si `time_limit` es `null`, solo termina
  cuando todos completan (o quedan <2 activos).
- Resultado general: `winner` (máximo puntaje único) o `draw` (empate). El ranking
  considera participaciones `started`, `completed` y `expired`.
- Las clasificaciones finales muestran puntaje, progreso por juego, estado y
  duración guardada en `Participation.elapsed_seconds`. La regla actual sigue
  declarando empate cuando dos o más participantes obtienen el mismo puntaje.
- El profesor ve un panel de **carrera en curso** con posiciones en vivo y el tiempo
  restante (`teacher/game-sessions/{id}`), sin control de rondas.
- Las páginas de juego son `student/game-sessions/{id}/{crossword,wordsearch,matching}`
  (`student.{crossword,wordsearch,matching}.play-session`).

## Sincronización y presencia

El estado de sala, los rankings y el temporizador se consultan por polling cada
2 segundos; los relojes locales de la interfaz se actualizan cada segundo. El
campo `connected` no representa una conexión real y no se expone como estado de
presencia. Si alguien pierde conexión, recarga o vuelve a entrar, conserva su
participación y el servidor sigue aplicando el tiempo límite; no hay detección
de desconexión ni presencia en línea.

Los eventos usan el canal privado `game-session.{id}` (participantes y docente
propietario). La autorización usa `GameSessionPolicy::view`, que valida la
sesión concreta y reconoce miembros del equipo asociado a la actividad:

- `PlayerJoined`, `PlayerLeft`
- `GameStarted`, `GameFinished`
- `TurnChanged`, `ScoreUpdated`
- `RoundStarted` (el profesor giró), `RoundClosed` (se agotó el tiempo o todos respondieron)

Las vistas de sala y los juegos por rondas pueden crear un cliente Laravel Echo
cuando `broadcasting.connections.reverb.enabled` está activo. Las vistas de
carrera libre dependen del polling de
`GET /student/game-sessions/{id}/state`; el estado incluye el progreso detallado
solo de la participación del equipo del solicitante y no recibe esos eventos con Echo.

La configuración de ejemplo usa `BROADCAST_CONNECTION=log` y
`REVERB_SERVER_ENABLED=false`. Compose declara un servicio Reverb bajo el perfil
`realtime`; no se inicia con los servicios por defecto. Nginx tiene proxy para
`/app/`, pero la configuración predeterminada no activa Reverb. Las clases de
evento implementan `ShouldBroadcast` y `QUEUE_CONNECTION` usa `database`;
Compose no define un worker de colas. Para usar eventos en producción hay que
activar el perfil y las variables de Reverb, ejecutar un worker de cola y
validar autenticación de canales y WebSocket desde el navegador. No hay health
check de Reverb ni prueba end-to-end WebSocket configurados. El polling sigue
siendo el comportamiento operativo disponible con la configuración de ejemplo.
