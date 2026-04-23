<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'     => 'Administrador',
                'email'    => 'admin@almacen.local',
                'password' => Hash::make('Admin1234!'),
                'role'     => 'admin',
            ],
            [
                'name'     => 'Admin Almacén',
                'email'    => 'admin.almacen@almacen.local',
                'password' => Hash::make('Admin1234!'),
                'role'     => 'admin_almacen',
            ],
            [
                'name'     => 'Almacenista Demo',
                'email'    => 'almacenista@almacen.local',
                'password' => Hash::make('Admin1234!'),
                'role'     => 'almacenista',
            ],
            [
                'name'     => 'Visitante Demo',
                'email'    => 'visitante@almacen.local',
                'password' => Hash::make('Admin1234!'),
                'role'     => 'visitante',
            ],
        ];

        foreach ($users as $data) {
            $role = $data['role'];
            unset($data['role']);

            $user = User::firstOrCreate(
                ['email' => $data['email']],
                $data
            );

            // Asignar rol si aún no lo tiene (Laratrust 8)
            $roleModel = Role::where('name', $role)->first();
            if ($roleModel && !$user->hasRole($role)) {
                $user->addRole($roleModel);
            }
        }
    }
}
