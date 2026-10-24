@extends('admin.layouts.app')

@section('title', 'Gestión de Tareas y Rondas')
@section('page-title', 'Gestión de Tareas y Rondas')
@section('page-subtitle', 'Control y seguimiento de asignaciones del personal de seguridad')

@section('content')
<!-- Estadísticas Rápidas -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card metric-card bg-primary">
            <div class="card-body">
                <div class="card-trend">
                    <span class="badge bg-light text-primary">+{{ $tasks->count() }}</span>
                </div>
                <h5 class="card-title">Total Tareas</h5>
                <p class="card-value">{{ $tasks->count() }}</p>
                <i class="fas fa-tasks card-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card metric-card bg-success">
            <div class="card-body">
                <div class="card-trend">
                    <span class="badge bg-light text-success">{{ $tasks->where('display_status', 'completed')->count() }}</span>
                </div>
                <h5 class="card-title">Completadas</h5>
                <p class="card-value">{{ $tasks->where('display_status', 'completed')->count() }}</p>
                <i class="fas fa-check-circle card-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card metric-card bg-warning">
            <div class="card-body">
                <div class="card-trend">
                    <span class="badge bg-light text-warning">{{ $tasks->where('display_status', 'pending')->count() }}</span>
                </div>
                <h5 class="card-title">Pendientes</h5>
                <p class="card-value">{{ $tasks->where('display_status', 'pending')->count() }}</p>
                <i class="fas fa-clock card-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card metric-card bg-danger">
            <div class="card-body">
                <div class="card-trend">
                    <span class="badge bg-light text-danger">{{ $tasks->where('display_status', 'expired')->count() }}</span>
                </div>
                <h5 class="card-title">Vencidas</h5>
                <p class="card-value">{{ $tasks->where('display_status', 'expired')->count() }}</p>
                <i class="fas fa-exclamation-triangle card-icon"></i>
            </div>
        </div>
    </div>
</div>

