<?php

namespace Database\Seeders;

/**
 * Contenido demo por materia: unidades, tipos de juego por unidad, y datos
 * para crear cada juego. DemoGameSeeder usa esta clase para poblar las
 * actividades de cada clase.
 *
 * @phpstan-type Content array{
 *   description: string,
 *   units: list<string>,
 *   rotation: int,
 *   wordsearch: list<string>,
 *   crossword: list<array{0: string, 1: string}>,
 *   matching: list<array{0: string, 1: string}>,
 *   kahoot: list<array{q: string, a: string, wrong: list<string>}>,
 *   roulette: list<array{q: string, options: array{0: string, 1: string, 2: string, 3: string}, correct: string}>
 * }
 */
final class DemoGameCatalog
{
    /**
     * @var array<string, Content>
     */
    private static ?array $cache = null;

    /**
     * Devuelve el contenido de una materia.
     *
     * @return Content
     */
    public static function subject(string $key): array
    {
        return self::all()[$key] ?? throw new \InvalidArgumentException("Materia desconocida: {$key}");
    }

    /**
     * @return list<string>
     */
    public static function keys(): array
    {
        return array_keys(self::all());
    }

    /**
     * @return array<string, Content>
     */
    private static function all(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        self::$cache = self::fresh();

        return self::$cache;
    }

