<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCobradoresRequest;
use App\Http\Requests\UpdateCobradoresRequest;
use App\Models\Cobradores;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
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
		if(auth()->user()->activo !=1){
			return view('inactivo');
		}
		$this->inicializarDatos();
		$menuIdCobradores = session()->get('menuIdCobradores');
		$cobradores = Cobradores::all();
		return view('cobradores.lista')->with(['cobradores' => $cobradores]);
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
	private function inicializarDatos()
	{
		$sql = "select ID_PERSONA as id, concat(NOMBRE,' ',AP_PATERNO,' ',AP_MATERNO) as nombre from core_persona where ID_TIPO_PERSONA = 10";
		$menuIdCobradoresTmp = DB::connection('remoto')->select($sql);
		$menuIdCobradores=array();
		foreach ($menuIdCobradoresTmp as $item){
			$menuIdCobradores[] =  array('id'=>$item->id,'nombre'=>$item->nombre);
		}
		session()->put('menuIdCobradores', $menuIdCobradores);


	/*	if (!session()->has('menuEnArchivo')) {
			$menuEnArchivo=array();
			$menuEnArchivo[] =  array('id'=>'seleccione','nombre'=>"Seleccione");
			$menuEnArchivo[] =  array('id'=>'si','nombre'=>'Si');
			$menuEnArchivo[] =  array('id'=>'no','nombre'=>'No');
			session()->put('menuEnArchivo', $menuEnArchivo);
		}*/
		/*if (!session()->has('arrFiltros')) {
			$arrFiltros = array(
				'fechaInicial' => '',
				'fechaFinal' => '',
				'cliente' => '',
				'archivo' => '',
				'enExcel' => 'seleccione',
				'cobrador' => 'seleccione'
			);
			session()->put('arrFiltros', $arrFiltros);
		}*/
	}

	public function nuevo()
	{
		$menuIdCobradores = session()->get('menuIdCobradores');
		//$cobrador = new Cobradores;
		$formularioCobrador = array(
			'id'=>'nuevo',
			'nombre'=>'',
			'paterno'=>'',
			'materno'=>'',
			'email'=>'',
			'idPersona'=>'',
			'usuario'=>'',
			'clave'=>'',
		);
		session()->put('formularioCobrador',$formularioCobrador);
		return view('cobradores.alta')->with(['menuIdCobradores'=>$menuIdCobradores]);
	}

	public function ficha($id){
		$menuIdCobradores = session()->get('menuIdCobradores');
		//$cobrador = Cobradores::find($id);
		$sql = "select cobradores.*, users.idTipoUsuario,users.email,users.usuario
			from cobradores left join users on users.id = cobradores.idUser where cobradores.id=$id";
		$cobradorTmp = DB::connection('mysql')->select($sql);
		$cobrador = $cobradorTmp[0];
		return view('cobradores.ficha')->with(['cobrador' => $cobrador,'menuIdCobradores'=>$menuIdCobradores]);
	}

	public function grabar(Request $request)
	{
		$id = $request->id;
		if ($id == 'nuevo'){
			$this->agregarCobrador($request);
		}else{
			$this->editarCobrador($request);
		}
		$cobradores = Cobradores::all();
		return view('cobradores.lista')->with(['cobradores' => $cobradores]);
	}

	public function messages()
	{
		return [
			'nombres.required' => 'El nombre es requerido',
			'paterno.required' => 'El apellido paterno es requerido',
			'email.required' => 'El correo es requerido',
			'email.unique' => 'El correo indicado ya existe en los registros de usuario',
			'idPersona.required' => 'El id del cobrador en el sistema San Juan es requerido',
			'usuario.required' => 'El usuario es requerido',
			'password.required' => 'El la clave es requerida',
		];
	}

	private function agregarCobrador(Request $request)
	{
		$formularioCobrador = session()->get('formularioCobrador');
		$formularioCobrador['nombre'] = $request->nombres;
		$formularioCobrador['paterno'] = $request->paterno;
		$formularioCobrador['materno'] = $request->materno;
		$formularioCobrador['email'] = $request->email;
		$formularioCobrador['idPersona'] = $request->idPersona;
		$formularioCobrador['usuario'] = $request->usuario;
		$formularioCobrador['clave'] = $request->password;
		session()->put('formularioCobrador',$formularioCobrador);
		$cobrador = session()->get('formularioCobrador');
		$fields = $request->validate([
			'nombres' => 'required|string',
			'paterno' => 'required|string',
			'email' => 'required|email|unique:users,email',
			'idPersona' => 'required|int',
			'usuario' => 'required|string|unique:users,usuario',
			'password' => 'required|string',
		],
		[
			'nombres.required' => 'El nombre esta vacio.',
			'paterno.required' => 'El apellido paterno esta vacio.',
			'email.required' => 'El correo esta vacio.',
			'email.unique' => 'El correo ya se uso.',
			'email.email' => 'El correo no es un correo valido.',
			'idPersona.required' => 'El id del vendedor es necesario.',
			'usuario.required' => 'El usuario esta vacio.',
			'usuario.unique' => 'El nombre de usuario ya se uso.',
			'password.required' => 'La clave esta vacia.',
		]


		);
		$nombreCompleto = $request->nombres." ".$request->paterno." ".$request->materno;

		$sql = "insert into users set 
                 name = '".$nombreCompleto."',
                 email = '".$request->email."',
                 idTipoUsuario = '".$request->idTipoUsuario."',
                 usuario = '".$request->usuario."',
                 password = '".bcrypt($request['password'])."',
                 activo = '1'";
		DB::connection('mysql')->insert($sql);
		$idInsertado = DB::getPdo()->lastInsertId();

		$sql = "insert into cobradores set 
                 idUser= '".$idInsertado."',
                 nombre = '".$request->nombres."',
                 paterno = '".$request->paterno."',
                 materno = '".$request->materno."',
                 idPersona = '".$request->idPersona."',
                 activo = '1'";
		DB::connection('mysql')->insert($sql);

		return back()->with('success', 'El cobrador se agregó correctamente.');
	}

	private function editarCobrador(Request $request)
	{
		$fields = $request->validate([
			'nombres' => 'required|string',
			'paterno' => 'required|string',
			'idPersona' => 'required|int',
			'usuario' => 'required|string'
		],
		[
			'nombres.required' => 'El nombre esta vacio.',
			'paterno.required' => 'El apellido paterno esta vacio.',
			'idPersona.required' => 'El id del vendedor es necesario.',
			'usuario.required' => 'El usuario esta vacio.',
			'password.required' => 'La clave esta vacia.',
		]
		);
		$nombreCompleto = $request->nombres." ".$request->paterno." ".$request->materno;
		$textoPass = (!empty($request->password)) ? "password = '".bcrypt($request['password'])."'," :  "";

		$sql = "update users set 
                 name = '".$nombreCompleto."',
                 email = '".$request->email."',
                 usuario = '".$request->usuario."',
                 $textoPass
                 activo = '".$request->activo."'
                 where id = ".$request->idUser;
		DB::connection('mysql')->update($sql);

		$sql = "update cobradores set 
                 idUser= '".$request->idUser."',
                 nombre = '".$request->nombres."',
                 paterno = '".$request->paterno."',
                 materno = '".$request->materno."',
                 idPersona = '".$request->idPersona."',
                 activo = '".$request->activo."'
                 where id = ".$request->id;
		DB::connection('mysql')->update($sql);
		return back()->with('success', 'El cobrador se actualizó correctamente.');
	}
}
