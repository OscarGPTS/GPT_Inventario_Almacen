<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $fillable = [
        'nombre',
        'codigo',
        'descripcion',
    ];

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class);
    }
}
