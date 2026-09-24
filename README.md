# Klassio

Klassio es una plataforma web educativa desarrollada con **Laravel 13** que permite a docentes crear clases, actividades interactivas y juegos educativos para estudiantes. La plataforma incorpora autenticación, gestión de roles, clases, actividades, participación individual y por equipos, resultados, rankings y un panel administrativo.

El sistema está diseñado con una arquitectura modular basada en **Controllers, Form Requests, Policies, Services, Models, Routes y Views**, lo que facilita su mantenimiento y la incorporación de nuevos tipos de actividades.

---

## 1. Características principales

- Autenticación de usuarios.
- Roles de:
  - Administrador
  - Docente
  - Estudiante
- Gestión de clases.
- Inscripción de estudiantes mediante código de clase.
- Creación y administración de actividades.
- Publicación y cierre de actividades.
- Actividades individuales.
- Actividades por equipos.
- Creación y administración de equipos.
- Participaciones y control de intentos.
- Registro de puntuación.
- Registro del tiempo empleado.
- Resultados para estudiantes y docentes.
- Rankings.
- Reportes.
- Panel administrativo.
- Validación mediante Form Requests.
- Autorización mediante Policies.
- Pruebas automatizadas.
- Ejecución mediante Docker Compose.

---

## 2. Juegos disponibles

Klassio integra actualmente los siguientes tipos de juegos:

| Tipo | Descripción |
|---|---|
| `word_search` | Sopa de letras |
| `crossword` | Crucigrama |
| `matching` | Relacionar elementos |
| `kahoot` | Preguntas de opción múltiple |

Los juegos pueden utilizar el modo:

- **Individual:** cada estudiante realiza la actividad por separado.
- **Equipo:** los estudiantes participan mediante un equipo asociado a la actividad.

---

## 3. Tecnologías utilizadas

### Backend

- PHP
- Laravel 13
- Eloquent ORM
- Laravel Policies
- Laravel Form Requests
- PHPUnit / Pest para pruebas

### Frontend

- Blade
- HTML
- CSS
- JavaScript

### Base de datos

La aplicación utiliza una base de datos relacional administrada mediante las migraciones de Laravel.

Entre las entidades principales se encuentran:

- `users`
- `classes`
- `enrollments`
- `activities`
- `participations`
- `teams`
- `team_members`
- `wordsearches`
- `words`
- `crosswords`
- `matching_items`
- `kahoots`
- `questions`
- `answers`

### Infraestructura

- Docker
- Docker Compose
- Git
- GitHub

---

# 4. Arquitectura del proyecto

Klassio sigue una arquitectura monolítica organizada por responsabilidades.

Flujo general:

```text
Route
  ↓
Middleware
  ↓
Controller
  ↓
Form Request
  ↓
Policy
  ↓
Service
  ↓
Model / Eloquent
  ↓
Database
  ↓
View
```

### Routes

Definen los endpoints disponibles y conectan las peticiones con los controladores correspondientes.

### Middleware

Controlan aspectos generales de acceso, como autenticación, rol y estado de la cuenta.

### Controllers

Reciben las peticiones HTTP y coordinan el flujo de la operación.

### Form Requests

Centralizan validaciones de entrada.

### Policies

Controlan autorización sobre recursos concretos.

### Services

Contienen la lógica de negocio para evitar concentrarla dentro de los controladores.

### Models

Representan las entidades de la base de datos y sus relaciones mediante Eloquent.

### Views

Contienen la interfaz de usuario construida principalmente con Blade.

---

# 5. Requisitos

Para ejecutar el proyecto se requiere:

- Git
- Docker
- Docker Compose
- Acceso a una terminal

No es necesario instalar PHP, Composer o una base de datos directamente en el sistema anfitrión cuando se utiliza el entorno Docker del proyecto.

---

# 6. Clonar el proyecto

Clonar el repositorio:

```bash
git clone https://github.com/EddyDS23/klassio.git
```

Entrar al proyecto:

```bash
cd klassio
```

Para trabajar con la rama de desarrollo correspondiente:

```bash
git checkout develop
```

o cambiar a la rama de trabajo requerida:

```bash
git checkout feature/details-final
```

---

# 7. Configuración del entorno

