<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="login-container">
                    <div class="login-header">
                        <div class="logo-container">
                            <i class="bi bi-shield-check" style="font-size: 3rem;"></i>
                        </div>
                        <h3 class="mb-0 fw-bold">SISTEMA DE GESTIÓN</h3>
                        <p class="mb-0 opacity-75">Control de Armamento y Personal</p>
                    </div>
                    
                    <div class="login-form">
                        <form id="FormLogin">
                            <div class="mb-4">
                                <label for="usuario_correo" class="form-label fw-semibold">
                                    <i class="bi bi-envelope me-2"></i>Correo Electrónico
                                </label>
                                <input type="email" 
                                       name="usuario_correo" 
                                       id="usuario_correo" 
                                       class="form-control form-control-lg" 
                                       placeholder="Ingrese su correo electrónico"
                                       required>
                            </div>
                            
                            <div class="mb-4">
                                <label for="usuario_contra" class="form-label fw-semibold">
                                    <i class="bi bi-lock me-2"></i>Contraseña
                                </label>
                                <div class="input-group">
                                    <input type="password" 
                                           name="usuario_contra" 
                                           id="usuario_contra" 
                                           class="form-control form-control-lg" 
                                           placeholder="Ingrese su contraseña"
                                           required>
                                    <button class="btn btn-outline-secondary" 
                                            type="button" 
                                            id="togglePassword">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="d-grid mb-3">
                                <button type="submit" 
                                        class="btn btn-primary btn-lg btn-login" 
                                        id="BtnIniciar">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>
                                    <span id="btn-text">Iniciar Sesión</span>
                                    <span id="btn-spinner" class="d-none">
                                        <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                        Iniciando...
                                    </span>
                                </button>
                            </div>
                        </form>
                        
                        <div class="text-center">
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                Sistema de Control de Armamento Militar
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= asset('build/js/login/index.js') ?>"></script>
    
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('usuario_contra');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });
    </script>
</body>
</html>