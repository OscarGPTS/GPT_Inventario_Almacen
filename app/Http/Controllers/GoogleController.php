<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Buscar usuario por google_id o email
            $user = User::where('google_id', $googleUser->id)
                ->orWhere('email', $googleUser->email)
                ->first();

            if ($user) {
                // Actualizar google_id y avatar si no están configurados
                if (!$user->google_id) {
                    $user->google_id = $googleUser->id;
                }
                if (!$user->avatar) {
                    $user->avatar = $googleUser->avatar;
                }
                $user->save();
            } else {
                // Crear nuevo usuario
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                    'password' => null,
                ]);

                // Asignar rol automáticamente basado en datos de la API de RH
                $this->asignarRolDesdeApi($user, $googleUser->email);
            }

            Auth::login($user);

            $destino = $user->hasRole('visitante') ? route('reportes.entradas') : '/dashboard';
            return redirect()->intended($destino);
        } catch (\Exception $e) {
            return redirect('/')->with('error', 'Error al autenticar con Google: ' . $e->getMessage());
        }
    }

    /**
     * Consulta la API de RH y asigna un rol al usuario según su puesto/departamento.
     * - Departamento IT → admin
     * - Puesto "Almacenista" o Depto "Almacén" → almacenista
     * - Cualquier otro → visitante
     */
    private function asignarRolDesdeApi(User $user, string $email): void
    {
        try {
            $response = Http::timeout(8)->get('https://services.satechenergy.com/api/rh/users');

            if (!$response->successful()) {
                $user->addRole('visitante');
                return;
            }

            $empleados = collect($response->json('data', []));
            $empleado = $empleados->first(function ($emp) use ($email) {
                return !empty($emp['email']) && mb_strtolower($emp['email']) === mb_strtolower($email);
            });

            if (!$empleado) {
                $user->addRole('visitante');
                return;
            }

            $deptoId = $empleado['departamento']['id'] ?? null;
            $deptoNombre = mb_strtolower($empleado['departamento']['nombre'] ?? '');
            $puestoNombre = mb_strtolower($empleado['puesto']['nombre'] ?? '');

            // Departamento IT → admin
            if ($deptoId === 4 || str_contains($deptoNombre, 'it')) {
                $user->addRole('admin');
            // Puesto Almacenista o Departamento Almacén → almacenista
            } elseif (str_contains($puestoNombre, 'almacenista') || $deptoId === 1 || str_contains($deptoNombre, 'almac')) {
                $user->addRole('almacenista');
            } else {
                $user->addRole('visitante');
            }
        } catch (\Exception $e) {
            // Si la API falla, asignar rol visitante por defecto
            if (!$user->roles()->exists()) {
                $user->addRole('visitante');
            }
        }
    }
}
