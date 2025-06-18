<div class="container py-5">
    <div class="row mb-5 justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body bg-gradient" style="background: linear-gradient(90deg, #f8fafc 60%, #e3f2fd 100%);">
                    <div class="mb-4 text-center">
                        <h5 class="fw-bold text-secondary mb-2">Bienvenido</h5>
                        <h3 class="fw-bold text-primary mb-0">CONTROL DE USUARIOS</h3>
                    </div>
                    <form id="formUsuario" class="p-4 bg-white rounded-3 shadow-sm border" enctype="multipart/form-data">
                        <input type="hidden" id="usuario_id" name="usuario_id">

                        <!-- Preview de Fotografía -->
                        <div class="row g-4 mb-4">
                            <div class="col-12 text-center">
                                <div id="previewFotografia" class="mb-3">
                                    <div class="bg-light border rounded-circle d-inline-flex align-items-center justify-content-center"
                                         style="width: 120px; height: 120px;">
                                        <i class="bi bi-person-fill text-muted" style="font-size: 3rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-4 mb-3">
                            <div class="col-md-6">
                                <label for="usuario_nombre" class="form-label">Ingrese su nombre</label>
                                <input type="text" class="form-control form-control-lg" id="usuario_nombre" name="usuario_nombre" placeholder="Ingrese primer nombre" required>
                            </div>
                            <div class="col-md-6">
                                <label for="usuario_apellido" class="form-label">Ingrese su Apellido</label>
                                <input type="text" class="form-control form-control-lg" id="usuario_apellido" name="usuario_apellido" placeholder="Ingrese primer apellido" required>
                            </div>
                        </div>

                        <div class="row g-4 mb-3">
                            <div class="col-md-6">
                                <label for="usuario_dpi" class="form-label">DPI</label>
                                <input type="text" class="form-control form-control-lg" id="usuario_dpi" name="usuario_dpi" placeholder="Ingrese DPI (13 dígitos)" maxlength="13" pattern="[0-9]{13}" required>
                                <div class="form-text">Debe contener exactamente 13 dígitos</div>
                            </div>
                            <div class="col-md-6">
                                <label for="usuario_correo" class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control form-control-lg" id="usuario_correo" name="usuario_correo" placeholder="ejemplo@ejemplo.com" required>
                            </div>
                        </div>
                        
                        <div class="row g-4 mb-3">
                            <div class="col-md-6">
                                <label for="usuario_contra" class="form-label">Contraseña</label>
                                <div class="input-group">
                                    <input type="password" class="form-control form-control-lg" id="usuario_contra" name="usuario_contra" placeholder="Ingrese contraseña" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <div class="form-text">Mínimo 6 caracteres</div>
                            </div>
                            <div class="col-md-6">
                                <label for="usuario_situacion" class="form-label">Estado</label>
                                <select class="form-select form-select-lg" id="usuario_situacion" name="usuario_situacion">
                                    <option value="1" selected>Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row g-4 mb-4">
                            <div class="col-md-12">
                                <label for="usuario_fotografia" class="form-label">
                                    <i class="bi bi-camera-fill me-2"></i>Fotografía del Usuario
                                </label>
                                <input type="file" class="form-control form-control-lg" id="usuario_fotografia" name="usuario_fotografia" accept="image/jpeg,image/jpg,image/png,image/gif">
                                <div class="form-text">
                                    <i class="bi bi-info-circle"></i> Formatos permitidos: JPG, JPEG, PNG, GIF. Tamaño máximo: 5MB
                                </div>
                            </div>
                        </div>

                        
                        <div class="d-flex justify-content-center gap-3">
                            <button class="btn btn-success btn-lg px-4 shadow" type="submit" id="BtnGuardar">
                                <i class="bi bi-save me-2"></i>Guardar
                            </button>
                            <button class="btn btn-warning btn-lg px-4 shadow d-none" type="button" id="BtnModificar">
                                <i class="bi bi-pencil-square me-2"></i>Modificar
                            </button>
                            <button class="btn btn-secondary btn-lg px-4 shadow" type="reset" id="BtnLimpiar">
                                <i class="bi bi-eraser me-2"></i>Limpiar
                            </button>
                            <button class="btn btn-primary btn-lg px-4 shadow" type="button" id="BtnBuscarUsuarios">
                                <i class="bi bi-search me-2"></i>Buscar Usuarios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row justify-content-center mt-5" id="seccionTabla" style="display: none;">
        <div class="col-lg-11">
            <div class="card shadow-lg border-primary rounded-4">
                <div class="card-body">
                    <h3 class="text-center text-primary mb-4">
                        <i class="bi bi-people-fill me-2"></i>Usuarios Registrados en el Sistema
                    </h3>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered align-middle rounded-3 overflow-hidden w-100" id="TableUsuarios" style="width: 100% !important;">
                            <thead class="table-dark">
                                <tr>
                                    <th>No.</th>
                                    <th>Fotografía</th>
                                    <th>Nombres</th>
                                    <th>Apellidos</th>
                                    <th>DPI</th>
                                    <th>Correo</th>
                                    <th>Fecha Registro</th>
                                    <th>Situación</th>
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

<!-- Modal para Ver Detalles del Usuario -->
<div class="modal fade" id="modalVerUsuario" tabindex="-1" aria-labelledby="modalVerUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalVerUsuarioLabel">
                    <i class="bi bi-person-circle me-2"></i>Detalles del Usuario
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="contenidoModalUsuario">
                <!-- Contenido cargado dinámicamente -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación para Eliminar -->
<div class="modal fade" id="modalEliminarUsuario" tabindex="-1" aria-labelledby="modalEliminarUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalEliminarUsuarioLabel">
                    <i class="bi bi-exclamation-triangle me-2"></i>Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="bi bi-person-x display-1 text-danger mb-3"></i>
                    <h5>¿Está seguro de eliminar este usuario?</h5>
                    <p class="text-muted">Esta acción cambiará el estado del usuario a inactivo.</p>
                    <div class="alert alert-warning">
                        <strong>Usuario:</strong> <span id="nombreUsuarioEliminar"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="confirmarEliminarUsuario">
                    <i class="bi bi-trash me-2"></i>Eliminar Usuario
                </button>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="<?= asset('build/js/usuarios/index.js') ?>"></script>
