<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Solicitud;
use App\Models\Departamento;
use App\Models\Producto;
use App\Models\UnidadMedida;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SolicitudApiController extends Controller
{
    /**
     * Listar solicitudes
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = Solicitud::with(['departamento', 'producto', 'unidadMedida', 'usuarioRegistro']);

            // Filtros opcionales
            if ($request->filled('estado')) {
                $query->where('estado', $request->estado);
            }

            if ($request->filled('prioridad')) {
                $query->where('prioridad', $request->prioridad);
            }

            if ($request->filled('usuario_id')) {
                $query->where('usuario_registro_id', $request->usuario_id);
            }

            if ($request->filled('search')) {
                $s = $request->search;
                $query->where(function ($q) use ($s) {
                    $q->where('folio', 'like', "%{$s}%")
                      ->orWhere('solicitante', 'like', "%{$s}%")
                      ->orWhereHas('producto', fn($pq) => $pq->where('codigo', 'like', "%{$s}%")->orWhere('descripcion', 'like', "%{$s}%"));
                });
            }

            // Ordenar por fecha descendente
            $query->orderBy('fecha', 'desc')->orderBy('id', 'desc');

            // Paginación   
            $perPage = $request->get('per_page', 15);
            $solicitudes = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $solicitudes->items(),
                'pagination' => [
                    'total' => $solicitudes->total(),
                    'per_page' => $solicitudes->perPage(),
                    'current_page' => $solicitudes->currentPage(),
                    'last_page' => $solicitudes->lastPage(),
                    'from' => $solicitudes->firstItem(),
                    'to' => $solicitudes->lastItem(),
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error al listar solicitudes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las solicitudes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear nueva solicitud desde frontend externo
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Detectar si viene desde app móvil (user_id + email) o desde web (usuario_registro_id)
            $fromMobile = $request->has('user_id') && $request->has('email');

            if ($fromMobile) {
                // === FLUJO PARA APP MÓVIL ===
                
                // Validación específica para app móvil
                $validator = Validator::make($request->all(), [
                    'user_id' => 'required|integer|exists:users,id',
                    'email' => 'required|email',
                    'producto_id' => 'required|integer|exists:productos,id',
                    'cantidad' => 'required|numeric|min:0.01',
                    'observaciones' => 'nullable|string',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Error de validación',
                        'errors' => $validator->errors()
                    ], 422);
                }

                // Consultar API de RH para obtener información del usuario
                $rhUserData = $this->getUserFromRH($request->email);

                if (!$rhUserData) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No se pudo obtener información del usuario desde el sistema de RH',
                    ], 404);
                }

                // Verificar que el usuario existe localmente
                $usuario = User::find($request->user_id);
                if (!$usuario) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Usuario no encontrado en el sistema local'
                    ], 404);
                }

                // Manejar el departamento desde la API de RH
                $departamentoRH = $rhUserData['departamento'] ?? null;
                
                if (!$departamentoRH) {
                    return response()->json([
                        'success' => false,
                        'message' => 'El usuario no tiene departamento asignado en el sistema de RH',
                    ], 422);
                }

                // Buscar o crear el departamento por nombre
                $departamento = Departamento::firstOrCreate(
                    ['nombre' => $departamentoRH['nombre']],
                    ['codigo' => strtoupper(substr($departamentoRH['nombre'], 0, 3))]
                );
                
                Log::info("Departamento para solicitud: ID {$departamento->id}, Nombre: {$departamento->nombre}");

                // Obtener el producto y su unidad de medida
                $producto = Producto::with('unidadMedida')->find($request->producto_id);
                
                if (!$producto) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Producto no encontrado'
                    ], 404);
                }

                // Preparar datos para crear la solicitud
                $solicitudData = [
                    'usuario_registro_id' => $request->user_id,
                    'fecha' => now()->format('Y-m-d'),
                    'fecha_requerida' => now()->addDays(7)->format('Y-m-d'), // 7 días por defecto
                    'solicitante' => $rhUserData['nombre_completo'] ?? $usuario->name,
                    'departamento_id' => $departamento->id,
                    'producto_id' => $request->producto_id,
                    'cantidad' => $request->cantidad,
                    'unidad_medida_id' => $producto->unidad_medida_id,
                    'observaciones' => $request->observaciones,
                    'estado' => 'pendiente',
                    'prioridad' => 'normal',
                ];

            } else {
                // === FLUJO ORIGINAL PARA WEB ===
                
                // Validación de datos
                $validator = Validator::make($request->all(), [
                    'usuario_registro_id' => 'required|integer|exists:users,id',
                    'fecha' => 'required|date',
                    'fecha_requerida' => 'nullable|date',
                    'folio' => 'nullable|string|max:50',
                    'solicitante' => 'required|string|max:255',
                    'departamento_id' => 'nullable|integer|exists:departamentos,id',
                    'departamento_nombre' => 'nullable|string|max:255',
                    'producto_id' => 'required|integer|exists:productos,id',
                    'cantidad' => 'required|numeric|min:0.01',
                    'unidad_medida_id' => 'nullable|integer|exists:unidades_medida,id',
                    'observaciones' => 'nullable|string',
                    'estado' => 'nullable|in:pendiente,aprobada,entregada,cancelada',
                    'prioridad' => 'nullable|in:urgente,alta,normal,baja',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Error de validación',
                        'errors' => $validator->errors()
                    ], 422);
                }

                $solicitudData = $validator->validated();

                // Verificar que el usuario existe
                $usuario = User::find($solicitudData['usuario_registro_id']);
                if (!$usuario) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Usuario no encontrado'
                    ], 404);
                }

                // Manejar departamento: crear si se proporciona nombre y no existe ID
                if (empty($solicitudData['departamento_id']) && !empty($solicitudData['departamento_nombre'])) {
                    $departamento = Departamento::firstOrCreate(
                        ['nombre' => $solicitudData['departamento_nombre']],
                        ['codigo' => strtoupper(substr($solicitudData['departamento_nombre'], 0, 3))]
                    );
                    $solicitudData['departamento_id'] = $departamento->id;
                }

                // Si no se proporciona departamento, validar que existe
                if (empty($solicitudData['departamento_id'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Debe proporcionar departamento_id o departamento_nombre'
                    ], 422);
                }

                // Verificar que el producto existe y obtener su unidad de medida si no se proporciona
                $producto = Producto::with('unidadMedida')->find($solicitudData['producto_id']);
                if (!$producto) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Producto no encontrado'
                    ], 404);
                }

                // Si no se proporciona unidad de medida, usar la del producto
                if (empty($solicitudData['unidad_medida_id']) && $producto->unidad_medida_id) {
                    $solicitudData['unidad_medida_id'] = $producto->unidad_medida_id;
                }

                // Valores por defecto
                $solicitudData['estado'] = $solicitudData['estado'] ?? 'pendiente';
                $solicitudData['prioridad'] = $solicitudData['prioridad'] ?? 'normal';

                // Remover campos que no están en fillable
                unset($solicitudData['departamento_nombre']);
            }

            // Crear la solicitud (común para ambos flujos)
            $solicitud = Solicitud::create($solicitudData);

            // Cargar relaciones
            $solicitud->load(['departamento', 'producto', 'unidadMedida', 'usuarioRegistro']);

            Log::info("Solicitud creada desde API " . ($fromMobile ? "(App Móvil)" : "(Web)") . ": ID {$solicitud->id}, Usuario ID {$solicitudData['usuario_registro_id']}, Producto ID {$solicitudData['producto_id']}");

            return response()->json([
                'success' => true,
                'message' => 'Solicitud creada exitosamente',
                'data' => [
                    'id' => $solicitud->id,
                    'folio' => $solicitud->folio,
                    'fecha' => $solicitud->fecha->format('Y-m-d'),
                    'fecha_requerida' => $solicitud->fecha_requerida?->format('Y-m-d'),
                    'solicitante' => $solicitud->solicitante,
                    'departamento' => [
                        'id' => $solicitud->departamento->id,
                        'nombre' => $solicitud->departamento->nombre,
                        'codigo' => $solicitud->departamento->codigo,
                    ],
                    'producto' => [
                        'id' => $solicitud->producto->id,
                        'codigo' => $solicitud->producto->codigo,
                        'descripcion' => $solicitud->producto->descripcion,
                    ],
                    'cantidad' => $solicitud->cantidad,
                    'unidad_medida' => $solicitud->unidadMedida ? [
                        'id' => $solicitud->unidadMedida->id,
                        'codigo' => $solicitud->unidadMedida->codigo,
                        'nombre' => $solicitud->unidadMedida->nombre,
                    ] : null,
                    'observaciones' => $solicitud->observaciones,
                    'estado' => $solicitud->estado,
                    'prioridad' => $solicitud->prioridad,
                    'usuario_registro' => [
                        'id' => $solicitud->usuarioRegistro->id,
                        'name' => $solicitud->usuarioRegistro->name,
                        'email' => $solicitud->usuarioRegistro->email,
                    ],
                    'created_at' => $solicitud->created_at->format('Y-m-d H:i:s'),
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error al crear solicitud desde API: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la solicitud',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Consultar información de usuario desde API de RH externa
     * 
     * @param string $email
     * @return array|null
     */
    private function getUserFromRH($email)
    {
        try {
            $response = Http::timeout(10)->post('https://services.satechenergy.com/api/rh/users/buscar-por-email', [
                'email' => $email
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['success'] ?? false) {
                    return $data['data'] ?? null;
                }
            }

            Log::warning("No se pudo obtener información de RH para el email: {$email}. Status: {$response->status()}");
            return null;

        } catch (\Exception $e) {
            Log::error("Error al consultar API de RH: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Obtener una solicitud específica
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $solicitud = Solicitud::with(['departamento', 'producto', 'unidadMedida', 'usuarioRegistro', 'movimientos'])
                ->find($id);

            if (!$solicitud) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solicitud no encontrada'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $solicitud->id,
                    'folio' => $solicitud->folio,
                    'fecha' => $solicitud->fecha->format('Y-m-d'),
                    'fecha_requerida' => $solicitud->fecha_requerida?->format('Y-m-d'),
                    'solicitante' => $solicitud->solicitante,
                    'departamento' => [
                        'id' => $solicitud->departamento->id,
                        'nombre' => $solicitud->departamento->nombre,
                        'codigo' => $solicitud->departamento->codigo,
                    ],
                    'producto' => [
                        'id' => $solicitud->producto->id,
                        'codigo' => $solicitud->producto->codigo,
                        'descripcion' => $solicitud->producto->descripcion,
                    ],
                    'cantidad' => $solicitud->cantidad,
                    'unidad_medida' => $solicitud->unidadMedida ? [
                        'id' => $solicitud->unidadMedida->id,
                        'codigo' => $solicitud->unidadMedida->codigo,
                        'nombre' => $solicitud->unidadMedida->nombre,
                    ] : null,
                    'observaciones' => $solicitud->observaciones,
                    'estado' => $solicitud->estado,
                    'prioridad' => $solicitud->prioridad,
                    'usuario_registro' => [
                        'id' => $solicitud->usuarioRegistro->id,
                        'name' => $solicitud->usuarioRegistro->name,
                        'email' => $solicitud->usuarioRegistro->email,
                    ],
                    'movimientos' => $solicitud->movimientos->map(fn($m) => [
                        'id' => $m->id,
                        'tipo' => $m->tipo,
                        'cantidad' => $m->cantidad,
                        'fecha' => $m->fecha->format('Y-m-d'),
                    ]),
                    'created_at' => $solicitud->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $solicitud->updated_at->format('Y-m-d H:i:s'),
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error al obtener solicitud: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la solicitud',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar estado de una solicitud
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateEstado(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'estado' => 'required|in:pendiente,aprobada,entregada,cancelada',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $solicitud = Solicitud::find($id);

            if (!$solicitud) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solicitud no encontrada'
                ], 404);
            }

            $solicitud->estado = $request->estado;
            $solicitud->save();

            Log::info("Estado de solicitud actualizado desde API: ID {$solicitud->id}, Nuevo estado: {$request->estado}");

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado exitosamente',
                'data' => [
                    'id' => $solicitud->id,
                    'estado' => $solicitud->estado,
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error al actualizar estado de solicitud: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estado',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
