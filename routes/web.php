<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CobradoresController;

use App\Http\Controllers\PagosController;
use App\Http\Controllers\ArchivosExportacionController;
use App\Http\Controllers\UserController;
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

//Route::get('/', function () {
//    return view('welcome');
//});

Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {
    Route::view('about', 'about')->name('about');

	//administradores
    Route::get('users', [UserController::class, 'index'])->name('users.index');
	Route::get('administrador/nuevo', [UserController::class, 'nuevo'])->name('administrador.nuevo');
	Route::get('administrador/ficha/{id}', [UserController::class, 'ficha'])->name('administrador.ficha');
	Route::post('administrador/grabar', [UserController::class, 'grabar'])->name('administrador.grabar');

    Route::get('profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::put('profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

	// cobrador
	Route::get('cobrador', [CobradoresController::class, 'index'])->name('cobrador.index');
	Route::get('cobrador/ficha/{id}', [CobradoresController::class, 'ficha'])->name('cobrador.ficha');
	Route::get('cobrador/nuevo', [CobradoresController::class, 'nuevo'])->name('cobrador.nuevo');
	Route::post('cobrador/grabar', [CobradoresController::class, 'grabar'])->name('cobrador.grabar');

	// pagos
	Route::get('pagos', [PagosController::class, 'index'])->name('pagos.index');
	Route::get('pagos/ficha/{id}', [PagosController::class, 'ficha'])->name('pago.ficha');
	Route::post('pago/updateProximaFecha', [PagosController::class, 'cambiarProximaFecha'])->name('pago.updateProximaFecha');
	Route::get('pagos/marcarTodos', [PagosController::class, 'marcarTodos'])->name('pago.marcarTodos');
	Route::get('pagos/quitarTodos', [PagosController::class, 'quitarTodos'])->name('pago.quitarTodos');
	Route::post('pago/submitLista', [PagosController::class, 'submitLista'])->name('pago.submitLista');
	Route::get('pago/regresarLista', [PagosController::class, 'regresarLista'])->name('pago.regresarLista');
	Route::post('pago/filtrarLista', [PagosController::class, 'filtrarLista'])->name('pago.filtrarLista');
	Route::get('cuentas', [PagosController::class, 'pantallaCuentas'])->name('pago.buscarCuenta');
	Route::post('cuentas/filtrar', [PagosController::class, 'filtrarCuentas'])->name('cuentas.filtrar');
	Route::post('cuentas/actualizarFecha', [PagosController::class, 'actualizarFecha'])->name('cuentas.actualizarFecha');

	// archivos
	Route::get('archivos', [ArchivosExportacionController::class, 'index'])->name('archivos.index');
	Route::get('archivos/detalle/{id}', [ArchivosExportacionController::class, 'detalle'])->name('archivos.detalle');
	Route::get('archivos/exportar/{nombreArchivo}', [ArchivosExportacionController::class, 'exportar'])->name('archivos.exportar');

	// borrar
	Route::post('pagos/getCobros', [PagosController::class, 'getCobros'])->name('pagos.getCobros');
	//Route::post('pagos/traerFoto', [PagosController::class, 'traerFoto'])->name('pagos.traerFoto');
	//Route::post('pagos/traerOrden', [PagosController::class, 'traerOrden'])->name('pagos.traerOrden');
	//Route::post('pagos/buscarCobros', [PagosController::class, 'buscarCobros'])->name('pagos.buscarCobros');
});


