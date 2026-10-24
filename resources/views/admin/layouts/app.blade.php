<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') | SISCOM UNET</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            /* Paleta de colores institucional */
            --primary-color: #0D47A1;
            --secondary-color: #1976D2;
            --accent-color: #42A5F5;
            --background-color: #F8F9FA;
            --text-primary: #263238;
            --text-secondary: #78909C;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --sidebar-width: 280px;
            
            /* Variables de diseño */
            --border-radius: 12px;
            --card-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            --sidebar-shadow: 4px 0 20px rgba(13, 71, 161, 0.1);
            --header-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }
        
        /* Estilos base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background-color: var(--background-color);
            font-family: 'Segoe UI', 'Inter', 'SF Pro Display', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-primary);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Sidebar profesional */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(165deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: var(--transition);
            z-index: 1000;
            box-shadow: var(--sidebar-shadow);
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            padding: 30px 25px 25px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
            background: rgba(0, 0, 0, 0.05);
        }

        .logo-container {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            transition: var(--transition);
        }

        .logo-container:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .logo-icon {
            font-size: 2.2rem;
            color: var(--primary-color);
        }

        .app-name {
            font-size: 1.6rem;
            font-weight: 800;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }

        .university-name {
            font-size: 0.8rem;
            opacity: 0.9;
            line-height: 1.4;
            font-weight: 500;
        }

        /* Menú de navegación */
        .sidebar-nav {
            flex: 1;
            padding: 25px 0;
        }

        .nav-section {
            margin-bottom: 10px;
        }

        .nav-section-title {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 0 25px 10px;
            opacity: 0.7;
            font-weight: 600;
        }

        .nav-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .nav-item {
            margin: 2px 15px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 14px 20px;
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            border-radius: var(--border-radius);
            transition: var(--transition);
            font-weight: 500;
            position: relative;
            overflow: hidden;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: white;
            transform: scaleY(0);
            transition: var(--transition);
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.12);
            color: white;
            transform: translateX(5px);
        }

        .nav-link:hover::before {
            transform: scaleY(1);
        }

        .nav-link.active {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .nav-link.active::before {
            transform: scaleY(1);
        }

        .nav-icon {
            margin-right: 14px;
            font-size: 1.2rem;
            width: 24px;
            text-align: center;
            transition: var(--transition);
        }

        .nav-link:hover .nav-icon {
            transform: scale(1.1);
        }

        .nav-badge {
            margin-left: auto;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            font-size: 0.7rem;
            padding: 3px 8px;
            border-radius: 10px;
            font-weight: 600;
        }

        /* Estilo especial para emergencias */
        .nav-emergency .nav-icon {
            color: #ff9e80 !important;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        /* Footer del sidebar */
        .sidebar-footer {
            padding: 20px 25px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(0, 0, 0, 0.05);
        }

        .user-info {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--accent-color) 0%, var(--secondary-color) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            margin-right: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        .user-details {
            flex: 1;
        }

        .user-name {
            font-weight: 600;
            margin-bottom: 2px;
            font-size: 0.95rem;
        }

        .user-role {
            font-size: 0.8rem;
            opacity: 0.8;
        }

        .btn-logout {
            background: rgba(220, 53, 69, 0.85);
            border: none;
            border-radius: var(--border-radius);
            padding: 12px;
            font-weight: 500;
            transition: var(--transition);
            color: white;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-logout:hover {
            background: #dc3545;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(220, 53, 69, 0.3);
        }

        /* Contenido principal */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: var(--transition);
        }

        /* Header superior - AHORA CON EL MISMO GRADIENTE INCREÍBLE */
        .top-header {
            background: linear-gradient(165deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            box-shadow: var(--header-shadow);
            padding: 18px 30px;
            display: flex;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 999;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .header-toggle {
            background: rgba(255, 255, 255, 0.15);
            border: none;
            color: white;
            font-size: 1.3rem;
            width: 45px;
            height: 45px;
            border-radius: var(--border-radius);
            margin-right: 20px;
            display: none;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .header-toggle:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: scale(1.05);
        }

        .page-title {
            flex: 1;
        }

        .page-title h1 {
            font-weight: 700;
            color: white;
            margin: 0;
            font-size: 1.8rem;
            line-height: 1.2;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        }

        .page-title p {
            color: rgba(255, 255, 255, 0.85);
            margin: 5px 0 0;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .status-badge {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            color: white;
            padding: 10px 18px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .status-badge i {
            margin-right: 8px;
            font-size: 0.6rem;
            color: #a5d6a7;
        }

        .theme-toggle {
            background: rgba(255, 255, 255, 0.15);
            border: none;
            color: white;
            font-size: 1.3rem;
            width: 45px;
            height: 45px;
            border-radius: var(--border-radius);
            transition: var(--transition);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .theme-toggle:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: scale(1.05);
        }

        /* Contenedor de contenido */
        .content-wrapper {
            padding: 30px;
            flex: 1;
            background-color: var(--background-color);
        }

        /* Elementos de seguridad en el fondo */
        .security-bg-element {
            position: fixed;
            opacity: 0.03;
            z-index: -1;
            font-size: 25rem;
            color: var(--primary-color);
            pointer-events: none;
        }

        .security-bg-1 {
            top: -150px;
            left: -150px;
        }

        .security-bg-2 {
            bottom: -150px;
            right: -150px;
            transform: rotate(180deg);
        }

        /* Responsive */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .header-toggle {
                display: flex;
            }
            
            .content-wrapper {
                padding: 20px;
            }
        }

        @media (max-width: 576px) {
            .top-header {
                padding: 15px 20px;
            }
            
            .page-title h1 {
                font-size: 1.5rem;
            }
            
            .content-wrapper {
                padding: 15px;
            }
            
            .security-bg-element {
                font-size: 15rem;
            }
            
            .header-actions {
                gap: 10px;
            }
            
            .status-badge {
                padding: 8px 12px;
                font-size: 0.8rem;
            }
        }

        /* Scrollbar personalizado */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Elementos decorativos de fondo -->
    <i class="fas fa-shield-alt security-bg-element security-bg-1"></i>
    <i class="fas fa-lock security-bg-element security-bg-2"></i>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="logo-container">
                <i class="fas fa-shield-alt logo-icon"></i>
            </div>
            <div class="app-name">SISCOM</div>
            <div class="university-name">UNIVERSIDAD NACIONAL EXPERIMENTAL DEL TÁCHIRA</div>
        </div>
        
        <!-- Navegación -->
        <div class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-title">Principal</div>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt nav-icon"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">Seguridad</div>
                <ul class="nav-menu">
                    <li class="nav-item nav-emergency">
                        <a href="{{ route('admin.emergencies.index') }}" class="nav-link {{ request()->routeIs('admin.emergencies.*') ? 'active' : '' }}">
                            <i class="fas fa-exclamation-triangle nav-icon"></i>
                            <span>Gestión de Emergencias</span>
                            <span class="nav-badge">3</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="{{ route('admin.contacts.index') }}" class="nav-link {{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}">
                            <i class="fas fa-headset nav-icon"></i>
                            <span>Contactos de Emergencia</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('admin.guardias.index') }}" class="nav-link {{ request()->routeIs('admin.guardias.*') ? 'active' : '' }}">
                            <i class="fas fa-user-shield nav-icon"></i>
                            <span>Gestión de Guardias</span>
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">Operaciones</div>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="{{ route('admin.tasks.history') }}" class="nav-link {{ request()->routeIs('admin.tasks.*') ? 'active' : '' }}">
                            <i class="fas fa-tasks nav-icon"></i>
                            <span>Tareas y Rondas</span>
                            <span class="nav-badge">12</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.index') ? 'active' : '' }}">
                            <i class="fas fa-file-alt nav-icon"></i>
                            <span>Reportes</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Footer del sidebar -->
        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="user-details">
                    <div class="user-name">{{ Auth::guard('admin_web')->user()->name ?? 'Administrador' }}</div>
                    <div class="user-role">Supervisor</div>
                </div>
            </div>
            
            <button class="btn-logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
            </button>
            <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
    </nav>

    <!-- Contenido principal -->
    <div class="main-content" id="main-content">
        <!-- Header superior - AHORA CON EL GRADIENTE INCREÍBLE -->
        <header class="top-header">
            <button class="header-toggle" id="sidebar-toggle">
                <i class="fas fa-bars"></i>
            </button>
            
            <div class="page-title">
                <h1>@yield('page-title', 'Dashboard de Supervisión')</h1>
                <p>@yield('page-subtitle', 'Resumen general del sistema de seguridad')</p>
            </div>
            
            <div class="header-actions">
                <button class="theme-toggle" id="theme-toggle" title="Cambiar tema">
                    <i class="fas fa-moon"></i>
                </button>
                
                <div class="status-badge">
                    <i class="fas fa-circle"></i>
                    Sistema Activo
                </div>
            </div>
        </header>

        <!-- Contenido de la vista -->
        <main class="content-wrapper">
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const mainContent = document.getElementById('main-content');
            const themeToggle = document.getElementById('theme-toggle');
            
            // Toggle del sidebar en móviles
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('active');
                });
            }
            
            // Cerrar sidebar al hacer clic en el contenido principal (en móviles)
            if (mainContent) {
                mainContent.addEventListener('click', function() {
                    if (window.innerWidth <= 992 && sidebar.classList.contains('active')) {
                        sidebar.classList.remove('active');
                    }
                });
            }
            
            // Toggle del tema claro/oscuro
            if (themeToggle) {
                themeToggle.addEventListener('click', function() {
                    document.body.classList.toggle('dark-theme');
                    const icon = themeToggle.querySelector('i');
                    
                    if (document.body.classList.contains('dark-theme')) {
                        icon.classList.remove('fa-moon');
                        icon.classList.add('fa-sun');
                        localStorage.setItem('theme', 'dark');
                    } else {
                        icon.classList.remove('fa-sun');
                        icon.classList.add('fa-moon');
                        localStorage.setItem('theme', 'light');
                    }
                });
                
                // Cargar tema guardado
                const savedTheme = localStorage.getItem('theme');
                if (savedTheme === 'dark') {
                    document.body.classList.add('dark-theme');
                    themeToggle.querySelector('i').classList.remove('fa-moon');
                    themeToggle.querySelector('i').classList.add('fa-sun');
                }
            }
            
            // Ajustar sidebar en resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 992) {
                    sidebar.classList.remove('active');
                }
            });
        });
    </script>
</body>
</html>