<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    protected $table = 'solicitudes';
    
    protected $fillable = [
        'folio',
        'fecha',
        'fecha_requerida',
        'solicitante',
        'departamento_id',
        'producto_id',
        'cantidad',
        'unidad_medida_id',
        'observaciones',
        'estado',
        'prioridad',
        'usuario_registro_id',
    ];

    protected $casts = [
        'fecha'           => 'date',
        'fecha_requerida' => 'date',
    ];

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function unidadMedida()
    {
        return $this->belongsTo(UnidadMedida::class);
    }

    public function usuarioRegistro()
    {
        return $this->belongsTo(User::class, 'usuario_registro_id');
    }

    public function movimientos()
    {
        return $this->hasMany(Movimiento::class);
    }
}
