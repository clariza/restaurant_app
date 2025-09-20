<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
      @vite(['resources/css/app.css'])
    <style>
        body {
            background-color: #203363; /* Color de fondo igual al botón */
            color: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        .form-control {
            margin-bottom: 15px;
        }
        .btn-login {
            width: 100%;
            background-color: #203363;
            color: #ffffff;
        }
        .btn-login:hover {
            background-color: #ffa500;
            color: #203363;
        }
        .alert {
            margin-bottom: 20px;
        }
        h2 {
            color: #203363;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .form-group label {
            color: #203363;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Iniciar Sesión</h2>

        <!-- Mostrar errores de validación -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Mostrar mensajes de éxito o error -->
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        <!-- Formulario de login -->
        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="Ingresa tu correo" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Ingresa tu contraseña" required>
            </div>
            <button type="submit" class="btn btn-login">Iniciar Sesión</button>
        </form>

        <!-- Enlace para registrarse (opcional) -->
        <div class="text-center mt-3">
        </div>
    </div>

    <!-- Bootstrap JS (opcional, si necesitas funcionalidades de Bootstrap) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>