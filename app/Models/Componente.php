<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Componente extends Model
{
    protected $fillable = [
        'codigo',
        'descripcion',
    ];

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
}
