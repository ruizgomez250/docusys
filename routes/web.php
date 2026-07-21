<?php

use App\Models\Legislador;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CargoController;
use App\Http\Controllers\OrigenController;
use App\Http\Controllers\DestinoController;
use App\Http\Controllers\EntidadController;
use App\Http\Controllers\TipoDocController;
use App\Http\Controllers\FirmanteController;

use App\Http\Controllers\ProfilesController;
use App\Http\Controllers\LegisladorController;
use App\Http\Controllers\MesaEntradaController;
use App\Http\Controllers\UserDestinoController;
use App\Http\Controllers\AutocompleteController;
use App\Http\Controllers\PartidoPoliticoController;
use App\Http\Controllers\PeriodoLegislativoController;
use App\Http\Controllers\ReporteController;

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::get('/home', function () {
    $totalDocumentos = \App\Models\MesaEntrada::count();
    $documentosHoy = \App\Models\MesaEntrada::whereDate('fecha_recepcion', \Carbon\Carbon::today())->count();
    $pendientes = \App\Models\MesaEntrada::whereHas('mapaRecorridos', function ($q) {
        $q->where('estado', 0);
    })->count();
    $finalizados = \App\Models\MesaEntrada::whereHas('mapaRecorridos', function ($q) {
        $q->where('estado', '!=', 0);
    })->count();

    return view('home', compact('totalDocumentos', 'documentosHoy', 'pendientes', 'finalizados'));
})->name('home')->middleware('auth');

