<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketImage;
use App\Models\User;
use App\Models\Producto;
use App\Models\Survey;
use App\Models\Solicitud;
use App\Models\Departamento;
use App\Models\UnidadMedida;
use App\Models\Movimiento;
use App\Mail\TicketCreatedMail;
use App\Mail\TicketAssignedMail;
use App\Mail\TicketCompletedMail;
use App\Mail\TicketSurveyMail;
use App\Notifications\TicketPendingApprovalNotification;
use App\Notifications\TicketAssignedNotification;
use App\Notifications\TicketAssignedToWarehouseNotification;
use App\Notifications\TicketCompletedNotification;
use App\Notifications\SurveyCompletedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    /**
     * Listado de tickets — todos los roles ven los suyos, admin/admin_almacen ven todos
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $esGestor = $user->hasRole(['admin', 'admin_almacen']);

        $query = Ticket::with(['user', 'assignedTo', 'solicitudImages', 'producto']);

        if (!$esGestor) {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%")
                  ->orWhere('id', $s);
            });
        }

        $query->orderByRaw("CASE WHEN status = 'pendiente' THEN 0 WHEN status = 'en_proceso' THEN 1 ELSE 2 END")
              ->orderByDesc('created_at');

        $tickets = $query->paginate(20)->appends($request->except('page'));

        // Estadísticas
        $baseQuery = $esGestor ? Ticket::query() : Ticket::where('user_id', $user->id);
        $stats = [
            'total'      => (clone $baseQuery)->count(),
            'pendiente'  => (clone $baseQuery)->where('status', 'pendiente')->count(),
            'en_proceso' => (clone $baseQuery)->where('status', 'en_proceso')->count(),
            'finalizado' => (clone $baseQuery)->where('status', 'finalizado')->count(),
            'cancelado'  => (clone $baseQuery)->where('status', 'cancelado')->count(),
        ];

        // Usuarios almacenistas para asignación (solo gestores)
        $almacenUsers = $esGestor
            ? User::whereHasRole(['almacenista', 'admin_almacen'])->orderBy('name')->get()
            : null;

        return view('tickets.index', compact('tickets', 'stats', 'almacenUsers'));
    }

    /**
     * Formulario de creación
     */
    public function create(Request $request)
    {
        $producto = null;
        if ($request->filled('producto_id')) {
            $producto = Producto::find($request->producto_id);
        }
        return view('tickets.create', compact('producto'));
    }

    /**
     * Guardar nuevo ticket
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'producto_id' => 'nullable|exists:productos,id',
            'images'      => 'nullable|array|max:5',
            'images.*'    => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $ticket = Ticket::create([
                'user_id'      => Auth::id(),
                'producto_id'  => $request->producto_id,
                'title'        => $request->title,
                'description'  => $request->description,
                'status'       => Ticket::STATUS_PENDIENTE,
            ]);

            if ($request->hasFile('images')) {
                $yearMonth = now()->format('Y/m');
                foreach ($request->file('images') as $image) {
                    $path = $image->store("tickets/{$yearMonth}/{$ticket->id}", 'public');

                    TicketImage::create([
                        'ticket_id'     => $ticket->id,
                        'uploaded_by'   => Auth::id(),
                        'file_path'     => $path,
                        'original_name' => $image->getClientOriginalName(),
                        'mime_type'     => $image->getMimeType(),
                        'file_size'     => $image->getSize(),
                        'type'          => TicketImage::TYPE_SOLICITUD,
                    ]);
                }
            }

            DB::commit();

            // Notificar a admins/admin_almacen (correo + base de datos)
            $ticket->load(['user', 'producto']);
            $admins = User::whereHasRole(['admin', 'admin_almacen'])->get();
            foreach ($admins as $admin) {
                Mail::to($admin->email)->send(new TicketCreatedMail($ticket));
            }
            Notification::send($admins, new TicketPendingApprovalNotification($ticket));

            return redirect()->route('solicitudes.concentrado', ['tab' => 'movimiento'])
                ->with('success', 'Solicitud creada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al crear la solicitud.'])->withInput();
        }
    }

    /**
     * Ver detalle
     */
    public function show(Ticket $ticket)
    {
        $user = Auth::user();
        $esGestor = $user->hasRole(['admin', 'admin_almacen']);

        if (!$esGestor && $ticket->user_id !== $user->id && $ticket->assigned_to !== $user->id) {
            abort(403);
        }

        $ticket->load(['user', 'assignedTo', 'images', 'producto', 'survey']);

        $almacenUsers = $esGestor
            ? User::whereHasRole(['almacenista', 'admin_almacen'])->orderBy('name')->get()
            : null;

        return view('tickets.show', compact('ticket', 'almacenUsers'));
    }

    /**
     * JSON endpoint — todos los datos del ticket para el modal en concentrado
     */
    public function detalle(Ticket $ticket): \Illuminate\Http\JsonResponse
    {
        $user          = Auth::user();
        $esGestor      = $user->hasRole(['admin', 'admin_almacen']);
        $esAlmacenista = $user->hasRole(['almacenista', 'admin_almacen']);
        $esDueno       = $ticket->user_id === $user->id;

        if (!$esGestor && $ticket->user_id !== $user->id && $ticket->assigned_to !== $user->id) {
            abort(403);
        }

        $ticket->load(['user', 'assignedTo', 'solicitudImages', 'evidenciaImages', 'producto', 'survey']);

        $almacenUsers = $esGestor
            ? User::whereHasRole(['almacenista', 'admin_almacen'])->orderBy('name')->get()
                ->map(fn($u) => ['id' => $u->id, 'name' => $u->name])->values()
            : [];

        return response()->json([
            'ticket' => [
                'id'                  => $ticket->id,
                'formatted_code'      => $ticket->formatted_code,
                'title'               => $ticket->title,
                'description'         => $ticket->description,
                'status'              => $ticket->status,
                'status_text'         => $ticket->status_text,
                'status_badge_class'  => $ticket->status_badge_class,
                'created_at'          => $ticket->created_at->format('d/m/Y H:i'),
                'assigned_at'         => $ticket->assigned_at?->format('d/m/Y H:i'),
                'completed_at'        => $ticket->completed_at?->format('d/m/Y H:i'),
                'cancelled_at'        => $ticket->cancelled_at?->format('d/m/Y H:i'),
                'work_evidence'       => $ticket->work_evidence,
                'cancellation_reason' => $ticket->cancellation_reason,
                'user'                => $ticket->user
                    ? ['id' => $ticket->user->id, 'name' => $ticket->user->name] : null,
                'assignedTo'          => $ticket->assignedTo
                    ? ['id' => $ticket->assignedTo->id, 'name' => $ticket->assignedTo->name] : null,
                'producto'            => $ticket->producto ? [
                    'id'          => $ticket->producto->id,
                    'codigo'      => $ticket->producto->codigo,
                    'descripcion' => $ticket->producto->descripcion,
                ] : null,
                'solicitudImages' => $ticket->solicitudImages
                    ->map(fn($img) => ['url' => $img->url, 'original_name' => $img->original_name])
                    ->values(),
                'evidenciaImages' => $ticket->evidenciaImages
                    ->map(fn($img) => ['url' => $img->url, 'original_name' => $img->original_name])
                    ->values(),
                'survey' => $ticket->survey ? [
                    'rating'       => $ticket->survey->rating,
                    'comments'     => $ticket->survey->comments,
                    'completed_at' => $ticket->survey->completed_at?->format('d/m/Y H:i'),
                ] : null,
            ],
            'permissions' => [
                'esDueno'            => $esDueno,
                'esGestor'           => $esGestor,
                'esAlmacenista'      => $esAlmacenista,
                'canDelete'          => $esDueno && $ticket->isPendiente(),
                'canCancel'          => ($esDueno || $esGestor) && $ticket->canBeCancelled(),
                'canUpdateProducto'  => $esGestor && !in_array($ticket->status, ['finalizado', 'cancelado']),
            ],
            'almacenUsers' => $almacenUsers,
            'urls' => [
                'assign'   => route('tickets.assign',   $ticket),
                'complete' => route('tickets.complete', $ticket),
                'cancel'   => route('tickets.cancel',   $ticket),
                'survey'   => route('tickets.survey',   $ticket),
                'destroy'  => route('tickets.destroy',  $ticket),
                'show'           => route('tickets.show',     $ticket),
                'update_producto' => route('tickets.updateProducto', $ticket),
                'producto'       => $ticket->producto
                    ? route('productos.show', $ticket->producto_id) : null,
            ],
        ]);
    }

    /**
     * Asignar ticket a un almacenista
     */
    public function assign(Request $request, Ticket $ticket)
    {
        $user = Auth::user();
        $esGestor = $user->hasRole(['admin', 'admin_almacen']);

        if (!$esGestor && !$user->hasRole('almacenista')) {
            abort(403);
        }

        if (in_array($ticket->status, ['finalizado', 'cancelado'])) {
            if ($request->wantsJson()) {
                return response()->json(['ok' => false, 'error' => 'Este ticket ya no puede asignarse.'], 422);
            }
            return back()->with('error', 'Este ticket ya no puede asignarse.');
        }

        if ($esGestor) {
            $request->validate(['assigned_to' => 'required|exists:users,id']);
            $assignee = User::findOrFail($request->assigned_to);
        } else {
            $assignee = $user;
        }

        $ticket->assignTo($assignee);

        // Notificar al solicitante y al almacenista asignado
        $ticket->load(['user', 'producto', 'assignedTo']);
        Mail::to($ticket->user->email)->send(new TicketAssignedMail($ticket));
        $ticket->user->notify(new TicketAssignedNotification($ticket));
        if ($assignee->id !== $ticket->user_id) {
            $assignee->notify(new TicketAssignedToWarehouseNotification($ticket));
        }

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'message' => 'Ticket asignado a ' . $assignee->name]);
        }
        return back()->with('success', 'Ticket asignado a ' . $assignee->name);
    }

    /**
     * Completar ticket con evidencia
     */
    public function complete(Request $request, Ticket $ticket)
    {
        $user = Auth::user();

        if (!$user->hasRole(['admin', 'admin_almacen', 'almacenista'])) {
            abort(403);
        }

        if (in_array($ticket->status, ['finalizado', 'cancelado'])) {
            if ($request->wantsJson()) {
                return response()->json(['ok' => false, 'error' => 'Este ticket ya fue cerrado.'], 422);
            }
            return back()->with('error', 'Este ticket ya fue cerrado.');
        }

        $request->validate([
            'work_evidence'   => 'required|string|max:5000',
            'evidence_images' => 'nullable|array|max:5',
            'evidence_images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $ticket->complete($request->work_evidence);

            // Registrar movimiento de transferencia completada
            if ($ticket->producto) {
                Movimiento::create([
                    'producto_id'      => $ticket->producto_id,
                    'usuario_id'       => Auth::id(),
                    'tipo_movimiento'  => 'transferencia',
                    'cantidad'         => 1,
                    'cantidad_anterior'=> $ticket->producto->cantidad_fisica ?? 0,
                    'cantidad_nueva'   => $ticket->producto->cantidad_fisica ?? 0,
                    'ticket_id'        => $ticket->id,
                    'descripcion'      => "Solicitud de movimiento completada: {$ticket->title}",
                    'referencia'       => $ticket->formatted_code,
                    'fuente'           => 'solicitud_movimiento',
                ]);
            }

            if ($request->hasFile('evidence_images')) {
                $yearMonth = $ticket->created_at->format('Y/m');
                foreach ($request->file('evidence_images') as $image) {
                    $path = $image->store("tickets/{$yearMonth}/{$ticket->id}/evidence", 'public');

                    TicketImage::create([
                        'ticket_id'     => $ticket->id,
                        'uploaded_by'   => Auth::id(),
                        'file_path'     => $path,
                        'original_name' => $image->getClientOriginalName(),
                        'mime_type'     => $image->getMimeType(),
                        'file_size'     => $image->getSize(),
                        'type'          => TicketImage::TYPE_EVIDENCIA,
                    ]);
                }
            }

            DB::commit();

            // Notificar al solicitante que el ticket fue completado
            $ticket->load(['user', 'assignedTo', 'producto']);
            Mail::to($ticket->user->email)->send(new TicketCompletedMail($ticket));
            $ticket->user->notify(new TicketCompletedNotification($ticket));

            if ($request->wantsJson()) {
                return response()->json(['ok' => true, 'message' => 'Ticket completado exitosamente.']);
            }
            return redirect()->route('tickets.show', $ticket)
                ->with('success', 'Ticket completado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json(['ok' => false, 'error' => 'Error al completar el ticket.'], 500);
            }
            return back()->with('error', 'Error al completar el ticket.');
        }
    }

    /**
     * Guardar encuesta de satisfacción
     */
    public function survey(Request $request, Ticket $ticket)
    {
        $user = Auth::user();

        // Solo el dueño del ticket puede responder la encuesta
        if ($ticket->user_id !== $user->id) {
            abort(403);
        }

        if (!$ticket->isFinalizado()) {
            return back()->with('error', 'El ticket aún no ha sido completado.');
        }

        if ($ticket->survey) {
            return back()->with('error', 'Ya has respondido la encuesta de este ticket.');
        }

        $request->validate([
            'rating'   => 'required|integer|min:1|max:5',
            'comments' => 'nullable|string|max:1000',
        ]);

        Survey::create([
            'ticket_id'    => $ticket->id,
            'user_id'      => $user->id,
            'rating'       => $request->rating,
            'comments'     => $request->comments,
            'completed_at' => now(),
        ]);

        // Notificar a admins sobre la encuesta
        $ticket->load(['user', 'assignedTo']);
        $surveyRecord = $ticket->survey()->latest()->first();
        $admins = User::whereHasRole(['admin', 'admin_almacen'])->get();
        foreach ($admins as $admin) {
            Mail::to($admin->email)->send(new TicketSurveyMail($ticket, $request->rating, $request->comments));
        }
        if ($surveyRecord) {
            Notification::send($admins, new SurveyCompletedNotification($surveyRecord->load('ticket.user')));
        }

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'message' => 'Gracias por tu retroalimentación.']);
        }
        return back()->with('success', 'Gracias por tu retroalimentación.');
    }

    /**
     * Cambiar estado de un ticket (AJAX inline desde el concentrado).
     * PATCH /tickets/{ticket}/cambiar-status
     */
    public function updateStatus(Request $request, Ticket $ticket)
    {
        if (!Auth::user()->hasRole(['admin', 'admin_almacen'])) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:pendiente,en_proceso,finalizado,cancelado',
        ]);

        $nuevo    = $request->status;
        $anterior = $ticket->status;

        if ($nuevo === $anterior) {
            return response()->json(['ok' => true, 'status' => $nuevo]);
        }

        // Bloquear cambios desde estados terminales
        if (in_array($anterior, ['finalizado', 'cancelado'])) {
            return response()->json(['ok' => false, 'error' => 'No se puede cambiar el estado de un ticket ' . $anterior . '.'], 422);
        }

        if ($nuevo === 'finalizado') {
            $ticket->complete('Marcado como finalizado desde panel administrativo');
        } elseif ($nuevo === 'cancelado') {
            $ticket->cancel('Cancelado desde panel administrativo');
        } else {
            $ticket->status = $nuevo;
            $ticket->save();
        }

        return response()->json(['ok' => true, 'status' => $nuevo]);
    }

    /**
     * Cancelar ticket
     */
    public function cancel(Request $request, Ticket $ticket)
    {
        $user = Auth::user();

        // Dueño o gestor
        if ($ticket->user_id !== $user->id && !$user->hasRole(['admin', 'admin_almacen'])) {
            abort(403);
        }

        if (!$ticket->canBeCancelled()) {
            if ($request->wantsJson()) {
                return response()->json(['ok' => false, 'error' => 'Este ticket no puede cancelarse en su estado actual.'], 422);
            }
            return back()->with('error', 'Este ticket no puede cancelarse en su estado actual.');
        }

        $request->validate([
            'cancellation_reason' => 'nullable|string|max:500',
        ]);

        $ticket->cancel($request->cancellation_reason ?: 'Cancelado por el usuario');

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'message' => 'Ticket cancelado.']);
        }
        return redirect()->route('solicitudes.concentrado', ['tab' => 'movimiento'])
            ->with('success', 'Ticket cancelado.');
    }

    /**
     * Eliminar ticket (solo dueño y solo si pendiente)
     */
    public function destroy(Ticket $ticket)
    {
        if ($ticket->user_id !== Auth::id() || !$ticket->isPendiente()) {
            abort(403);
        }

        foreach ($ticket->images as $image) {
            Storage::disk('public')->delete($image->file_path);
        }

        $ticket->delete();

        if (request()->wantsJson()) {
            return response()->json(['ok' => true, 'message' => 'Solicitud eliminada.']);
        }
        return redirect()->route('solicitudes.concentrado', ['tab' => 'movimiento'])
            ->with('success', 'Solicitud eliminada.');
    }

    /**
     * Actualizar (o agregar) el producto de un ticket
     */
    public function updateProducto(Request $request, Ticket $ticket)
    {
        $user     = Auth::user();
        $esGestor = $user->hasRole(['admin', 'admin_almacen']);

        if (!$esGestor) abort(403);

        if (in_array($ticket->status, ['finalizado', 'cancelado'])) {
            if ($request->wantsJson()) {
                return response()->json(['ok' => false, 'error' => 'No se puede modificar un ticket en estado terminal.'], 422);
            }
            return back()->with('error', 'No se puede modificar un ticket en estado terminal.');
        }

        $request->validate(['producto_id' => 'nullable|exists:productos,id']);

        $ticket->producto_id = $request->filled('producto_id') ? $request->producto_id : null;
        $ticket->save();

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'message' => 'Producto actualizado correctamente.']);
        }
        return back()->with('success', 'Producto actualizado.');
    }

    /**
     * Vista unificada: Requisiciones de Material + Solicitudes de Movimiento
     */
    public function concentrado(Request $request)
    {
        $user     = Auth::user();
        $esGestor = $user->hasRole(['admin', 'admin_almacen']);
        $tab      = $request->get('tab', 'todos');
        $search   = $request->get('search', '');

        // ── Estadísticas globales ────────────────────────────────────────────
        $baseSol = $esGestor ? Solicitud::query() : Solicitud::where('usuario_registro_id', $user->id);
        $baseTck = $esGestor ? Ticket::query()    : Ticket::where('user_id', $user->id);

        $stats = [
            'sol' => [
                'total'     => (clone $baseSol)->count(),
                'pendiente' => (clone $baseSol)->where('estado', 'pendiente')->count(),
                'aprobada'  => (clone $baseSol)->where('estado', 'aprobada')->count(),
                'entregada' => (clone $baseSol)->where('estado', 'entregada')->count(),
                'cancelada' => (clone $baseSol)->where('estado', 'cancelada')->count(),
            ],
            'tck' => [
                'total'      => (clone $baseTck)->count(),
                'pendiente'  => (clone $baseTck)->where('status', 'pendiente')->count(),
                'en_proceso' => (clone $baseTck)->where('status', 'en_proceso')->count(),
                'finalizado' => (clone $baseTck)->where('status', 'finalizado')->count(),
                'cancelado'  => (clone $baseTck)->where('status', 'cancelado')->count(),
            ],
        ];

        // ── Solicitudes de Material ──────────────────────────────────────────
        $solicitudes = null;
        $recentSol   = null;

        $buildSolQ = function () use ($user, $esGestor, $search, $request) {
            $q = Solicitud::with(['departamento', 'producto', 'unidadMedida', 'usuarioRegistro'])
                ->orderBy('fecha', 'desc')->orderBy('id', 'desc');
            if (!$esGestor) $q->where('usuario_registro_id', $user->id);
            if ($search) {
                $s = $search;
                $q->where(function ($sq) use ($s) {
                    $sq->where('folio', 'like', "%{$s}%")
                       ->orWhere('solicitante', 'like', "%{$s}%")
                       ->orWhereHas('producto', fn ($pq) => $pq->where('codigo', 'like', "%{$s}%")->orWhere('descripcion', 'like', "%{$s}%"))
                       ->orWhereHas('departamento', fn ($dq) => $dq->where('nombre', 'like', "%{$s}%"));
                });
            }
            if ($request->filled('estado_mat')) $q->where('estado', $request->estado_mat);
            if ($request->filled('prioridad'))  $q->where('prioridad', $request->prioridad);
            return $q;
        };

        if ($tab === 'material') {
            $solicitudes = $buildSolQ()->paginate(20)->appends($request->except('page'));
        } else {
            $recentSol = $buildSolQ()->paginate(10, ['*'], 'page_sol')->appends($request->except('page_sol'));
        }

        // ── Tickets de Movimiento ────────────────────────────────────────────
        $tickets   = null;
        $recentTck = null;

        $buildTckQ = function () use ($user, $esGestor, $search, $request) {
            $q = Ticket::with(['user', 'assignedTo', 'solicitudImages', 'producto'])
                ->orderByRaw("CASE WHEN status='pendiente' THEN 0 WHEN status='en_proceso' THEN 1 ELSE 2 END")
                ->orderByDesc('created_at');
            if (!$esGestor) $q->where('user_id', $user->id);
            if ($search) {
                $s = $search;
                $q->where(function ($sq) use ($s) {
                    $sq->where('title', 'like', "%{$s}%")
                       ->orWhere('description', 'like', "%{$s}%");
                    if (is_numeric($s)) $sq->orWhere('id', $s);
                });
            }
            if ($request->filled('status_tck')) $q->where('status', $request->status_tck);
            return $q;
        };

        if ($tab === 'movimiento') {
            $tickets = $buildTckQ()->paginate(20)->appends($request->except('page'));
        } else {
            $recentTck = $buildTckQ()->paginate(10, ['*'], 'page_tck')->appends($request->except('page_tck'));
        }

        // ── Catálogos para modal nueva requisición ───────────────────────────
        $departamentos  = Departamento::orderBy('nombre')->get();
        $unidadesMedida = UnidadMedida::orderBy('codigo')->get();
        $almacenUsers   = $esGestor
            ? User::whereHasRole(['almacenista', 'admin_almacen'])->orderBy('name')->get()
            : null;

        return view('solicitudes.concentrado', compact(
            'solicitudes', 'tickets', 'stats', 'tab', 'esGestor', 'almacenUsers',
            'recentSol', 'recentTck', 'departamentos', 'unidadesMedida', 'search'
        ));
    }
}
