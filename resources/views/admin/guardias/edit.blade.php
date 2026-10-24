@extends('admin.layouts.app')

@section('title', 'Editar Guardia: ' . $guardia->name)
@section('page-title', 'Editar Información del Guardia')
@section('page-subtitle', 'Actualizar datos del miembro del equipo de seguridad')

@section('content')
<!-- Header con Acciones -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-gradient-blue text-white">
            <div class="card-body py-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-1 fw-bold">
                            <i class="fas fa-edit me-2"></i>Editar Guardia
                        </h3>
                        <p class="mb-0 opacity-90">Actualizar información de {{ $guardia->name }}</p>
                    </div>
                    <a href="{{ route('admin.guardias.index') }}" class="btn btn-light btn-lg px-4 text-primary fw-bold">
                        <i class="fas fa-arrow-left me-2"></i>Volver al Listado
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Formulario -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-user-edit me-2 text-primary"></i>
                    Editar Información del Guardia
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.guardias.update', $guardia) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <!-- Columna Izquierda -->
                        <div class="col-md-6">
                            <!-- Campo Nombre -->
                            <div class="mb-4">
                                <label for="name" class="form-label fw-semibold">
                                    <i class="fas fa-user me-2 text-primary"></i>
                                    Nombre Completo
                                </label>
                                <input type="text" name="name" id="name" 
                                       class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $guardia->name) }}" 
                                       placeholder="Nombre completo del guardia" 
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Campo Email -->
                            <div class="mb-4">
                                <label for="email" class="form-label fw-semibold">
                                    <i class="fas fa-envelope me-2 text-primary"></i>
                                    Email / Usuario
                                </label>
                                <input type="email" name="email" id="email" 
                                       class="form-control form-control-lg @error('email') is-invalid @enderror" 
                                       value="{{ old('email', $guardia->email) }}" 
                                       placeholder="correo@unet.edu.ve" 
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Usuario para acceso a la aplicación móvil
                                </div>
                            </div>
                        </div>

                        <!-- Columna Derecha -->
                        <div class="col-md-6">
                            <!-- Campo Contraseña -->
                            <div class="mb-4">
                                <label for="password" class="form-label fw-semibold">
                                    <i class="fas fa-lock me-2 text-primary"></i>
                                    Nueva Contraseña
                                </label>
                                <input type="password" name="password" id="password" 
                                       class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                       placeholder="Dejar vacío para no cambiar">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Solo complete si desea cambiar la contraseña actual
                                </div>
                            </div>

                            <!-- Campo Turno -->
                            <div class="mb-4">
                                <label for="shift" class="form-label fw-semibold">
                                    <i class="fas fa-clock me-2 text-primary"></i>
                                    Turno Asignado
                                </label>
                                <select name="shift" id="shift" class="form-select form-select-lg @error('shift') is-invalid @enderror">
                                    <option value="">-- Seleccionar Turno (Opcional) --</option>
                                    <option value="Mañana" {{ old('shift', $guardia->shift) == 'Mañana' ? 'selected' : '' }}>Mañana (6:00 - 14:00)</option>
                                    <option value="Tarde" {{ old('shift', $guardia->shift) == 'Tarde' ? 'selected' : '' }}>Tarde (14:00 - 22:00)</option>
                                    <option value="Noche" {{ old('shift', $guardia->shift) == 'Noche' ? 'selected' : '' }}>Noche (22:00 - 6:00)</option>
                                </select>
                                @error('shift')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror>
                                <div class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Reasignar turno para organización del personal
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información de Registro -->
                    <div class="alert alert-warning mt-4">
                        <div class="d-flex">
                            <i class="fas fa-history fa-2x me-3 text-warning"></i>
                            <div>
                                <h6 class="alert-heading mb-2">Información de Registro</h6>
                                <p class="mb-1"><strong>Fecha de creación:</strong> {{ $guardia->created_at->format('d/m/Y H:i') }}</p>
                                <p class="mb-1"><strong>Última actualización:</strong> {{ $guardia->updated_at->format('d/m/Y H:i') }}</p>
                                <p class="mb-0"><strong>Estado actual:</strong> 
                                    <span class="badge bg-success">Activo</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('admin.guardias.index') }}" class="btn btn-outline-secondary btn-lg px-4">
                                    <i class="fas fa-times me-2"></i>Cancelar
                                </a>
                                <button type="submit" class="btn btn-warning btn-lg px-5">
                                    <i class="fas fa-save me-2"></i>Actualizar Guardia
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
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

    .form-label {
        font-weight: 600;
        color: var(--text-color);
        margin-bottom: 8px;
    }

    .form-text {
        font-size: 0.85rem;
    }

    .alert {
        border-radius: 12px;
        border: none;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
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
</style>
@endpush