Crear el archivo de variables de entorno:

```bash
cp .env.example .env
```

La configuración debe contener los datos correspondientes al entorno Docker y a la base de datos utilizada por el proyecto.

Ejemplo de configuración típica:

```env
APP_NAME=Klassio
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=klassio
DB_USERNAME=klassio
DB_PASSWORD=secret
```

Los valores exactos pueden adaptarse al archivo `docker-compose.yml` utilizado por el proyecto.

---

# 8. Levantar el proyecto con Docker

Construir y levantar los contenedores:

```bash
docker compose up -d --build
```

Comprobar el estado:

```bash
docker compose ps
```

La aplicación queda disponible en la URL configurada por el servicio web, normalmente:

```text
http://localhost
```

Para ver los logs:

```bash
docker compose logs -f
```

Para consultar solamente los logs de la aplicación:

```bash
docker compose logs -f app
```

---

# 9. Instalar dependencias

Si el proyecto acaba de ser clonado y el contenedor necesita instalar dependencias:

```bash
docker compose exec app composer install
```

Generar la clave de Laravel:

```bash
docker compose exec app php artisan key:generate
```

---

# 10. Base de datos

Ejecutar las migraciones:

```bash
docker compose exec app php artisan migrate
```

Para ejecutar migraciones junto con seeders:

```bash
docker compose exec app php artisan migrate --seed
```

Si se necesita reconstruir completamente la base de datos durante desarrollo:

```bash
docker compose exec app php artisan migrate:fresh --seed
```

> `migrate:fresh` elimina las tablas existentes, por lo que debe utilizarse únicamente cuando sea apropiado para el entorno.

---

# 11. Comandos principales de Laravel

Entrar al contenedor:

```bash
docker compose exec app bash
```

Ver las rutas:

```bash
docker compose exec app php artisan route:list
```

Limpiar cachés:

```bash
docker compose exec app php artisan optimize:clear
```

Limpiar únicamente la caché de configuración:

```bash
docker compose exec app php artisan config:clear
```

Limpiar la caché de rutas:

```bash
docker compose exec app php artisan route:clear
```

Limpiar la caché de vistas:

```bash
docker compose exec app php artisan view:clear
```

---

# 12. Usuarios y roles

Klassio maneja tres roles principales.

## Administrador

Tiene acceso al panel administrativo y a la gestión general del sistema.

Puede consultar y administrar información relacionada con:

- Usuarios
- Clases
- Actividades
- Resultados

## Docente

Puede:

- Crear clases.
- Editar clases.
- Archivar clases.
- Generar códigos de clase.
- Crear actividades.
- Editar actividades.
- Publicar actividades.
- Cerrar actividades.
- Crear y administrar equipos.
- Consultar resultados.
- Consultar rankings y reportes.

## Estudiante

Puede:

- Unirse a clases.
- Consultar sus clases.
- Consultar actividades disponibles.
- Participar en juegos.
- Participar individualmente o mediante equipos.
- Consultar sus resultados.

---

# 13. Clases

Las clases pertenecen a un docente y pueden contener múltiples estudiantes.

El flujo general es:

```text
Docente
   ↓
Crea clase
   ↓
Se genera código
   ↓
Estudiante utiliza código
   ↓
Se crea inscripción
   ↓
Estudiante pertenece a la clase
```

Las inscripciones manejan el estado de participación del estudiante en una clase.

---

# 14. Actividades

Las actividades pertenecen a una clase.

Los campos principales incluyen:

- Título
- Descripción
- Tipo de actividad
- Modo
- Puntuación máxima
- Tiempo límite
- Fecha límite
- Estado

Los estados principales son:

```text
draft
  ↓
published
  ↓
closed
```

### Draft

La actividad está en preparación y todavía no está disponible para los estudiantes.

### Published

La actividad está disponible para participación.

### Closed

La actividad ha sido cerrada.

---

# 15. Modos de participación

Cada actividad puede configurarse como:

```text
individual
```

o:

```text
team
```

## Modo individual

Cada estudiante tiene su propia participación.

```text
Student
   ↓
Participation
   ↓
Activity
```

## Modo equipo

Los estudiantes pertenecen a un equipo asociado a la actividad.

```text
Activity
   ↓
Team
   ↓
Team Members
   ↓
Students
```

