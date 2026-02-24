<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Miquna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #203363;
            --secondary-color: #ffa500;
        }

        body {
            background: linear-gradient(135deg, #203363 0%, #2a4480 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }

        .login-container {
            background-color: #ffffff;
            padding: 2.5rem;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 450px;
            animation: slideIn 0.4s ease-out;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .logo-section {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo-section img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-bottom: 1rem;
            box-shadow: 0 4px 12px rgba(32,51,99,0.2);
        }

        .logo-section h2 {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 0.5rem;
            font-size: 1.8rem;
        }

        .logo-section p {
            color: #6c757d;
            font-size: 0.9rem;
            margin: 0;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-group label i {
            color: var(--secondary-color);
        }

        .form-control,
        .form-select {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(32,51,99,0.1);
            outline: none;
        }

        .form-control.is-invalid,
        .form-select.is-invalid {
            border-color: #dc3545;
        }

        .form-control::placeholder {
            color: #adb5bd;
        }

        /* ── Wrapper para el toggle de contraseña ── */
        .password-wrapper {
            position: relative;
        }

        .password-wrapper .form-control {
            padding-right: 3rem;
        }

        .password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            transition: color 0.2s;
            z-index: 5;
        }

        .password-toggle:hover {
            color: var(--primary-color);
        }

        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, var(--primary-color) 0%, #2a4480 100%);
            color: #ffffff;
            border: none;
            padding: 0.85rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            margin-top: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-login:hover:not(:disabled) {
            background: linear-gradient(135deg, var(--secondary-color) 0%, #ff8c00 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255,165,0,0.3);
            color: #ffffff;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .alert {
            border-radius: 10px;
            border: none;
            margin-bottom: 1.5rem;
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }

        .alert-danger  { background-color: #fff5f5; color: #c53030; border-left: 4px solid #fc8181; }
        .alert-success { background-color: #f0fff4; color: #22543d; border-left: 4px solid #68d391; }
        .alert-warning { background-color: #fffbeb; color: #744210; border-left: 4px solid #f6ad55; }

        .alert ul {
            margin: 0;
            padding-left: 1.2rem;
        }

        .info-text {
            text-align: center;
            color: #6c757d;
            font-size: 0.85rem;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e9ecef;
        }

        @media (max-width: 576px) {
            .login-container { padding: 2rem 1.5rem; }
            .logo-section h2 { font-size: 1.5rem; }
        }
    </style>
</head>
<body>
<div class="login-container">

    {{-- Logo --}}
    <div class="logo-section">
        <img src="https://static.vecteezy.com/system/resources/previews/000/656/554/original/restaurant-badge-and-logo-good-for-print-vector.jpg"
             alt="Logo Miquna">
        <h2>Miquna POS</h2>
        <p>Sistema de Punto de Venta</p>
    </div>

    {{-- Errores generales --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <div class="d-flex align-items-center mb-2">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>Error de Validación</strong>
            </div>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('status'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('status') }}
        </div>
    @endif

    @if (session('warning'))
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('warning') }}
        </div>
    @endif

    <form action="{{ route('login') }}" method="POST" id="loginForm" novalidate>
        @csrf

        {{-- Sucursal --}}
        <div class="form-group">
            <label for="branch_id">
                <i class="fas fa-store"></i> Sucursal
            </label>
            <select name="branch_id"
                    id="branch_id"
                    class="form-select @error('branch_id') is-invalid @enderror"
                    required>
                <option value="" disabled selected>Seleccione una sucursal</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}"
                        {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                        {{ $branch->name }}
                        @if($branch->city) - {{ $branch->city }} @endif
                        @if($branch->is_main) ⭐ Principal @endif
                    </option>
                @endforeach
            </select>
            @error('branch_id')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        {{-- Email --}}
        <div class="form-group">
            <label for="email">
                <i class="fas fa-envelope"></i> Correo Electrónico
            </label>
            <input type="email"
                   name="email"
                   id="email"
                   class="form-control @error('email') is-invalid @enderror"
                   placeholder="correo@ejemplo.com"
                   value="{{ old('email') }}"
                   required
                   autocomplete="email"
                   autofocus>
            @error('email')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        {{-- Contraseña --}}
        <div class="form-group">
            <label for="password">
                <i class="fas fa-lock"></i> Contraseña
            </label>
            <div class="password-wrapper">
                <input type="password"
                       name="password"
                       id="password"
                       class="form-control @error('password') is-invalid @enderror"
                       placeholder="Ingrese su contraseña"
                       required
                       autocomplete="current-password">
                <i class="fas fa-eye password-toggle"
                   id="togglePassword"
                   title="Mostrar/Ocultar contraseña"></i>
            </div>
            @error('password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-login" id="submitBtn">
            <i class="fas fa-sign-in-alt" id="submitIcon"></i>
            <span id="submitText">Iniciar Sesión</span>
        </button>
    </form>

    <div class="info-text">
        <i class="fas fa-shield-alt me-1"></i>
        Sistema seguro y confiable
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {

        // ── Referencias ──────────────────────────────────────────────────
        const loginForm     = document.getElementById('loginForm');
        const submitBtn     = document.getElementById('submitBtn');
        const submitIcon    = document.getElementById('submitIcon');
        const submitText    = document.getElementById('submitText');
        const branchSelect  = document.getElementById('branch_id');
        const emailInput    = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const toggleBtn     = document.getElementById('togglePassword');

        // ── Foco inicial inteligente ─────────────────────────────────────
        if (!branchSelect.value) {
            branchSelect.focus();
        } else if (!emailInput.value) {
            emailInput.focus();
        }

        // ── Toggle visibilidad contraseña ────────────────────────────────
        toggleBtn.addEventListener('click', function () {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            this.classList.toggle('fa-eye',      !isPassword);
            this.classList.toggle('fa-eye-slash', isPassword);
        });

        // ── Submit único: validación + estado de carga ───────────────────
        loginForm.addEventListener('submit', function (e) {

            // Validación client-side antes de enviar
            if (!branchSelect.value || !emailInput.value.trim() || !passwordInput.value) {
                e.preventDefault();
                alert('Por favor, complete todos los campos requeridos.');
                return;
            }

            // Activar estado de carga
            submitBtn.disabled    = true;
            submitIcon.className  = 'fas fa-spinner fa-spin';
            submitText.textContent = 'Iniciando...';
        });

    });
</script>
</body>
</html>