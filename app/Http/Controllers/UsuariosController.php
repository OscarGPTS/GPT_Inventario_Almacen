<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Departamento;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class UsuariosController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if (!$user->hasRole('admin')) {
            abort(403, 'Solo los administradores pueden gestionar usuarios.');
        }

        $usuarios = User::with('roles')->orderBy('name')->get();
        $roles = Role::orderBy('name')->get();

        return view('usuarios.index', compact('usuarios', 'roles'));
    }

    /**
     * Buscar empleados en la API de RH.
     */
    public function buscarApi(Request $request)
    {
        $user = auth()->user();
        if (!$user->hasRole('admin')) {
            abort(403);
        }

        try {
            $response = Http::timeout(10)->get('https://services.satechenergy.com/api/rh/users');

            if (!$response->successful()) {
                return response()->json(['error' => 'No se pudo conectar con la API de RH'], 502);
            }

            $data = $response->json();
            $empleados = collect($data['data'] ?? []);

            // Emails ya registrados en el sistema
            $existingEmails = User::pluck('email')->filter()->map(fn($e) => mb_strtolower($e))->toArray();

            // Marcar como ya_registrado los que ya existen; excluir los que no tienen email
            $empleados = $empleados
                ->filter(fn($emp) => !empty($emp['email']))
                ->map(function ($emp) use ($existingEmails) {
                    $emp['ya_registrado'] = in_array(mb_strtolower($emp['email']), $existingEmails);
                    return $emp;
                })
                ->values();

            return response()->json($empleados);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al consultar API: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Dar de alta un usuario desde la API de RH.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        if (!$user->hasRole('admin')) {
            abort(403);
        }

        $request->validate([
            'email'   => 'required|email|unique:users,email',
            'name'    => 'required|string|max:255',
            'avatar'  => 'nullable|url|max:500',
            'role'    => 'required|string|exists:roles,name',
        ]);

        $newUser = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'avatar'   => $request->avatar,
            'password' => null,
        ]);

        $newUser->addRole($request->role);

        AuditLog::registrar('created', 'users', $newUser->id, null, [
            'name'  => $newUser->name,
            'email' => $newUser->email,
            'role'  => $request->role,
        ]);

        return redirect()->route('usuarios.index')
            ->with('success', "Usuario «{$newUser->name}» dado de alta con rol «{$request->role}».");
    }

    /**
     * Actualizar rol de un usuario.
     */
    public function updateRole(Request $request, User $usuario)
    {
        $user = auth()->user();
        if (!$user->hasRole('admin')) {
            abort(403);
        }

        $request->validate([
            'role' => 'required|string|exists:roles,name',
        ]);

        $oldRoles = $usuario->roles->pluck('name')->toArray();
        $usuario->syncRoles([$request->role]);

        AuditLog::registrar('updated', 'users', $usuario->id, [
            'roles' => $oldRoles,
        ], [
            'roles' => [$request->role],
        ]);

        return redirect()->route('usuarios.index')
            ->with('success', "Rol de «{$usuario->name}» actualizado a «{$request->role}».");
    }

    /**
     * Eliminar un usuario (solo admin, no puede eliminarse a sí mismo).
     */
    public function destroy(User $usuario)
    {
        $user = auth()->user();
        if (!$user->hasRole('admin')) {
            abort(403);
        }

        if ($user->id === $usuario->id) {
            return redirect()->route('usuarios.index')
                ->with('error', 'No puedes eliminarte a ti mismo.');
        }

        $old = ['name' => $usuario->name, 'email' => $usuario->email];
        $nombre = $usuario->name;

        $usuario->roles()->detach();
        $usuario->delete();

        AuditLog::registrar('deleted', 'users', $old['name'] ? 0 : 0, $old, null);

        return redirect()->route('usuarios.index')
            ->with('success', "Usuario «{$nombre}» eliminado.");
    }

    /**
     * Obtener información del usuario actual desde la API de RH.
     * Retorna departamento (con id local si existe) para pre-llenar el modal de solicitud.
     */
    public function infoRhUsuario()
    {
        $email = auth()->user()->email;

        try {
            $response = Http::timeout(10)->post(
                'https://services.satechenergy.com/api/rh/users/buscar-por-email',
                ['email' => $email]
            );

            if (!$response->successful()) {
                return response()->json(['error' => 'API RH no disponible'], 502);
            }

            $data   = $response->json();
            $emp    = $data['data'] ?? $data;

            // Extraer nombre del departamento — ajustar la clave si la API usa otro nombre
            $deptNombre = $emp['departamento'] ?? $emp['department'] ?? $emp['area'] ?? null;

            if (!$deptNombre) {
                return response()->json(['departamento_id' => null, 'departamento_nombre' => null]);
            }

            // Buscar si ya existe localmente (insensible a mayúsculas)
            $depto = Departamento::whereRaw('LOWER(nombre) = ?', [mb_strtolower($deptNombre)])->first();

            return response()->json([
                'departamento_id'     => $depto?->id,
                'departamento_nombre' => $deptNombre,
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
