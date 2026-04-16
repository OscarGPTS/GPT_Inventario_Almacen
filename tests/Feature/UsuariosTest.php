<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsuariosTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $noAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear roles y permisos
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Administrador']);
        Role::create(['name' => 'admin_almacen', 'display_name' => 'Admin Almacén']);
        Role::create(['name' => 'almacenista', 'display_name' => 'Almacenista']);
        Role::create(['name' => 'visitante', 'display_name' => 'Visitante']);

        Permission::create(['name' => 'catalogos-read']);
        Permission::create(['name' => 'catalogos-create']);
        Permission::create(['name' => 'catalogos-update']);
        Permission::create(['name' => 'catalogos-delete']);
        Permission::create(['name' => 'logs-read']);

        $adminRole->syncPermissions(Permission::all());

        $this->admin = User::factory()->create(['name' => 'Admin User', 'email' => 'admin@test.com']);
        $this->admin->addRole('admin');

        $this->noAdmin = User::factory()->create(['name' => 'Regular User', 'email' => 'regular@test.com']);
        $this->noAdmin->addRole('almacenista');
    }

    // ─── INDEX ────────────────────────────────────────────────────

    public function test_admin_can_view_usuarios_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('usuarios.index'));

        $response->assertStatus(200);
        $response->assertSee('Gestión de Usuarios');
        $response->assertSee('Admin User');
        $response->assertSee('Regular User');
    }

    public function test_non_admin_cannot_view_usuarios_index(): void
    {
        $response = $this->actingAs($this->noAdmin)->get(route('usuarios.index'));
        $response->assertStatus(403);
    }

    public function test_guest_redirected_from_usuarios(): void
    {
        $response = $this->get(route('usuarios.index'));
        $response->assertRedirect(route('login'));
    }

    // ─── STORE ────────────────────────────────────────────────────

    public function test_admin_can_create_user(): void
    {
        $response = $this->actingAs($this->admin)->post(route('usuarios.store'), [
            'name'   => 'Nuevo Usuario',
            'email'  => 'nuevo@test.com',
            'avatar' => 'https://example.com/avatar.png',
            'role'   => 'visitante',
        ]);

        $response->assertRedirect(route('usuarios.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', ['email' => 'nuevo@test.com', 'name' => 'Nuevo Usuario']);

        $newUser = User::where('email', 'nuevo@test.com')->first();
        $this->assertTrue($newUser->hasRole('visitante'));
    }

    public function test_store_creates_audit_log(): void
    {
        $this->actingAs($this->admin)->post(route('usuarios.store'), [
            'name'  => 'Audit Test',
            'email' => 'audit@test.com',
            'role'  => 'almacenista',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'created',
            'table_name' => 'users',
            'user_id' => $this->admin->id,
        ]);
    }

    public function test_store_validates_unique_email(): void
    {
        $response = $this->actingAs($this->admin)->post(route('usuarios.store'), [
            'name'  => 'Duplicate',
            'email' => 'admin@test.com', // already exists
            'role'  => 'visitante',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_store_validates_valid_role(): void
    {
        $response = $this->actingAs($this->admin)->post(route('usuarios.store'), [
            'name'  => 'Bad Role',
            'email' => 'badrole@test.com',
            'role'  => 'superadmin_nonexistent',
        ]);

        $response->assertSessionHasErrors(['role']);
    }

    public function test_non_admin_cannot_create_user(): void
    {
        $response = $this->actingAs($this->noAdmin)->post(route('usuarios.store'), [
            'name'  => 'Nope',
            'email' => 'nope@test.com',
            'role'  => 'visitante',
        ]);

        $response->assertStatus(403);
    }

    // ─── UPDATE ROLE ─────────────────────────────────────────────

    public function test_admin_can_update_user_role(): void
    {
        $response = $this->actingAs($this->admin)->put(route('usuarios.update_role', $this->noAdmin), [
            'role' => 'admin_almacen',
        ]);

        $response->assertRedirect(route('usuarios.index'));
        $response->assertSessionHas('success');
        $this->assertTrue($this->noAdmin->fresh()->hasRole('admin_almacen'));
        $this->assertFalse($this->noAdmin->fresh()->hasRole('almacenista'));
    }

    public function test_update_role_creates_audit_log(): void
    {
        $this->actingAs($this->admin)->put(route('usuarios.update_role', $this->noAdmin), [
            'role' => 'visitante',
        ]);

        $log = AuditLog::where('action', 'updated')->where('table_name', 'users')->first();
        $this->assertNotNull($log);
        $this->assertEquals(['almacenista'], $log->old_values['roles']);
        $this->assertEquals(['visitante'], $log->new_values['roles']);
    }

    public function test_non_admin_cannot_update_role(): void
    {
        $response = $this->actingAs($this->noAdmin)->put(route('usuarios.update_role', $this->admin), [
            'role' => 'visitante',
        ]);

        $response->assertStatus(403);
    }

    // ─── DESTROY ──────────────────────────────────────────────────

    public function test_admin_can_delete_other_user(): void
    {
        $userId = $this->noAdmin->id;

        $response = $this->actingAs($this->admin)->delete(route('usuarios.destroy', $this->noAdmin));

        $response->assertRedirect(route('usuarios.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('users', ['id' => $userId]);
    }

    public function test_admin_cannot_delete_self(): void
    {
        $response = $this->actingAs($this->admin)->delete(route('usuarios.destroy', $this->admin));

        $response->assertRedirect(route('usuarios.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }

    public function test_non_admin_cannot_delete_user(): void
    {
        $response = $this->actingAs($this->noAdmin)->delete(route('usuarios.destroy', $this->admin));

        $response->assertStatus(403);
    }
}
