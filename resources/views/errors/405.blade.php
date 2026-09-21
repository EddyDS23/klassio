@include('errors.layout', [
    'code' => 405,
    'title' => 'Método no permitido',
    'message' => 'El método utilizado para realizar esta solicitud no está permitido.'
])