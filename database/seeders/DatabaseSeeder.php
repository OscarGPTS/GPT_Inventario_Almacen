<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Componente;
use App\Models\Categoria;
use App\Models\Familia;
use App\Models\UnidadMedida;
use App\Models\Ubicacion;
use App\Models\Departamento;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Componentes
        Componente::create(['codigo' => 'T', 'descripcion' => 'Tipo T']);
        Componente::create(['codigo' => 'G', 'descripcion' => 'Tipo G']);

        // Categorías
        Categoria::create(['codigo' => 'SL', 'descripcion' => 'Soldadura']);
        Categoria::create(['codigo' => 'HE', 'descripcion' => 'Herramientas']);
        Categoria::create(['codigo' => 'EP', 'descripcion' => 'Equipo de Protección']);

        // Familias
        Familia::create(['codigo' => '016', 'descripcion' => 'Equipamiento']);
        Familia::create(['codigo' => '017', 'descripcion' => 'Consumibles']);
        Familia::create(['codigo' => '018', 'descripcion' => 'Seguridad']);

        // Unidades de Medida
        UnidadMedida::create(['codigo' => 'PZ', 'descripcion' => 'Pieza']);
        UnidadMedida::create(['codigo' => 'KG', 'descripcion' => 'Kilogramo']);
        UnidadMedida::create(['codigo' => 'LT', 'descripcion' => 'Litro']);
        UnidadMedida::create(['codigo' => 'MT', 'descripcion' => 'Metro']);
        UnidadMedida::create(['codigo' => 'CJ', 'descripcion' => 'Caja']);

        // Ubicaciones
        Ubicacion::create(['codigo' => 'R-34', 'descripcion' => 'Rack 34']);
        Ubicacion::create(['codigo' => 'R-35', 'descripcion' => 'Rack 35']);
        Ubicacion::create(['codigo' => 'A-10', 'descripcion' => 'Anaquel 10']);
        Ubicacion::create(['codigo' => 'A-11', 'descripcion' => 'Anaquel 11']);

        // Departamentos
        Departamento::create(['nombre' => 'Producción', 'descripcion' => 'Departamento de Producción']);
        Departamento::create(['nombre' => 'Mantenimiento', 'descripcion' => 'Departamento de Mantenimiento']);
        Departamento::create(['nombre' => 'Almacén', 'descripcion' => 'Departamento de Almacén']);
        Departamento::create(['nombre' => 'Soldadura', 'descripcion' => 'Departamento de Soldadura']);
    }
}

