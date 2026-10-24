<?php

namespace App\Http\Controllers\Admin;

use App\Models\Report;
use App\Models\Task;
use App\Models\User;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        // Métricas básicas
        $totalReports = Report::count();
        $totalTasks = Task::count();
        $completedTasks = Task::where('status', 'completed')->count();
        $totalGuards = User::count();

        // Datos para gráfica de tareas (como arreglo plano)
        $tasksData = [
            'assigned' => $totalTasks,
            'completed' => $completedTasks,
        ];

        // Datos para gráfica de reportes por tipo (como arreglo plano)
        $reportTypes = Report::selectRaw('type, count(*) as count')
                            ->groupBy('type')
                            ->pluck('count', 'type')
                            ->toArray();

        return view('admin.dashboard', compact(
            'totalReports',
            'totalTasks',
            'completedTasks',
            'totalGuards',
            'tasksData',
            'reportTypes'
        ));
    }
}