@extends('admin.layouts.app')

@section('title', $contact->exists ? 'Editar Contacto' : 'Añadir Contacto')
@section('page-title', $contact->exists ? 'Editar Contacto de Emergencia' : 'Crear Nuevo Contacto')
@section('page-subtitle', $contact->exists ? 'Modificar información del contacto existente' : 'Agregar nueva entidad de soporte crítico')

@section('content')
<!-- Header con Acciones -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-gradient-blue text-white">
            <div class="card-body py-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-1 fw-bold">
                            @if ($contact->exists)
                                <i class="fas fa-edit me-2"></i>Editar Contacto: {{ $contact->name }}
                            @else
                                <i class="fas fa-plus-circle me-2"></i>Nuevo Contacto de Emergencia
                            @endif
                        </h3>
                        <p class="mb-0 opacity-90">
                            @if ($contact->exists)
                                Actualizar información del contacto existente
                            @else
                                Agregar nueva entidad de soporte crítico al directorio
                            @endif
                        </p>
                    </div>
                    <a href="{{ route('admin.contacts.index') }}" class="btn btn-light btn-lg px-4 text-primary fw-bold">
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
                    <i class="fas fa-user-circle me-2 text-primary"></i>
                    Información del Contacto
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ $contact->exists ? route('admin.contacts.update', $contact) : route('admin.contacts.store') }}" method="POST">
                    @csrf
                    @if ($contact->exists)
                        @method('PUT')
                    @endif

                    <div class="row">
                        <!-- Columna Izquierda -->
                        <div class="col-md-6">
                            <!-- Campo Institución -->
                            <div class="mb-4">
                                <label for="institution" class="form-label fw-semibold">
                                    <i class="fas fa-building me-2 text-primary"></i>
                                    Institución / Entidad
                                </label>
                                <input type="text" name="institution" id="institution" 
                                       class="form-control form-control-lg @error('institution') is-invalid @enderror" 
                                       value="{{ old('institution', $contact->institution) }}" 
                                       placeholder="Ej: Policía Nacional, Bomberos, Dirección de Seguridad Interna" 
                                       required>
                                @error('institution')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Nombre oficial de la entidad de soporte
                                </div>
                            </div>

                            <!-- Campo Nombre de Contacto -->
                            <div class="mb-4">
                                <label for="name" class="form-label fw-semibold">
                                    <i class="fas fa-user me-2 text-primary"></i>
                                    Nombre del Contacto
                                </label>
                                <input type="text" name="name" id="name" 
                                       class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $contact->name) }}" 
                                       placeholder="Nombre completo de la persona de contacto" 
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Campo Rol/Cargo -->
                            <div class="mb-4">
                                <label for="role" class="form-label fw-semibold">
                                    <i class="fas fa-briefcase me-2 text-primary"></i>
                                    Rol / Cargo
                                </label>
                                <input type="text" name="role" id="role" 
                                       class="form-control form-control-lg @error('role') is-invalid @enderror" 
                                       value="{{ old('role', $contact->role) }}" 
                                       placeholder="Ej: Coordinador de Turno, Jefe de Operaciones" 
                                       required>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror>
                                <div class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Posición o cargo del contacto dentro de la institución
                                </div>
                            </div>
                        </div>

                        <!-- Columna Derecha -->
                        <div class="col-md-6">
                            <!-- Campo Número de Teléfono -->
                            <div class="mb-4">
                                <label for="phone_number" class="form-label fw-semibold">
                                    <i class="fas fa-phone-alt me-2 text-success"></i>
                                    Número de Teléfono
                                </label>
                                <input type="text" name="phone_number" id="phone_number" 
                                       class="form-control form-control-lg @error('phone_number') is-invalid @enderror" 
                                       value="{{ old('phone_number', $contact->phone_number) }}" 
                                       placeholder="Ej: +58 412 123 4567" 
                                       required>
                                @error('phone_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Formato internacional recomendado
                                </div>
                            </div>

                            <!-- Campo Link de WhatsApp -->
                            <div class="mb-4">
                                <label for="whatsapp_link" class="form-label fw-semibold">
                                    <i class="fab fa-whatsapp me-2 text-success"></i>
                                    Enlace de WhatsApp
                                </label>
                                <input type="url" name="whatsapp_link" id="whatsapp_link" 
                                       class="form-control form-control-lg @error('whatsapp_link') is-invalid @enderror" 
                                       value="{{ old('whatsapp_link', $contact->whatsapp_link) }}" 
                                       placeholder="https://wa.me/584121234567">
                                @error('whatsapp_link')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Enlace directo para chatear por WhatsApp
                                </div>
                            </div>

                            <!-- Información Adicional -->
                            <div class="alert alert-info mt-4">
                                <div class="d-flex">
                                    <i class="fas fa-info-circle fa-2x me-3 text-info"></i>
                                    <div>
                                        <h6 class="alert-heading mb-2">Información Importante</h6>
                                        <p class="mb-1">• Los contactos de emergencia son críticos para la respuesta inmediata</p>
                                        <p class="mb-1">• Verifique que los números estén actualizados regularmente</p>
                                        <p class="mb-0">• El enlace de WhatsApp permite comunicación directa desde la app móvil</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('admin.contacts.index') }}" class="btn btn-outline-secondary btn-lg px-4">
                                    <i class="fas fa-times me-2"></i>Cancelar
                                </a>
                                <button type="submit" class="btn {{ $contact->exists ? 'btn-warning' : 'btn-success' }} btn-lg px-5">
                                    <i class="fas fa-save me-2"></i>
                                    {{ $contact->exists ? 'Actualizar Contacto' : 'Guardar Contacto' }}
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