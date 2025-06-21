<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="build/js/app.js"></script>
    <link rel="shortcut icon" href="<?= asset('images/cit.png') ?>" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= asset('build/styles.css') ?>">
    <title>Sistema de Gestión - DemoApp</title>

    <style>
        :root {
            --navbar-bg: #ffffff;
            --text-primary: #2c3e50;
            --text-secondary: #6c757d;
            --border-light: #e9ecef;
            --shadow-sm: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            --shadow-md: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            --hover-bg: #f8f9fa;
            --active-bg: #e9ecef;
        }

        body {
            background-color: #f8f9fa;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            color: var(--text-primary);
        }

        .navbar-custom {
            background-color: var(--navbar-bg);
            border-bottom: 1px solid var(--border-light);
            box-shadow: var(--shadow-sm);
            padding: 1rem 0;
        }

        .navbar-brand {
            font-weight: 600;
            color: var(--text-primary) !important;
            font-size: 1.25rem;
        }

        .navbar-brand img {
            border-radius: 8px;
            border: 1px solid var(--border-light);
        }

        .nav-link {
            color: var(--text-secondary) !important;
            font-weight: 500;
            padding: 0.75rem 1rem !important;
            border-radius: 8px;
            margin: 0 0.25rem;
            transition: all 0.2s ease;
            position: relative;
        }

        .nav-link:hover {
            background-color: var(--hover-bg);
            color: var(--text-primary) !important;
        }

        .nav-link.active {
            background-color: var(--active-bg);
            color: var(--text-primary) !important;
            font-weight: 600;
        }

        .nav-link i {
            width: 20px;
            text-align: center;
        }

        .dropdown-menu {
            border: 1px solid var(--border-light);
            box-shadow: var(--shadow-md);
            border-radius: 8px;
            padding: 0.5rem;
            background-color: var(--navbar-bg);
        }

        .dropdown-item {
            border-radius: 6px;
            margin: 0.125rem 0;
            padding: 0.5rem 0.75rem;
            color: var(--text-secondary);
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background-color: var(--hover-bg);
            color: var(--text-primary);
        }

        .btn-menu {
            background-color: var(--text-primary);
            border: none;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            color: white;
            transition: all 0.2s ease;
            font-size: 0.875rem;
        }

        .btn-menu:hover {
            background-color: #1a252f;
            color: white;
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .progress-custom {
            height: 3px;
            background-color: var(--border-light);
        }

        .progress-bar-custom {
            background-color: var(--text-primary);
            border-radius: 3px;
        }

        .main-content {
            background-color: white;
            border-radius: 12px;
            margin: 1.5rem;
            padding: 2rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-light);
            min-height: 70vh;
        }

        .footer-custom {
            background-color: white;
            border-top: 1px solid var(--border-light);
            padding: 1.5rem 0;
            margin-top: auto;
        }

        .footer-text {
            color: var(--text-secondary);
            font-size: 0.875rem;
            font-weight: 400;
        }

        .navbar-toggler {
            border: none;
            padding: 0.25rem 0.5rem;
        }

        .navbar-toggler:focus {
            box-shadow: none;
        }

        .navbar-toggler-icon-custom {
            font-size: 1.25rem;
            color: var(--text-secondary);
        }

        @media (max-width: 768px) {
            .main-content {
                margin: 1rem;
                padding: 1.5rem;
                border-radius: 8px;
            }

            .navbar-brand {
                font-size: 1.1rem;
            }

            .nav-link {
                margin: 0.125rem 0;
            }
        }

        /* Estados específicos para móvil */
        @media (max-width: 991.98px) {
            .navbar-nav {
                padding-top: 1rem;
                border-top: 1px solid var(--border-light);
                margin-top: 1rem;
            }
        }

        /* Animación sutil para elementos */
        .nav-item {
            animation: fadeInUp 0.3s ease forwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarToggler"
                aria-controls="navbarToggler" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-list navbar-toggler-icon-custom"></i>
            </button>

            <a class="navbar-brand d-flex align-items-center" href="/guzman_final_armamento_ingSoft1/dashboard">
                <img src="<?= asset('./images/cit.png') ?>" width="32" height="32" alt="CIT" class="me-2">
                <span>Sistema de Gestión</span>
            </a>

            <div class="collapse navbar-collapse" id="navbarToggler">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" href="/guzman_final_armamento_ingSoft1/dashboard">
                            <i class="bi bi-house-fill me-2"></i>Inicio
                        </a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-gear me-2"></i>Personal
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="/guzman_final_armamento_ingSoft1/usuarios">
                                    <i class="bi bi-people-fill me-2"></i>Usuarios
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item" href="/guzman_final_armamento_ingSoft1/personal">
                                    <i class="bi bi-person-badge me-2"></i>Personal
                                </a>
                            </li>
                        </ul>
                    </li>



                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-gear me-2"></i>Armamento
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="/guzman_final_armamento_ingSoft1/armamento">
                                    <i class="bi bi-shield-check me-2"></i>Tipo de Armamento
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item" href="/guzman_final_armamento_ingSoft1/asignaciones">
                                    <i class="bi bi-clipboard-check me-2"></i>Asignacion de Armamento
                                </a>
                            </li>
                        </ul>
                    </li>


                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-gear me-2"></i>Graficas
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="/guzman_final_armamento_ingSoft1/estadisticas">
                                    <i class="bi bi-bar-chart-fill me-2"></i>Estadisticas
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item" href="/guzman_final_armamento_ingSoft1/mapas">
                                    <i class="bi bi-geo-alt me-2"></i>Mapas
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-gear me-2"></i>Administracion
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="/guzman_final_armamento_ingSoft1/permisos">
                                    <i class="bi bi-shield-check me-2"></i>Permisos
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item" href="/guzman_final_armamento_ingSoft1/historial">
                                    <i class="bi bi-clock-history me-2"></i>Historial de Actividades
                                </a>
                            </li>
                        </ul>
                    </li>


                </ul>

                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted small">
                        Bienvenido, <?= $_SESSION['usuario']['nombre'] ?? 'Usuario' ?>
                    </span>
                    <a href="/guzman_final_armamento_ingSoft1/logout" class="btn btn-danger btn-sm">
                        <i class="bi bi-box-arrow-right me-1"></i>Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="progress fixed-bottom progress-custom">
        <div class="progress-bar progress-bar-custom" id="bar" role="progressbar"
            aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
    </div>

    <main class="flex-grow-1">
        <div class="container-fluid">
            <div class="main-content">
                <?php echo $contenido; ?>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer-custom">
        <div class="container-fluid">
            <div class="row justify-content-center text-center">
                <div class="col-12">
                    <p class="footer-text mb-0">
                        <i class="bi bi-shield-check me-2"></i>
                        Comando de Informática y Tecnología &copy; <?= date('Y') ?>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Activar el enlace actual
        document.addEventListener('DOMContentLoaded', function() {
            const currentPath = window.location.pathname;
            const navLinks = document.querySelectorAll('.nav-link:not(.dropdown-toggle)');

            navLinks.forEach(link => {
                link.classList.remove('active');
                const href = link.getAttribute('href');
                if (href && currentPath.includes(href.split('/').pop())) {
                    link.classList.add('active');
                }
            });

            // Barra de progreso simple
            const progressBar = document.getElementById('bar');
            window.addEventListener('beforeunload', function() {
                progressBar.style.width = '100%';
            });
        });

        // Funcionalidad para mostrar progreso en navegación
        document.querySelectorAll('a[href]').forEach(link => {
            link.addEventListener('click', function(e) {
                if (this.getAttribute('href').startsWith('/')) {
                    const progressBar = document.getElementById('bar');
                    progressBar.style.width = '50%';
                    progressBar.classList.add('progress-bar-animated');
                }
            });
        });
    </script>
</body>

</html>