La participación se relaciona con el equipo.

---

# 16. Equipos

Los docentes pueden administrar equipos para actividades configuradas en modo `team`.

Las operaciones principales incluyen:

- Consultar equipos.
- Crear equipos.
- Agregar estudiantes.
- Eliminar estudiantes.
- Eliminar equipos.
- Asignar estudiantes aleatoriamente.
- Generar equipos aleatoriamente.

La autorización de estas operaciones se controla mediante Policies y las validaciones correspondientes.

---

# 17. Participaciones

La entidad `Participation` registra la ejecución de una actividad.

Entre sus datos se encuentran:

- Actividad.
- Estudiante o equipo.
- Estado.
- Intento.
- Puntuación.
- Tiempo empleado.

El flujo general es:

```text
Actividad publicada
       ↓
Inicio de participación
       ↓
Juego
       ↓
Respuestas
       ↓
Finalización
       ↓
Resultado
```

Los estados de una participación permiten distinguir una actividad iniciada de una actividad finalizada, abandonada o expirada.

---

# 18. Tiempo, intentos y puntuación

Klassio registra información relacionada con la ejecución de las actividades.

## Tiempo

El tiempo empleado se almacena mediante:

```text
elapsed_seconds
```

Esto permite representar el tiempo utilizado por el estudiante o equipo.

## Intentos

Las participaciones manejan el número de intento:

```text
attempt
```

Esto permite conservar diferentes ejecuciones de una actividad.

## Puntuación

La participación almacena:

```text
score
```

La actividad define la puntuación máxima mediante:

```text
max_score
```

---

# 19. Resultados

Los resultados permiten consultar el desempeño obtenido en una actividad.

El sistema distingue entre:

- Resultados del estudiante.
- Resultados del docente.
- Resultados individuales.
- Resultados de equipos.

La consulta de resultados respeta las reglas de autorización correspondientes.

El estudiante puede consultar sus propios resultados y el docente puede consultar los resultados de las actividades que administra.

---

# 20. Ranking y reportes

Klassio cuenta con servicios específicos para generar información de desempeño.

Estos servicios permiten trabajar con:

- Puntuaciones.
- Participaciones.
- Intentos.
- Desempeño individual.
- Desempeño por equipos.
- Resúmenes de actividad.

Las operaciones de agregación se mantienen dentro de la capa de servicios para separar la lógica de negocio de los controladores y las vistas.

---

# 21. Juegos incluidos

## 21.1 Word Search

La sopa de letras utiliza palabras asociadas a la actividad.

El flujo general es:

```text
Actividad
   ↓
Word Search
   ↓
Palabras
   ↓
Participación
   ↓
Respuesta
   ↓
Resultado
```

---

## 21.2 Crossword

El crucigrama utiliza una estructura de cuadrícula almacenada para representar el tablero.

El estudiante interactúa con la cuadrícula y envía sus respuestas al backend.

El servicio correspondiente valida y procesa la participación.

---

## 21.3 Matching

El juego de relacionar elementos presenta elementos que deben ser asociados correctamente.

El backend procesa las respuestas y calcula el resultado correspondiente.

---

## 21.4 Kahoot

Kahoot utiliza preguntas y opciones de respuesta.

Una pregunta debe tener exactamente una opción correcta.

El servicio de Kahoot gestiona operaciones como:

- Crear preguntas.
- Actualizar preguntas.
- Validar respuestas.
- Registrar respuestas.
- Obtener preguntas para jugar.

---

# 22. Cómo añadir un nuevo juego

Una de las características importantes de la arquitectura de Klassio es que permite incorporar nuevos tipos de actividades sin modificar la estructura completa del sistema.

Supongamos que se quiere añadir un nuevo juego llamado:

```text
memory
```

El proceso recomendado es el siguiente.

---

## 22.1 Registrar el nuevo tipo

Agregar el nuevo tipo al lugar donde Klassio define los tipos válidos de actividades.

Ejemplo:

```text
word_search
crossword
matching
kahoot
memory
```

El valor debe ser consistente en:

- Base de datos.
- Validaciones.
- Modelos.
- Servicios.
- Controladores.
- Rutas.
- Vistas.
- Tests.

