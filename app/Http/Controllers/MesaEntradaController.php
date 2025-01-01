<?php

namespace App\Http\Controllers;

use App\Models\ArchivosDocumento;
use TCPDF;
use App\Models\Destino;
use App\Models\Firmante;
use App\Models\MapaRecorrido;
use App\Models\MesaEntrada;
use App\Models\MesaEntradaFirmante;
use App\Models\Origen;
use App\Models\RecorridoDoc;
use App\Models\TipoDoc;
use App\Models\User;
use App\Models\UserDestino;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isNull;

class MesaEntradaController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:Mesa de Entrada', ['only' => ['index', 'show']]);
        $this->middleware('permission:Mesa de Entrada', ['only' => ['store', 'create']]);
        $this->middleware('permission:Mesa de Entrada', ['only' => ['update', 'edit']]);
        $this->middleware('permission:Mesa de Entrada', ['only' => ['destroy']]);
    }
    // public function index()
    // {
    //     $heads = [
    //         '',
    //         'Nro MEntrada',
    //         'Año',
    //         'Fecha Recepción',
    //         'Origen',
    //         'Tipo Doc',
    //         'Destino',
    //         'Observación',
    //         'Estado',
    //         'Usuario',
    //         'Acción'
    //     ];

    //     // Obtener todas las entradas de mesa junto con la información de si tienen documentos o no
    //     $mesasEntrada = MesaEntrada::with(['documentos', 'firmantes'])->get()->map(function ($mesaEntrada) {
    //         // Agregar una propiedad indicando si tiene documentos
    //         $mesaEntrada->tiene_documentos = $mesaEntrada->documentos->isNotEmpty();

    //         // Agregar una propiedad indicando si tiene firmantes
    //         $mesaEntrada->tiene_firmantes = $mesaEntrada->firmantes->isNotEmpty();

    //         return $mesaEntrada;
    //     });

    //     return view('mesa_entrada.index', ['mesasEntrada' => $mesasEntrada, 'heads' => $heads]);
    // }
    public function index()
    {
        $heads = [
            '',
            'Nro MEntrada',
            'Año',
            'Fecha Recepción',
            'Origen',
            'Tipo Doc',
            'Destino',
            'Observación',
            'Estado',
            'Usuario',
            'Acción'
        ];

        // Obtener todas las entradas de mesa junto con la información de si tienen documentos o no
        // $mesasEntrada = MesaEntrada::with(['documentos', 'firmantes'])->get()->map(function ($mesaEntrada) {
        //     // Agregar una propiedad indicando si tiene documentos
        //     $mesaEntrada->tiene_documentos = $mesaEntrada->documentos->isNotEmpty();

        //     // Agregar una propiedad indicando si tiene firmantes
        //     $mesaEntrada->tiene_firmantes = $mesaEntrada->firmantes->isNotEmpty();

        //     return $mesaEntrada;
        //  });



        //return view('mesa_entrada.index', ['mesasEntrada' => $mesasEntrada, 'heads' => $heads]);
        $heads = [
            '',
            'Nro MEntrada',
            'Año',
            'Fecha Recepción',
            'Origen',
            'Tipo Doc',
            'Firmantes',
            'Destino',
            'Observación',
            'Estado',
            'Usuario',
            'Acciones'
        ];

        return view('mesa_entrada.index', ['heads' => $heads]);
    }
    public function recepcionado()
    {
        $heads = [
            '',
            'Nro MEntrada',
            'Año',
            'Fecha Recepción',
            'Origen',
            'Tipo Doc',
            'Observación',
            'Estado',
            'Usuario',
            'Acción',
            'Ult. Act.'
        ];
        $userId = auth()->id();
        $usuario = User::find($userId);
        $userDestino = UserDestino::where('user_id', $userId)->first();
        $iddest = $userDestino->destino_id;
        $destinos = Destino::all();
        $mesasEntrada = MesaEntrada::join('mapa_recorrido', 'mesa_entrada.id', '=', 'mapa_recorrido.id_mentrada')
            ->leftJoin('mesa_entrada_firmante as mef', 'mesa_entrada.id', '=', 'mef.id_mentrada')
            ->leftJoin('firmantes as f', 'mef.id_firmante', '=', 'f.id')
            ->where('mapa_recorrido.estado', '!=', 0)
            ->where('mapa_recorrido.id_actual', $iddest)
            ->select(
                'mesa_entrada.id',
                'mesa_entrada.modificar',
                'mesa_entrada.nro_mentrada',
                'mesa_entrada.anho',
                'mesa_entrada.fecha_recepcion',
                'mesa_entrada.id_origen',
                'mesa_entrada.id_tipo_doc',
                'mesa_entrada.id_destino',
                'mesa_entrada.observacion',
                'mesa_entrada.estado',
                'mesa_entrada.id_usuario',
                'mesa_entrada.created_at',
                'mesa_entrada.updated_at',
                'mapa_recorrido.estado as mapa_estado',
                'mapa_recorrido.observacion as mapa_observacion',
                'mapa_recorrido.id_actual as origeninterno',
                'mapa_recorrido.id_destino as destinointerno',
                'mapa_recorrido.created_at as mapa_created_at',
                DB::raw('GROUP_CONCAT(f.nombre SEPARATOR ", ") as nombres_firmantes')
            )
            ->groupBy(
                'mesa_entrada.id',
                'mesa_entrada.modificar',
                'mesa_entrada.nro_mentrada',
                'mesa_entrada.anho',
                'mesa_entrada.fecha_recepcion',
                'mesa_entrada.id_origen',
                'mesa_entrada.id_tipo_doc',
                'mesa_entrada.id_destino',
                'mesa_entrada.observacion',
                'mesa_entrada.estado',
                'mesa_entrada.id_usuario',
                'mesa_entrada.created_at',
                'mesa_entrada.updated_at',
                'mapa_recorrido.estado',
                'mapa_recorrido.observacion',
                'mapa_recorrido.id_actual',
                'mapa_recorrido.id_destino',
                'mapa_recorrido.created_at'
            )
            ->with('documentos')
            ->get()
            ->map(function ($mesaEntrada) {
                $mesaEntrada->tiene_documentos = $mesaEntrada->documentos->isNotEmpty();
                return $mesaEntrada;
            });


        //dd($mesasEntrada);
        return view('mesa_entrada.recepcionado', ['mesasEntrada' => $mesasEntrada, 'destinos' => $destinos, 'heads' => $heads]);
    }
    // public function reenviado()
    // {
    //     $heads = [
    //         '',
    //         'Nro MEntrada',
    //         'Año',
    //         'Fecha Recepción',
    //         'Origen',
    //         'Tipo Doc',
    //         'Firmantes',
    //         'Observación',
    //         'Estado',
    //         'Usuario',
    //         'Acción',
    //         'Ult. Act.'
    //     ];
    //     $userId = auth()->id();
    //     $userDestino = UserDestino::where('user_id', $userId)->first();
    //     $iddest = $userDestino->destino_id;
    //     $destinos = Destino::all();
    //     $mesasEntrada = MesaEntrada::whereIn('id', function ($query) use ($iddest) {
    //         $query->select('me.id')
    //             ->from('mesa_entrada as me')
    //             ->join('mapa_recorrido as mr', 'mr.id_mentrada', '=', 'me.id')
    //             ->where('mr.id_actual', $iddest)
    //             ->where('mr.estado', '=', 0) // Excluir estado 1
    //             ->distinct();
    //     })
    //         ->with([
    //             'documentos', // Cargar la relación con documentos
    //             'recorridoDocs' => function ($query) {
    //                 $query->orderBy('created_at', 'desc');
    //             },
    //             'user' // Cargar la relación con el usuario
    //         ])
    //         ->get()
    //         ->map(function ($mesaEntrada) {
    //             // Agregar la propiedad si tiene documentos
    //             $mesaEntrada->tiene_documentos = $mesaEntrada->documentos->isNotEmpty();

    //             // Obtener el último recorrido con el destino
    //             $recorrido = DB::table('mapa_recorrido as mr')
    //                 ->leftJoin('destinos as d', 'mr.id_destino', '=', 'd.id') // Unir con la tabla destinos
    //                 ->where('mr.id_mentrada', $mesaEntrada->id)
    //                 ->orderBy('mr.created_at', 'desc') // Ordenar por la fecha de creación más reciente
    //                 ->first(['mr.created_at', 'mr.estado', 'd.nombre as destino_nombre']); // Traer también el nombre del destino

    //             // Asignar la fecha de creación del recorrido, estado y nombre del destino
    //             $mesaEntrada->fecha_creacion_recorrido = $recorrido ? $recorrido->created_at : null;
    //             $mesaEntrada->estado_recorrido = $recorrido ? $recorrido->estado : null;
    //             $mesaEntrada->destino_nombre = $recorrido ? $recorrido->destino_nombre : null; // Nombre del destino

    //             // Obtener los nombres de los firmantes concatenados
    //             $nombresFirmantes = DB::table('mesa_entrada_firmante AS mef')
    //                 ->join('firmantes AS f', 'f.id', '=', 'mef.id_firmante')
    //                 ->where('mef.id_mentrada', $mesaEntrada->id)
    //                 ->select(DB::raw('GROUP_CONCAT(f.nombre SEPARATOR ", ") AS nombres_firmantes'))
    //                 ->pluck('nombres_firmantes')
    //                 ->first();

    //             // Asignar los nombres de los firmantes a la entrada de mesa
    //             $mesaEntrada->nombres_firmantes = $nombresFirmantes;

    //             return $mesaEntrada;
    //         });
    //     //dd($mesasEntrada);


    //     return view('mesa_entrada.reenviado', ['mesasEntrada' => $mesasEntrada, 'destinos' => $destinos, 'heads' => $heads]);
    // }
    public function reenviado()
    {
        $heads = [
            '',
            'Nro MEntrada',
            'Año',
            'Fecha Recepción',
            'Origen',
            'Tipo Doc',
            'Firmantes',
            'Observación',
            'Estado',
            'Usuario',
            'Acción',
            'Ult. Act.'
        ];
        $destinos = Destino::all();
        return view('mesa_entrada.reenviado', ['heads' => $heads, 'destinos' => $destinos]);
    }
    public function finalizado()
    {
        $heads = [
            '',
            'Nro MEntrada',
            'Año',
            'Fecha Recepción',
            'Origen',
            'Tipo Doc',
            'Destino',
            'Observación',
            'Estado',
            'Usuario',
            'Acción',
            'Ult. Act.'
        ];
        $userId = auth()->id();
        $userDestino = UserDestino::where('user_id', $userId)->first();
        $iddest = $userDestino->destino_id;
        $destinos = Destino::all();
        $mesasEntrada = MesaEntrada::join('mapa_recorrido', 'mesa_entrada.id', '=', 'mapa_recorrido.id_mentrada')
            ->where('mapa_recorrido.estado', '=', 0)
            ->where('mapa_recorrido.id_actual', $iddest)
            ->select(
                'mesa_entrada.*',
                'mapa_recorrido.estado as mapa_estado',
                'mapa_recorrido.observacion as mapa_observacion',
                'mapa_recorrido.id_destino as destinointerno',
                'mapa_recorrido.created_at as mapa_created_at'
            )
            ->get();
        return view('mesa_entrada.finalizado', ['mesasEntrada' => $mesasEntrada, 'destinos' => $destinos, 'heads' => $heads]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $origenes = $origenes = Origen::orderBy('indice')->orderBy('subindice')->get();
        $tiposdoc = TipoDoc::all();
        $destinos = Destino::all();
        return view('mesa_entrada.create', ['origenes' => $origenes, 'tiposDoc' => $tiposdoc, 'destinos' => $destinos]);
    }
    public function reportetipodocfechas(): View
    {

        return view('mesa_entrada.resporfechas');
    }
    public function aux(): View
    {
        $origenes = $origenes = Origen::orderBy('indice')->orderBy('subindice')->get();
        $tiposdoc = TipoDoc::all();
        $destinos = Destino::all();
        return view('mesa_entrada.createaux', ['origenes' => $origenes, 'tiposDoc' => $tiposdoc, 'destinos' => $destinos]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            //dd($request->input());
            DB::transaction(function () use ($request) {
                $validatedData = $request->validate([
                    'id_origen' => 'required|integer',
                    'nromesaentrada' => 'required|integer',
                    'id_tipo_doc' => 'required|integer',
                    'id_destino' => 'required|integer',
                    'observacion' => 'nullable|string',
                    'idfirmante' => 'required|array',
                    'cedula' => 'nullable|array',
                    'nombre' => 'required|array',
                    'telefono' => 'nullable|array',
                    'email.*' => 'nullable|email',
                ]);
                $anho = date('Y');

                // Obtener el mayor número de 'nro_mentrada' para el año especificado
                $maxNroMentrada = MesaEntrada::where('anho', $anho)->max('nro_mentrada');

                // Si no se encuentra ningún registro, establecer el número como 0
                if (is_null($maxNroMentrada)) {
                    $maxNroMentrada = 1;
                } else {
                    $maxNroMentrada++;
                }

                $fechaAct = date('Y-m-d');
                $userId = auth()->id();
                $destinoactual = UserDestino::where('user_id', $userId)->first();

                // Crear una nueva instancia del modelo MesaEntrada
                $mesaEntrada = new MesaEntrada();

                // Asignar cada campo individualmente
                $mesaEntrada->nro_mentrada = $request->input('nromesaentrada'); //$maxNroMentrada;
                $mesaEntrada->anho = $anho;
                $mesaEntrada->fecha_recepcion = $request->input('fechaemision');
                $mesaEntrada->id_origen = $request->input('id_origen');
                $mesaEntrada->id_tipo_doc = $request->input('id_tipo_doc');
                $mesaEntrada->id_destino = $request->input('id_destino');
                $mesaEntrada->observacion = $request->input('observacion');
                $mesaEntrada->duplicado = $request->input('duplicado');
                $mesaEntrada->estado = 1;
                $mesaEntrada->id_usuario = $userId;

                // Guardar el nuevo registro en la base de datos
                $mesaEntrada->save();





                // if ($request->hasFile('archivo')) {
                //     $archivo = $request->file('archivo');

                //     // Crear nuevo nombre de archivo
                //     $descripcion = substr($request->input('descripcion'), 0, 15);
                //     $descripcionSinEspacios = str_replace(' ', '_', $descripcion);
                //     $fechaHora = date('Ymd_His');
                //     $extension = $archivo->getClientOriginalExtension();
                //     $nombreNuevoArchivo = $descripcionSinEspacios . '_' . $fechaHora . '.' . $extension;

                //     // Mover archivo a la carpeta 'archivos'
                //     $rutaArchivo = $archivo->move(public_path('archivos'), $nombreNuevoArchivo);

                //     // Crear registro en la base de datos
                //     ArchivosDocumento::create([
                //         'id_mentrada' => $mesaEntrada->id,
                //         'nombre_archivo' => $nombreNuevoArchivo,
                //         'ruta_archivo' => 'archivos/' . $nombreNuevoArchivo,
                //     ]);
                // }
                $mapaRecorrido = new MapaRecorrido();
                $mapaRecorrido->id_mentrada = $mesaEntrada->id;
                $mapaRecorrido->fecha_recepcion = $fechaAct;
                $mapaRecorrido->id_actual = $destinoactual->destino_id;
                $mapaRecorrido->id_destino =  $request->input('id_destino');
                $mapaRecorrido->observacion = $request->input('observacion');
                $mapaRecorrido->estado = 1;
                //dd('a');
                // Guardar el nuevo registro en la base de datos
                $mapaRecorrido->save();

                date_default_timezone_set('America/Asuncion'); // Cambia 'America/Asuncion' por tu zona horaria

                // Obtener la fecha y hora actual en el formato deseado
                $destino = Destino::find($destinoactual->destino_id);
                $userId = auth()->id();
                $fechaHoraActual = date('Y-m-d H:i:s');
                $recorridodoc = new RecorridoDoc();
                $recorridodoc->id_mentrada = $mesaEntrada->id;
                $recorridodoc->fecha = $fechaHoraActual;
                $recorridodoc->descripcion = 'Recepcionado: ' . $destino->nombre;
                $recorridodoc->id_usuario = $userId;

                // Guardar el nuevo registro en la base de datos
                $recorridodoc->save();

                if ($request->hasFile('documento')) {
                    $file = $request->file('documento');

                    // Crear nuevo nombre de archivo
                    $descripcion = substr($request->input('descripcion'), 0, 15);
                    $descripcionSinEspacios = str_replace(' ', '_', $descripcion);
                    $fechaHora = date('Ymd_His');
                    $extension = $file->getClientOriginalExtension();
                    $nombreNuevo = $descripcionSinEspacios . '_' . $fechaHora . '.' . $extension;

                    // Mover archivo a la carpeta 'documentos'
                    $rutaDocumento = $file->move(public_path('documentos'), $nombreNuevo);

                    // Crear registro en la base de datos
                    ArchivosDocumento::create([
                        'id_recorrido' => $recorridodoc->id,
                        'id_mentrada' => $mesaEntrada->id,
                        'nombre_archivo' => $nombreNuevo,
                        'ruta_archivo' => 'documentos/' . $nombreNuevo,
                        'id_usuario' => $userId,
                    ]);
                }

                foreach ($validatedData['idfirmante'] as $index => $idfirmante) {
                    // Si el idfirmante es 0, crear un nuevo registro de Firmante

                    if ($idfirmante == 0) {
                        $firmanteData = [
                            'nombre' => $validatedData['nombre'][$index],
                        ];

                        // Agregar el correo electrónico si está presente y no es null
                        if (isset($validatedData['email'][$index])) {
                            $firmanteData['correo'] = $validatedData['email'][$index];
                        }
                        if (isset($validatedData['cedula'][$index])) {
                            $firmanteData['cedula'] = $validatedData['cedula'][$index];
                        } else {
                            $firmanteData['cedula'] = 0;
                        }
                        if (isset($validatedData['telefono'][$index])) {
                            $firmanteData['telefono'] = $validatedData['telefono'][$index];
                        }
                        $firmante = Firmante::create($firmanteData);
                    } else {
                        // Buscar el firmante en la base de datos y actualizar si existe
                        $firmante = Firmante::find($idfirmante);
                        if ($firmante) {
                            $firmante->update([
                                'nombre' => $validatedData['nombre'][$index],
                                'cedula' => $validatedData['cedula'][$index],
                                'telefono' => $validatedData['telefono'][$index],
                                'correo' => $validatedData['email'][$index] ?? null, // Actualizar el correo solo si está presente
                            ]);
                        }
                    }

                    // Guardar en mesa_entrada_firmante
                    if ($firmante) {
                        MesaEntradaFirmante::updateOrCreate(
                            ['id_mentrada' => $mesaEntrada->id, 'id_firmante' => $firmante->id],
                            ['created_at' => now(), 'updated_at' => now()]
                        );
                    }
                }
            });

            return redirect()->route('mesaentrada.create')->with('success', 'Operación exitosa');
        } catch (Exception $e) {
            dd($e);
            return redirect()->route('mesaentrada.create')->with('error', 'Hubo un problema con la operación. Por favor, inténtelo de nuevo.');
        }
    }
    public function storeaux(Request $request)
    {

        try {
            DB::transaction(function () use ($request) {
                $validatedData = $request->validate([
                    'id_origen' => 'required|integer',
                    'id_tipo_doc' => 'required|integer',
                    'nromesaentrada' => 'required|integer',
                    'id_destino' => 'required|integer',
                    'observacion' => 'nullable|string',
                    'idfirmante' => 'required|array',
                    'cedula' => 'nullable|array',
                    'nombre' => 'required|array',
                    'telefono' => 'nullable|array',
                    'email.*' => 'nullable|email',
                ]);
                $anho = date('Y');
                $maxSuplementario = MesaEntrada::where('nro_mentrada', $request->input('nromesaentrada'))
                    ->max('nro_suplementario');

                $nuevoSuplementario = $maxSuplementario ? $maxSuplementario + 1 : 1;

                // Usar la fecha de recepción ingresada por el usuario
                $fechaRecepcion = $request->input('fechaemision'); // Asume que es una fecha válida en formato 'Y-m-d'
                $userId = auth()->id();
                $destinoactual = UserDestino::where('user_id', $userId)->first();

                $mesaEntrada = new MesaEntrada();
                $mesaEntrada->nro_mentrada = $request->input('nromesaentrada');
                $mesaEntrada->nro_suplementario = $nuevoSuplementario;
                $mesaEntrada->anho = $anho;
                $mesaEntrada->fecha_recepcion = $fechaRecepcion; // Asignar la fecha de recepción
                $mesaEntrada->id_origen = $request->input('id_origen');
                $mesaEntrada->id_tipo_doc = $request->input('id_tipo_doc');
                $mesaEntrada->id_destino = $request->input('id_destino');
                $mesaEntrada->observacion = $request->input('observacion');
                $mesaEntrada->duplicado = $request->input('duplicado');
                $mesaEntrada->estado = 1;
                $mesaEntrada->id_usuario = $userId;

                // Asignar fecha de creación y actualización
                $mesaEntrada->created_at = $fechaRecepcion;
                $mesaEntrada->updated_at = $fechaRecepcion;

                $mesaEntrada->save();

                $mapaRecorrido = new MapaRecorrido();
                $mapaRecorrido->id_mentrada = $mesaEntrada->id;
                $mapaRecorrido->fecha_recepcion = $fechaRecepcion; // Usar la fecha ingresada
                $mapaRecorrido->id_actual = $destinoactual->destino_id;
                $mapaRecorrido->id_destino = $request->input('id_destino');
                $mapaRecorrido->observacion = $request->input('observacion');
                $mapaRecorrido->estado = 1;

                // Asignar fecha de creación y actualización
                $mapaRecorrido->created_at = $fechaRecepcion;
                $mapaRecorrido->updated_at = $fechaRecepcion;

                // Guardar el nuevo registro en la base de datos
                $mapaRecorrido->save();

                date_default_timezone_set('America/Asuncion'); // Cambia 'America/Asuncion' por tu zona horaria

                // Obtener la fecha y hora actual en el formato deseado
                $destino = Destino::find($destinoactual->destino_id);
                $fechaHoraActual = date('Y-m-d H:i:s');

                $recorridodoc = new RecorridoDoc();
                $recorridodoc->id_mentrada = $mesaEntrada->id;
                $recorridodoc->fecha = $fechaHoraActual;
                $recorridodoc->descripcion = 'Recepcionado: ' . $destino->nombre;
                $recorridodoc->id_usuario = $userId;

                // Asignar fecha de creación y actualización
                $recorridodoc->created_at = $fechaRecepcion;
                $recorridodoc->updated_at = $fechaRecepcion;

                // Guardar el nuevo registro en la base de datos
                $recorridodoc->save();

                if ($request->hasFile('documento')) {
                    $file = $request->file('documento');

                    // Crear nuevo nombre de archivo
                    $descripcion = substr($request->input('descripcion'), 0, 15);
                    $descripcionSinEspacios = str_replace(' ', '_', $descripcion);
                    $fechaHora = date('Ymd_His');
                    $extension = $file->getClientOriginalExtension();
                    $nombreNuevo = $descripcionSinEspacios . '_' . $fechaHora . '.' . $extension;

                    // Mover archivo a la carpeta 'documentos'
                    $rutaDocumento = $file->move(public_path('documentos'), $nombreNuevo);

                    // Crear registro en la base de datos
                    ArchivosDocumento::create([
                        'id_recorrido' => $recorridodoc->id,
                        'id_mentrada' => $mesaEntrada->id,
                        'nombre_archivo' => $nombreNuevo,
                        'ruta_archivo' => 'documentos/' . $nombreNuevo,
                        'id_usuario' => $userId,
                        'created_at' => $fechaRecepcion, // Asignar fecha de creación
                        'updated_at' => $fechaRecepcion, // Asignar fecha de actualización
                    ]);
                }

                foreach ($validatedData['idfirmante'] as $index => $idfirmante) {
                    // Si el idfirmante es 0, crear un nuevo registro de Firmante
                    if ($idfirmante == 0) {
                        $firmanteData = [
                            'nombre' => $validatedData['nombre'][$index],
                            'correo' => $validatedData['email'][$index] ?? null,
                            'cedula' => $validatedData['cedula'][$index] ?? 0,
                            'telefono' => $validatedData['telefono'][$index] ?? null,
                            'created_at' => $fechaRecepcion, // Asignar fecha de creación
                            'updated_at' => $fechaRecepcion, // Asignar fecha de actualización
                        ];

                        $firmante = Firmante::create($firmanteData);
                    } else {
                        // Buscar el firmante en la base de datos y actualizar si existe
                        $firmante = Firmante::find($idfirmante);
                        if ($firmante) {
                            $firmante->update([
                                'nombre' => $validatedData['nombre'][$index],
                                'cedula' => $validatedData['cedula'][$index],
                                'telefono' => $validatedData['telefono'][$index],
                                'correo' => $validatedData['email'][$index] ?? null, // Actualizar el correo solo si está presente
                                'updated_at' => $fechaRecepcion, // Asignar fecha de actualización
                            ]);
                        }
                    }

                    // Guardar en mesa_entrada_firmante
                    if ($firmante) {
                        MesaEntradaFirmante::updateOrCreate(
                            ['id_mentrada' => $mesaEntrada->id, 'id_firmante' => $firmante->id],
                            ['created_at' => $fechaRecepcion, 'updated_at' => $fechaRecepcion]
                        );
                    }
                }
            });

            return redirect()->route('createaux')->with('success', 'Operación exitosa');
        } catch (Exception $e) {
            return redirect()->route('createaux')->with('error', 'Hubo un problema con la operación. Por favor, inténtelo de nuevo.');
        }
    }
    public function storedocs(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                // Validar los datos del formulario
                $request->validate([
                    'documento' => 'nullable|mimes:pdf,doc,docx|max:2048',
                    'archivo' => 'nullable|mimes:zip,rar|max:2048',
                    'link' => 'nullable|url',
                    'observacion' => 'nullable|string|max:255',
                    'descripcion' => 'nullable|string|max:255',
                    'idmentrada1' => 'required|integer',
                ]);
                $userId = auth()->id();
                // Obtener el ID de entrada
                $idEntrada = $request->input('idmentrada1');

                // Procesar el documento
                $documentoPath = null;
                $nombreDocumento = null;
                if ($request->hasFile('documento')) {
                    $file = $request->file('documento');
                    $documentoPath = $this->handleFileUpload($file, 'documentos', $request->input('descripcion'));
                    $nombreDocumento = basename($documentoPath);
                }

                // Procesar el archivo
                $archivoPath = null;
                $nombreArchivo = null;
                if ($request->hasFile('archivo')) {
                    $file = $request->file('archivo');
                    $archivoPath = $this->handleFileUpload($file, 'archivos', $request->input('descripcion'));
                    $nombreArchivo = basename($archivoPath);
                }

                // Asegurarse de que siempre haya un nombre de archivo
                $nombreArchivoFinal = $nombreDocumento ?? $nombreArchivo;
                $maxId = RecorridoDoc::where('id_mentrada', $idEntrada)->max('id');
                // Crear el registro en la base de datos
                $data = [
                    'id_recorrido' => $maxId,
                    'id_mentrada' => $idEntrada,
                    'nombre_archivo' => $nombreArchivoFinal,
                    'ruta_archivo' => $documentoPath ?? $archivoPath,
                    'link' => $request->input('link'),
                    'observacion' => $request->input('observacion'),
                    'id_usuario' => $userId,
                ];
                // Imprimir los datos para depuración
                ArchivosDocumento::create($data);
                DB::commit();
            });

            return redirect()->route('recepciondoc')->with('success', 'Operación exitosa');
        } catch (Exception $e) {
            return redirect()->route('recepciondoc')->with('error', 'Hubo un problema con la operación. Por favor, inténtelo de nuevo.');
        }
    }

    private function handleFileUpload($file, $folder, $description)
    {
        // Crear nuevo nombre de archivo
        $descripcion = substr($description, 0, 15);
        $descripcionSinEspacios = str_replace(' ', '_', $descripcion);
        $fechaHora = date('Ymd_His');
        $extension = $file->getClientOriginalExtension();
        $nombreNuevo = $descripcionSinEspacios . '_' . $fechaHora . '.' . $extension;

        // Mover archivo a la carpeta especificada
        $rutaArchivo = $file->move(public_path($folder), $nombreNuevo);

        return $folder . '/' . $nombreNuevo;
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $mesaEntrada = MesaEntrada::findOrFail($id);
            $mesaEntrada->fechaemision = Carbon::parse($mesaEntrada->fechaemision)->format('Y-m-d');
            $origenes = Origen::all();
            $tiposdoc = TipoDoc::all();
            $destinos = Destino::all();
            $firmantes = MesaEntradaFirmante::where('id_mentrada', $id)
                ->with('firmante')
                ->get()
                ->pluck('firmante');
            return view('mesa_entrada.edit', ['origenes' => $origenes, 'tiposDoc' => $tiposdoc, 'destinos' => $destinos, 'mesaEntrada' => $mesaEntrada, 'firmantes' => $firmantes]);
        } catch (Exception $e) {
            return redirect()->route('mesaentrada.index')->with('error', 'No se pudo completar la operación.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            DB::transaction(function () use ($request, $id) {
                $validatedData = $request->validate([
                    'id_origen' => 'required|integer',
                    'modificar' => 'nullable|integer',
                    'id_tipo_doc' => 'required|integer',
                    'id_destino' => 'required|integer',
                    'observacion' => 'nullable|string',
                    'idfirmante' => 'required|array',
                    'cedula' => 'required|array',
                    'nombre' => 'required|array',
                    'telefono' => 'required|array',
                    'email.*' => 'nullable|email',
                ]);

                $mesaEntrada = MesaEntrada::findOrFail($id);

                // Actualizar los campos de MesaEntrada
                $mesaEntrada->update([
                    'id_origen' => $validatedData['id_origen'],
                    'id_tipo_doc' => $validatedData['id_tipo_doc'],
                    'id_destino' => $validatedData['id_destino'],
                    'observacion' => $validatedData['observacion'],
                    'modificar' => 0,
                ]);

                // Obtener los firmantes actuales
                $currentFirmantes = MesaEntradaFirmante::where('id_mentrada', $mesaEntrada->id)
                    ->pluck('id_firmante')
                    ->toArray();

                // Determinar los firmantes que se eliminarán
                $newFirmantes = $validatedData['idfirmante'];
                $firmantesToDelete = array_diff($currentFirmantes, $newFirmantes);

                // Eliminar firmantes que ya no están en la lista
                if (!empty($firmantesToDelete)) {
                    MesaEntradaFirmante::whereIn('id_firmante', $firmantesToDelete)
                        ->where('id_mentrada', $mesaEntrada->id)
                        ->delete();
                }

                foreach ($validatedData['idfirmante'] as $index => $idfirmante) {
                    // Si el idfirmante es 0, crear un nuevo registro de Firmante
                    if ($idfirmante == 0) {
                        $firmanteData = [
                            'nombre' => $validatedData['nombre'][$index],
                            'cedula' => $validatedData['cedula'][$index],
                            'telefono' => $validatedData['telefono'][$index],
                        ];

                        // Agregar el correo electrónico si está presente y no es null
                        if (isset($validatedData['email'][$index])) {
                            $firmanteData['correo'] = $validatedData['email'][$index];
                        }

                        $firmante = Firmante::create($firmanteData);
                    } else {
                        // Buscar el firmante en la base de datos y actualizar si existe
                        $firmante = Firmante::find($idfirmante);
                        if ($firmante) {
                            $firmante->update([
                                'nombre' => $validatedData['nombre'][$index],
                                'cedula' => $validatedData['cedula'][$index],
                                'telefono' => $validatedData['telefono'][$index],
                                'correo' => $validatedData['email'][$index] ?? null,
                            ]);
                        }
                    }

                    // Guardar en mesa_entrada_firmante
                    if ($firmante) {
                        MesaEntradaFirmante::updateOrCreate(
                            ['id_mentrada' => $mesaEntrada->id, 'id_firmante' => $firmante->id],
                            ['created_at' => now(), 'updated_at' => now()]
                        );
                    }
                }
            });

            return redirect()->route('mesaentrada.index')->with('success', 'Mesa de Entrada actualizada exitosamente.');
        } catch (Exception $e) {
            dd($e);
            return redirect()->route('mesaentrada.index')->with('error', 'No se pudo completar la operación.');
        }
    }




    /**
     * Display the specified resource.
     */
    public function show(MesaEntrada $mesaEntrada)
    {
        return response()->json($mesaEntrada);
    }

    /**
     * Update the specified resource in storage.
     */


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {

        try {
            $mesaEntrada = MesaEntrada::find($request->input('id_mentrada'));
            DB::transaction(function () use ($mesaEntrada) {
                $documentos = ArchivosDocumento::where('id_mentrada', $mesaEntrada->id)->get();

                foreach ($documentos as $documento) {
                    // Eliminar el archivo del sistema de archivos
                    $rutaArchivo = public_path($documento->ruta_archivo);
                    if (file_exists($rutaArchivo)) {
                        unlink($rutaArchivo);
                    }

                    // Eliminar el registro de la base de datos
                    $documento->delete();
                }

                // Eliminar todos los registros relacionados en mesa_entrada_firmante
                MesaEntradaFirmante::where('id_mentrada', $mesaEntrada->id)->delete();

                // Eliminar todos los registros relacionados en RecorridoDoc
                RecorridoDoc::where('id_mentrada', $mesaEntrada->id)->delete();

                // Eliminar todos los registros relacionados en MapaRecorrido
                MapaRecorrido::where('id_mentrada', $mesaEntrada->id)->delete();

                // Eliminar el registro principal en la tabla MesaEntrada
                $mesaEntrada->delete();
            });

            return redirect()->route('mesaentrada.index')->with('success', 'Operación exitosa');
        } catch (Exception $e) {
            return redirect()->route('mesaentrada.create')->with('error', 'Vuelva a intentarlo');
        }
    }

    public function enviar($id)
    {
        DB::beginTransaction();

        try {
            $userId = Auth::id();
            $userDestino = UserDestino::where('user_id', $userId)->first();
            $mapaRecorrido = MapaRecorrido::where('id_mentrada', $id)
                ->where('estado', '>', 0)
                ->where('id_actual', $userDestino->destino_id)
                ->first();

            if ($mapaRecorrido) {
                $mapaRecorrido->estado = 0;
                $mapaRecorrido->save();

                // Obtener el id_destino del registro encontrado
                $idDestino = $mapaRecorrido->id_destino;

                // Crear un nuevo registro en MapaRecorrido
                $nuevoMapaRecorrido = MapaRecorrido::create([
                    'id_mentrada' => $id,
                    'fecha_recepcion' => now(),
                    'id_actual' => $idDestino,
                    'id_destino' => null,
                    'observacion' => 'Nuevo recorrido creado',
                    'estado' => 1,
                ]);
                $mesaEntrada = MesaEntrada::findOrFail($id);
                $mesaEntrada->estado = 2;
                $mesaEntrada->save();

                date_default_timezone_set('America/Asuncion'); // Cambia 'America/Asuncion' por tu zona horaria

                // Obtener la fecha y hora actual en el formato deseado
                $destino = Destino::find($idDestino,);

                $userId = auth()->id();
                $fechaHoraActual = date('Y-m-d H:i:s');
                $recorridodoc = new RecorridoDoc();
                $recorridodoc->id_mentrada = $mesaEntrada->id;
                $recorridodoc->fecha = $fechaHoraActual;
                $recorridodoc->descripcion = 'Enviado: ' . $destino->nombre;
                $recorridodoc->id_usuario = $userId;

                // Guardar el nuevo registro en la base de datos
                $recorridodoc->save();
                //dd($nuevoMapaRecorrido);
                DB::commit();

                return redirect()->route('mesaentrada.index')->with('success', 'Mesa de Entrada actualizada exitosamente.');
            } else {
                DB::rollBack();
                return redirect()->route('mesaentrada.index')->with('error', 'Mesa de Entrada no se pudo actualizar.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('mesaentrada.index')->with('error', 'Mesa de Entrada no se pudo actualizar.');
        }
    }
    public function aceptar($id)
    {
        DB::beginTransaction();

        try {
            // Buscar el mapa de recorrido por id_mentrada
            $userId = Auth::id();
            $userDestino = UserDestino::where('user_id', $userId)->first();
            $mapaRecorrido = MapaRecorrido::where('id_mentrada', $id)
                ->where('estado', 1)
                ->where('id_actual', $userDestino->destino_id)
                ->first();



            if ($mapaRecorrido) {
                $mapaRecorrido->estado = 2;
                $mapaRecorrido->save();


                $mesaEntrada = MesaEntrada::findOrFail($id);
                // $mesaEntrada->estado = 3;
                // $mesaEntrada->save();
                date_default_timezone_set('America/Asuncion'); // Cambia 'America/Asuncion' por tu zona horaria

                // Obtener la fecha y hora actual en el formato deseado
                $destino = Destino::find($mapaRecorrido->id_actual);

                $userId = auth()->id();

                $fechaHoraActual = date('Y-m-d H:i:s');
                $recorridodoc = new RecorridoDoc();
                $recorridodoc->id_mentrada = $mapaRecorrido->id_mentrada;
                $recorridodoc->fecha = $fechaHoraActual;
                $recorridodoc->descripcion = 'Confirmado Recepcion: ' . $destino->nombre;
                $recorridodoc->id_usuario = $userId;

                // Guardar el nuevo registro en la base de datos
                $recorridodoc->save();
                //dd($nuevoMapaRecorrido);
                //$destinos = Destino::all();
                //return view('mesa_entrada.create', ['origenes' => $origenes, 'tiposDoc' => $tiposdoc, 'destinos' => $destinos]);
                DB::commit();
                return redirect()->route('recepciondoc')->with('success', 'Mesa de Entrada actualizada exitosamente.');
            } else {
                DB::rollBack();
                return redirect()->route('recepciondoc')->with('error', 'Mesa de Entrada no se pudo actualizar.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('recepciondoc')->with('error', 'Mesa de Entrada no se pudo actualizar.');
        }
    }
    public function finalizar($id)
    {
        DB::beginTransaction();

        try {
            // Buscar el mapa de recorrido por id_mentrada
            $contador = MapaRecorrido::where('id_mentrada', $id)
                ->where('estado', '>', 0)
                ->count();
            $userId = Auth::id();
            $userDestino = UserDestino::where('user_id', $userId)->first();
            $mapaRecorrido = MapaRecorrido::where('id_mentrada', $id)
                ->where('estado', 2)
                ->where('id_actual', $userDestino->destino_id)
                ->first();
            if ($mapaRecorrido) {
                $mapaRecorrido->estado = 0;
                $mapaRecorrido->save();

                if ($contador == 1) {
                    $mesaEntrada = MesaEntrada::findOrFail($id);
                    $mesaEntrada->estado = 0;
                    $mesaEntrada->save();
                }
                date_default_timezone_set('America/Asuncion'); // Cambia 'America/Asuncion' por tu zona horaria

                // Obtener la fecha y hora actual en el formato deseado
                $destino = Destino::find($mapaRecorrido->id_actual);

                $userId = auth()->id();

                $fechaHoraActual = date('Y-m-d H:i:s');
                $recorridodoc = new RecorridoDoc();
                $recorridodoc->id_mentrada = $mapaRecorrido->id_mentrada;
                $recorridodoc->fecha = $fechaHoraActual;
                $recorridodoc->descripcion = 'Trámite  Documental Finalizado: ' . $destino->nombre;
                $recorridodoc->id_usuario = $userId;

                // Guardar el nuevo registro en la base de datos
                $recorridodoc->save();
                //dd($nuevoMapaRecorrido);
                //$destinos = Destino::all();
                //return view('mesa_entrada.create', ['origenes' => $origenes, 'tiposDoc' => $tiposdoc, 'destinos' => $destinos]);
                DB::commit();
                return redirect()->route('recepciondoc')->with('success', 'Mesa de Entrada actualizada exitosamente.');
            } else {
                DB::rollBack();
                return redirect()->route('recepciondoc')->with('error', 'Mesa de Entrada no se pudo actualizar.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('recepciondoc')->with('error', 'Mesa de Entrada no se pudo actualizar.');
        }
    }
    public function reenviardoc(Request $request)
    {
        DB::beginTransaction();
        try {
            // Buscar el mapa de recorrido por id_mentrada
            $id = $request->post('idmentrada');
            $userId = Auth::id();
            $userDestino = UserDestino::where('user_id', $userId)->first();

            $mapaRecorrido = MapaRecorrido::where('id_mentrada', $id)

                ->where('id_actual', $userDestino->destino_id)
                ->first();

            if ($request->post('masdestinos') == 1) {
                date_default_timezone_set('America/Asuncion');

                $mapaRecorrido = MapaRecorrido::create([
                    'id_mentrada' => $id,
                    'fecha_recepcion' => now(),
                    'id_actual' => $userDestino->destino_id,
                    'id_destino' => null,
                    'observacion' => 'Nuevo recorrido creado',
                    'estado' => 1,
                ]);
            }
            if ($mapaRecorrido) {

                $mapaRecorrido->id_destino = $request->post('id_destino');
                $mapaRecorrido->estado = 0;
                $mapaRecorrido->save();

                MapaRecorrido::create([
                    'id_mentrada' => $id,
                    'fecha_recepcion' => now(),
                    'id_actual' => $request->post('id_destino'),
                    'id_destino' => null,
                    'observacion' => 'Nuevo recorrido creado',
                    'estado' => 1,
                ]);
                date_default_timezone_set('America/Asuncion'); // Cambia 'America/Asuncion' por tu zona horaria

                // Obtener la fecha y hora actual en el formato deseado
                $destino = Destino::find($request->post('id_destino'));
                $userId = auth()->id();

                $fechaHoraActual = date('Y-m-d H:i:s');
                $recorridodoc = new RecorridoDoc();
                $recorridodoc->id_mentrada = $mapaRecorrido->id_mentrada;
                $recorridodoc->fecha = $fechaHoraActual;
                $recorridodoc->descripcion = 'Enviado: ' . $destino->nombre;
                $recorridodoc->id_usuario = $userId;

                // Guardar el nuevo registro en la base de datos
                $recorridodoc->save();
                $mesaEntrada = MesaEntrada::findOrFail($id);
                $mesaEntrada->estado = 2;
                $mesaEntrada->save();
                DB::commit();
                return redirect()->route('reenviado')->with('success', 'Mesa de Entrada actualizada exitosamente.');
            } else {
                DB::rollBack();
                return redirect()->route('reenviado')->with('error', 'Mesa de Entrada no se pudo actualizar.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('reenviado')->with('error', 'Mesa de Entrada no se pudo actualizar.');
        }
    }

    function recorrido(MesaEntrada $row)
    {
        $recorridos = RecorridoDoc::with('user')->where('id_mentrada', $row->id)->get();
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetPrintHeader(false); // Deshabilita la impresión del encabezado
        $pdf->SetFont('Times', 'IU', 14);
        $pdf->AddPage();
        $pdf->SetLeftMargin(12); // Ajusta el margen izquierdo a 12 mm
        $pdf->Ln(25);

        // Insertar marca de agua
        $pdf->SetAlpha(0.3); // Establece la opacidad al 10%
        $pdf->Image('vendor/adminlte/dist/img/icono camara.png', 10, 50, 190); // Ajusta la posición y tamaño de la imagen
        $pdf->SetAlpha(1); // Restablece la opacidad al 100%
        // Establecer título del documento
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetTitle('Recorrido del Documento');
        $pdf->Cell(0, 10, 'Mapa Recorrido Mesa Entrada Nº: ' . $row->nro_mentrada . '/' . $row->anho, 0, 1, 'C'); // Celda centrada con el título
        $pdf->SetFont('Times', '', 12);

        $pdf->Ln(10); // Espacio antes de comenzar el diagrama

        $xPosition = 20;
        $stateNumber = 1;

        //para imprimir los documentos hay que saber la fecha de carga e inicializar una bandera
        $doccargado = -1;

        // Iterar sobre los recorridos y dibujar el diagrama
        foreach ($recorridos as $recorrido) {
            $currentPage = $pdf->getPage();
            // Dibujar el círculo
            $pdf->Circle($xPosition, $pdf->GetY() + 5, 5); // Ajustar la posición vertical para el círculo

            // Escribir el número del estado centrado en el círculo
            $pdf->SetXY($xPosition - 2.5, $pdf->GetY() + 2.5); // Ajustar la posición del número para centrarlo
            $pdf->Cell(5, 5, $stateNumber, 0, 1, 'C');

            // Escribir la descripción en negrita
            $pdf->SetX($xPosition + 10);
            $pdf->SetFont('Times', 'B', 12);
            $pdf->Write(0, $recorrido->descripcion . ' - Usuario: ' . $recorrido->user->name);

            // Escribir la fecha debajo de la descripción
            $pdf->Ln(7); // Espacio para la fecha
            $pdf->SetX($xPosition + 10);
            $pdf->SetFont('Times', '', 12);
            $fecha = \Carbon\Carbon::parse($recorrido->fecha)->format('d/m/Y H:i');
            $pdf->Write(0, "Fecha: $fecha");

            if ($doccargado == -1) {
                $doccargado = 0;
            } else {
                // Agregar información sobre los documentos
                $documentos = ArchivosDocumento::where('id_mentrada', $row->id)
                    ->where('id_recorrido', $recorrido->id)
                    ->get();

                if ($documentos->isNotEmpty()) {
                    $pdf->Ln(10); // Espacio antes de la sección de documentos
                    $pdf->SetFont('Times', 'B', 12);
                    $pdf->Cell(0, 10, 'Documentos Asociados', 0, 1, 'L');

                    $pdf->SetFont('Times', '', 12);
                    $pdf->Ln(5); // Espacio antes de la lista de documentos
                    $pdf->SetX(22);
                    $pdf->Write(0, '• Se agregaron:');

                    // Inicializar contadores para cada tipo de documento
                    $pdfCount = 0;
                    $docCount = 0;
                    $zipCount = 0;
                    $linkCount = 0;
                    $comentario = '';

                    foreach ($documentos as $documento) {
                        if (!empty($documento->link)) {
                            $linkCount++;
                        }
                        $extension = pathinfo($documento->nombre_archivo, PATHINFO_EXTENSION);
                        switch (strtolower($extension)) {
                            case 'pdf':
                                $pdfCount++;
                                break;
                            case 'doc':
                            case 'docx':
                                $docCount++;
                                break;
                            case 'zip':
                                $zipCount++;
                                break;
                        }
                        if (!empty($documento->observacion)) {
                            $comentario .= ' (' . $documento->observacion . ') ';
                        }
                    }

                    // Escribir los tipos de documentos
                    $pdf->Ln(5); // Espacio entre líneas
                    $pdf->SetX(32);
                    $pdf->Write(0, "$linkCount LINKS");
                    $pdf->Ln(5);
                    $pdf->SetX(32);
                    $pdf->Write(0, "$pdfCount PDF(s)");
                    $pdf->Ln(5);
                    $pdf->SetX(32);
                    $pdf->Write(0, "$docCount Docx(s)");
                    $pdf->Ln(5);
                    $pdf->SetX(32);
                    $pdf->Write(0, "$zipCount ZIP(s)");
                    $pdf->Ln(10); // Espacio después de los documentos

                    $pdf->Write(0, $comentario);
                    $pdf->Ln(20); // Espacio después de los documentos
                }
            }

            // Incrementar el número del estado y la posición vertical
            $stateNumber++;
            $pdf->Ln(10); // Ajustar el espacio vertical entre los estados
            if ($pdf->getPage() > $currentPage) {
                // Insertar marca de agua
                $pdf->SetAlpha(0.3); // Establece la opacidad al 10%
                $pdf->Image('vendor/adminlte/dist/img/icono camara.png', 10, 50, 190); // Ajusta la posición y tamaño de la imagen
                $pdf->SetAlpha(1); // Restablece la opacidad al 100%
            }
        }

        // Salida del PDF
        $pdf->Output('maparecorrido.pdf', 'I');
    }

    public function generarReporte(Request $request)
    {
        // Obtener las fechas desde y hasta del request
        $fechaInicio = $request->input('desde');  // "2024-12-04"
        $fechaFin = $request->input('hasta');     // "2024-12-04"

        // Obtener los datos desde la base de datos
        $documentos = MesaEntrada::whereBetween('fecha_recepcion', [$fechaInicio, $fechaFin])
            ->with('tipoDoc') // Obtener también el tipo de documento relacionado
            ->get();

        // Preprocesar los datos para agrupar por tipo de documento y contar las cantidades
        $datos = $documentos->groupBy('id_tipo_doc')->map(function ($items) {
            return [
                'codigo' => $items->first()->id_tipo_doc,
                'tipo_documento' => $items->first()->tipoDoc->nombre, // Obtener el nombre del tipo de documento
                'cantidad' => $items->count(), // Contar cuántos documentos del mismo tipo
            ];
        });

        // Crear instancia de TCPDF
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

        // Configuración del documento
        $pdf->SetPrintHeader(false); // Deshabilitar encabezado
        $pdf->SetFont('Times', '', 12);
        $pdf->AddPage();
        $pdf->SetAlpha(0.3); // Establece la opacidad al 10%
        $pdf->Image('vendor/adminlte/dist/img/icono camara.png', 10, 50, 190); // Ajusta la posición y tamaño de la imagen
        $pdf->SetAlpha(1); // Restablece la opacidad al 100%
        // Título del reporte
        $pdf->SetFont('Times', 'B', 14);
        $pdf->Cell(0, 10, 'Planilla de documentos ingresados', 0, 1, 'C');
        $pdf->Ln(5);

        // Fechas
        $pdf->SetFont('Times', '', 12);
        $pdf->Cell(45, 10, 'Desde: ' . $fechaInicio, 0, 0, 'L');
        $pdf->Cell(45, 10, 'Hasta: ' . $fechaFin, 0, 1, 'L');

        // Agregar tabla de datos
        $pdf->Ln(10);
        $pdf->SetFont('Times', 'B', 12);

        // Cabecera de la tabla
        $pdf->Cell(30, 10, 'Código', 1, 0, 'C');
        $pdf->Cell(100, 10, 'Tipo de documento', 1, 0, 'C');
        $pdf->Cell(30, 10, 'Cantidad', 1, 1, 'C');

        // Guardar la página actual
        $currentPage = $pdf->getPage();

        // Datos de la tabla
        $pdf->SetFont('Times', '', 12);
        $totalCantidad = 0; // Inicializar el total de documentos

        foreach ($datos as $row) {
            // Verificar si el número de página ha cambiado
            if ($pdf->getPage() > $currentPage) {
                // Insertar marca de agua cuando cambie de página
                $pdf->SetAlpha(0.3); // Establecer la opacidad al 30%
                $pdf->Image('vendor/adminlte/dist/img/icono camara.png', 10, 50, 190); // Ajusta la posición y tamaño de la imagen
                $pdf->SetAlpha(1); // Restablecer la opacidad al 100%

                // Actualizar la página actual
                $currentPage = $pdf->getPage();
            }

            // Imprimir los datos de cada fila
            $pdf->Cell(30, 10, $row['codigo'], 1, 0, 'C');
            $pdf->Cell(100, 10, $row['tipo_documento'], 1, 0, 'C');
            $pdf->Cell(30, 10, $row['cantidad'], 1, 1, 'C');

            // Sumar la cantidad de documentos
            $totalCantidad += $row['cantidad'];
        }

        // Agregar el total en la parte inferior de la tabla
        $pdf->Ln(5);
        $pdf->SetFont('Times', 'B', 12);
        $pdf->Cell(130, 10, 'Total de documentos', 1, 0, 'C');
        $pdf->Cell(30, 10, $totalCantidad, 1, 1, 'C');

        // Salida del PDF
        $pdf->Output('planilla_documentos.pdf', 'I');
    }





    public function documentos($id)
    {
        $mesaEntrada = MesaEntrada::with('documentos')->findOrFail($id);
        return response()->json($mesaEntrada->documentos);
    }
    public function firmantes($id)
    {
        $firmantes = MesaEntradaFirmante::where('id_mentrada', $id)
            ->with('firmante') // Cargar la relación firmante
            ->get();
        return response()->json($firmantes);
    }
    public function verificarDuplicado(Request $request)
    {
        $duplicado = $request->input('duplicado');
        if (empty($duplicado)) {
            return response()->json(['duplicado' => false]);
        } else {
            $numeroDuplicado = DB::table('mesa_entrada')
                ->where('duplicado', 'like', '%' . $duplicado . '%')
                ->count();

            if ($numeroDuplicado > 0) {
                return response()->json([
                    'duplicado' => true,
                    'numero_duplicado' => $numeroDuplicado
                ]);
            }
        }
        return response()->json(['duplicado' => false]);

        // Buscar cualquier número en la observación
        preg_match_all('/\d+/', $observacion, $matches);
        $numeroDuplicado = 0;

        // return response()->json([
        //     'duplicado' => true,
        //     'numero_duplicado' => DB::table('mesa_entrada')
        //         ->whereIn('observacion', $matches[0])
        //         ->toSql()
        // ]);
        // No se encontraron duplicados
        //return response()->json(['duplicado' => false]);
    }
    public function getData(Request $request)
    {
        // Iniciar la consulta base
        $query = MesaEntrada::with(['documentos', 'firmantes', 'origen', 'tipoDoc', 'destino', 'user']);

        // Filtrar y buscar en toda la base de datos
        if ($request->has('search.value') && $request->input('search.value') !== '') {
            $search = $request->input('search.value');
            $query->where(function ($q) use ($search) {
                $q->where('nro_mentrada', 'like', "%{$search}%")
                    ->orWhere('observacion', 'like', "%{$search}%")
                    ->orWhereHas('origen', function ($query) use ($search) {
                        $query->where('nombre', 'like', "%{$search}%");
                    })
                    ->orWhereHas('destino', function ($query) use ($search) {
                        $query->where('nombre', 'like', "%{$search}%");
                    })
                    ->orWhereHas('tipoDoc', function ($query) use ($search) {
                        $query->where('nombre', 'like', "%{$search}%");
                    })
                    ->orWhereHas('firmantes', function ($query) use ($search) {
                        $query->where('nombre', 'like', "%{$search}%");
                    });
            });
        }

        // Ordenar por anho y nro_mentrada en orden descendente
        $query->orderBy('anho', 'desc')->orderBy('nro_mentrada', 'desc');

        // Contar registros antes de paginar
        $totalData = $query->count();

        // Paginación
        $query->offset($request->input('start'))
            ->limit($request->input('length'));

        // Obtener datos paginados
        $mesasEntrada = $query->get();

        // Formatear datos para DataTables
        $data = [];
        foreach ($mesasEntrada as $row) {
            $actions = '<a href="' . route('reporte.recorrido', $row->id) . '" target="_blank" class="btn btn-sm btn-outline-secondary">
                <i class="fa fa-file-pdf"></i>
            </a>';

            if ($row->estado == 1) {
                $actions .= '<a href="' . route('mesaentrada.edit', $row->id) . '" class="btn btn-sm btn-outline-secondary">
                    <i class="fa fa-sm fa-fw fa-pen"></i>
                </a>
                <form action="' . route('mesaentrada.destroy', $row->id) . '" method="post" class="d-inline delete-form">
                    ' . csrf_field() . '
                    ' . method_field('DELETE') . '
                    <button type="submit" class="btn btn-sm btn-outline-secondary delete-button">
                        <ion-icon name="trash-outline"><i class="fa fa-sm fa-fw fa-trash"></i></ion-icon>
                    </button>
                </form>';
            }

            $data[] = [
                'nro_mentrada' => $row->nro_mentrada . ($row->nro_suplementario !== null ? '.' . $row->nro_suplementario : ''),
                'anho' => $row->anho,
                'fecha_recepcion' => $row->fecha_recepcion,
                'origen' => $row->origen->nombre ?? 'N/A',
                'tipo_doc' => $row->tipoDoc->nombre ?? 'N/A',
                'firmantes' => $row->firmantes->isNotEmpty() ? $row->firmantes->pluck('nombre')->join(', ') : 'N/A',
                'destino' => $row->destino->nombre ?? 'N/A',
                'observacion' => $row->observacion,
                'estado' => $row->estado == '1' ? 'Recepcionado' : 'Enviado',
                'usuario' => $row->user->name ?? 'N/A',
                'acciones' => $actions,
            ];
        }

        // Respuesta JSON
        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalData,
            'data' => $data,
        ]);
    }
    public function getDatareenv(Request $request)
    {
        $userId = auth()->id();
        $userDestino = UserDestino::where('user_id', $userId)->first();
        $iddest = $userDestino->destino_id;

        $query = MesaEntrada::join('mapa_recorrido', 'mesa_entrada.id', '=', 'mapa_recorrido.id_mentrada')
            ->leftJoin('mesa_entrada_firmante as mef', 'mesa_entrada.id', '=', 'mef.id_mentrada')
            ->leftJoin('firmantes as f', 'mef.id_firmante', '=', 'f.id')
            ->where('mapa_recorrido.estado', '!=', 0)
            ->where('mapa_recorrido.id_actual', $iddest)
            ->select(
                'mesa_entrada.id',
                'mesa_entrada.nro_mentrada',
                'mesa_entrada.anho',
                'mesa_entrada.fecha_recepcion',
                'mesa_entrada.id_origen',
                'mesa_entrada.id_tipo_doc',
                'mesa_entrada.id_destino',
                'mesa_entrada.observacion',
                'mesa_entrada.estado',
                'mesa_entrada.id_usuario',
                'mesa_entrada.created_at',
                'mesa_entrada.updated_at',
                'mapa_recorrido.estado as estado_recorrido',
                'mapa_recorrido.created_at as fecha_creacion_recorrido',
                DB::raw('GROUP_CONCAT(f.nombre SEPARATOR ", ") as nombres_firmantes')
            )
            ->groupBy(
                'mesa_entrada.id',
                'mesa_entrada.nro_mentrada',
                'mesa_entrada.anho',
                'mesa_entrada.fecha_recepcion',
                'mesa_entrada.id_origen',
                'mesa_entrada.id_tipo_doc',
                'mesa_entrada.id_destino',
                'mesa_entrada.observacion',
                'mesa_entrada.estado',
                'mesa_entrada.id_usuario',
                'mesa_entrada.created_at',
                'mesa_entrada.updated_at',
                'mapa_recorrido.estado',
                'mapa_recorrido.created_at'
            );

        // Filtro de búsqueda
        if ($request->has('search.value') && $request->input('search.value') !== '') {
            $search = $request->input('search.value');
            $query->where(function ($q) use ($search) {
                $q->where('mesa_entrada.nro_mentrada', 'like', "%{$search}%")
                    ->orWhere('mesa_entrada.observacion', 'like', "%{$search}%");
            });
        }

        // Contar total de registros
        $totalData = $query->count();
        $totalFiltered = $totalData;

        // Paginación
        $query->offset($request->input('start'))
            ->limit($request->input('length'));

        // Obtener datos
        $mesasEntrada = $query->get();

        // Formatear datos para DataTables
        $data = $mesasEntrada->map(function ($row) {
            $acciones = '<a href="' . route('reporte.recorrido', $row->id) . '" target="_blank" class="btn btn-sm btn-outline-secondary">
                        <i class="fa fa-file-pdf"></i>
                    </a>';

            if ($row->tiene_documentos) {
                $acciones .= '<button type="button" class="btn btn-sm btn-outline-secondary" onclick="openDocumentosModal(' . $row->id . ')">
                            <i class="fa fa-sm fa-fw fa-print"></i>
                        </button>';
            }

            return [
                'nro_mentrada' => $row->nro_mentrada,
                'anho' => $row->anho,
                'fecha_recepcion' => $row->fecha_recepcion,
                'origen' => $row->origen->nombre ?? 'N/A',
                'tipo_doc' => $row->tipoDoc->nombre ?? 'N/A',
                'firmantes' => $row->nombres_firmantes ?? 'N/A',
                'observacion' => $row->observacion,
                'estado' => $row->estado_recorrido == '2' ? 'Enviado' : 'Recepcionado',
                'usuario' => $row->user->name ?? 'N/A',
                'acciones' => $acciones,
                'fecha_actualizacion' => $row->fecha_creacion_recorrido,
            ];
        });

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ]);
    }
}
