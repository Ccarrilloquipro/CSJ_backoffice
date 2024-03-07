<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArchivosExportacion extends Model
{
    use HasFactory;
	protected $table = 'archivosExportacion';

	protected $fillable = [
		'idGenerador',
		'fechaCreacion',
		'archivo'
	];
}
