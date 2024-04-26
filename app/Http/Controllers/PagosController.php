<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePagosRequest;
use App\Http\Requests\UpdatePagosRequest;
use App\Models\Pagos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use DateTime;
use DateInterval;


class PagosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
		$this->inicializarDatos();
		$arrFiltros = session()->get('arrFiltros');
		$this->pagosLista();

		$pagos = session()->get('pagos');
		$arrComisiones = $this->sumaComision($pagos);
		$menuCobradores = session()->get('menuCobradores');
		$menuEnArchivo = session()->get('menuEnArchivo');
		$arrFiltros = session()->get('arrFiltros');
		return view('pagos.lista')->with(['arrPagos' => $pagos, 'menuCobradores' => $menuCobradores, 'menuEnArchivo' => $menuEnArchivo, 'arrFiltros' => $arrFiltros,'total'=>$arrComisiones['monto'],'comision'=>$arrComisiones['comision']]);
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
		$arrComisiones = $this->sumaComision($pagos);
		return view('pagos.lista')->with(['arrPagos' => $pagos, 'menuCobradores' => $menuCobradores, 'menuEnArchivo' => $menuEnArchivo, 'arrFiltros' => $arrFiltros,'total'=>$arrComisiones['monto'],'comision'=>$arrComisiones['comision']]);

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
		$arrComisiones = $this->sumaComision($pagos);
		return view('pagos.lista')->with(['arrPagos' => $pagos, 'menuCobradores' => $menuCobradores, 'menuEnArchivo' => $menuEnArchivo, 'arrFiltros' => $arrFiltros,'total'=>$arrComisiones['monto'],'comision'=>$arrComisiones['comision']]);
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
		$arrComisiones = $this->sumaComision($pagos);
		return view('pagos.lista')->with(['arrPagos' => $pagos, 'menuCobradores' => $menuCobradores, 'menuEnArchivo' => $menuEnArchivo, 'arrFiltros' => $arrFiltros,'total'=>$arrComisiones['monto'],'comision'=>$arrComisiones['comision']]);