<!-- Header con Acciones -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-gradient-blue text-white">
            <div class="card-body py-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-1 fw-bold">Gestión de Tareas y Rondas</h3>
                        <p class="mb-0 opacity-90">Control centralizado de asignaciones del personal de seguridad</p>
                    </div>
                    <div class="d-flex gap-3">
                        <form action="{{ route('admin.tasks.clearAll') }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-light btn-lg px-4 text-danger fw-bold" 
                                    onclick="return confirmClearAll()">
                                <i class="fas fa-trash-alt me-2"></i>Vaciar Todo
                            </button>
                        </form>
                        <button type="button" class="btn btn-light btn-lg px-4 text-primary fw-bold" 
                                data-bs-toggle="modal" data-bs-target="#assignNewTaskModal">
                            <i class="fas fa-plus-circle me-2"></i>Nueva Tarea
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alertas de Sistema -->
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Filtros y Controles -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Filtros y Controles</h5>
                    <div class="btn-group">
                        <button class="btn btn-outline-primary btn-sm active" data-filter="all">Todas</button>
                        <button class="btn btn-outline-success btn-sm" data-filter="completed">Completadas</button>
                        <button class="btn btn-outline-warning btn-sm" data-filter="pending">Pendientes</button>
                        <button class="btn btn-outline-danger btn-sm" data-filter="expired">Vencidas</button>
                        <button class="btn btn-outline-info btn-sm" data-filter="high">Alta Prioridad</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Tareas -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Listado de Tareas Activas</h5>
                    <small class="text-muted">Mostrando {{ $tasks->count() }} tareas</small>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="tasksTable">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">#ID</th>
                                <th>Tarea</th>
                                <th>Asignado a</th>
                                <th>Prioridad</th>
                                <th>Fecha Límite</th>
                                <th>Estado</th>
                                <th class="text-center pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tasks as $task)
                            <tr class="task-row" 
                                data-status="{{ $task->display_status }}" 
                                data-priority="{{ $task->priority }}">
                                <td class="ps-4 fw-bold text-primary">#{{ $task->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-3">
                                            <i class="fas fa-tasks text-white"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $task->title }}</div>
                                            <small class="text-muted">{{ Str::limit($task->description, 50) }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-info rounded-circle d-flex align-items-center justify-content-center me-2">
                                            <i class="fas fa-user-shield text-white"></i>
                                        </div>
                                        <div class="fw-semibold">{{ $task->user->name ?? 'No asignado' }}</div>
                                    </div>
                                </td>
                                <td>
                                    @if ($task->priority === 'high')
                                        <span class="badge bg-danger rounded-pill px-3 py-2">
                                            <i class="fas fa-exclamation-circle me-1"></i>ALTA
                                        </span>
                                    @elseif ($task->priority === 'normal')
                                        <span class="badge bg-warning rounded-pill px-3 py-2">
                                            <i class="fas fa-info-circle me-1"></i>NORMAL
                                        </span>
                                    @else
                                        <span class="badge bg-secondary rounded-pill px-3 py-2">
                                            <i class="fas fa-arrow-down me-1"></i>BAJA
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-calendar-alt text-primary me-2"></i>
                                        <div>
                                            <div class="fw-semibold">{{ \Carbon\Carbon::parse($task->due_date)->format('d/m/Y') }}</div>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($task->due_date)->format('H:i') }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if ($task->display_status === 'completed')
                                        <span class="badge bg-success rounded-pill px-3 py-2">
                                            <i class="fas fa-check me-1"></i>COMPLETADA
                                        </span>
                                    @elseif ($task->display_status === 'expired')
                                        <span class="badge bg-dark rounded-pill px-3 py-2">
                                            <i class="fas fa-clock me-1"></i>VENCIDA
                                        </span>
                                    @else
                                        <span class="badge bg-info rounded-pill px-3 py-2">
                                            <i class="fas fa-hourglass-half me-1"></i>PENDIENTE
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center pe-4">
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-danger btn-sm px-3" 
                                                data-bs-toggle="tooltip" title="Eliminar Tarea"
                                                onclick="confirmDelete({{ $task->id }}, '{{ $task->title }}')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-tasks fa-3x mb-3"></i>
                                        <h5>No hay tareas registradas</h5>
                                        <p>Comienza asignando la primera tarea al personal de seguridad</p>
                                        <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#assignNewTaskModal">
                                            <i class="fas fa-plus me-2"></i>Crear Primera Tarea
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginación Mejorada y Profesional -->
                @if($tasks->hasPages())
                <div class="card-footer bg-white border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            <i class="fas fa-list-alt me-1"></i>
                            Mostrando <strong>{{ $tasks->firstItem() ?? 0 }}-{{ $tasks->lastItem() ?? 0 }}</strong> de 
                            <strong>{{ $tasks->total() }}</strong> tareas
                        </div>
                        <nav aria-label="Navegación de tareas">
                            <ul class="pagination pagination-sm mb-0">
                                <!-- Enlace Anterior -->
                                @if($tasks->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link">
                                            <i class="fas fa-chevron-left"></i>
                                        </span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $tasks->previousPageUrl() }}" aria-label="Anterior">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    </li>
                                @endif

                                <!-- Números de Página -->
                                @php
                                    $current = $tasks->currentPage();
                                    $last = $tasks->lastPage();
                                    $start = max(1, $current - 2);
                                    $end = min($last, $current + 2);
                                @endphp

                                @if($start > 1)
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $tasks->url(1) }}">1</a>
                                    </li>
                                    @if($start > 2)
                                        <li class="page-item disabled">
                                            <span class="page-link">...</span>
                                        </li>
                                    @endif
                                @endif

                                @for($i = $start; $i <= $end; $i++)
                                    @if($i == $current)
                                        <li class="page-item active">
                                            <span class="page-link">{{ $i }}</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $tasks->url($i) }}">{{ $i }}</a>
                                        </li>
                                    @endif
                                @endfor

                                @if($end < $last)
                                    @if($end < $last - 1)
                                        <li class="page-item disabled">
                                            <span class="page-link">...</span>
                                        </li>
                                    @endif
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $tasks->url($last) }}">{{ $last }}</a>
                                    </li>
                                @endif

                                <!-- Enlace Siguiente -->
                                @if($tasks->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $tasks->nextPageUrl() }}" aria-label="Siguiente">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link">
                                            <i class="fas fa-chevron-right"></i>
                                        </span>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal: Asignar Nueva Tarea -->
