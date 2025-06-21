import Chart from 'chart.js/auto';

// Variables globales para los charts
let chartArmamentoTipo = null;
let chartArmamentoEstado = null;
let chartAsignacionesMes = null;
let chartPersonalUnidad = null;

// Inicializar cuando cargue la página
document.addEventListener('DOMContentLoaded', function() {
    inicializarDashboard();
});

const inicializarDashboard = async () => {
    try {
        await Promise.all([
            cargarArmamentoPorTipo(),
            cargarArmamentoPorEstado(),
            cargarDistribucionUsuariosRol(),
            cargarPersonalPorUnidad()
        ]);
        console.log('Dashboard cargado exitosamente');
    } catch (error) {
        console.error('Error al cargar dashboard:', error);
    }
};

// GRÁFICA 1: Armamento por Tipo (Dona)
const cargarArmamentoPorTipo = async () => {
    const url = `/guzman_final_armamento_ingSoft1/estadisticas/armamentoPorTipoAPI`;
    
    try {
        const respuesta = await fetch(url);
        const datos = await respuesta.json();
        
        if (datos.codigo === 1) {
            const labels = datos.data.map(item => item.tipo_nombre);
            const valores = datos.data.map(item => parseInt(item.cantidad));
            
            const ctx = document.getElementById('chartArmamentoTipo').getContext('2d');
            
            // Destruir gráfico anterior si existe
            if (chartArmamentoTipo) {
                chartArmamentoTipo.destroy();
            }
            
            chartArmamentoTipo = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: valores,
                        backgroundColor: [
                            '#2F5233', // Verde militar
                            '#4A90E2', // Azul
                            '#7ED321', // Verde claro
                            '#F5A623', // Naranja
                            '#D0021B', // Rojo
                            '#9013FE'  // Morado
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Armamento por Tipo',
                            font: {
                                size: 16,
                                weight: 'bold'
                            }
                        },
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                usePointStyle: true
                            }
                        }
                    }
                }
            });
        }
    } catch (error) {
        console.error('Error al cargar armamento por tipo:', error);
    }
};

// GRÁFICA 2: Armamento por Estado (Barras)
const cargarArmamentoPorEstado = async () => {
    const url = `/guzman_final_armamento_ingSoft1/estadisticas/armamentoPorEstadoAPI`;
    
    try {
        const respuesta = await fetch(url);
        const datos = await respuesta.json();
        
        if (datos.codigo === 1) {
            const labels = datos.data.map(item => item.estado_nombre);
            const valores = datos.data.map(item => parseInt(item.cantidad));
            
            const ctx = document.getElementById('chartArmamentoEstado').getContext('2d');
            
            // Destruir gráfico anterior si existe
            if (chartArmamentoEstado) {
                chartArmamentoEstado.destroy();
            }
            
            chartArmamentoEstado = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Cantidad de Armamento',
                        data: valores,
                        backgroundColor: [
                            '#28a745', // Verde para buen estado
                            '#ffc107', // Amarillo para reparable
                            '#dc3545'  // Rojo para irreparable
                        ],
                        borderColor: [
                            '#1e7e34',
                            '#e0a800',
                            '#c82333'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Estados del Armamento',
                            font: {
                                size: 16,
                                weight: 'bold'
                            }
                        },
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }
    } catch (error) {
        console.error('Error al cargar armamento por estado:', error);
    }
};

// GRÁFICA 3: Distribución de Usuarios por Rol (Barras)
const cargarDistribucionUsuariosRol = async () => {
    const url = `/guzman_final_armamento_ingSoft1/estadisticas/distribucionUsuariosRolAPI`;
    
    try {
        const respuesta = await fetch(url);
        const datos = await respuesta.json();
        
        if (datos.codigo === 1) {
            const labels = datos.data.map(item => item.categoria);
            const valores = datos.data.map(item => parseInt(item.cantidad));
            
            const ctx = document.getElementById('chartAsignacionesMes').getContext('2d');
            
            // Destruir gráfico anterior si existe
            if (chartAsignacionesMes) {
                chartAsignacionesMes.destroy();
            }
            
            chartAsignacionesMes = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Usuarios por Rol',
                        data: valores,
                        backgroundColor: [
                            '#2F5233', // Verde militar
                            '#4A90E2', // Azul
                            '#F5A623'  // Naranja
                        ],
                        borderColor: [
                            '#1e3a2a',
                            '#357abd',
                            '#d4871a'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Distribución de Usuarios por Rol',
                            font: {
                                size: 16,
                                weight: 'bold'
                            }
                        },
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }
    } catch (error) {
        console.error('Error al cargar distribución de usuarios por rol:', error);
    }
};

// GRÁFICA 4: Personal por Unidad (Barras Horizontales)
const cargarPersonalPorUnidad = async () => {
    const url = `/guzman_final_armamento_ingSoft1/estadisticas/personalPorUnidadAPI`;
    
    try {
        const respuesta = await fetch(url);
        const datos = await respuesta.json();
        
        if (datos.codigo === 1) {
            const labels = datos.data.map(item => item.unidad_nombre);
            const valores = datos.data.map(item => parseInt(item.cantidad));
            
            const ctx = document.getElementById('chartPersonalUnidad').getContext('2d');
            
            if (chartPersonalUnidad) {
                chartPersonalUnidad.destroy();
            }
            
            chartPersonalUnidad = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Cantidad de Personal',
                        data: valores,
                        backgroundColor: [
                            '#1a4c96', '#2563eb', '#3b82f6', '#60a5fa', '#93c5fd',
                            '#1e40af', '#1d4ed8', '#2563eb', '#3b82f6', '#60a5fa'
                        ],
                        borderColor: '#1e3a8a',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: {
                        title: {
                            display: true,
                            text: 'Personal por Unidad Militar',
                            font: {
                                size: 16,
                                weight: 'bold'
                            }
                        },
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }
    } catch (error) {
        console.error('Error al cargar personal por unidad:', error);
    }
};

// Función para actualizar todas las gráficas
const actualizarDashboard = () => {
    inicializarDashboard();
};

// Exportar función para uso externo si es necesario
window.actualizarDashboard = actualizarDashboard;