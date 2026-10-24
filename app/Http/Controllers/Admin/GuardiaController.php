<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Carbon\Carbon; // 💡 ¡IMPORTANTE! Importamos Carbon

class GuardiaController extends Controller
{
    /**
     * Muestra la lista de todos los Guardias.
     */
    public function index()
    {
        // 🚨 Solo Guardias (filtrar si es necesario, pero asumimos que User::all() solo trae Guardias o que filtramos después)
        $guardias = User::where('role', 'guardia')->get(); // Filtrado más seguro por rol

        // ==========================================================
        // 💡 LÓGICA CORREGIDA PARA CALCULAR GUARDIAS "EN TURNO"
        // ==========================================================
        $guardiasEnTurno = $guardias->filter(function ($guardia) {
            
            // Si no tiene turno asignado, no puede estar "En Turno"
            if (!$guardia->shift) {
                return false;
            }

            $now = Carbon::now();
            $shift = $guardia->shift;

            // Definición de horarios
            $horarios = [
                'Mañana' => ['06:00', '14:00'],
                'Tarde' => ['14:00', '22:00'],
                'Noche' => ['22:00', '06:00'], // Cruza la medianoche
            ];

            if (!isset($horarios[$shift])) {
                return false; // Turno desconocido
            }
            
            [$startTime, $endTime] = $horarios[$shift];
            
            $start = Carbon::createFromTimeString($startTime);
            $end = Carbon::createFromTimeString($endTime);
            
            if ($shift === 'Noche') {
                // El turno de noche (22:00 a 06:00) cruza la medianoche.
                // Si la hora de inicio es mayor a la hora de fin (22:00 > 06:00 del mismo día),
                // el turno se extiende al día siguiente.
                if ($start->greaterThan($end)) {
                    // Opción A: Es hoy entre 22:00 y medianoche (00:00)
                    // Opción B: Es hoy entre 00:00 y 06:00 (lo que implica que empezó el día anterior)
                    return $now->between($start, Carbon::createFromTimeString('23:59:59')) ||
                           $now->between(Carbon::createFromTimeString('00:00:00'), $end);
                }
            }
            
            // Para Mañana y Tarde (turnos dentro del mismo día)
            return $now->between($start, $end);

        })->count();
        // ==========================================================
        
        // Retornamos la vista principal del CRUD
        return view('admin.guardias.index', compact('guardias', 'guardiasEnTurno'));
    }

    /**
     * Muestra el formulario para crear un nuevo Guardia.
     */
    public function create()
    {
        return view('admin.guardias.create');
    }

    /**
     * Almacena un nuevo Guardia en la base de datos.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            // 💡 Asumimos que tienes un campo 'role' o 'shift' para la gestión
            'role' => ['nullable', Rule::in(['guardia', 'supervisor'])], // Opcional si solo creas Guardias
            'shift' => 'nullable|string|max:100', // Campo para el turno asignado
        ]);
        
        // Sobrescribimos el rol si la aplicación móvil solo usa 'guardia'
        $validatedData['role'] = 'guardia'; 
        $validatedData['password'] = Hash::make($validatedData['password']);

        User::create($validatedData);

        return redirect()->route('admin.guardias.index')->with('success', 'Guardia creado exitosamente.');
    }

    /**
     * Muestra los detalles y estadísticas individuales de un Guardia.
     */
   public function show(User $guardia)
    {
        // 1. Datos para KPIs (ya existentes)
        $totalReports = $guardia->reports()->count();
        $totalTasks = $guardia->tasks()->count();
        $completedTasks = $guardia->tasks()->where('status', 'completed')->count();
        
        // 2. 🚨 DATOS PARA ACTIVIDAD RECIENTE (NUEVO)
        // Límite de 10 elementos por lista, ordenado por fecha descendente.
        
        $recentAssignedTasks = $guardia->tasks()
                                       ->orderBy('created_at', 'desc')
                                       ->limit(10)
                                       ->get();
        
        $recentCompletedTasks = $guardia->tasks()
                                        ->where('status', 'completed')
                                        ->orderBy('updated_at', 'desc') // Ordenamos por fecha de finalización (updated_at)
                                        ->limit(10)
                                        ->get();

        $recentReports = $guardia->reports()
                                 ->orderBy('created_at', 'desc')
                                 ->limit(10)
                                 ->get();
        
        // Retornamos todos los datos a la vista
        return view('admin.guardias.show', compact(
            'guardia', 
            'totalReports', 
            'totalTasks', 
            'completedTasks',
            // Nuevas variables
            'recentAssignedTasks',
            'recentCompletedTasks',
            'recentReports'
        ));
    }


    /**
     * Muestra el formulario para editar un Guardia.
     */
    public function edit(User $guardia)
    {
        return view('admin.guardias.edit', compact('guardia'));
    }

    /**
     * Actualiza los datos de un Guardia.
     */
    public function update(Request $request, User $guardia)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            // El email debe ser único, excluyendo el email actual del guardia
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($guardia->id)],
            'password' => 'nullable|string|min:8', // Contraseña opcional
            'shift' => 'nullable|string|max:100', 
        ]);

        if (!empty($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        } else {
            unset($validatedData['password']); // No actualizar si está vacío
        }
        
        $guardia->update($validatedData);

        return redirect()->route('admin.guardias.index')->with('success', 'Guardia actualizado exitosamente.');
    }

    /**
     * Elimina un Guardia.
     */
    public function destroy(User $guardia)
    {
        $guardia->delete();
        return redirect()->route('admin.guardias.index')->with('success', 'Guardia eliminado exitosamente.');
    }

    
}