<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
     
            <div class="text-center mb-4">
                <h1 class="display-4">Inicio</h1>
                <p class="lead text-muted">Aplicacion de control de Armamento</p>
            </div>

            <!-- Información del Usuario -->
            <?php 
            session_start();
            if(isset($_SESSION['usuario_id']) && isset($_SESSION['rol_id'])) {
                echo "<div class='alert alert-info text-center mb-4'>";
                echo "<strong>Usuario:</strong> " . $_SESSION['usuario_id'] . " | ";
                echo "<strong>Rol:</strong> " . $_SESSION['rol_id'];
                echo "</div>";
            }
            ?>

            <!-- Módulos del Sistema -->
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h5 class="card-title">Gestión de Usuarios</h5>
                            <p class="card-text">Administrar usuarios del sistema</p>
                            <a href="/guzman_final_armamento_ingSof1/usuarios" class="btn btn-primary">Acceder</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h5 class="card-title">Control de Aplicaciones</h5>
                            <p class="card-text">Gestionar aplicaciones registradas</p>
                            <a href="/guzman_final_armamento_ingSof1/aplicacion" class="btn btn-primary">Acceder</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h5 class="card-title">Sistema de Permisos</h5>
                            <p class="card-text">Crear y administrar permisos</p>
                            <a href="/guzman_final_armamento_ingSof1/permisos" class="btn btn-primary">Acceder</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h5 class="card-title">Asignación de Permisos</h5>
                            <p class="card-text">Asignar permisos a usuarios</p>
                            <a href="/guzman_final_armamento_ingSof1/asignacionpermisos" class="btn btn-primary">Acceder</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pie de página -->
            <div class="text-center mt-5 pt-4 border-top">
                <small class="text-muted">
                    Sistema de Control de Acceso | <?= date('Y') ?>
                </small>
            </div>

        </div>
    </div>
</div>

<script src="<?= asset('build/js/inicio.js') ?>"></script>