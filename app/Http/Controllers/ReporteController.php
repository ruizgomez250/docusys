<?php

namespace App\Http\Controllers;

use App\Models\MesaEntrada;
use App\Models\Origen;
use App\Models\TipoDoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use TCPDF;


class ReporteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $portiposdocs = DB::select("
    SELECT 
        DATE_FORMAT(me.fecha_recepcion, '%Y-%m') AS mes,
        LOWER(td.nombre) AS tipo_doc, -- Convertir a minúsculas
        COUNT(*) AS cantidad
    FROM mesa_entrada me
    JOIN tipo_docs td ON me.id_tipo_doc = td.id
    WHERE me.fecha_recepcion >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
    GROUP BY mes, td.nombre
    HAVING cantidad > 0 -- Filtrar solo los que tienen más de 0 documentos
    ORDER BY mes ASC, td.nombre ASC;
");


        $portiposdocs = collect($portiposdocs);

        $documentosporfechas = DB::select("
        SELECT 
            DATE_FORMAT(me.fecha_recepcion, '%Y-%m') AS mes,
            COUNT(*) AS cantidad
        FROM mesa_entrada me
        WHERE me.fecha_recepcion >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
        GROUP BY mes
        ORDER BY mes ASC;
    ");

        $documentosporfechas = collect($documentosporfechas);
        return view('reportes.index', ['documentosporfechas' => $documentosporfechas, 'portiposdocs' => $portiposdocs]);
    }




    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Obtenés todos los registros de la tabla tipo_docs
        $tiposdocs = TipoDoc::all();
        $origen = $origen = Origen::where('subindice', '<', 1)->get();



        // Retornás la vista con los datos
        return view('reportes.create', [
            'tiposdocs' => $tiposdocs,
            'origen' => $origen
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function pdfreportes($fechadesde, $fechahasta, $id = null, $tiporeporte)
    {
        $documentosporfechas = '';
        $titulo = '';
        if ($tiporeporte == 1) {
            $desdeC = \Carbon\Carbon::parse($fechadesde)->format('d/m/Y'); // Convierte la fecha de inicio al formato dd/mm/yyyy
            $hastaC = \Carbon\Carbon::parse($fechahasta)->format('d/m/Y'); // Convierte la fecha de fin al formato dd/mm/yyyy

            $titulo = 'Reporte por origen Desde ' . $desdeC . ' Hasta ' . $hastaC;
            $origen = Origen::find($id);

            $origen = Origen::find($id);

            // Traer las mesas de entrada donde el 'indice' de 'origen' coincida con 'id_origen' en 'mesa_entrada'
            // y que también estén dentro del rango de fechas
            $documentosporfechas = MesaEntrada::join('origen', 'mesa_entrada.id_origen', '=', 'origen.id')
                ->where('origen.indice', $origen->indice) // Comparar el indice
                ->whereBetween('mesa_entrada.fecha_recepcion', [$fechadesde, $fechahasta]) // Rango de fechas
                ->with(['user', 'origen', 'tipoDoc', 'destino']) // Relaciones si quieres usarlas
                ->orderBy('mesa_entrada.fecha_recepcion', 'asc')
                ->get();
        } else if ($tiporeporte == 2) {
            $documentosporfechas = MesaEntrada::where('tipoDoc', $id)
                ->whereBetween('fecha_recepcion', [$fechadesde, $fechahasta])
                ->with(['user', 'origen', 'tipoDoc', 'destino']) // relaciones si querés usarlas en la vista
                ->orderBy('fecha_recepcion', 'asc')
                ->get();
        } else {
            $documentosporfechas = MesaEntrada::whereBetween('fecha_recepcion', [$fechadesde, $fechahasta])
                ->with(['user', 'origen', 'tipoDoc', 'destino']) // relaciones si querés usarlas en la vista
                ->orderBy('fecha_recepcion', 'asc')
                ->get();
        }
        $agrupados = $documentosporfechas->groupBy(function ($item) {
            return $item->id_tipo_doc . '-' . $item->id_origen;
        });
        $tablaDatos = [];
        foreach ($agrupados as $key => $grupo) {
            $primero = $grupo->first();
            $tablaDatos[] = [
                'origen' => $primero->origen->nombre ?? 'Sin origen',
                'tipo_doc' => $primero->tipoDoc->nombre ?? 'Sin tipo',
                'cantidad' => $grupo->count(),
            ];
        }



        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);  // Cambiar 'P' por 'L' para orientación horizontal
        $pdf->SetPrintHeader(false); // Deshabilita la impresión del encabezado
        $pdf->SetFont('Times', 'IU', 12);
        $pdf->AddPage();
        $pdf->SetLeftMargin(12); // Ajusta el margen izquierdo a 12 mm
        $pdf->Ln(10);
        $pdf->SetFont('Times', 'I', 12);
        $pdf->Cell(0, 10, 'Honorable Cámara de Diputados - Sistema de Trazabilidad Documental', 0, 1, 'C');

        // Título del reporte con la fecha

        $titulo = $titulo;
        $pdf->SetFont('Times', 'B', 10);
        $pdf->Cell(0, 10, $titulo, 0, 1, 'C'); // Centra el título en la página

        // Insertar marca de agua en la primera página
        $pdf->SetAlpha(0.3); // Establece la opacidad al 30%
        $pdf->Image('vendor/adminlte/dist/img/icono camara.png', 100, 50, 100); // Ajusta la posición y tamaño de la imagen
        $pdf->SetAlpha(1); // Restablece la opacidad al 100%

        // Cabecera de la tabla
        $pdf->Cell(60, 8, 'Institucion de Origen', 1, 0, 'C');
        $pdf->Cell(90, 8, 'Descripcion', 1, 0, 'C');
        $pdf->Cell(30, 8, 'Fecha Ingreso', 1, 0, 'C');
        $pdf->Cell(30, 8, 'Nro M. Entrada', 1, 0, 'C');
        $pdf->Cell(50, 8, 'Funcionario', 1, 1, 'C');

        // Contenido de la tabla
        $pdf->SetFont('Times', '', 12);

        $currentPage = $pdf->getPage(); // Guarda la página actual para controlarla
        $contador = 0;
        foreach ($documentosporfechas as $dato) {
            $origen = $dato->origen ? $dato->origen->nombre : 'N/A';
            $observacion = $dato->observacion ?: 'N/A';
            $fechaIngreso = \Carbon\Carbon::parse($dato->fecha_recepcion)->format('d/m/Y');
            $nroMesaEntrada = $dato->nro_mentrada;
            $funcionario = $dato->user ? $dato->user->name : 'N/A';

            // Imprimir cada fila
            $pdf->Cell(60, 8, utf8_decode($origen), 1, 0, 'L');
            $pdf->Cell(90, 8, utf8_decode($observacion), 1, 0, 'L');
            $pdf->Cell(30, 8, $fechaIngreso, 1, 0, 'C');
            $pdf->Cell(30, 8, $nroMesaEntrada, 1, 0, 'C');
            $pdf->Cell(50, 8, utf8_decode($funcionario), 1, 1, 'L');

            // Si la página cambia, insertar la imagen nuevamente con opacidad
            if ($pdf->getPage() > $currentPage) {
                // Insertar marca de agua en la nueva página
                $pdf->SetAlpha(0.3); // Establece la opacidad al 30%
                $pdf->Image('vendor/adminlte/dist/img/icono camara.png', 20, 50, 150); // Ajusta la posición y tamaño de la imagen
                $pdf->SetAlpha(1); // Restablece la opacidad al 100%
                $currentPage = $pdf->getPage(); // Actualiza la página actual
            }
        }
        $pdf->SetFont('Times', 'B', 12);
        $pdf->Cell(210, 10, 'Total de Documentos: ' . $contador, 1, 0, 'R');
        // Salida del PDF
        $pdf->Output('reporte_por_fechas.pdf', 'I');
    }
}