---

## 22.2 Crear la estructura de base de datos

Crear una migración específica para la información que necesite el nuevo juego.

Por ejemplo:

```bash
docker compose exec app php artisan make:migration create_memories_table
```

La tabla debe relacionarse con la actividad correspondiente.

Una posible estructura conceptual sería:

```text
activities
    ↓
memories
    ↓
memory_items
```

La estructura real debe adaptarse a las reglas del nuevo juego.

---

## 22.3 Crear el Model

Crear los modelos necesarios:

```bash
docker compose exec app php artisan make:model Memory
```

Si el juego requiere entidades adicionales:

```bash
docker compose exec app php artisan make:model MemoryItem
```

Definir las relaciones Eloquent correspondientes.

Por ejemplo:

```php
public function activity()
{
    return $this->belongsTo(Activity::class);
}
```

---

## 22.4 Crear el Service

Cada juego debe concentrar su lógica de negocio en un servicio independiente.

Ejemplo:

```text
app/Services/MemoryService.php
```

El servicio puede encargarse de:

- Crear el juego.
- Actualizarlo.
- Obtener información para jugar.
- Validar respuestas.
- Registrar resultados.
- Procesar la finalización.

La lógica específica del juego no debe concentrarse directamente en el Controller.

---

## 22.5 Crear los Form Requests

Si el juego necesita entradas específicas, crear Form Requests.

Ejemplo:

```text
app/Http/Requests/StoreMemoryRequest.php
app/Http/Requests/UpdateMemoryRequest.php
```

Los Requests deben encargarse de validar:

- Campos obligatorios.
- Tipos de datos.
- Valores permitidos.
- Relaciones.
- Estructuras específicas del juego.

---

## 22.6 Crear el Controller

Crear un controlador específico:

```text
app/Http/Controllers/MemoryController.php
```

El controlador debe coordinar:

```text
Request
   ↓
Validation
   ↓
Authorization
   ↓
MemoryService
   ↓
Response / View
```

La lógica principal debe permanecer en `MemoryService`.

---

## 22.7 Crear la Policy

Si el juego necesita autorización específica, crear una Policy.

Ejemplo:

```text
app/Policies/MemoryPolicy.php
```

La Policy debe comprobar que el usuario tenga autorización para acceder o modificar la actividad.

Las reglas generales de participación continúan siendo responsabilidad de `ParticipationPolicy`.

---

## 22.8 Crear las rutas

Agregar rutas para el nuevo juego siguiendo la estructura existente.

Ejemplo conceptual:

```php
Route::get(
    '/activities/{activity}/memory',
    [MemoryController::class, 'play']
)->name('student.memory.play');
```

Para responder una acción del juego:

```php
Route::post(
    '/activities/{activity}/memory/answer',
    [MemoryController::class, 'answer']
)->name('student.memory.answer');
```

Las rutas deben respetar:

- Middleware.
- Policies.
- Convenciones de nombres.
- Separación entre docente y estudiante.

---

## 22.9 Integrar Participation

El nuevo juego debe integrarse con el sistema general de participación.

Cuando el estudiante inicia una actividad:

```text
ParticipationController
        ↓
Activity
        ↓
Activity Type
        ↓
Memory
```

El sistema debe poder identificar que:

```text
activity.type = memory
```

y dirigir al estudiante a la interfaz correspondiente.

---

## 22.10 Crear las vistas

Crear las vistas Blade del juego.

Ejemplo:

```text
resources/views/student/memory/
    play.blade.php
    result.blade.php
```

Si el docente administra contenido del juego:

```text
resources/views/teacher/memory/
    create.blade.php
    edit.blade.php
```

---

## 22.11 Integrar resultados

El nuevo juego debe registrar su resultado mediante `Participation`.

Debe conservar, como mínimo:

```text
score
attempt
elapsed_seconds
status
```

De esta manera el juego puede utilizar automáticamente la infraestructura existente para:

- Resultados.
- Ranking.
- Reportes.
- Tiempo.
- Intentos.

---

## 22.12 Integrar equipos

Si el nuevo juego admite modo equipo, debe soportar la misma estructura de participación utilizada por los demás juegos.

En modo individual:

```text
Participation
    student_id
    team_id = null
```

