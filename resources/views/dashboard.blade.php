@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Encabezado --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Panel de Control</h1>
            <p class="text-sm text-gray-500 mt-1">Resumen general del sistema de inventario</p>
        </div>
        <div class="text-sm text-gray-400">
            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            {{ now()->isoFormat('D [de] MMMM [de] YYYY') }}
        </div>
    </div>

    {{-- Tarjetas principales --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Productos --}}
        <div class="rounded-xl p-5 text-white shadow-lg transition hover:shadow-xl" style="background: linear-gradient(135deg, #4A568D 0%, #5d6ba3 100%);">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs opacity-90 font-medium uppercase tracking-wide mb-1">Total Productos</p>
                    <p class="text-3xl font-bold mb-1">{{ number_format($totalProductos) }}</p>
                    <p class="text-xs opacity-75">en inventario</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-lg p-2.5">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Valor Total --}}
        <div class="bg-white rounded-xl p-5 shadow border-l-4 transition hover:shadow-lg" style="border-color:#4A568D;">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Valor Inventario</p>
                    <p class="text-3xl font-bold text-gray-800 mb-1">${{ number_format($valorInventario, 2) }}</p>
                    <p class="text-xs text-gray-400">valor total estimado</p>
                </div>
                <div class="rounded-lg p-2.5" style="background-color:#eef0f8;">
                    <svg class="w-6 h-6" style="color:#4A568D;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Movimientos Hoy --}}
        <div class="bg-white rounded-xl p-5 shadow border-l-4 transition hover:shadow-lg" style="border-color:#4A568D;">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Movimientos Hoy</p>
                    <p class="text-3xl font-bold text-gray-800 mb-1">{{ number_format($totalMovimientos) }}</p>
                    <p class="text-xs text-gray-400">{{ number_format($movimientosSemana) }} esta semana</p>
                </div>
                <div class="rounded-lg p-2.5" style="background-color:#eef0f8;">
                    <svg class="w-6 h-6" style="color:#4A568D;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Solicitudes --}}
        <div class="bg-white rounded-xl p-5 shadow border-l-4 transition hover:shadow-lg" style="border-color:#4A568D;">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Solicitudes</p>
                    <p class="text-3xl font-bold text-gray-800 mb-1">{{ number_format($totalSolicitudes) }}</p>
                    <p class="text-xs text-gray-400">{{ number_format($solicitudesPendientes) }} pendientes</p>
                </div>
                <div class="rounded-lg p-2.5" style="background-color:#eef0f8;">
                    <svg class="w-6 h-6" style="color:#4A568D;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Alertas y estadísticas de secciones --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        {{-- Stock Bajo --}}
        <div class="bg-white rounded-lg p-4 shadow border-l-4 border-orange-400">
            <div class="flex items-center gap-3">
                <div class="bg-orange-50 rounded-lg p-2.5">
                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-gray-700">Stock Bajo</h3>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($productosStockBajo) }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">productos con menos de 10 unidades</p>
                </div>
            </div>
        </div>

        {{-- No Conforme --}}
        <div class="bg-white rounded-lg p-4 shadow border-l-4 border-red-400">
            <div class="flex items-center gap-3">
                <div class="bg-red-50 rounded-lg p-2.5">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-gray-700">No Conforme</h3>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($productosNoConformes) }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">productos marcados como NC</p>
                </div>
            </div>
        </div>

        {{-- Próximos a Vencer --}}
        <div class="bg-white rounded-lg p-4 shadow border-l-4 border-yellow-400">
            <div class="flex items-center gap-3">
                <div class="bg-yellow-50 rounded-lg p-2.5">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-gray-700">Próximos a Vencer</h3>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($productosProximosVencer) }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">vencen en los próximos 30 días</p>
                </div>
            </div>
        </div>

        {{-- Entradas Recientes --}}
        <div class="bg-white rounded-lg p-4 shadow border-l-4" style="border-color:#4A568D;">
            <div class="flex items-center gap-3">
                <div class="rounded-lg p-2.5" style="background-color:#eef0f8;">
                    <svg class="w-5 h-5" style="color:#4A568D;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-gray-700">Entradas Recientes</h3>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($entradasRecientes) }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">últimos 7 días</p>
                </div>
            </div>
        </div>

        {{-- Barras --}}
        <div class="bg-white rounded-lg p-4 shadow border-l-4" style="border-color:#4A568D;">
            <div class="flex items-center gap-3">
                <div class="rounded-lg p-2.5" style="background-color:#eef0f8;">
                    <svg class="w-5 h-5" style="color:#4A568D;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-gray-700">Categoría Barras</h3>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($productosBarras) }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">productos en barras</p>
                </div>
            </div>
        </div>

        {{-- Top Categorías --}}
        <div class="bg-white rounded-lg p-4 shadow border-l-4" style="border-color:#4A568D;">
            <div class="flex items-center gap-3">
                <div class="rounded-lg p-2.5" style="background-color:#eef0f8;">
                    <svg class="w-5 h-5" style="color:#4A568D;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-gray-700">Categorías Activas</h3>
                    <p class="text-2xl font-bold text-gray-900">{{ $topCategorias->count() }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">con productos registrados</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Distribución por Categorías --}}
    @if($topCategorias->count() > 0)
    <div class="bg-white rounded-xl shadow border border-gray-200 p-5">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5" style="color:#4A568D;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            Top 5 Categorías con Más Productos
        </h3>
        <div class="space-y-3">
            @foreach($topCategorias as $item)
            @php
                $porcentaje = $totalProductos > 0 ? ($item->total / $totalProductos) * 100 : 0;
            @endphp
            <div>
                <div class="flex items-center justify-between text-sm mb-1.5">
                    <span class="font-medium text-gray-700">
                        {{ $item->categoria->codigo ?? 'Sin categoría' }} — {{ $item->categoria->descripcion ?? 'N/A' }}
                    </span>
                    <span class="text-gray-900 font-semibold">{{ number_format($item->total) }} <span class="text-gray-400 text-xs font-normal">({{ number_format($porcentaje, 1) }}%)</span></span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                    <div class="h-2 rounded-full transition-all duration-500" style="background-color:#4A568D; width: {{ $porcentaje }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Tablas de actividad reciente --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Últimas Requisiciones --}}
        <div class="bg-white rounded-xl shadow border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5" style="color:#4A568D;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Últimas Requisiciones
                </h3>
                <a href="{{ route('solicitudes.index') }}" class="text-xs font-medium hover:underline" style="color:#4A568D;">Ver todas →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead style="background-color:#4A568D;">
                        <tr>
                            <th class="px-3 py-2 text-left text-white font-semibold uppercase tracking-wide">Folio</th>
                            <th class="px-3 py-2 text-left text-white font-semibold uppercase tracking-wide">Producto</th>
                            <th class="px-3 py-2 text-center text-white font-semibold uppercase tracking-wide">Cant.</th>
                            <th class="px-3 py-2 text-left text-white font-semibold uppercase tracking-wide">Depto.</th>
                            <th class="px-3 py-2 text-center text-white font-semibold uppercase tracking-wide">Estado</th>
                            <th class="px-3 py-2 text-left text-white font-semibold uppercase tracking-wide">Fecha</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($ultimasRequisiciones as $req)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-3 py-2 font-mono font-semibold whitespace-nowrap" style="color:#4A568D;">{{ $req->folio }}</td>
                            <td class="px-3 py-2 text-gray-800">{{ $req->producto->descripcion ?? 'N/A' }}</td>
                            <td class="px-3 py-2 text-center text-gray-700">{{ number_format($req->cantidad, 2) }}</td>
                            <td class="px-3 py-2 text-gray-700">{{ $req->departamento->nombre ?? 'N/A' }}</td>
                            <td class="px-3 py-2 text-center">
                                @if($req->estado === 'pendiente')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pendiente</span>
                                @elseif($req->estado === 'aprobada')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aprobada</span>
                                @elseif($req->estado === 'rechazada')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Rechazada</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ ucfirst($req->estado) }}</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-gray-600 text-xs whitespace-nowrap">{{ $req->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400 text-sm">No hay requisiciones registradas</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Últimos Movimientos --}}
        <div class="bg-white rounded-xl shadow border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5" style="color:#4A568D;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    Últimos Movimientos
                </h3>
                <a href="{{ route('movimientos.index') }}" class="text-xs font-medium hover:underline" style="color:#4A568D;">Ver todos →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead style="background-color:#4A568D;">
                        <tr>
                            <th class="px-3 py-2 text-left text-white font-semibold uppercase tracking-wide">Producto</th>
                            <th class="px-3 py-2 text-center text-white font-semibold uppercase tracking-wide">Tipo</th>
                            <th class="px-3 py-2 text-center text-white font-semibold uppercase tracking-wide">Cantidad</th>
                            <th class="px-3 py-2 text-left text-white font-semibold uppercase tracking-wide">Usuario</th>
                            <th class="px-3 py-2 text-left text-white font-semibold uppercase tracking-wide">Fecha</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($ultimosMovimientos as $mov)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-3 py-2 text-gray-800">{{ $mov->producto->descripcion ?? 'N/A' }}</td>
                            <td class="px-3 py-2 text-center">
                                @if($mov->tipo_movimiento === 'entrada')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4"/>
                                        </svg>
                                        Entrada
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                        </svg>
                                        Salida
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-center font-semibold text-gray-700">{{ number_format($mov->cantidad, 2) }}</td>
                            <td class="px-3 py-2 text-gray-600">{{ $mov->usuario->name ?? 'Sistema' }}</td>
                            <td class="px-3 py-2 text-gray-600 text-xs whitespace-nowrap">{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-400 text-sm">No hay movimientos registrados</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Buscador dinámico --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-4 py-3 flex gap-3 items-center">
            <div class="flex-1 relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" id="searchInput"
                    placeholder="Buscar por c&oacute;digo, descripci&oacute;n, ubicaci&oacute;n, factura... (Esc para limpiar)"
                    class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition"
                    autocomplete="off">
            </div>
            <div id="searchStatus" class="flex items-center gap-1.5 text-sm text-gray-400 whitespace-nowrap hidden">
                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                Buscando...
            </div>
            <span id="searchCount" class="text-xs font-semibold px-2 py-1 rounded-full hidden" style="background-color:#eef0f8;color:#4A568D;"></span>
            <button id="clearBtn" onclick="clearSearch()" class="hidden flex items-center gap-1 px-3 py-1.5 bg-gray-100 text-gray-500 text-xs rounded-lg hover:bg-gray-200 transition font-medium">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Limpiar
            </button>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto" style="max-height: calc(100vh - 280px); overflow-y: auto;">
            <table class="w-full text-xs border-collapse" style="min-width:1800px;">
                <thead class="sticky top-0 z-10">
                    <tr style="background-color:#4A568D;">
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">CODIGO</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">COMP.</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">CAT.</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">FAM.</th>
                        <th class="px-3 py-2.5 text-center text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">CONS.</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide border-r border-indigo-600" style="min-width:200px;">DESCRIPCI&Oacute;N</th>
                        <th class="px-3 py-2.5 text-center text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">UM</th>
                        <th class="px-3 py-2.5 text-right text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">ENTRADA</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">UBIC.</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">F. ENTRADA</th>
                        <th class="px-3 py-2.5 text-right text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">SALIDA</th>
                        <th class="px-3 py-2.5 text-right text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">FISICO</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">F. SALIDA</th>
                        <th class="px-3 py-2.5 text-right text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">P.U</th>
                        <th class="px-3 py-2.5 text-center text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">MXN/USD</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">FACTURA</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide border-r border-indigo-600" style="min-width:180px;">DN/NP/OBSERVACI&Oacute;N</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">F. VENCIMIENTO</th>
                        <th class="px-3 py-2.5 text-center text-white font-semibold uppercase tracking-wide whitespace-nowrap">H. SEGURIDAD</th>
                    </tr>
                </thead>
                <tbody id="tableBody" class="divide-y divide-gray-100">
                    @forelse($productos as $p)
                    <tr class="hover:bg-blue-50 transition-colors {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}" data-server-row>
                        <td class="px-3 py-2 font-mono font-semibold whitespace-nowrap">
                            <a href="{{ route('productos.show', $p->id) }}" class="hover:underline" style="color:#4A568D;">{{ $p->codigo }}</a>
                        </td>
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $p->componente->codigo ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $p->categoria->codigo ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $p->familia->codigo ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-700 text-center">{{ $p->consecutivo ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-800">{{ $p->descripcion }}</td>
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $p->unidadMedida->codigo ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-800 text-right whitespace-nowrap">{{ $p->cantidad_entrada !== null ? number_format($p->cantidad_entrada, 2) : '—' }}</td>
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $p->ubicacion->codigo ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $p->fecha_entrada ? $p->fecha_entrada->format('d/m/Y') : '—' }}</td>
                        <td class="px-3 py-2 text-gray-800 text-right whitespace-nowrap">{{ $p->cantidad_salida !== null ? number_format($p->cantidad_salida, 2) : '—' }}</td>
                        <td class="px-3 py-2 text-right whitespace-nowrap font-semibold {{ ($p->cantidad_fisica !== null && $p->cantidad_fisica < 10) ? 'text-red-600' : 'text-gray-800' }}">
                            {{ $p->cantidad_fisica !== null ? number_format($p->cantidad_fisica, 2) : '—' }}
                        </td>
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $p->fecha_salida ? $p->fecha_salida->format('d/m/Y') : '—' }}</td>
                        <td class="px-3 py-2 text-gray-800 text-right whitespace-nowrap">{{ $p->precio_unitario !== null ? number_format($p->precio_unitario, 2) : '—' }}</td>
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $p->moneda ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $p->factura ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-600">{{ $p->observaciones ?? '—' }}</td>
                        <td class="px-3 py-2 whitespace-nowrap {{ ($p->fecha_vencimiento && $p->fecha_vencimiento->lte(now()->addDays(30))) ? 'text-red-600 font-semibold' : 'text-gray-700' }}">
                            {{ $p->fecha_vencimiento ? $p->fecha_vencimiento->format('d/m/Y') : '—' }}
                        </td>
                        <td class="px-3 py-2 text-center">
                            @if($p->hoja_seguridad)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">S&iacute;</span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr data-server-row>
                        <td colspan="19" class="px-6 py-12 text-center text-gray-400 text-sm">No hay productos registrados</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginaci&oacute;n --}}
        <div id="paginationSection">
            @if($productos->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 flex items-center justify-between">
                <p class="text-xs text-gray-500">
                    Mostrando {{ $productos->firstItem() }}&ndash;{{ $productos->lastItem() }} de {{ number_format($productos->total()) }} productos
                </p>
                <div class="flex gap-1">
                    @if($productos->onFirstPage())
                        <span class="px-3 py-1 text-xs border border-gray-200 rounded text-gray-300 cursor-not-allowed">‹ Ant.</span>
                    @else
                        <a href="{{ $productos->previousPageUrl() }}" class="px-3 py-1 text-xs border border-gray-300 rounded text-gray-600 hover:bg-gray-50 transition">‹ Ant.</a>
                    @endif

                    @foreach($productos->getUrlRange(max(1, $productos->currentPage() - 2), min($productos->lastPage(), $productos->currentPage() + 2)) as $page => $url)
                        @if($page == $productos->currentPage())
                            <span class="px-3 py-1 text-xs border rounded text-white font-semibold" style="background-color:#4A568D;border-color:#4A568D;">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="px-3 py-1 text-xs border border-gray-300 rounded text-gray-600 hover:bg-gray-50 transition">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if($productos->hasMorePages())
                        <a href="{{ $productos->nextPageUrl() }}" class="px-3 py-1 text-xs border border-gray-300 rounded text-gray-600 hover:bg-gray-50 transition">Sig. ›</a>
                    @else
                        <span class="px-3 py-1 text-xs border border-gray-200 rounded text-gray-300 cursor-not-allowed">Sig. ›</span>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

</div>

<script>
const API_BASE = '/api/v1';
let debounceTimer = null;
let currentQuery = '';

const searchInput       = document.getElementById('searchInput');
const tableBody         = document.getElementById('tableBody');
const paginationSection = document.getElementById('paginationSection');
const searchStatus      = document.getElementById('searchStatus');
const searchCount       = document.getElementById('searchCount');
const clearBtn          = document.getElementById('clearBtn');

searchInput.addEventListener('input', function () {
    const q = this.value.trim();
    clearTimeout(debounceTimer);
    if (q === '') { clearSearch(); return; }
    debounceTimer = setTimeout(() => doSearch(q), 300);
});

searchInput.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') clearSearch();
});

