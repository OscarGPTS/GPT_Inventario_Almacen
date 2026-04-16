<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Categoria;
use App\Models\Componente;
use App\Models\Familia;
use App\Models\Permission;
use App\Models\Producto;
use App\Models\Role;
use App\Models\Ubicacion;
use App\Models\UnidadMedida;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogosTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $almacenista;
    protected User $visitante;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear roles y permisos
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Admin']);
        $almacenistaRole = Role::create(['name' => 'almacenista', 'display_name' => 'Almacenista']);
        $visitanteRole = Role::create(['name' => 'visitante', 'display_name' => 'Visitante']);

        $read = Permission::create(['name' => 'catalogos-read']);
        $create = Permission::create(['name' => 'catalogos-create']);
        $update = Permission::create(['name' => 'catalogos-update']);
        $delete = Permission::create(['name' => 'catalogos-delete']);
        $logs = Permission::create(['name' => 'logs-read']);

        $adminRole->syncPermissions([$read, $create, $update, $delete, $logs]);
        $almacenistaRole->syncPermissions([$read]);

        // Crear usuarios
        $this->admin = User::factory()->create(['name' => 'Admin User']);
        $this->admin->addRole('admin');

        $this->almacenista = User::factory()->create(['name' => 'Almacenista User']);
        $this->almacenista->addRole('almacenista');

        $this->visitante = User::factory()->create(['name' => 'Visitante User']);
        $this->visitante->addRole('visitante');
    }

    // ─── INDEX ────────────────────────────────────────────────────

    public function test_admin_can_view_catalogos_index(): void
    {
        Componente::create(['codigo' => 'G', 'descripcion' => 'Gasto']);
        Categoria::create(['codigo' => 'AF', 'descripcion' => 'Activo Fijo']);

        $response = $this->actingAs($this->admin)->get(route('catalogos.index'));

        $response->assertStatus(200);
        $response->assertSee('Catálogos del Sistema');
        $response->assertSee('Gasto');
        $response->assertSee('Activo Fijo');
    }

    public function test_almacenista_can_view_catalogos_index(): void
    {
        $response = $this->actingAs($this->almacenista)->get(route('catalogos.index'));
        $response->assertStatus(200);
    }

    public function test_visitante_cannot_view_catalogos_index(): void
    {
        $response = $this->actingAs($this->visitante)->get(route('catalogos.index'));
        $response->assertStatus(403);
    }

    public function test_guest_redirected_to_login(): void
    {
        $response = $this->get(route('catalogos.index'));
        $response->assertRedirect(route('login'));
    }

    // ─── STORE ────────────────────────────────────────────────────

    public function test_admin_can_create_componente(): void
    {
        $response = $this->actingAs($this->admin)->post(route('catalogos.store', 'componentes'), [
            'codigo' => 'G',
            'descripcion' => 'Gasto',
        ]);

        $response->assertRedirect(route('catalogos.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('componentes', ['codigo' => 'G', 'descripcion' => 'Gasto']);
    }

    public function test_admin_can_create_categoria(): void
    {
        $response = $this->actingAs($this->admin)->post(route('catalogos.store', 'categorias'), [
            'codigo' => 'AF',
            'descripcion' => 'Activo Fijo',
        ]);

        $response->assertRedirect(route('catalogos.index'));
        $this->assertDatabaseHas('categorias', ['codigo' => 'AF']);
    }

    public function test_admin_can_create_familia(): void
    {
        $response = $this->actingAs($this->admin)->post(route('catalogos.store', 'familias'), [
            'codigo' => '005',
            'descripcion' => 'Herramientas',
        ]);

        $response->assertRedirect(route('catalogos.index'));
        $this->assertDatabaseHas('familias', ['codigo' => '005']);
    }

    public function test_admin_can_create_unidad_medida(): void
    {
        $response = $this->actingAs($this->admin)->post(route('catalogos.store', 'unidades_medida'), [
            'codigo' => 'PZA',
            'descripcion' => 'Pieza',
        ]);

        $response->assertRedirect(route('catalogos.index'));
        $this->assertDatabaseHas('unidades_medida', ['codigo' => 'PZA']);
    }

    public function test_admin_can_create_ubicacion(): void
    {
        $response = $this->actingAs($this->admin)->post(route('catalogos.store', 'ubicaciones'), [
            'codigo' => 'A1',
            'descripcion' => 'Anaquel 1',
        ]);

        $response->assertRedirect(route('catalogos.index'));
        $this->assertDatabaseHas('ubicaciones', ['codigo' => 'A1']);
    }

    public function test_store_creates_audit_log(): void
    {
        $this->actingAs($this->admin)->post(route('catalogos.store', 'componentes'), [
            'codigo' => 'X',
            'descripcion' => 'Test',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'created',
            'table_name' => 'componentes',
            'user_id' => $this->admin->id,
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->admin)->post(route('catalogos.store', 'componentes'), [
            'codigo' => '',
            'descripcion' => '',
        ]);

        $response->assertSessionHasErrors(['codigo', 'descripcion']);
    }

    public function test_store_validates_unique_codigo(): void
    {
        Componente::create(['codigo' => 'G', 'descripcion' => 'Gasto']);

        $response = $this->actingAs($this->admin)->post(route('catalogos.store', 'componentes'), [
            'codigo' => 'G',
            'descripcion' => 'Duplicado',
        ]);

        $response->assertSessionHasErrors(['codigo']);
    }

    public function test_almacenista_cannot_create(): void
    {
        $response = $this->actingAs($this->almacenista)->post(route('catalogos.store', 'componentes'), [
            'codigo' => 'Z',
            'descripcion' => 'Test',
        ]);

        $response->assertStatus(403);
    }

    public function test_store_invalid_catalogo_returns_404(): void
    {
        $response = $this->actingAs($this->admin)->post(route('catalogos.store', 'invalido'), [
            'codigo' => 'X',
            'descripcion' => 'Test',
        ]);

        $response->assertStatus(404);
    }

    // ─── UPDATE ───────────────────────────────────────────────────

    public function test_admin_can_update_componente(): void
    {
        $comp = Componente::create(['codigo' => 'G', 'descripcion' => 'Gasto']);

        $response = $this->actingAs($this->admin)->put(route('catalogos.update', ['catalogo' => 'componentes', 'id' => $comp->id]), [
            'codigo' => 'T',
            'descripcion' => 'Gasto Modificado',
        ]);

        $response->assertRedirect(route('catalogos.index'));
        $this->assertDatabaseHas('componentes', ['id' => $comp->id, 'codigo' => 'T', 'descripcion' => 'Gasto Modificado']);
    }

    public function test_update_creates_audit_log(): void
    {
        $comp = Componente::create(['codigo' => 'G', 'descripcion' => 'Gasto']);

        $this->actingAs($this->admin)->put(route('catalogos.update', ['catalogo' => 'componentes', 'id' => $comp->id]), [
            'codigo' => 'T',
            'descripcion' => 'Gasto Editado',
        ]);

        $log = AuditLog::where('action', 'updated')->where('table_name', 'componentes')->first();
        $this->assertNotNull($log);
        $this->assertEquals('G', $log->old_values['codigo']);
        $this->assertEquals('T', $log->new_values['codigo']);
    }

    public function test_update_validates_unique_codigo_ignoring_self(): void
    {
        $comp1 = Componente::create(['codigo' => 'A', 'descripcion' => 'Uno']);
        $comp2 = Componente::create(['codigo' => 'B', 'descripcion' => 'Dos']);

        // Updating comp2 with comp1's codigo should fail
        $response = $this->actingAs($this->admin)->put(route('catalogos.update', ['catalogo' => 'componentes', 'id' => $comp2->id]), [
            'codigo' => 'A',
            'descripcion' => 'Conflict',
        ]);
        $response->assertSessionHasErrors(['codigo']);

        // Updating comp1 with its own codigo should succeed
        $response = $this->actingAs($this->admin)->put(route('catalogos.update', ['catalogo' => 'componentes', 'id' => $comp1->id]), [
            'codigo' => 'A',
            'descripcion' => 'Still A',
        ]);
        $response->assertRedirect(route('catalogos.index'));
    }

    public function test_almacenista_cannot_update(): void
    {
        $comp = Componente::create(['codigo' => 'G', 'descripcion' => 'Gasto']);

        $response = $this->actingAs($this->almacenista)->put(route('catalogos.update', ['catalogo' => 'componentes', 'id' => $comp->id]), [
            'codigo' => 'G2',
            'descripcion' => 'Not Allowed',
        ]);

        $response->assertStatus(403);
    }

    public function test_update_nonexistent_record_returns_404(): void
    {
        $response = $this->actingAs($this->admin)->put(route('catalogos.update', ['catalogo' => 'componentes', 'id' => 99999]), [
            'codigo' => 'X',
            'descripcion' => 'Ghost',
        ]);

        $response->assertStatus(404);
    }

    // ─── DESTROY ──────────────────────────────────────────────────

    public function test_admin_can_delete_componente(): void
    {
        $comp = Componente::create(['codigo' => 'DEL', 'descripcion' => 'To Delete']);

        $response = $this->actingAs($this->admin)->delete(route('catalogos.destroy', ['catalogo' => 'componentes', 'id' => $comp->id]));

        $response->assertRedirect(route('catalogos.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('componentes', ['id' => $comp->id]);
    }

    public function test_delete_creates_audit_log(): void
    {
        $comp = Componente::create(['codigo' => 'DEL', 'descripcion' => 'To Delete']);

        $this->actingAs($this->admin)->delete(route('catalogos.destroy', ['catalogo' => 'componentes', 'id' => $comp->id]));

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'deleted',
            'table_name' => 'componentes',
            'record_id' => $comp->id,
        ]);
    }

    public function test_cannot_delete_componente_in_use(): void
    {
        $comp = Componente::create(['codigo' => 'G', 'descripcion' => 'Gasto']);
        $cat = Categoria::create(['codigo' => 'AF', 'descripcion' => 'A.F.']);
        $fam = Familia::create(['codigo' => '001', 'descripcion' => 'Fam']);
        $um = UnidadMedida::create(['codigo' => 'PZA', 'descripcion' => 'Pieza']);
        $ub = Ubicacion::create(['codigo' => 'A1', 'descripcion' => 'An1']);

        Producto::create([
            'codigo' => 'GAF0010001',
            'consecutivo' => '0001',
            'descripcion' => 'Test Product',
            'componente_id' => $comp->id,
            'categoria_id' => $cat->id,
            'familia_id' => $fam->id,
            'unidad_medida_id' => $um->id,
            'ubicacion_id' => $ub->id,
            'cantidad_entrada' => 10,
            'cantidad_fisica' => 10,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('catalogos.destroy', ['catalogo' => 'componentes', 'id' => $comp->id]));

        $response->assertRedirect(route('catalogos.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('componentes', ['id' => $comp->id]);
    }

    public function test_almacenista_cannot_delete(): void
    {
        $comp = Componente::create(['codigo' => 'NP', 'descripcion' => 'No Perm']);

        $response = $this->actingAs($this->almacenista)->delete(route('catalogos.destroy', ['catalogo' => 'componentes', 'id' => $comp->id]));

        $response->assertStatus(403);
        $this->assertDatabaseHas('componentes', ['id' => $comp->id]);
    }

    // ─── ALL 5 CATALOGS ──────────────────────────────────────────

    public function test_crud_all_five_catalogs(): void
    {
        $catalogs = [
            'componentes' => ['model' => Componente::class, 'table' => 'componentes'],
            'categorias' => ['model' => Categoria::class, 'table' => 'categorias'],
            'familias' => ['model' => Familia::class, 'table' => 'familias'],
            'unidades_medida' => ['model' => UnidadMedida::class, 'table' => 'unidades_medida'],
            'ubicaciones' => ['model' => Ubicacion::class, 'table' => 'ubicaciones'],
        ];

        // Codes that respect per-catalog DB column limits
        $testCodigos = [
            'componentes'    => ['create' => 'T',    'update' => 'U'],
            'categorias'     => ['create' => 'TCA',  'update' => 'UCA'],
            'familias'       => ['create' => 'TFA',  'update' => 'UFA'],
            'unidades_medida'=> ['create' => 'TUM',  'update' => 'UUM'],
            'ubicaciones'    => ['create' => 'TUB',  'update' => 'UUB'],
        ];

        foreach ($catalogs as $key => $cfg) {
            $createCode = $testCodigos[$key]['create'];
            $updateCode = $testCodigos[$key]['update'];

            // Create
            $this->actingAs($this->admin)->post(route('catalogos.store', $key), [
                'codigo' => $createCode,
                'descripcion' => 'Test ' . $key,
            ])->assertRedirect(route('catalogos.index'));

            $record = $cfg['model']::where('codigo', $createCode)->first();
            $this->assertNotNull($record, "Record not created for {$key}");

            // Update
            $this->actingAs($this->admin)->put(route('catalogos.update', [$key, $record->id]), [
                'codigo' => $updateCode,
                'descripcion' => 'Updated ' . $key,
            ])->assertRedirect(route('catalogos.index'));

            $this->assertDatabaseHas($cfg['table'], ['id' => $record->id, 'codigo' => $updateCode]);

            // Delete
            $this->actingAs($this->admin)->delete(route('catalogos.destroy', [$key, $record->id]))
                ->assertRedirect(route('catalogos.index'));

            $this->assertDatabaseMissing($cfg['table'], ['id' => $record->id]);
        }
    }
}
