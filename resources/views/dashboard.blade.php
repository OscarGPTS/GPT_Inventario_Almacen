@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <div class="bg-white overflow-hidden shadow-lg rounded-lg border-t-4 border-gray-800">
        <div class="p-6 bg-white">
            <h2 class="text-3xl font-bold text-gray-800 mb-6 flex items-center">
                <svg class="h-8 w-8 mr-3 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Panel de Control
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Total Productos -->
                <div class="bg-gray-800 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition border border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm opacity-90 font-medium">Total Productos</p>
                            <p class="text-4xl font-bold mt-2">{{ $totalProductos }}</p>
                        </div>
                        <div class="bg-white bg-opacity-10 p-4 rounded-lg">
                            <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Stock Bajo -->
                <div class="bg-white rounded-xl shadow-lg p-6 text-gray-800 transform hover:scale-105 transition border-2 border-yellow-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Stock Bajo</p>
                            <p class="text-4xl font-bold mt-2 text-yellow-600">{{ $productosStockBajo }}</p>
                        </div>
                        <div class="bg-yellow-100 p-4 rounded-lg">
                            <svg class="h-12 w-12 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Movimientos Hoy -->
                <div class="bg-gray-700 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition border border-gray-600">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm opacity-90 font-medium">Movimientos Hoy</p>
                            <p class="text-4xl font-bold mt-2">{{ $totalMovimientos }}</p>
                        </div>
                        <div class="bg-white bg-opacity-10 p-4 rounded-lg">
                            <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Productos Stock Bajo -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Productos con Stock Bajo</h3>
                <div class="space-y-3">
                    @forelse($productosStockBajoDetalle as $producto)
                    <div class="flex justify-between items-center border-b pb-2">
                        <div class="flex-1">
                            <p class="font-medium text-gray-800">{{ $producto->codigo }}</p>
                            <p class="text-sm text-gray-600">{{ Str::limit($producto->descripcion, 40) }}</p>
                        </div>
                        <span class="bg-red-100 text-red-800 text-xs font-semibold px-2 py-1 rounded">
                            {{ $producto->cantidad_fisica }} {{ $producto->unidadMedida->codigo }}
                        </span>
                    </div>
                    @empty
                    <p class="text-gray-500 text-center py-4">No hay productos con stock bajo</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Productos Próximos a Vencer -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Próximos a Vencer (30 días)</h3>
                <div class="space-y-3">
                    @forelse($productosProximosVencer as $producto)
                    <div class="flex justify-between items-center border-b pb-2">
                        <div class="flex-1">
                            <p class="font-medium text-gray-800">{{ $producto->codigo }}</p>
                            <p class="text-sm text-gray-600">{{ Str::limit($producto->descripcion, 40) }}</p>
                        </div>
                        <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2 py-1 rounded">
                            {{ $producto->fecha_vencimiento->format('d/m/Y') }}
                        </span>
                    </div>
                    @empty
                    <p class="text-gray-500 text-center py-4">No hay productos próximos a vencer</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Últimos Movimientos -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Últimos Movimientos</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($ultimosMovimientos as $movimiento)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $movimiento->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $badgeColors = [
                                        'entrada' => 'bg-green-100 text-green-800',
                                        'salida' => 'bg-red-100 text-red-800',
                                        'ajuste' => 'bg-yellow-100 text-yellow-800',
                                        'transferencia' => 'bg-blue-100 text-blue-800',
                                    ];
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badgeColors[$movimiento->tipo_movimiento] }}">
                                    {{ ucfirst($movimiento->tipo_movimiento) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $movimiento->producto->codigo }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $movimiento->cantidad }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $movimiento->usuario->name }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                No hay movimientos registrados
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
