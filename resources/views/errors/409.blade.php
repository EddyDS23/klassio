@include('errors.layout', [
    'code' => 409,
    'title' => 'Conflicto',
    'message' => 'La solicitud no pudo completarse debido a un conflicto.'
])