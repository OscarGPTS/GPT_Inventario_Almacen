@extends('layouts.app')

@section('title', 'Movimientos')

@section('content')
<div class="bg-white shadow-sm rounded-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-gray-800 flex items-center">
            <svg class="h-8 w-8 mr-3 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
            </svg>
            Historial de Movimientos
        </h2>
    </div>

    <!-- Filters -->
    <form method="GET" class="mb-6 grid grid-cols-1  md:grid-cols-4 gap-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar producto..." class="border border-gray-300 rounded-md px-4 py-2">
        
        <select name="tipo_movimiento" class="border border-gray-300 rounded-md px-4 py-2">
            <option value="">Todos los tipos</option>
            <option value="entrada" {{ request('tipo_movimiento') == 'entrada' ? 'selected' : '' }}>Entrada</option>
            <option value="salida" {{ request('tipo_movimiento') == 'salida' ? 'selected' : '' }}>Salida</option>
            <option value="ajuste" {{ request('tipo_movimiento') == 'ajuste' ? 'selected' : '' }}>Ajuste</option>
            <option value="transferencia" {{ request('tipo_movimiento') == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
        </select>

        <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" class="border border-gray-300 rounded-md px-4 py-2" placeholder="Desde">
        <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" class="border border-gray-300 rounded-md px-4 py-2" placeholder="Hasta">
    </form>

    <div class="flex gap-2 mb-6">
        <button type="submit" form="filter-form" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-lg shadow transition">
            Filtrar
        </button>
        <a href="{{ route('movimientos.index') }}" class="bg-gray-700 hover:bg-gray-800 text-white font-bold py-2 px-4 rounded-lg shadow transition">
            Limpiar
        </a>
    </div>

    <!-- Movimientos Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Anterior</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Nuevo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($movimientos as $movimiento)
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
                        <a href="{{ route('productos.show', $movimiento->producto) }}" class="text-blue-600 hover:text-blue-900">
                            {{ $movimiento->producto->codigo }}
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $movimiento->tipo_movimiento == 'entrada' ? 'text-green-600' : 'text-red-600' }}">
                        {{ $movimiento->tipo_movimiento == 'entrada' ? '+' : '-' }}{{ $movimiento->cantidad }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $movimiento->cantidad_anterior }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                        {{ $movimiento->cantidad_nueva }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $movimiento->usuario->name }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">
                        {{ Str::limit($movimiento->descripcion, 40) }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                        No hay movimientos registrados
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $movimientos->links() }}
    </div>
</div>
@endsection
