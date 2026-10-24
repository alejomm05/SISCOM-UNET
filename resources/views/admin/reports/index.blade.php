@extends('admin.layouts.app')

@section('title', 'Reportes de Guardias - SISCOM UNET')
@section('page-title', 'Gestión de Reportes e Incidencias')
@section('page-subtitle', 'Control y seguimiento de reportes del personal de seguridad')

@section('content')
<!-- Estadísticas Rápidas -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card metric-card bg-primary">
            <div class="card-body">
                <div class="card-trend">
                    <span class="badge bg-light text-primary">+{{ $stats['total'] }}</span>
                </div>
                <h5 class="card-title">Total Reportes</h5>
                <p class="card-value">{{ $stats['total'] }}</p>
                <i class="fas fa-file-alt card-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card metric-card bg-success">
            <div class="card-body">
                <div class="card-trend">
                    <span class="badge bg-light text-success">{{ $stats['sospechoso'] }}</span>
                </div>
                <h5 class="card-title">Sospechosos</h5>
                <p class="card-value">{{ $stats['sospechoso'] }}</p>
                <i class="fas fa-exclamation-circle card-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card metric-card bg-warning">
            <div class="card-body">
                <div class="card-trend">
                    <span class="badge bg-light text-warning">{{ $stats['daño'] }}</span>
                </div>
                <h5 class="card-title">Daños</h5>
                <p class="card-value">{{ $stats['daño'] }}</p>
                <i class="fas fa-tools card-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card metric-card bg-info">
            <div class="card-body">
                <div class="card-trend">
                    <span class="badge bg-light text-info">{{ $stats['with_files'] }}</span>
                </div>
                <h5 class="card-title">Con Archivos</h5>
                <p class="card-value">{{ $stats['with_files'] }}</p>
                <i class="fas fa-paperclip card-icon"></i>
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
                        <h3 class="mb-1 fw-bold">Sistema de Reportes e Incidencias</h3>
                        <p class="mb-0 opacity-90">Control centralizado de reportes del personal de seguridad</p>
                    </div>
                    <div class="d-flex gap-3">
                        <form action="{{ route('admin.reports.clearAll') }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-light btn-lg px-4 text-danger fw-bold" 
                                    onclick="return confirmClearAll()">
                                <i class="fas fa-eraser me-2"></i>Vaciar Todo
                            </button>
                        </form>
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