En modo equipo:

```text
Participation
    student_id = null
    team_id
```

La lógica específica del juego debe respetar esta distinción.

---

## 22.13 Integrar administración

Si el nuevo juego requiere configuración administrativa, debe incorporarse al flujo de administración de actividades.

Esto incluye:

- Tipo de actividad.
- Información específica.
- Visualización.
- Validación.
- Edición.
- Eliminación cuando corresponda.

---

## 22.14 Crear pruebas

Cada juego nuevo debe tener pruebas propias.

Por ejemplo:

```text
tests/Feature/MemoryTest.php
```

Las pruebas deben cubrir como mínimo:

- Acceso autorizado.
- Acceso no autorizado.
- Creación.
- Actualización.
- Validación.
- Inicio de participación.
- Respuestas correctas.
- Respuestas incorrectas.
- Puntuación.
- Finalización.
- Intentos.
- Tiempo.
- Modo individual.
- Modo equipo, si aplica.
- Resultado.

---

# 23. Checklist para añadir un juego

Antes de considerar terminado un nuevo juego:

```text
[ ] Tipo registrado
[ ] Migración creada
[ ] Modelo creado
[ ] Relaciones Eloquent
[ ] Service creado
[ ] Form Requests
[ ] Policy
[ ] Controller
[ ] Rutas
[ ] Vista de configuración
[ ] Vista de juego
[ ] Vista de resultado
[ ] Integración con Participation
[ ] Integración con tiempo
[ ] Integración con intentos
[ ] Integración con score
[ ] Integración individual
[ ] Integración por equipos
[ ] Resultados
[ ] Ranking
[ ] Reportes
[ ] Administración
[ ] Tests
```

---

# 24. Modificar un juego existente

Cuando sea necesario modificar un juego existente, se recomienda localizar primero todas las partes que dependen del tipo de actividad.

Buscar el identificador del juego:

```bash
grep -R "word_search" app routes resources tests
```

Cambiar `word_search` por el tipo que se desea modificar.

También se pueden utilizar herramientas de búsqueda del editor para localizar:

- Services.
- Controllers.
- Requests.
- Policies.
- Routes.
- Views.
- Tests.

El cambio debe mantener la integración con:

```text
Activity
Participation
Results
Ranking
Reports
Teams
```

cuando corresponda.

---

# 25. Seguridad

Klassio utiliza diferentes capas para proteger las operaciones.

## Autenticación

Las rutas privadas requieren un usuario autenticado.

## Roles

El sistema diferencia las capacidades de:

```text
admin
teacher
student
```

## Policies

Las Policies controlan si el usuario puede realizar una operación sobre un recurso.

## Form Requests

Los datos recibidos del usuario son validados antes de llegar a la lógica de negocio.

## Propiedad de recursos

Las operaciones deben comprobar que el usuario tenga relación con el recurso solicitado.

Por ejemplo:

```text
Teacher
   ↓
Owns Activity
```

o:

```text
Student
   ↓
Owns Participation
```

o en modo equipo:

```text
Student
   ↓
Member of Team
   ↓
Participation
```

Esto evita depender únicamente del identificador recibido desde el navegador.

---

# 26. Prevención de N+1

Cuando se trabaja con relaciones Eloquent se debe evitar ejecutar consultas dentro de ciclos cuando los datos pueden cargarse previamente.

Ejemplo que debe evitarse:

```php
foreach ($students as $student) {
    $student->participations;
}
```

si la relación no ha sido precargada.

Es preferible utilizar:

```php
$students = Student::with('participations')->get();
```

También deben utilizarse herramientas como:

```php
with()
```

```php
load()
```

```php
withCount()
```

```php
withExists()
```

cuando sean adecuadas.

En operaciones donde se requiere comparar muchos registros, es preferible obtener los datos necesarios en consultas agrupadas y realizar el procesamiento en memoria.

---

# 27. Paginación

Las listas que pueden crecer considerablemente deben utilizar paginación.

Ejemplo:

```php
->paginate(20)
```

Cuando se utilizan filtros o parámetros de consulta:

```php
->paginate(20)
->withQueryString();
```

Esto permite mantener un rendimiento adecuado incluso cuando existen grandes cantidades de:

