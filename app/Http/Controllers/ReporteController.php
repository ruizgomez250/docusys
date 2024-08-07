<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RegistroAsistencia;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;

class ReporteAsistenciaController extends Controller
{
    public function generarReporte($fecha_sesion)
    {
        $asistencias = RegistroAsistencia::where('fecha_sesion', $fecha_sesion)
            ->with('legislador')
            ->get();

        $html = '<!DOCTYPE html>
        <html>
        <head>
            <title>Reporte de Asistencia</title>
            <style>
                table {
                    width: 100%;
                    border-collapse: collapse;
                }
                th, td {
                    border: 1px solid black;
                    padding: 8px;
                    text-align: left;
                }
                th {
                    background-color: #f2f2f2;
                }
            </style>
        </head>
        <body>
            <h1>Reporte de Asistencia</h1>
            <p>Fecha de Sesión: ' . $fecha_sesion . '</p>
            <table>
                <thead>
                    <tr>
                        <th>ID Legislador</th>
                        <th>Nombre</th>
                        <th>Estado</th>
                        <th>Justificación</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($asistencias as $asistencia) {
            $html .= '<tr>
                <td>' . $asistencia->legislador->id . '</td>
                <td>' . $asistencia->legislador->nombre . ' ' . $asistencia->legislador->apellido . '</td>
                <td>' . $asistencia->estado . '</td>
                <td>' . $asistencia->justificacion . '</td>
            </tr>';
        }

        $html .= '</tbody>
            </table>
        </body>
        </html>';

        $pdf = PDF::loadHTML($html);

        return $pdf->download('reporte_asistencia_' . $fecha_sesion . '.pdf');
    }
}
