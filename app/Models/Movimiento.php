<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    protected $fillable = [
        'producto_id',
        'usuario_id',
        'tipo_movimiento',
        'cantidad',
        'cantidad_anterior',
        'cantidad_nueva',
        'solicitud_id',
        'descripcion',
        'referencia',
    ];

    protected $table = 'movimientos';

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class);
    }

    // Event para registrar automáticamente
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($movimiento) {
            if (!$movimiento->usuario_id && auth()->check()) {
                $movimiento->usuario_id = auth()->id();
            }
        });
    }
}
