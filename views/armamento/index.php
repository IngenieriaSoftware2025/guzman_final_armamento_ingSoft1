<div class="container py-5">
    <div class="row mb-5 justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body bg-gradient" style="background: linear-gradient(90deg, #f8fafc 60%, #e3f2fd 100%);">
                    <div class="mb-4 text-center">
                        <h5 class="fw-bold text-secondary mb-2">Sistema de Gestión</h5>
                        <h3 class="fw-bold text-primary mb-0">CONTROL DE ARMAMENTO</h3>
                    </div>
                    <form id="formArmamento" class="p-4 bg-white rounded-3 shadow-sm border">
                        <input type="hidden" id="arma_id" name="arma_id">
                        
                        <div class="row g-4 mb-3">
                            <div class="col-md-6">
                                <label for="arma_numero_serie" class="form-label">Número de Serie</label>
                                <input type="text" class="form-control form-control-lg" id="arma_numero_serie" name="arma_numero_serie" placeholder="Ingrese número de serie" required>
                            </div>
                            <div class="col-md-6">
                                <label for="arma_tipo" class="form-label">Tipo de Armamento</label>
                                <select class="form-select form-select-lg" id="arma_tipo" name="arma_tipo" required>
                                    <option value="">Seleccione tipo de armamento</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-4 mb-3">
                            <div class="col-md-6">
                                <label for="arma_calibre" class="form-label">Calibre</label>
                                <select class="form-select form-select-lg" id="arma_calibre" name="arma_calibre" required>
                                    <option value="">Seleccione calibre</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="arma_estado" class="form-label">Estado del Armamento</label>
                                <select class="form-select form-select-lg" id="arma_estado" name="arma_estado">
                                    <option value="BUEN_ESTADO" selected>BUEN ESTADO</option>
                                    <option value="MAL_ESTADO_REPARABLE">MAL ESTADO REPARABLE</option>
                                    <option value="MAL_ESTADO_IRREPARABLE">MAL ESTADO IRREPARABLE</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row g-4 mb-3">
                            <div class="col-md-6">
                                <label for="arma_almacen" class="form-label">Almacén</label>
                                <select class="form-select form-select-lg" id="arma_almacen" name="arma_almacen" required>
                                    <option value="">Seleccione almacén</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="arma_situacion" class="form-label">Situación</label>
                                <select class="form-select form-select-lg" id="arma_situacion" name="arma_situacion">
                                    <option value="1" selected>Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-12">
                                <label for="arma_observaciones" class="form-label">
                                    <i class="bi bi-journal-text me-2"></i>Observaciones
                                </label>
                                <textarea class="form-control form-control-lg" id="arma_observaciones" name="arma_observaciones" rows="3" placeholder="Ingrese observaciones adicionales (opcional)"></textarea>
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
                            <button class="btn btn-primary btn-lg px-4 shadow" type="button" id="BtnBuscarArmamento">
                                <i class="bi bi-search me-2"></i>Buscar Armamento
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
                        <i class="bi bi-shield-check me-2"></i>Armamento Registrado en el Sistema
                    </h3>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered align-middle rounded-3 overflow-hidden w-100" id="TableArmamento" style="width: 100% !important;">
                            <thead class="table-dark">
                                <tr>
                                    <th>No.</th>
                                    <th>Número de Serie</th>
                                    <th>Tipo</th>
                                    <th>Calibre</th>
                                    <th>Estado</th>
                                    <th>Almacén</th>
                                    <th>Fecha Ingreso</th>
                                    <th>Observaciones</th>
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


<div class="modal fade" id="modalVerArmamento" tabindex="-1" aria-labelledby="modalVerArmamentoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalVerArmamentoLabel">
                    <i class="bi bi-shield-check me-2"></i>Detalles del Armamento
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="contenidoModalArmamento">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modalEliminarArmamento" tabindex="-1" aria-labelledby="modalEliminarArmamentoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalEliminarArmamentoLabel">
                    <i class="bi bi-exclamation-triangle me-2"></i>Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="bi bi-shield-x display-1 text-danger mb-3"></i>
                    <h5>¿Está seguro de eliminar este armamento?</h5>
                    <p class="text-muted">Esta acción cambiará el estado del armamento a inactivo.</p>
                    <div class="alert alert-warning">
                        <strong>Armamento:</strong> <span id="serieArmamentoEliminar"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="confirmarEliminarArmamento">
                    <i class="bi bi-trash me-2"></i>Eliminar Armamento
                </button>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="<?= asset('build/js/armamento/index.js') ?>"></script>