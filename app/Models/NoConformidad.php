<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NoConformidad extends Model
{
    protected $table = 'no_conformidades';

    protected $fillable = [
        'producto_id',
        'cantidad',
        'estatus',
        'descripcion',
        'resolucion',
        'fecha_deteccion',
        'fecha_resolucion',
        'registrado_por',
        'resuelto_por',
    ];

    protected $casts = [
        'fecha_deteccion'  => 'date',
        'fecha_resolucion' => 'date',
        'cantidad'         => 'decimal:2',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function registrador()
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public function resolutor()
    {
        return $this->belongsTo(User::class, 'resuelto_por');
    }
}
