<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CobradoresController;

use App\Http\Controllers\PagosController;

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

    Route::get('users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');

    Route::get('profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::put('profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

	// clientes
	Route::get('cobrador', [CobradoresController::class, 'index'])->name('cobrador.index');
	Route::get('cobrador/alta', [CobradoresController::class, 'alta'])->name('cobrador.alta');


});
Route::post('cobrador/update', [CobradoresController::class, 'cobradorUpdate'])->name('cobrador.update');
Route::post('cobrador/grabar', [CobradoresController::class, 'grabarNuevo'])->name('cobrador.grabar');
Route::get('cobrador/ficha/{id}', [CobradoresController::class, 'ficha'])->name('cobrador.ficha');
Route::get('pagos', [PagosController::class, 'index'])->name('pagos.index');
Route::get('pagos/ficha/{id}', [PagosController::class, 'ficha'])->name('pago.ficha');
//Route::post('pagos/ficha/{id}', [PagosController::class, 'ficha'])->name('pago.ficha');
Route::post('pago/updateProximaFecha', [PagosController::class, 'cambiarProximaFecha'])->name('pago.updateProximaFecha');
Route::get('pagos/marcarTodos', [PagosController::class, 'marcarTodos'])->name('pago.marcarTodos');
Route::get('pagos/quitarTodos', [PagosController::class, 'quitarTodos'])->name('pago.quitarTodos');
Route::post('pago/submitLista', [PagosController::class, 'submitLista'])->name('pago.submitLista');
Route::get('pago/regresarLista', [PagosController::class, 'regresarLista'])->name('pago.regresarLista');



