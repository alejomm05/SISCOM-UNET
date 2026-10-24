@extends('admin.layouts.app')

@section('title', 'Gestión de Emergencias')
@section('page-title', 'Gestión de Emergencias')
@section('page-subtitle', 'Control y seguimiento de alertas de seguridad')

@section('content')
<div class="row mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card metric-card bg-danger">
            <div class="card-body">
                <div class="card-trend">
                    <span class="badge bg-light text-danger">+5</span>
                </div>
                <h5 class="card-title">Emergencias Activas</h5>
                <p class="card-value">{{ $emergencies->where('status', 'reported')->count() }}</p>
                <i class="fas fa-exclamation-triangle card-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card metric-card bg-warning">
            <div class="card-body">
                <div class="card-trend">
                    <span class="badge bg-light text-warning">+2</span>
                </div>
                <h5 class="card-title">En Progreso</h5>
                <p class="card-value">{{ $emergencies->where('status', 'in_progress')->count() }}</p>
                <i class="fas fa-clock card-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card metric-card bg-success">
            <div class="card-body">
                <div class="card-trend">
                    <span class="badge bg-light text-success">+12</span>
                </div>
                <h5 class="card-title">Resueltas Hoy</h5>
                <p class="card-value">{{ $emergencies->where('status', 'resolved')->filter(fn ($e) => $e->created_at->isToday())->count() }}</p>
                <i class="fas fa-check-circle card-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card metric-card bg-info">
            <div class="card-body">
                <div class="card-trend">
                    <span class="badge bg-light text-info">24h</span>
                </div>
                <h5 class="card-title">Tiempo Promedio</h5>
                <p class="card-value">18min</p>
                <i class="fas fa-stopwatch card-icon"></i>
            </div>
        </div>
    </div>
</div>

<!-- Header con el mismo color azul del layout -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-gradient-blue text-white">
            <div class="card-body py-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-1 fw-bold">Sistema de Alertas de Emergencia</h3>
                        <p class="mb-0 opacity-90">Gestión centralizada de incidentes de seguridad</p>
                    </div>
                    <button type="button" class="btn btn-light btn-lg px-4 text-primary fw-bold" data-bs-toggle="modal" data-bs-target="#adminEmergencyModal">
                        <i class="fas fa-bullhorn me-2"></i>Nueva Alerta
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

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

