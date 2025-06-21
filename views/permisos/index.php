<div class="container py-5">
    <div class="row mb-5 justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body bg-gradient" style="background: linear-gradient(90deg, #f8fafc 60%, #e3f2fd 100%);">
                    <div class="mb-4 text-center">
                        <h5 class="fw-bold text-secondary mb-2">Sistema de Gestión</h5>
                        <h3 class="fw-bold text-primary mb-0">CONTROL DE PERMISOS POR APLICACIÓN</h3>
                    </div>
                    <form id="formPermisos" class="p-4 bg-white rounded-3 shadow-sm border">
                        <div class="row g-4 mb-3">
                            <div class="col-md-6">
                                <label for="permiso_usuario" class="form-label">Usuario</label>
                                <select class="form-select form-select-lg" id="permiso_usuario" name="permiso_usuario" required>
                                    <option value="">Seleccione usuario</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="permiso_aplicacion" class="form-label">Aplicación</label>
                                <select class="form-select form-select-lg" id="permiso_aplicacion" name="permiso_aplicacion" required>
                                    <option value="">Seleccione aplicación</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label for="permiso_nivel" class="form-label">Nivel de Permiso</label>
                                <select class="form-select form-select-lg" id="permiso_nivel" name="permiso_nivel" required>
                                    <option value="">Seleccione nivel</option>
                                    <option value="LECTURA">LECTURA - Solo consultar</option>
                                    <option value="ESCRITURA">ESCRITURA - Consultar y modificar</option>
                                    <option value="TOTAL">TOTAL - Control total</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="usuario_permisos" class="form-label">Ver Permisos de Usuario</label>
                                <select class="form-select form-select-lg" id="usuario_permisos">
                                    <option value="">Seleccione usuario para ver permisos</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-center gap-3">
                            <button class="btn btn-success btn-lg px-4 shadow" type="submit" id="BtnAsignar">
                                <i class="bi bi-shield-check me-2"></i>Asignar Permiso
                            </button>
                            <button class="btn btn-secondary btn-lg px-4 shadow" type="reset" id="BtnLimpiar">
                                <i class="bi bi-eraser me-2"></i>Limpiar
                            </button>
                            <button class="btn btn-primary btn-lg px-4 shadow" type="button" id="BtnVerPermisos">
                                <i class="bi bi-eye me-2"></i>Ver Todos los Permisos
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sección de permisos por usuario -->
    <div class="row justify-content-center mt-5" id="seccionPermisosUsuario" style="display: none;">
        <div class="col-lg-12">
            <div class="card shadow-lg border-info rounded-4">
                <div class="card-body">
                    <h3 class="text-center text-info mb-4">
                        <i class="bi bi-person-gear me-2"></i>Permisos del Usuario Seleccionado
                    </h3>
                    <div id="contenidoPermisosUsuario">
                        <!-- Contenido dinámico -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección de tabla general -->
    <div class="row justify-content-center mt-5" id="seccionTablaPermisos" style="display: none;">
        <div class="col-lg-12">
            <div class="card shadow-lg border-primary rounded-4">
                <div class="card-body">
                    <h3 class="text-center text-primary mb-4">
                        <i class="bi bi-shield-fill-check me-2"></i>Permisos Asignados en el Sistema
                    </h3>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered align-middle rounded-3 overflow-hidden w-100" id="TablePermisos" style="width: 100% !important;">
                            <thead class="table-dark">
                                <tr>
                                    <th>No.</th>
                                    <th>Usuario</th>
                                    <th>Aplicación</th>
                                    <th>Nivel</th>
                                    <th>Fecha Asignación</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalRevocarPermiso" tabindex="-1" aria-labelledby="modalRevocarPermisoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalRevocarPermisoLabel">
                    <i class="bi bi-shield-x me-2"></i>Revocar Permiso
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="bi bi-shield-exclamation display-1 text-danger mb-3"></i>
                    <h5>¿Está seguro de revocar este permiso?</h5>
                    <p class="text-muted">Esta acción eliminará el acceso del usuario a la aplicación</p>
                    <div class="alert alert-warning">
                        <strong>Permiso:</strong> <span id="infoPermisoRevocar"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="confirmarRevocarPermiso">
                    <i class="bi bi-shield-x me-2"></i>Revocar Permiso
                </button>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="<?= asset('build/js/permisos/index.js') ?>"></script>