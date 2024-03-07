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
		$arrItems = $this->traerDatosLista();
		$arrPagos = $arrItems['pagos'];
		$arrMenuCobradores = $arrItems['menuCobradores'];
		session()->put('pagos', $arrPagos);
		session()->put('menuCobradores', $arrMenuCobradores);
		$pagos = session()->get('pagos');
		$menuCobradores = session()->get('menuCobradores');
		return view('pagos.lista')->with(['arrPagos' => $pagos, 'menuCobradores' => $menuCobradores]);
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
	public function ficha($id){
		$pago = $this->traerDatosFicha($id);
		return view('pagos.ficha')->with(['pago' => $pago]);
	}

	public function formatoFecha($fecha){
		$cachos = explode('-',$fecha);
		$dia = $cachos[2];
		$mes = $cachos[1];
		$ano = $cachos[0];
		return($dia.'-'.$mes.'-'.$ano);
	}

	public function formatoFechaMysql($fecha){
		$cachos = explode('-',$fecha);
		$dia = $cachos[0];
		$mes = $cachos[1];
		$ano = $cachos[2];
		return($ano.'-'.$mes.'-'.$dia);
	}

	public function cambiarProximaFecha(Request $request)
	{
		$datos = $request;
		$id = $request->id;
		$nuevaFecha = $request->nuevaFecha;
		$nuevaFechaMy = $this->formatoFechaMysql($request->nuevaFecha);
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
		$pago->fechaRegistroH = $this->formatoFecha($pago->fechaRegistro);
		$pago->fechaSiguientePagoH = $this->formatoFecha($pago->fechaSiguientePago);
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
		return view('pagos.lista')->with(['arrPagos' => $pagos]);
	}

	public function quitarTodos()
	{
		$pagos = Session::get('pagos');
		foreach ($pagos as $pago){
			$pago->marcado = 0;
		}
		return view('pagos.lista')->with(['arrPagos' => $pagos]);
	}


	private function traerDatosLista()
	{
		$sql = "select pagos.*,date_format(pagos.fechaSiguientePago,'%d-%m-%Y') as fechaSiguientePagoH,
       	date_format(pagos.fechaRegistro,'%d-%m-%Y') as fechaDePago, 
       	cobradores.nombre as nombreCobrador, cobradores.id as idCobradorLocal, 
       	archivosExportacion.archivo as archivo,
       	0 as marcado     
			from pagos 
			left join cobradores on cobradores.idPersona = pagos.idCobrador
			left join archivosExportacion on archivosExportacion.id = pagos.idArchivoExportacion";
		$pagos = DB::connection('mysql')->select($sql);
		$sql = "select id, concat(nombre,' ',paterno,' ',materno) as nombre from cobradores ";
		$menuCobradores = DB::connection('mysql')->select($sql);




		$arrInfo = array('pagos'=>$pagos,'menuCobradores'=>$menuCobradores);
		return($arrInfo);
	}

	public function regresarLista()
	{
		$pagos = Session::get('pagos');
		return view('pagos.lista')->with(['arrPagos' => $pagos]);
	}


	public function submitLista(Request $request)
	{
		$datos = $request->toArray();
		switch ($request->accion){
			case 'excel':
				break;
			default:
				$this->actualizarMarcados($datos);
				$id = $request->accion;
				$this->ficha($id);
				break;
		}











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

		if(!empty($arrSeleccionados)){
			$sql = "select date_format(pagos.fechaRegistro,'%d-%m-%Y') as fechaDePago, cobradores.nombre as nombreCobrador, pagos.claveCuenta, pagos.nombreCliente , pagos.montoCobradoEnVisita    
			from pagos 
			left join cobradores on cobradores.idPersona = pagos.idCobrador
			where pagos.id in ($textoSeleccionados)";
			$resultado = DB::connection('mysql')->select($sql);
			$arrResultado = array();
			foreach ($resultado as $item){
				$arrResultado[]=array('fechaDePago'=>$item->fechaDePago,'nombreCobrador'=>$item->nombreCobrador,'claveCuenta'=>$item->claveCuenta,'nombreCliente'=>$item->nombreCliente,'montoCobradoEnVisita'=>$item->montoCobradoEnVisita);
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
				$resultado = DB::connection('mysql')->insert($sql);
				$idInsertado = DB::getPdo()->lastInsertId();
				// update cada archivo con el nombre de el excel generado
				$sql1 = "update pagos set enExcel=1, idArchivoExportacion=$idInsertado where pagos.id in ($textoSeleccionados)";
				DB::connection('mysql')->insert($sql1);
				// hacer nueva lista para desplegar
				// download el excel generado
				$this->downloadExcel($filepath);
			}






		}

		$arrItems = $this->traerDatosLista();
		$pagos = $arrItems['pagos'];
		$menuCobradores = $arrItems['menuCobradores'];
		//Session::put('pagos', $pagos);
		return view('pagos.lista')->with(['arrPagos' => $pagos, 'menuCobradores' => $menuCobradores]);
	}

	private function actualizarMarcados()
	{

	}

	private function hacerExcel($datos,$titulo,$file)
	{
		// hacer titulo

//		$nombreUsuario = Auth::user()->name;
//		$nombreNormalizado = $this->convertirAASCII($nombreUsuario);
//		$hoy = date('d_m_Y_H_i_s');
//		$titulo = $nombreNormalizado.'_'.$hoy;

		//object of the Spreadsheet class to create the excel data
		$spreadsheet = new Spreadsheet();
		$spreadsheet->getDefaultStyle()->getFont()->setName('Arial');
		$spreadsheet->getDefaultStyle()->getFont()->setSize(12);
		//add some data in excel cells
		$y = 3;
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
		$spreadsheet->getActiveSheet()->getStyle('A' . $y . ':E' . $y)->getFont()
			->setBold(true)
			->setSize(16)
			->getColor()
			->setRGB('FFFFFF');

		$spreadsheet
			->getActiveSheet()
			->getStyle('A' . $y . ':E' . $y)
			->getFill()
			->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
			->getStartColor()
			->setARGB('025023');
		$spreadsheet->setActiveSheetIndex(0)
			->setCellValue('A' . $y, $titulo);

		$spreadsheet->getActiveSheet()->mergeCells('A' . $y . ':E' . $y); //, Worksheet::MERGE_CELL_CONTENT_HIDE

		// formatos cabecera
		$y++;
		$spreadsheet->getActiveSheet()->getStyle('A' . $y . ':E' . $y)->getFont()
			->setBold(true)
			->setSize(14)
			->getColor()
			->setRGB('FFFFFF');

		$spreadsheet
			->getActiveSheet()
			->getStyle('A' . $y . ':E' . $y)
			->getFill()
			->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
			->getStartColor()
			->setARGB('7C9248');

		$spreadsheet->setActiveSheetIndex(0)
			->setCellValue('A' . $y, 'Fecha de pago')
			->setCellValue('B' . $y, 'Cobrador')
			->setCellValue('C' . $y, 'Clave cuenta')
			->setCellValue('D' . $y, 'Cliente')
			->setCellValue('E' . $y, 'Pago');


		$no = 0;

		for ($x = 0; $x < count($arreglo); $x++) {

			// renglon y formato de celda
			$y++;

			// datos
			//$fechaH = $this->fechaHumana($arreglo[$x]['fechaRegistro']);


			$spreadsheet->getActiveSheet()->getStyle('A' . $y . ':E' . $y)->getFont()
				->setBold(true)
				->setSize(12)
				->getColor()
				->setRGB('000000');



//			$spreadsheet->getActiveSheet()->getStyle('C' . $y)->getNumberFormat()
//				->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

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
			$spreadsheet->getActiveSheet()->getStyle('A' . $y . ':E' . $y)->applyFromArray($styleArray);

			// anotar datos del renglon
			$spreadsheet->setActiveSheetIndex(0)
				->setCellValue('A' . $y,$arreglo[$x]['fechaDePago'])
				->setCellValue('B' . $y, $arreglo[$x]['nombreCobrador'])
				->setCellValue('C' . $y, $arreglo[$x]['claveCuenta'])
				->setCellValue('D' . $y, $arreglo[$x]['nombreCliente'])
				->setCellValue('E' . $y, $arreglo[$x]['montoCobradoEnVisita']);
		}

		// linea de abajo verde

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
}
