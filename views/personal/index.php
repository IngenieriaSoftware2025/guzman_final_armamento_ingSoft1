<div class="container py-5">
    <div class="row mb-5 justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body bg-gradient" style="background: linear-gradient(90deg, #f8fafc 60%, #e3f2fd 100%);">
                    <div class="mb-4 text-center">
                        <h5 class="fw-bold text-secondary mb-2">Sistema de Gestión</h5>
                        <h3 class="fw-bold text-primary mb-0">CONTROL DE PERSONAL MILITAR</h3>
                    </div>
                    <form id="formPersonal" class="p-4 bg-white rounded-3 shadow-sm border">
                        <input type="hidden" id="personal_id" name="personal_id">
                        
                        <div class="row g-4 mb-3">
                            <div class="col-md-6">
                                <label for="personal_nombres" class="form-label">Nombres Completos</label>
                                <input type="text" class="form-control form-control-lg" id="personal_nombres" name="personal_nombres" placeholder="Ingrese nombres completos" required>
                            </div>
                            <div class="col-md-6">
                                <label for="personal_apellidos" class="form-label">Apellidos Completos</label>
                                <input type="text" class="form-control form-control-lg" id="personal_apellidos" name="personal_apellidos" placeholder="Ingrese apellidos completos" required>
                            </div>
                        </div>

                        <div class="row g-4 mb-3">
                            <div class="col-md-6">
                                <label for="personal_grado" class="form-label">Grado Militar</label>
                                <select class="form-select form-select-lg" id="personal_grado" name="personal_grado" required>
                                    <option value="">Seleccione grado militar</option>
                                    <option value="General">General</option>
                                    <option value="Coronel">Coronel</option>
                                    <option value="Teniente Coronel">Teniente Coronel</option>
                                    <option value="Mayor">Mayor</option>
                                    <option value="Capitán">Capitán</option>
                                    <option value="Teniente">Teniente</option>
                                    <option value="Subteniente">Subteniente</option>
                                    <option value="Sargento Mayor">Sargento Mayor</option>
                                    <option value="Sargento Primero">Sargento Primero</option>
                                    <option value="Sargento Segundo">Sargento Segundo</option>
                                    <option value="Cabo">Cabo</option>
                                    <option value="Soldado">Soldado</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="personal_unidad" class="form-label">Brigada Militar</label>
                                <select class="form-select form-select-lg" id="personal_unidad" name="personal_unidad" required>
                                    <option value="disabled">Seleccione unidad militar</option>
                                    <option value="Comando de Informatica y Tecnologia">Informatica</option>
                                    <option value="Brigada de Fuerzas Especiales">Kaibil</option>
                                    <option value="Comando de Educación Superior para el Ejército">Cosede</option>
                                    <option value="Segunda Brigada de Infantería">Zacapa</option>
                                    <option value="Tercera Brigada de Infantería">Jutiapa</option>
                                    <option value="Cuarta Brigada de Infantería">Retalhuleu</option>
                                    <option value="Quinta Brigada de Infantería">Huehuetenando</option>
                                    <option value="Sexta Brigada de Infantería">Playa Grande</option>
                                    <option value="Primera Brigada de Policia Militar">Guardia de honor</option>
                                    <option value="Base Militar Mariscal Zavala">Base Militar Mariscal Zavala</option>
                                    
                                </select>
                            </div>
                        </div>
                        
                        <div class="row g-4 mb-3">
                            <div class="col-md-6">
                                <label for="personal_dpi" class="form-label">DPI</label>
                                <input type="text" class="form-control form-control-lg" id="personal_dpi" name="personal_dpi" placeholder="Ingrese DPI" maxlength="13" pattern="[0-9]{13}" required>
                                <div class="form-text">Debe contener 13 numeros</div>
                            </div>
                            <div class="col-md-6">
                                <label for="personal_situacion" class="form-label">Situación</label>
                                <select class="form-select form-select-lg" id="personal_situacion" name="personal_situacion">
                                    <option value="1" selected>Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
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
                            <button class="btn btn-primary btn-lg px-4 shadow" type="button" id="BtnBuscarPersonal">
                                <i class="bi bi-search me-2"></i>Buscar Personal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row justify-content-center mt-5" id="seccionTabla" style="display: none;">
        <div class="col-lg-12">
            <div class="card shadow-lg border-primary rounded-4">
                <div class="card-body">
                    <h3 class="text-center text-primary mb-4">
                        <i class="bi bi-people-fill me-2"></i>Personal Militar registrado en la base de datos
                    </h3>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered align-middle rounded-3 overflow-hidden w-100" id="TablePersonal" style="width: 100% !important;">
                            <thead class="table-dark">
                                <tr>
                                    <th>No.</th>
                                    <th>Nombres</th>
                                    <th>Apellidos</th>
                                    <th>Grado</th>
                                    <th>Unidad</th>
                                    <th>DPI</th>
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

<div class="modal fade" id="modalVerPersonal" tabindex="-1" aria-labelledby="modalVerPersonalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalVerPersonalLabel">
                    <i class="bi bi-person-badge me-2"></i>Detalles del Personal
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="contenidoModalPersonal">
  
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEliminarPersonal" tabindex="-1" aria-labelledby="modalEliminarPersonalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalEliminarPersonalLabel">
                    <i class="bi bi-exclamation-triangle me-2"></i>Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="bi bi-person-x display-1 text-danger mb-3"></i>
                    <h5>¿Está seguro de eliminar este personal?</h5>
                    <p class="text-muted">Esta acción cambiará el estado del personal a inactivo.</p>
                    <div class="alert alert-warning">
                        <strong>Personal:</strong> <span id="nombrePersonalEliminar"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="confirmarEliminarPersonal">
                    <i class="bi bi-trash me-2"></i>Eliminar Personal
                </button>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="<?= asset('build/js/personal/index.js') ?>"></script>