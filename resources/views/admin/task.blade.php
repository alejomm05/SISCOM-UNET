@extends('admin.layouts.app')

@section('content')
<h1 class="mb-4">Gestión e Historial de Tareas</h1>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card shadow">
            <div class="card-header bg-success text-white">Asignar Nueva Tarea</div>
            <div class="card-body">
                
                <form action="{{ route('admin.tasks.assign') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="assigned_to" class="form-label">Asignar a Guardia:</label>
                        <select name="assigned_to" id="assigned_to" class="form-select @error('assigned_to') is-invalid @enderror" required>
                            <option value="">-- Seleccionar Guardia --</option>
                            @foreach ($guardias as $guardia)
                                <option value="{{ $guardia->id }}" {{ old('assigned_to') == $guardia->id ? 'selected' : '' }}>
                                    {{ $guardia->name }} ({{ $guardia->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('assigned_to') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Título de la Tarea:</label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" class="form-control @error('title') is-invalid @enderror" required>
                        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Descripción:</label>
                        <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="due_date" class="form-label">Fecha y Hora Límite:</label>
                        <input type="datetime-local" name="due_date" id="due_date" value="{{ old('due_date') }}" class="form-control @error('due_date') is-invalid @enderror" required>
                        @error('due_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    <button type="submit" class="btn btn-success w-100">
                        Asignar Tarea
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header">Historial de Tareas ({{ $tasks->count() }} total)</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Asignado a</th>
                                <th>Límite</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tasks as $task)
                                <tr>
                                    <td>{{ $task->title }}</td>
                                    <td>{{ $task->assignedTo->name ?? 'N/A' }}</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($task->due_date)->tz('America/Caracas')->format('d/m/Y H:i') }}
                                    </td>
                                    <td>
                                        @php
                                            $statusClass = 'bg-secondary';
                                            if ($task->status === 'completed') $statusClass = 'bg-success';
                                            elseif ($task->status === 'pending' && $task->due_date < now()) $statusClass = 'bg-danger';
                                            elseif ($task->status === 'pending') $statusClass = 'bg-warning';
                                        @endphp
                                        <span class="badge {{ $statusClass }}">
                                            {{ ucfirst($task->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if($tasks->isEmpty())
                    <p class="text-center text-muted mt-4">No hay tareas en el historial.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection