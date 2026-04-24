<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!DB::table('roles')->where('name', 'calidad')->exists()) {
            DB::table('roles')->insert([
                'name'         => 'calidad',
                'display_name' => 'Control de Calidad',
                'description'  => 'Rol de control de calidad con acceso a inspecciones de ingreso a inventario',
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('roles')->where('name', 'calidad')->delete();
    }
};