<div class="modal fade" id="assignNewTaskModal" tabindex="-1" aria-labelledby="assignNewTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('admin.tasks.assign') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white rounded-top">
                    <div class="d-flex align-items-center">
                        <div class="modal-icon me-3">
                            <i class="fas fa-plus-circle fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="modal-title mb-0" id="assignNewTaskModalLabel">Asignar Nueva Tarea</h5>
                            <small class="opacity-75">Crear nueva asignación para el personal de seguridad</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="assigned_to" class="form-label fw-semibold">
                                <i class="fas fa-user-shield me-2 text-primary"></i>
                                Asignar a Guardia
                            </label>
                            <select name="assigned_to" id="assigned_to" class="form-select form-select-lg" required>
                                <option value="">-- Seleccionar Guardia --</option>
                                @foreach ($guardias as $guardia)
                                    <option value="{{ $guardia->id }}">{{ $guardia->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="priority" class="form-label fw-semibold">
                                <i class="fas fa-flag me-2 text-primary"></i>
                                Prioridad
                            </label>
                            <select name="priority" id="priority" class="form-select form-select-lg" required>
                                <option value="normal" selected>Normal</option>
                                <option value="high">Alta</option>
                                <option value="low">Baja</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="title" class="form-label fw-semibold">
                            <i class="fas fa-heading me-2 text-primary"></i>
                            Título de la Tarea
                        </label>
                        <input type="text" name="title" id="title" class="form-control form-control-lg" 
                               placeholder="Ej: Ronda de verificación en Edificio A" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">
                            <i class="fas fa-align-left me-2 text-primary"></i>
                            Descripción Detallada
                        </label>
                        <textarea name="description" id="description" class="form-control form-control-lg" 
                                  rows="4" placeholder="Describa los detalles específicos de la tarea..." required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="due_date" class="form-label fw-semibold">
                            <i class="fas fa-calendar-alt me-2 text-primary"></i>
                            Fecha y Hora Límite
                        </label>
                        <input type="datetime-local" name="due_date" id="due_date" class="form-control form-control-lg" required>
                    </div>
                    
                    <div class="alert alert-info mt-3">
                        <div class="d-flex">
                            <i class="fas fa-info-circle fa-2x me-3 text-info"></i>
                            <div>
                                <h6 class="alert-heading mb-2">Información Importante</h6>
                                <p class="mb-1">• El guardia recibirá una notificación en tiempo real sobre la nueva tarea</p>
                                <p class="mb-1">• Las tareas de alta prioridad se destacarán en la aplicación móvil</p>
                                <p class="mb-0">• Verifique la fecha límite para asegurar un tiempo de ejecución adecuado</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom">
                    <button type="button" class="btn btn-outline-secondary btn-lg px-4" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        <i class="fas fa-paper-plane me-2"></i>Asignar Tarea
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Formulario oculto para eliminación -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
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
    .bg-warning { background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%); }
    .bg-danger { background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); }

    .avatar-sm {
        width: 40px;
        height: 40px;
    }

    .task-row:hover {
        background-color: rgba(13, 71, 161, 0.02) !important;
        transform: translateY(-1px);
        transition: all 0.2s ease;
    }

    .table > :not(caption) > * > * {
        padding: 1rem 0.75rem;
        border-bottom: 1px solid #f0f0f0;
    }

    .btn-group .btn {
        border-radius: 8px !important;
        margin: 0 2px;
    }

    .badge.rounded-pill {
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .modal-icon {
        width: 40px;
        height: 40px;
        background: rgba(255,255,255,0.2);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .form-control-lg {
        border-radius: 12px;
        border: 2px solid #e9ecef;
        padding: 12px 16px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-control-lg:focus {
        border-color: var(--accent-color);
        box-shadow: 0 0 0 3px rgba(66, 165, 245, 0.1);
    }

    .form-select-lg {
        border-radius: 12px;
        border: 2px solid #e9ecef;
        padding: 12px 16px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-select-lg:focus {
        border-color: var(--accent-color);
        box-shadow: 0 0 0 3px rgba(66, 165, 245, 0.1);
    }

    .btn-lg {
        border-radius: 12px;
        padding: 12px 30px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-lg:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.15);
    }

    .alert {
        border-radius: 12px;
        border: none;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    /* Estilos mejorados para la paginación */
    .pagination {
        margin: 0;
    }

    .page-link {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        margin: 0 2px;
        color: var(--text-color);
        font-weight: 500;
        transition: all 0.3s ease;
        min-width: 38px;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .page-link:hover {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(13, 71, 161, 0.2);
    }

    .page-item.active .page-link {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        border-color: var(--primary-color);
        color: white;
        box-shadow: 0 2px 8px rgba(13, 71, 161, 0.3);
    }

    .page-item.disabled .page-link {
        color: #6c757d;
        background-color: #f8f9fa;
        border-color: #e9ecef;
        transform: none;
        box-shadow: none;
    }

    .card-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid rgba(0,0,0,0.05);
    }
</style>
@endpush

@push('scripts')
<script>
    // Inicializar tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Filtrado de tareas
        document.querySelectorAll('[data-filter]').forEach(button => {
            button.addEventListener('click', function() {
                const filter = this.getAttribute('data-filter');
                
                // Actualizar botones activos
                document.querySelectorAll('[data-filter]').forEach(btn => {
                    btn.classList.remove('active');
                });
                this.classList.add('active');
                
                // Filtrar filas
                document.querySelectorAll('.task-row').forEach(row => {
                    if (filter === 'all') {
                        row.style.display = '';
                    } else if (filter === 'high') {
                        if (row.getAttribute('data-priority') === 'high') {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    } else {
                        if (row.getAttribute('data-status') === filter) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    }
                });
            });
        });
    });

    // Confirmación para eliminar tarea individual
    function confirmDelete(taskId, taskTitle) {
        Swal.fire({
            title: '¿Confirmar eliminación?',
            html: `¿Está seguro de eliminar la tarea <strong>"${taskTitle}"</strong>?<br><span class="text-danger">Esta acción no se puede deshacer.</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteForm');
                form.action = `{{ url('admin/tasks') }}/${taskId}`;
                form.submit();
            }
        });
    }

    // Confirmación para vaciar todas las tareas
    function confirmClearAll() {
        return confirm('⚠️ ADVERTENCIA CRÍTICA:\n\nEsta acción eliminará TODAS las tareas del sistema.\n\n¿Está completamente seguro de que desea continuar?\n\nEsta operación NO se puede deshacer.');
    }

    // Si no tienes SweetAlert2, puedes usar confirm nativo como fallback para eliminación individual
    if (typeof Swal === 'undefined') {
        function confirmDelete(taskId, taskTitle) {
            if (confirm(`¿Está seguro de eliminar la tarea "${taskTitle}"? Esta acción no se puede deshacer.`)) {
                const form = document.getElementById('deleteForm');
                form.action = `{{ url('admin/tasks') }}/${taskId}`;
                form.submit();
            }
        }
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush