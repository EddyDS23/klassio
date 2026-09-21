@include('errors.layout', [
    'code' => 401,
    'title' => 'No autorizado',
    'message' => 'Necesitas iniciar sesión para acceder a este recurso.'
])