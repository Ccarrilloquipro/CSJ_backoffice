<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCobradoresRequest;
use App\Http\Requests\UpdateCobradoresRequest;
use App\Models\Cobradores;
use Illuminate\Http\Request;
use LDAP\Result;

class CobradoresController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$cobradores = Cobradores::all();
		return view('cobradores.lista')->with(['arrCobradores' => $cobradores]);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		// validar autorizacion
		//if(auth()->user()->idTipoUsuario==3) return response(['message' => 'No autorizado para agregar'], 401);

		//		$request->validate([
		//			'idCategoria' => 'required|exists:catCobradores,id',
		//			'nombre' => 'required',
		//			'descripcion' => 'required',
		//		]);

		$registro = Cobradores::create($request->all());
		return \response($registro);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		$registro = Cobradores::findOrFail($id);
		return \response($registro);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
		// validar autorizacion
		//if(auth()->user()->idTipoUsuario==3) return response(['message' => 'No autorizado para editar'], 401);

		Cobradores::findOrFail($id)
			->update($request->all());
		$registro = Cobradores::findOrFail($id);
		return \response($registro);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		// validar autorizacion
		//if(auth()->user()->idTipoUsuario==3) return response(['message' => 'No autorizado para borrar'], 401);

		Cobradores::destroy($id);
		return response(['message' => 'Se borro el registro'], 200);
	}

	/**
	 * web
	 */

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		return response(['message' => 'Función no habilitada'], 400);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Request $request, $id)
	{
		return response(['message' => 'Función no habilitada'], 400);
	}

	// jom
	public function ficha($id){
		$cobrador = Cobradores::find($id);
		return view('cobradores.ficha')->with(['cobrador' => $cobrador]);
	}

	public function cobradorUpdate(Request $request)
	{

	}

	public function nuevo()
	{
		$cobrador = new Cobradores;
		return view('cobradores.ficha')->with(['cobrador' => $cobrador]);
	}
}