async function doSearch(q) {
    currentQuery = q;
    searchStatus.classList.remove('hidden');
    searchCount.classList.add('hidden');
    clearBtn.classList.remove('hidden');
    paginationSection.classList.add('hidden');

    try {
        const res  = await fetch(`${API_BASE}/productos/buscar?q=${encodeURIComponent(q)}&limit=200`);
        const data = await res.json();

        if (searchInput.value.trim() !== q) return; // query cambi&oacute;

        searchStatus.classList.add('hidden');
        searchCount.classList.remove('hidden');
        searchCount.textContent = `${data.total} resultado${data.total !== 1 ? 's' : ''}`;

        renderResults(data.data || [], q);
    } catch {
        searchStatus.classList.add('hidden');
        searchCount.classList.remove('hidden');
        searchCount.textContent = 'Error al buscar';
        searchCount.style.backgroundColor = '#fee2e2';
        searchCount.style.color = '#dc2626';
    }
}

function renderResults(productos, q) {
    document.querySelectorAll('[data-server-row]').forEach(r => r.classList.add('hidden'));
    document.querySelectorAll('[data-search-row]').forEach(r => r.remove());

    if (productos.length === 0) {
        const tr = document.createElement('tr');
        tr.setAttribute('data-search-row', '');
        tr.innerHTML = `<td colspan="19" class="px-6 py-12 text-center text-gray-400 text-sm">
            Sin resultados para "<strong>${escape(q)}</strong>"
        </td>`;
        tableBody.appendChild(tr);
        return;
    }

    productos.forEach((p, i) => {
        const fisico    = p.fisico != null ? parseFloat(p.fisico) : null;
        const fisicoStr = fisico !== null ? fisico.toLocaleString('es-MX', {minimumFractionDigits: 2}) : '—';
        const puStr     = p.pu != null    ? parseFloat(p.pu).toLocaleString('es-MX', {minimumFractionDigits: 2}) : '—';
        const fisicoClass = (fisico !== null && fisico < 10) ? 'text-red-600 font-semibold' : 'text-gray-800';

        const tr = document.createElement('tr');
        tr.setAttribute('data-search-row', '');
        tr.className = (i % 2 === 0 ? 'bg-white' : 'bg-gray-50') + ' hover:bg-blue-50 transition-colors';
        tr.innerHTML = `
            <td class="px-3 py-2 font-mono font-semibold whitespace-nowrap">
                <a href="/productos/${p.id}" class="hover:underline" style="color:#4A568D;">${hl(p.codigo, q)}</a>
            </td>
            <td class="px-3 py-2 text-gray-400 whitespace-nowrap">—</td>
            <td class="px-3 py-2 text-gray-400 whitespace-nowrap">—</td>
            <td class="px-3 py-2 text-gray-400 whitespace-nowrap">—</td>
            <td class="px-3 py-2 text-gray-400 text-center">—</td>
            <td class="px-3 py-2 text-gray-800">${hl(p.descripcion || '', q)}</td>
            <td class="px-3 py-2 text-gray-700 whitespace-nowrap">${p.um || '—'}</td>
            <td class="px-3 py-2 text-gray-400 text-right whitespace-nowrap">—</td>
            <td class="px-3 py-2 text-gray-700 whitespace-nowrap">${hl(p.ubicacion || '—', q)}</td>
            <td class="px-3 py-2 text-gray-400 whitespace-nowrap">—</td>
            <td class="px-3 py-2 text-gray-400 text-right whitespace-nowrap">—</td>
            <td class="px-3 py-2 text-right whitespace-nowrap ${fisicoClass}">${fisicoStr}</td>
            <td class="px-3 py-2 text-gray-400 whitespace-nowrap">—</td>
            <td class="px-3 py-2 text-gray-800 text-right whitespace-nowrap">${puStr}</td>
            <td class="px-3 py-2 text-gray-400 whitespace-nowrap">—</td>
            <td class="px-3 py-2 text-gray-400 whitespace-nowrap">—</td>
            <td class="px-3 py-2 text-gray-400">—</td>
            <td class="px-3 py-2 text-gray-400 whitespace-nowrap">—</td>
            <td class="px-3 py-2 text-center text-gray-400">—</td>
        `;
        tableBody.appendChild(tr);
    });
}

function clearSearch() {
    searchInput.value = '';
    currentQuery = '';
    clearTimeout(debounceTimer);
    document.querySelectorAll('[data-server-row]').forEach(r => r.classList.remove('hidden'));
    document.querySelectorAll('[data-search-row]').forEach(r => r.remove());
    paginationSection.classList.remove('hidden');
    searchStatus.classList.add('hidden');
    searchCount.classList.add('hidden');
    searchCount.style.backgroundColor = '#eef0f8';
    searchCount.style.color = '#4A568D';
    clearBtn.classList.add('hidden');
    searchInput.focus();
}

function hl(text, q) {
    if (!text || !q) return xss(text || '');
    return xss(text).replace(new RegExp(`(${xssReg(q)})`, 'gi'), '<mark class="bg-yellow-200 px-0.5 rounded">$1</mark>');
}
function xss(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function xssReg(s) { return s.replace(/[.*+?^${}()|[\]\\]/g,'\\$&'); }
function escape(s) { return xss(s); }
</script>
@endsection

