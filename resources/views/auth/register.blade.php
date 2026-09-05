<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - AulaPlay</title>
</head>
<body>
    <h1>Registro</h1>

    @if($errors->any())
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="/register">
        @csrf
        <label>Nombre</label>
        <input type="text" name="name" value="{{ old('name') }}">

        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}">

        <label>Password</label>
        <input type="password" name="password">

        <label>Confirmar password</label>
        <input type="password" name="password_confirmation">

        <label>Rol</label>
        <select name="role">
            <option value="teacher" {{ old('role') == 'teacher' ? 'selected' : '' }}>Maestro</option>
            <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Estudiante</option>
        </select>

        <button type="submit">Registrarse</button>
    </form>

    <a href="/login">Iniciar sesión</a>
</body>
</html>