Route::get('mesas-entrada/data1', [MesaEntradaController::class, 'getData'])->name('recepcionadoData');
//acceden los autenticados
Route::middleware('auth')->group(function () {
    Route::resource('entidades', EntidadController::class);
    Route::resource('cargos', CargoController::class);
    Route::resource('partidos', PartidoPoliticoController::class);
    Route::resource('periodos', PeriodoLegislativoController::class);
    Route::resource('legislador', LegisladorController::class);
    Route::resource('/firmante', FirmanteController::class);
    Route::resource('/reportes', ReporteController::class);
    Route::resource('/tipodoc', TipoDocController::class);
    Route::resource('/destino', DestinoController::class);
    Route::get('/destino/search', [DestinoController::class, 'search'])->name('destino.search');
    Route::post('/destino/store-ajax', [DestinoController::class, 'storeAjax'])->name('destino.store-ajax');
    Route::resource('/origen', OrigenController::class);
    Route::resource('mesaentrada', MesaEntradaController::class);
    Route::resource('usuariodestino', UserDestinoController::class);
    Route::get('/recepciondoc',  [MesaEntradaController::class, 'recepcionado'])->name('recepciondoc');
    Route::get('/reenviadodoc',  [MesaEntradaController::class, 'reenviado'])->name('reenviado');
    Route::get('/restipodocfechas',  [MesaEntradaController::class, 'reportetipodocfechas'])->name('restipodocfechas');
    Route::get('reporterecorrido/{row}', [MesaEntradaController::class, 'recorrido'])->name('reporte.recorrido');
    Route::get('/reporte/multiple', [ReporteController::class, 'generateMultipleReport'])
     ->name('reporte.multiple');
    Route::get('/autocomplete/firmante',  [AutocompleteController::class, 'getfirmante'])->name('obtenerfirmante');
Route::get('/autocomplete/observacion',  [AutocompleteController::class, 'getObservacion'])->name('obtenerobservacion');
    Route::post('mesaentrada/{id}/enviar', [MesaEntradaController::class, 'enviar'])->name('mesaentrada.enviar');
    Route::post('mesaentrada/{id}/aceptar', [MesaEntradaController::class, 'aceptar'])->name('mesaentrada.aceptar');
    Route::post('mesaentrada/{id}/finalizar', [MesaEntradaController::class, 'finalizar'])->name('mesaentrada.finalizar');
    Route::post('reenviardoc', [MesaEntradaController::class, 'reenviardoc'])->name('reenviardoc');
    Route::post('mesaentrada/{id}/redirigir', [MesaEntradaController::class, 'redirigir'])->name('mesaentrada.redirigir');
    Route::post('/mesaentrada/storedocs', [MesaEntradaController::class, 'storedocs'])->name('mesaentrada.storedocs');
    Route::get('/proyectos-en-estudio', [App\Http\Controllers\ProyectosEnEstudioController::class, 'index'])->name('proyectos-en-estudio.index');
    Route::get('/proyectos-en-estudio/data', [App\Http\Controllers\ProyectosEnEstudioController::class, 'recepcionadoData'])->name('proyectos-en-estudio.data');
    Route::get('/proyectos-en-estudio/listado', [App\Http\Controllers\ProyectosEnEstudioController::class, 'listado'])->name('proyectos-en-estudio.listado');
    Route::get('/proyectos-en-estudio/listado-data', [App\Http\Controllers\ProyectosEnEstudioController::class, 'listadoData'])->name('proyectos-en-estudio.listado-data');
    Route::get('/proyectos-en-estudio/crear/{id_mentrada}', [App\Http\Controllers\ProyectosEnEstudioController::class, 'create'])->name('proyectos-en-estudio.create');
    Route::get('/proyectos-en-estudio/configuracion', [App\Http\Controllers\ProyectosEnEstudioController::class, 'configuracion'])->name('proyectos-en-estudio.configuracion');
    Route::post('/proyectos-en-estudio/configuracion', [App\Http\Controllers\ProyectosEnEstudioController::class, 'updateConfiguracion'])->name('proyectos-en-estudio.update-config');
    Route::get('/proyectos-en-estudio/listado-pdf', [App\Http\Controllers\ProyectosEnEstudioController::class, 'exportListadoPDF'])->name('proyectos-en-estudio.listado-pdf');
    Route::get('/proyectos-en-estudio/listado-excel', [App\Http\Controllers\ProyectosEnEstudioController::class, 'exportListadoExcel'])->name('proyectos-en-estudio.listado-excel');
    Route::get('/proyectos-en-estudio/sesiones', [App\Http\Controllers\ProyectosEnEstudioController::class, 'listSesiones'])->name('proyectos-en-estudio.sesiones');
    Route::post('/proyectos-en-estudio/sesiones/generar-pdf', [App\Http\Controllers\ProyectosEnEstudioController::class, 'generarPDFSesion'])->name('proyectos-en-estudio.sesiones.generar-pdf');
    Route::post('/proyectos-en-estudio/sesiones/preparar', [App\Http\Controllers\ProyectosEnEstudioController::class, 'prepararSesion'])->name('proyectos-en-estudio.sesiones.preparar');
    Route::get('/proyectos-en-estudio/asuntos-entrados', [App\Http\Controllers\ProyectosEnEstudioController::class, 'listAsuntosEntrados'])->name('proyectos-en-estudio.asuntos-entrados');
    Route::get('/proyectos-en-estudio/asuntos-entrados/{id}/pdf', [App\Http\Controllers\ProyectosEnEstudioController::class, 'downloadPDFSesion'])->name('proyectos-en-estudio.asuntos-entrados.pdf');
    Route::get('/proyectos-en-estudio/asuntos-entrados/{id}/editar', [App\Http\Controllers\ProyectosEnEstudioController::class, 'editSesion'])->name('proyectos-en-estudio.edit-sesion');
    Route::put('/proyectos-en-estudio/asuntos-entrados/{id}', [App\Http\Controllers\ProyectosEnEstudioController::class, 'updateSesion'])->name('proyectos-en-estudio.update-sesion');
    Route::delete('/proyectos-en-estudio/sesiones/{id}', [App\Http\Controllers\ProyectosEnEstudioController::class, 'destroySesion'])->name('proyectos-en-estudio.destroy-sesion');
    Route::get('/proyectos-en-estudio/{id}/pdf', [App\Http\Controllers\ProyectosEnEstudioController::class, 'downloadPDF'])->name('proyectos-en-estudio.pdf');
    Route::get('/proyectos-en-estudio/{id}/word', [App\Http\Controllers\ProyectosEnEstudioController::class, 'downloadWord'])->name('proyectos-en-estudio.word');
    Route::get('/proyectos-en-estudio/{id}', [App\Http\Controllers\ProyectosEnEstudioController::class, 'show'])->name('proyectos-en-estudio.show');
    Route::put('/proyectos-en-estudio/{id}', [App\Http\Controllers\ProyectosEnEstudioController::class, 'update'])->name('proyectos-en-estudio.update');
    Route::delete('/proyectos-en-estudio/{id}', [App\Http\Controllers\ProyectosEnEstudioController::class, 'destroy'])->name('proyectos-en-estudio.destroy');
Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profiles', [ProfilesController::class, 'index'])->name('profiles');
    Route::resource('users', App\Http\Controllers\UserController::class);
    Route::resource('roles', App\Http\Controllers\RolesController::class);
    Route::get('roles/{role}/give-permissions', [App\Http\Controllers\RolesController::class, 'addPermissionToRole'])->name('roles.addpermissionrole');
    Route::put('roles/{role}/give-permissions', [App\Http\Controllers\RolesController::class, 'givePermissionToRole'])->name('roles.updatepermissionrole');
    Route::resource('permissions', App\Http\Controllers\PermissionController::class);
    Route::get('/mesaentrada/documentos/{id}', [MesaEntradaController::class, 'documentos'])->name('mesaentrada.documentos');
    Route::get('/createaux', [MesaEntradaController::class, 'aux'])->name('createaux');
    Route::post('/mesaentradastoreaux', [MesaEntradaController::class, 'storeaux'])->name('mesaentradastoreaux');
    Route::post('/mesaentrada/{id}/autorizar-modif', [MesaEntradaController::class, 'autorizarModif'])->name('mesaentrada.autorizarmodif');
    Route::get('/generar-planilla', [MesaEntradaController::class, 'generarReporte'])->name('generar-planilla');
    Route::get('/reportefechaspdf/{desde}/{hasta}/{idproducto?}/{tiporeporte}', [ReporteController::class, 'pdfreportes']);
    Route::get('/reportefechaspdfresumen/{desde}/{hasta}', [ReporteController::class, 'pdfreportesresumen']);

    
// Ruta AJAX para datos (devuelve JSON en formato DataTables)
Route::get('/mesa-entrada/recepcionado/data2', [MesaEntradaController::class, 'recepcionadoData'])->name('mesaentrada.recepcionado.data');
Route::get('/mesa-entrada/recepcionado/finalizadodata', [MesaEntradaController::class, 'finalizadoData'])->name('mesaentrada.finalizado.data');
});
Route::post('/verificar-duplicado', [MesaEntradaController::class, 'verificarDuplicado'])->name('verificar-duplicado');
Route::get('/mesaentrada/firmantes/{id}', [MesaEntradaController::class, 'firmantes'])->name('mesaentrada.firmantes');
Route::get('/sinpermiso', function () {
    return view('sinpermiso.index');
})->name('sinpermiso');



Route::get('mesas-entrada/data', [MesaEntradaController::class, 'getData'])->name('mesas-entrada.data');


Route::get('/mesaentrada/reenviados/data', [MesaEntradaController::class, 'getDataRen'])
    ->name('mesaentrada.reenviados.data');

Route::get('/autocomplete',  [AutocompleteController::class, 'autocomplete'])->name('autocomplete');
Route::get('/autocomplete/proveedor',  [AutocompleteController::class, 'proveedor'])->name('obtenerproveedor');
Route::get('/autocomplete/producto',  [AutocompleteController::class, 'getproducto'])->name('obtenerproducto');
Route::get('/autocomplete/acapite', [AutocompleteController::class, 'getAcapite'])->name('obteneracapite');
Route::get('/autocomplete/destino', [AutocompleteController::class, 'getDestino'])->name('obtenerdestino');
//Route::post('/guardar-categoria', 'CategoriaController@storeCat')->name('guardar-categoria');
Route::get('/create', function () {
    return view('create');
});
//Route::post('/guardar-categoria', [CrearCategoriaComponent::class, 'store'])->name('guardar-categoria');



//Route::get('/mascota', 'MascotaController@getRaza');
