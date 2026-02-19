<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnidadMedida extends Model
{
    protected $table = 'unidades_medida';
    
    protected $fillable = [
        'codigo',
        'descripcion',
    ];

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class);
    }
}
