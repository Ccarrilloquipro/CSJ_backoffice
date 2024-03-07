<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cobradores extends Model
{
    use HasFactory;
	protected $table = 'cobradores';

	protected $fillable = [
		'idUser',
		'nombre',
		'paterno',
		'materno',
		'idPersona',
		'activo',
	];


	public function pagos()
	{
		return $this->hasMany('App\Models\Pagos',"idCobrador");
	}
}
