<?php

namespace App\Http\Controllers;

use App\Models\Destino;
use App\Models\MesaEntrada;
use App\Models\MesaEntradaFirmante;
use App\Models\NumeracionExpediente;
use App\Models\ProyectosConfiguracion;
use App\Models\ProyectosEnEstudio;
use App\Models\Sesion;
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
        $this->middleware('permission:Proyectos en Estudio', ['only' => ['index', 'listado', 'show', 'configuracion', 'listSesiones', 'listAsuntosEntrados', 'editSesion']]);
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
                    ->orWhere('mesa_entrada.observacion', 'like', "%{$search}%")
                    ->orWhere('u.name', 'like', "%{$search}%")
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
            'tipo_sesion' => 'EXTRAORDINARIA',
            'nro_sesion' => 1,
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
            'acapite' => '',
            'destino' => '',
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
            'acapite' => $request->input('acapite', $proyecto->acapite),
            'destino' => $request->input('destino', $proyecto->destino),
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
            'tipo_sesion' => 'EXTRAORDINARIA',
            'nro_sesion' => 1,
            'ultimo_nro_expediente' => 0,
        ]);
        return view('proyectos_en_estudio.configuracion', compact('config'));
    }

    public function updateConfiguracion(Request $request)
    {
        $config = ProyectosConfiguracion::firstOrCreate([], [
            'membrete' => null,
            'leyenda' => '',
            'tipo_sesion' => 'EXTRAORDINARIA',
            'nro_sesion' => 1,
            'ultimo_nro_expediente' => 0,
        ]);

        $config->update([
            'leyenda' => $request->input('leyenda', ''),
            'tipo_sesion' => $request->input('tipo_sesion', 'EXTRAORDINARIA'),
            'nro_sesion' => $request->input('nro_sesion', 1),
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

            if (strpos($contenido, 'obs-tramites-table') !== false) {
                $tablePattern = '/<table[^>]*class="[^"]*obs-tramites-table[^"]*"[^>]*>.*?<\/table>/is';
                if (preg_match($tablePattern, $contenido, $matches, PREG_OFFSET_CAPTURE)) {
                    $beforeTable = substr($contenido, 0, $matches[0][1]);
                    $tableHtml = $matches[0][0];
                    $afterTable = substr($contenido, $matches[0][1] + strlen($matches[0][0]));

                    if (!empty(trim($beforeTable))) {
                        $html = '<div style="text-align: justify; font-family: Times, serif; font-size: 12pt;">' . $beforeTable . '</div>';
                        $pdf->writeHTML($html, true, false, true, false, '');
                    }

                    $cleanHtml = preg_replace('/\bborder="[^"]*"/i', '', $tableHtml);
                    $cleanHtml = preg_replace('/\bcellpadding="[^"]*"/i', '', $cleanHtml);
                    $cleanHtml = preg_replace('/\bcellspacing="[^"]*"/i', '', $cleanHtml);
                    $cleanHtml = preg_replace('/style="[^"]*"/i', '', $cleanHtml);
                    $cleanHtml = preg_replace('/<table\b/', '<table border="0" cellpadding="4" cellspacing="0"', $cleanHtml, 1);
                    $cleanHtml = preg_replace('/<th\b/', '<th border="0"', $cleanHtml);
                    $cleanHtml = preg_replace('/<td\b/', '<td border="0"', $cleanHtml);

                    $margins = $pdf->getMargins();
                    $tableWidth = $pdf->getPageWidth() - $margins['left'] - $margins['right'];

                    $yBefore = $pdf->GetY();
                    $html = '<div style="font-family: Times, serif; font-size: 12pt;">' . $cleanHtml . '</div>';
                    $pdf->writeHTML($html, true, false, true, false, '');
                    $yAfter = $pdf->GetY();

                    $pdf->SetLineStyle(['width' => 0.5, 'color' => [0, 0, 0], 'cap' => 'butt', 'join' => 'miter']);
                    $pdf->Rect($margins['left'], $yBefore, $tableWidth, $yAfter - $yBefore, 'D');

                    if (!empty(trim($afterTable))) {
                        $html = '<div style="text-align: justify; font-family: Times, serif; font-size: 12pt;">' . $afterTable . '</div>';
                        $pdf->writeHTML($html, true, false, true, false, '');
                    }
                }
            } else {
                $html = '<div style="text-align: justify; font-family: Times, serif; font-size: 12pt;">' . $contenido . '</div>';
                $pdf->writeHTML($html, true, false, true, false, '');
            }
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

        $watermarkPath = public_path('vendor/adminlte/dist/img/icono camara.png');
        $tempWm = null;

        if (file_exists($watermarkPath)) {
            $original = imagecreatefrompng($watermarkPath);
            if ($original) {
                $origW = imagesx($original);
                $origH = imagesy($original);
                $targetW = 450;
                $targetH = (int)($origH * ($targetW / $origW));

                $img = imagecreatetruecolor($targetW, $targetH);
                imagesavealpha($img, true);

                $bg = imagecolorallocatealpha($img, 0, 0, 0, 127);
                imagefill($img, 0, 0, $bg);

                imagecopyresampled(
                    $img,
                    $original,
                    0,
                    0,
                    0,
                    0,
                    $targetW,
                    $targetH,
                    $origW,
                    $origH
                );

                imagedestroy($original);

                // Hacer la marca mucho más clara
                for ($x = 0; $x < $targetW; $x++) {
                    for ($y = 0; $y < $targetH; $y++) {

                        $rgba = imagecolorat($img, $x, $y);

                        $a = ($rgba >> 24) & 0x7F;
                        $r = ($rgba >> 16) & 0xFF;
                        $g = ($rgba >> 8) & 0xFF;
                        $b = $rgba & 0xFF;

                        // Mezclar con blanco (80%)
                        $r = (int)($r + (255 - $r) * 0.80);
                        $g = (int)($g + (255 - $g) * 0.80);
                        $b = (int)($b + (255 - $b) * 0.80);

                        // Mucha transparencia
                        $newA = min(127, $a + 118);

                        $color = imagecolorallocatealpha(
                            $img,
                            $r,
                            $g,
                            $b,
                            $newA
                        );

                        imagesetpixel($img, $x, $y, $color);
                    }
                }

                $tempWm = tempnam(sys_get_temp_dir(), 'wm_') . '.png';
                imagepng($img, $tempWm);
                imagedestroy($img);

                $header->addWatermark($tempWm, [
                    'width' => 450,
                    'height' => $targetH,
                    'posHorizontal' => 'center',
                    'posHorizontalRel' => 'page',
                    'posVertical' => 'center',
                    'posVerticalRel' => 'page',
                    'wrap' => 'behind',
                ]);
            }
        }

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
            if (strpos($contenido, 'obs-tramites-table') !== false) {
                $tablePattern = '/<table[^>]*class="[^"]*obs-tramites-table[^"]*"[^>]*>.*?<\/table>/is';
                if (preg_match($tablePattern, $contenido, $matches, PREG_OFFSET_CAPTURE)) {
                    $beforeTable = substr($contenido, 0, $matches[0][1]);
                    $tableHtml = $matches[0][0];
                    $afterTable = substr($contenido, $matches[0][1] + strlen($matches[0][0]));

                    if (!empty(trim($beforeTable))) {
                        $beforeTable = preg_replace('/<br\s*\/?>/i', '<br/>', $beforeTable);
                        $beforeTable = preg_replace('/<img([^>]+)>/i', '<img$1/>', $beforeTable);
                        $beforeTable = preg_replace('/<hr(\s*)>/i', '<hr$1/>', $beforeTable);
                        $beforeTable = preg_replace('/&(?![a-zA-Z0-9#]+;)/', '&amp;', $beforeTable);
                        $beforeTable = preg_replace('/<\/?(?:html|head|body)[^>]*>/i', '', $beforeTable);
                        Html::addHtml($section, '<div style="text-align: justify;">' . $beforeTable . '</div>', false, false);
                    }

                    $tableDom = new DOMDocument();
                    $tableDom->loadHTML('<?xml encoding="UTF-8">' . $tableHtml, LIBXML_NOERROR | LIBXML_NOWARNING);
                    $tableNode = $tableDom->getElementsByTagName('table')->item(0);

                    $colWidths = [];
                    $thNodes = $tableDom->getElementsByTagName('th');
                    if ($thNodes->length > 0) {
                        foreach ($thNodes as $th) {
                            $w = $th->getAttribute('width');
                            $colWidths[] = !empty($w) ? (int) $w : 25;
                        }
                    }

                    $rows = $tableNode->getElementsByTagName('tr');
                    $rowCount = $rows->length;

                    $margins = $section->getSettings();
                    $pageWidth = $margins['pageSizeW'] ?? 11906;
                    $marginLeft = $margins['marginLeft'] ?? 1440;
                    $marginRight = $margins['marginRight'] ?? 1440;
                    $usableWidth = $pageWidth - $marginLeft - $marginRight;

                    $table = $section->addTable([
                        'borderSize' => 0,
                        'width' => $usableWidth,
                        'unit' => \PhpOffice\PhpWord\SimpleType\TblWidth::TWIP,
                    ]);

                    foreach ($rows as $rowIndex => $row) {
                        $table->addRow();
                        $tdNodes = [];
                        foreach ($row->childNodes as $child) {
                            if ($child->nodeName === 'td' || $child->nodeName === 'th') {
                                $tdNodes[] = $child;
                            }
                        }
                        $colCount = count($tdNodes);
                        foreach ($tdNodes as $colIndex => $td) {
                            $pct = isset($colWidths[$colIndex]) ? $colWidths[$colIndex] : (100 / $colCount);
                            $cellWidth = (int) ($usableWidth * $pct / 100);

                            $noBorder = [
                                'borderTopSize' => 0,
                                'borderTopColor' => 'FFFFFF',
                                'borderTopStyle' => 'nil',
                                'borderBottomSize' => 0,
                                'borderBottomColor' => 'FFFFFF',
                                'borderBottomStyle' => 'nil',
                                'borderLeftSize' => 0,
                                'borderLeftColor' => 'FFFFFF',
                                'borderLeftStyle' => 'nil',
                                'borderRightSize' => 0,
                                'borderRightColor' => 'FFFFFF',
                                'borderRightStyle' => 'nil',
                            ];
                            if ($rowIndex === 0) {
                                $noBorder['borderTopSize'] = 16;
                                $noBorder['borderTopColor'] = '000000';
                                $noBorder['borderTopStyle'] = 'single';
                            }
                            if ($rowIndex === $rowCount - 1) {
                                $noBorder['borderBottomSize'] = 16;
                                $noBorder['borderBottomColor'] = '000000';
                                $noBorder['borderBottomStyle'] = 'single';
                            }
                            if ($colIndex === 0) {
                                $noBorder['borderLeftSize'] = 16;
                                $noBorder['borderLeftColor'] = '000000';
                                $noBorder['borderLeftStyle'] = 'single';
                            }
                            if ($colIndex === $colCount - 1) {
                                $noBorder['borderRightSize'] = 16;
                                $noBorder['borderRightColor'] = '000000';
                                $noBorder['borderRightStyle'] = 'single';
                            }

                            $cellStyle = array_merge([
                                'width' => $cellWidth,
                                'unit' => \PhpOffice\PhpWord\SimpleType\TblWidth::TWIP,
                            ], $noBorder);

                            $cell = $table->addCell($cellWidth, $cellStyle);
                            $text = trim($td->textContent);
                            $isHeader = ($td->nodeName === 'th');
                            $runs = $td->getElementsByTagName('strong') ?: $td->getElementsByTagName('b');
                            if ($runs->length > 0) {
                                $text = trim($runs->item(0)->textContent);
                            }
                            $fontOpts = ['size' => 11];
                            if ($isHeader || $runs->length > 0) {
                                $fontOpts['bold'] = true;
                            }
                            $cell->addText($text, $fontOpts);
                        }
                    }

                    if (!empty(trim($afterTable))) {
                        $afterTable = preg_replace('/<br\s*\/?>/i', '<br/>', $afterTable);
                        $afterTable = preg_replace('/<img([^>]+)>/i', '<img$1/>', $afterTable);
                        $afterTable = preg_replace('/<hr(\s*)>/i', '<hr$1/>', $afterTable);
                        $afterTable = preg_replace('/&(?![a-zA-Z0-9#]+;)/', '&amp;', $afterTable);
                        $afterTable = preg_replace('/<\/?(?:html|head|body)[^>]*>/i', '', $afterTable);
                        Html::addHtml($section, '<div style="text-align: justify;">' . $afterTable . '</div>', false, false);
                    }
                }
            } else {
                $contenidoLimpio = preg_replace('/<br\s*\/?>/i', '<br/>', $contenido);
                $contenidoLimpio = preg_replace('/<img([^>]+)>/i', '<img$1/>', $contenidoLimpio);
                $contenidoLimpio = preg_replace('/<hr(\s*)>/i', '<hr$1/>', $contenidoLimpio);
                $contenidoLimpio = preg_replace('/&(?![a-zA-Z0-9#]+;)/', '&amp;', $contenidoLimpio);
                $contenidoLimpio = preg_replace('/<\/?(?:html|head|body)[^>]*>/i', '', $contenidoLimpio);
                Html::addHtml($section, $contenidoLimpio, false, false);
            }
        }

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        ob_start();
        $objWriter->save('php://output');
        $content = ob_get_clean();

        if ($tempWm && file_exists($tempWm)) {
            @unlink($tempWm);
        }

        $filename = 'proyecto_' . $proyecto->nro_expediente . '.docx';
        return response($content, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function listSesiones()
    {
        $proyectos = ProyectosEnEstudio::with('mesaEntrada')
            ->where('nro_expediente', '>', 0)
            ->whereNull('sesion_id')
            ->orderBy('nro_expediente', 'asc')
            ->get();

        $fechas = MesaEntrada::join('proyectos_en_estudio as p', 'mesa_entrada.id', '=', 'p.id_mentrada')
            ->where('p.nro_expediente', '>', 0)
            ->whereNull('p.sesion_id')
            ->select(
                'mesa_entrada.fecha_recepcion',
                DB::raw('COUNT(p.id) as cantidad')
            )
            ->groupBy('mesa_entrada.fecha_recepcion')
            ->orderBy('mesa_entrada.fecha_recepcion', 'desc')
            ->get()
            ->map(function ($item) {
                $carbon = \Carbon\Carbon::parse($item->fecha_recepcion);
                $dias = ['DOMINGO', 'LUNES', 'MARTES', 'MIERCOLES', 'JUEVES', 'VIERNES', 'SABADO'];
                $meses = [1 => 'ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'];
                $item->fecha_texto = $dias[$carbon->dayOfWeek] . ', ' . $carbon->format('d') . ' DE ' . $meses[$carbon->month] . ' DE ' . $carbon->format('Y');
                return $item;
            });

        return view('proyectos_en_estudio.sesiones', compact('proyectos', 'fechas'));
    }

    public function generarPDFSesion(Request $request)
    {
        $request->validate([
            'doc_ids' => 'required|string',
            'tipo_sesion' => 'required|string',
            'nro_sesion' => 'required|integer|min:1',
            'fecha_sesion' => 'required|date',
        ]);

        $ids = array_map('intval', explode(',', $request->input('doc_ids')));
        $tipoSesion = strtoupper($request->input('tipo_sesion'));
        $nroSesion = $request->input('nro_sesion');
        $fechaSesion = $request->input('fecha_sesion');

        $proyectos = ProyectosEnEstudio::with('mesaEntrada')
            ->whereIn('id', $ids)
            ->where('nro_expediente', '>', 0)
            ->whereNull('sesion_id')
            ->orderBy('nro_expediente', 'asc')
            ->get();

        if ($proyectos->isEmpty()) {
            return response()->json(['error' => 'No se encontraron documentos seleccionados.'], 404);
        }

        DB::beginTransaction();
        try {
            $sesion = Sesion::create([
                'fecha_sesion' => $fechaSesion,
                'tipo_sesion' => $tipoSesion,
                'nro_sesion' => $nroSesion,
            ]);

            ProyectosEnEstudio::whereIn('id', $ids)->update(['sesion_id' => $sesion->id]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al guardar la sesión: ' . $e->getMessage()], 500);
        }

        $carbon = \Carbon\Carbon::parse($fechaSesion);

        $config = ProyectosConfiguracion::firstOrCreate([], [
            'membrete' => null,
            'leyenda' => '',
            'tipo_sesion' => 'EXTRAORDINARIA',
            'nro_sesion' => 1,
            'ultimo_nro_expediente' => 0,
        ]);

        $dias = [0 => 'DOMINGO', 1 => 'LUNES', 2 => 'MARTES', 3 => 'MIERCOLES', 4 => 'JUEVES', 5 => 'VIERNES', 6 => 'SABADO'];
        $meses = [1 => 'ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'];
        $diaSemana = $dias[$carbon->dayOfWeek];
        $diaNumero = $carbon->format('d');
        $mesNombre = $meses[$carbon->month];
        $anio = $carbon->format('Y');
        $fechaFormateada = $diaSemana . ', ' . $diaNumero . ' DE ' . $mesNombre . ' DE ' . $anio;

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
        }

        $pdf->SetFont('Times', 'B', 13);
        $pdf->Cell(0, 8, 'CONGRESO DE LA NACIÓN', 0, 1, 'C');
        $pdf->SetFont('Times', 'B', 12);
        $pdf->Cell(0, 8, 'HONORABLE CÁMARA DE DIPUTADOS', 0, 1, 'C');

        $pdf->Ln(8);

        $pdf->SetFont('Times', 'B', 16);
        $pdf->Cell(0, 10, 'ASUNTOS ENTRADOS', 0, 1, 'C');

        $pdf->SetFont('Times', 'B', 13);
        $pdf->Cell(0, 8, 'SESIÓN ' . $tipoSesion . ' N° ' . $nroSesion, 0, 1, 'C');

        $pdf->SetFont('Times', '', 12);
        $pdf->Cell(0, 8, $fechaFormateada . '.', 0, 1, 'C');

        $pdf->Ln(8);

        foreach ($proyectos as $proyecto) {
            $nroReal = $proyecto->nro_expediente;

            $acapite = trim($proyecto->acapite ?? '');
            if (!empty($acapite)) {
                $acapiteLimpio = preg_replace('/<br\s*\/?>/i', '<br/>', $acapite);
                $acapiteLimpio = preg_replace('/<\/?(?:thead|tbody)>/i', '', $acapiteLimpio);
                $acapiteLimpio = preg_replace('/<!--StartFragment-->/i', '', $acapiteLimpio);
                $acapiteLimpio = preg_replace('/<!--EndFragment-->/i', '', $acapiteLimpio);
            } else {
                $acapiteLimpio = '';
            }

            $destino = trim($proyecto->destino ?? '');
            if (!empty($destino)) {
                $destino = '. ' . $destino;
            }

            $html = '<p style="text-align: justify; font-family: Times, serif; font-size: 12pt;">'
                . '<strong>' . $nroReal . '</strong>. '
                . $acapiteLimpio
                . $destino
                . '</p>';

            $pdf->writeHTML($html, true, false, true, false, '');
            $pdf->Ln(3);
        }

        $filename = 'sesion_' . strtolower($tipoSesion) . '_nro_' . $nroSesion . '_' . $carbon->format('Y-m-d') . '.pdf';
        $pdf->Output($filename, 'I');
    }

    public function prepararSesion(Request $request)
    {
        $request->validate([
            'doc_ids' => 'required|string',
            'tipo_sesion' => 'required|string',
            'nro_sesion' => 'required|integer|min:1',
            'fecha_sesion' => 'required|date',
        ]);

        $ids = array_map('intval', explode(',', $request->input('doc_ids')));
        $tipoSesion = strtoupper($request->input('tipo_sesion'));
        $nroSesion = $request->input('nro_sesion');
        $fechaSesion = $request->input('fecha_sesion');

        $proyectos = ProyectosEnEstudio::with('mesaEntrada')
            ->whereIn('id', $ids)
            ->where('nro_expediente', '>', 0)
            ->whereNull('sesion_id')
            ->orderBy('nro_expediente', 'asc')
            ->get();

        if ($proyectos->isEmpty()) {
            return response()->json(['error' => 'No se encontraron documentos seleccionados.'], 404);
        }

        DB::beginTransaction();
        try {
            $sesion = Sesion::create([
                'fecha_sesion' => $fechaSesion,
                'tipo_sesion' => $tipoSesion,
                'nro_sesion' => $nroSesion,
            ]);

            ProyectosEnEstudio::whereIn('id', $ids)->update(['sesion_id' => $sesion->id]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al guardar la sesión: ' . $e->getMessage()], 500);
        }

        return response()->json(['success' => true, 'sesion_id' => $sesion->id]);
    }

    public function listAsuntosEntrados(Request $request)
    {
        $query = Sesion::with('proyectos.mesaEntrada');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('tipo_sesion', 'like', "%{$search}%")
                    ->orWhere('nro_sesion', 'like', "%{$search}%")
                    ->orWhere('fecha_sesion', 'like', "%{$search}%");
            });
        }

        $sesiones = $query->orderByDesc('created_at')->get();

        return view('proyectos_en_estudio.asuntos_entrados', compact('sesiones'));
    }

    public function editSesion($id)
    {
        $sesion = Sesion::with('proyectos.mesaEntrada')->findOrFail($id);

        $dias = [0 => 'DOMINGO', 1 => 'LUNES', 2 => 'MARTES', 3 => 'MIERCOLES', 4 => 'JUEVES', 5 => 'VIERNES', 6 => 'SABADO'];
        $meses = [1 => 'ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'];
        $carbon = $sesion->fecha_sesion;
        $sesion->fecha_texto = $dias[$carbon->dayOfWeek] . ', ' . $carbon->format('d') . ' DE ' . $meses[$carbon->month] . ' DE ' . $carbon->format('Y');

        return view('proyectos_en_estudio.edit_sesion', compact('sesion'));
    }

    public function updateSesion(Request $request, $id)
    {
        $request->validate([
            'tipo_sesion' => 'required|string',
            'nro_sesion' => 'required|integer|min:1',
            'fecha_sesion' => 'required|date',
        ]);

        $sesion = Sesion::findOrFail($id);
        $sesion->update([
            'tipo_sesion' => strtoupper($request->input('tipo_sesion')),
            'nro_sesion' => $request->input('nro_sesion'),
            'fecha_sesion' => $request->input('fecha_sesion'),
        ]);

        return redirect()->route('proyectos-en-estudio.asuntos-entrados')
            ->with('success', 'Sesión actualizada exitosamente.');
    }

    public function downloadPDFSesion($id)
    {
        $sesion = Sesion::with('proyectos.mesaEntrada')->findOrFail($id);

        $proyectos = $sesion->proyectos
            ->sortBy('nro_expediente')
            ->values();

        $config = ProyectosConfiguracion::firstOrCreate([], [
            'membrete' => null,
            'leyenda' => '',
            'tipo_sesion' => 'EXTRAORDINARIA',
            'nro_sesion' => 1,
            'ultimo_nro_expediente' => 0,
        ]);

        $carbon = $sesion->fecha_sesion;
        $dias = [0 => 'DOMINGO', 1 => 'LUNES', 2 => 'MARTES', 3 => 'MIERCOLES', 4 => 'JUEVES', 5 => 'VIERNES', 6 => 'SABADO'];
        $meses = [1 => 'ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'];
        $fechaFormateada = $dias[$carbon->dayOfWeek] . ', ' . $carbon->format('d') . ' DE ' . $meses[$carbon->month] . ' DE ' . $carbon->format('Y');

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
        }

        $pdf->SetFont('Times', 'B', 13);
        $pdf->Cell(0, 8, 'CONGRESO DE LA NACIÓN', 0, 1, 'C');
        $pdf->SetFont('Times', 'B', 12);
        $pdf->Cell(0, 8, 'HONORABLE CÁMARA DE DIPUTADOS', 0, 1, 'C');
        $pdf->Ln(8);

        $pdf->SetFont('Times', 'B', 16);
        $pdf->Cell(0, 10, 'ASUNTOS ENTRADOS', 0, 1, 'C');
        $pdf->SetFont('Times', 'B', 13);
        $pdf->Cell(0, 8, 'SESIÓN ' . $sesion->tipo_sesion . ' N° ' . $sesion->nro_sesion, 0, 1, 'C');
        $pdf->SetFont('Times', '', 12);
        $pdf->Cell(0, 8, $fechaFormateada . '.', 0, 1, 'C');
        $pdf->Ln(8);

        foreach ($proyectos as $proyecto) {
            $acapite = trim($proyecto->acapite ?? '');
            if (!empty($acapite)) {
                $acapiteLimpio = strip_tags($acapite);
                $acapiteLimpio = trim(preg_replace('/\s+/', ' ', $acapiteLimpio));
            } else {
                $acapiteLimpio = '';
            }

            $destino = trim($proyecto->destino ?? '');

            $linea = '<strong>' . $proyecto->nro_expediente . '</strong>';
            if (!empty($acapiteLimpio)) {
                $linea .= '. ' . $acapiteLimpio;
            }
            if (!empty($destino)) {
                $linea .= '. ' . $destino;
            }
            $linea .= '.';

            $html = '<p style="text-align: justify; font-family: Times, serif; font-size: 12pt; margin: 0; padding: 0;">';
            $html .= $linea;
            $html .= '</p>';

            $pdf->writeHTML($html, true, false, true, false, '');
            $pdf->Ln(3);
        }

        $filename = 'sesion_' . strtolower($sesion->tipo_sesion) . '_nro_' . $sesion->nro_sesion . '_' . $carbon->format('Y-m-d') . '.pdf';
        $pdf->Output($filename, 'I');
    }

    public function destroySesion($id)
    {
        $sesion = Sesion::findOrFail($id);

        ProyectosEnEstudio::where('sesion_id', $sesion->id)->update(['sesion_id' => null]);

        $sesion->delete();

        return redirect()->route('proyectos-en-estudio.asuntos-entrados')
            ->with('success', 'Sesión eliminada. Los documentos volvieron al listado general.');
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
