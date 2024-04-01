<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArchivosExportacionRequest;
use App\Http\Requests\UpdateArchivosExportacionRequest;
use App\Models\ArchivosExportacion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ArchivosExportacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
//		$archivos = ArchivosExportacion::all();
		$sql = "select archivosExportacion.*,users.name as generador
		from archivosExportacion
		left join users on archivosExportacion.idGenerador= users.id";
		$archivos = DB::connection('mysql')->select($sql);
		return view('archivos.lista', compact('archivos'));
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
    public function store(StoreArchivosExportacionRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ArchivosExportacion $archivosExportacion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ArchivosExportacion $archivosExportacion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateArchivosExportacionRequest $request, ArchivosExportacion $archivosExportacion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ArchivosExportacion $archivosExportacion)
    {
        //
    }


	// jom

	public function detalle($id)
	{

		$sql = "select archivo from archivosExportacion where id = $id";
		$resultado = DB::connection('mysql')->select($sql);
		$archivo = $resultado[0]->archivo;
		$sql = "select pagos.*,date_format(pagos.fechaRegistro,'%d-%m-%Y') as fechaDePago, 
			cobradores.nombre as nombreCobrador, cobradores.id as idCobradorLocal,
			archivosExportacion.archivo
			from pagos 
			left join cobradores on cobradores.idPersona = pagos.idCobrador
			left join archivosExportacion on archivosExportacion.id = pagos.idArchivoExportacion
			
			where idArchivoExportacion = $id";
		$pagos = DB::connection('mysql')->select($sql);
		return view('archivos.detalle', compact('pagos'));
	}

	public function exportar($nombreArchivo)
	{
		$filepath = Storage::path('tmp/'.$nombreArchivo);

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

}
