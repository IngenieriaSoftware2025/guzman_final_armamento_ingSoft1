<?php
// Verificar autenticación y permisos
session_start();
if (!isset($_SESSION['login'])) {
    header('Location: /');
    exit;
}
?>

<div class="row justify-content-center p-3">
    <div class="col-lg-10">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <div class="row mb-3">
                    <h5 class="text-center mb-2">Sistema de Gestión Militar</h5>
                    <h4 class="text-center mb-2 text-primary">Gestión de Usuarios</h4>
                </div>

                <!-- Formulario para Usuarios -->
                <div class="row justify-content-center p-4 shadow-lg mb-4">
                    <form id="FormUsuarios">
                        <input type="hidden" id="usuario_id" name="usuario_id">

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="usuario_nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="usuario_nombre" name="usuario_nombre"
                                    placeholder="Ingrese nombre del usuario" required>
                            </div>
                            <div class="col-lg-6">
                                <label for="usuario_apellido" class="form-label">Apellido <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="usuario_apellido" name="usuario_apellido"
                                    placeholder="Ingrese apellido del usuario" required>
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="usuario_dpi" class="form-label">DPI <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="usuario_dpi" name="usuario_dpi"
                                    placeholder="Ingrese DPI de 13 dígitos" maxlength="13" pattern="[0-9]{13}" required>
                                <small class="form-text text-muted">DPI debe tener exactamente 13 dígitos</small>
                            </div>
                            <div class="col-lg-6">
                                <label for="usuario_correo" class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="usuario_correo" name="usuario_correo"
                                    placeholder="Ingrese correo electrónico" required>
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="usuario_contra" class="form-label">Contraseña <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="usuario_contra" name="usuario_contra" 
                                    placeholder="Ingrese contraseña" required>
                                <small class="form-text text-muted">Mínimo 8 caracteres</small>
                            </div>
                            <div class="col-lg-6">
                                <label for="usuario_rol" class="form-label">Rol <span class="text-danger">*</span></label>
                                <select class="form-control" id="usuario_rol" name="usuario_rol" required>
                                    <option value="">Seleccione un rol...</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="usuario_situacion" class="form-label">Estado</label>
                                <select class="form-control" id="usuario_situacion" name="usuario_situacion">
                                    <option value="1">Activo</option>
                                    <option value="2">Inactivo</option>
                                    <option value="3">Suspendido</option>
                                </select>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="row justify-content-center mt-4">
                            <div class="col-auto">
                                <button class="btn btn-success" type="submit" id="BtnGuardar">
                                    <i class="bi bi-floppy me-2"></i>Guardar Usuario
                                </button>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-warning d-none" type="button" id="BtnModificar">
                                    <i class="bi bi-pencil me-2"></i>Modificar
                                </button>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-secondary" type="button" id="BtnLimpiar">
                                    <i class="bi bi-arrow-clockwise me-2"></i>Limpiar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sección de Tabla de Usuarios -->
<div class="row justify-content-center p-3">
    <div class="col-lg-12">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <div class="row mb-3">
                    <h3 class="text-center">Usuarios Registrados</h3>
                </div>

                <!-- Botón de búsqueda -->
                <div class="row justify-content-center mb-3">
                    <div class="col-auto">
                        <button class="btn btn-primary" type="button" id="BtnBuscarUsuarios">
                            <i class="bi bi-search me-2"></i>Buscar Usuarios
                        </button>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-info" type="button" id="BtnActualizar">
                            <i class="bi bi-arrow-clockwise me-2"></i>Actualizar
                        </button>
                    </div>
                </div>

                <!-- Sección de tabla -->
                <div id="seccion-usuarios" class="d-none">
                    <div class="table-responsive p-2">
                        <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableUsuarios">
                        </table>
                    </div>
                </div>

                <!-- Mensaje cuando no hay usuarios -->
                <div id="mensaje-sin-usuarios" class="text-center p-4">
                    <i class="bi bi-people fs-1 text-muted"></i>
                    <h5 class="text-muted mt-2">Presiona "Buscar Usuarios" para cargar los datos</h5>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="<?= asset('build/js/usuarios/index.js') ?>"></script>