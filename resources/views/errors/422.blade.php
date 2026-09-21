@include('errors.layout', [
    'code' => 422,
    'title' => 'Solicitud no válida',
    'message' => 'Los datos enviados no pudieron ser procesados.'
])