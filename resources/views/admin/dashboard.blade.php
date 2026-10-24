@extends('admin.layouts.app')

@section('title', 'Dashboard - SISCOM UNET')
@section('page-title', 'Dashboard de Supervisión')
@section('page-subtitle', 'Resumen general del sistema de seguridad')

@section('content')
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card metric-card bg-primary">
            <div class="card-body">
                <div class="card-trend">
                    <span class="badge bg-light text-primary trend-badge" data-trend="+12%">+12%</span>
                </div>
                <h5 class="card-title">Total Reportes</h5>
                <p class="card-value">{{ $totalReports ?? '142' }}</p>
                <i class="fas fa-flag card-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card metric-card bg-success">
            <div class="card-body">
                <div class="card-trend">
                    <span class="badge bg-light text-success trend-badge" data-trend="+8%">+8%</span>
                </div>
                <h5 class="card-title">Tareas Completadas</h5>
                <p class="card-value">{{ $completedTasks ?? '86' }}</p>
                <i class="fas fa-check-circle card-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card metric-card bg-warning">
            <div class="card-body">
                <div class="card-trend">
                    <span class="badge bg-light text-warning trend-badge" data-trend="-3%">-3%</span>
                </div>
                <h5 class="card-title">Total Tareas Asignadas</h5>
                <p class="card-value">{{ $totalTasks ?? '112' }}</p>
                <i class="fas fa-tasks card-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card metric-card bg-info">
            <div class="card-body">
                <div class="card-trend">
                    <span class="badge bg-light text-info trend-badge" data-trend="+5%">+5%</span>
                </div>
                <h5 class="card-title">Guardias Activos</h5>
                <p class="card-value">{{ $totalGuards ?? '24' }}</p>
                <i class="fas fa-user-shield card-icon"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Relación Tareas Asignadas y Completadas</span>
                <span class="badge bg-primary">Este Mes</span>
            </div>
            <div class="card-body">
                <div class="chart-container" style="height: 300px;">
                    <canvas id="tasksChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Reportes por Tipo</span>
                <span class="badge bg-primary">Este Mes</span>
            </div>
            <div class="card-body">
                <div class="chart-container" style="height: 300px;">
                    <canvas id="reportsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

{{-----------------------------------------------------------}}
{{-- SCRIPTS (SINTAXIS BLADE RESUELTA) --}}
{{-----------------------------------------------------------}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    // ** Uso de bloques PHP para asegurar la sintaxis Blade **
    <?php
        $tasksData = $tasksData ?? ['completed' => 86, 'assigned' => 112];
    ?>
    const tasksData = @json($tasksData);

    <?php
        $reportTypes = $reportTypes ?? ['Incidentes' => 42, 'Mantenimiento' => 35, 'Accesos' => 28, 'Emergencias' => 22, 'Otros' => 15];
    ?>
    const reportTypes = @json($reportTypes);
    
    // Variables para las instancias de las gráficas
    window.tasksChartInstance = null;
    window.reportsChartInstance = null;

    // Función para obtener colores de texto/borde dinámicamente según el modo
    function getChartColors() {
        const isDarkMode = document.body.classList.contains('dark-mode');
        return {
            fontColor: isDarkMode ? '#E0E0E0' : '#263238', 
            gridColor: isDarkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)', 
            tooltipBg: isDarkMode ? 'rgba(30, 30, 30, 0.9)' : 'rgba(0, 0, 0, 0.8)'
        };
    }

    // Inicialización de Gráficas
    function initializeCharts() {
        const colors = getChartColors();

        // 1. Gráfica de Tareas (Dona)
        const tasksCtx = document.getElementById('tasksChart').getContext('2d');
        if (window.tasksChartInstance) {
            window.tasksChartInstance.destroy();
        }
        window.tasksChartInstance = new Chart(tasksCtx, {
            type: 'doughnut',
            data: {
                labels: ['Completadas', 'Pendientes'],
                datasets: [{
                    data: [tasksData.completed, tasksData.assigned - tasksData.completed],
                    backgroundColor: ['#28a745', '#ffc107'], // Success, Warning
                    borderWidth: 0,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            font: {
                                size: 12
                            },
                            color: colors.fontColor 
                        }
                    },
                    tooltip: {
                        backgroundColor: colors.tooltipBg, 
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                },
                cutout: '65%'
            }
        });

        // 2. Gráfica de Reportes (Barra)
        const reportsCtx = document.getElementById('reportsChart').getContext('2d');
        if (window.reportsChartInstance) {
            window.reportsChartInstance.destroy();
        }
        window.reportsChartInstance = new Chart(reportsCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(reportTypes),
                datasets: [{
                    label: 'Cantidad de Reportes',
                    data: Object.values(reportTypes),
                    backgroundColor: [
                        'rgba(13, 71, 161, 0.8)', 
                        'rgba(25, 118, 210, 0.8)', 
                        'rgba(66, 165, 245, 0.8)', 
                        'rgba(100, 181, 246, 0.8)',
                        'rgba(144, 202, 249, 0.8)'
                    ],
                    borderColor: [
                        '#0D47A1',
                        '#1976D2',
                        '#42A5F5',
                        '#64B5F6',
                        '#90CAF9'
                    ],
                    borderWidth: 1,
                    borderRadius: 6
                }]
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    y: { 
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            color: colors.fontColor 
                        },
                        grid: {
                            drawBorder: false,
                            color: colors.gridColor 
                        }
                    },
                    x: {
                        ticks: {
                            color: colors.fontColor 
                        },
                        grid: {
                            display: false
                        }
                    }
                },
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: colors.tooltipBg, 
                        titleFont: { size: 14 },
                        bodyFont: { size: 13 },
                        padding: 10,
                        cornerRadius: 8
                    }
                }
            }
        });
    }

    // Actualizar badges de tendencia (usando el color del texto del padre para el fondo)
    function updateTrendBadges() {
        const trendBadges = document.querySelectorAll('.metric-card .trend-badge');
        trendBadges.forEach(badge => {
            const text = badge.getAttribute('data-trend');
            // Quitar clases previas de color
            badge.classList.remove('text-primary', 'text-success', 'text-warning', 'text-danger');
            
            if (text.includes('+')) {
                badge.classList.add('text-success');
            } else if (text.includes('-')) {
                badge.classList.add('text-danger');
            } else {
                 badge.classList.add('text-primary');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        initializeCharts();
        updateTrendBadges();

        // Escucha cambios en el atributo class del body (para el modo oscuro/claro)
        const observer = new MutationObserver((mutations) => {
            mutations.forEach(mutation => {
                if (mutation.attributeName === 'class') {
                    // Re-inicializa las gráficas para aplicar los nuevos colores de fuente/grid
                    initializeCharts(); 
                }
            });
        });

        observer.observe(document.body, { attributes: true }); 
    });
</script>
@endpush

{{-----------------------------------------------------------}}
{{-- ESTILOS (ADAPTACIÓN AL MODO OSCURO) --}}
{{-----------------------------------------------------------}}
@push('styles')
<style>
    /* ----------------------------------------------------- */
    /* Estilos de Tarjetas de Métricas */
    /* ----------------------------------------------------- */
    .metric-card {
        color: white; 
        border-radius: 16px;
        padding: 25px;
        position: relative;
        overflow: hidden;
        height: 140px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        transition: all 0.3s ease;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1); 
    }

    .metric-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.2);
    }

    /* Colores Gradientes */
    .bg-primary { 
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%); 
    }
    .bg-success { 
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%); 
    }
    .bg-warning { 
        background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%); 
    }
    .bg-info { 
        background: linear-gradient(135deg, #17a2b8 0%, #42A5F5 100%); 
    }

    .metric-card .card-title {
        font-size: 0.9rem;
        opacity: 0.9;
        margin-bottom: 10px;
        font-weight: 500;
    }

    .metric-card .card-value {
        font-size: 2.2rem;
        font-weight: 700;
        margin-bottom: 0;
        line-height: 1;
    }

    .metric-card .card-icon {
        position: absolute;
        bottom: 20px;
        right: 20px;
        font-size: 3.5rem;
        opacity: 0.3;
        transition: all 0.3s ease;
    }

    .metric-card:hover .card-icon {
        opacity: 0.5;
        transform: scale(1.1);
    }

    .metric-card .card-trend {
        position: absolute;
        top: 20px;
        right: 20px;
        font-size: 0.8rem;
        display: flex;
        align-items: center;
    }

    .emergency-metric-box {
        border-color: var(--border-color) !important;
        transition: all 0.3s;
    }
    .emergency-metric-box:hover {
        box-shadow: 0 4px 12px var(--shadow-color);
    }
    
    .chart-container {
        position: relative;
    }
</style>
@endpush