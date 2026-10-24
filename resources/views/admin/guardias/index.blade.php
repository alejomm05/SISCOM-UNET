@extends('admin.layouts.app')

@section('title', 'Gestión de Guardias')
@section('page-title', 'Gestión de Guardias')
@section('page-subtitle', 'Administración del personal de seguridad')

@section('content')
<div class="row mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card metric-card bg-primary">
            <div class="card-body">
                <div class="card-trend">
                    <span class="badge bg-light text-primary">+{{ $guardias->count() }}</span>
                </div>
                <h5 class="card-title">Total Guardias</h5>
                <p class="card-value">{{ $guardias->count() }}</p>
                <i class="fas fa-user-shield card-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card metric-card bg-info">
            <div class="card-body">
                <div class="card-trend">
                    <span class="badge bg-light text-info">3 Turnos</span>
                </div>
                <h5 class="card-title">Distribución</h5>
                <p class="card-value">{{ $guardias->groupBy('shift')->count() }}</p>
                <i class="fas fa-users card-icon"></i>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-gradient-blue text-white">
            <div class="card-body py-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-1 fw-bold">Gestión de Personal de Seguridad</h3>
                        <p class="mb-0 opacity-90">Administración centralizada del equipo de guardias</p>
                    </div>
                    <a href="{{ route('admin.guardias.create') }}" class="btn btn-light btn-lg px-4 text-primary fw-bold">
                        <i class="fas fa-user-plus me-2"></i>Nuevo Guardia
                    </a>
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

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Listado de Guardias Activos</h5>
                    <div class="btn-group">
                        <button class="btn btn-outline-primary btn-sm active" data-filter="all">Todos</button>
                        <button class="btn btn-outline-success btn-sm" data-filter="active">Activos</button>
                        <button class="btn btn-outline-warning btn-sm" data-filter="inactive">Inactivos</button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="guardiasTable">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">#ID</th>
                                <th>Guardia</th>
                                <th>Contacto</th>
                                <th>Turno</th>
                                <th>Estado</th>
                                <th class="text-center pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($guardias as $guardia)
                            <tr class="guardia-row" data-status="{{ $guardia->status ?? 'active' }}">
                                <td class="ps-4 fw-bold text-primary">#{{ $guardia->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-3">
                                            <i class="fas fa-user-shield text-white"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $guardia->name }}</div>
                                            <small class="text-muted">Personal de seguridad</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $guardia->email }}</div>
                                    <small class="text-muted">Usuario de acceso</small>
                                </td>
                                <td>
                                    @if($guardia->shift)
                                        <span class="badge bg-info rounded-pill px-3 py-2">
                                            <i class="fas fa-clock me-1"></i>{{ $guardia->shift }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary rounded-pill px-3 py-2">
                                            <i class="fas fa-times me-1"></i>No asignado
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if(($guardia->status ?? 'active') === 'active')
                                        <span class="badge bg-success rounded-pill px-3 py-2">
                                            <i class="fas fa-circle me-1" style="font-size: 0.6rem;"></i>
                                            ACTIVO
                                        </span>
                                    @else
                                        <span class="badge bg-warning rounded-pill px-3 py-2">
                                            <i class="fas fa-circle me-1" style="font-size: 0.6rem;"></i>
                                            INACTIVO
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center pe-4">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.guardias.show', $guardia) }}" class="btn btn-info btn-sm px-3" 
                                           data-bs-toggle="tooltip" title="Ver Estadísticas">
                                            <i class="fas fa-chart-bar"></i>
                                        </a>
                                        
                                        <a href="{{ route('admin.guardias.edit', $guardia) }}" class="btn btn-warning btn-sm px-3" 
                                           data-bs-toggle="tooltip" title="Editar Guardia">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <button type="button" class="btn btn-danger btn-sm px-3" 
                                                data-bs-toggle="tooltip" title="Eliminar Guardia"
                                                onclick="confirmDelete({{ $guardia->id }}, '{{ $guardia->name }}')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-user-shield fa-3x mb-3"></i>
                                        <h5>No hay guardias registrados</h5>
                                        <p>Comienza agregando el primer guardia al sistema</p>
                                        <a href="{{ route('admin.guardias.create') }}" class="btn btn-primary mt-2">
                                            <i class="fas fa-plus me-2"></i>Agregar Primer Guardia
                                        </a>
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
    .bg-info { background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%); }
    .bg-warning { background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%); }

    .avatar-sm {
        width: 40px;
        height: 40px;
    }

    .guardia-row:hover {
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

        // Filtrado de guardias
        document.querySelectorAll('[data-filter]').forEach(button => {
            button.addEventListener('click', function() {
                const filter = this.getAttribute('data-filter');
                
                // Actualizar botones activos
                document.querySelectorAll('[data-filter]').forEach(btn => {
                    btn.classList.remove('active');
                });
                this.classList.add('active');
                
                // Filtrar filas
                document.querySelectorAll('.guardia-row').forEach(row => {
                    if (filter === 'all') {
                        row.style.display = '';
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

    // Confirmación para eliminar guardia
    function confirmDelete(guardiaId, guardiaName) {
        Swal.fire({
            title: '¿Confirmar eliminación?',
            html: `¿Está seguro de eliminar al guardia <strong>${guardiaName}</strong>?<br><span class="text-danger">Esta acción es irreversible.</span>`,
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
                form.action = `{{ url('admin/guardias') }}/${guardiaId}`;
                form.submit();
            }
        });
    }

    // Si no tienes SweetAlert2, puedes usar confirm nativo como fallback
    if (typeof Swal === 'undefined') {
        function confirmDelete(guardiaId, guardiaName) {
            if (confirm(`¿Está seguro de eliminar al guardia ${guardiaName}? Esta acción es irreversible.`)) {
                const form = document.getElementById('deleteForm');
                form.action = `{{ url('admin/guardias') }}/${guardiaId}`;
                form.submit();
            }
        }
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush