<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        {{ isset($crossword) ? 'Editar' : 'Configurar' }} crucigrama
    </title>
</head>

<body>

    <h1>
        {{ isset($crossword) ? 'Editar' : 'Configurar' }} crucigrama
    </h1>

    <h2>{{ $activity->title }}</h2>

    <p>
        Agrega las palabras que formarán parte del crucigrama.
        El sistema intentará cruzarlas automáticamente usando letras en común.
    </p>

    @if(session('success'))
        <p>{{ session('success') }}</p>
    @endif

    @if($errors->any())
        <div>
            <h2>Errores</h2>

            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $isEditing = isset($crossword);
    @endphp

    <form
        action="{{
            $isEditing
                ? route('teacher.crossword.update', $activity->id)
                : route('teacher.crossword.store', $activity->id)
        }}"
        method="POST"
    >

        @csrf

        @if($isEditing)
            @method('PUT')
        @endif

        <table border="1">
            <thead>
                <tr>
                    <th>Palabra</th>
                    <th>Pista</th>
                    <th>Puntuación</th>
                    <th>Acción</th>
                </tr>
            </thead>

            <tbody id="words-container">

                @if($isEditing && $words->count() > 0)

                    @foreach($words as $index => $word)
                        <tr class="word-row">

                            <td>
                                <input
                                    type="text"
                                    name="words[{{ $index }}][word]"
                                    value="{{ old(
                                        "words.$index.word",
                                        $word->word
                                    ) }}"
                                    maxlength="20"
                                    required
                                >
                            </td>

                            <td>
                                <input
                                    type="text"
                                    name="words[{{ $index }}][clue]"
                                    value="{{ old(
                                        "words.$index.clue",
                                        $word->clue
                                    ) }}"
                                    maxlength="255"
                                    required
                                >
                            </td>

                            <td>
                                <input
                                    type="number"
                                    name="words[{{ $index }}][score]"
                                    value="{{ old(
                                        "words.$index.score",
                                        $word->score
                                    ) }}"
                                    min="1"
                                    max="1000"
                                    required
                                >
                            </td>

                            <td>
                                <button type="button" onclick="removeRow(this)">
                                    Eliminar
                                </button>
                            </td>

                        </tr>
                    @endforeach

                @else

                    @for($i = 0; $i < 2; $i++)
                        <tr class="word-row">

                            <td>
                                <input
                                    type="text"
                                    name="words[{{ $i }}][word]"
                                    value="{{ old("words.$i.word") }}"
                                    maxlength="20"
                                    required
                                >
                            </td>

                            <td>
                                <input
                                    type="text"
                                    name="words[{{ $i }}][clue]"
                                    value="{{ old("words.$i.clue") }}"
                                    maxlength="255"
                                    required
                                >
                            </td>

                            <td>
                                <input
                                    type="number"
                                    name="words[{{ $i }}][score]"
                                    value="{{ old("words.$i.score", 100) }}"
                                    min="1"
                                    max="1000"
                                    required
                                >
                            </td>

                            <td>
                                <button type="button" onclick="removeRow(this)">
                                    Eliminar
                                </button>
                            </td>

                        </tr>
                    @endfor

                @endif

            </tbody>
        </table>

        <br>

        <button type="button" onclick="addRow()">
            Agregar palabra
        </button>

        <br>
        <br>

        <button type="submit">
            {{ $isEditing ? 'Actualizar crucigrama' : 'Guardar crucigrama' }}
        </button>

    </form>

    <hr>

    <nav>
        <a href="{{ route('teacher.activities.show', $activity->id) }}">
            Cancelar
        </a>

        <br>

        <a href="{{ route('teacher.activities.index', $activity->class_id) }}">
            Volver a actividades
        </a>
    </nav>

    <script>
        let rowIndex = {{ $isEditing ? $words->count() : 2 }};

        function addRow() {
            const container = document.getElementById('words-container');

            const row = document.createElement('tr');

            row.classList.add('word-row');

            row.innerHTML = `
                <td>
                    <input
                        type="text"
                        name="words[${rowIndex}][word]"
                        maxlength="20"
                        required
                    >
                </td>

                <td>
                    <input
                        type="text"
                        name="words[${rowIndex}][clue]"
                        maxlength="255"
                        required
                    >
                </td>

                <td>
                    <input
                        type="number"
                        name="words[${rowIndex}][score]"
                        value="100"
                        min="1"
                        max="1000"
                        required
                    >
                </td>

                <td>
                    <button type="button" onclick="removeRow(this)">
                        Eliminar
                    </button>
                </td>
            `;

            container.appendChild(row);

            rowIndex++;
        }

        function removeRow(button) {
            const rows = document.querySelectorAll('.word-row');

            if (rows.length <= 2) {
                alert('El crucigrama debe tener al menos 2 palabras.');
                return;
            }

            button.closest('tr').remove();
        }
    </script>

</body>

</html>

