@extends('layouts.app')

@section('title', 'Detalle del Producto')

@section('content')
<div class="bg-white shadow-sm rounded-lg p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-gray-800 flex items-center">
            <svg class="h-8 w-8 mr-3 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            Producto: {{ $producto->codigo }}
        </h2>
        <div class="flex gap-3">
            <a href="{{ url()->previous() }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg shadow transition">
                <svg class="inline-block h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver
            </a>
            <a href="{{ route('productos.edit', $producto) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow transition">
                <svg class="inline-block h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Editar
            </a>
        </div>
    </div>

    <!-- Información Principal -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Columna Izquierda -->
        <div class="space-y-4">
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-4 rounded-lg border border-blue-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                    <svg class="h-5 w-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    Identificación
                </h3>
                <div class="space-y-2">
                    <div>
                        <span class="text-sm font-medium text-gray-600">Código:</span>
                        <p class="text-lg font-bold text-gray-900">{{ $producto->codigo }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-600">Descripción:</span>
                        <p class="text-gray-900">{{ $producto->descripcion }}</p>
                    </div>
                    @if($producto->numero_parte)
                    <div>
                        <span class="text-sm font-medium text-gray-600">Número de Parte:</span>
                        <p class="text-gray-900">{{ $producto->numero_parte }}</p>
                    </div>
                    @endif
                    @if($producto->dimensiones)
                    <div>
                        <span class="text-sm font-medium text-gray-600">Dimensiones:</span>
                        <p class="text-gray-900">{{ $producto->dimensiones }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <div class="bg-gradient-to-r from-green-50 to-green-100 p-4 rounded-lg border border-green-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                    <svg class="h-5 w-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    Clasificación
                </h3>
                <div class="space-y-2">
                    @if($producto->componente)
                    <div>
                        <span class="text-sm font-medium text-gray-600">Componente:</span>
                        <p class="text-gray-900 font-semibold">{{ $producto->componente->codigo }} - {{ $producto->componente->nombre }}</p>
                    </div>
                    @endif
                    @if($producto->categoria)
                    <div>
                        <span class="text-sm font-medium text-gray-600">Categoría:</span>
                        <p class="text-gray-900 font-semibold">{{ $producto->categoria->codigo }} - {{ $producto->categoria->descripcion }}</p>
                    </div>
                    @endif
                    @if($producto->familia)
                    <div>
                        <span class="text-sm font-medium text-gray-600">Familia:</span>
                        <p class="text-gray-900 font-semibold">{{ $producto->familia->codigo }} - {{ $producto->familia->descripcion }}</p>
                    </div>
                    @endif
                    @if($producto->consecutivo)
                    <div>
                        <span class="text-sm font-medium text-gray-600">Consecutivo:</span>
                        <p class="text-gray-900 font-mono">{{ $producto->consecutivo }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Columna Derecha -->
        <div class="space-y-4">
            <div class="bg-gradient-to-r from-purple-50 to-purple-100 p-4 rounded-lg border border-purple-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                    <svg class="h-5 w-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    Inventario
                </h3>
                <div class="space-y-2">
                    <div>
                        <span class="text-sm font-medium text-gray-600">Cantidad Entrada:</span>
                        <p class="text-2xl font-bold text-green-600">{{ number_format($producto->cantidad_entrada, 2) }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-600">Cantidad Física:</span>
                        <p class="text-2xl font-bold text-blue-600">{{ number_format($producto->cantidad_fisica, 2) }}</p>
                    </div>
                    @if($producto->cantidad_salida)
                    <div>
                        <span class="text-sm font-medium text-gray-600">Cantidad Salida:</span>
                        <p class="text-2xl font-bold text-red-600">{{ number_format($producto->cantidad_salida, 2) }}</p>
                    </div>
                    @endif
                    @if($producto->unidadMedida)
                    <div>
                        <span class="text-sm font-medium text-gray-600">Unidad de Medida:</span>
                        <p class="text-gray-900">{{ $producto->unidadMedida->codigo }}</p>
                    </div>
                    @endif
                    @if($producto->ubicacion)
                    <div>
                        <span class="text-sm font-medium text-gray-600">Ubicación:</span>
                        <p class="text-gray-900 font-semibold">{{ $producto->ubicacion->codigo }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 p-4 rounded-lg border border-yellow-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                    <svg class="h-5 w-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Información Económica
                </h3>
                <div class="space-y-2">
                    @if($producto->precio_unitario)
                    <div>
                        <span class="text-sm font-medium text-gray-600">Precio Unitario:</span>
                        <p class="text-xl font-bold text-gray-900">
                            ${{ number_format($producto->precio_unitario, 2) }} 
                            @if($producto->moneda)
                            <span class="text-sm text-gray-600">{{ $producto->moneda }}</span>
                            @endif
                        </p>
                    </div>
                    @endif
                    @if($producto->factura)
                    <div>
                        <span class="text-sm font-medium text-gray-600">Factura:</span>
                        <p class="text-gray-900">{{ $producto->factura }}</p>
                    </div>
                    @endif
                    @if($producto->orden_compra)
                    <div>
                        <span class="text-sm font-medium text-gray-600">Orden de Compra:</span>
                        <p class="text-gray-900">{{ $producto->orden_compra }}</p>
                    </div>
                    @endif
                    @if($producto->numero_requisicion)
                    <div>
                        <span class="text-sm font-medium text-gray-600">Número de Requisición:</span>
                        <p class="text-gray-900">{{ $producto->numero_requisicion }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Fechas e Información Adicional -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-gradient-to-r from-indigo-50 to-indigo-100 p-4 rounded-lg border border-indigo-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                <svg class="h-5 w-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Fechas
            </h3>
            <div class="space-y-2">
                @if($producto->fecha_entrada)
                <div>
                    <span class="text-sm font-medium text-gray-600">Fecha de Entrada:</span>
                    <p class="text-gray-900">{{ $producto->fecha_entrada->format('d/m/Y') }}</p>
                </div>
                @endif
                @if($producto->fecha_salida)
                <div>
                    <span class="text-sm font-medium text-gray-600">Fecha de Salida:</span>
                    <p class="text-gray-900">{{ $producto->fecha_salida->format('d/m/Y') }}</p>
                </div>
                @endif
                @if($producto->fecha_vencimiento)
                <div>
                    <span class="text-sm font-medium text-gray-600">Fecha de Vencimiento:</span>
                    <p class="text-gray-900">{{ $producto->fecha_vencimiento->format('d/m/Y') }}</p>
                </div>
                @endif
            </div>
        </div>

        @if($producto->observaciones)
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 p-4 rounded-lg border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                <svg class="h-5 w-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Observaciones
            </h3>
            <p class="text-gray-700">{{ $producto->observaciones }}</p>
        </div>
        @endif
    </div>

    <!-- No Conforme (si aplica) -->
    @if($producto->no_conforme)
    <div class="bg-gradient-to-r from-red-50 to-red-100 p-4 rounded-lg border border-red-200 mb-8">
        <h3 class="text-lg font-semibold text-red-800 mb-3 flex items-center">
            <svg class="h-5 w-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            Producto No Conforme
        </h3>
        <div class="space-y-2">
            @if($producto->fecha_nc)
            <div>
                <span class="text-sm font-medium text-red-700">Fecha No Conforme:</span>
                <p class="text-red-900">{{ $producto->fecha_nc->format('d/m/Y') }}</p>
            </div>
            @endif
            @if($producto->observacion_nc)
            <div>
                <span class="text-sm font-medium text-red-700">Observación:</span>
                <p class="text-red-900">{{ $producto->observacion_nc }}</p>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Historial de Movimientos -->
    @if($movimientos && $movimientos->count() > 0)
    <div class="bg-white border border-gray-200 rounded-lg p-4">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <svg class="h-5 w-5 mr-2 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
            </svg>
            Historial de Movimientos
        </h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Usuario</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Notas</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($movimientos as $movimiento)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            {{ $movimiento->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            @if($movimiento->tipo === 'entrada')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Entrada</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Salida</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ number_format($movimiento->cantidad, 2) }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                            {{ $movimiento->usuario->name ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            {{ $movimiento->notas ?? '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $movimientos->links() }}
        </div>
    </div>
    @endif
</div>
@endsection