<!-- Filtros y Búsqueda -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body py-3">
                <div class="row g-3">
                    <div class="col-md-5">
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="searchReports" 
                                   placeholder="Buscar reportes por descripción, ubicación o guardia...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select form-select-lg" id="typeFilter">
                            <option value="">Todos los tipos</option>
                            {{-- Mismos tipos que valida Api\ReportController --}}
                            <option value="sospechoso">Sospechoso</option>
                            <option value="daño">Daño</option>
                            <option value="emergencia">Emergencia</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select form-select-lg" id="attachmentFilter">
                            <option value="">Todos</option>
                            <option value="with_files">Con Archivos</option>
                            <option value="without_files">Sin Archivos</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary btn-lg w-100" id="applyFilters">
                            <i class="fas fa-filter me-2"></i>Filtrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Reportes -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Reportes Registrados</h5>
                    <small class="text-muted">Mostrando {{ $reports->count() }} reportes</small>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="reportsTable">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">#ID</th>
                                <th>Tipo</th>
                                <th>Guardia</th>
                                <th>Ubicación</th>
                                <th>Descripción</th>
                                <th>Archivo</th>
                                <th>Fecha</th>
                                <th class="text-center pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($reports as $report)
                            <tr class="report-row" 
                                data-type="{{ $report->type }}"
                                data-has-file="{{ $report->image_path ? 'with_files' : 'without_files' }}"
                                data-search="{{ mb_strtolower($report->description . ' ' . $report->location_text . ' ' . ($report->user->name ?? '')) }}">
                                <td class="ps-4 fw-bold text-primary">#{{ $report->id }}</td>
                                <td>
                                    @if ($report->type === 'sospechoso')
                                        <span class="badge bg-danger rounded-pill px-3 py-2">
                                            <i class="fas fa-user-secret me-1"></i>SOSPECHOSO
                                        </span>
                                    @elseif ($report->type === 'daño')
                                        <span class="badge bg-warning rounded-pill px-3 py-2">
                                            <i class="fas fa-tools me-1"></i>DAÑO
                                        </span>
                                    @elseif ($report->type === 'emergencia')
                                        <span class="badge bg-dark rounded-pill px-3 py-2">
                                            <i class="fas fa-bell me-1"></i>EMERGENCIA
                                        </span>
                                    @else
                                        <span class="badge bg-secondary rounded-pill px-3 py-2">
                                            <i class="fas fa-file me-1"></i>{{ strtoupper($report->type) }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-3">
                                            <i class="fas fa-user-shield text-white"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $report->user->name ?? 'Usuario Desconocido' }}</div>
                                            <small class="text-muted">Guardia</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                        <span class="fw-semibold">{{ $report->location_text ?? 'Ubicación no especificada' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="report-description">{{ Str::limit($report->description, 80) }}</span>
                                </td>
                                <td>
                                    @if ($report->image_path)
                                        @php
                                            $extension = pathinfo($report->image_path, PATHINFO_EXTENSION);
                                            $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif']);
                                            $isVideo = in_array($extension, ['mp4', 'mov', 'avi']);
                                            $isAudio = in_array($extension, ['mp3', 'wav']);
                                        @endphp
                                        <button type="button" class="btn btn-info btn-sm px-3" 
                                                data-bs-toggle="tooltip" title="Ver Archivo Adjunto"
                                                data-file-path="{{ asset('storage/' . $report->image_path) }}"
                                                data-file-type="{{ $isImage ? 'image' : ($isVideo ? 'video' : ($isAudio ? 'audio' : 'file')) }}"
                                                onclick="openFileModal(this)">
                                            <i class="fas {{ $isImage ? 'fa-image' : ($isVideo ? 'fa-video' : ($isAudio ? 'fa-volume-up' : 'fa-file')) }} me-1"></i>
                                            {{ $isImage ? 'Imagen' : ($isVideo ? 'Video' : ($isAudio ? 'Audio' : 'Archivo')) }}
                                        </button>
                                    @else
                                        <span class="badge bg-light text-muted rounded-pill px-3 py-2">
                                            <i class="fas fa-times me-1"></i>Sin Adjunto
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-calendar-alt text-primary me-2"></i>
                                        <div>
                                            <div class="fw-semibold">{{ $report->created_at->format('d/m/Y') }}</div>
                                            <small class="text-muted">{{ $report->created_at->format('H:i') }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center pe-4">
                                    <div class="btn-group" role="group">
                                        @if ($report->image_path)
                                        <button type="button" class="btn btn-info btn-sm px-3" 
                                                data-bs-toggle="tooltip" title="Ver Archivo"
                                                data-file-path="{{ asset('storage/' . $report->image_path) }}"
                                                data-file-type="{{ $isImage ? 'image' : ($isVideo ? 'video' : ($isAudio ? 'audio' : 'file')) }}"
                                                onclick="openFileModal(this)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @endif
                                        
                                        <button type="button" class="btn btn-danger btn-sm px-3" 
                                                data-bs-toggle="tooltip" title="Eliminar Reporte"
                                                onclick="confirmDelete({{ $report->id }}, '{{ addslashes(Str::limit($report->description, 50)) }}')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-file-alt fa-3x mb-3"></i>
                                        <h5>No hay reportes registrados</h5>
                                        <p>El sistema no tiene reportes de incidencia activos</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginación Mejorada -->
                @if($reports->hasPages())
                <div class="card-footer bg-white border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            <i class="fas fa-list-alt me-1"></i>
                            Mostrando <strong>{{ $reports->firstItem() ?? 0 }}-{{ $reports->lastItem() ?? 0 }}</strong> de 
                            <strong>{{ $reports->total() }}</strong> reportes
                        </div>
                        <nav aria-label="Navegación de reportes">
                            <ul class="pagination pagination-sm mb-0">
                                <!-- Enlace Anterior -->
                                @if($reports->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link">
                                            <i class="fas fa-chevron-left"></i>
                                        </span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $reports->previousPageUrl() }}" aria-label="Anterior">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    </li>
                                @endif

                                <!-- Números de Página -->
                                @php
                                    $current = $reports->currentPage();
                                    $last = $reports->lastPage();
                                    $start = max(1, $current - 2);
                                    $end = min($last, $current + 2);
                                @endphp

                                @if($start > 1)
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $reports->url(1) }}">1</a>
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
                                            <a class="page-link" href="{{ $reports->url($i) }}">{{ $i }}</a>
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
                                        <a class="page-link" href="{{ $reports->url($last) }}">{{ $last }}</a>
                                    </li>
                                @endif

                                <!-- Enlace Siguiente -->
                                @if($reports->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $reports->nextPageUrl() }}" aria-label="Siguiente">
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

