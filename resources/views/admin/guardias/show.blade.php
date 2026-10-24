@extends('admin.layouts.app')

@section('title', 'Estadísticas de ' . $guardia->name)
@section('page-title', 'Estadísticas de Rendimiento')
@section('page-subtitle', 'Análisis detallado del desempeño del guardia')

@section('content')
<!-- Header con Acciones -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-gradient-blue text-white">
            <div class="card-body py-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-1 fw-bold">{{ $guardia->name }}</h3>
                        <p class="mb-0 opacity-90">Análisis completo de desempeño y actividad</p>
                    </div>
                    <a href="{{ route('admin.guardias.index') }}" class="btn btn-light btn-lg px-4 text-primary fw-bold">
                        <i class="fas fa-arrow-left me-2"></i>Volver al Listado
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Información del Guardia -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-id-card me-2 text-primary"></i>
                    Información Personal
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <div class="avatar-lg bg-primary rounded-circle d-flex align-items-center justify-content-center me-4">
                        <i class="fas fa-user-shield text-white fa-2x"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-1">{{ $guardia->name }}</h4>
                        <span class="badge bg-success rounded-pill px-3 py-2">
                            <i class="fas fa-circle me-1" style="font-size: 0.6rem;"></i>
                            ACTIVO
                        </span>
                    </div>
                </div>
                
                <div class="info-item mb-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-envelope text-primary me-3"></i>
                        <div>
                            <small class="text-muted">Email / Usuario</small>
                            <div class="fw-semibold">{{ $guardia->email }}</div>
                        </div>
                    </div>
                </div>
                
                <div class="info-item mb-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-clock text-primary me-3"></i>
                        <div>
                            <small class="text-muted">Turno Asignado</small>
                            <div class="fw-semibold">
                                @if($guardia->shift)
                                    <span class="badge bg-info">{{ $guardia->shift }}</span>
                                @else
                                    <span class="text-muted">No asignado</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-calendar text-primary me-3"></i>
                        <div>
                            <small class="text-muted">Fecha de Registro</small>
                            <div class="fw-semibold">{{ $guardia->created_at->format('d/m/Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Métricas de Actividad -->
    <div class="col-md-8">
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card metric-card bg-primary">
                    <div class="card-body">
                        <div class="card-trend">
                            <span class="badge bg-light text-primary">Total</span>
                        </div>
                        <h5 class="card-title">Tareas Asignadas</h5>
                        <p class="card-value">{{ $totalTasks }}</p>
                        <i class="fas fa-tasks card-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card metric-card bg-success">
                    <div class="card-body">
                        <div class="card-trend">
                            <span class="badge bg-light text-success">{{ $totalTasks > 0 ? round(($completedTasks/$totalTasks)*100) : 0 }}%</span>
                        </div>
                        <h5 class="card-title">Tareas Completadas</h5>
                        <p class="card-value">{{ $completedTasks }}</p>
                        <i class="fas fa-check-circle card-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card metric-card bg-info">
                    <div class="card-body">
                        <div class="card-trend">
                            <span class="badge bg-light text-info">+{{ $totalReports }}</span>
                        </div>
                        <h5 class="card-title">Reportes Enviados</h5>
                        <p class="card-value">{{ $totalReports }}</p>
                        <i class="fas fa-flag card-icon"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico de Rendimiento -->
        <div class="card">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-chart-pie me-2 text-primary"></i>
                    Distribución de Tareas
                </h5>
            </div>
            <div class="card-body">
                <div class="chart-container" style="height: 300px;">
                    <canvas id="tasksCompletionChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Actividad Reciente -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-history me-2 text-primary"></i>
                        Actividad Reciente
                    </h5>
                    <small class="text-muted">Últimas 10 actividades</small>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="nav-tabs-container">
                    <ul class="nav nav-tabs nav-tabs-custom" id="recentActivityTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="reports-tab" data-bs-toggle="tab" data-bs-target="#reports" type="button" role="tab">
                                <i class="fas fa-flag me-2"></i>Reportes
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="assigned-tab" data-bs-toggle="tab" data-bs-target="#assigned" type="button" role="tab">
                                <i class="fas fa-tasks me-2"></i>Tareas Asignadas
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed" type="button" role="tab">
                                <i class="fas fa-check-circle me-2"></i>Tareas Completadas
                            </button>
                        </li>
                    </ul>
                </div>
                
                <div class="tab-content p-4" id="recentActivityContent">
                    <!-- Pestaña 1: Reportes Enviados -->
                    <div class="tab-pane fade show active" id="reports" role="tabpanel">
                        <div class="activity-list">
                            @forelse ($recentReports as $report)
                            <div class="activity-item d-flex align-items-center p-3 border-bottom">
                                @if ($report->image_path)
                                    <button type="button" class="btn p-0 me-3" data-bs-toggle="modal" data-bs-target="#imageModal" data-image-url="{{ asset('storage/' . $report->image_path) }}">
                                        <img src="{{ asset('storage/' . $report->image_path) }}" alt="Miniatura" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                    </button>
                                @else
                                    <div class="avatar-sm bg-danger rounded-circle d-flex align-items-center justify-content-center me-3">
                                        <i class="fas fa-exclamation-triangle text-white"></i>
                                    </div>
                                @endif
                                
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold text-danger mb-1">Reporte: {{ $report->type }}</h6>
                                    <p class="text-muted mb-1">{{ Str::limit($report->description, 100) }}</p>
                                    <small class="text-muted">{{ $report->created_at->format('d/m/Y H:i') }}</small>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-flag fa-2x mb-2"></i>
                                <p>No hay reportes recientes</p>
                            </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Pestaña 2: Tareas Asignadas -->
                    <div class="tab-pane fade" id="assigned" role="tabpanel">
                        <div class="activity-list">
                            @forelse ($recentAssignedTasks as $task)
                            @php
                                $dueDate = \Carbon\Carbon::parse($task->due_date);
                                $isOverdue = $task->status != 'completed' && $dueDate->isPast();
                            @endphp
                            <div class="activity-item d-flex align-items-center p-3 border-bottom {{ $isOverdue ? 'bg-danger bg-opacity-10' : '' }}">
                                <div class="avatar-sm bg-warning rounded-circle d-flex align-items-center justify-content-center me-3">
                                    <i class="fas fa-tasks text-white"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-1 {{ $isOverdue ? 'text-danger' : 'text-dark' }}">
                                        {{ $task->title }}
                                        @if ($isOverdue)
                                            <span class="badge bg-danger ms-2">VENCIDA</span>
                                        @elseif ($task->status == 'pending')
                                            <span class="badge bg-warning ms-2">PENDIENTE</span>
                                        @endif
                                    </h6>
                                    <p class="text-muted mb-1">{{ Str::limit($task->description, 100) }}</p>
                                    <small class="text-muted">Límite: {{ $dueDate->format('d/m/Y H:i') }}</small>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-tasks fa-2x mb-2"></i>
                                <p>No hay tareas asignadas recientemente</p>
                            </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Pestaña 3: Tareas Completadas -->
                    <div class="tab-pane fade" id="completed" role="tabpanel">
                        <div class="activity-list">
                            @forelse ($recentCompletedTasks as $task)
                            <div class="activity-item d-flex align-items-center p-3 border-bottom">
                                <div class="avatar-sm bg-success rounded-circle d-flex align-items-center justify-content-center me-3">
                                    <i class="fas fa-check text-white"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold text-success mb-1">{{ $task->title }}</h6>
                                    <p class="text-muted mb-1">{{ Str::limit($task->description, 100) }}</p>
                                    <small class="text-muted">Completada: {{ $task->updated_at->format('d/m/Y H:i') }}</small>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-check-circle fa-2x mb-2"></i>
                                <p>No hay tareas completadas recientemente</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Visualización de Imágenes -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Imagen de Reporte</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid rounded" alt="Reporte de Incidente" style="max-height: 80vh;">
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .bg-gradient-blue {
        background: linear-gradient(165deg, var(--primary-color) 0%, var(--secondary-color) 100%) !important;
        border: none;
        box-shadow: 0 4px 15px rgba(13, 71, 161, 0.2);
    }

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
    }

    .metric-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }

    .metric-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100px;
        height: 100px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
        transform: translate(30px, -30px);
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

    .bg-primary { background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%); }
    .bg-success { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); }
    .bg-info { background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%); }

    .avatar-sm {
        width: 40px;
        height: 40px;
    }

    .avatar-lg {
        width: 80px;
        height: 80px;
    }

    .info-item {
        border-left: 3px solid var(--primary-color);
        padding-left: 15px;
    }

    .nav-tabs-custom .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
        color: var(--text-secondary);
        font-weight: 500;
        padding: 12px 20px;
        transition: all 0.3s ease;
    }

    .nav-tabs-custom .nav-link.active {
        color: var(--primary-color);
        border-bottom-color: var(--primary-color);
        background: transparent;
    }

    .nav-tabs-custom .nav-link:hover {
        color: var(--primary-color);
        border-bottom-color: var(--primary-color);
    }

    .activity-item {
        transition: all 0.3s ease;
    }

    .activity-item:hover {
        background-color: rgba(13, 71, 161, 0.02);
        transform: translateX(5px);
    }

    .chart-container {
        position: relative;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Configuración del gráfico Chart.js
    document.addEventListener('DOMContentLoaded', function() {
        const tasksData = {
            labels: ['Completadas', 'Pendientes'],
            datasets: [{
                label: 'Estado de Tareas',
                data: [{{ $completedTasks }}, {{ $totalTasks - $completedTasks }}], 
                backgroundColor: ['#28a745', '#17a2b8'], 
                borderWidth: 0,
                hoverOffset: 8
            }]
        };

        const config = {
            type: 'doughnut',
            data: tasksData,
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
                            }
                        }
                    },
                    tooltip: {
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
        };

        new Chart(
            document.getElementById('tasksCompletionChart'),
            config
        );
        
        // Lógica para cargar la imagen en el modal
        const imageModal = document.getElementById('imageModal');
        if (imageModal) {
            imageModal.addEventListener('show.bs.modal', event => {
                const button = event.relatedTarget; 
                const imageUrl = button.getAttribute('data-image-url');
                
                const modalImage = imageModal.querySelector('#modalImage');
                modalImage.src = imageUrl;
            });
        }
    });
</script>
@endpush