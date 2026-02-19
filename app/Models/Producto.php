<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'codigo',
        'componente_id',
        'categoria_id',
        'familia_id',
        'consecutivo',
        'descripcion',
        'unidad_medida_id',
        'ubicacion_id',
        'cantidad_entrada',
        'cantidad_salida',
        'cantidad_fisica',
        'fecha_entrada',
        'fecha_salida',
        'precio_unitario',
        'moneda',
        'factura',
        'observaciones',
        'fecha_vencimiento',
        'hoja_seguridad',
    ];

    protected $casts = [
        'fecha_entrada' => 'date',
        'fecha_salida' => 'date',
        'fecha_vencimiento' => 'date',
        'precio_unitario' => 'decimal:2',
    ];

    public function componente()
    {
        return $this->belongsTo(Componente::class);
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function familia()
    {
        return $this->belongsTo(Familia::class);
    }

    public function unidadMedida()
    {
        return $this->belongsTo(UnidadMedida::class);
    }

    public function ubicacion()
    {
        return $this->belongsTo(Ubicacion::class);
    }

    public function movimientos()
    {
        return $this->hasMany(Movimiento::class);
    }

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class);
    }

    // Generar código automáticamente
    public static function generarCodigo($componente_codigo, $categoria_codigo, $familia_codigo, $consecutivo)
    {
        return $componente_codigo . $categoria_codigo . $familia_codigo . str_pad($consecutivo, 4, '0', STR_PAD_LEFT);
    }
}