    /**
     * Datos reales por materia (simplificados al caso demo).
     *
     * @return array<string, Content>
     */
    private static function fresh(): array
    {
        return [
            'matematicas' => [
                'description' => 'Operamos, medimos y resolvemos problemas de la vida diaria.',
                'units' => ['Números naturales', 'Fracciones y decimales', 'Geometría básica'],
                'rotation' => 0,
                'wordsearch' => ['SUMA', 'RESTA', 'DIVISION', 'FRACCION', 'GEOMETRIA', 'NUMERO'],
                'crossword' => [
                    ['NUMERO', 'Representa una cantidad'],
                    ['CERO', 'Elemento neutro de la suma'],
                    ['ANGULO', 'Espacio entre dos rectas'],
                    ['AREA', 'Medida de una superficie'],
                ],
                'matching' => [
                    ['Suma', 'Juntar dos cantidades'],
                    ['Resta', 'Quitar de una cantidad'],
                    ['Perímetro', 'Contorno de una figura'],
                    ['Área', 'Superficie de una figura'],
                    ['Fracción', 'Parte de un entero'],
                ],
                'kahoot' => [
                    ['q' => '¿Cuánto es 12 × 8?', 'a' => '96', 'wrong' => ['86', '104', '92']],
                    ['q' => '¿Cuánto es 45 ÷ 9?', 'a' => '5', 'wrong' => ['6', '4', '8']],
                    ['q' => '¿Cómo se llama la parte de arriba de una fracción?', 'a' => 'Numerador', 'wrong' => ['Denominador', 'Dividendo', 'Cociente']],
                    ['q' => '¿Cuántos grados tiene un ángulo recto?', 'a' => '90', 'wrong' => ['180', '45', '360']],
                ],
                'roulette' => [
                    ['q' => '¿Cuánto es 7 × 6?', 'options' => ['42', '48', '36', '45'], 'correct' => 'a'],
                    ['q' => '¿Cuánto es 100 − 37?', 'options' => ['63', '53', '73', '67'], 'correct' => 'a'],
                    ['q' => '¿Cuántos lados tiene un pentágono?', 'options' => ['5', '6', '4', '7'], 'correct' => 'a'],
                    ['q' => '¿Cuánto es la mitad de 90?', 'options' => ['45', '40', '50', '35'], 'correct' => 'a'],
                ],
            ],
            'lengua' => [
                'description' => 'Escribimos mejor, leemos más y ganamos ortografía.',
                'units' => ['Ortografía', 'Gramática', 'Tipos de texto'],
                'rotation' => 1,
                'wordsearch' => ['SUSTANTIVO', 'VERBO', 'ADJETIVO', 'ORACION', 'TILDE', 'PALABRA'],
                'crossword' => [
                    ['VERBO', 'Palabra que expresa acción'],
                    ['SUSTANTIVO', 'Nombra personas, lugares o cosas'],
                    ['SILABA', 'Golpe de voz de una palabra'],
                    ['PUNTO', 'Marca el fin de la oración'],
                ],
                'matching' => [
                    ['Sustantivo', 'Mesa, escuela, perro'],
                    ['Verbo', 'Correr, leer, saltar'],
                    ['Adjetivo', 'Grande, azul, rápido'],
                    ['Pronombre', 'Yo, tú, él'],
                    ['Conjunción', 'Y, o, pero'],
                ],
                'kahoot' => [
                    ['q' => '¿Cuál de estas palabras es sustantivo?', 'a' => 'Escuela', 'wrong' => ['Correr', 'Bello', 'Rápido']],
                    ['q' => '¿Qué palabra lleva tilde?', 'a' => 'Café', 'wrong' => ['Casa', 'Sol', 'Pan']],
                    ['q' => '¿Cuántas sílabas tiene "mariposa"?', 'a' => '4', 'wrong' => ['3', '2', '5']],
                    ['q' => '¿Qué texto cuenta una historia?', 'a' => 'Narrativo', 'wrong' => ['Instructivo', 'Publicitario', 'Descriptivo']],
                ],
                'roulette' => [
                    ['q' => '¿Qué signo termina una pregunta?', 'options' => ['?', '!', '.', ','], 'correct' => 'a'],
                    ['q' => '"Rápidamente" es un…', 'options' => ['adverbio', 'sustantivo', 'verbo', 'adjetivo'], 'correct' => 'a'],
                    ['q' => '¿Cuántas sílabas tiene "elefante"?', 'options' => ['4', '3', '2', '5'], 'correct' => 'a'],
                    ['q' => '¿Qué separa sílabas dentro de la palabra?', 'options' => ['El guion', 'La tilde', 'La coma', 'El punto y coma'], 'correct' => 'a'],
                ],
            ],
            'ciencias' => [
                'description' => 'Exploramos el cuerpo, los seres vivos y su ambiente.',
                'units' => ['El cuerpo humano', 'Plantas y animales', 'Ecosistemas'],
                'rotation' => 2,
                'wordsearch' => ['CELULA', 'CORAZON', 'HUESO', 'PLANTA', 'ECOSISTEMA', 'SEMILLA'],
                'crossword' => [
                    ['CELULA', 'Unidad básica de la vida'],
                    ['CORAZON', 'Órgano que bombea la sangre'],
                    ['HUESO', 'Estructura del esqueleto'],
                    ['HOJA', 'Lugar de la fotosíntesis'],
                ],
                'matching' => [
                    ['Corazón', 'Bombea la sangre'],
                    ['Pulmones', 'Capturan oxígeno'],
                    ['Raíz', 'Fija la planta y absorbe agua'],
                    ['Estómago', 'Digiere los alimentos'],
                    ['Hoja', 'Lugar de la fotosíntesis'],
                ],
                'kahoot' => [
                    ['q' => '¿Qué órgano bombea la sangre?', 'a' => 'Corazón', 'wrong' => ['Pulmón', 'Estómago', 'Hígado']],
                    ['q' => '¿Qué producen las plantas con la luz?', 'a' => 'Oxígeno', 'wrong' => ['Dióxido de azufre', 'Agua salada', 'Barro']],
                    ['q' => '¿Cuántas extremidades tiene el ser humano?', 'a' => '4', 'wrong' => ['2', '6', '8']],
                    ['q' => '¿Qué es un herbívoro?', 'a' => 'Animal que come plantas', 'wrong' => ['Animal que come carne', 'Animal que come todo', 'Animal que no come']],
                ],
                'roulette' => [
                    ['q' => '¿Qué parte de la planta absorbe agua?', 'options' => ['La raíz', 'La hoja', 'La flor', 'El fruto'], 'correct' => 'a'],
                    ['q' => '¿Con qué sistema respiramos?', 'options' => ['Respiratorio', 'Digestivo', 'Nervioso', 'Óseo'], 'correct' => 'a'],
                    ['q' => '¿Qué animal es mamífero?', 'options' => ['El delfín', 'El tiburón', 'La serpiente', 'La rana'], 'correct' => 'a'],
                    ['q' => '¿Qué gas capturan las plantas para fotosintetizar?', 'options' => ['CO₂', 'O₂', 'N₂', 'Helio'], 'correct' => 'a'],
                ],
            ],
            'historia' => [
                'description' => 'Recorremos las grandes épocas de la humanidad.',
                'units' => ['América antigua', 'Culturas del mundo', 'Independencia'],
                'rotation' => 3,
                'wordsearch' => ['MAYAS', 'AZTECAS', 'INCAS', 'PIRAMIDE', 'IMPERIO', 'ESCUDO'],
                'crossword' => [
                    ['MAYAS', 'Cultura de la península de Yucatán'],
                    ['INCAS', 'Imperio andino del Tahuantinsuyo'],
                    ['PIRAMIDE', 'Construcción de los egipcios'],
                    ['IMPERIO', 'Estado gobernado por un emperador'],
                ],
                'matching' => [
                    ['Mayas', 'Calendario y glifos'],
                    ['Egipcios', 'Piramides y jeroglíficos'],
                    ['Romanos', 'Calzadas y legiones'],
                    ['Griegos', 'Democracia y filosofía'],
                    ['Aztecas', 'Tenochtitlán y chinampas'],
                ],
                'kahoot' => [
                    ['q' => '¿Qué cultura construyó las piramides de Giza?', 'a' => 'Egipcia', 'wrong' => ['Maya', 'Romana', 'China']],
                    ['q' => '¿Qué imperio dominó el Mediterráneo desde Roma?', 'a' => 'Romano', 'wrong' => ['Persa', 'Azteca', 'Vikingo']],
                    ['q' => '¿Qué cultura se asentó en Mesoamérica?', 'a' => 'Maya', 'wrong' => ['Egipcia', 'Griega', 'Celta']],
                    ['q' => '¿Quién escribía con glifos en su calendario?', 'a' => 'Los mayas', 'wrong' => ['Los griegos', 'Los romanos', 'Los vikingos']],
                ],
                'roulette' => [
                    ['q' => '¿Dónde estaba el imperio inca?', 'options' => ['Los Andes', 'Mesoamérica', 'El Nilo', 'Mesopotamia'], 'correct' => 'a'],
                    ['q' => '¿Qué construyeron los romanos para el agua?', 'options' => ['Acueductos', 'Aviones', 'Bombillas', 'Imprenta'], 'correct' => 'a'],
                    ['q' => '¿Qué cultura usaba jeroglíficos?', 'options' => ['Egipcia', 'Romana', 'Griega', 'Inca'], 'correct' => 'a'],
                    ['q' => '¿Cuál fue una gran ciudad azteca?', 'options' => ['Tenochtitlán', 'Atenas', 'Roma', 'Babilonia'], 'correct' => 'a'],
                ],
            ],
            'geografia' => [
                'description' => 'Conocemos el mundo: continentes, climas y recursos.',
                'units' => ['Continentes y océanos', 'Climas y mapas', 'Recursos naturales'],
                'rotation' => 4,
                'wordsearch' => ['MAPA', 'CONTINENTE', 'OCEANO', 'CLIMA', 'MONTANA', 'RIO'],
                'crossword' => [
                    ['MAPA', 'Representación del territorio'],
                    ['OCEANO', 'Gran masa de agua salada'],
                    ['CLIMA', 'Estado del tiempo habitual'],
                    ['MONTANA', 'Gran elevación del terreno'],
                ],
                'matching' => [
                    ['Océano Pacífico', 'Entre Asia y América'],
                    ['Ecuador', 'Línea imaginaria del planeta'],
                    ['Desierto', 'Clima seco'],
                    ['Mediterráneo', 'Mar entre Europa y África'],
                    ['Brújula', 'Instrumento para orientarse'],
                ],
                'kahoot' => [
                    ['q' => '¿Cuántos continentes hay?', 'a' => '6', 'wrong' => ['5', '7', '4']],
                    ['q' => '¿Cuál es el río más caudaloso?', 'a' => 'El Amazonas', 'wrong' => ['El Támesis', 'El Rin', 'El Volga']],
                    ['q' => '¿En qué continente está Chile?', 'a' => 'América', 'wrong' => ['Europa', 'Asia', 'África']],
                    ['q' => '¿Cuál es el océano más grande?', 'a' => 'Pacífico', 'wrong' => ['Atlántico', 'Índico', 'Ártico']],
                ],
                'roulette' => [
                    ['q' => '¿Instrumento para orientarse?', 'options' => ['La brújula', 'El termómetro', 'El reloj', 'La balanza'], 'correct' => 'a'],
                    ['q' => '¿Qué país está en Asia?', 'options' => ['Japón', 'Chile', 'Italia', 'Cuba'], 'correct' => 'a'],
                    ['q' => '¿Qué clima tiene el desierto?', 'options' => ['Seco', 'Lluvioso', 'Nevado', 'Templado'], 'correct' => 'a'],
                    ['q' => '¿Qué muestra un mapa físico?', 'options' => ['Relieve del terreno', 'Internet', 'Edificios', 'El clima actual'], 'correct' => 'a'],
                ],
            ],
            'programacion' => [
                'description' => 'Aprendemos a crear páginas web desde cero.',
                'units' => ['Introducción al desarrollo web', 'HTML y CSS', 'JavaScript', 'PHP y bases de datos'],
                'rotation' => 2,
                'wordsearch' => ['HTML', 'CSS', 'JAVASCRIPT', 'LARAVEL', 'ETIQUETA', 'NAVEGADOR'],
                'crossword' => [
                    ['HTML', 'Lenguaje de estructura web'],
                    ['CSS', 'Lenguaje de estilos'],
                    ['PHP', 'Lenguaje del servidor'],
                    ['NAVEGADOR', 'Programa para ver páginas web'],
                ],
                'matching' => [
                    ['HTML', 'Estructura de una página'],
                    ['CSS', 'Estilos visuales'],
                    ['JavaScript', 'Interactividad'],
                    ['Laravel', 'Framework de PHP'],
                    ['MySQL', 'Base de datos'],
                ],
                'kahoot' => [
                    ['q' => '¿Qué lenguaje estructura una página web?', 'a' => 'HTML', 'wrong' => ['CSS', 'SQL', 'PHP']],
                    ['q' => '¿Qué lenguaje estiliza la página?', 'a' => 'CSS', 'wrong' => ['HTML', 'SQL', 'Go']],
                    ['q' => '¿Qué lenguaje se ejecuta en el navegador?', 'a' => 'JavaScript', 'wrong' => ['HTML', 'CSS', 'Python']],
                    ['q' => '¿En qué lenguaje está escrito Laravel?', 'a' => 'PHP', 'wrong' => ['Java', 'C#', 'Ruby']],
                ],
                'roulette' => [
                    ['q' => '¿Qué etiqueta contiene el título principal?', 'options' => ['<h1>', '<p>', '<img>', '<br>'], 'correct' => 'a'],
                    ['q' => '¿Propiedad CSS para cambiar el color del texto?', 'options' => ['color', 'font-size', 'margin', 'display'], 'correct' => 'a'],
                    ['q' => '¿Qué palabra declara una constante en JavaScript?', 'options' => ['const', 'let', 'var', 'static'], 'correct' => 'a'],
                    ['q' => '¿Comandos de Laravel: ¿cuál sirve para servir?', 'options' => ['php artisan serve', 'npm run build', 'composer update', 'php artisan migrate'], 'correct' => 'a'],
                ],
            ],
            'ingles' => [
                'description' => 'Construimos vocabulario y frases para comunicarnos.',
                'units' => ['Vocabulario esencial', 'Verbos comunes', 'Frases de contacto'],
                'rotation' => 1,
                'wordsearch' => ['HOUSE', 'GREEN', 'TEACHER', 'FRIEND', 'SUMMER', 'FAMILY'],
                'crossword' => [
                    ['TEACHER', 'Persona que enseña (inglés)'],
                    ['FRIEND', 'Compañero de confianza (inglés)'],
                    ['HOUSE', 'Lugar para vivir (inglés)'],
                    ['GREEN', 'Color de las plantas (inglés)'],
                ],
                'matching' => [
                    ['Hello', 'Hola'],
                    ['Thank you', 'Gracias'],
                    ['Family', 'Familia'],
                    ['Library', 'Biblioteca'],
                    ['Breakfast', 'Desayuno'],
                ],
                'kahoot' => [
                    ['q' => '¿Cómo dices "maestro" en inglés?', 'a' => 'Teacher', 'wrong' => ['Doctor', 'Worker', 'Farmer']],
                    ['q' => '¿Qué significa "library"?', 'a' => 'Biblioteca', 'wrong' => ['Librería', 'Laboratorio', 'Auditorio']],
                    ['q' => '¿Cómo dices "gracias" en inglés?', 'a' => 'Thank you', 'wrong' => ['Please', 'Sorry', 'Welcome']],
                    ['q' => '¿Qué significa "green"?', 'a' => 'Verde', 'wrong' => ['Gris', 'Azul', 'Rosado']],
                ],
                'roulette' => [
                    ['q' => '¿Cómo preguntas por la hora?', 'options' => ['What time is it?', 'Who are you?', 'Where is he?', 'How old is it?'], 'correct' => 'a'],
                    ['q' => '¿Cómo se dice "perro" en inglés?', 'options' => ['Dog', 'Cat', 'Cow', 'Bird'], 'correct' => 'a'],
                    ['q' => 'Forma correcta de "to be" en primera persona:', 'options' => ['I am', 'I is', 'I are', 'I be'], 'correct' => 'a'],
                    ['q' => '¿Cómo se dice "escuela" en inglés?', 'options' => ['School', 'Store', 'Hospital', 'Station'], 'correct' => 'a'],
                ],
            ],
            'civica' => [
                'description' => 'Conocemos nuestros derechos, deberes y la vida en comunidad.',
                'units' => ['Democracia', 'Derechos y deberes', 'Comunidad'],
                'rotation' => 3,
                'wordsearch' => ['VOTO', 'DEBERES', 'DERECHOS', 'DEMOCRACIA', 'LEYES', 'PAIS'],
                'crossword' => [
                    ['VOTO', 'Decisión expresada en urnas'],
                    ['LEYES', 'Normas que regulan la sociedad'],
                    ['DERECHO', 'Facultad protegida por la ley'],
                    ['CIUDAD', 'Conjunto de barrios con gobierno local'],
                ],
                'matching' => [
                    ['Votar', 'Elegir representantes'],
                    ['Ley', 'Norma de convivencia'],
                    ['Municipalidad', 'Gobierno de la ciudad'],
                    ['Bandera', 'Símbolo patrio'],
                    ['Respeto', 'Trato adecuado a otros'],
                ],
                'kahoot' => [
                    ['q' => '¿Qué es la democracia?', 'a' => 'Gobierno del pueblo', 'wrong' => ['Gobierno de uno solo', 'Gobierno militar', 'Gobierno del rey']],
                    ['q' => '¿Qué es un derecho?', 'a' => 'Facultad protegida por la ley', 'wrong' => ['Un castigo', 'Una tarea', 'Un premio']],
                    ['q' => '¿Para qué sirve el voto?', 'a' => 'Elegir representantes', 'wrong' => ['Cobrar impuestos', 'Viajar', 'Aprobar materias']],
                    ['q' => '¿Qué es un deber ciudadano?', 'a' => 'Respetar las leyes', 'wrong' => ['Dormir temprano', 'Ir de compras', 'Ver televisión']],
                ],
                'roulette' => [
                    ['q' => '¿Qué documento recoge nuestros derechos?', 'options' => ['La Constitución', 'El calendario', 'La agenda', 'El periódico'], 'correct' => 'a'],
                    ['q' => '¿Quién gobierna el municipio?', 'options' => ['El alcalde', 'El rector', 'El capitán', 'El director'], 'correct' => 'a'],
                    ['q' => 'Significado de "convivir":', 'options' => ['Vivir con otros en armonía', 'Vivir solo', 'Viajar lejos', 'Competir siempre'], 'correct' => 'a'],
                    ['q' => '¿Qué es una norma?', 'options' => ['Regla de convivencia', 'Un juego', 'Una materia', 'Un premio'], 'correct' => 'a'],
                ],
            ],
        ];
    }
}
