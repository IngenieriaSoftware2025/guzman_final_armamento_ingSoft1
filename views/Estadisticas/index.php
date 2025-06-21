<div class="container-fluid py-4">
    <!-- Encabezado del Dashboard -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-lg" style="background: linear-gradient(135deg, #2F5233 0%, #4A90E2 100%);">
                <div class="card-body text-white text-center py-4">
                    <h2 class="fw-bold mb-2">
                        <i class="bi bi-bar-chart-fill me-3"></i>
                        DASHBOARD DE ESTADÍSTICAS
                    </h2>
                    <p class="mb-0 fs-5">Sistema de Control de Armamento Militar</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Primera Fila de Gráficas -->
    <div class="row mb-4">
        <!-- Gráfica 1: Armamento por Tipo (Dona) -->
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-light border-bottom">
                    <h5 class="card-title mb-0 text-center">
                        <i class="bi bi-pie-chart-fill text-primary me-2"></i>
                        Distribución por Tipo de Armamento
                    </h5>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div style="position: relative; height: 350px; width: 100%;">
                        <canvas id="chartArmamentoTipo"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfica 2: Armamento por Estado (Barras) -->
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-light border-bottom">
                    <h5 class="card-title mb-0 text-center">
                        <i class="bi bi-bar-chart text-success me-2"></i>
                        Estados del Armamento
                    </h5>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div style="position: relative; height: 350px; width: 100%;">
                        <canvas id="chartArmamentoEstado"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Segunda Fila de Gráficas -->
    <div class="row mb-4">
        <!-- Gráfica 3: Asignaciones por Mes (Líneas) -->
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-light border-bottom">
                    <h5 class="card-title mb-0 text-center">
                        <i class="bi bi-graph-up text-info me-2"></i>
                        Tendencia de Asignaciones
                    </h5>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div style="position: relative; height: 350px; width: 100%;">
                        <canvas id="chartAsignacionesMes"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfica 4: Personal por Unidad (Barras Horizontales) -->
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-light border-bottom">
                    <h5 class="card-title mb-0 text-center">
                        <i class="bi bi-people-fill text-warning me-2"></i>
                        Personal por Unidad Militar
                    </h5>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div style="position: relative; height: 350px; width: 100%;">
                        <canvas id="chartPersonalUnidad"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fila de Métricas Rápidas -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="card-title mb-0 text-center">
                        <i class="bi bi-speedometer2 me-2"></i>
                        Métricas del Sistema
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3 col-6 mb-3">
                            <div class="p-3 bg-primary bg-opacity-10 rounded">
                                <i class="bi bi-shield-check display-6 text-primary"></i>
                                <h4 class="mt-2 mb-1" id="totalArmamento">0</h4>
                                <small class="text-muted">Total Armamento</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <div class="p-3 bg-success bg-opacity-10 rounded">
                                <i class="bi bi-person-badge display-6 text-success"></i>
                                <h4 class="mt-2 mb-1" id="totalPersonal">0</h4>
                                <small class="text-muted">Personal Activo</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <div class="p-3 bg-warning bg-opacity-10 rounded">
                                <i class="bi bi-clipboard-check display-6 text-warning"></i>
                                <h4 class="mt-2 mb-1" id="totalAsignaciones">0</h4>
                                <small class="text-muted">Asignaciones Activas</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <div class="p-3 bg-info bg-opacity-10 rounded">
                                <i class="bi bi-people display-6 text-info"></i>
                                <h4 class="mt-2 mb-1" id="totalUsuarios">0</h4>
                                <small class="text-muted">Usuarios Sistema</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Botón de Actualización -->
    <div class="row">
        <div class="col-12 text-center">
            <button class="btn btn-primary btn-lg px-5" onclick="actualizarDashboard()">
                <i class="bi bi-arrow-clockwise me-2"></i>
                Actualizar Estadísticas
            </button>
        </div>
    </div>
</div>

<!-- CSS personalizado para las gráficas -->
<style>
    .card {
        transition: transform 0.2s ease-in-out;
    }
    
    .card:hover {
        transform: translateY(-2px);
    }
    
    canvas {
        max-height: 350px !important;
    }
    
    .bg-gradient {
        background: linear-gradient(135deg, #2F5233 0%, #4A90E2 100%);
    }
</style>

<!-- Incluir los iconos de Bootstrap -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<!-- Incluir el JavaScript del dashboard -->
<script src="<?= asset('build/js/estadisticas/index.js') ?>"></script>