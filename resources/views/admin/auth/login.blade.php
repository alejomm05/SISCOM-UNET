{{-- resources/views/admin/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Supervisor - SISCOM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #0D47A1;
            --secondary-color: #1976D2;
            --accent-color: #42A5F5;
            --background-color: #F8F9FA;
            --text-color: #263238;
            --hint-color: #78909C;
            --card-bg: #ffffff;
        }
        
        body {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000" opacity="0.05"><polygon fill="white" points="0,1000 1000,0 1000,1000"/><circle fill="white" cx="200" cy="200" r="150"/><rect fill="white" x="700" y="300" width="200" height="200"/></svg>');
            background-size: cover;
            z-index: 0;
        }

        .login-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 420px;
        }

        .login-card {
            background: var(--card-bg);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: none;
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            padding: 40px 30px 30px;
            text-align: center;
            color: white;
        }

        .logo-container {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .logo-icon {
            font-size: 2rem;
            color: var(--primary-color);
        }

        .app-name {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }

        .university-name {
            font-size: 0.9rem;
            opacity: 0.9;
            font-weight: 500;
        }

        .login-body {
            padding: 40px 30px 30px;
        }

        .form-label {
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 8px;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #fafbfc;
        }

        .form-control:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(66, 165, 245, 0.1);
            background: white;
        }

        .btn-login {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            color: white;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(13, 71, 161, 0.3);
        }

        .footer-info {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.9);
            padding: 12px 20px;
            border-radius: 12px;
            font-size: 0.85rem;
            color: var(--text-color);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            text-align: right;
        }

        .security-icon {
            position: absolute;
            opacity: 0.1;
            font-size: 8rem;
            z-index: 0;
        }

        .security-icon-1 {
            top: 10%;
            left: 10%;
            color: white;
        }

        .security-icon-2 {
            bottom: 10%;
            right: 10%;
            color: white;
        }

        .input-group {
            position: relative;
        }

        .input-icon {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--hint-color);
            z-index: 2;
        }

        @media (max-width: 576px) {
            .login-container {
                margin: 20px;
            }
            
            .footer-info {
                position: relative;
                bottom: auto;
                right: auto;
                margin-top: 30px;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <!-- Iconos de fondo decorativos -->
    <i class="fas fa-shield-alt security-icon security-icon-1"></i>
    <i class="fas fa-lock security-icon security-icon-2"></i>

    <div class="login-container">
        <div class="login-card">
            <!-- Header con logo y nombre -->
            <div class="login-header">
                <div class="logo-container">
                    <i class="fas fa-shield-alt logo-icon"></i>
                </div>
                <h1 class="app-name">SISCOM</h1>
                <p class="university-name">UNIVERSIDAD NACIONAL EXPERIMENTAL DEL TÁCHIRA</p>
                <p class="mb-0 mt-2 opacity-75">Sistema de Seguridad Integral</p>
            </div>

            <!-- Formulario de login -->
            <div class="login-body">
                <h4 class="text-center mb-4" style="color: var(--text-color);">Panel de Supervisión</h4>
                
                <form method="POST" action="{{ route('admin.login') }}">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <div class="input-group">
                            <input type="email" name="email" id="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email') }}" 
                                   placeholder="usuario@unet.edu.ve" 
                                   required autofocus>
                            <i class="fas fa-envelope input-icon"></i>
                            @error('email') 
                                <div class="invalid-feedback">{{ $message }}</div> 
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="password" class="form-label">Contraseña</label>
                        <div class="input-group">
                            <input type="password" name="password" id="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   placeholder="••••••••" 
                                   required>
                            <i class="fas fa-lock input-icon"></i>
                            @error('password') 
                                <div class="invalid-feedback">{{ $message }}</div> 
                            @enderror
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-login w-100">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Iniciar Sesión
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Solo se muestra si PANEL_DEMO_EMAIL/PANEL_DEMO_PASSWORD están definidas en .env (ver config/app.php) --}}
    @if (config('app.demo_login.email'))
    <div class="footer-info" style="margin-bottom: 70px;" >
        <div class="fw-bold">Email: {{ config('app.demo_login.email') }}</div>
        <div class="fw-bold">Contraseña: {{ config('app.demo_login.password') }}</div>
    </div>
    @endif
    <!-- Información del autor -->
    <div class="footer-info">
        <div class="fw-bold">Autor: Douglas Aliendres</div>
        <div class="text-muted">Materia: Programación II</div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>