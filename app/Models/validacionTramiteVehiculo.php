<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class validacionTramiteVehiculo extends Model
{
    use HasFactory;
    protected $table="validacion_tramite_vehiculo";
    protected $fillable = [
        'serie',
        'folio',
        'encriptado',
    ];

}