- Clases.
- Estudiantes.
- Actividades.
- Usuarios.
- Resultados.

---

# 28. Pruebas automatizadas

Las pruebas se ejecutan dentro del contenedor de Laravel.

Ejecutar toda la suite:

```bash
docker compose exec app php artisan test
```

Ejecutar una prueba específica:

```bash
docker compose exec app php artisan test tests/Feature/WordsearchTest.php
```

Ejecutar un conjunto específico:

```bash
docker compose exec app php artisan test tests/Feature
```

También es posible ejecutar una prueba mediante su nombre:

```bash
docker compose exec app php artisan test --filter=NombreDeLaPrueba
```

---

# 29. Estructura de pruebas

Las pruebas principales cubren funcionalidades como:

```text
tests/
├── Feature/
│   ├── Admin/
│   ├── ParticipationTest.php
│   ├── TeamTest.php
│   ├── WordsearchTest.php
│   ├── CrosswordTest.php
│   ├── MatchingTest.php
│   ├── KahootTest.php
│   ├── ResultsTest.php
│   └── RankingTest.php
```

La estructura exacta puede crecer conforme se incorporen nuevas funcionalidades.

---

# 30. Desarrollo con Git

Consultar el estado:

```bash
git status
```

Consultar ramas:

```bash
git branch
```

Consultar ramas remotas:

```bash
git branch -r
```

Actualizar referencias:

```bash
git fetch origin
```

Crear una rama para una funcionalidad:

```bash
git checkout -b feature/nueva-funcionalidad
```

Agregar cambios:

```bash
git add .
```

Crear commit:

```bash
git commit -m "feat: descripción del cambio"
```

Subir la rama:

```bash
git push -u origin feature/nueva-funcionalidad
```

---

# 31. Flujo recomendado para nuevas funcionalidades

El flujo recomendado es:

```text
1. Crear rama
       ↓
2. Analizar funcionalidad
       ↓
3. Crear / modificar migración
       ↓
4. Crear / modificar Model
       ↓
5. Crear Service
       ↓
6. Crear Form Request
       ↓
7. Crear Policy
       ↓
8. Crear Controller
       ↓
9. Crear Routes
       ↓
10. Crear Views
       ↓
11. Integrar Participation
       ↓
12. Integrar Results
       ↓
13. Integrar Ranking / Reports
       ↓
14. Crear Tests
       ↓
15. Ejecutar suite completa
       ↓
16. Revisar Git
       ↓
17. Merge
```

---

# 32. Comandos útiles de Docker

Levantar:

```bash
docker compose up -d
```

Construir:

```bash
docker compose up -d --build
```

Detener:

```bash
docker compose down
```

Ver contenedores:

```bash
docker compose ps
```

Ver logs:

```bash
docker compose logs -f
```

Entrar al contenedor:

```bash
docker compose exec app bash
```

Ejecutar Artisan:

```bash
docker compose exec app php artisan <comando>
```

Ejecutar Composer:

```bash
docker compose exec app composer <comando>
```

---

# 33. Solución rápida de problemas

## La aplicación no carga

Comprobar:

```bash
docker compose ps
```

y:

```bash
docker compose logs -f
```

Después limpiar cachés:

```bash
docker compose exec app php artisan optimize:clear
```

---

## Problemas con la base de datos

Comprobar los contenedores:

```bash
docker compose ps
```

Ver los logs:

```bash
docker compose logs -f db
```

Comprobar la configuración de `.env`.

Después ejecutar:

```bash
docker compose exec app php artisan migrate:status
```

---

## Problemas después de modificar rutas

Ejecutar:

```bash
docker compose exec app php artisan route:clear
docker compose exec app php artisan optimize:clear
```

Comprobar las rutas:

```bash
docker compose exec app php artisan route:list
```

---

## Problemas después de modificar vistas

Ejecutar:

```bash
docker compose exec app php artisan view:clear
```

---

# 34. Flujo completo de una actividad

El funcionamiento general de una actividad puede representarse así:

```text
Teacher
   │
   ├── Creates Activity
   │
   ├── Configures Game
   │
   └── Publishes Activity
            │
            ▼
       Student sees Activity
            │
            ▼
       Starts Participation
            │
            ▼
          Game
            │
            ├── Answers
            ├── Timer
            └── Score
            │
            ▼
       Finish Participation
            │
            ▼
          Result
            │
       ┌────┴────┐
       ▼         ▼
    Ranking    Report
```

