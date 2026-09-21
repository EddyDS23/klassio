<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Error {{ $code }} - Klassio</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-gray-100 flex items-center justify-center">

    <div class="text-center px-6">

        <h1 class="text-6xl font-bold text-gray-800">
            {{ $code }}
        </h1>

        <h2 class="mt-4 text-2xl font-semibold text-gray-700">
            {{ $title }}
        </h2>

        <p class="mt-2 text-gray-500">
             {{ $message }}
        </p>

        <a
            href="{{ url('/') }}"
            class="inline-block mt-6 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
        >
            Volver al inicio
        </a>

    </div>

</body>
</html>