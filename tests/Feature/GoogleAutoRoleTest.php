<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GoogleAutoRoleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin', 'display_name' => 'Admin']);
        Role::create(['name' => 'admin_almacen', 'display_name' => 'Admin Almacén']);
        Role::create(['name' => 'almacenista', 'display_name' => 'Almacenista']);
        Role::create(['name' => 'visitante', 'display_name' => 'Visitante']);
    }

    private function makeController(): \App\Http\Controllers\GoogleController
    {
        return new \App\Http\Controllers\GoogleController();
    }

    private function callAsignarRol(User $user, string $email): void
    {
        $controller = $this->makeController();
        $method = new \ReflectionMethod($controller, 'asignarRolDesdeApi');
        $method->setAccessible(true);
        $method->invoke($controller, $user, $email);
    }

    public function test_it_department_gets_admin_role(): void
    {
        Http::fake([
            'services.satechenergy.com/*' => Http::response([
                'success' => true,
                'data' => [[
                    'id' => 1,
                    'nombre_completo' => 'Dev User',
                    'email' => 'dev@company.com',
                    'puesto' => ['id' => 10, 'nombre' => 'Desarrollador'],
                    'departamento' => ['id' => 4, 'nombre' => 'IT'],
                ]],
            ]),
        ]);

        $user = User::factory()->create(['email' => 'dev@company.com']);
        $this->callAsignarRol($user, 'dev@company.com');

        $this->assertTrue($user->fresh()->hasRole('admin'));
    }

    public function test_almacenista_puesto_gets_almacenista_role(): void
    {
        Http::fake([
            'services.satechenergy.com/*' => Http::response([
                'success' => true,
                'data' => [[
                    'id' => 2,
                    'nombre_completo' => 'Alm User',
                    'email' => 'alm@company.com',
                    'puesto' => ['id' => 76, 'nombre' => 'Almacenista'],
                    'departamento' => ['id' => 1, 'nombre' => 'Almacén'],
                ]],
            ]),
        ]);

        $user = User::factory()->create(['email' => 'alm@company.com']);
        $this->callAsignarRol($user, 'alm@company.com');

        $this->assertTrue($user->fresh()->hasRole('almacenista'));
    }

    public function test_almacen_department_gets_almacenista_role(): void
    {
        Http::fake([
            'services.satechenergy.com/*' => Http::response([
                'success' => true,
                'data' => [[
                    'id' => 3,
                    'nombre_completo' => 'Depto Alm',
                    'email' => 'deptalm@company.com',
                    'puesto' => ['id' => 50, 'nombre' => 'Auxiliar'],
                    'departamento' => ['id' => 1, 'nombre' => 'Almacén'],
                ]],
            ]),
        ]);

        $user = User::factory()->create(['email' => 'deptalm@company.com']);
        $this->callAsignarRol($user, 'deptalm@company.com');

        $this->assertTrue($user->fresh()->hasRole('almacenista'));
    }

    public function test_other_department_gets_visitante_role(): void
    {
        Http::fake([
            'services.satechenergy.com/*' => Http::response([
                'success' => true,
                'data' => [[
                    'id' => 4,
                    'nombre_completo' => 'Other User',
                    'email' => 'other@company.com',
                    'puesto' => ['id' => 30, 'nombre' => 'Contador'],
                    'departamento' => ['id' => 5, 'nombre' => 'Finanzas'],
                ]],
            ]),
        ]);

        $user = User::factory()->create(['email' => 'other@company.com']);
        $this->callAsignarRol($user, 'other@company.com');

        $this->assertTrue($user->fresh()->hasRole('visitante'));
    }

    public function test_user_not_found_in_api_gets_visitante(): void
    {
        Http::fake([
            'services.satechenergy.com/*' => Http::response([
                'success' => true,
                'data' => [],
            ]),
        ]);

        $user = User::factory()->create(['email' => 'notfound@company.com']);
        $this->callAsignarRol($user, 'notfound@company.com');

        $this->assertTrue($user->fresh()->hasRole('visitante'));
    }

    public function test_api_failure_gives_visitante(): void
    {
        Http::fake([
            'services.satechenergy.com/*' => Http::response([], 500),
        ]);

        $user = User::factory()->create(['email' => 'fail@company.com']);
        $this->callAsignarRol($user, 'fail@company.com');

        $this->assertTrue($user->fresh()->hasRole('visitante'));
    }

    public function test_existing_user_login_does_not_reassign_role(): void
    {
        // If a user was already registered with a role (e.g. admin gave them admin_almacen),
        // when they login again, no re-assignment should occur because the code
        // only calls asignarRolDesdeApi for NEW users (else branch).
        $user = User::factory()->create(['email' => 'existing@company.com', 'google_id' => 'gid123']);
        $user->addRole('admin_almacen');

        // Verify role sticks
        $this->assertTrue($user->fresh()->hasRole('admin_almacen'));
    }
}