//		return view('pagos.lista')->with(['arrPagos' => $pagos, 'menuCobradores' => $menuCobradores, 'menuEnArchivo' => $menuEnArchivo, 'arrFiltros' => $arrFiltros]);
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
		$this->actualizarMarcados($arrSeleccionados);
		switch ($request->accion){
			case 'excel':
				if(!empty($arrSeleccionados)){
					$arrValidarEnviados = $this->evaluarPagosParaExcel($textoSeleccionados);
					if(empty($arrValidarEnviados)){
						$this->prepararExcel($textoSeleccionados);
					}else{
						$pagos = session()->get('pagos');
						$menuCobradores = session()->get('menuCobradores');
						$menuEnArchivo = session()->get('menuEnArchivo');
						$arrFiltros = session()->get('arrFiltros');
						$arrComisiones = $this->sumaComision($pagos);
						return view('pagos.lista')->with(['arrPagos' => $pagos, 'menuCobradores' => $menuCobradores, 'menuEnArchivo' => $menuEnArchivo, 'arrFiltros' => $arrFiltros,'total'=>$arrComisiones['monto'],'comision'=>$arrComisiones['comision'],'errores'=>$arrValidarEnviados]);
					}
				}
				break;
			case 'excelValidado':
				$this->prepararExcel($textoSeleccionados);
				break;
			default:
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
				$pago->marcado = 1;
			}else{
				$pago->marcado = 0;
			}
		}
		//session()->forget('pagos');
		session()->put('pagos', $pagos);
	}

	private function evaluarPagosParaExcel($textoSeleccionados)
	{
		$sql = "select date_format(pagos.fechaRegistro,'%d-%m-%Y') as fechaDePago,  pagos.claveCuenta,  pagos.montoCobradoEnVisita, pagos.cobrado as recibo, enExcel ,nombreCliente  
			from pagos 
			left join cobradores on cobradores.idPersona = pagos.idCobrador
			where pagos.id in ($textoSeleccionados)";
		$resultado = DB::connection('mysql')->select($sql);
		$arrValidarEnviados = array();
		foreach ($resultado as $item){
			if($item->enExcel==1) $arrValidarEnviados[]=$item->claveCuenta." - ".$item->nombreCliente." - $".number_format($item->montoCobradoEnVisita,2,'.',',');
		}
		return ($arrValidarEnviados);
	}

	private function prepararExcel($textoSeleccionados)
	{
		$sql = "select date_format(pagos.fechaRegistro,'%d-%m-%Y') as fechaDePago,  pagos.idCuenta,  pagos.montoCobradoEnVisita, pagos.recibo as recibo   
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
		$file = urldecode($titulo.'.xls');
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
		//$file = urldecode($titulo.'.xls');
		if(preg_match('/^[^.][-a-z0-9_.]+[a-z]$/i', $file)) {
			$filepath = Storage::path('tmp/' . $file);
			//$filepath = "tmp/".$file;
			//$writer = new Xlsx($spreadsheet);
			$writer = new Xls($spreadsheet);
			//$fxls ='excel-file_1.xlsx';
			$writer->save($filepath);
		}

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

	public function downloadExcel($filepath)
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
		$arrComisiones = $this->sumaComision($pagos);
		return view('pagos.lista')->with(['arrPagos' => $pagos, 'menuCobradores' => $menuCobradores, 'menuEnArchivo' => $menuEnArchivo, 'arrFiltros' => $arrFiltros,'total'=>$arrComisiones['monto'],'comision'=>$arrComisiones['comision']]);
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

	public function pantallaCuentas()
	{
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
		$menuCobradores = session()->get('menuCobradores');
		//		$archivos = ArchivosExportacion::all();
		//		$sql = "select archivosExportacion.*,users.name as generador
		//		from archivosExportacion
		//		left join users on archivosExportacion.idGenerador= users.id";
		//		$archivos = DB::connection('mysql')->select($sql);
		//return view('archivos.lista', compact('archivos'));
		return view('cuentas.cuentas',['menuCobradores' => $menuCobradores]);
	}

	public function filtrarCuentas(Request $request)
	{
		$menuCobradores = session()->get('menuCobradores');
		$idCobrador = $request->cobrador;
		session()->put('idCobradorFiltro', $idCobrador);
		$idCuenta = $request->cuenta;
		session()->put('idCuentaFiltro', $idCuenta);
		$menuCobradores = session()->get('menuCobradores');
		$respuesta = $this->buscarCuentas();
		if(isset($respuesta['error'])){
			return view('cuentas.cuentas',['menuCobradores' => $menuCobradores, 'error' => $respuesta['error']]);
		}else{
			$listaCuentas = $respuesta['listaCuentas'];
			return view('cuentas.cuentas',['menuCobradores' => $menuCobradores, 'listaCuentas' => $listaCuentas]);
		}
	}

	public function actualizarFecha(Request $request)
	{
		$menuCobradores = session()->get('menuCobradores');
		$idCuenta = $request->accion;
		$nombreCampo = "proximoPago_".$request->accion;
		$nuevaFecha = $request->$nombreCampo;
		$nuevaFechaMy = $this->hacerFechaMysql($nuevaFecha);
		$sql = "update core_cuenta set FECHA_PROXIMO_PAGO='".$nuevaFechaMy."' where ID_CUENTA=$idCuenta";
		DB::connection('remoto')->update($sql);
		$resultado = $this->buscarCuentas();
		$listaCuentas = $resultado['listaCuentas'];
		return view('cuentas.cuentas',['menuCobradores' => $menuCobradores, 'listaCuentas' => $listaCuentas]);
	}

	private function buscarCuentas()
	{
		$idCobrador = session()->get('idCobradorFiltro');
		$idCuenta = session()->get('idCuentaFiltro');

		$textoWhere = "";
		if($idCobrador=='seleccione' && $idCuenta == null){
			return(['error' => 'No se indico ni cobrador ni cuenta.']);
		}
		if($idCobrador!='seleccione'){
			$textoWhere = " core_cliente_aval.ID_COBRADOR = $idCobrador ";
		}
		if($idCuenta!=null && $idCuenta>0){
			if(!empty($textoWhere)) $textoWhere.=" && ";
			$textoWhere.= " core_cuenta.ID_CUENTA = $idCuenta ";
		}
		if(!empty($textoWhere)){
			$textoWhere.=" && core_cuenta.ACTIVO=1";
			$sql = "select core_persona.ID_PERSONA as idPersona,
				concat(core_persona.NOMBRE,' ',core_persona.AP_PATERNO,' ',core_persona.AP_MATERNO) as nombreCliente,
				core_contrato.ID_CLIENTE as idCliente,
				core_cuenta.ID_CUENTA as idCuenta,
				core_cuenta.CLAVE_CUENTA as claveCuenta,
				date_format(core_cuenta.FECHA_VENTA,'%d-%m-%Y') as fechaVenta,
				core_cuenta.ID_TIPO_PAGO as idTipoPago,
				core_cuenta.DIA_PAGO as diaPago,
				core_cuenta.DIA_PAGO_A as diaPagoA,
				core_cuenta.DIA_PAGO_B as diaPagoB,
				date_format(core_cuenta.FECHA_PROXIMO_PAGO,'%d-%m-%Y') as fechaProximoPago,
				core_cuenta.MONTO_TOTAL as montoTotal,
				core_cuenta.ENGANCHE as enganche,
				core_cuenta.ABONO_ACORDADO as montoAbonoAcordado,
				core_cuenta.FECHA_PRIMER_PAGO as fechaPrimerPago,
				core_cliente_aval.ID_COBRADOR as idCobrador,
				core_cliente_aval.ID_CONTRATO as idContrato,
				1 as incluir
				from core_persona
				left join core_contrato on core_persona.ID_PERSONA =core_contrato.ID_CLIENTE
				left join core_cuenta on core_cuenta.ID_CONTRATO=core_contrato.ID_CONTRATO
				left join core_cliente_aval on core_cliente_aval.ID_CONTRATO=core_contrato.ID_CONTRATO
				 where $textoWhere";
			$listaCuentas = DB::connection('remoto')->select($sql);
			return(['listaCuentas' => $listaCuentas]);
		}else{
			return(['error' => 'No se definieron los valores de filtro correctamente.']);
		}
	}

	// borrar

//	public function getCobros(Request $request)
//	{
//		$hoy = date('Y-m-d');
//		$arrCobros = array();
//		// todo: cuando se defina el campo donde esta le fecha de cobro cambiar query para filtrar por ese campo
//		$sql = "select core_cliente_aval.ID_COBRADOR as idCobrador,
//			core_cliente_aval.ID_CONTRATO as idContrato,
//			core_contrato.ID_CLIENTE as idCliente,
//			core_cuenta.ID_CUENTA as idCuenta,
//			core_cuenta.CLAVE_CUENTA as claveCuenta,
//			date_format(core_cuenta.FECHA_VENTA,'%d-%m-%Y') as fechaVenta,
//			core_cuenta.ID_TIPO_PAGO as idTipoPago,
//			core_cuenta.DIA_PAGO as diaPago,
//			core_cuenta.DIA_PAGO_A as diaPagoA,
//			core_cuenta.DIA_PAGO_B as diaPagoB,
//			core_cuenta.FECHA_PROXIMO_PAGO as fechaProximoPago,
//			core_cuenta.MONTO_TOTAL as montoTotal,
//			core_cuenta.ENGANCHE as enganche,
//			core_cuenta.ABONO_ACORDADO as montoAbonoAcordado,
//			core_cuenta.FECHA_PRIMER_PAGO as fechaPrimerPago,
//			sum(core_saldo.MONTO_ABONO) as totalPagadoEnAbonos,
//			core_persona.ID_PERSONA as idPersona,
//       		concat(core_persona.NOMBRE,' ',core_persona.AP_PATERNO,' ',core_persona.AP_MATERNO) as nombreCliente
//			from core_cliente_aval
//			left join core_contrato on core_contrato.ID_CONTRATO=core_cliente_aval.ID_CONTRATO
//			left join core_cuenta on core_cuenta.ID_CONTRATO=core_contrato.ID_CONTRATO
//			left join core_persona on core_persona.ID_PERSONA =core_contrato.ID_CLIENTE
//			left join core_saldo on core_saldo.ID_CUENTA=core_cuenta.ID_CUENTA
//			where core_cliente_aval.ID_COBRADOR=".$request->idCobrador." && core_cuenta.FECHA_PROXIMO_PAGO='".$hoy."' && core_cuenta.ACTIVO=1 group by core_cuenta.ID_CUENTA";
//
//		// having montoTotal-totalPagadoEnAbonos-enganche>0
//		$cobros = DB::connection('remoto')->select($sql);
//
//		foreach ($cobros as $cobro){
//			// evitar nulos en campos
//			$cobro->diaPago = ($cobro->diaPago == null) ? ' ' : $cobro->diaPago;
//
//			switch($cobro->idTipoPago){
//				case '30': //semanal
//					$periodicidadDePagoEnDias = 7;
//					break;
//				case '31': // quincenal
//					$periodicidadDePagoEnDias = 14;
//					break;
//				case '32': //mensual
//					$periodicidadDePagoEnDias = 30.4;
//					break;
//			}
//
//			// calcular fecha de liquidacion
//			$montoAPlazos = $cobro->montoTotal-$cobro->enganche;
//			$numeroDeAbonosTotales = ceil($montoAPlazos/$cobro->montoAbonoAcordado);
//			$abonosPagados = floor($cobro->totalPagadoEnAbonos/$cobro->montoAbonoAcordado);
//
//			$fechaInicial = new DateTime($cobro->fechaPrimerPago);
//			$fechaPrimerPagoH = $fechaInicial->format('d-m-Y'); // 5
//
//			$fechaHoy = new DateTime('NOW');
//			$diferenciaPrimerPagoYHoy = $fechaInicial->diff($fechaHoy);
//			$diasTranscurridos = $diferenciaPrimerPagoYHoy->days;
//
//			$pagosALaFecha = (floor($diasTranscurridos/$periodicidadDePagoEnDias) > ($numeroDeAbonosTotales) ) ? $numeroDeAbonosTotales : floor($diasTranscurridos/$periodicidadDePagoEnDias);
//			$pagosAtrasados = $pagosALaFecha-$abonosPagados; //8
//			$saldoAtrasado = $pagosAtrasados*$cobro->montoAbonoAcordado; // 9
//
//			$porcentajePagadoTmp = ($cobro->totalPagadoEnAbonos/$montoAPlazos)*100; // 7
//			$porcentajePagado = number_format((float)$porcentajePagadoTmp, 2, '.', ',');
//
//
//			$cantidadDeDiasDelPlazo = ceil($numeroDeAbonosTotales*$periodicidadDePagoEnDias);
//			$codigoParaDate = "P".$cantidadDeDiasDelPlazo."D";
//			$fechaTerminacionCredito = $fechaInicial;
//			$fechaTerminacionCredito->add(new DateInterval($codigoParaDate));
//			$fechaTerminacionCreditoH = $fechaTerminacionCredito->format('d-m-Y'); // 6
//
//			$fechaProximoPagoInt = new DateTime($cobro->fechaProximoPago);
//			$fechaProximoPagoH = $fechaProximoPagoInt->format('d-m-Y');
//
//			//calcular saldo
//			$saldo = $cobro->montoTotal-$cobro->totalPagadoEnAbonos-$cobro->enganche;
//
//			$pagoMinimo = $cobro->montoAbonoAcordado; // 10
//
//			// lista de pagos
//			$arrPagos = array();
//			$sql1 = "select ID_CUENTA as idCuenta, NUMERO_PAGO as numeroPago, date_format(fecha_pago,'%d-%m-%Y') as fechaPago, RECIBO_TIENDA as reciboTienda, MONTO_ABONO as montoAbono, MONTO_SALDO as montoSaldo from core_saldo where ID_CUENTA = ".$cobro->idCuenta." order by NUMERO_PAGO";
//			$resultados = DB::connection('remoto')->select($sql1);
//			foreach ($resultados as $resultado){
//				$arrPagos[] = array(
//					'pagoNo' => $resultado->numeroPago,
//					'fecha' => $resultado->fechaPago,
//					'recibo' => $resultado->reciboTienda,
//					'monto' => $resultado->montoAbono,
//					'saldo' => $resultado->montoSaldo,
//				);
//			}
//			$arrPagosInv = array();
//			$ultimo = count($arrPagos)-1;
//			for($i=$ultimo;$i>=0;$i--){
//				$arrPagosInv[] = $arrPagos[$i];
//			}
//
//			$total = (count($arrPagosInv)>=10) ? 10 : count($arrPagosInv);
//			$stringPagos='';
//			for($t=0;$t<$total;$t++){
//				if(!empty($stringPagos)) $stringPagos.='y';
//				$stringPagos.=$arrPagosInv[$t]['pagoNo']." ".$arrPagosInv[$t]['fecha']." ".$arrPagosInv[$t]['recibo']." ".$arrPagosInv[$t]['monto']." ".$arrPagosInv[$t]['saldo'];
//			}
//
//			//$arrAbonos = DB::connection('remoto')->select($sql1); // 13
//			$sql2 = "select max(NUMERO_PAGO) as ultimoAbono from core_saldo where ID_CUENTA = ".$cobro->idCuenta;
//			$resultados = DB::connection('remoto')->select($sql2);
//			foreach ($resultados as $resultado){
//				$ultimoAbono = 	$resultado->ultimoAbono;
//			}
//
//			// productos
//			$sql3 = "select sc.DESCRIPCION_CAT_INFORMACION as productos from core_mercancia
//				left join sec_cat_catalogo_informacion sc on sc.ID_SEC_CAT_INFORMACION = core_mercancia.ID_MERCANCIA
//				where core_mercancia.ID_CUENTA = ". $cobro->idCuenta;
//			$resultados = DB::connection('remoto')->select($sql3);
//			$productos = ''; // 12
//			foreach ($resultados as $resultado){
//				if(!empty($productos)) $productos.=", ";
//				$productos.=$resultado->productos;
//			}
//
//			// pronto pago
//			$stringProntoPago='';
//			$sql4 = "select ID_CUENTA as idCuenta, PLAZO as plazo,PRECIO as precio, FECHA as fecha, DATE_FORMAT(FECHA, '%d-%m-%Y') as fechaH from core_credicontado where ID_CUENTA = ".$cobro->idCuenta. " order by plazo";
//			$arrProntoPago = array(); // 14 - 15
//			$resultados = DB::connection('remoto')->select($sql4);
//			foreach ($resultados as $resultado){
//				$fechaHoy = new DateTime('NOW');
//				$fechaDescuento = new DateTime($resultado->fecha);
//				if($fechaHoy<=$fechaDescuento){
//					//$arrProntoPago[] = array('idCuenta' => $resultado->idCuenta,'plazo' => $resultado->plazo,'precio'=>$resultado->precio,'fecha'=>$resultado->fecha);
//					if(!empty($stringProntoPago)) $stringProntoPago.='y';
//					$stringProntoPago.=$resultado->fechaH." ".$resultado->precio;
//				}
//			}
//
//
//			// direccion
//			$fotoFachada = '';
//			$sql4="select core_direccion.ID_DIRECCION,
//			core_direccion.CALLE,
//			core_direccion.NUMERO_EXTERIOR,
//			core_direccion.NUMERO_INTERIOR,
//			core_direccion.COLONIA,
//			core_direccion.DELEGACION,
//			core_direccion.MUNICIPIO,
//			core_direccion.REFERENCIA_CALLES,
//			core_direccion.CODIGO_POSTAL from core_direccion where ID_SUJETO = ".$cobro->idCliente;
//			$direcciones = DB::connection('remoto')->select($sql4);
//			foreach ($direcciones as $direccion) {
//				$idDireccion = $direccion->ID_DIRECCION;
//				$calle = ($direccion->CALLE == null) ? ' ' : $direccion->CALLE;
//				$no_ext = ($direccion->NUMERO_EXTERIOR == null) ? ' ' : $direccion->NUMERO_EXTERIOR;
//				$no_int = ($direccion->NUMERO_INTERIOR == null) ? ' ' : $direccion->NUMERO_INTERIOR;
//				$colonia = ($direccion->COLONIA == null) ? ' ' : $direccion->COLONIA;
//				$delegacion = ($direccion->DELEGACION == null) ? '6RXG+WP' : $direccion->DELEGACION.' 6RXG+WP';
//				$municipio = ($direccion->MUNICIPIO == null) ? ' ' : $direccion->MUNICIPIO;
//				$referencias = ($direccion->REFERENCIA_CALLES == null) ? ' ' : $direccion->REFERENCIA_CALLES;
//				$cp = ($direccion->CODIGO_POSTAL == null) ? ' ' : $direccion->CODIGO_POSTAL;
//			}
//			// todo: aqui se busca la ruta de las imagenes de credencial y de fachada
////			$sql2 = "select IMAGEN as imagen from core_imagen where ID_CLIENTE= ". $cobro->idCliente." && ID_TIPO_IMAGEN = 1 ";
////			$foto = DB::connection('remoto')->select($sql2);
////			foreach ($foto as $fotoDireccion) {
////				$fotoFachada = $fotoDireccion->imagen;
////			}
//			$montoTotal = floatval($cobro->montoTotal);
//			$montoAbonoAcordado = floatval($cobro->montoAbonoAcordado);
//
//			//imagenes
//				//ife = 2
//				// foto domicilio 1
//
//
//
//
//
//			// traer el orden
//			$sql5a = "select max(orden) as maximo from orden";
//			$maximoOrdenTmp = DB::connection('mysql')->select($sql5a);
//			if(!empty($maximoOrdenTmp)) {
//				$maximo = $maximoOrdenTmp[0]->maximo + 1;
//			}else{
//				$maximo = 1;
//			}
//
//			$sql5="select orden from orden where idCuenta=".$cobro->idCuenta;
//			$ordenTmp = DB::connection('mysql')->select($sql5);
//			if(!empty($ordenTmp)){
//				$ordenDeItem = $ordenTmp[0]->orden;
//			}else{
//				$sql6 = "insert into orden set idCuenta = ".$cobro->idCuenta.",orden = $maximo";
//				DB::connection('mysql')->select($sql6);
//				$ordenDeItem = $maximo;
//				$maximo++;
//			}
//			$arrCobros[] = array(
//				'idCobrador' => $cobro->idCobrador,
//				'idContrato' => $cobro->idContrato,
//				'idCliente' => $cobro->idCliente,
//				'idCuenta' => $cobro->idCuenta,
//				'fechaVenta' => $cobro->fechaVenta,
//				'claveCuenta' => $cobro->claveCuenta,
//				'idTipoPago' => $cobro->idTipoPago,
//				'diaPago' => $cobro->diaPago,
//				'diaPagoA' => $cobro->diaPagoA,
//				'diaPagoB' => $cobro->diaPagoB,
//				'fechaProximoPago' => $fechaProximoPagoH,
//				'idPersona' => $cobro->idPersona,
//				'nombreCliente' => $cobro->nombreCliente,
//				'idDireccion' => $idDireccion,
//				'calle' => $calle,
//				'no_ext' => $no_ext,
//				'no_int' => $no_int,
//				'colonia' => $colonia,
//				'delegacion' => $delegacion,
//				'municipio' => $municipio,
//				'referencias' => $referencias,
//				'cp' => $cp,
//				'orden' => $ordenDeItem,
//				'cobrado' => 0,
//				'subido' => 0,
//				'fotoIdentificacion' => 'ife.jpg',
//				'fotoFachada' => 'fachada.png',
//				'montoCobradoEnVisita' => 0.00,
//				'fechaSiguientePago' => '',
//				'nota' => '',
//				'visitado' => 0,
//				'montoTotal' => $montoTotal,
//				'montoAbonoAcordado' => $montoAbonoAcordado,
//				'fechaPrimerPago' => $fechaPrimerPagoH,
//				'fechaTerminacionCredito' => $fechaTerminacionCreditoH,
//				'saldo' => $saldo,
//				'porcentajePagado' => $porcentajePagado,
//				'pagosAtrasados' => $pagosAtrasados,
//				'saldoAtrasado' => $saldoAtrasado,
//				'productos' => $productos,
//				'prontoPago' => $stringProntoPago,
//				'relacionPagos' => $stringPagos
//			);
//
//			// campos dejados fuera del arreglo
//			//'diasTranscurridos' => $diasTranscurridos,
//			//'enganche' => $cobro->enganche,
//			//'montoAPlazos' => $montoAPlazos,
//			//'periodicidadDePagoEnDias' => $periodicidadDePagoEnDias,
//			//'numeroDeAbonosTotales' => $numeroDeAbonosTotales,
//			//'totalPagadoEnAbonos' => $cobro->totalPagadoEnAbonos,
//
//			//'abonosPagados' => $abonosPagados,
//			//'pagosALaFecha' => $pagosALaFecha,
//			//'ultimoAbono' => $ultimoAbono,
//
//		}
//
//		// ordenar por campo orden
//		$campoOrden  = array_column($arrCobros, 'orden');
//		array_multisort($campoOrden, SORT_ASC, $arrCobros);
//
//
//
//		return response($arrCobros, 200);
//	}


	public function traerFoto(Request $request)
	{
		$sql = "select ID_IMAGEN, ID_CLIENTE,ID_TIPO_IMAGEN,NOMBRE_IMAGEN,NOMBRE_ARCHIVO_IMAGEN,IMAGEN from core_imagen where ID_TIPO_IMAGEN =2 and ID_CLIENTE = 23117";
		$resultados = DB::connection('remoto')->select($sql);
		$fotos = ''; // 12
		foreach ($resultados as $resultado){
			$imagen = $resultado->IMAGEN;
			$nombreImagen = $resultado->NOMBRE_ARCHIVO_IMAGEN;
			$cachos = explode(".",$nombreImagen);
			$cuantos = count($cachos);
			$ultimo = $cuantos-1;
			$extension = $cachos[$ultimo];
			$nombreArchivoImagen = $this->normalizarNombre($nombreImagen);
			$ruta = 'public/tmp/'.$nombreArchivoImagen;
			Storage::put($ruta, $imagen);
		}
	}

	public function traerOrden(Request $request)
	{
		// update el orden del registro
//		$idCuenta = $request->idCuenta;
//		$sqlLocal = "select count(*) as cuantos from orden where idCuenta=$idCuenta";
//		$resultado = DB::connection('mysql')->select($sqlLocal);
//		$cuantos = $resultado[0]->cuantos;
//		$nuevoOrden = $request->pagos[$x]['orden'];
//		if($cuantos > 0){
//			$sqlLocal2 = "update orden set orden = $nuevoOrden where idCuenta=$idCuenta";
//			DB::connection('mysql')->update($sqlLocal2);
//		}else{
//			$sqlLocal2 = "insert into orden set idCuenta=$idCuenta, orden=".$request->pagos[$x]['orden'];
//			DB::connection('mysql')->insert($sqlLocal2);
//		}
	}

	private function normalizarNombre ($nombre) {
		$tabla = array('Á'=>'A','É'=>'E','Í'=>'I','Ó'=>'O','Ú'=>'U','á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','Ñ'=>'N','ñ'=>'n','&'=>'_',' '=>'_');
		return strtr($nombre, $tabla);
	}

	public function buscarCobros(Request $request)
	{
		$nombre = $request->nombre;
		$cuenta = $request->cuenta;
		$direccionFiltro = $request->direccion;
		$idCobrador = $request->idCobrador;
		$arrCobros = array();
		$textoWhere = '';
		if(!empty($nombre)){
			$textoWhere = " (core_persona.NOMBRE like '%".$nombre."%' ||  core_persona.AP_MATERNO  like '%".$nombre."%' || core_persona.AP_MATERNO   like '%".$nombre."%') ";
		}
		if(!empty($cuenta)){
			if(!empty($textoWhere)) $textoWhere.=" && ";
			$textoWhere.=" core_cuenta.CLAVE_CUENTA like '%".$cuenta."%' ";
		}
		if(!empty($textoWhere)){
			$textoWhere.= " && core_cliente_aval.ID_COBRADOR = $idCobrador ";
			$textoWhere = " where ".$textoWhere;
			$sql = "select core_persona.ID_PERSONA as idPersona,
				concat(core_persona.NOMBRE,' ',core_persona.AP_PATERNO,' ',core_persona.AP_MATERNO) as nombreCliente,
				core_contrato.ID_CLIENTE as idCliente,
				core_cuenta.ID_CUENTA as idCuenta,
				core_cuenta.CLAVE_CUENTA as claveCuenta,
				date_format(core_cuenta.FECHA_VENTA,'%d-%m-%Y') as fechaVenta,
				core_cuenta.ID_TIPO_PAGO as idTipoPago,
				core_cuenta.DIA_PAGO as diaPago,
				core_cuenta.DIA_PAGO_A as diaPagoA,
				core_cuenta.DIA_PAGO_B as diaPagoB,
				core_cuenta.FECHA_PROXIMO_PAGO as fechaProximoPago,
				core_cuenta.MONTO_TOTAL as montoTotal,
				core_cuenta.ENGANCHE as enganche,
				core_cuenta.ABONO_ACORDADO as montoAbonoAcordado,
				core_cuenta.FECHA_PRIMER_PAGO as fechaPrimerPago,
				core_cliente_aval.ID_COBRADOR as idCobrador,
				core_cliente_aval.ID_CONTRATO as idContrato,
				1 as incluir
				from core_persona
				left join core_contrato on core_persona.ID_PERSONA =core_contrato.ID_CLIENTE
				left join core_cuenta on core_cuenta.ID_CONTRATO=core_contrato.ID_CONTRATO
				left join core_cliente_aval on core_cliente_aval.ID_CONTRATO=core_contrato.ID_CONTRATO
				$textoWhere";

			$cobros = DB::connection('remoto')->select($sql);
			foreach ($cobros as $cobro){
				if($cobro->idCliente !=null){
					if(empty($direccionFiltro)){
						$sql4="select 
						core_direccion.ID_DIRECCION AS idDireccion,
						core_direccion.CALLE as calle,
						core_direccion.NUMERO_EXTERIOR as noExt,
						core_direccion.NUMERO_INTERIOR as noInt,
						core_direccion.COLONIA as colonia,
						core_direccion.DELEGACION as delegacion,
						core_direccion.MUNICIPIO as municipio,
						core_direccion.REFERENCIA_CALLES as referencias,
						core_direccion.CODIGO_POSTAL as cp from core_direccion where ID_SUJETO = ".$cobro->idCliente." limit 1";
						$direcciones = DB::connection('remoto')->select($sql4);
						$direccion = $direcciones[0];
						//foreach ($direcciones as $direccion) {
							$cobro->idDireccion =  $direccion->idDireccion;
							$cobro->calle =  $direccion->calle;
							$cobro->noExt =  $direccion->noExt;
							$cobro->noInt =  $direccion->noInt;
							$cobro->colonia =  $direccion->colonia;
							$cobro->delegacion =  $direccion->delegacion;
							$cobro->municipio =  $direccion->municipio;
							$cobro->referencias =  $direccion->referencias;
							$cobro->cp =  $direccion->cp;
						//}
					}else{
						$sql5 = "select count(*) as cuantos from core_direccion where 
								 (CALLE like '%".$direccionFiltro."%' or              
								 NUMERO_EXTERIOR like '%".$direccionFiltro."%' or
								 NUMERO_INTERIOR like '%".$direccionFiltro."%' or              
								 COLONIA like '%".$direccionFiltro."%' or
								 DELEGACION like '%".$direccionFiltro."%' or              
								 MUNICIPIO like '%".$direccionFiltro."%' or
								 REFERENCIA_CALLES like '%".$direccionFiltro."%' or              
								 CODIGO_POSTAL like '%".$direccionFiltro."%') &&  ID_SUJETO = ".$cobro->idCliente;
						$direcciones = DB::connection('remoto')->select($sql5);
						$cuantos = $direcciones[0]->cuantos;
						if ($cuantos!=0){
							$sql6="select 
								core_direccion.ID_DIRECCION AS idDireccion,
								core_direccion.CALLE as calle,
								core_direccion.NUMERO_EXTERIOR as noExt,
								core_direccion.NUMERO_INTERIOR as noInt,
								core_direccion.COLONIA as colonia,
								core_direccion.DELEGACION as delegacion,
								core_direccion.MUNICIPIO as municipio,
								core_direccion.REFERENCIA_CALLES as referencias,
								core_direccion.CODIGO_POSTAL as cp 
								from core_direccion where 
								CALLE like '%".$direccionFiltro."%' or
								NUMERO_EXTERIOR like '%".$direccionFiltro."%' or
								NUMERO_INTERIOR like '%".$direccionFiltro."%' or
								COLONIA like '%".$direccionFiltro."%' or
								DELEGACION like '%".$direccionFiltro."%' or
								MUNICIPIO like '%".$direccionFiltro."%' or
								REFERENCIA_CALLES like '%".$direccionFiltro."%' or
								CODIGO_POSTAL like '%".$direccionFiltro."%' limit 1";
							$direcciones = DB::connection('remoto')->select($sql6);
							foreach ($direcciones as $direccion) {
								$cobro->idDireccion =  $direccion->idDireccion;
								$cobro->calle =  $direccion->calle;
								$cobro->noExt =  $direccion->noExt;
								$cobro->noInt =  $direccion->noInt;
								$cobro->colonia =  $direccion->colonia;
								$cobro->delegacion =  $direccion->delegacion;
								$cobro->municipio =  $direccion->municipio;
								$cobro->referencias =  $direccion->referencias;
								$cobro->cp =  $direccion->cp;
							}
						}else{
							$cobro->incluir=0;
						}
					}
				}











				// direccion
//				if($cobro->idCliente !=null){
//					$sql4="select
//    				core_direccion.ID_DIRECCION,
//					core_direccion.CALLE,
//					core_direccion.NUMERO_EXTERIOR,
//					core_direccion.NUMERO_INTERIOR,
//					core_direccion.COLONIA,
//					core_direccion.DELEGACION,
//					core_direccion.MUNICIPIO,
//					core_direccion.REFERENCIA_CALLES,
//					core_direccion.CODIGO_POSTAL from core_direccion where ID_SUJETO = ".$cobro->idCliente." limit 1";
//
//					$direcciones = DB::connection('remoto')->select($sql4);
//					foreach ($direcciones as $direccion) {
//						$idDireccion = $direccion->ID_DIRECCION;
//						$calle = ($direccion->CALLE == null) ? ' ' : $direccion->CALLE;
//						$no_ext = ($direccion->NUMERO_EXTERIOR == null) ? ' ' : $direccion->NUMERO_EXTERIOR;
//						$no_int = ($direccion->NUMERO_INTERIOR == null) ? ' ' : $direccion->NUMERO_INTERIOR;
//						$colonia = ($direccion->COLONIA == null) ? ' ' : $direccion->COLONIA;
//						$delegacion = ($direccion->DELEGACION == null) ? ' ' : $direccion->DELEGACION;
//						$municipio = ($direccion->MUNICIPIO == null) ? ' ' : $direccion->MUNICIPIO;
//						$referencias = ($direccion->REFERENCIA_CALLES == null) ? ' ' : $direccion->REFERENCIA_CALLES;
//						$cp = ($direccion->CODIGO_POSTAL == null) ? ' ' : $direccion->CODIGO_POSTAL;
//
//					}
//
//
//
//
//
//					if(!isset($idDireccion) ){
//						$cobro->incluir = 0;
//					}
//				}
			}
			// continuar solo con los que cumplen la condicion

			foreach ($cobros as $cobro){
				if($cobro->incluir ==1) {
					if($cobro->idCuenta !=null) {
						// traer saldo
						$sql = "select sum(core_saldo.MONTO_ABONO) as totalPagadoEnAbonos from core_saldo where core_saldo.ID_CUENTA =" . $cobro->idCuenta;
						$saldoTmp = DB::connection('remoto')->select($sql);
						foreach ($saldoTmp as $item) {
							$cobro->totalPagadoEnAbonos = $item->totalPagadoEnAbonos;
						}
						// evitar nulos en campos
						$cobro->diaPago = ($cobro->diaPago == null) ? ' ' : $cobro->diaPago;
						switch ($cobro->idTipoPago) {
							case '30': //semanal
								$periodicidadDePagoEnDias = 7;
								break;
							case '31': // quincenal
								$periodicidadDePagoEnDias = 14;
								break;
							case '32': //mensual
								$periodicidadDePagoEnDias = 30.4;
								break;
						}
						// calcular fecha de liquidacion
						$montoAPlazos = $cobro->montoTotal - $cobro->enganche;
						$numeroDeAbonosTotales = ceil($montoAPlazos / $cobro->montoAbonoAcordado);
						$abonosPagados = floor($cobro->totalPagadoEnAbonos / $cobro->montoAbonoAcordado);
						$fechaInicial = new DateTime($cobro->fechaPrimerPago);
						$fechaPrimerPagoH = $fechaInicial->format('d-m-Y'); // 5
						$fechaHoy = new DateTime('NOW');
						$diferenciaPrimerPagoYHoy = $fechaInicial->diff($fechaHoy);
						$diasTranscurridos = $diferenciaPrimerPagoYHoy->days;
						$pagosALaFecha = (floor($diasTranscurridos / $periodicidadDePagoEnDias) > ($numeroDeAbonosTotales)) ? $numeroDeAbonosTotales : floor($diasTranscurridos / $periodicidadDePagoEnDias);
						$pagosAtrasados = $pagosALaFecha - $abonosPagados; //8
						$saldoAtrasado = $pagosAtrasados * $cobro->montoAbonoAcordado; // 9
						$porcentajePagadoTmp = ($cobro->totalPagadoEnAbonos / $montoAPlazos) * 100; // 7
						$porcentajePagado = number_format((float)$porcentajePagadoTmp, 2, '.', ',');
						$cantidadDeDiasDelPlazo = ceil($numeroDeAbonosTotales * $periodicidadDePagoEnDias);
						$codigoParaDate = "P" . $cantidadDeDiasDelPlazo . "D";
						$fechaTerminacionCredito = $fechaInicial;
						$fechaTerminacionCredito->add(new DateInterval($codigoParaDate));
						$fechaTerminacionCreditoH = $fechaTerminacionCredito->format('d-m-Y'); // 6
						$fechaProximoPagoInt = new DateTime($cobro->fechaProximoPago);
						$fechaProximoPagoH = $fechaProximoPagoInt->format('d-m-Y');
						//calcular saldo
						$saldo = $cobro->montoTotal - $cobro->totalPagadoEnAbonos - $cobro->enganche;
						$pagoMinimo = $cobro->montoAbonoAcordado; // 10
						// lista de pagos
						$stringPagos = '';
						$sql1 = "select ID_CUENTA as idCuenta, NUMERO_PAGO as numeroPago, date_format(fecha_pago,'%d-%m-%Y') as fechaPago, RECIBO_TIENDA as reciboTienda, MONTO_ABONO as montoAbono, MONTO_SALDO as montoSaldo from core_saldo where ID_CUENTA = " . $cobro->idCuenta . " order by NUMERO_PAGO";
						$resultados = DB::connection('remoto')->select($sql1);
						foreach ($resultados as $resultado) {
							if (!empty($stringPagos)) $stringPagos .= 'y';
							$stringPagos .= $resultado->numeroPago . " " . $resultado->fechaPago . " " . $resultado->reciboTienda . " " . $resultado->montoAbono . " " . $resultado->montoSaldo;
						}
						//$arrAbonos = DB::connection('remoto')->select($sql1); // 13
						$sql2 = "select max(NUMERO_PAGO) as ultimoAbono from core_saldo where ID_CUENTA = " . $cobro->idCuenta;
						$resultados = DB::connection('remoto')->select($sql2);
						foreach ($resultados as $resultado) {
							$ultimoAbono = $resultado->ultimoAbono;
						}
						// productos
						$sql3 = "select sc.DESCRIPCION_CAT_INFORMACION as productos from core_mercancia
				left join sec_cat_catalogo_informacion sc on sc.ID_SEC_CAT_INFORMACION = core_mercancia.ID_MERCANCIA
				where core_mercancia.ID_CUENTA = " . $cobro->idCuenta;
						$resultados = DB::connection('remoto')->select($sql3);
						$productos = ''; // 12
						foreach ($resultados as $resultado) {
							if (!empty($productos)) $productos .= ", ";
							$productos .= $resultado->productos;
						}
						// pronto pago
						$stringProntoPago = '';
						$sql4 = "select ID_CUENTA as idCuenta, PLAZO as plazo,PRECIO as precio, DATE_FORMAT(FECHA, '%d-%m-%Y') as fecha from core_credicontado where ID_CUENTA = " . $cobro->idCuenta . " order by plazo";
						$arrProntoPago = array(); // 14 - 15
						$resultados = DB::connection('remoto')->select($sql4);
						foreach ($resultados as $resultado) {
							$arrProntoPago[] = array('idCuenta' => $resultado->idCuenta, 'plazo' => $resultado->plazo, 'precio' => $resultado->precio, 'fecha' => $resultado->fecha);
							if (!empty($stringProntoPago)) $stringProntoPago .= 'y';
							$stringProntoPago .= $resultado->fecha . " " . $resultado->precio;
						}
						// montos totales
						$montoTotal = floatval($cobro->montoTotal);
						$montoAbonoAcordado = floatval($cobro->montoAbonoAcordado);
						// traer el orden

						$idCobrador = (empty($cobro->idCobrador)) ? 0 : $cobro->idCobrador;
						$idContrato = (empty($cobro->idContrato)) ? 0 : $cobro->idContrato;
						$idCliente = (empty($cobro->idCliente)) ? 0 : $cobro->idCliente;
						$idCuenta = (empty($cobro->idCuenta)) ? 0 : $cobro->idCuenta;
						$fechaVenta = (empty($cobro->fechaVenta)) ? " " : $cobro->fechaVenta;
						$claveCuenta = (empty($cobro->claveCuenta)) ? " " : $cobro->claveCuenta;
						$idTipoPago = (empty($cobro->idTipoPago)) ? 0 : $cobro->idTipoPago;
						$diaPago = (empty($cobro->diaPago)) ? " " : $cobro->diaPago;
						$diaPagoA = (empty($cobro->diaPagoA)) ? 0 : $cobro->diaPagoA;
						$diaPagoB = (empty($cobro->diaPagoB)) ? 0 : $cobro->diaPagoB;
						$fechaProximoPagoH = (empty($fechaProximoPagoH)) ? " " : $fechaProximoPagoH;
						$idPersona = (empty($cobro->idPersona)) ? 0 : $cobro->idPersona;
						$nombreCliente = (empty($cobro->nombreCliente)) ? " " : $cobro->nombreCliente;
						$idDireccion = (empty($cobro->idDireccion)) ? 0 : $cobro->idDireccion;
						$calle = (empty($cobro->calle)) ? " " : $cobro->calle;
						$no_ext = (empty($cobro->noExt)) ? " " : $cobro->noExt;
						$no_int = (empty($cobro->noInt)) ? " " : $cobro->noInt;
						$colonia = (empty($cobro->colonia)) ? " " : $cobro->colonia;
						$delegacion = (empty($cobro->delegacion)) ? " " : $cobro->delegacion;
						$municipio = (empty($cobro->municipio)) ? " " : $cobro->municipio;
						$referencias = (empty($cobro->referencias)) ? " " : $cobro->referencias;
						$cp = (empty($cobro->cp)) ? " " : $cobro->cp;
						$arrCobros[] = array('idCobrador' => $idCobrador, 'idContrato' => $idContrato, 'idCliente' => $idCliente,
							'idCuenta' => $idCuenta, 'fechaVenta' => $fechaVenta, 'claveCuenta' => $claveCuenta,
							'idTipoPago' => $idTipoPago, 'diaPago' => $diaPago, 'diaPagoA' => $diaPagoA, 'diaPagoB' => $diaPagoB,
							'fechaProximoPago' => $fechaProximoPagoH, 'idPersona' => $idPersona, 'nombreCliente' => $nombreCliente, 'idDireccion' => $idDireccion, 'calle' => $calle, 'no_ext' => $no_ext, 'no_int' => $no_int, 'colonia' => $colonia, 'delegacion' => $delegacion, 'municipio' => $municipio, 'referencias' => $referencias, 'cp' => $cp, 'orden' => 1, 'cobrado' => 0, 'subido' => 0, 'recibo' => 0, 'fotoIdentificacion' => "", 'fotoFachada' => "", 'montoCobradoEnVisita' => 0.00, 'fechaSiguientePago' => '', 'nota' => '', 'visitado' => 0, 'montoTotal' => $montoTotal, 'montoAbonoAcordado' => $montoAbonoAcordado, 'fechaPrimerPago' => $fechaPrimerPagoH, 'fechaTerminacionCredito' => $fechaTerminacionCreditoH, 'saldo' => $saldo, 'porcentajePagado' => $porcentajePagado, 'pagosAtrasados' => $pagosAtrasados, 'saldoAtrasado' => $saldoAtrasado, 'productos' => $productos, 'prontoPago' => $stringProntoPago, 'relacionPagos' => $stringPagos);
					}
				}
			}
			return response($arrCobros, 200);

		}else{
			// si solo se mando la direccion
			return response($arrCobros, 200);
		}
	}

	private function sumaComision($pagos)
	{
		$monto = 0;
		$comision = 0;
		foreach ($pagos as $pago){
			$monto+=$pago->montoCobradoEnVisita;
		}
		$comision = $monto*.08;
		return ['monto'=>$monto,'comision'=>$comision];
	}


	public function getCobros(Request $request)
	{
		$hoy = date('Y-m-d');
		$arrCobros = array();
		$sql = "select core_cliente_aval.ID_COBRADOR as idCobrador,
			core_cliente_aval.ID_CONTRATO as idContrato,
			core_contrato.ID_CLIENTE as idCliente,
			core_cuenta.ID_CUENTA as idCuenta,
			core_cuenta.CLAVE_CUENTA as claveCuenta,
			date_format(core_cuenta.FECHA_VENTA,'%d-%m-%Y') as fechaVenta,
			core_cuenta.ID_TIPO_PAGO as idTipoPago,
			core_cuenta.DIA_PAGO as diaPago,
			core_cuenta.DIA_PAGO_A as diaPagoA,
			core_cuenta.DIA_PAGO_B as diaPagoB,
			core_cuenta.FECHA_PROXIMO_PAGO as fechaProximoPago, 
			core_cuenta.MONTO_TOTAL as montoTotal,
			core_cuenta.ENGANCHE as enganche,
			core_cuenta.ABONO_ACORDADO as montoAbonoAcordado,
			core_cuenta.FECHA_PRIMER_PAGO as fechaPrimerPago,
			sum(core_saldo.MONTO_ABONO) as totalPagadoEnAbonos,
			core_persona.ID_PERSONA as idPersona,
       		concat(core_persona.NOMBRE,' ',core_persona.AP_PATERNO,' ',core_persona.AP_MATERNO) as nombreCliente 
			from core_cliente_aval
			left join core_contrato on core_contrato.ID_CONTRATO=core_cliente_aval.ID_CONTRATO
			left join core_cuenta on core_cuenta.ID_CONTRATO=core_contrato.ID_CONTRATO
			left join core_persona on core_persona.ID_PERSONA =core_contrato.ID_CLIENTE
			left join core_saldo on core_saldo.ID_CUENTA=core_cuenta.ID_CUENTA
			where core_cliente_aval.ID_COBRADOR=".$request->idCobrador." && core_cuenta.FECHA_PROXIMO_PAGO='".$hoy."' && core_cuenta.ACTIVO=1 group by core_cliente_aval.ID_COBRADOR, core_cliente_aval.ID_CONTRATO,core_contrato.ID_CLIENTE,core_cuenta.CLAVE_CUENTA,core_cuenta.FECHA_VENTA,core_cuenta.ID_TIPO_PAGO,core_cuenta.DIA_PAGO,core_cuenta.DIA_PAGO_A,core_cuenta.DIA_PAGO_B,core_cuenta.FECHA_PROXIMO_PAGO, core_cuenta.MONTO_TOTAL, core_cuenta.ENGANCHE,core_cuenta.ABONO_ACORDADO,core_cuenta.FECHA_PRIMER_PAGO,
			core_persona.ID_PERSONA,core_persona.NOMBRE,core_persona.AP_PATERNO,core_persona.AP_MATERNO,core_cuenta.ID_CUENTA";

		// having montoTotal-totalPagadoEnAbonos-enganche>0
		$cobros = DB::connection('remoto')->select($sql);

		foreach ($cobros as $cobro){
			// evitar nulos en campos
			$cobro->diaPago = ($cobro->diaPago == null) ? ' ' : $cobro->diaPago;

			switch($cobro->idTipoPago){
				case '30': //semanal
					$periodicidadDePagoEnDias = 7;
					break;
				case '31': // quincenal
					$periodicidadDePagoEnDias = 14;
					break;
				case '32': //mensual
					$periodicidadDePagoEnDias = 30.4;
					break;
			}
			// calcular fecha de liquidacion

			$montoAPlazos = $cobro->montoTotal-$cobro->enganche;
			$numeroDeAbonosTotales = ceil($montoAPlazos/$cobro->montoAbonoAcordado);
			$abonosPagados = floor($cobro->totalPagadoEnAbonos/$cobro->montoAbonoAcordado);

			$fechaInicial = new DateTime($cobro->fechaPrimerPago);
			$fechaPrimerPagoH = $fechaInicial->format('d-m-Y'); // 5

			$fechaHoy = new DateTime('NOW');
			$diferenciaPrimerPagoYHoy = $fechaInicial->diff($fechaHoy);
			$diasTranscurridos = $diferenciaPrimerPagoYHoy->days;

			$pagosALaFecha = (floor($diasTranscurridos/$periodicidadDePagoEnDias) > ($numeroDeAbonosTotales) ) ? $numeroDeAbonosTotales : floor($diasTranscurridos/$periodicidadDePagoEnDias);
			$pagosAtrasados = $pagosALaFecha-$abonosPagados; //8
			$saldoAtrasado = $pagosAtrasados*$cobro->montoAbonoAcordado; // 9

			$porcentajePagadoTmp = ($cobro->totalPagadoEnAbonos/$montoAPlazos)*100; // 7
			$porcentajePagado = number_format((float)$porcentajePagadoTmp, 2, '.', ',');


			$cantidadDeDiasDelPlazo = ceil($numeroDeAbonosTotales*$periodicidadDePagoEnDias);
			$codigoParaDate = "P".$cantidadDeDiasDelPlazo."D";
			$fechaTerminacionCredito = $fechaInicial;
			$fechaTerminacionCredito->add(new DateInterval($codigoParaDate));
			$fechaTerminacionCreditoH = $fechaTerminacionCredito->format('d-m-Y'); // 6

			$fechaProximoPagoInt = new DateTime($cobro->fechaProximoPago);
			$fechaProximoPagoH = $fechaProximoPagoInt->format('d-m-Y');

			//calcular saldo
			$saldo = $cobro->montoTotal-$cobro->totalPagadoEnAbonos-$cobro->enganche;

			$pagoMinimo = $cobro->montoAbonoAcordado; // 10

			// lista de pagos
//			$stringPagos='';
//			$sql1 = "select ID_CUENTA as idCuenta, NUMERO_PAGO as numeroPago, date_format(fecha_pago,'%d-%m-%Y') as fechaPago, RECIBO_TIENDA as reciboTienda, MONTO_ABONO as montoAbono, MONTO_SALDO as montoSaldo from core_saldo where ID_CUENTA = ".$cobro->idCuenta." order by NUMERO_PAGO";
//			$resultados = DB::connection('remoto')->select($sql1);
//			foreach ($resultados as $resultado){
//				if(!empty($stringPagos)) $stringPagos.='y';
//				$stringPagos.=$resultado->numeroPago." ".$resultado->fechaPago." ".$resultado->reciboTienda." ".$resultado->montoAbono." ".$resultado->montoSaldo;
//			}


			// lista de pagos iva
			$arrPagos = array();
			$sql1 = "select ID_CUENTA as idCuenta, NUMERO_PAGO as numeroPago, date_format(fecha_pago,'%d-%m-%Y') as fechaPago, RECIBO_TIENDA as reciboTienda, MONTO_ABONO as montoAbono, MONTO_SALDO as montoSaldo from core_saldo where ID_CUENTA = ".$cobro->idCuenta." order by NUMERO_PAGO";
			$resultados = DB::connection('remoto')->select($sql1);
			foreach ($resultados as $resultado){
				$arrPagos[] = array(
					'pagoNo' => $resultado->numeroPago,
					'fecha' => $resultado->fechaPago,
					'recibo' => $resultado->reciboTienda,
					'monto' => $resultado->montoAbono,
					'saldo' => $resultado->montoSaldo,
				);
			}
			$arrPagosInv = array();
			$ultimo = count($arrPagos)-1;
			for($i=$ultimo;$i>=0;$i--){
				$arrPagosInv[] = $arrPagos[$i];
			}

			$total = (count($arrPagosInv)>=10) ? 10 : count($arrPagosInv);
			$stringPagos='';
			for($t=0;$t<$total;$t++){
				if(!empty($stringPagos)) $stringPagos.='y';
				$stringPagos.=$arrPagosInv[$t]['pagoNo']." ".$arrPagosInv[$t]['fecha']." ".$arrPagosInv[$t]['recibo']." ".$arrPagosInv[$t]['monto']." ".$arrPagosInv[$t]['saldo'];
			}



			$sql2 = "select max(NUMERO_PAGO) as ultimoAbono from core_saldo where ID_CUENTA = ".$cobro->idCuenta;
			$resultados = DB::connection('remoto')->select($sql2);
			foreach ($resultados as $resultado){
				$ultimoAbono = 	$resultado->ultimoAbono;
			}

			// productos
			$sql3 = "select sc.DESCRIPCION_CAT_INFORMACION as productos from core_mercancia
				left join sec_cat_catalogo_informacion sc on sc.ID_SEC_CAT_INFORMACION = core_mercancia.ID_MERCANCIA
				where core_mercancia.ID_CUENTA = ". $cobro->idCuenta;
			$resultados = DB::connection('remoto')->select($sql3);
			$productos = ''; // 12
			foreach ($resultados as $resultado){
				if(!empty($productos)) $productos.=", ";
				$productos.=$resultado->productos;
			}

			// pronto pago ant
//			$stringProntoPago='';
//			$sql4 = "select ID_CUENTA as idCuenta, PLAZO as plazo,PRECIO as precio, DATE_FORMAT(FECHA, '%d-%m-%Y') as fecha from core_credicontado where ID_CUENTA = ".$cobro->idCuenta. " order by plazo";
//			$arrProntoPago = array(); // 14 - 15
//			$resultados = DB::connection('remoto')->select($sql4);
//			foreach ($resultados as $resultado){
//				$arrProntoPago[] = array('idCuenta' => $resultado->idCuenta,'plazo' => $resultado->plazo,'precio'=>$resultado->precio,'fecha'=>$resultado->fecha);
//				if(!empty($stringProntoPago)) $stringProntoPago.='y';
//				$stringProntoPago.=$resultado->fecha." ".$resultado->precio;
//			}


			// pronto pago
			$stringProntoPago='';
			$sql4 = "select ID_CUENTA as idCuenta, PLAZO as plazo,PRECIO as precio, FECHA as fecha, DATE_FORMAT(FECHA, '%d-%m-%Y') as fechaH from core_credicontado where ID_CUENTA = ".$cobro->idCuenta. "  and ACTIVO=1 order by plazo";
			$arrProntoPago = array(); // 14 - 15
			$resultados = DB::connection('remoto')->select($sql4);
			foreach ($resultados as $resultado){
				$fechaHoy = new DateTime('NOW');
				$fechaDescuento = new DateTime($resultado->fecha);
				if($fechaHoy<=$fechaDescuento){
//					$arrProntoPago[] = array('idCuenta' => $resultado->idCuenta,'plazo' => $resultado->plazo,'precio'=>$resultado->precio,'fecha'=>$resultado->fecha);
					if(!empty($stringProntoPago)) $stringProntoPago.='y';
					$stringProntoPago.=$resultado->fechaH." ".$resultado->precio;
				}
			}

			// direccion
			$fotoFachada = '';
			$sql4="select core_direccion.ID_DIRECCION,
			core_direccion.CALLE,
			core_direccion.NUMERO_EXTERIOR,
			core_direccion.NUMERO_INTERIOR,
			core_direccion.COLONIA,
			core_direccion.DELEGACION,
			core_direccion.MUNICIPIO,
			core_direccion.REFERENCIA_CALLES,
			core_direccion.CODIGO_POSTAL from core_direccion where ID_SUJETO = ".$cobro->idCliente;
			$direcciones = DB::connection('remoto')->select($sql4);
			foreach ($direcciones as $direccion) {
				$idDireccion = $direccion->ID_DIRECCION;
				$calle = ($direccion->CALLE == null) ? ' ' : $direccion->CALLE;
				$no_ext = ($direccion->NUMERO_EXTERIOR == null) ? ' ' : $direccion->NUMERO_EXTERIOR;
				$no_int = ($direccion->NUMERO_INTERIOR == null) ? ' ' : $direccion->NUMERO_INTERIOR;
				$colonia = ($direccion->COLONIA == null) ? ' ' : $direccion->COLONIA;
				$delegacion = ($direccion->DELEGACION == null) ? ' ' : $direccion->DELEGACION;
				$municipio = ($direccion->MUNICIPIO == null) ? ' ' : $direccion->MUNICIPIO;
				$referencias = ($direccion->REFERENCIA_CALLES == null) ? ' ' : $direccion->REFERENCIA_CALLES;
				$cp = ($direccion->CODIGO_POSTAL == null) ? ' ' : $direccion->CODIGO_POSTAL;
			}
			// todo: aqui se busca la ruta de las imagenes de credencial y de fachada
			// fotos
//			$fotoFachada = $this->traerFoto(1,$cobro->idCliente);
//			$fotoIne = $this->traerFoto(2,$cobro->idCliente);

			$fotoFachada = 'qwerwqr';
			$fotoIne = 'xcvbxcvbxcvb';

			// montos totales
			$montoTotal = floatval($cobro->montoTotal);
			$montoAbonoAcordado = floatval($cobro->montoAbonoAcordado);

			// traer el orden
			$sql5a = "select max(orden) as maximo from orden";
			$maximoOrdenTmp = DB::connection('mysql')->select($sql5a);
			if(!empty($maximoOrdenTmp)) {
				$maximo = $maximoOrdenTmp[0]->maximo + 1;
			}else{
				$maximo = 1;
			}

			$sql5="select orden from orden where idCuenta=".$cobro->idCuenta;
			$ordenTmp = DB::connection('mysql')->select($sql5);
			if(!empty($ordenTmp)){
				$ordenDeItem = $ordenTmp[0]->orden;
			}else{
				$sql6 = "insert into orden set idCuenta = ".$cobro->idCuenta.",orden = $maximo";
				DB::connection('mysql')->select($sql6);
				$ordenDeItem = $maximo;
				//$maximo++;
			}

			$arrCobros[] = array(
				'idCobrador' => $cobro->idCobrador,
				'idContrato' => $cobro->idContrato,
				'idCliente' => $cobro->idCliente,
				'idCuenta' => $cobro->idCuenta,
				'fechaVenta' => $cobro->fechaVenta,
				'claveCuenta' => $cobro->claveCuenta,
				'idTipoPago' => $cobro->idTipoPago,
				'diaPago' => $cobro->diaPago,
				'diaPagoA' => $cobro->diaPagoA,
				'diaPagoB' => $cobro->diaPagoB,
				'fechaProximoPago' => $fechaProximoPagoH,
				'idPersona' => $cobro->idPersona,
				'nombreCliente' => $cobro->nombreCliente,
				'idDireccion' => $idDireccion,
				'calle' => $calle,
				'no_ext' => $no_ext,
				'no_int' => $no_int,
				'colonia' => $colonia,
				'delegacion' => $delegacion,
				'municipio' => $municipio,
				'referencias' => $referencias,
				'cp' => $cp,
				'orden' => $ordenDeItem,
				'cobrado' => 0,
				'subido' => 0,
				'recibo' => 0,
				'fotoIdentificacion' => $fotoIne,
				'fotoFachada' => $fotoFachada,
				'montoCobradoEnVisita' => 0.00,
				'fechaSiguientePago' => '',
				'nota' => '',
				'visitado' => 0,
				'montoTotal' => $montoTotal,
				'montoAbonoAcordado' => $montoAbonoAcordado,
				'fechaPrimerPago' => $fechaPrimerPagoH,
				'fechaTerminacionCredito' => $fechaTerminacionCreditoH,
				'saldo' => $saldo,
				'porcentajePagado' => $porcentajePagado,
				'pagosAtrasados' => $pagosAtrasados,
				'saldoAtrasado' => $saldoAtrasado,
				'productos' => $productos,
				'prontoPago' => $stringProntoPago,
				'relacionPagos' => $stringPagos
			);
		}

		// ordenar por campo orden
		$campoOrden  = array_column($arrCobros, 'orden');
		array_multisort($campoOrden, SORT_ASC, $arrCobros);

		return response($arrCobros, 200);
	}
}
