<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class LaratrustSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        $roles = [
            ['name' => 'admin',          'display_name' => 'Administrador',          'description' => 'Acceso total al sistema'],
            ['name' => 'admin_almacen',  'display_name' => 'Admin Almacén',          'description' => 'Administración del almacén'],
            ['name' => 'almacenista',    'display_name' => 'Almacenista',            'description' => 'Operaciones del almacén'],
            ['name' => 'visitante',      'display_name' => 'Visitante',              'description' => 'Solo lectura'],
        ];

        foreach ($roles as $r) {
            Role::firstOrCreate(['name' => $r['name']], $r);
        }

        // Permisos
        $permissions = [
            ['name' => 'catalogos-read',   'display_name' => 'Ver catálogos',       'description' => 'Ver catálogos del sistema'],
            ['name' => 'catalogos-create', 'display_name' => 'Crear catálogos',     'description' => 'Crear registros de catálogos'],
            ['name' => 'catalogos-update', 'display_name' => 'Editar catálogos',    'description' => 'Editar registros de catálogos'],
            ['name' => 'catalogos-delete', 'display_name' => 'Eliminar catálogos',  'description' => 'Eliminar registros de catálogos'],
            ['name' => 'logs-read',        'display_name' => 'Ver bitácora',        'description' => 'Ver bitácora de auditoría'],
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p['name']], $p);
        }

        // Asignar permisos a roles
        $admin = Role::where('name', 'admin')->first();
        $admin->syncPermissions(Permission::all());

        $adminAlmacen = Role::where('name', 'admin_almacen')->first();
        $adminAlmacen->syncPermissions(Permission::whereIn('name', [
            'catalogos-read', 'catalogos-create', 'catalogos-update', 'logs-read',
        ])->get());

        $almacenista = Role::where('name', 'almacenista')->first();
        $almacenista->syncPermissions(Permission::whereIn('name', [
            'catalogos-read',
        ])->get());

        // Visitante no tiene permisos de catálogos
    }
}
