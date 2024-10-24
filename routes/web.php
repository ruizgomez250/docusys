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

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::get('/home', function () {
    return view('home');
})->name('home')->middleware('auth');


//acceden los autenticados
Route::middleware('auth')->group(function () {
    Route::resource('entidades', EntidadController::class);
    Route::resource('cargos', CargoController::class);
    Route::resource('partidos', PartidoPoliticoController::class);
    Route::resource('periodos', PeriodoLegislativoController::class);
    Route::resource('legislador', LegisladorController::class);
    Route::resource('/firmante', FirmanteController::class);
    Route::resource('/tipodoc', TipoDocController::class);
    Route::resource('/destino', DestinoController::class);
    Route::resource('/origen', OrigenController::class);
    Route::resource('mesaentrada', MesaEntradaController::class);
    Route::resource('usuariodestino', UserDestinoController::class);
    Route::get('/recepciondoc',  [MesaEntradaController::class, 'recepcionado'])->name('recepciondoc');
    Route::get('/reenviadodoc',  [MesaEntradaController::class, 'reenviado'])->name('reenviado');
    Route::get('reporterecorrido/{row}', [MesaEntradaController::class, 'recorrido'])->name('reporte.recorrido');
    Route::get('/autocomplete/firmante',  [AutocompleteController::class, 'getfirmante'])->name('obtenerfirmante');
    Route::post('mesaentrada/{id}/enviar', [MesaEntradaController::class, 'enviar'])->name('mesaentrada.enviar');
    Route::post('mesaentrada/{id}/aceptar', [MesaEntradaController::class, 'aceptar'])->name('mesaentrada.aceptar');
    Route::post('mesaentrada/{id}/finalizar', [MesaEntradaController::class, 'finalizar'])->name('mesaentrada.finalizar');
    Route::post('reenviardoc', [MesaEntradaController::class, 'reenviardoc'])->name('reenviardoc');
    Route::post('mesaentrada/{id}/redirigir', [MesaEntradaController::class, 'redirigir'])->name('mesaentrada.redirigir');
    Route::post('/mesaentrada/storedocs', [MesaEntradaController::class, 'storedocs'])->name('mesaentrada.storedocs');
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
});
Route::post('/verificar-duplicado', [MesaEntradaController::class, 'verificarDuplicado'])->name('verificar-duplicado');
Route::get('/mesaentrada/firmantes/{id}', [MesaEntradaController::class, 'firmantes'])->name('mesaentrada.firmantes');
Route::get('/sinpermiso', function () {
    return view('sinpermiso.index');
})->name('sinpermiso');



Route::get('mesas-entrada/data', [MesaEntradaController::class, 'getData'])->name('mesas-entrada.data');

Route::get('/autocomplete',  [AutocompleteController::class, 'autocomplete'])->name('autocomplete');
Route::get('/autocomplete/proveedor',  [AutocompleteController::class, 'proveedor'])->name('obtenerproveedor');
Route::get('/autocomplete/producto',  [AutocompleteController::class, 'getproducto'])->name('obtenerproducto');
//Route::post('/guardar-categoria', 'CategoriaController@storeCat')->name('guardar-categoria');
Route::get('/create', function () {
    return view('create');
});
//Route::post('/guardar-categoria', [CrearCategoriaComponent::class, 'store'])->name('guardar-categoria');



//Route::get('/mascota', 'MascotaController@getRaza');