<!-- Listado de Emergencias -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Listado de Emergencias Activas</h5>
                    <div class="btn-group">
                        <button class="btn btn-outline-primary btn-sm active" data-filter="all">Todas</button>
                        <button class="btn btn-outline-danger btn-sm" data-filter="reported">Reportadas</button>
                        <button class="btn btn-outline-warning btn-sm" data-filter="in_progress">En Progreso</button>
                        <button class="btn btn-outline-success btn-sm" data-filter="resolved">Resueltas</button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="emergenciesTable">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">#ID</th>
                                <th>Reportante</th>
                                <th>Incidente</th>
                                <th>Ubicación</th>
                                <th>Estado</th>
                                <th>Timestamp</th>
                                <th class="text-center pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($emergencies as $emergency)
                            <tr class="emergency-row" data-status="{{ $emergency->status }}">
                                <td class="ps-4 fw-bold text-primary">#{{ $emergency->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-3">
                                            <i class="fas fa-user-shield text-white"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $emergency->user->name ?? 'Sistema' }}</div>
                                            <small class="text-muted">Guardia</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $emergency->type }}</div>
                                    <small class="text-muted">{{ Str::limit($emergency->description, 60) }}</small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                        <span>{{ $emergency->location ?? 'Ubicación no especificada' }}</span>
                                    </div>
                                </td>
                                <td>
                                    @if ($emergency->status === 'reported')
                                        <span class="badge bg-danger rounded-pill px-3 py-2">
                                            <i class="fas fa-circle me-1" style="font-size: 0.6rem;"></i>
                                            REPORTADA
                                        </span>
                                    @elseif ($emergency->status === 'in_progress')
                                        <span class="badge bg-warning rounded-pill px-3 py-2">
                                            <i class="fas fa-clock me-1" style="font-size: 0.6rem;"></i>
                                            EN PROGRESO
                                        </span>
                                    @elseif ($emergency->status === 'resolved')
                                        <span class="badge bg-success rounded-pill px-3 py-2">
                                            <i class="fas fa-check me-1" style="font-size: 0.6rem;"></i>
                                            RESUELTA
                                        </span>
                                    @else
                                        <span class="badge bg-secondary rounded-pill px-3 py-2">{{ strtoupper($emergency->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">{{ $emergency->created_at->format('d/m/Y H:i') }}</small>
                                </td>
                                <td class="text-center pe-4">
                                    <div class="btn-group" role="group">
                                        @if ($emergency->status !== 'resolved')
                                            <button type="button" class="btn btn-success btn-sm px-3" 
                                                    data-bs-toggle="tooltip" title="Marcar como Resuelta"
                                                    onclick="confirmResolution({{ $emergency->id }})">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            
                                            <button type="button" class="btn btn-info btn-sm px-3" 
                                                    data-bs-toggle="tooltip" title="Asignar como Tarea"
                                                    data-emergency-id="{{ $emergency->id }}"
                                                    data-emergency-type="{{ $emergency->type }}"
                                                    data-emergency-desc="{{ $emergency->description }}"
                                                    onclick="openAssignTaskModal(this)">
                                                <i class="fas fa-clipboard-list"></i>
                                            </button>
                                        @else
                                            <span class="text-muted small">Finalizada</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3"></i>
                                        <h5>No hay alertas de emergencia registradas</h5>
                                        <p>El sistema se encuentra sin incidentes activos</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Crear Alerta Administrativa -->
<div class="modal fade" id="adminEmergencyModal" tabindex="-1" aria-labelledby="adminEmergencyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('admin.emergencies.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white rounded-top">
                    <div class="d-flex align-items-center">
                        <div class="modal-icon me-3">
                            <i class="fas fa-bullhorn fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="modal-title mb-0" id="adminEmergencyModalLabel">Emitir Alerta Administrativa</h5>
                            <small class="opacity-75">Crear una alerta de emergencia global</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label fw-semibold">Tipo de Emergencia</label>
                            <select name="type" id="type" class="form-select" required>
                                <option value="">Seleccionar tipo...</option>
                                <option value="Incendio">Incendio</option>
                                <option value="Intrusión">Intrusión</option>
                                <option value="Fallo Eléctrico Mayor">Fallo Eléctrico Mayor</option>
                                <option value="Accidente">Accidente</option>
                                <option value="Amenaza">Amenaza</option>
                                <option value="Otro">Otro (Especificar en descripción)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="location" class="form-label fw-semibold">Ubicación</label>
                            <input type="text" name="location" id="location" class="form-control" placeholder="Ej: Edificio A, Piso 3">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">Descripción Detallada</label>
                        <textarea name="description" id="description" class="form-control" rows="4" 
                                  placeholder="Describa la situación de emergencia con el mayor detalle posible..." required></textarea>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Importante:</strong> Esta alerta será enviada a todos los guardias activos y generará una notificación en tiempo real.
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger px-4">
                        <i class="fas fa-paper-plane me-2"></i>Emitir Alerta Global
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Asignar como Tarea -->
<div class="modal fade" id="assignTaskModal" tabindex="-1" aria-labelledby="assignTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form id="assignTaskForm" method="POST">
                @csrf
                <div class="modal-header bg-info text-white rounded-top">
                    <div class="d-flex align-items-center">
                        <div class="modal-icon me-3">
                            <i class="fas fa-clipboard-list fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="modal-title mb-0" id="assignTaskModalLabel">Asignar Emergencia como Tarea</h5>
                            <small class="opacity-75">Alta prioridad - Vencimiento 2 horas</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="_method" value="POST">
                    <input type="hidden" id="assignTaskRouteBase" value="{{ url('admin/emergencies') }}">
                    
                    <div class="emergency-info bg-light p-3 rounded mb-4">
                        <h6 class="fw-bold mb-2">Información de la Emergencia:</h6>
                        <p class="mb-1"><strong>Tipo:</strong> <span id="taskEmergencyType" class="text-primary"></span></p>
                        <p class="mb-0"><strong>Descripción:</strong> <span id="taskEmergencyDesc" class="text-muted"></span></p>
                    </div>

                    <div class="mb-4">
                        <label for="assigned_to" class="form-label fw-semibold">Asignar a Guardia</label>
                        <select name="assigned_to" id="assigned_to" class="form-select" required>
                            <option value="">-- Seleccionar Guardia --</option>
                            @foreach ($guardias as $guardia)
                                <option value="{{ $guardia->id }}">{{ $guardia->name }} - {{ $guardia->current_zone ?? 'Sin zona asignada' }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Esta acción creará una <strong>TAREA DE ALTA PRIORIDAD</strong> con vencimiento en 2 horas y notificará al guardia en tiempo real.
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info px-4">
                        <i class="fas fa-check me-2"></i>Asignar Tarea
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Formulario oculto para resolución -->
<form id="resolveForm" method="POST" style="display: none;">
    @csrf
    @method('PUT')
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

    .bg-danger { background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); }
    .bg-warning { background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%); }
    .bg-success { background: linear-gradient(135deg, #28a745 0%, #218838 100%); }
    .bg-info { background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); }

    .avatar-sm {
        width: 40px;
        height: 40px;
    }

    .emergency-row:hover {
        background-color: rgba(13, 71, 161, 0.02) !important;
        transform: translateY(-1px);
        transition: all 0.2s ease;
    }

    .table > :not(caption) > * > * {
        padding: 1rem 0.75rem;
        border-bottom: 1px solid #f0f0f0;
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

    .btn-group .btn {
        border-radius: 8px !important;
        margin: 0 2px;
    }

    .badge.rounded-pill {
        font-weight: 600;
        letter-spacing: 0.5px;
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

        // Filtrado de emergencias
        document.querySelectorAll('[data-filter]').forEach(button => {
            button.addEventListener('click', function() {
                const filter = this.getAttribute('data-filter');
                
                // Actualizar botones activos
                document.querySelectorAll('[data-filter]').forEach(btn => {
                    btn.classList.remove('active');
                });
                this.classList.add('active');
                
                // Filtrar filas
                document.querySelectorAll('.emergency-row').forEach(row => {
                    if (filter === 'all' || row.getAttribute('data-status') === filter) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });
    });

    // Confirmación para resolver emergencia
    function confirmResolution(emergencyId) {
        Swal.fire({
            title: '¿Confirmar resolución?',
            text: "¿Está seguro de marcar esta emergencia como RESUELTA?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, marcar como resuelta',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('resolveForm');
                form.action = `{{ url('admin/emergencies') }}/${emergencyId}/resolve`;
                form.submit();
            }
        });
    }

    // Abrir modal de asignación de tarea
    function openAssignTaskModal(button) {
        const emergencyId = button.getAttribute('data-emergency-id');
        const emergencyType = button.getAttribute('data-emergency-type');
        const emergencyDesc = button.getAttribute('data-emergency-desc');
        const routeBase = document.getElementById('assignTaskRouteBase').value;

        document.getElementById('taskEmergencyType').textContent = emergencyType;
        document.getElementById('taskEmergencyDesc').textContent = emergencyDesc;
        
        const form = document.getElementById('assignTaskForm');
        form.action = `${routeBase}/${emergencyId}/assign-task`;

        new bootstrap.Modal(document.getElementById('assignTaskModal')).show();
    }

    // Si no tienes SweetAlert2, puedes usar confirm nativo como fallback
    if (typeof Swal === 'undefined') {
        function confirmResolution(emergencyId) {
            if (confirm('¿Está seguro de marcar esta emergencia como RESUELTA?')) {
                const form = document.getElementById('resolveForm');
                form.action = `{{ url('admin/emergencies') }}/${emergencyId}/resolve`;
                form.submit();
            }
        }
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush