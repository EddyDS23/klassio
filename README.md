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

---

# 42. Módulo multijugador en tiempo real

Esta sección amplía la documentación funcional general y describe el módulo de partidas multijugador tal como está implementado en el código revisado el **16 de octubre de 2026**. Para el historial de cierre y las salvedades de validación del módulo, véase también `README_CIERRE_MODULO_MULTIJUGADOR_KLASSIO.md`.

El modo multijugador por sala es distinto de una actividad convencional configurada como `team`: la sala tiene un código, un docente anfitrión, participantes registrados para esa sesión, estado compartido y una pantalla de resultados de esa partida. Las actividades individuales y las actividades convencionales por equipo siguen utilizando el flujo de participaciones ordinario.

## 42.1 Juegos soportados en una sala

El módulo admite los cinco tipos siguientes:

| Tipo | Modo de partida | Control principal |
|---|---|---|
| `word_search` | Carrera libre | Cada participación busca palabras y comparte su progreso según el modo individual/equipo. |
| `crossword` | Carrera libre | Cada participación resuelve el tablero y comparte respuestas y puntuación según el modo. |
| `matching` | Carrera libre | Cada participación relaciona elementos; la interacción y el resultado se sincronizan. |
| `roulette` | Por rondas | El docente gira la ruleta y abre una ronda común para los participantes. |
| `kahoot` | Por rondas/preguntas | El docente avanza las preguntas; cada participación responde la pregunta vigente. |

En crucigrama, sopa de letras y Matching, el progreso de cada participación está aislado de los demás jugadores/equipos. En actividades por equipos, los integrantes del mismo equipo comparten una participación y su progreso. En Ruleta y Kahoot, el docente controla la apertura/avance de rondas; en equipos el sistema asigna un integrante respondedor para la ronda y los demás observan el progreso compartido.

## 42.2 Componentes de código

La responsabilidad está distribuida entre las siguientes capas:

| Capa | Componentes principales | Responsabilidad |
|---|---|---|
| Rutas | `routes/web.php`, `routes/channels.php` | Entrada a sala, creación y control docente, estado, juego y autorización del canal privado. |
| Controladores | `GameSessionController`, `TeacherGameSessionController` y controladores de los juegos | Validar acceso, coordinar requests, delegar la lógica y seleccionar las vistas específicas del juego. |
| Validación | `StoreGameSessionRequest`, `JoinGameSessionRequest` | Validar actividad/cupo al crear una sala y código al unirse. |
| Autorización | `GameSessionPolicy` | Limitar creación, consulta, ingreso, juego, resultados, control y cancelación al rol/propietario/participante correspondiente. |
| Lógica de dominio | `GameSessionService`, `GameSessionAnswerService`, `ParticipationService` | Gestionar ciclo de vida, jugadores, turnos/rondas, estado, respuestas, presencia, tiempos y resultados. |
| Persistencia | `GameSession`, `GameSessionPresence`, `Participation` | Representar sesión, presencia individual y participación individual o de equipo. |
| Sincronización | Eventos `PlayerJoined`, `PlayerLeft`, `GameStarted`, `TurnChanged`, `RoundStarted`, `RoundClosed`, `ScoreUpdated`, `GameFinished` | Publicar cambios de estado a los suscriptores autorizados del canal de la sala. |
| Vistas | `resources/views/teacher/game_sessions/`, `resources/views/student/game_sessions/` y vistas multiplayer de cada juego | Mostrar creación, espera, controles docentes, juego y resultados. |
| Pruebas | `GameSessionTest`, `MultiplayerRaceTest`, `MultiplayerRouletteTest`, `KahootMultiplayerTest`, `GameSessionChannelTest` | Cubrir permisos, estados, modos, rondas, puntuación, resultados y canal. |

Los controladores no deben convertirse en el lugar de la lógica de juego: las reglas de concurrencia, estado y participación se centralizan en los servicios. Las rutas existentes son el contrato del frontend y no deben sustituirse por endpoints inventados.

## 42.3 Flujo de una sala

```text
Docente selecciona una actividad publicada
        ↓
Se crea GameSession con código único y estado waiting
        ↓
Alumnos ingresan el código y se validan cupo, actividad y duplicados
        ↓
Se crea o recupera la Participation de la sesión
        ↓
Para modo equipo, la Participation identifica al team y cada alumno tiene Presence propia
        ↓
El docente inicia la sala: estado playing
        ↓
El juego actualiza respuestas/progreso y el estado compartido
        ↓
Se finaliza por condición del juego, tiempo o acción correspondiente
        ↓
GameSession y participaciones quedan disponibles para resultados
```

El docente no ocupa un lugar de jugador. La sala se crea para una actividad publicada que pertenece al profesor. El código es único, tiene prefijo de producto `KLS` y se usa para localizar la sala. El cupo configurable aceptado por el request va de 2 a 16.

Si el profesor pierde la pantalla o se desconecta, puede volver a abrir la sala de su actividad desde la vista de gestión/panel mientras la sesión siga disponible. El panel de **Salas activas** muestra salas propias en espera o en juego, con código y estado. Las sesiones finalizadas o canceladas no aparecen como activas.

## 42.4 Estados y datos persistidos

### `game_sessions`

La migración `2026_10_07_000001_create_game_sessions_table.php` crea la entidad de sala. Sus datos relevantes son:

- `activity_id`: actividad asociada; se elimina la sala si se elimina la actividad.
- `code`: código único de acceso.
- `status`: `waiting`, `starting`, `playing`, `finished` o `cancelled`.
- `max_players`: cupo máximo.
- `created_by`: docente creador, nullable y con `nullOnDelete`.
- `started_at`, `finished_at`: marcas temporales de inicio y cierre.
- `current_turn_participation_id`: referencia lógica al turno, indexada.
- `winner_participation_id`: referencia lógica al ganador, indexada.
- `result_type`: `winner`, `draw` o `collaborative_success`.
- `state`: JSON con el estado específico compartido del juego/ronda.

Los identificadores del turno y ganador no usan claves foráneas hacia `participations` para evitar una dependencia circular con `participations.game_session_id`.

### Relación con `participations`

La migración `2026_10_07_000002_add_game_session_id_to_participations_table.php` agrega `game_session_id` nullable e indexado, con clave foránea hacia `game_sessions` y `nullOnDelete`. Así se conservan las participaciones convencionales con `game_session_id = null`, mientras que las participaciones pertenecientes a una sala quedan identificadas por la sesión. La entidad Participation conserva `activity_id`, `student_id` o `team_id`, `attempt`, `status`, `score`, `completed_at` y `elapsed_seconds`.

Los estados de participación contemplan `waiting`, `started`, `completed`, `abandoned` y `expired`. En una sesión de equipo, una participación representa el progreso y puntaje común del equipo; no se crea una participación de juego por cada integrante conectado.

### `game_session_presences`

La migración `2026_10_16_000127_create_game_session_presences_table.php` agrega una fila de presencia por estudiante y sala, asociada a su participación. Incluye `status` (`active`/`left`), `joined_at` y `left_at`; la combinación `(game_session_id, student_id)` es única. Esta separación permite que el equipo tenga un resultado compartido, pero que el sistema sepa quién está conectado o salió. La política permite volver a entrar de acuerdo con el estado de la sesión y la participación compartida.

Las respuestas específicas siguen guardándose en las tablas propias de cada juego y referencian la participación. Por ello, las respuestas de una sala se separan de las de la actividad convencional a través de la participación asociada a `game_session_id`, sin cambiar el contrato de respuesta de cada juego.

## 42.5 Individual, equipo convencional y equipo multijugador

Conviene distinguir tres casos:

1. **Actividad individual convencional:** cada estudiante tiene su propia Participation para la actividad.
2. **Actividad convencional en modo equipo:** los miembros comparten los intentos del equipo. `TeamAttemptSelector` selecciona el mejor intento completado para resultados, ranking y reportes: mayor puntuación, luego menor tiempo y, si también empatan, intento más reciente/ID más reciente. Si no existe intento completado, conserva una salida terminal reciente; en las tablas docentes prioriza el intento activo.
3. **Partida multijugador por sala en modo equipo:** la participación común del equipo queda vinculada a una GameSession. Las presencias individuales permiten que más integrantes del equipo accedan a la sala sin crear resultados duplicados por integrante. El progreso y la puntuación se comparten por equipo.

En modo individual, cada participante de una sala mantiene su propia participación. Para ordenar resultados individuales se usa puntuación descendente y menor tiempo empleado como desempate cuando aplica. En los resultados por equipo se muestra una fila/resultado por participación de equipo, no una fila competitiva independiente por cada miembro.

