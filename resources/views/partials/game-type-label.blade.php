{{-- Etiqueta amigable del tipo de juego (solo presentación).
     Los identificadores internos (word_search, kahoot…) NO cambian:
     este partial solo traduce el valor mostrado en pantallas. --}}
@php
    $gameTypeLabels = [
        'word_search' => 'Sopa de letras',
        'crossword' => 'Crucigrama',
        'roulette' => 'Ruleta',
        'matching' => 'Unir conceptos',
        'kahoot' => 'Kahoot',
    ];

    $gameTypeLabel = $gameTypeLabels[$type] ?? $type;
@endphp

{{ $gameTypeLabel }}
