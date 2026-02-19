<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
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
                    'password' => null, // No requiere password para Google OAuth
                ]);
            }

            Auth::login($user);

            return redirect()->intended('/dashboard');
        } catch (\Exception $e) {
            return redirect('/')->with('error', 'Error al autenticar con Google: ' . $e->getMessage());
        }
    }
}
