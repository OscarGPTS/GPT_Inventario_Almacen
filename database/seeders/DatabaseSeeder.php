<?php

namespace Database\Seeders;

use App\Models\Componente;
use App\Models\Categoria;
use App\Models\Familia;
use App\Models\UnidadMedida;
use App\Models\Ubicacion;
use App\Models\Departamento;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Roles y permisos ───────────────────────────────────────────
        $this->call(LaratrustSeeder::class);

        // ── 2. Catálogos base ─────────────────────────────────────────────

        // Componentes
        $componentes = [
            ['codigo' => 'T', 'descripcion' => 'Tipo T'],
            ['codigo' => 'G', 'descripcion' => 'Tipo G'],
            ['codigo' => 'P', 'descripcion' => 'Tipo P'],
        ];
        foreach ($componentes as $c) {
            Componente::firstOrCreate(['codigo' => $c['codigo']], $c);
        }

        // Categorías
        $categorias = [
            ['codigo' => 'SL', 'descripcion' => 'Soldadura'],
            ['codigo' => 'HE', 'descripcion' => 'Herramientas'],
            ['codigo' => 'EP', 'descripcion' => 'Equipo de Protección'],
            ['codigo' => 'ME', 'descripcion' => 'Materiales Eléctricos'],
            ['codigo' => 'MT', 'descripcion' => 'Materiales de Taller'],
            ['codigo' => 'LU', 'descripcion' => 'Lubricantes'],
            ['codigo' => 'BR', 'descripcion' => 'Barras'],
        ];
        foreach ($categorias as $c) {
            Categoria::firstOrCreate(['codigo' => $c['codigo']], $c);
        }

        // Familias
        $familias = [
            ['codigo' => '016', 'descripcion' => 'Equipamiento'],
            ['codigo' => '017', 'descripcion' => 'Consumibles'],
            ['codigo' => '018', 'descripcion' => 'Seguridad'],
            ['codigo' => '019', 'descripcion' => 'Mantenimiento'],
        ];
        foreach ($familias as $f) {
            Familia::firstOrCreate(['codigo' => $f['codigo']], $f);
        }

        // Unidades de Medida
        $unidades = [
            ['codigo' => 'PZ',  'descripcion' => 'Pieza'],
            ['codigo' => 'KG',  'descripcion' => 'Kilogramo'],
            ['codigo' => 'LT',  'descripcion' => 'Litro'],
            ['codigo' => 'MT',  'descripcion' => 'Metro'],
            ['codigo' => 'CJ',  'descripcion' => 'Caja'],
            ['codigo' => 'JGO', 'descripcion' => 'Juego'],
            ['codigo' => 'PAR', 'descripcion' => 'Par'],
            ['codigo' => 'ML',  'descripcion' => 'Mililitro'],
            ['codigo' => 'GR',  'descripcion' => 'Gramo'],
            ['codigo' => 'CM',  'descripcion' => 'Centímetro'],
            ['codigo' => 'RLL', 'descripcion' => 'Rollo'],
        ];
        foreach ($unidades as $u) {
            UnidadMedida::firstOrCreate(['codigo' => $u['codigo']], $u);
        }

        // Ubicaciones
        $ubicaciones = [
            ['codigo' => 'R-34', 'descripcion' => 'Rack 34'],
            ['codigo' => 'R-35', 'descripcion' => 'Rack 35'],
            ['codigo' => 'A-10', 'descripcion' => 'Anaquel 10'],
            ['codigo' => 'A-11', 'descripcion' => 'Anaquel 11'],
            ['codigo' => 'A-12', 'descripcion' => 'Anaquel 12'],
            ['codigo' => 'B-01', 'descripcion' => 'Bodega 01'],
            ['codigo' => 'B-02', 'descripcion' => 'Bodega 02'],
            ['codigo' => 'PISO', 'descripcion' => 'Piso de producción'],
        ];
        foreach ($ubicaciones as $u) {
            Ubicacion::firstOrCreate(['codigo' => $u['codigo']], $u);
        }

        // Departamentos
        $departamentos = [
            ['nombre' => 'Producción',    'descripcion' => 'Departamento de Producción'],
            ['nombre' => 'Mantenimiento', 'descripcion' => 'Departamento de Mantenimiento'],
            ['nombre' => 'Almacén',       'descripcion' => 'Departamento de Almacén'],
            ['nombre' => 'Soldadura',     'descripcion' => 'Departamento de Soldadura'],
            ['nombre' => 'Calidad',       'descripcion' => 'Departamento de Calidad'],
            ['nombre' => 'Logística',     'descripcion' => 'Departamento de Logística'],
            ['nombre' => 'Administración','descripcion' => 'Departamento de Administración'],
        ];
        foreach ($departamentos as $d) {
            Departamento::firstOrCreate(['nombre' => $d['nombre']], $d);
        }

        // ── 3. Usuarios de demostración ───────────────────────────────────
        $this->call(UsersSeeder::class);
    }
}

