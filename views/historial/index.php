<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body bg-gradient" style="background: linear-gradient(90deg, #f8fafc 60%, #e3f2fd 100%);">
                    <div class="mb-4 text-center">
                        <h5 class="fw-bold text-secondary mb-2">Sistema de Auditoría</h5>
                        <h3 class="fw-bold text-primary mb-0">HISTORIAL DE ACTIVIDADES</h3>
                    </div>
                    
                    <div class="p-4 bg-white rounded-3 shadow-sm border">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <button class="btn btn-primary" type="button" id="BtnActualizar">
                                    <i class="bi bi-arrow-clockwise me-2"></i>Actualizar
                                </button>
                            </div>
                            <div class="col-md-6 text-end">
                                <small class="text-muted">Últimas 500 actividades registradas</small>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-bordered align-middle rounded-3 overflow-hidden w-100" id="TableHistorial" style="width: 100% !important;">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No.</th>
                                        <th>Usuario</th>
                                        <th>Actividad</th>
                                        <th>Módulo</th>
                                        <th>Fecha y Hora</th>
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
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="<?= asset('build/js/historial/index.js') ?>"></script>