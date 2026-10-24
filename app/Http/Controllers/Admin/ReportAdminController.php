<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Usaremos DB para TRUNCATE
use Illuminate\Support\Facades\Storage; // Para eliminar archivos adjuntos

class ReportAdminController extends Controller
{
    /**
     * Muestra la lista de todos los reportes enviados.
     */
    public function index()
    {
        // ... (lógica existente) ...
        $reports = Report::with('user')->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.reports.index', compact('reports'));
    }

    /**
     * Elimina un reporte individual y su archivo adjunto.
     */
    public function destroy(Report $report)
    {
        // Eliminar el archivo adjunto si existe
        if ($report->image_path) {
            Storage::disk('public')->delete($report->image_path);
        }
        
        $report->delete();
        return redirect()->route('admin.reports.index')->with('success', 'Reporte y archivo adjunto eliminados individualmente.');
    }

    /**
     * Elimina TODOS los reportes y sus archivos adjuntos (VACIA la tabla).
     */
    public function clearAll()
    {
        $reports = Report::all();
        
        // 1. Eliminar archivos adjuntos (CRÍTICO)
        foreach ($reports as $report) {
             if ($report->image_path) {
                Storage::disk('public')->delete($report->image_path);
            }
        }
        
        // 2. Eliminar todos los registros de la base de datos
        DB::table('reports')->truncate(); 

        return redirect()->route('admin.reports.index')->with('success', '¡Historial de reportes y archivos adjuntos vaciado con éxito!');
    }
}