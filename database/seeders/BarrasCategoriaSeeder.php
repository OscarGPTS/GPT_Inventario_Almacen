<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;

class BarrasCategoriaSeeder extends Seeder
{
    public function run(): void
    {
        Categoria::firstOrCreate(
            ['codigo' => 'BR'],
            ['descripcion' => 'Barras']
        );
    }
}
