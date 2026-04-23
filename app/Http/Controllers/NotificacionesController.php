<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificacionesController extends Controller
{
    /**
     * Página completa de notificaciones.
     * GET /notificaciones
     */
    public function index()
    {
        /** @var User $user */
        $user  = Auth::user();
        $notificaciones = $user->notifications()->latest()->paginate(20);

        return view('notificaciones.index', compact('notificaciones'));
    }

    /**
     * Marcar una notificación específica como leída.
     * POST /notificaciones/{id}/leer
     */
    public function leer(string $id)
    {
        /** @var User $user */
        $user  = Auth::user();
        $notif = $user->notifications()->findOrFail($id);
        $notif->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Marcar todas las notificaciones del usuario como leídas.
     * POST /notificaciones/leer-todas
     */
    public function leerTodas()
    {
        /** @var User $user */
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Eliminar una notificación específica.
     * DELETE /notificaciones/{id}
     */
    public function destroy(string $id)
    {
        /** @var User $user */
        $user  = Auth::user();
        $notif = $user->notifications()->findOrFail($id);
        $notif->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('notificaciones.index');
    }
}
