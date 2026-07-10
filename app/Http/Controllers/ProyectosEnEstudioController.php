<?php

namespace App\Http\Controllers;

use App\Models\Destino;
use App\Models\MesaEntrada;
use App\Models\MesaEntradaFirmante;
use App\Models\NumeracionExpediente;
use App\Models\ProyectosConfiguracion;
use App\Models\ProyectosEnEstudio;
use App\Models\UserDestino;
use Carbon\Carbon;
use DOMDocument;
use DOMXPath;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\Html;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use TCPDF;
use Yajra\DataTables\Facades\DataTables;

class ProyectosEnEstudioController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:Proyectos en Estudio', ['only' => ['index', 'listado', 'show', 'configuracion']]);
        $this->middleware('permission:Proyectos en Estudio', ['only' => ['store', 'create']]);
        $this->middleware('permission:Proyectos en Estudio', ['only' => ['update', 'edit']]);
        $this->middleware('permission:Proyectos en Estudio', ['only' => ['destroy']]);
    }

    public function index()
    {
        $userDestino = UserDestino::where('user_id', Auth::id())->first();
        $destinos = Destino::all();
        return view('proyectos_en_estudio.index', compact('destinos'));
    }

    public function listado()
    {
        return view('proyectos_en_estudio.listado');
    }

    public function listadoData()
    {
        $proyectos = ProyectosEnEstudio::with('mesaEntrada')
            ->select('proyectos_en_estudio.*');

        return DataTables::of($proyectos)
            ->addColumn('expediente', function ($row) {
                $letra = '';
                if ($row->camara == 'Senado') $letra = 'S';
                elseif ($row->camara == 'Diputados') $letra = 'D';
                elseif ($row->camara == 'Congreso') $letra = 'C';
                $aa = substr($row->anho, -2);
                return $letra . '-' . $aa . $row->nro_expediente;
            })
            ->addColumn('recepcion_nro', function ($row) {
                return $row->mesaEntrada->nro_mentrada . '/' . $row->mesaEntrada->anho;
            })
            ->addColumn('contenido', function ($row) {
                $texto = strip_tags($row->contenido);
                $texto = html_entity_decode($texto, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $texto = preg_replace('/\s+/', ' ', $texto);
                return mb_substr($texto, 0, 200) . (mb_strlen($texto) > 200 ? '...' : '');
            })
            ->addColumn('acciones', function ($row) {
                $btn = '';
                $btn .= '<a href="' . route('proyectos-en-estudio.show', $row->id) . '" class="btn btn-sm btn-info mr-1" title="Editar"><i class="fas fa-edit"></i></a>';
                $btn .= '<a href="' . route('proyectos-en-estudio.pdf', $row->id) . '" class="btn btn-sm btn-danger mr-1" title="PDF"><i class="fas fa-file-pdf"></i></a>';
                $btn .= '<a href="' . route('proyectos-en-estudio.word', $row->id) . '" class="btn btn-sm btn-success mr-1" title="Word"><i class="fas fa-file-word"></i></a>';

                $ultimo = ProyectosEnEstudio::max('nro_expediente');
                if ($row->nro_expediente == $ultimo) {
                    $btn .= '<button type="button" class="btn btn-sm btn-outline-danger delete-btn" data-id="' . $row->id . '" title="Eliminar (último de la lista)"><i class="fas fa-trash"></i></button>';
                } else {
                    $btn .= '<button type="button" class="btn btn-sm btn-secondary" disabled title="No se puede eliminar (no es el último)"><i class="fas fa-trash"></i></button>';
                }
                return $btn;
            })
            ->rawColumns(['acciones'])
            ->make(true);
    }

    public function recepcionadoData(Request $request)
    {
        $usuario = auth()->user();
        $userDestino = UserDestino::where('user_id', $usuario->id)->first();
        $iddest = $userDestino->destino_id;

        $query = MesaEntrada::join('mapa_recorrido', 'mesa_entrada.id', '=', 'mapa_recorrido.id_mentrada')
            ->leftJoin('mesa_entrada_firmante as mef', 'mesa_entrada.id', '=', 'mef.id_mentrada')
            ->leftJoin('firmantes as f', 'mef.id_firmante', '=', 'f.id')
            ->leftJoin('users as u', 'mesa_entrada.id_usuario', '=', 'u.id')
            ->leftJoin('proyectos_en_estudio as p', 'mesa_entrada.id', '=', 'p.id_mentrada')
            ->where('mapa_recorrido.estado', '!=', 0)
            ->where('mapa_recorrido.id_actual', $iddest)
            ->select(
                'mesa_entrada.id',
                'mesa_entrada.nro_mentrada',
                'mesa_entrada.anho',
                'mesa_entrada.fecha_recepcion',
                'mesa_entrada.id_origen',
                'mesa_entrada.id_tipo_doc',
                'mesa_entrada.id_tipo_docr',
                'mesa_entrada.id_destino',
                'mesa_entrada.observacion',
                'mesa_entrada.estado',
                'mesa_entrada.id_usuario',
                'mesa_entrada.created_at',
                'u.name as usuario_nombre',
                'mapa_recorrido.estado as mapa_estado',
                'mapa_recorrido.created_at as mapa_created_at',
                'p.id as proyecto_id',
                'p.nro_expediente',
                'p.camara as proyecto_camara',
                'p.anho as proyecto_anho',
                DB::raw('GROUP_CONCAT(f.nombre SEPARATOR ", ") as nombres_firmantes')
            )
            ->groupBy(
                'mesa_entrada.id',
                'mesa_entrada.nro_mentrada',
                'mesa_entrada.anho',
                'mesa_entrada.fecha_recepcion',
                'mesa_entrada.id_origen',
                'mesa_entrada.id_tipo_doc',
                'mesa_entrada.id_tipo_docr',
                'mesa_entrada.id_destino',
                'mesa_entrada.observacion',
                'mesa_entrada.estado',
                'mesa_entrada.id_usuario',
                'mesa_entrada.created_at',
                'u.name',
                'mapa_recorrido.estado',
                'mapa_recorrido.created_at',
                'p.id',
                'p.nro_expediente',
                'p.camara',
                'p.anho'
            )
            ->with(['origen', 'tipoDoc', 'tipoDocR']);

        if ($request->filled('search.value')) {
            $search = $request->input('search.value');
            $query->where(function ($q) use ($search) {
                $q->where('mesa_entrada.nro_mentrada', 'like', "%{$search}%")
                    ->orWhere('mesa_entrada.anho', 'like', "%{$search}%")
                    ->orWhere('mesa_entrada.fecha_recepcion', 'like', "%{$search}%")
                    ->orWhere('f.nombre', 'like', "%{$search}%");
            });
        }

        return DataTables::of($query)
            ->addColumn('origen', function ($row) {
                return $row->origen->nombre ?? '';
            })
            ->addColumn('tipo_doc', function ($row) {
                return $row->tipoDoc->nombre ?? '';
            })
            ->addColumn('tipo_docr', function ($row) {
                return $row->tipoDocR->nombre ?? '';
            })
            ->addColumn('firmantes', function ($row) {
                return $row->nombres_firmantes ?? '';
            })
            ->addColumn('usuario', function ($row) {
                return $row->usuario_nombre ?? '';
            })
            ->addColumn('acciones', function ($row) {
                $btn = '';
                if ($row->proyecto_id) {
                    $letra = '';
                    if ($row->proyecto_camara == 'Senado') $letra = 'S';
                    elseif ($row->proyecto_camara == 'Diputados') $letra = 'D';
                    elseif ($row->proyecto_camara == 'Congreso') $letra = 'C';
                    $aa = substr($row->proyecto_anho, -2);
                    $btn .= '<a href="' . route('proyectos-en-estudio.show', $row->proyecto_id) . '" class="btn btn-sm btn-success mr-1" title="Editar Proyecto"><i class="fas fa-file-alt"></i> Proyecto N° ' . $letra . '-' . $aa . $row->nro_expediente . '</a>';
                } else {
                    $btn .= '<button type="button" class="btn btn-sm btn-primary" title="Crear Proyecto" onclick="abrirModalCrear(' . $row->id . ')"><i class="fas fa-plus"></i> Crear Proyecto</button>';
                }
                return $btn;
            })
            ->rawColumns(['acciones'])
            ->make(true);
    }

    public function create(Request $request, $id_mentrada)
    {
        $mesaEntrada = MesaEntrada::with(['firmantes', 'origen', 'tipoDoc'])->findOrFail($id_mentrada);

        $anho = date('Y');
        $config = ProyectosConfiguracion::firstOrCreate([], [
            'membrete' => null,
            'leyenda' => '',
            'ultimo_nro_expediente' => 0,
        ]);

        $camara = $request->input('camara', '');
        $firmantes = MesaEntradaFirmante::obtenerFirmantesPorMesaEntrada($id_mentrada);

        $fechaCarbon = Carbon::parse($mesaEntrada->fecha_recepcion);
        $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
        $fechaTexto = $fechaCarbon->format('d') . ' de ' . $meses[$fechaCarbon->month - 1] . ' de ' . $fechaCarbon->format('Y');

        $contenido = '';

        $proyecto = ProyectosEnEstudio::create([
            'id_mentrada' => $id_mentrada,
            'nro_expediente' => 0,
            'anho' => $anho,
            'camara' => $camara,
            'presentado_por' => $firmantes,
            'fecha_recepcion_texto' => $fechaTexto,
            'leyenda' => $config ? $config->leyenda : '',
            'cantidad_observaciones' => 1,
            'contenido' => $contenido,
        ]);

        return redirect()->route('proyectos-en-estudio.show', $proyecto->id)
            ->with('success', 'Proyecto creado. Complete el contenido y guarde para asignar el N° de expediente.');
    }

    public function show($id)
    {
        $proyecto = ProyectosEnEstudio::with(['mesaEntrada.firmantes', 'mesaEntrada.origen', 'mesaEntrada.tipoDoc'])->findOrFail($id);
        return view('proyectos_en_estudio.show', compact('proyecto'));
    }

    public function update(Request $request, $id)
    {
        $proyecto = ProyectosEnEstudio::findOrFail($id);
        $anho = date('Y');
        $mensaje = 'Proyecto actualizado exitosamente.';

        $data = [
            'camara' => $request->input('camara', $proyecto->camara),
            'presentado_por' => $request->input('presentado_por', ''),
            'fecha_recepcion_texto' => $request->input('fecha_recepcion_texto', ''),
            'contenido' => $request->input('contenido'),
            'cantidad_observaciones' => $request->input('cantidad_observaciones', 1),
        ];

        DB::beginTransaction();

        try {
            if ($proyecto->nro_expediente == 0) {
                $config = ProyectosConfiguracion::firstOrCreate([], [
                    'membrete' => null,
                    'leyenda' => '',
                    'ultimo_nro_expediente' => 0,
                ]);

                $nro_expediente = $config->ultimo_nro_expediente + 1;
                $config->increment('ultimo_nro_expediente');

                NumeracionExpediente::create([
                    'nro_expediente' => $nro_expediente,
                    'anho' => $anho,
                ]);

                $data['nro_expediente'] = $nro_expediente;
                $mensaje = 'Proyecto guardado. N° Expediente asignado: ' . $nro_expediente;
            }

            if (!$proyecto->update($data)) {
                throw new \Exception('No se pudo actualizar el proyecto.');
            }

            DB::commit();

            return redirect()->route('proyectos-en-estudio.show', $proyecto->id)
                ->with('success', $mensaje);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('proyectos-en-estudio.show', $proyecto->id)
                ->with('error', 'Error al guardar: ' . $e->getMessage());
        }
    }

    public function configuracion()
    {
        $config = ProyectosConfiguracion::firstOrCreate([], [
            'membrete' => null,
            'leyenda' => '',
            'ultimo_nro_expediente' => 0,
        ]);
        return view('proyectos_en_estudio.configuracion', compact('config'));
    }

    public function updateConfiguracion(Request $request)
    {
        $config = ProyectosConfiguracion::firstOrCreate([], [
            'membrete' => null,
            'leyenda' => '',
            'ultimo_nro_expediente' => 0,
        ]);

        $config->update([
            'leyenda' => $request->input('leyenda', ''),
            'ultimo_nro_expediente' => $request->input('ultimo_nro_expediente', 0),
        ]);

        if ($request->hasFile('membrete')) {
            $file = $request->file('membrete');
            $nombre = 'membrete_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('membretes'), $nombre);
            $config->update(['membrete' => 'membretes/' . $nombre]);
        }

        return redirect()->route('proyectos-en-estudio.configuracion')
            ->with('success', 'Configuración actualizada exitosamente.');
    }

    public function exportListadoPDF()
    {
        $proyectos = ProyectosEnEstudio::with('mesaEntrada')
            ->orderBy('nro_expediente', 'desc')
            ->get();

        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->SetMargins(10, 15, 10);
        $pdf->SetAutoPageBreak(true, 20);
        $pdf->AddPage();

        $pdf->SetFont('Times', 'B', 16);
        $pdf->Cell(0, 10, 'LISTADO DE PROYECTOS CREADOS', 0, 1, 'C');
        $pdf->Ln(5);

        $html = '<table border="1" cellpadding="4" cellspacing="0" style="font-size: 9pt;">
            <thead>
                <tr style="background-color: #cccccc; font-weight: bold; text-align: center;">
                    <td width="12%">Expediente</td>
                    <td width="18%">Presentado Por</td>
                    <td width="12%">Fecha Recepción</td>
                    <td width="10%">Cámara</td>
                    <td width="10%">N° Recepción</td>
                    <td width="38%">Contenido</td>
                </tr>
            </thead>
            <tbody>';

        foreach ($proyectos as $row) {
            $letra = '';
            if ($row->camara == 'Senado') $letra = 'S';
            elseif ($row->camara == 'Diputados') $letra = 'D';
            elseif ($row->camara == 'Congreso') $letra = 'C';
            $aa = substr($row->anho, -2);
            $expediente = $letra . '-' . $aa . $row->nro_expediente;

            $recepcion = $row->mesaEntrada ? $row->mesaEntrada->nro_mentrada . '/' . $row->mesaEntrada->anho : '';

            $contenido = strip_tags($row->contenido);
            $contenido = html_entity_decode($contenido, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $contenido = preg_replace('/\s+/', ' ', $contenido);
            $contenido = mb_substr($contenido, 0, 300);

            $html .= '<tr>
                <td>' . $expediente . '</td>
                <td>' . e($row->presentado_por) . '</td>
                <td>' . e($row->fecha_recepcion_texto) . '</td>
                <td>' . e($row->camara) . '</td>
                <td>' . e($recepcion) . '</td>
                <td>' . e($contenido) . '</td>
            </tr>';
        }

        $html .= '</tbody></table>';

        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('listado_proyectos.pdf', 'I');
    }

    public function exportListadoExcel()
    {
        $proyectos = ProyectosEnEstudio::with('mesaEntrada')
            ->orderBy('nro_expediente', 'desc')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Proyectos');

        $headers = ['Expediente', 'Presentado Por', 'Fecha Recepción', 'Cámara', 'N° Recepción', 'Contenido'];
        $col = 1;
        foreach ($headers as $header) {
            $sheet->setCellValueByColumnAndRow($col, 1, $header);
            $sheet->getStyleByColumnAndRow($col, 1)->getFont()->setBold(true);
            $col++;
        }

        $sheet->getStyle('A1:F1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFCCCCCC');

        $rowNum = 2;
        foreach ($proyectos as $row) {
            $letra = '';
            if ($row->camara == 'Senado') $letra = 'S';
            elseif ($row->camara == 'Diputados') $letra = 'D';
            elseif ($row->camara == 'Congreso') $letra = 'C';
            $aa = substr($row->anho, -2);
            $expediente = $letra . '-' . $aa . $row->nro_expediente;

            $recepcion = $row->mesaEntrada ? $row->mesaEntrada->nro_mentrada . '/' . $row->mesaEntrada->anho : '';

            $contenido = strip_tags($row->contenido);
            $contenido = html_entity_decode($contenido, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $contenido = preg_replace('/\s+/', ' ', $contenido);

            $sheet->setCellValueByColumnAndRow(1, $rowNum, $expediente);
            $sheet->setCellValueByColumnAndRow(2, $rowNum, $row->presentado_por);
            $sheet->setCellValueByColumnAndRow(3, $rowNum, $row->fecha_recepcion_texto);
            $sheet->setCellValueByColumnAndRow(4, $rowNum, $row->camara);
            $sheet->setCellValueByColumnAndRow(5, $rowNum, $recepcion);
            $sheet->setCellValueByColumnAndRow(6, $rowNum, $contenido);
            $rowNum++;
        }

        foreach (range(1, 6) as $col) {
            $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();

        $filename = 'listado_proyectos.xlsx';
        return response($content, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function downloadPDF($id)
    {
        $proyecto = ProyectosEnEstudio::with('mesaEntrada')->findOrFail($id);
        $config = ProyectosConfiguracion::first();

        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->SetMargins(20, 15, 20);
        $pdf->SetAutoPageBreak(true, 30);
        $pdf->setFontSubsetting(true);
        $pdf->AddPage();

        $pdf->SetAlpha(0.15);
        $watermark = public_path('vendor/adminlte/dist/img/icono camara.png');
        if (file_exists($watermark)) {
            $pdf->Image($watermark, 15, 50, 180, '', 'PNG', '', '', false, 330, '', false, false, 0);
        }
        $pdf->SetAlpha(1);

        if ($config && $config->leyenda) {
            $pdf->SetFont('Times', 'I', 11);
            $pdf->Cell(0, 8, '"' . strtoupper($config->leyenda) . '"', 0, 1, 'C');
            $pdf->Ln(3);
        }

        if ($config && $config->membrete && file_exists(public_path($config->membrete))) {
            $imgPath = public_path($config->membrete);
            $imgData = base64_encode(file_get_contents($imgPath));
            $imgHtml = '<div style="text-align: center;"><img src="@' . $imgData . '" width="70" /></div>';
            $pdf->writeHTML($imgHtml, true, false, true, false, '');
            //$pdf->Ln(1);
        }

        $pdf->SetFont('Times', 'B', 13);
        $pdf->Cell(0, 8, 'CONGRESO DE LA NACIÓN', 0, 1, 'C');
        $pdf->SetFont('Times', 'B', 12);
        $pdf->Cell(0, 8, 'HONORABLE CÁMARA DE DIPUTADOS', 0, 1, 'C');

        $pdf->Ln(3);

        $letra = match ($proyecto->camara) {
            'Diputados' => 'D',
            'Senado' => 'S',
            'Congreso' => 'C',
            default => 'X',
        };
        $anhoCorto = substr($proyecto->anho, -2);
        $numeroExp = $letra . '-' . $anhoCorto . $proyecto->nro_expediente;

        $pdf->SetFont('Times', 'B', 16);
        $margins = $pdf->getMargins();
        $expW = $pdf->GetStringWidth('EXP No. ' . $numeroExp) + 10;
        $pdf->SetX($pdf->getPageWidth() - $margins['right'] - $expW - 4);
        $pdf->SetLineStyle(['width' => 0.5, 'color' => [0, 0, 0]]);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->Cell($expW + 4, 10, 'EXP No. ' . $numeroExp, 1, 1, 'C', true);

        $pdf->Ln(5);

        $contenido = $proyecto->contenido;
        if (!empty(trim($contenido))) {
            $pdf->SetFont('Times', '', 11);
            $html = '<div style="text-align: justify; font-family: Times, serif; font-size: 12pt;">' . $contenido . '</div>';
            $pdf->writeHTML($html, true, false, true, false, '');
        }

        $pdf->Output('proyecto_' . $proyecto->nro_expediente . '.pdf', 'I');
    }

    public function downloadWord($id)
    {
        $proyecto = ProyectosEnEstudio::with('mesaEntrada')->findOrFail($id);
        $config = ProyectosConfiguracion::first();

        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        $header = $section->addHeader();

        if ($config && $config->leyenda) {
            $header->addText('"' . strtoupper($config->leyenda) . '"', ['italic' => true, 'size' => 11], ['align' => 'center']);
        }

        if ($config && $config->membrete && file_exists(public_path($config->membrete))) {
            $header->addImage(public_path($config->membrete), [
                'width' => 80,
                'align' => 'center',
            ]);
        }

        $header->addText('CONGRESO DE LA NACIÓN', ['bold' => true, 'size' => 13], ['align' => 'center']);
        $header->addText('Honorable Cámara de Diputados', ['bold' => true, 'size' => 12], ['align' => 'center']);

        $section->addTextBreak(1);

        $letra = match ($proyecto->camara) {
            'Diputados' => 'D',
            'Senado' => 'S',
            'Congreso' => 'C',
            default => 'X',
        };
        $anhoCorto = substr($proyecto->anho, -2);
        $numeroExp = $letra . '-' . $anhoCorto . $proyecto->nro_expediente;

        $table = $section->addTable([
            'borderSize' => 16,
            'borderColor' => '000000',
            'align' => 'right',
        ]);
        $table->addRow();
        $cell = $table->addCell(3000);
        $cell->addText('EXP No. ' . $numeroExp, ['bold' => true, 'size' => 16]);

        $section->addTextBreak(1);

        $contenido = $proyecto->contenido;
        if (!empty(trim($contenido))) {
            $contenidoLimpio = preg_replace('/<br\s*\/?>/i', '<br/>', $contenido);
            $contenidoLimpio = preg_replace('/<img([^>]+)>/i', '<img$1/>', $contenidoLimpio);
            $contenidoLimpio = preg_replace('/<hr(\s*)>/i', '<hr$1/>', $contenidoLimpio);
            $contenidoLimpio = preg_replace('/&(?![a-zA-Z0-9#]+;)/', '&amp;', $contenidoLimpio);
            $contenidoLimpio = preg_replace('/<\/?(?:thead|tbody)>/i', '', $contenidoLimpio);
            $contenidoLimpio = preg_replace('/<!--StartFragment-->/i', '', $contenidoLimpio);
            $contenidoLimpio = preg_replace('/<!--EndFragment-->/i', '', $contenidoLimpio);
            $contenidoLimpio = preg_replace('/<p[^>]*>\s*(<table[\s>])/i', '$1', $contenidoLimpio);
            $contenidoLimpio = preg_replace('/(<\/table>)\s*<\/p>/i', '$1', $contenidoLimpio);

            $useErrors = libxml_use_internal_errors(true);
            $dom = new DOMDocument();
            $dom->loadHTML('<?xml encoding="UTF-8">' . $contenidoLimpio, LIBXML_NOERROR | LIBXML_NOWARNING);

            foreach ($dom->getElementsByTagName('table') as $table) {
                if (strpos($table->getAttribute('class'), 'obs-tramites-table') !== false) {
                    $tableStyle = $table->getAttribute('style');
                    $tableStyle = preg_replace('/border\s*:\s*[^;]+;?/i', '', $tableStyle);
                    $table->setAttribute('style', trim($tableStyle));

                    $rows = $table->getElementsByTagName('tr');
                    $rowCount = $rows->length;
                    foreach ($rows as $rowIndex => $row) {
                        $cells = [];
                        foreach ($row->childNodes as $child) {
                            if ($child->nodeName === 'td' || $child->nodeName === 'th') {
                                $cells[] = $child;
                            }
                        }
                        $colCount = count($cells);
                        foreach ($cells as $colIndex => $cell) {
                            $style = $cell->getAttribute('style');
                            $additions = [];
                            if ($rowIndex === 0) $additions[] = 'border-top: 3px #000 solid';
                            if ($rowIndex === $rowCount - 1) $additions[] = 'border-bottom: 3px #000 solid';
                            if ($colIndex === 0) $additions[] = 'border-left: 3px #000 solid';
                            if ($colIndex === $colCount - 1) $additions[] = 'border-right: 3px #000 solid';
                            if (!empty($additions)) {
                                $cell->setAttribute('style', $style . '; ' . implode('; ', $additions));
                            }
                        }
                    }
                }
            }

            $body = $dom->getElementsByTagName('body')->item(0);
            $contenidoLimpio = '';
            if ($body) {
                foreach ($body->childNodes as $child) {
                    $contenidoLimpio .= $dom->saveXML($child, LIBXML_NOXMLDECL);
                }
            }
            $contenidoLimpio = preg_replace('/<\/?(?:br|hr|img)([^>]*)><\/(?:br|hr|img)>/i', '<$1/>', $contenidoLimpio);
            $contenidoLimpio = preg_replace(
                '/border:\s*([0-9]+[^\s;]*)\s+(#[a-fA-F0-9]+|[a-zA-Z]+)\s+(#[a-fA-F0-9]+|[a-zA-Z]+)/i',
                'border: $1 $3 $2',
                $contenidoLimpio
            );
            libxml_clear_errors();
            libxml_use_internal_errors($useErrors);

            Html::addHtml($section, $contenidoLimpio, false, false);
        }

        $observacion = trim($proyecto->mesaEntrada->observacion ?? '');
        if (!empty($observacion)) {
            $section->addTextBreak(1);
            Html::addHtml($section, '<div style="border: 1px #000 solid; padding: 10px; text-align: justify;">' . nl2br(htmlspecialchars($observacion)) . '</div>', false, false);
        }

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        ob_start();
        $objWriter->save('php://output');
        $content = ob_get_clean();

        $filename = 'proyecto_' . $proyecto->nro_expediente . '.docx';
        return response($content, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function destroy($id)
    {
        $proyecto = ProyectosEnEstudio::findOrFail($id);
        $ultimo = ProyectosEnEstudio::max('nro_expediente');

        if ($proyecto->nro_expediente != $ultimo) {
            return redirect()->back()
                ->with('error', 'Solo se puede eliminar el último proyecto de la lista para mantener la correlatividad numérica.');
        }

        $proyecto->delete();

        return redirect()->route('proyectos-en-estudio.listado')
            ->with('success', 'Proyecto eliminado exitosamente.');
    }
}
