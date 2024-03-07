<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pagos extends Model
{
    use HasFactory;
	protected $table = 'pagos';

	protected $fillable = [
		'fechaRegistro',
		'idCuenta',
		'idCobrador',
		'idContrato',
		'idCliente',
		'claveCuenta',
		'idTipoPago',
		'diaPago',
		'diaPagoA',
		'diaPagoB',
		'montoTotal',
		'abono',
		'totalPagado',
		'idPersona',
		'nombreCliente',
		'idDireccion',
		'calle',
		'noExt',
		'noInt',
		'colonia',
		'delegacion',
		'municipio',
		'referencias',
		'cp',
		'orden',
		'cobrado',
		'subido',
		'fotoIdentificacion',
		'fotoFachada',
		'montoPagado',
		'fechaSiguientePago',
		'nota',
		'visitado'
	];
	public function cobrador()
	{
		return $this->belongsTo('App\Models\Cobradores', 'idCobrador');
	}

//	public function precios()
//	{
//		return $this->hasMany('App\Models\PreciosEventos',"idEvento");
//	}




}
