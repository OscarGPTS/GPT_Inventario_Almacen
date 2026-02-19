@extends('layouts.app')

@section('title', 'Solicitudes')

@section('content')
<div class="bg-white shadow-sm rounded-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-gray-800 flex items-center">
            <svg class="h-8 w-8 mr-3 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Solicitudes
        </h2>
        <a href="{{ route('solicitudes.create') }}" class="bg-gray-800 hover:bg-gray-900 text-white font-bold py-3 px-6 rounded-lg shadow-lg transform hover:scale-105 transition">
            <svg class="inline-block h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Nueva Solicitud
        </a>
    </div>

    <!-- Filters -->
    <form method="GET" class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por folio o solicitante..." class="border border-gray-300 rounded-md px-4 py-2">
        
        <select name="estado" class="border border-gray-300 rounded-md px-4 py-2">
            <option value="">Todos los estados</option>
            <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
            <option value="aprobada" {{ request('estado') == 'aprobada' ? 'selected' : '' }}>Aprobada</option>
            <option value="entregada" {{ request('estado') == 'entregada' ? 'selected' : '' }}>Entregada</option>
            <option value="cancelada" {{ request('estado') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
        </select>

        <div class="flex gap-2">
            <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-lg shadow flex-1 transition">
                Filtrar
            </button>
            <a href="{{ route('solicitudes.index') }}" class="bg-gray-700 hover:bg-gray-800 text-white font-bold py-2 px-4 rounded-lg shadow transition">
                Limpiar
            </a>
        </div>
    </form>

    <!-- Solicitudes Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Folio</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Solicitante</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($solicitudes as $solicitud)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $solicitud->folio }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $solicitud->fecha->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $solicitud->solicitante }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">
                        {{ $solicitud->producto->codigo }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $solicitud->cantidad }} {{ $solicitud->unidadMedida->codigo }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $badgeColors = [
                                'pendiente' => 'bg-yellow-100 text-yellow-800',
                                'aprobada' => 'bg-blue-100 text-blue-800',
                                'entregada' => 'bg-green-100 text-green-800',
                                'cancelada' => 'bg-red-100 text-red-800',
                            ];
                        @endphp
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badgeColors[$solicitud->estado] }}">
                            {{ ucfirst($solicitud->estado) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('solicitudes.show', $solicitud) }}" class="text-blue-600 hover:text-blue-900">Ver</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                        No hay solicitudes registradas
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $solicitudes->links() }}
    </div>
</div>
@endsection
