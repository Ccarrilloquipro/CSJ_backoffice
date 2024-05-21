<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
		if(auth()->user()->activo !=1){
			return view('inactivo');
		}
		$sql = "select count(pagos.id) as cuantos, 0 as monto,pagos.idCobrador,
		concat(cobradores.nombre,' ',cobradores.paterno,' ',cobradores.materno) as cobrador
		from pagos
		left join cobradores on cobradores.idPersona=pagos.idCobrador
		where  pagos.enExcel = 0
		group by pagos.idCobrador,cobrador";
			$arrCobradores = DB::connection('mysql')->select($sql);

			$sql = "select sum(montoCobradoEnVisita) as importe,idCobrador from pagos where  pagos.enExcel = 0 group by idCobrador";
			$montos = DB::connection('mysql')->select($sql);
			foreach ($arrCobradores as $cobrador){
				foreach ($montos as $monto){
					if($cobrador->idCobrador == $monto->idCobrador) $cobrador->monto = $monto->importe;
				}
			}
			return view('home', compact('arrCobradores'));
		}
}
