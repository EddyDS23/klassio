<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - AulaPlay</title>
</head>
<body>
    <h1>Iniciar sesión</h1>

    @if($errors->any())
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="/login">
        @csrf
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}">

        <label>Password</label>
        <input type="password" name="password">

        <button type="submit">Entrar</button>
    </form>

    <a href="/register">Registrarse</a>
</body>
</html>