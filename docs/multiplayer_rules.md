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
| Quién inicia | solo el host (creador de la sala) |
| Entrada | código único `KLS###` |
| Estados de sala | `waiting`, `starting`, `playing`, `finished`, `cancelled` |
| Jugador duplicado | rechazado |
| Sala llena | rechaza nuevos jugadores |
| Sala en `playing`/`finished` | no acepta nuevos jugadores |
| Salir | marca `Participation` como `abandoned`; si quedan <2 activos la partida termina |

Autoridad: el servidor valida turnos, puntaje y finalización. El frontend solo muestra.

## Ruleta (implementada)

- Dinámica: **competitiva por turnos**.
- Solo el jugador con `current_turn_participation_id` puede girar (`POST /roulette/spin-session`).
- Si otro jugador intenta girar o responder → `403 "No es tu turno."`.
- Cada giro selecciona en el servidor un ítem pendiente y lo guarda en `game_sessions.state.roulette_item_id`.
- La pregunta se muestra a todos (modal), pero solo el jugador del turno puede responder (`POST /roulette/answer-session`).
- Cada jugador responde los mismos ítems de la ruleta una sola vez (protegido con transacción; el índice de pendientes del servicio + verificación de duplicados).
- Puntaje calculado en el servidor con `RouletteService::checkAnswer` + `ParticipationService::syncScore` (normaliza a `Activity.max_score`).
- La partida termina cuando el jugador activo completa todas sus preguntas.
- Resultado general: `winner` (máximo puntaje único) o `draw` (empate en el máximo).
- Resultado individual: aciertos, errores, puntaje y duración por `Participation`.
- Tiempo: reloj del servidor (límite de `Activity.time_limit` si existe); si expira, la partida finaliza con el puntaje actual.

## Eventos realtime

Canal privado `game-session.{id}` (solo participantes y maestro de la actividad):

- `PlayerJoined`, `PlayerLeft`
- `GameStarted`, `GameFinished`
- `TurnChanged`, `ScoreUpdated`

Los clientes usan Laravel Echo cuando Reverb está habilitado; hay polling de
`GET /student/game-sessions/{id}/state` como respaldo si no hay servidor realtime.