<div class="container py-5">
    <div class="row mb-5 justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body bg-gradient" style="background: linear-gradient(90deg, #f8fafc 60%, #e3f2fd 100%);">
                    <div class="mb-4 text-center">
                        <h5 class="fw-bold text-secondary mb-2">Sistema de Gestión</h5>
                        <h3 class="fw-bold text-primary mb-0">ASIGNACIÓN DE ARMAMENTO</h3>
                    </div>
                    <form id="formAsignacion" class="p-4 bg-white rounded-3 shadow-sm border">
                        <input type="hidden" id="asignacion_id" name="asignacion_id">
                        <input type="hidden" id="asignacion_usuario" name="asignacion_usuario" value="1">
                        
                        <div class="row g-4 mb-3">
                            <div class="col-md-6">
                                <label for="asignacion_arma" class="form-label">Armamento Disponible</label>
                                <select class="form-select form-select-lg" id="asignacion_arma" name="asignacion_arma" required>
                                    <option value="">Seleccione armamento</option>
                                </select>
                                <div class="form-text">Solo se muestran armamentos disponibles</div>
                            </div>
                            <div class="col-md-6">
                                <label for="asignacion_personal" class="form-label">Personal Militar</label>
                                <select class="form-select form-select-lg" id="asignacion_personal" name="asignacion_personal" required>
                                    <option value="">Seleccione personal</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-4 mb-3">
                            <div class="col-md-6">
                                <label for="asignacion_fecha_asignacion" class="form-label">Fecha de Asignación</label>
                                <input type="date" class="form-control form-control-lg" id="asignacion_fecha_asignacion" name="asignacion_fecha_asignacion" required>
                            </div>
                            <div class="col-md-6">
                                <label for="asignacion_situacion" class="form-label">Estado de la Asignación</label>
                                <select class="form-select form-select-lg" id="asignacion_situacion" name="asignacion_situacion">
                                    <option value="1" selected>Activa</option>
                                    <option value="0">Inactiva</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-12">
                                <label for="asignacion_motivo" class="form-label">
                                    <i class="bi bi-journal-text me-2"></i>Motivo de la Asignación
                                </label>
                                <textarea class="form-control form-control-lg" id="asignacion_motivo" name="asignacion_motivo" rows="3" placeholder="Describa el motivo de la asignación (operación, guardia, entrenamiento, etc.)" required></textarea>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-center gap-3">
                            <button class="btn btn-success btn-lg px-4 shadow" type="submit" id="BtnGuardar">
                                <i class="bi bi-check-circle me-2"></i>Asignar
                            </button>
                            <button class="btn btn-secondary btn-lg px-4 shadow" type="reset" id="BtnLimpiar">
                                <i class="bi bi-eraser me-2"></i>Limpiar
                            </button>
                            <button class="btn btn-primary btn-lg px-4 shadow" type="button" id="BtnBuscarAsignaciones">
                                <i class="bi bi-search me-2"></i>Ver Asignaciones
                            </button>
                            <button class="btn btn-info btn-lg px-4 shadow" type="button" id="BtnHistorial">
                                <i class="bi bi-clock-history me-2"></i>Historial
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
                        <i class="bi bi-clipboard-check me-2"></i>Asignaciones de Armamento Registradas
                    </h3>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered align-middle rounded-3 overflow-hidden w-100" id="TableAsignaciones" style="width: 100% !important;">
                            <thead class="table-dark">
                                <tr>
                                    <th>No.</th>
                                    <th>Armamento</th>
                                    <th>Personal</th>
                                    <th>Fecha Asignación</th>
                                    <th>Fecha Devolución</th>
                                    <th>Estado</th>
                                    <th>Motivo</th>
                                    <th>Usuario</th>
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

<div class="modal fade" id="modalDevolucion" tabindex="-1" aria-labelledby="modalDevolucionLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="modalDevolucionLabel">
                    <i class="bi bi-arrow-return-left me-2"></i>Devolución de Armamento
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formDevolucion">
                    <input type="hidden" id="devolucion_asignacion_id">
                    <div class="mb-3">
                        <label for="fecha_devolucion" class="form-label">Fecha de Devolución</label>
                        <input type="date" class="form-control" id="fecha_devolucion" name="fecha_devolucion" required>
                    </div>
                    <div class="alert alert-info">
                        <strong>Armamento:</strong> <span id="armamento_devolucion"></span><br>
                        <strong>Personal:</strong> <span id="personal_devolucion"></span>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-warning" id="confirmarDevolucion">
                    <i class="bi bi-arrow-return-left me-2"></i>Procesar Devolución
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalHistorial" tabindex="-1" aria-labelledby="modalHistorialLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="modalHistorialLabel">
                    <i class="bi bi-clock-history me-2"></i>Historial de Asignaciones por Personal
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="personal_historial" class="form-label">Seleccionar Personal</label>
                        <select class="form-select" id="personal_historial">
                            <option value="">Seleccione personal para ver historial</option>
                        </select>
                    </div>
                </div>
                <div id="contenido_historial">
                    <div class="text-center text-muted">
                        <i class="bi bi-info-circle me-2"></i>Seleccione un personal para ver su historial de asignaciones
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEliminarAsignacion" tabindex="-1" aria-labelledby="modalEliminarAsignacionLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalEliminarAsignacionLabel">
                    <i class="bi bi-exclamation-triangle me-2"></i>Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="bi bi-clipboard-x display-1 text-danger mb-3"></i>
                    <h5>¿Está seguro de eliminar esta asignación?</h5>
                    <p class="text-muted">Esta acción no se puede deshacer</p>
                    <div class="alert alert-warning">
                        <strong>Asignación:</strong> <span id="infoAsignacionEliminar"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="confirmarEliminarAsignacion">
                    <i class="bi bi-trash me-2"></i>Eliminar Asignación
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        transition: transform 0.2s ease-in-out;
    }
    
    .card:hover {
        transform: translateY(-2px);
    }
    
    .table th {
        background-color: #343a40 !important;
        color: white !important;
    }
    
    .badge {
        font-size: 0.75rem;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
</style>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<script src="<?= asset('build/js/asignaciones/index.js') ?>"></script>