<!-- Modal: Visualización de Archivos -->
<div class="modal fade" id="fileModal" tabindex="-1" aria-labelledby="fileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white rounded-top">
                <div class="d-flex align-items-center">
                    <div class="modal-icon me-3">
                        <i class="fas fa-file-alt fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="fileModalLabel">Archivo Adjunto del Reporte</h5>
                        <small class="opacity-75">Visualización de contenido multimedia</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-0" id="fileContent" style="min-height: 400px; background: #f8f9fa;">
                <!-- Contenido inyectado por JavaScript -->
            </div>
            <div class="modal-footer bg-light rounded-bottom">
                <a id="downloadLink" href="#" download class="btn btn-primary">
                    <i class="fas fa-download me-2"></i>Descargar Archivo
                </a>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cerrar
                </button>
            </div>
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
    .bg-info { background: linear-gradient(135deg, #17a2b8 0%, #42A5F5 100%); }

    .avatar-sm {
        width: 40px;
        height: 40px;
    }

    .report-row:hover {
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

    .form-control-lg, .form-select-lg {
        border-radius: 12px;
        border: 2px solid #e9ecef;
        padding: 12px 16px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-control-lg:focus, .form-select-lg:focus {
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

    .cursor-pointer {
        cursor: pointer;
    }

    .report-description {
        max-width: 300px;
    }

    /* Estilos para la paginación */
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

        // Filtrado de reportes
        document.getElementById('applyFilters').addEventListener('click', function() {
            const typeFilter = document.getElementById('typeFilter').value;
            const attachmentFilter = document.getElementById('attachmentFilter').value;
            const searchTerm = document.getElementById('searchReports').value.toLowerCase();
            
            document.querySelectorAll('.report-row').forEach(row => {
                const rowType = row.getAttribute('data-type');
                const rowHasFile = row.getAttribute('data-has-file');
                const rowSearch = row.getAttribute('data-search');
                
                let showRow = true;
                
                // Filtro por tipo
                if (typeFilter && rowType !== typeFilter) {
                    showRow = false;
                }
                
                // Filtro por archivo adjunto
                if (attachmentFilter && rowHasFile !== attachmentFilter) {
                    showRow = false;
                }
                
                // Filtro por búsqueda
                if (searchTerm && !rowSearch.includes(searchTerm)) {
                    showRow = false;
                }
                
                row.style.display = showRow ? '' : 'none';
            });
        });

        // Buscar al presionar Enter
        document.getElementById('searchReports').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('applyFilters').click();
            }
        });
    });

    // Modal para archivos
    function openFileModal(button) {
        const filePath = button.getAttribute('data-file-path');
        const fileType = button.getAttribute('data-file-type');
        
        const fileContent = document.getElementById('fileContent');
        const downloadLink = document.getElementById('downloadLink');
        const fileModal = new bootstrap.Modal(document.getElementById('fileModal'));
        
        fileContent.innerHTML = '';
        downloadLink.href = filePath;

        if (fileType === 'image') {
            fileContent.innerHTML = `
                <div class="d-flex align-items-center justify-content-center h-100">
                    <img src="${filePath}" class="img-fluid rounded" alt="Archivo adjunto del reporte" style="max-height: 70vh;">
                </div>
            `;
        } else if (fileType === 'video') {
            fileContent.innerHTML = `
                <div class="d-flex align-items-center justify-content-center h-100 p-4">
                    <video controls src="${filePath}" class="rounded" style="max-height: 70vh; max-width: 100%;">
                        Tu navegador no soporta la reproducción de video.
                    </video>
                </div>
            `;
        } else if (fileType === 'audio') {
            fileContent.innerHTML = `
                <div class="d-flex align-items-center justify-content-center h-100 p-4">
                    <audio controls src="${filePath}" class="w-100">
                        Tu navegador no soporta la reproducción de audio.
                    </audio>
                </div>
            `;
        } else {
            fileContent.innerHTML = `
                <div class="d-flex align-items-center justify-content-center h-100">
                    <div class="text-center">
                        <i class="fas fa-file fa-5x text-muted mb-3"></i>
                        <p class="text-muted">No se puede previsualizar este tipo de archivo</p>
                        <p class="small text-muted">Por favor, descargue el archivo para visualizarlo</p>
                    </div>
                </div>
            `;
        }
        
        fileModal.show();
    }

    // Detener reproducción al cerrar modal
    document.getElementById('fileModal').addEventListener('hidden.bs.modal', function () {
        const video = this.querySelector('video');
        const audio = this.querySelector('audio');
        if (video) video.pause();
        if (audio) audio.pause();
    });

    // Confirmación para eliminar reporte individual
    function confirmDelete(reportId, reportDescription) {
        Swal.fire({
            title: '¿Confirmar eliminación?',
            html: `¿Está seguro de eliminar el reporte <strong>"${reportDescription}"</strong>?<br><span class="text-danger">Esta acción eliminará también el archivo adjunto y no se puede deshacer.</span>`,
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
                form.action = `{{ url('admin/reports') }}/${reportId}`;
                form.submit();
            }
        });
    }

    // Confirmación para vaciar todos los reportes
    function confirmClearAll() {
        return confirm('⚠️ ADVERTENCIA CRÍTICA:\n\nEsta acción eliminará TODOS los reportes del sistema y sus archivos adjuntos.\n\n¿Está completamente seguro de que desea continuar?\n\nEsta operación NO se puede deshacer.');
    }

    // Función para ver detalles del reporte (puede expandirse)
    function showReportDetails(reportId) {
        // Aquí puedes implementar un modal de detalles más elaborado
        Swal.fire({
            title: 'Detalles del Reporte',
            text: 'Funcionalidad de detalles en desarrollo...',
            icon: 'info',
            confirmButtonText: 'Entendido'
        });
    }

    // Fallback para SweetAlert2
    if (typeof Swal === 'undefined') {
        function confirmDelete(reportId, reportDescription) {
            if (confirm(`¿Está seguro de eliminar el reporte "${reportDescription}"? Esta acción no se puede deshacer.`)) {
                const form = document.getElementById('deleteForm');
                form.action = `{{ url('admin/reports') }}/${reportId}`;
                form.submit();
            }
        }
        
        function showReportDetails(reportId) {
            alert('Detalles del reporte #' + reportId + ' - Funcionalidad en desarrollo');
        }
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush