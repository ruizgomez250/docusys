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
        $desdeC = \Carbon\Carbon::parse($fechadesde)->format('d/m/Y'); // Convierte la fecha de inicio al formato dd/mm/yyyy
        $hastaC = \Carbon\Carbon::parse($fechahasta)->format('d/m/Y'); // Convierte la fecha de fin al formato dd/mm/yyyy
        $documentosporfechas = '';
        $titulo = '';
        $texttotal='';
        if ($tiporeporte == 1) {

            $origenm = Origen::find($id);
            $titulo = 'Reporte de Origen: ' . $origenm->nombre . ' - Desde ' . $desdeC . ' Hasta ' . $hastaC;
            $texttotal = mb_strtoupper($origenm->nombre);


            // Traer las mesas de entrada donde el 'indice' de 'origen' coincida con 'id_origen' en 'mesa_entrada'
            // y que también estén dentro del rango de fechas
            $documentosporfechas = MesaEntrada::join('origen', 'mesa_entrada.id_origen', '=', 'origen.id')
                ->where('origen.indice', $origenm->indice)
                ->whereBetween('mesa_entrada.fecha_recepcion', [$fechadesde, $fechahasta])
                ->with(['user', 'origen', 'tipoDoc', 'destino', 'firmantes']) // AÑADIDO firmantes
                ->orderBy('mesa_entrada.fecha_recepcion', 'asc')
                ->get();
        } else if ($tiporeporte == 2) {
            $tipod = TipoDoc::find($id);
            $texttotal = mb_strtoupper($tipod->nombre);
            $titulo = 'Reporte Tipo Documento: ' . $tipod->nombre . ' - Desde ' . $desdeC . ' Hasta ' . $hastaC;
            $documentosporfechas = MesaEntrada::join('origen', 'mesa_entrada.id_origen', '=', 'origen.id')
                ->where('mesa_entrada.id_tipo_doc', $tipod->id)
                ->whereBetween('mesa_entrada.fecha_recepcion', [$fechadesde, $fechahasta])
                ->with(['user', 'origen', 'tipoDoc', 'destino', 'firmantes']) // AÑADIDO firmantes
                ->orderBy('mesa_entrada.fecha_recepcion', 'asc')
                ->get();
        } else {
            $texttotal = ' TODOS LOS DOCUMENTOS ';
            $titulo = 'Reporte Todos los Documentos - Desde ' . $desdeC . ' Hasta ' . $hastaC;
            $documentosporfechas = MesaEntrada::join('origen', 'mesa_entrada.id_origen', '=', 'origen.id')
                ->whereBetween('mesa_entrada.fecha_recepcion', [$fechadesde, $fechahasta])
                ->with(['user', 'origen', 'tipoDoc', 'destino', 'firmantes']) // AÑADIDO firmantes
                ->orderBy('mesa_entrada.fecha_recepcion', 'asc')
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
        $pdf->Cell(70, 8, 'Firmantes', 1, 1, 'C');

        // Contenido de la tabla
        $pdf->SetFont('Times', '', 10);

        $currentPage = $pdf->getPage(); // Guarda la página actual para controlarla
        $contador = 0;

        foreach ($documentosporfechas as $dato) {
            $contador++;

            $origen = $dato->origen ? $dato->origen->nombre : 'N/A';
            $fechaIngreso = \Carbon\Carbon::parse($dato->fecha_recepcion)->format('d/m/Y');
            $nroMesaEntrada = $dato->nro_mentrada;

            if ($dato->firmantes && $dato->firmantes->count()) {
                $firmantes = $dato->firmantes->pluck('nombre')->toArray();
                $funcionario = implode(" / ", $firmantes);
            } else {
                $funcionario = 'N/A';
            }

            // Anchos de columnas
            $wOrigen = 60;
            $wObs = 90;
            $wFecha = 30;
            $wNro = 30;
            $wFunc = 70;

            $lineHeight = 6;

            $wObs = max($wObs ?? 90, 10);
            $observacion = trim($dato->observacion ?? '');

            $observacion = $observacion !== '' ? $observacion : 'Sin observación';
            $observacion = utf8_decode($observacion);
            $observacion = preg_replace('/[[:^print:]]/', '', $observacion);
            $observacion = str_replace(["\r\n", "\r"], "\n", $observacion);
            $observacion = preg_replace('/(\S{60})/', '$1 ', $observacion);

            $lineas = $pdf->getNumLines($observacion, $wObs);
            $nbLines = (is_numeric($lineas) && $lineas > 0) ? $lineas : 1;
            $maxHeight = $nbLines * $lineHeight;

            // Verifica si entra la fila completa en la página actual
            $currentY = $pdf->GetY();
            $pageHeight = $pdf->getPageHeight();
            $bottomMargin = $pdf->getBreakMargin(); // Margen inferior
            $availableHeight = $pageHeight - $currentY - $bottomMargin;

            if ($maxHeight > $availableHeight) {
                $pdf->AddPage();
                $currentPage = $pdf->getPage();

                $pdf->SetLeftMargin(12);
                $pdf->Ln(10);
                $pdf->SetFont('Times', 'I', 12);
                $pdf->Cell(0, 10, 'Honorable Cámara de Diputados - Sistema de Trazabilidad Documental', 0, 1, 'C');

                $pdf->SetFont('Times', 'B', 10);
                $pdf->Cell(0, 10, $titulo, 0, 1, 'C');

                $pdf->SetAlpha(0.3);
                $pdf->Image('vendor/adminlte/dist/img/icono camara.png', 100, 50, 100);
                $pdf->SetAlpha(1);
            }

            // Dibujar la fila
            $pdf->MultiCell($wOrigen, $maxHeight, $origen, 1, 'L', false, 0, '', '', true, 0, false, true, $maxHeight, 'M');
            $pdf->MultiCell($wObs, $maxHeight, $observacion, 1, 'L', false, 0, '', '', true, 0, false, true, $maxHeight, 'T');
            $pdf->MultiCell($wFecha, $maxHeight, $fechaIngreso, 1, 'C', false, 0, '', '', true, 0, false, true, $maxHeight, 'M');
            $pdf->MultiCell($wNro, $maxHeight, $nroMesaEntrada, 1, 'C', false, 0, '', '', true, 0, false, true, $maxHeight, 'M');
            $pdf->MultiCell($wFunc, $maxHeight, $funcionario, 1, 'L', false, 1, '', '', true, 0, false, true, $maxHeight, 'M');
        }

        $pdf->SetFont('Times', 'B', 12);
        $pdf->Cell(210, 10, 'TOTAL DOCUMENTOS ' . $texttotal . ': ' . $contador, 1, 0, 'R');
        // Salida del PDF
        $pdf->Output('reporte_por_fechas.pdf', 'I');
    }
}