Para actividades por equipos:

```text
Teacher
   ↓
Activity
   ↓
Team
   ↓
Team Members
   ↓
Participation
   ↓
Game
   ↓
Result
```

---

# 35. Convenciones recomendadas

Mantener nombres claros y consistentes.

### Controllers

```text
ActivityController
ParticipationController
TeamController
```

### Services

```text
ActivityService
ParticipationService
TeamService
WordsearchService
CrosswordService
MatchingService
KahootService
```

### Policies

```text
ActivityPolicy
ParticipationPolicy
TeamPolicy
```

### Requests

```text
StoreActivityRequest
UpdateActivityRequest
AddTeamMemberRequest
```

La misma convención debe mantenerse al crear nuevos módulos.

---

# 36. Extensibilidad

La arquitectura de Klassio está preparada para ampliar el catálogo de juegos sin tener que reconstruir el sistema de participación.

La infraestructura común se reutiliza:

```text
Activity
    │
    ├── Game
    │
    ├── Participation
    │
    ├── Results
    │
    ├── Ranking
    │
    └── Reports
```

Esto permite que un nuevo juego se concentre principalmente en su propia lógica.

Por ejemplo:

```text
                    ┌── Word Search
                    │
                    ├── Crossword
Activity ───────────┼── Matching
                    │
                    ├── Kahoot
                    │
                    └── Nuevo juego
```

El sistema común de participación, resultados y usuarios permanece reutilizable.

---

# 37. Ejemplo de expansión futura

Un juego nuevo como `memory` podría utilizar:

```text
Activity
   ↓
MemoryService
   ↓
Memory
   ↓
MemoryItems
```

Mientras que la participación continúa utilizando:

```text
Participation
```

y los resultados:

```text
Result
Ranking
Reports
```

De esta manera, añadir un juego no requiere crear nuevamente el sistema de autenticación, clases, usuarios, participación, equipos, resultados o reportes.

---

# 38. Estado funcional del proyecto

Klassio integra:

- Autenticación y roles.
- Gestión de clases.
- Gestión de actividades.
- Juegos educativos.
- Participación individual.
- Participación por equipos.
- Gestión de equipos.
- Resultados.
- Rankings.
- Reportes.
- Administración.
- Registro de tiempo.
- Control de intentos.
- Puntuaciones.
- Validación.
- Autorización.
- Pruebas automatizadas.
- Entorno Docker.

La suite de pruebas automatizadas del proyecto se utiliza como mecanismo de validación de las funcionalidades implementadas.

---

# 39. Inicio rápido

Para una instalación rápida:

```bash
git clone https://github.com/EddyDS23/klassio.git
cd klassio
cp .env.example .env
docker compose up -d --build
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
docker compose exec app php artisan optimize:clear
```

Después acceder a:

```text
http://localhost
```

Para verificar el proyecto:

```bash
docker compose exec app php artisan test
```

---

# 40. Comandos de referencia

| Acción | Comando |
|---|---|
| Levantar proyecto | `docker compose up -d` |
| Reconstruir | `docker compose up -d --build` |
| Detener | `docker compose down` |
| Estado | `docker compose ps` |
| Logs | `docker compose logs -f` |
| Entrar al contenedor | `docker compose exec app bash` |
| Migraciones | `docker compose exec app php artisan migrate` |
| Seeders | `docker compose exec app php artisan db:seed` |
| Migración + seed | `docker compose exec app php artisan migrate --seed` |
| Reiniciar BD | `docker compose exec app php artisan migrate:fresh --seed` |
| Rutas | `docker compose exec app php artisan route:list` |
| Limpiar cachés | `docker compose exec app php artisan optimize:clear` |
| Tests | `docker compose exec app php artisan test` |

---

# 41. Licencia

Proyecto académico desarrollado para la plataforma educativa **Klassio**.

---

## Klassio

Plataforma educativa modular para la creación, administración y ejecución de actividades interactivas, con soporte para estudiantes, docentes, administradores, participación individual y por equipos.
# CI/CD funcionando
