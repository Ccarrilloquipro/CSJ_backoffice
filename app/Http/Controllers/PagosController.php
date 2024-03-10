<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePagosRequest;
use App\Http\Requests\UpdatePagosRequest;
use App\Models\Pagos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;



class PagosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
//		session()->forget('key');
//		if (session()->has('key')) {
//			// Key exists in the session
//		}

		$this->inicializarDatos();
		$arrFiltros = session()->get('arrFiltros');
		$this->pagosLista();

		$pagos = session()->get('pagos');
		$menuCobradores = session()->get('menuCobradores');
		$menuEnArchivo = session()->get('menuEnArchivo');
		$arrFiltros = session()->get('arrFiltros');
		return view('pagos.lista')->with(['arrPagos' => $pagos, 'menuCobradores' => $menuCobradores, 'menuEnArchivo' => $menuEnArchivo, 'arrFiltros' => $arrFiltros]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePagosRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Pagos $pagos)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pagos $pagos)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePagosRequest $request, Pagos $pagos)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pagos $pagos)
    {
        //
    }

	// jom
	private function inicializarDatos()
	{
		if (!session()->has('menuEnArchivo')) {
			$menuEnArchivo=array();
			$menuEnArchivo[] =  array('id'=>'seleccione','nombre'=>"Seleccione");
			$menuEnArchivo[] =  array('id'=>'si','nombre'=>'Si');
			$menuEnArchivo[] =  array('id'=>'no','nombre'=>'No');
			session()->put('menuEnArchivo', $menuEnArchivo);
		}
		if (!session()->has('menuCobradores')) {
			$sql = "select idPersona as id, concat(nombre,' ',paterno,' ',materno) as nombre from cobradores ";
			$menuCobradoresTmp = DB::connection('mysql')->select($sql);
			$menuCobradores=array();
			$menuCobradores[] =  array('id'=>'seleccione','nombre'=>"Seleccione");
			foreach ($menuCobradoresTmp as $item){
				$menuCobradores[] =  array('id'=>$item->id,'nombre'=>$item->nombre);
			}
			session()->put('menuCobradores', $menuCobradores);
		}
		if (!session()->has('arrFiltros')) {
			$arrFiltros = array(
				'fechaInicial' => '',
				'fechaFinal' => '',
				'cliente' => '',
				'archivo' => '',
				'enExcel' => 'seleccione',
				'cobrador' => 'seleccione'
			);
			session()->put('arrFiltros', $arrFiltros);
		}
	}

	public function ficha($id){
		$pago = $this->traerDatosFicha($id);
		return view('pagos.ficha')->with(['pago' => $pago]);
	}

	public function cambiarProximaFecha(Request $request)
	{
		$datos = $request;
		$id = $request->id;
		$nuevaFecha = $request->nuevaFecha;
		$nuevaFechaMy = $this->hacerFechaMysql($request->nuevaFecha);
		$sql = "select idCuenta from pagos where id = $id";
		$resultado = DB::select($sql);
		$idCuenta = $resultado[0]->idCuenta;

		$sql1 = "update core_cuenta set FECHA_PROXIMO_PAGO = '".$nuevaFechaMy."' where ID_CUENTA =$idCuenta";
		DB::connection('remoto')->update($sql1);
		$sql2 = "update pagos set fechaSiguientePago = '".$nuevaFechaMy."' where id =$id";
		DB::connection('mysql')->update($sql2);
		$pago = $this->traerDatosFicha($request->id);
		return view('pagos.ficha')->with(['pago' => $pago]);
	}

	private function traerDatosFicha($id)
	{
		$pago = Pagos::find($id);
		$pago->fechaRegistroH = $this->formatoFechaH($pago->fechaRegistro);
		$pago->fechaSiguientePagoH = $this->formatoFechaH($pago->fechaSiguientePago);
		$sql = "select concat(nombre,' ',paterno,' ',materno) as nombreCobrador from cobradores where idPersona = ".$pago->idCobrador;
		$cobrador = DB::select($sql);
		$pago->nombreCobrador = $cobrador[0]->nombreCobrador;
		return($pago);
	}

	public function marcarTodos()
	{
		$pagos = Session::get('pagos');
		foreach ($pagos as $pago){
			$pago->marcado = 1;
		}
		session()->put('pagos', $pagos);


		$pagos = session()->get('pagos');
		$menuCobradores = session()->get('menuCobradores');
		$menuEnArchivo = session()->get('menuEnArchivo');
		$arrFiltros = session()->get('arrFiltros');
		return view('pagos.lista')->with(['arrPagos' => $pagos, 'menuCobradores' => $menuCobradores, 'menuEnArchivo' => $menuEnArchivo, 'arrFiltros' => $arrFiltros]);

	}

	public function quitarTodos()
	{
		$pagos = Session::get('pagos');
		foreach ($pagos as $pago){
			$pago->marcado = 0;
		}
		$pagos = session()->get('pagos');
		$menuCobradores = session()->get('menuCobradores');
		$menuEnArchivo = session()->get('menuEnArchivo');
		$arrFiltros = session()->get('arrFiltros');
		return view('pagos.lista')->with(['arrPagos' => $pagos, 'menuCobradores' => $menuCobradores, 'menuEnArchivo' => $menuEnArchivo, 'arrFiltros' => $arrFiltros]);
	}


	private function pagosLista($stringBusqueda = ''){
		if(!empty($stringBusqueda)) $stringBusqueda= " where ".$stringBusqueda;
		$sql = "select pagos.*,date_format(pagos.fechaSiguientePago,'%d-%m-%Y') as fechaSiguientePagoH,
       	date_format(pagos.fechaRegistro,'%d-%m-%Y') as fechaDePago, 
       	cobradores.nombre as nombreCobrador, cobradores.id as idCobradorLocal, 
       	archivosExportacion.archivo as archivo,
       	0 as marcado     
			from pagos 
			left join cobradores on cobradores.idPersona = pagos.idCobrador
			left join archivosExportacion on archivosExportacion.id = pagos.idArchivoExportacion
			$stringBusqueda";
		$pagos = DB::connection('mysql')->select($sql);
		//if (session()->has('pagos'))  session()->forget('pagos');
		session()->put('pagos', $pagos);
	}



	private function traerDatosLista($stringBusqueda = '')
	{
		if(!empty($stringBusqueda)) $stringBusqueda= " where ".$stringBusqueda;
		$sql = "select pagos.*,date_format(pagos.fechaSiguientePago,'%d-%m-%Y') as fechaSiguientePagoH,
       	date_format(pagos.fechaRegistro,'%d-%m-%Y') as fechaDePago, 
       	cobradores.nombre as nombreCobrador, cobradores.id as idCobradorLocal, 
       	archivosExportacion.archivo as archivo,
       	0 as marcado     
			from pagos 
			left join cobradores on cobradores.idPersona = pagos.idCobrador
			left join archivosExportacion on archivosExportacion.id = pagos.idArchivoExportacion
			$stringBusqueda";
		$pagos = DB::connection('mysql')->select($sql);
		$sql = "select id, concat(nombre,' ',paterno,' ',materno) as nombre from cobradores ";
		$menuCobradoresTmp = DB::connection('mysql')->select($sql);
		$menuCobradores=array();
		$menuCobradores[] =  array('id'=>'seleccione','nombre'=>"Seleccione");
		foreach ($menuCobradoresTmp as $item){
			$menuCobradores[] =  array('id'=>$item->id,'nombre'=>$item->nombre);
		}
		$menuEnArchivo=array();
		$menuEnArchivo[] =  array('id'=>'seleccione','nombre'=>"Seleccione");
		$menuEnArchivo[] =  array('id'=>'si','nombre'=>'Si');
		$menuEnArchivo[] =  array('id'=>'no','nombre'=>'No');

		$arrInfo = array('pagos'=>$pagos,'menuCobradores'=>$menuCobradores,'menuEnArchivo'=>$menuEnArchivo);
		return($arrInfo);
	}

	public function regresarLista()
	{
		$pagos = session()->get('pagos');
		$menuCobradores = session()->get('menuCobradores');
		$menuEnArchivo = session()->get('menuEnArchivo');
		$arrFiltros = session()->get('arrFiltros');
		return view('pagos.lista')->with(['arrPagos' => $pagos, 'menuCobradores' => $menuCobradores, 'menuEnArchivo' => $menuEnArchivo, 'arrFiltros' => $arrFiltros]);
	}

	public function submitLista(Request $request)
	{
		$datos = $request->toArray();
		$arrSeleccionados=array();
		$textoSeleccionados = '';
		foreach ($datos as $key=>$value){
			if(substr($key,0,5) == "chBx_"){
				$cachos = explode('_',$key);
				$arrSeleccionados[]=$cachos[1];
				if(!empty($textoSeleccionados)) $textoSeleccionados.=',';
				$textoSeleccionados.=$cachos[1];
			}
		}
		switch ($request->accion){
			case 'excel':
				if(!empty($arrSeleccionados)){
					$this->prepararExcel($textoSeleccionados);
				}
				break;
			default:
				$this->actualizarMarcados($arrSeleccionados);
				$id = $request->accion;
				$pago = $this->traerDatosFicha($id);
				return view('pagos.ficha')->with(['pago' => $pago]);
				break;
		}
	}

	private function actualizarMarcados($arrSeleccionados)
	{
		$pagos = session()->get('pagos');
		foreach ($pagos as $pago){
			if(in_array($pago->id,$arrSeleccionados)) {
				$pago->marcado == 1;
			}else{
				$pago->marcado == 0;
			}
		}
		//session()->forget('pagos');
		session()->put('pagos', $pagos);
	}

	private function prepararExcel($textoSeleccionados)
	{
		$sql = "select date_format(pagos.fechaRegistro,'%d-%m-%Y') as fechaDePago,  pagos.idCuenta,  pagos.montoCobradoEnVisita, pagos.cobrado as recibo   
			from pagos 
			left join cobradores on cobradores.idPersona = pagos.idCobrador
			where pagos.id in ($textoSeleccionados)";
		$resultado = DB::connection('mysql')->select($sql);
		$arrResultado = array();
		foreach ($resultado as $item){
			$arrResultado[]=array('fechaDePago'=>$item->fechaDePago,'cuenta'=>$item->idCuenta,'montoCobradoEnVisita'=>$item->montoCobradoEnVisita,'recibo'=>$item->recibo);
		}

		// hacer titulo
		$nombreUsuario = Auth::user()->name;
		$nombreNormalizado = $this->convertirAASCII($nombreUsuario);
		$hoy = date('d_m_Y_H_i_s');
		$fechaCreacion =  date('Y_m_d');
		$titulo = $nombreNormalizado.'_'.$hoy;
		$file = urldecode($titulo.'.xlsx');
		if(preg_match('/^[^.][-a-z0-9_.]+[a-z]$/i', $file)) {
			$this->hacerExcel($arrResultado,$titulo,$file);
			$filepath = Storage::path('tmp/' . $file);
			// grabar en tabla de exceles
			$idGenerador = Auth::user()->id;
			$sql = "insert into archivosExportacion set 
                        idGenerador = $idGenerador,
                        fechaCreacion = '".$fechaCreacion."',
                        archivo = '".$file."'";
			DB::connection('mysql')->insert($sql);
			$idInsertado = DB::getPdo()->lastInsertId();
			// update cada archivo con el nombre de el excel generado
			$sql1 = "update pagos set enExcel=1, idArchivoExportacion=$idInsertado where pagos.id in ($textoSeleccionados)";
			DB::connection('mysql')->insert($sql1);
			// hacer nueva lista para desplegar
			// download el excel generado
			$this->downloadExcel($filepath);
		}
	}

	private function hacerExcel($datos,$titulo,$file)
	{
		//object of the Spreadsheet class to create the excel data
		$spreadsheet = new Spreadsheet();
		$spreadsheet->getDefaultStyle()->getFont()->setName('Arial');
		$spreadsheet->getDefaultStyle()->getFont()->setSize(12);
		//add some data in excel cells
		$y = 1;
		$this->llenarExcel($spreadsheet,$datos,$titulo,$y);

		// ancho de todas las columnas
		foreach (range('A','E') as $col) {
			$spreadsheet->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
		}

		// generacion y grabado
		//$file = urldecode($titulo.'.xlsx');
		//if(preg_match('/^[^.][-a-z0-9_.]+[a-z]$/i', $file)) {
			$filepath = Storage::path('tmp/' . $file);
			//$filepath = "tmp/".$file;
			$writer = new Xlsx($spreadsheet);
			//$fxls ='excel-file_1.xlsx';
			$writer->save($filepath);

		//}

		// Download
//		if(file_exists($filepath)) {
//			header('Content-Description: File Transfer');
//			header('Content-Type: application/octet-stream');
//			header('Content-Disposition: attachment; filename="'.basename($filepath).'"');
//			header('Expires: 0');
//			header('Cache-Control: must-revalidate');
//			header('Pragma: public');
//			header('Content-Length: ' . filesize($filepath));
//			flush(); // Flush system output buffer
//			readfile($filepath);
//			//unlink($filepath);
//			$this->grabar
//			die();
//
//		} else {
//			$this->fx->dispararAlerta('No se pudo elaborar el reporte. Por favor vuelva a intentarlo.');
//			//http_response_code(404);
//			die();
//		}
	}

	private function downloadExcel($filepath)
	{
		// Download
		if(file_exists($filepath)) {
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="'.basename($filepath).'"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($filepath));
			flush(); // Flush system output buffer
			readfile($filepath);
			//unlink($filepath);
			die();
		}
	}

	private function llenarExcel($spreadsheet,$arreglo,$titulo,$y){

		$totalSeccion=0;
		// titulo de seccion
//		$spreadsheet->getActiveSheet()->getStyle('A' . $y . ':D' . $y)->getFont()
//			->setBold(true)
//			->setSize(16)
//			->getColor()
//			->setRGB('FFFFFF');
//
//		$spreadsheet
//			->getActiveSheet()
//			->getStyle('A' . $y . ':D' . $y)
//			->getFill()
//			->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
//			->getStartColor()
//			->setARGB('025023');
//		$spreadsheet->setActiveSheetIndex(0)
//			->setCellValue('A' . $y, $titulo);
//
//		$spreadsheet->getActiveSheet()->mergeCells('A' . $y . ':D' . $y); //, Worksheet::MERGE_CELL_CONTENT_HIDE

		// formatos cabecera
//		$y++;
		$spreadsheet->getActiveSheet()->getStyle('A' . $y . ':D' . $y)->getFont()
			->setBold(true)
			->setSize(14)
			->getColor()
			->setRGB('FFFFFF');

		$spreadsheet
			->getActiveSheet()
			->getStyle('A' . $y . ':D' . $y)
			->getFill()
			->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
			->getStartColor()
			->setARGB('7C9248');

		$spreadsheet->setActiveSheetIndex(0)
			->setCellValue('A' . $y, '# de recibo')
			->setCellValue('B' . $y, '# cuenta')
			->setCellValue('C' . $y, '# pago realizado')
			->setCellValue('D' . $y, 'Fecha de pago');
		$no = 0;

		for ($x = 0; $x < count($arreglo); $x++) {

			// renglon y formato de celda
			$y++;
			$spreadsheet->getActiveSheet()->getStyle('A' . $y . ':D' . $y)->getFont()
				->setBold(true)
				->setSize(12)
				->getColor()
				->setRGB('000000');

			$no++;
			// borde de renglon
			$styleArray = array(
				'borders' => array(
					'top' => array(
						'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
						'color' => array('argb' => '025023'),
					),
				),
			);
			$spreadsheet->getActiveSheet()->getStyle('A' . $y . ':D' . $y)->applyFromArray($styleArray);

			// anotar datos del renglon
			$spreadsheet->setActiveSheetIndex(0)
				->setCellValue('A' . $y,$arreglo[$x]['recibo'])
				->setCellValue('B' . $y, $arreglo[$x]['cuenta'])
				->setCellValue('C' . $y, $arreglo[$x]['montoCobradoEnVisita'])
				->setCellValue('D' . $y, $arreglo[$x]['fechaDePago']);
		}
		return($y);
	}

	private function convertirAASCII($texto)
	{
		return strtr(utf8_decode($texto),
			utf8_decode(
				'ŠŒŽšœžŸ¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ& '),
			'SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy__');
	}

	private function fechaHumana($fecha)
	{
		$cachos=explode('-',$fecha);
		return($cachos['2']."-".$cachos['1']."-".$cachos['0']);
	}

	public function filtrarLista(Request $request)
	{
		$fechaInicial = $this->validarFecha($request->fechaInicial);
		$fechaInicialValidada = ($fechaInicial == 0) ? "" : $fechaInicial;
		$fechaFinal = $this->validarFecha($request->fechaFinal);
		$fechaFinalValidada = ($fechaFinal == 0) ? "" : $fechaFinal;
		$clienteValidado = (!empty($request->cliente)) ? $request->cliente : '';
		$archivoValidado = (!empty($request->archivo)) ? $request->archivo : '';
		$arrFiltros = array(
			'fechaInicial' => $fechaInicialValidada,
			'fechaFinal' => $fechaFinalValidada,
			'cliente' => $clienteValidado,
			'archivo' => $archivoValidado,
			'enExcel' => $request->enArchivo,
			'cobrador' => $request->cobrador
		);
		session()->put('arrFiltros', $arrFiltros);
		$stringBusqueda = $this->hacerStringBusqueda();
		$this->pagosLista($stringBusqueda);

		$pagos = session()->get('pagos');
		$menuCobradores = session()->get('menuCobradores');
		$menuEnArchivo = session()->get('menuEnArchivo');
		$arrFiltros = session()->get('arrFiltros');
		return view('pagos.lista')->with(['arrPagos' => $pagos, 'menuCobradores' => $menuCobradores, 'menuEnArchivo' => $menuEnArchivo, 'arrFiltros' => $arrFiltros]);
	}

	private function hacerStringBusqueda()
	{
		$datos = session()->get('arrFiltros');
		$stringBusquedaFecha = '';
		$stringBusquedaCliente = '';
		$stringBusquedaArchivo = '';
		$stringBusquedaCobrador = '';
		$stringBusquedaEnExcel = '';
		$stringFinal = '';
		// fechas
		if(!empty($datos['fechaInicial']) || !empty($datos['fechaFinal'])){
			$stringBusquedaFecha = $this->hacerStringFecha($datos['fechaInicial'],$datos['fechaFinal']);
		}

		// cliente
		if(!empty($datos['cliente'])) $stringBusquedaCliente = " nombreCliente like '%".$datos['cliente']."%' ";

		// archivo
		if(!empty($datos['archivo'])) $stringBusquedaArchivo = " archivo like '%".$datos['archivo']."%' ";

		// cobrador
		if($datos['cobrador']!='seleccione' && $datos['cobrador']>0) $stringBusquedaCobrador = " idCobrador = ".$datos['cobrador']." " ;

		// enArchivo
		if($datos['enExcel']!='seleccione') {
			$stringBusquedaEnExcel = ($datos['enExcel']=='si') ? $stringBusquedaEnExcel = "  enExcel = 1 " : $stringBusquedaEnExcel = "  enExcel = 0 ";
		}

		if(!empty($stringBusquedaFecha)){
			if(!empty($stringFinal)) $stringFinal.=" && ";
			$stringFinal.=$stringBusquedaFecha;
		}
		if(!empty($stringBusquedaCliente)){
			if(!empty($stringFinal)) $stringFinal.=" && ";
			$stringFinal.=$stringBusquedaCliente;
		}
		if(!empty($stringBusquedaArchivo)){
			if(!empty($stringFinal)) $stringFinal.=" && ";
			$stringFinal.=$stringBusquedaArchivo;
		}
		if(!empty($stringBusquedaCobrador)){
			if(!empty($stringFinal)) $stringFinal.=" && ";
			$stringFinal.=$stringBusquedaCobrador;
		}
		if(!empty($stringBusquedaEnExcel)){
			if(!empty($stringFinal)) $stringFinal.=" && ";
			$stringFinal.=$stringBusquedaEnExcel;
		}

		return($stringFinal);
	}

	private function hacerStringFecha($fechaInicial,$fechaFinal)
	{
		if(!empty($fechaInicial) && !empty($fechaFinal) ){
			$fechaInicialMy = $this->hacerFechaMysql($fechaInicial);
			$fechaFinalMy = $this->hacerFechaMysql($fechaFinal);
			$stringBusquedaFecha = " fechaRegistro >= '".$fechaInicialMy."'  && fechaRegistro <= '".$fechaFinalMy."' " ;
		}else if(!empty($fechaFinal)){
			$fechaFinalMy = $this->hacerFechaMysql($fechaFinal);
			$stringBusquedaFecha = " fechaRegistro <= '".$fechaFinalMy."' " ;
		}else{
			$fechaInicialMy = $this->hacerFechaMysql($fechaInicial);
			$stringBusquedaFecha = " fechaRegistro >= '".$fechaInicialMy."' " ;
		}

		return($stringBusquedaFecha);
	}


	private function validarFecha($fecha) // 06-03-2018
	{

		$cachos = explode('-',$fecha);
		if(count($cachos)==3){
			$dia = $cachos[0];
			$mes = $cachos[1];
			$ano = $cachos[2];
			if ($dia<1 or $mes<1 or strlen($ano)!=4){
				$resultado=0;
			}else{
				$dia =(strlen($dia)==1) ? '0'.$dia : $dia;
				$mes =(strlen($mes)==1) ? '0'.$mes : $mes;
				$resultado=(checkdate ($mes,$dia,$ano )) ? $dia."-".$mes."-".$ano :'0';
			}
		}else{
			$resultado=0;
		}

		return($resultado);
	}

	private function hacerFechaMysql($fecha)
	{
		$cachos=explode('-',$fecha);
		$fechaMy = $cachos[2].'-'.$cachos[1].'-'.$cachos[0];
		return($fechaMy);
	}

	public function formatoFechaH($fecha){
		$cachos = explode('-',$fecha);
		$dia = $cachos[2];
		$mes = $cachos[1];
		$ano = $cachos[0];
		return($dia.'-'.$mes.'-'.$ano);
	}
}
