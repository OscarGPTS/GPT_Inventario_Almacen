<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\TicketCompletedMail;
use App\Models\Survey;
use App\Models\Ticket;
use App\Models\TicketImage;
use App\Models\User;
use App\Notifications\SurveyCompletedNotification;
use App\Notifications\TicketAssignedNotification;
use App\Notifications\TicketAssignedToWarehouseNotification;
use App\Notifications\TicketCompletedNotification;
use App\Notifications\TicketPendingApprovalNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

/**
 * MobileController — API para la app móvil de Solicitudes de Movimiento
 *
 * Endpoints base: /api/mobile/...
 *
 * Roles del sistema:
 *   - solicitante  : usuario de la app móvil que crea solicitudes
 *   - almacenista  : personal de almacén que atiende/completa solicitudes
 *   - admin        : administrador con acceso total
 *   - admin_almacen: administrador de almacén
 */
class MobileController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // POST /api/mobile/auth/login
    // Login o registro de usuario con Firebase UID
    // ─────────────────────────────────────────────────────────────────────────
    public function loginOrRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'        => 'required|email',
            'name'         => 'required|string|max:255',
            'firebase_uid' => 'nullable|string',
            'avatar'       => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                $user = User::create([
                    'name'              => $request->name,
                    'email'             => $request->email,
                    'provider_id'       => $request->firebase_uid,
                    'provider'          => 'firebase',
                    'avatar'            => $request->avatar,
                    'email_verified_at' => now(),
                    'last_login_at'     => now(),
                ]);

                $user->addRole('visitante');

                Log::info("Mobile: nuevo usuario creado {$user->email} (ID: {$user->id})");
            } else {
                $updateData = ['last_login_at' => now()];

                if ($user->name !== $request->name) {
                    $updateData['name'] = $request->name;
                }
                if ($request->avatar) {
                    $updateData['avatar'] = $request->avatar;
                }
                if ($request->firebase_uid && !$user->provider_id) {
                    $updateData['provider_id'] = $request->firebase_uid;
                    $updateData['provider']    = 'firebase';
                }

                $user->update($updateData);

                Log::info("Mobile: login exitoso {$user->email} (ID: {$user->id})");
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $user->wasRecentlyCreated ? 'Usuario registrado exitosamente' : 'Inicio de sesión exitoso',
                'data'    => [
                    'usuario' => $this->formatUser($user),
                    'es_nuevo' => $user->wasRecentlyCreated,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Mobile loginOrRegister: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la solicitud',
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // POST /api/mobile/tickets
    // Obtener tickets activos según el rol del usuario (por email)
    // ─────────────────────────────────────────────────────────────────────────
    public function getTickets(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Email inválido',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado en el sistema',
            ], 404);
        }

        $query = Ticket::query();

        if ($user->hasRole('visitante')) {
            // Visitante (solicitante móvil): sus tickets en proceso/pendientes + finalizados sin calificar
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->whereIn('status', [Ticket::STATUS_EN_PROCESO, Ticket::STATUS_PENDIENTE])
                  ->orWhere(function ($sub) use ($user) {
                      $sub->where('user_id', $user->id)
                          ->where('status', Ticket::STATUS_FINALIZADO)
                          ->where(function ($s) {
                              $s->whereDoesntHave('survey')
                                ->orWhereHas('survey', fn($sq) => $sq->whereNull('completed_at'));
                          });
                  });
            });
        } else {
            // Almacenista / admin: solo pendientes y en proceso
            $query->whereIn('status', [Ticket::STATUS_EN_PROCESO, Ticket::STATUS_PENDIENTE]);
        }

        $tickets = $query->with([
                'user:id,name,email',
                'assignedTo:id,name,email',
                'solicitudImages:id,ticket_id,file_path',
                'evidenciaImages:id,ticket_id,file_path',
                'survey',
            ])
            ->orderByRaw("CASE
                WHEN status = 'en_proceso' THEN 0
                WHEN status = 'pendiente'  THEN 1
                WHEN status = 'finalizado' THEN 2
                ELSE 3 END")
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Tickets obtenidos correctamente',
            'data'    => [
                'usuario' => $this->formatUser($user),
                'tickets' => $tickets->map(fn($t) => $this->formatTicket($t)),
                'estadisticas' => [
                    'total'                      => $tickets->count(),
                    'en_proceso'                 => $tickets->where('status', Ticket::STATUS_EN_PROCESO)->count(),
                    'pendientes'                 => $tickets->where('status', Ticket::STATUS_PENDIENTE)->count(),
                    'finalizados_sin_calificar'  => $tickets->where('status', Ticket::STATUS_FINALIZADO)
                        ->filter(fn($t) => !$t->survey || !$t->survey->completed_at)->count(),
                ],
            ],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // POST /api/mobile/tickets/create
    // Crear nueva solicitud de movimiento desde la app (solicitante)
    // ─────────────────────────────────────────────────────────────────────────
    public function createTicket(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'       => 'required|exists:users,id',
            'titulo'        => 'required|string|max:255',
            'descripcion'   => 'required|string',
            'imagenes'      => 'nullable|array|max:5',
            'imagenes.*'    => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = User::find($request->user_id);

        if (!$user || !$user->hasRole('visitante')) {
            return response()->json([
                'success' => false,
                'message' => 'Solo usuarios con rol de visitante pueden crear solicitudes',
            ], 403);
        }

        DB::beginTransaction();
        try {
            $ticket = Ticket::create([
                'user_id'     => $user->id,
                'title'       => $request->titulo,
                'description' => $request->descripcion,
                'status'      => Ticket::STATUS_PENDIENTE,
            ]);

            $imagenesGuardadas = [];
            if ($request->hasFile('imagenes')) {
                $yearMonth = now()->format('Y/m');
                foreach ($request->file('imagenes') as $image) {
                    $path = $image->store('tickets/' . $yearMonth . '/' . $ticket->id . '/solicitud', 'public');
                    $ti = TicketImage::create([
                        'ticket_id'     => $ticket->id,
                        'uploaded_by'   => $user->id,
                        'file_path'     => $path,
                        'original_name' => $image->getClientOriginalName(),
                        'mime_type'     => $image->getMimeType(),
                        'file_size'     => $image->getSize(),
                        'type'          => TicketImage::TYPE_SOLICITUD,
                    ]);
                    $imagenesGuardadas[] = ['id' => $ti->id, 'path' => $ti->file_path];
                }
            }

            // Notificar a admins y admin_almacen
            try {
                $admins = User::role(['admin', 'admin_almacen'])->get();
                if ($admins->isNotEmpty()) {
                    Notification::send($admins, new TicketPendingApprovalNotification($ticket));
                    Log::info("Mobile: notificados {$admins->count()} admins para ticket #{$ticket->id}");
                }
            } catch (\Exception $e) {
                Log::error("Mobile createTicket notificaciones: " . $e->getMessage());
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Solicitud creada exitosamente. El equipo de almacén ha sido notificado.',
                'data'    => [
                    'ticket' => [
                        'id'          => $ticket->id,
                        'codigo'      => $ticket->formatted_code,
                        'titulo'      => $ticket->title,
                        'descripcion' => $ticket->description,
                        'status'      => $ticket->status,
                        'status_texto' => $ticket->status_text,
                        'created_at'  => $ticket->created_at->format('Y-m-d H:i:s'),
                        'solicitante' => $this->formatUser($user),
                        'imagenes'    => $imagenesGuardadas,
                    ],
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Mobile createTicket: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al crear la solicitud: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /api/mobile/almacen-users
    // Obtener lista de almacenistas disponibles para asignación
    // ─────────────────────────────────────────────────────────────────────────
    public function getAlmacenUsers()
    {
        try {
            // Incluye almacenista, admin_almacen y admin (todos pueden atender tickets)
            $users = User::role(['almacenista', 'admin_almacen', 'admin'])
                ->orderBy('name')
                ->get(['id', 'name', 'email', 'avatar']);

            return response()->json([
                'success' => true,
                'message' => 'Usuarios de almacén obtenidos correctamente',
                'data'    => [
                    'usuarios' => $users->map(fn($u) => [
                        'id'     => $u->id,
                        'nombre' => $u->name,
                        'email'  => $u->email,
                        'avatar' => $u->avatar,
                    ]),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener usuarios: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // POST /api/mobile/tickets/assign
    // Asignar un ticket a un almacenista
    // ─────────────────────────────────────────────────────────────────────────
    public function assignTicket(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ticket_id'   => 'required|exists:tickets,id',
            'assigned_to' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $ticket = Ticket::with(['user', 'assignedTo'])->findOrFail($request->ticket_id);

        if ($ticket->status === Ticket::STATUS_CANCELADO) {
            return response()->json(['success' => false, 'message' => 'No se puede asignar un ticket cancelado'], 400);
        }
        if ($ticket->status === Ticket::STATUS_FINALIZADO) {
            return response()->json(['success' => false, 'message' => 'Este ticket ya ha sido finalizado'], 400);
        }

        $assignedUser = User::findOrFail($request->assigned_to);

        if (!$assignedUser->hasRole(['almacenista', 'admin_almacen', 'admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Solo puedes asignar tickets a personal de almacén',
            ], 403);
        }

        DB::beginTransaction();
        try {
            $ticket->assignTo($assignedUser);

            // Notificar al solicitante que su ticket fue asignado
            try {
                $ticket->user->notify(new TicketAssignedNotification($ticket->fresh(['user', 'assignedTo'])));
            } catch (\Exception $e) {
                Log::error("Mobile assignTicket notif solicitante: " . $e->getMessage());
            }

            // Notificar al almacenista asignado
            try {
                $assignedUser->notify(new TicketAssignedToWarehouseNotification($ticket->fresh(['user', 'assignedTo'])));
            } catch (\Exception $e) {
                Log::error("Mobile assignTicket notif almacen: " . $e->getMessage());
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Ticket asignado exitosamente a ' . $assignedUser->name,
                'data'    => [
                    'ticket_id'   => $ticket->id,
                    'codigo'      => $ticket->formatted_code,
                    'status'      => $ticket->status,
                    'assigned_at' => $ticket->assigned_at?->format('Y-m-d H:i:s'),
                    'asignado_a'  => [
                        'id'     => $assignedUser->id,
                        'nombre' => $assignedUser->name,
                        'email'  => $assignedUser->email,
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al asignar el ticket: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // POST /api/mobile/tickets/complete
    // Completar un ticket (almacenista/admin)
    // ─────────────────────────────────────────────────────────────────────────
    public function completeTicket(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ticket_id'   => 'required|exists:tickets,id',
            'user_id'     => 'required|exists:users,id',
            'descripcion' => 'required|string',
            'imagen'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $ticket      = Ticket::with('user')->findOrFail($request->ticket_id);
        $almacenUser = User::findOrFail($request->user_id);

        if (!$almacenUser->hasRole(['almacenista', 'admin_almacen', 'admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Solo personal de almacén puede completar tickets',
            ], 403);
        }

        if ($ticket->status === Ticket::STATUS_CANCELADO) {
            return response()->json(['success' => false, 'message' => 'No se puede completar un ticket cancelado'], 400);
        }

        DB::beginTransaction();
        try {
            // Reasignar si aún no está asignado a este usuario
            if ($ticket->assigned_to !== $almacenUser->id) {
                $ticket->assignTo($almacenUser);
            }

            $ticket->update([
                'work_evidence' => $request->descripcion,
                'status'        => Ticket::STATUS_FINALIZADO,
                'completed_at'  => now(),
            ]);

            // Guardar imagen de evidencia si se adjuntó
            if ($request->hasFile('imagen')) {
                $image     = $request->file('imagen');
                $yearMonth = $ticket->created_at->format('Y/m');
                $path      = $image->store('tickets/' . $yearMonth . '/' . $ticket->id . '/evidence', 'public');
                TicketImage::create([
                    'ticket_id'     => $ticket->id,
                    'uploaded_by'   => $almacenUser->id,
                    'file_path'     => $path,
                    'original_name' => $image->getClientOriginalName(),
                    'mime_type'     => $image->getMimeType(),
                    'file_size'     => $image->getSize(),
                    'type'          => TicketImage::TYPE_EVIDENCIA,
                ]);
            }

            // Crear encuesta de satisfacción pendiente
            Survey::firstOrCreate(
                ['ticket_id' => $ticket->id],
                ['user_id' => $ticket->user_id, 'rating' => 0, 'comments' => null]
            );

            $ticket->refresh();

            // Notificar al solicitante (correo + DB)
            try {
                Mail::to($ticket->user->email)->send(new TicketCompletedMail($ticket));
                $ticket->user->notify(new TicketCompletedNotification($ticket->load('assignedTo')));
            } catch (\Exception $e) {
                Log::error("Mobile completeTicket notif: " . $e->getMessage());
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Ticket completado exitosamente',
                'data'    => [
                    'ticket_id'    => $ticket->id,
                    'codigo'       => $ticket->formatted_code,
                    'status'       => $ticket->status,
                    'completed_at' => $ticket->completed_at->format('Y-m-d H:i:s'),
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al completar el ticket: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // POST /api/mobile/tickets/completed
    // Obtener tickets finalizados con paginación y filtro por mes/año
    // ─────────────────────────────────────────────────────────────────────────
    public function getCompletedTickets(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'  => 'required|exists:users,id',
            'mes'      => 'nullable|integer|min:1|max:12',
            'anio'     => 'nullable|integer|min:2020|max:2100',
            'page'     => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:5|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user    = User::findOrFail($request->user_id);
        $mes     = $request->mes    ?? now()->month;
        $anio    = $request->anio   ?? now()->year;
        $perPage = $request->per_page ?? 15;

        $query = Ticket::where('status', Ticket::STATUS_FINALIZADO)
            ->whereMonth('completed_at', $mes)
            ->whereYear('completed_at', $anio)
            ->with([
                'user:id,name,email',
                'assignedTo:id,name,email',
                'solicitudImages:id,ticket_id,file_path',
                'evidenciaImages:id,ticket_id,file_path',
                'survey',
            ]);

        if ($user->hasRole('admin')) {
            // Admin: todos los tickets finalizados
        } elseif ($user->hasRole(['almacenista', 'admin_almacen'])) {
            $query->where('assigned_to', $user->id);
        } elseif ($user->hasRole('visitante')) {
            $query->where('user_id', $user->id);
        } else {
            return response()->json(['success' => false, 'message' => 'Rol no válido'], 403);
        }

        $tickets     = $query->orderBy('completed_at', 'desc')->paginate($perPage);
        $statsTickets = (clone $query)->get();

        return response()->json([
            'success' => true,
            'message' => 'Tickets finalizados obtenidos correctamente',
            'data'    => [
                'usuario' => $this->formatUser($user),
                'filtro'  => [
                    'mes'       => $mes,
                    'anio'      => $anio,
                    'mes_nombre' => \Carbon\Carbon::create($anio, $mes)->locale('es')->translatedFormat('F Y'),
                ],
                'tickets' => $tickets->map(fn($t) => array_merge($this->formatTicket($t), [
                    'encuesta' => $t->survey ? [
                        'id'          => $t->survey->id,
                        'rating'      => $t->survey->rating,
                        'comentarios' => $t->survey->comments,
                        'completada'  => (bool) $t->survey->completed_at,
                        'completed_at' => $t->survey->completed_at?->format('Y-m-d H:i:s'),
                    ] : null,
                ])),
                'paginacion' => [
                    'total'           => $tickets->total(),
                    'por_pagina'      => $tickets->perPage(),
                    'pagina_actual'   => $tickets->currentPage(),
                    'ultima_pagina'   => $tickets->lastPage(),
                    'tiene_mas_paginas' => $tickets->hasMorePages(),
                ],
                'estadisticas' => [
                    'total_finalizados'       => $statsTickets->count(),
                    'con_encuesta_completada' => $statsTickets->filter(fn($t) => $t->survey?->completed_at)->count(),
                    'promedio_calificacion'   => round(
                        $statsTickets->filter(fn($t) => $t->survey?->completed_at && $t->survey->rating > 0)
                            ->avg(fn($t) => $t->survey->rating) ?? 0, 1
                    ),
                ],
            ],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // POST /api/mobile/surveys/complete
    // Calificar un ticket finalizado (solicitante)
    // ─────────────────────────────────────────────────────────────────────────
    public function completeSurvey(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ticket_id' => 'required|exists:tickets,id',
            'user_id'   => 'required|exists:users,id',
            'rating'    => 'required|integer|min:1|max:5',
            'comments'  => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user   = User::findOrFail($request->user_id);
        $ticket = Ticket::with(['user', 'assignedTo', 'survey'])->findOrFail($request->ticket_id);

        if (!$user->hasRole('visitante')) {
            return response()->json(['success' => false, 'message' => 'Solo visitantes pueden calificar tickets'], 403);
        }

        if ($ticket->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Este ticket no te pertenece'], 403);
        }

        if ($ticket->status !== Ticket::STATUS_FINALIZADO) {
            return response()->json(['success' => false, 'message' => 'Solo puedes calificar tickets finalizados'], 400);
        }

        $survey = $ticket->survey;

        if (!$survey) {
            return response()->json(['success' => false, 'message' => 'No existe encuesta para este ticket'], 404);
        }

        if ($survey->completed_at) {
            return response()->json([
                'success' => false,
                'message' => 'Esta encuesta ya ha sido completada',
                'data'    => [
                    'completed_at' => $survey->completed_at->format('Y-m-d H:i:s'),
                    'rating'       => $survey->rating,
                    'comments'     => $survey->comments,
                ],
            ], 400);
        }

        DB::beginTransaction();
        try {
            $survey->update([
                'rating'       => $request->rating,
                'comments'     => $request->comments,
                'completed_at' => now(),
            ]);

            // Notificar a admins y admin_almacen
            try {
                $admins = User::role(['admin', 'admin_almacen'])->get();
                if ($admins->isNotEmpty()) {
                    Notification::send($admins, new SurveyCompletedNotification($survey->fresh(['ticket.user'])));
                }
            } catch (\Exception $e) {
                Log::error("Mobile completeSurvey notif: " . $e->getMessage());
            }

            DB::commit();
            $survey->refresh();

            return response()->json([
                'success' => true,
                'message' => '¡Gracias por tu calificación!',
                'data'    => [
                    'encuesta' => [
                        'id'           => $survey->id,
                        'ticket_id'    => $ticket->id,
                        'ticket_codigo' => $ticket->formatted_code,
                        'rating'       => $survey->rating,
                        'comments'     => $survey->comments,
                        'completed_at' => $survey->completed_at?->format('Y-m-d H:i:s'),
                    ],
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al completar la encuesta: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers privados
    // ─────────────────────────────────────────────────────────────────────────

    private function formatUser(User $user): array
    {
        return [
            'id'     => $user->id,
            'nombre' => $user->name,
            'email'  => $user->email,
            'avatar' => $user->avatar,
            'rol'    => $user->getRoleNames()->first(),
            'roles'  => $user->getRoleNames()->values(),
        ];
    }

    private function formatTicket(Ticket $ticket): array
    {
        $calificado = false;
        if ($ticket->status === Ticket::STATUS_FINALIZADO) {
            $calificado = $ticket->survey && $ticket->survey->completed_at !== null;
        }

        return [
            'id'           => $ticket->id,
            'codigo'       => $ticket->formatted_code,
            'titulo'       => $ticket->title,
            'descripcion'  => $ticket->description,
            'status'       => $ticket->status,
            'status_texto' => $ticket->status_text,
            'calificado'   => $calificado,
            'work_evidence' => $ticket->work_evidence,
            'created_at'   => $ticket->created_at->format('Y-m-d H:i:s'),
            'assigned_at'  => $ticket->assigned_at?->format('Y-m-d H:i:s'),
            'completed_at' => $ticket->completed_at?->format('Y-m-d H:i:s'),
            'solicitante'  => $ticket->user ? [
                'id'     => $ticket->user->id,
                'nombre' => $ticket->user->name,
                'email'  => $ticket->user->email,
            ] : null,
            'asignado_a'   => $ticket->assignedTo ? [
                'id'     => $ticket->assignedTo->id,
                'nombre' => $ticket->assignedTo->name,
                'email'  => $ticket->assignedTo->email,
            ] : null,
            'imagenes_solicitud' => $ticket->solicitudImages->map(fn($i) => [
                'id'   => $i->id,
                'path' => $i->file_path,
            ])->values(),
            'imagenes_evidencia' => $ticket->evidenciaImages->map(fn($i) => [
                'id'   => $i->id,
                'path' => $i->file_path,
            ])->values(),
        ];
    }
}