## 42.6 Mecánica de carrera libre frente a rondas

### Carrera libre

Crucigrama, sopa de letras y Matching actualizan la participación que resuelve el juego. La respuesta se valida usando los servicios y modelos propios del juego, y se enlaza con el progreso de la participación en la sesión. La finalización debe ser idempotente: una actualización de estado no puede volver a otorgar puntuación ni cerrar dos veces la misma partida.

### Ruleta y Kahoot

El docente abre/avanza rondas. `GameSessionService` persiste en `state` el elemento o pregunta vigente, los identificadores ya utilizados, el inicio de la ronda, la duración, las respuestas y los respondedores de equipo. El endpoint de estado puede cerrar una ronda cuando se agota el tiempo o cuando todos los participantes esperados respondieron. La siguiente ronda requiere la acción de control docente.

En actividades de equipo, el sistema selecciona un respondedor por equipo para cada ronda y evita elegir repetidamente al mismo cuando hay otros integrantes disponibles. El estado que reciben los integrantes indica quién puede responder, si el equipo ya respondió y el resultado de esa respuesta; los demás pueden seguir la ronda como espectadores. Esta modalidad evita envíos múltiples para una sola respuesta de equipo.

## 42.7 Rutas y contratos HTTP

Las rutas están bajo el middleware de usuario autenticado y los grupos de rol existentes. Los nombres concretos deben consultarse con `php artisan route:list`; entre las rutas implementadas se encuentran:

| Acción | Nombre de ruta |
|---|---|
| Formulario para unirse | `student.game-sessions.join-page` |
| Unirse mediante código | `student.game-sessions.join` |
| Espera del estudiante | `student.game-sessions.show` |
| Abandonar sala | `student.game-sessions.leave` |
| Obtener estado compartido | `student.game-sessions.state` |
| Redirigir a la vista del juego | `student.game-sessions.play` |
| Resultados de la sala | `student.game-sessions.result` |
| Crear sala (docente) | `teacher.game-sessions.create` / `teacher.game-sessions.store` |
| Gestionar sala (docente) | `teacher.game-sessions.show` |
| Iniciar partida | `teacher.game-sessions.start` |
| Cancelar sala vacía | `teacher.game-sessions.cancel` |
| Resultados del docente | `teacher.game-sessions.result` |

Los controladores específicos agregan endpoints de respuesta y avance para las modalidades de cada juego. La ruta de juego central redirige por tipo a Ruleta, Kahoot, Crucigrama, Sopa de letras o Matching. Los campos JSON y parámetros deben conservarse; una mejora de interfaz no autoriza a cambiar payloads ni a alterar métodos HTTP.

## 42.8 Autorización y concurrencia

`GameSessionPolicy` verifica los permisos antes de operar: el docente dueño de la actividad puede gestionar/iniciar/avanzar/resultar; estudiantes con participación válida pueden ver, jugar y consultar su resultado. En modo equipo se comprueba que el estudiante pertenezca al equipo vinculado a la actividad y, donde aplica, que su presencia esté activa.

Las acciones sensibles de creación/ingreso, inicio, respuesta, finalización y cierre usan transacciones y bloqueos de fila donde se requiere serializar peticiones concurrentes. Esto reduce el riesgo de duplicar participaciones, exceder cupos, contestar dos veces la misma ronda o registrar dos veces una finalización. El comportamiento definitivo de bloqueo bajo carga debe validarse con el mismo motor de base de datos usado en producción; una suite con SQLite en memoria no certifica la concurrencia de MariaDB.

## 42.9 Canal de eventos, polling y timeouts

El canal privado `game-session.{id}` se autoriza mediante la política de vista de la sesión. Los eventos de dominio se publican después del commit de la transacción para que los clientes no reciban un cambio que luego se revierta. El frontend puede actualizar presencia, estado de juego, puntaje, rondas y finalización a partir de los eventos y del endpoint de estado.

La validación del cierre del módulo documentó el **polling** como fallback operativo disponible. No se certificó una prueba end-to-end con un servidor Reverb activo ni una prueba real de WebSocket. La presencia de eventos y de la autorización del canal no equivale por sí sola a verificar la infraestructura de broadcasting de un entorno.

