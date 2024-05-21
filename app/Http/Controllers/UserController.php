<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
		if(auth()->user()->activo !=1){
			return view('inactivo');
		}
		$sql = "select id, name , email ,usuario,activo from users where idTipoUsuario=1";
		$administradores = DB::connection('mysql')->select($sql);
        //$administradores = User::where('idTipoUsuario','=',1)->get();
        return view('users.lista', compact('administradores'));
    }

	public function nuevo()
	{
		$administrador = new User();
		return view('users.ficha', compact('administrador'));
	}

	public function ficha($id)
	{
		$sql = "select id, name , email ,usuario,activo from users where id=$id";
		$resultado = DB::connection('mysql')->select($sql);
		$administrador = $resultado[0];
		return view('users.ficha', compact('administrador'));
	}

	public function grabar(Request $request)
	{
		$id = $request->id;
		if ($id == null){
			$this->agregarAdministrador($request);
		}else{
			$this->editarAdministrador($request);
		}
		$sql = "select id, name , email ,usuario,activo from users where idTipoUsuario=1";
		$administradores = DB::connection('mysql')->select($sql);
		//$administradores = User::where('idTipoUsuario','=',1)->get();
		return view('users.lista', compact('administradores'));
	}

	private function agregarAdministrador(Request $request)
	{
		$fields = $request->validate([
			'name' => 'required|string',
			'email' => 'required|email|unique:users,email',
			'usuario' => 'required|string|unique:users,usuario',
			'idTipoUsuario' => 'required|int',
			'password' => 'required|string'
		],
		[
			'name.required' => 'El nombre esta vacio.',
			'email.required' => 'El correo esta vacio.',
			'email.unique' => 'El correo ya se uso.',
			'email.email' => 'El correo no es un correo valido.',
			'usuario.required' => 'El usuario esta vacio.',
			'usuario.unique' => 'El nombre de usuario ya se uso.',
			'password.required' => 'La clave esta vacia.'
		]);

		$user = User::create([
			'name' => $fields['name'],
			'email' => $fields['email'],
			'usuario' => $fields['usuario'],
			'idTipoUsuario' => $fields['idTipoUsuario'],
			'password' => bcrypt($fields['password'])
		]);
	}

	private function editarAdministrador(Request $request)
	{
		$fields = $request->validate([
			'name' => 'required|string',
			'email' => 'required|string',
			'usuario' => 'required|string',
		],
		[
			'name.required' => 'El nombre esta vacio.',
			'email.required' => 'El correo esta vacio.',
			'email.unique' => 'El correo ya se uso.',
			'email.email' => 'El correo no es un correo valido.',
			'usuario.required' => 'El usuario esta vacio.',
			'usuario.unique' => 'El nombre de usuario ya se uso.'
		]);

		$sql = "update users set 
                 name = '".$request->name."',
                 email = '".$request->email."',
                 usuario = '".$request->usuario."',
                 activo = '".$request->activo."'
                 where id = ".$request->id;
		DB::connection('mysql')->update($sql);
	}
}
