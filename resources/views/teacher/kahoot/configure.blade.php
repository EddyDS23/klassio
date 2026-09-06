<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($kahoot) ? 'Editar' : 'Configurar' }} Kahoot — {{ $activity->title }}</title>
    <style>
        body { font-family: sans-serif; padding: 20px; max-width: 900px; margin: 0 auto; }
        .question-block {
            border: 1px solid #ccc;
            border-radius: 6px;
            padding: 16px;
            margin-bottom: 20px;
            background: #f9f9f9;
        }
        .question-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }
        .question-header h3 { margin: 0; }
        .options-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 10px;
        }
        .option-row {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 8px;
        }
        .option-row input[type="text"] { flex: 1; border: none; outline: none; font-size: 14px; }
        .option-row input[type="radio"] { width: 16px; height: 16px; cursor: pointer; }
        .option-correct { border-color: #10b981; background: #f0fdf4; }
        .field-row { margin-bottom: 10px; }
        .field-row label { display: block; font-size: 13px; color: #555; margin-bottom: 4px; }
        .field-row input { padding: 6px 8px; border: 1px solid #ccc; border-radius: 4px; width: 120px; }
        .btn { padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; }
        .btn-primary { background: #2563eb; color: #fff; }
        .btn-secondary { background: #e5e7eb; color: #111; }
        .btn-danger { background: #ef4444; color: #fff; }
        .btn-add-option { background: #f3f4f6; border: 1px dashed #aaa; color: #555; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 13px; }
        .errors { background: #fee2e2; border: 1px solid #ef4444; padding: 12px; border-radius: 4px; margin-bottom: 16px; }
        .errors ul { margin: 0; padding-left: 16px; }
        .note { font-size: 12px; color: #888; margin-top: 4px; }
        .meta-row { display: flex; gap: 20px; margin-top: 10px; }
    </style>
</head>
<body>

<h1>{{ isset($kahoot) ? 'Editar' : 'Configurar' }} Kahoot</h1>
<h2 style="font-weight: normal; color: #555;">{{ $activity->title }}</h2>

@if (session('success'))
    <p style="color: green;">{{ session('success') }}</p>
@endif

@if ($errors->any())
    <div class="errors">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<p>Agrega las preguntas y sus opciones. Marca con el círculo cuál es la opción correcta.</p>
<p class="note">Cada pregunta necesita entre 2 y 4 opciones. Exactamente una debe ser correcta.</p>

@if (isset($kahoot))
    <form method="POST" action="{{ route('teacher.kahoot.update', $activity->id) }}" id="kahoot-form">
        @method('PUT')
@else
    <form method="POST" action="{{ route('teacher.kahoot.store', $activity->id) }}" id="kahoot-form">
@endif
    @csrf

    <div id="questions-container">

        @if (isset($questions) && $questions->count() > 0)

            @foreach ($questions as $qi => $question)
                <div class="question-block" id="question-block-{{ $qi }}">
                    <div class="question-header">
                        <h3>Pregunta <span class="q-number">{{ $qi + 1 }}</span></h3>
                        <button type="button" class="btn btn-danger" onclick="removeQuestion(this)">
                            Eliminar pregunta
                        </button>
                    </div>

                    <div class="field-row">
                        <label>Enunciado</label>
                        <input type="text"
                               name="questions[{{ $qi }}][question]"
                               value="{{ old("questions.$qi.question", $question->question) }}"
                               placeholder="¿Cuál es el ORM de Laravel?"
                               maxlength="255"
                               style="width: 100%;"
                               required>
                    </div>

                    <div class="meta-row">
                        <div class="field-row">
                            <label>Tiempo límite (segundos)</label>
                            <input type="number"
                                   name="questions[{{ $qi }}][time_limit]"
                                   value="{{ old("questions.$qi.time_limit", $question->time_limit) }}"
                                   min="5" max="120" required>
                        </div>
                        <div class="field-row">
                            <label>Puntaje</label>
                            <input type="number"
                                   name="questions[{{ $qi }}][score]"
                                   value="{{ old("questions.$qi.score", $question->score) }}"
                                   min="1" max="1000" required>
                        </div>
                    </div>

                    <p class="note">Opciones — marca el círculo de la respuesta correcta</p>

                    <div class="options-grid" id="options-{{ $qi }}">
                        @foreach ($question->options as $oi => $option)
                            <div class="option-row {{ $option->is_correct ? 'option-correct' : '' }}" id="option-{{ $qi }}-{{ $oi }}">
                                <input type="radio"
                                       name="questions[{{ $qi }}][correct]"
                                       value="{{ $oi }}"
                                       {{ $option->is_correct ? 'checked' : '' }}
                                       onchange="markCorrect(this, {{ $qi }})">
                                <input type="hidden"
                                       name="questions[{{ $qi }}][options][{{ $oi }}][is_correct]"
                                       value="{{ $option->is_correct ? '1' : '0' }}"
                                       class="is-correct-input">
                                <input type="text"
                                       name="questions[{{ $qi }}][options][{{ $oi }}][text]"
                                       value="{{ old("questions.$qi.options.$oi.text", $option->text) }}"
                                       placeholder="Opción {{ $oi + 1 }}"
                                       maxlength="255"
                                       required>
                                <button type="button" onclick="removeOption(this, {{ $qi }})" title="Eliminar">✕</button>
                            </div>
                        @endforeach
                    </div>

                    <button type="button" class="btn-add-option" style="margin-top: 8px;"
                            onclick="addOption({{ $qi }})">
                        + Agregar opción
                    </button>
                </div>
            @endforeach

        @else

            {{-- Bloque inicial vacío --}}
            <div class="question-block" id="question-block-0">
                <div class="question-header">
                    <h3>Pregunta <span class="q-number">1</span></h3>
                    <button type="button" class="btn btn-danger" onclick="removeQuestion(this)">
                        Eliminar pregunta
                    </button>
                </div>

                <div class="field-row">
                    <label>Enunciado</label>
                    <input type="text"
                           name="questions[0][question]"
                           value="{{ old('questions.0.question') }}"
                           placeholder="¿Cuál es el ORM de Laravel?"
                           maxlength="255"
                           style="width: 100%;"
                           required>
                </div>

                <div class="meta-row">
                    <div class="field-row">
                        <label>Tiempo límite (segundos)</label>
                        <input type="number" name="questions[0][time_limit]"
                               value="{{ old('questions.0.time_limit', 20) }}" min="5" max="120" required>
                    </div>
                    <div class="field-row">
                        <label>Puntaje</label>
                        <input type="number" name="questions[0][score]"
                               value="{{ old('questions.0.score', 100) }}" min="1" max="1000" required>
                    </div>
                </div>

                <p class="note">Opciones — marca el círculo de la respuesta correcta</p>

                <div class="options-grid" id="options-0">
                    <div class="option-row" id="option-0-0">
                        <input type="radio" name="questions[0][correct]" value="0" onchange="markCorrect(this, 0)">
                        <input type="hidden" name="questions[0][options][0][is_correct]" value="0" class="is-correct-input">
                        <input type="text" name="questions[0][options][0][text]" placeholder="Opción 1" maxlength="255" required>
                        <button type="button" onclick="removeOption(this, 0)" title="Eliminar">✕</button>
                    </div>
                    <div class="option-row" id="option-0-1">
                        <input type="radio" name="questions[0][correct]" value="1" onchange="markCorrect(this, 1)">
                        <input type="hidden" name="questions[0][options][1][is_correct]" value="0" class="is-correct-input">
                        <input type="text" name="questions[0][options][1][text]" placeholder="Opción 2" maxlength="255" required>
                        <button type="button" onclick="removeOption(this, 0)" title="Eliminar">✕</button>
                    </div>
                </div>

                <button type="button" class="btn-add-option" style="margin-top: 8px;" onclick="addOption(0)">
                    + Agregar opción
                </button>
            </div>

        @endif

    </div>

    <button type="button" class="btn btn-secondary" onclick="addQuestion()" style="margin-bottom: 20px;">
        + Agregar pregunta
    </button>

    <br>

    <button type="submit" class="btn btn-primary">
        {{ isset($kahoot) ? 'Actualizar Kahoot' : 'Guardar Kahoot' }}
    </button>

    <a href="{{ route('teacher.activities.show', $activity->id) }}" style="margin-left: 12px;">
        Cancelar
    </a>

</form>

<script>
    // Índices globales para evitar colisiones de nombres
    let questionIndex = {{ isset($questions) && $questions->count() > 0 ? $questions->count() : 1 }};

    // optionCount[qi] = número de opciones actuales para esa pregunta
    const optionCount = {};

    @if (isset($questions) && $questions->count() > 0)
        @foreach ($questions as $qi => $question)
            optionCount[{{ $qi }}] = {{ $question->options->count() }};
        @endforeach
    @else
        optionCount[0] = 2;
    @endif

    // -------------------------------------------------------------------------
    // Marcar opción correcta
    // -------------------------------------------------------------------------
    function markCorrect(radio, qi) {
        const grid = document.getElementById(`options-${qi}`);
        const rows = grid.querySelectorAll('.option-row');

        rows.forEach((row, i) => {
            const hidden = row.querySelector('.is-correct-input');
            const isSelected = (i === parseInt(radio.value));
            hidden.value = isSelected ? '1' : '0';
            row.classList.toggle('option-correct', isSelected);
        });
    }

    // -------------------------------------------------------------------------
    // Agregar opción a una pregunta
    // -------------------------------------------------------------------------
    function addOption(qi) {
        const grid = document.getElementById(`options-${qi}`);
        const currentCount = grid.querySelectorAll('.option-row').length;

        if (currentCount >= 4) {
            alert('Cada pregunta puede tener máximo 4 opciones.');
            return;
        }

        const oi = optionCount[qi] ?? currentCount;
        optionCount[qi] = oi + 1;

        const div = document.createElement('div');
        div.classList.add('option-row');
        div.id = `option-${qi}-${oi}`;

        div.innerHTML = `
            <input type="radio" name="questions[${qi}][correct]" value="${oi}" onchange="markCorrect(this, ${qi})">
            <input type="hidden" name="questions[${qi}][options][${oi}][is_correct]" value="0" class="is-correct-input">
            <input type="text" name="questions[${qi}][options][${oi}][text]"
                   placeholder="Opción ${currentCount + 1}" maxlength="255" required>
            <button type="button" onclick="removeOption(this, ${qi})" title="Eliminar">✕</button>
        `;

        grid.appendChild(div);
    }

    // -------------------------------------------------------------------------
    // Eliminar opción
    // -------------------------------------------------------------------------
    function removeOption(btn, qi) {
        const grid = document.getElementById(`options-${qi}`);
        const rows = grid.querySelectorAll('.option-row');

        if (rows.length <= 2) {
            alert('Cada pregunta necesita al menos 2 opciones.');
            return;
        }

        btn.closest('.option-row').remove();
        reindexOptions(qi);
    }

    // Reindexar los names de opciones después de eliminar una
    function reindexOptions(qi) {
        const grid    = document.getElementById(`options-${qi}`);
        const rows    = grid.querySelectorAll('.option-row');
        const wasCorrect = [];

        rows.forEach((row, i) => {
            const radio  = row.querySelector('input[type="radio"]');
            const hidden = row.querySelector('.is-correct-input');
            const text   = row.querySelector('input[type="text"]');
            const rmBtn  = row.querySelector('button');

            wasCorrect.push(hidden.value === '1');

            radio.name  = `questions[${qi}][correct]`;
            radio.value = i;
            radio.onchange = function() { markCorrect(this, qi); };

            hidden.name = `questions[${qi}][options][${i}][is_correct]`;
            text.name   = `questions[${qi}][options][${i}][text]`;
            text.placeholder = `Opción ${i + 1}`;

            rmBtn.onclick = function() { removeOption(this, qi); };
        });

        optionCount[qi] = rows.length;
    }

    // -------------------------------------------------------------------------
    // Agregar pregunta nueva
    // -------------------------------------------------------------------------
    function addQuestion() {
        const qi        = questionIndex;
        optionCount[qi] = 2;

        const container = document.getElementById('questions-container');
        const block     = document.createElement('div');
        block.classList.add('question-block');
        block.id = `question-block-${qi}`;

        block.innerHTML = `
            <div class="question-header">
                <h3>Pregunta <span class="q-number">${container.querySelectorAll('.question-block').length + 1}</span></h3>
                <button type="button" class="btn btn-danger" onclick="removeQuestion(this)">
                    Eliminar pregunta
                </button>
            </div>

            <div class="field-row">
                <label>Enunciado</label>
                <input type="text" name="questions[${qi}][question]"
                       placeholder="Escribe la pregunta..." maxlength="255"
                       style="width: 100%;" required>
            </div>

            <div class="meta-row">
                <div class="field-row">
                    <label>Tiempo límite (segundos)</label>
                    <input type="number" name="questions[${qi}][time_limit]"
                           value="20" min="5" max="120" required>
                </div>
                <div class="field-row">
                    <label>Puntaje</label>
                    <input type="number" name="questions[${qi}][score]"
                           value="100" min="1" max="1000" required>
                </div>
            </div>

            <p class="note">Opciones — marca el círculo de la respuesta correcta</p>

            <div class="options-grid" id="options-${qi}">
                <div class="option-row" id="option-${qi}-0">
                    <input type="radio" name="questions[${qi}][correct]" value="0" onchange="markCorrect(this, ${qi})">
                    <input type="hidden" name="questions[${qi}][options][0][is_correct]" value="0" class="is-correct-input">
                    <input type="text" name="questions[${qi}][options][0][text]" placeholder="Opción 1" maxlength="255" required>
                    <button type="button" onclick="removeOption(this, ${qi})" title="Eliminar">✕</button>
                </div>
                <div class="option-row" id="option-${qi}-1">
                    <input type="radio" name="questions[${qi}][correct]" value="1" onchange="markCorrect(this, ${qi})">
                    <input type="hidden" name="questions[${qi}][options][1][is_correct]" value="0" class="is-correct-input">
                    <input type="text" name="questions[${qi}][options][1][text]" placeholder="Opción 2" maxlength="255" required>
                    <button type="button" onclick="removeOption(this, ${qi})" title="Eliminar">✕</button>
                </div>
            </div>

            <button type="button" class="btn-add-option" style="margin-top: 8px;" onclick="addOption(${qi})">
                + Agregar opción
            </button>
        `;

        container.appendChild(block);
        questionIndex++;
        updateQuestionNumbers();
    }

    // -------------------------------------------------------------------------
    // Eliminar pregunta
    // -------------------------------------------------------------------------
    function removeQuestion(btn) {
        const container = document.getElementById('questions-container');

        if (container.querySelectorAll('.question-block').length <= 1) {
            alert('El Kahoot necesita al menos una pregunta.');
            return;
        }

        btn.closest('.question-block').remove();
        updateQuestionNumbers();
    }

    function updateQuestionNumbers() {
        const blocks = document.querySelectorAll('.question-block .q-number');
        blocks.forEach((el, i) => { el.textContent = i + 1; });
    }
</script>

</body>
</html>