Existe el comando Artisan `game-sessions:process-timeouts`, implementado en `app/Console/Commands/ProcessGameSessionTimeouts.php`, que recorre sesiones `playing` y solicita el cierre de rondas vencidas. Antes de producción se debe confirmar que el scheduler/cron del despliegue invoque el comando con la frecuencia deseada; el comando por sí solo no demuestra que esté programado.

## 42.10 Reingreso y cierre de salas sin uso

El panel docente contiene la lista **Salas activas** para recuperar una sesión propia que quedó abierta al perder la pantalla o la conexión. La lista filtra estados activos (en espera o en juego) y no mezcla sesiones de otros docentes.

Para una sala que nadie usó, la pantalla de gestión ofrece **Cerrar sala sin iniciar** únicamente cuando está en `waiting` y la lista de jugadores está vacía. El servidor verifica además que quien solicita sea el docente propietario, que no existan participaciones y que la sesión continúe esperando. `cancelWaitingSession` hace la comprobación bajo transacción y bloqueo de la fila para proteger la carrera entre cancelación e ingreso simultáneo. La sala pasa a `cancelled` y deja de aparecer en Salas activas. No se cancela una partida que ya tuvo participantes.

## 42.11 Datos de prueba

`database/seeders/GameTestingActivitySeeder.php` crea o actualiza actividades de prueba para los cinco juegos, tanto individuales como por equipos, asociadas a la clase de demostración `PROGWEB01`. Configura contenido base de cada juego y distribuye estudiantes disponibles en equipos para las actividades `team`. El seeder usa `updateOrCreate`, por lo que está pensado para facilitar repeticiones en el entorno local.

Antes de ejecutarlo, confirmar que existan la clase y los usuarios que el seeder espera. No es un seeder genérico para producción ni debe asumirse que crea cuentas o clases ausentes. Los cambios de datos generados deben probarse en una base local o de pruebas.

## 42.12 Vistas y experiencia multijugador

Las vistas compartidas cubren:

- Formulario del estudiante para ingresar el código.
- Sala de espera del estudiante con código, participantes y estado.
- Creación y configuración docente de una sala.
- Pantalla docente de gestión, inicio, avance de ronda y resultados.
- Resultados del estudiante y clasificación de participantes.
- Vistas de juego específicas de cada uno de los cinco juegos.

Las mejoras visuales conservaron la separación entre pantallas offline y multijugador cuando las mecánicas difieren, los controles reales del juego, el envío de formularios/respuestas y la información que cada tipo de juego muestra en resultados (por ejemplo, palabras, parejas o preguntas). Las vistas no deben enviar respuestas por participantes duplicados cuando el estado y el puntaje son compartidos por equipo.

## 42.13 Pruebas y límites de verificación

El cierre documentado el 15 de octubre de 2026 reportó 373 pruebas aprobadas, 1 risky y 1607 aserciones. En la continuación posterior se añadió el cierre seguro de salas vacías y sus casos de autorización/estado; la suite completa reportada el 16 de octubre fue:

```text
376 pruebas aprobadas
1 prueba marcada risky
1624 aserciones
0 pruebas fallidas
```

La prueba marcada risky es el test existente `test_join_page_is_available_for_students`; PHPUnit no lo contó como fallo. También se reportaron satisfactorios Pint, lint PHP de los archivos del cierre, compilación de vistas con `VIEW_COMPILED_PATH=/tmp/klassio-compiled-views`, validación de Composer y `git diff --check`.

El usuario confirmó manualmente las fases 8 y 9 del roadmap antes del cierre. No se repitió una inspección manual de navegador en la última tarea de cierre. Las limitaciones que deben conservarse explícitas en cualquier entrega son:

- Las pruebas SQLite en memoria no prueban bloqueos ni concurrencia real de MariaDB.
- No se informó una prueba end-to-end con Reverb activo.
- No se ejecutó build de Vite/npm en el checkout citado, que no contenía `package.json`.
- Las pruebas automatizadas no sustituyen una prueba de desconexión/reingreso bajo carga real.

## 42.14 Estado del repositorio al ampliar esta documentación

La inspección de esta ampliación encontró el checkout en la rama `develop`, con `HEAD` en `7c83074` (`refactory: views update multiplayers pretty 2`) y sin cambios locales reportados por `git status --short`. Esta observación describe el checkout inspeccionado, no altera el historial temporal del README de cierre del 15 de octubre, que reportaba que no se había realizado merge en ese momento.

No se realizó commit, push ni modificación de código para crear esta documentación.
