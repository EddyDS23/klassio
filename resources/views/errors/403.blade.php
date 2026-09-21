@include('errors.layout', [
    'code' => 403,
    'title' => 'Acceso denegado',
    'message' => 'No tienes permiso para acceder a este recurso.'
])