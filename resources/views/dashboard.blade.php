@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
    $acento   = '#4A568D';
    $acentoBg = '#eef0f8';
    $teal     = '#0d7a6b';
    $tealBg   = '#e6f4f2';
@endphp

<div class="space-y-6">

    {{-- ══════════════════════════════════════
         ENCABEZADO
    ══════════════════════════════════════ --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Panel de Control</h1>
            <p class="text-sm text-gray-500 mt-0.5">
                Bienvenido, <strong>{{ auth()->user()->name }}</strong> &mdash;
                {{ now()->isoFormat('dddd D [de] MMMM [de] YYYY') }}
            </p>
        </div>
        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('reportes.entradas') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium text-white shadow-sm transition hover:opacity-90"
               style="background-color:{{ $acento }};">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                Inventario
            </a>
            <a href="{{ route('solicitudes.concentrado') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium text-white shadow-sm transition hover:opacity-90"
               style="background-color:{{ $teal }};">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Solicitudes
            </a>
            @if($esAdmin)
            <a href="{{ route('tickets.index') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium border border-gray-300 text-gray-700 bg-white shadow-sm hover:bg-gray-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
                Gestionar Movimientos
            </a>
            @endif
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="px-4 py-3 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- ══════════════════════════════════════
         KPIs INVENTARIO
    ══════════════════════════════════════ --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">

        <div class="col-span-2 sm:col-span-1 rounded-xl p-4 text-white shadow-lg" style="background: linear-gradient(135deg, {{ $acento }} 0%, #5d6ba3 100%);">
            <p class="text-xs opacity-80 font-medium uppercase tracking-wide mb-1">Productos</p>
            <p class="text-3xl font-bold">{{ number_format($totalProductos) }}</p>
            <p class="text-xs opacity-70 mt-1">en inventario</p>
        </div>

        <div class="bg-white rounded-xl p-4 shadow border-t-4" style="border-color:{{ $acento }};">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Valor</p>
            <p class="text-2xl font-bold text-gray-800">${{ number_format($valorInventario / 1000, 1) }}K</p>
            <p class="text-xs text-gray-400 mt-1">MXN estimado</p>
        </div>

        <div class="bg-white rounded-xl p-4 shadow border-t-4 border-indigo-300">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Movimientos</p>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($totalMovimientos) }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ number_format($movimientosSemana) }} esta semana</p>
        </div>

        <div class="bg-white rounded-xl p-4 shadow border-t-4 border-orange-400">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Stock Bajo</p>
            <p class="text-2xl font-bold {{ $productosStockBajo > 0 ? 'text-orange-600' : 'text-gray-800' }}">{{ number_format($productosStockBajo) }}</p>
            <p class="text-xs text-gray-400 mt-1">&lt; 10 unidades</p>
        </div>

        <div class="bg-white rounded-xl p-4 shadow border-t-4 border-red-400">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">No Conforme</p>
            <p class="text-2xl font-bold {{ $productosNoConformes > 0 ? 'text-red-600' : 'text-gray-800' }}">{{ number_format($productosNoConformes) }}</p>
            <p class="text-xs text-gray-400 mt-1">piezas marcadas</p>
        </div>

        <div class="bg-white rounded-xl p-4 shadow border-t-4 border-yellow-400">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Por Vencer</p>
            <p class="text-2xl font-bold {{ $productosProximosVencer > 0 ? 'text-yellow-600' : 'text-gray-800' }}">{{ number_format($productosProximosVencer) }}</p>
            <p class="text-xs text-gray-400 mt-1">pr&oacute;ximos 30 d&iacute;as</p>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         KPIs TICKETS / SOLICITUDES MOVIMIENTO
    ══════════════════════════════════════ --}}
    <div>
        <div class="flex items-center gap-2 mb-3">
            <svg class="w-4 h-4" style="color:{{ $teal }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
            </svg>
            <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wide">Solicitudes de Movimiento</h2>
            <a href="{{ route('tickets.index') }}" class="ml-auto text-xs font-medium hover:underline" style="color:{{ $teal }}">Ver todas &rarr;</a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">

            <a href="{{ route('tickets.index', ['status' => 'pendiente']) }}"
               class="bg-white rounded-xl p-4 shadow border-l-4 border-yellow-400 flex items-center gap-3 hover:shadow-md transition group">
                <div class="bg-yellow-50 rounded-lg p-2.5 group-hover:bg-yellow-100 transition">
                    <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Pendientes</p>
                    <p class="text-2xl font-bold {{ $ticketsPendientes > 0 ? 'text-yellow-600' : 'text-gray-800' }}">{{ number_format($ticketsPendientes) }}</p>
                </div>
                @if($ticketsPendientes > 0)
                <span class="ml-auto w-2 h-2 rounded-full bg-yellow-400 animate-pulse"></span>
                @endif
            </a>

            <a href="{{ route('tickets.index', ['status' => 'en_proceso']) }}"
               class="bg-white rounded-xl p-4 shadow border-l-4 border-blue-400 flex items-center gap-3 hover:shadow-md transition group">
                <div class="bg-blue-50 rounded-lg p-2.5 group-hover:bg-blue-100 transition">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">En Proceso</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($ticketsEnProceso) }}</p>
                </div>
            </a>

            <a href="{{ route('tickets.index', ['status' => 'finalizado']) }}"
               class="bg-white rounded-xl p-4 shadow border-l-4 border-green-400 flex items-center gap-3 hover:shadow-md transition group">
                <div class="bg-green-50 rounded-lg p-2.5 group-hover:bg-green-100 transition">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Finalizados</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($ticketsFinalizados) }}</p>
                </div>
            </a>

            <div class="bg-white rounded-xl p-4 shadow border-l-4 border-gray-300 flex items-center gap-3">
                <div class="bg-gray-50 rounded-lg p-2.5">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Cancelados</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($ticketsCancelados) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         PANEL ADMIN: ASIGNAR / SEGUIMIENTO
    ══════════════════════════════════════ --}}
    @if($esAdmin)
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

        {{-- Tickets pendientes de asignación --}}
        <div class="bg-white rounded-xl shadow border border-gray-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between" style="background-color:{{ $tealBg }};">
                <h3 class="text-sm font-bold flex items-center gap-2" style="color:{{ $teal }};">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Pendientes de asignaci&oacute;n
                    @if($ticketsPendientes > 0)
                    <span class="ml-1 bg-yellow-100 text-yellow-700 text-xs font-semibold px-2 py-0.5 rounded-full">{{ $ticketsPendientes }}</span>
                    @endif
                </h3>
                <a href="{{ route('tickets.index', ['status' => 'pendiente']) }}" class="text-xs font-medium hover:underline" style="color:{{ $teal }};">Ver todos &rarr;</a>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse($ticketsPendientesList as $ticket)
                <div class="px-4 py-3 hover:bg-gray-50 transition">
                    <div class="flex items-start gap-3">
                        <div class="shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white mt-0.5" style="background-color:{{ $teal }};">
                            {{ $ticket->id }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800 truncate">{{ $ticket->title }}</p>
                            <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                                <span class="text-xs text-gray-500">Por: <strong class="text-gray-700">{{ $ticket->user->name ?? '&mdash;' }}</strong></span>
                                @if($ticket->producto)
                                <span class="inline-flex px-1.5 py-0.5 rounded text-xs font-mono font-medium" style="background-color:{{ $acentoBg }};color:{{ $acento }};">{{ $ticket->producto->codigo }}</span>
                                @endif
                                <span class="text-xs text-gray-400">{{ $ticket->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <div class="shrink-0 flex items-center gap-1.5">
                            <form method="POST" action="{{ route('dashboard.tickets.assign', $ticket) }}" class="flex items-center gap-1">
                                @csrf
                                <select name="assigned_to" required
                                    class="border border-gray-300 rounded-lg px-2 py-1 text-xs text-gray-700 focus:outline-none focus:ring-1 focus:ring-teal-400">
                                    <option value="">Asignar&hellip;</option>
                                    @foreach($almacenUsers as $au)
                                    <option value="{{ $au->id }}">{{ $au->name }}</option>
                                    @endforeach
                                </select>
                                <button type="submit"
                                    class="px-2.5 py-1.5 rounded-lg text-xs font-bold text-white transition hover:opacity-90"
                                    style="background-color:{{ $teal }};"
                                    title="Asignar">
                                    &#10003;
                                </button>
                            </form>
                            <a href="{{ route('tickets.show', $ticket) }}"
                               class="px-2.5 py-1.5 rounded-lg text-xs font-medium border border-gray-300 text-gray-600 hover:bg-gray-100 transition whitespace-nowrap">
                                Ver
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-5 py-10 text-center text-gray-400 text-sm">
                    <svg class="w-9 h-9 mx-auto mb-2 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Sin tickets pendientes
                </div>
                @endforelse
            </div>
        </div>

        {{-- Tickets en proceso --}}
        <div class="bg-white rounded-xl shadow border border-gray-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between bg-blue-50">
                <h3 class="text-sm font-bold flex items-center gap-2 text-blue-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    En proceso
                    @if($ticketsEnProceso > 0)
                    <span class="ml-1 bg-blue-100 text-blue-700 text-xs font-semibold px-2 py-0.5 rounded-full">{{ $ticketsEnProceso }}</span>
                    @endif
                </h3>
                <a href="{{ route('tickets.index', ['status' => 'en_proceso']) }}" class="text-xs font-medium text-blue-600 hover:underline">Ver todos &rarr;</a>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse($ticketsEnProcesoList as $ticket)
                <div class="px-4 py-3 hover:bg-gray-50 transition">
                    <div class="flex items-start gap-3">
                        <div class="shrink-0 w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-xs font-bold text-white mt-0.5">
                            {{ $ticket->id }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800 truncate">{{ $ticket->title }}</p>
                            <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                                <span class="text-xs text-gray-500">
                                    Asignado a: <strong class="text-blue-700">{{ $ticket->assignedTo->name ?? '&mdash;' }}</strong>
                                </span>
                                @if($ticket->producto)
                                <span class="inline-flex px-1.5 py-0.5 rounded text-xs font-mono font-medium" style="background-color:{{ $acentoBg }};color:{{ $acento }};">{{ $ticket->producto->codigo }}</span>
                                @endif
                                @if($ticket->assigned_at)
                                <span class="text-xs text-gray-400">{{ $ticket->assigned_at->diffForHumans() }}</span>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('tickets.show', $ticket) }}"
                           class="shrink-0 px-2.5 py-1.5 rounded-lg text-xs font-medium border border-gray-300 text-gray-600 hover:bg-gray-100 transition">
                            Ver
                        </a>
                    </div>
                </div>
                @empty
                <div class="px-5 py-10 text-center text-gray-400 text-sm">
                    <svg class="w-9 h-9 mx-auto mb-2 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Sin tickets en proceso
                </div>
                @endforelse
            </div>
        </div>

    </div>
    @endif

    {{-- ══════════════════════════════════════
         ACTIVIDAD RECIENTE
    ══════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Últimas Requisiciones --}}
        <div class="bg-white rounded-xl shadow border border-gray-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-700 flex items-center gap-2">
                    <svg class="w-4 h-4" style="color:{{ $acento }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    &Uacute;ltimas Requisiciones
                    @if($solicitudesPendientes > 0)
                    <span class="bg-yellow-100 text-yellow-700 text-xs font-semibold px-2 py-0.5 rounded-full">{{ $solicitudesPendientes }} pend.</span>
                    @endif
                </h3>
                <a href="{{ route('solicitudes.index') }}" class="text-xs font-medium hover:underline" style="color:{{ $acento }};">Ver todas &rarr;</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr style="background-color:{{ $acento }};">
                            <th class="px-3 py-2 text-left text-white font-semibold">Folio</th>
                            <th class="px-3 py-2 text-left text-white font-semibold">Producto</th>
                            <th class="px-3 py-2 text-center text-white font-semibold">Cant.</th>
                            <th class="px-3 py-2 text-center text-white font-semibold">Estado</th>
                            <th class="px-3 py-2 text-left text-white font-semibold">Fecha</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($ultimasRequisiciones as $req)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-3 py-2 font-mono font-semibold whitespace-nowrap" style="color:{{ $acento }};">{{ $req->folio }}</td>
                            <td class="px-3 py-2 text-gray-800 max-w-[140px] truncate">{{ $req->producto->descripcion ?? 'N/A' }}</td>
                            <td class="px-3 py-2 text-center text-gray-700">{{ number_format($req->cantidad, 2) }}</td>
                            <td class="px-3 py-2 text-center">
                                @php $est = $req->estado ?? 'pendiente'; @endphp
                                @if($est === 'pendiente')
                                    <span class="inline-flex px-1.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pendiente</span>
                                @elseif($est === 'aprobada')
                                    <span class="inline-flex px-1.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aprobada</span>
                                @elseif($est === 'rechazada')
                                    <span class="inline-flex px-1.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Rechazada</span>
                                @else
                                    <span class="inline-flex px-1.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">{{ ucfirst($est) }}</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-gray-500 whitespace-nowrap">{{ $req->created_at->format('d/m/y H:i') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Sin requisiciones</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Últimos Movimientos de Inventario --}}
        <div class="bg-white rounded-xl shadow border border-gray-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-700 flex items-center gap-2">
                    <svg class="w-4 h-4" style="color:{{ $acento }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    &Uacute;ltimos Movimientos
                </h3>
                <a href="{{ route('movimientos.index') }}" class="text-xs font-medium hover:underline" style="color:{{ $acento }};">Ver todos &rarr;</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr style="background-color:{{ $acento }};">
                            <th class="px-3 py-2 text-left text-white font-semibold">Producto</th>
                            <th class="px-3 py-2 text-center text-white font-semibold">Tipo</th>
                            <th class="px-3 py-2 text-center text-white font-semibold">Cant.</th>
                            <th class="px-3 py-2 text-left text-white font-semibold">Usuario</th>
                            <th class="px-3 py-2 text-left text-white font-semibold">Fecha</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($ultimosMovimientos as $mov)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-3 py-2 text-gray-800 max-w-[140px] truncate">{{ $mov->producto->descripcion ?? 'N/A' }}</td>
                            <td class="px-3 py-2 text-center">
                                @if($mov->tipo_movimiento === 'entrada')
                                    <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4"/></svg>
                                        Entrada
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                        Salida
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-center font-semibold text-gray-700">{{ number_format($mov->cantidad, 2) }}</td>
                            <td class="px-3 py-2 text-gray-600 max-w-[100px] truncate">{{ $mov->usuario->name ?? 'Sistema' }}</td>
                            <td class="px-3 py-2 text-gray-500 whitespace-nowrap">{{ $mov->created_at->format('d/m/y H:i') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Sin movimientos</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         DISTRIBUCIÓN POR CATEGORÍAS
    ══════════════════════════════════════ --}}
    @if($topCategorias->count() > 0)
    <div class="bg-white rounded-xl shadow border border-gray-200 p-5">
        <h3 class="text-sm font-bold text-gray-700 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4" style="color:{{ $acento }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            Distribuci&oacute;n por Categor&iacute;a (Top 5)
        </h3>
        <div class="space-y-3">
            @foreach($topCategorias as $item)
            @php $pct = $totalProductos > 0 ? ($item->total / $totalProductos) * 100 : 0; @endphp
            <div>
                <div class="flex items-center justify-between text-xs mb-1">
                    <span class="font-medium text-gray-700">
                        {{ $item->categoria->codigo ?? '&mdash;' }} &mdash; {{ $item->categoria->descripcion ?? 'N/A' }}
                    </span>
                    <span class="font-semibold text-gray-900">{{ number_format($item->total) }} <span class="text-gray-400 font-normal">({{ number_format($pct, 1) }}%)</span></span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                    <div class="h-2 rounded-full transition-all duration-500" style="background-color:{{ $acento }};width:{{ $pct }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════
         BÚSQUEDA RÁPIDA DE INVENTARIO
    ══════════════════════════════════════ --}}
    <div class="bg-white rounded-xl shadow border border-gray-200">
        <div class="px-5 py-3.5 border-b border-gray-200 flex items-center gap-3">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <h3 class="text-sm font-bold text-gray-700">B&uacute;squeda R&aacute;pida de Inventario</h3>
            <a href="{{ route('reportes.entradas') }}" class="ml-auto text-xs font-medium hover:underline" style="color:{{ $acento }};">Inventario completo &rarr;</a>
        </div>
        <div class="px-4 py-3 flex gap-3 items-center">
            <div class="flex-1 relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" id="searchInput"
                    placeholder="Buscar por c&oacute;digo, descripci&oacute;n, ubicaci&oacute;n, factura&hellip; (Esc para limpiar)"
                    class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 transition"
                    autocomplete="off">
            </div>
            <div id="searchStatus" class="hidden items-center gap-1.5 text-sm text-gray-400 whitespace-nowrap">
                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                Buscando&hellip;
            </div>
            <span id="searchCount" class="hidden text-xs font-semibold px-2 py-1 rounded-full" style="background-color:{{ $acentoBg }};color:{{ $acento }};"></span>
            <button id="clearBtn" onclick="clearSearch()" class="hidden items-center gap-1 px-3 py-1.5 bg-gray-100 text-gray-500 text-xs rounded-lg hover:bg-gray-200 transition font-medium">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Limpiar
            </button>
        </div>
    </div>

    {{-- Tabla inventario --}}
    <div class="bg-white rounded-xl shadow border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto" style="max-height:60vh;overflow-y:auto;">
            <table class="w-full text-xs border-collapse" style="min-width:1600px;">
                <thead class="sticky top-0 z-10">
                    <tr style="background-color:{{ $acento }};">
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
                        <th class="px-3 py-2.5 text-right text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">F&Iacute;SICO</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">F. SALIDA</th>
                        <th class="px-3 py-2.5 text-right text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">P.U</th>
                        <th class="px-3 py-2.5 text-center text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">MXN/USD</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">FACTURA</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide border-r border-indigo-600" style="min-width:160px;">DN/NP/OBS.</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">F. VENCIM.</th>
                        <th class="px-3 py-2.5 text-center text-white font-semibold uppercase tracking-wide whitespace-nowrap">H. SEG.</th>
                    </tr>
                </thead>
                <tbody id="tableBody" class="divide-y divide-gray-100">
                    @forelse($productos as $p)
                    <tr class="hover:bg-blue-50 transition-colors {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}" data-server-row>
                        <td class="px-3 py-2 font-mono font-semibold whitespace-nowrap">
                            <a href="{{ route('productos.show', $p->id) }}" class="hover:underline" style="color:{{ $acento }};">{{ $p->codigo }}</a>
                        </td>
                        <td class="px-3 py-2 text-gray-600 whitespace-nowrap">{{ $p->componente->codigo ?? '&mdash;' }}</td>
                        <td class="px-3 py-2 text-gray-600 whitespace-nowrap">{{ $p->categoria->codigo ?? '&mdash;' }}</td>
                        <td class="px-3 py-2 text-gray-600 whitespace-nowrap">{{ $p->familia->codigo ?? '&mdash;' }}</td>
                        <td class="px-3 py-2 text-gray-600 text-center">{{ $p->consecutivo ?? '&mdash;' }}</td>
                        <td class="px-3 py-2 text-gray-800">{{ $p->descripcion }}</td>
                        <td class="px-3 py-2 text-gray-600 whitespace-nowrap">{{ $p->unidadMedida->codigo ?? '&mdash;' }}</td>
                        <td class="px-3 py-2 text-gray-800 text-right whitespace-nowrap">{{ $p->cantidad_entrada !== null ? number_format($p->cantidad_entrada, 2) : '&mdash;' }}</td>
                        <td class="px-3 py-2 text-gray-600 whitespace-nowrap">{{ $p->ubicacion->codigo ?? '&mdash;' }}</td>
                        <td class="px-3 py-2 text-gray-600 whitespace-nowrap">{{ $p->fecha_entrada ? $p->fecha_entrada->format('d/m/Y') : '&mdash;' }}</td>
                        <td class="px-3 py-2 text-gray-800 text-right whitespace-nowrap">{{ $p->cantidad_salida !== null ? number_format($p->cantidad_salida, 2) : '&mdash;' }}</td>
                        <td class="px-3 py-2 text-right whitespace-nowrap font-semibold {{ ($p->cantidad_fisica !== null && $p->cantidad_fisica < 10) ? 'text-red-600' : 'text-gray-800' }}">
                            {{ $p->cantidad_fisica !== null ? number_format($p->cantidad_fisica, 2) : '&mdash;' }}
                        </td>
                        <td class="px-3 py-2 text-gray-600 whitespace-nowrap">{{ $p->fecha_salida ? $p->fecha_salida->format('d/m/Y') : '&mdash;' }}</td>
                        <td class="px-3 py-2 text-gray-800 text-right whitespace-nowrap">{{ $p->precio_unitario !== null ? number_format($p->precio_unitario, 2) : '&mdash;' }}</td>
                        <td class="px-3 py-2 text-gray-600 whitespace-nowrap">{{ $p->moneda ?? '&mdash;' }}</td>
                        <td class="px-3 py-2 text-gray-600 whitespace-nowrap">{{ $p->factura ?? '&mdash;' }}</td>
                        <td class="px-3 py-2 text-gray-600">{{ $p->observaciones ?? '&mdash;' }}</td>
                        <td class="px-3 py-2 whitespace-nowrap {{ ($p->fecha_vencimiento && $p->fecha_vencimiento->lte(now()->addDays(30))) ? 'text-red-600 font-semibold' : 'text-gray-600' }}">
                            {{ $p->fecha_vencimiento ? $p->fecha_vencimiento->format('d/m/Y') : '&mdash;' }}
                        </td>
                        <td class="px-3 py-2 text-center">
                            @if($p->hoja_seguridad)
                                <span class="inline-flex px-1.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">S&iacute;</span>
                            @else
                                <span class="text-gray-400">&mdash;</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr data-server-row>
                        <td colspan="19" class="px-6 py-12 text-center text-gray-400">No hay productos registrados</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div id="paginationSection">
            @if($productos->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 flex items-center justify-between">
                <p class="text-xs text-gray-500">
                    Mostrando {{ $productos->firstItem() }}&ndash;{{ $productos->lastItem() }} de {{ number_format($productos->total()) }} productos
                </p>
                <div class="flex gap-1">
                    @if($productos->onFirstPage())
                        <span class="px-3 py-1 text-xs border border-gray-200 rounded text-gray-300 cursor-not-allowed">&lsaquo; Ant.</span>
                    @else
                        <a href="{{ $productos->previousPageUrl() }}" class="px-3 py-1 text-xs border border-gray-300 rounded text-gray-600 hover:bg-gray-50 transition">&lsaquo; Ant.</a>
                    @endif
                    @foreach($productos->getUrlRange(max(1,$productos->currentPage()-2), min($productos->lastPage(),$productos->currentPage()+2)) as $page => $url)
                        @if($page == $productos->currentPage())
                            <span class="px-3 py-1 text-xs border rounded text-white font-semibold" style="background-color:{{ $acento }};border-color:{{ $acento }};">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="px-3 py-1 text-xs border border-gray-300 rounded text-gray-600 hover:bg-gray-50 transition">{{ $page }}</a>
                        @endif
                    @endforeach
                    @if($productos->hasMorePages())
                        <a href="{{ $productos->nextPageUrl() }}" class="px-3 py-1 text-xs border border-gray-300 rounded text-gray-600 hover:bg-gray-50 transition">Sig. &rsaquo;</a>
                    @else
                        <span class="px-3 py-1 text-xs border border-gray-200 rounded text-gray-300 cursor-not-allowed">Sig. &rsaquo;</span>
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
let currentQuery  = '';

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
    searchStatus.classList.add('flex');
    searchCount.classList.add('hidden');
    clearBtn.classList.remove('hidden');
    clearBtn.classList.add('flex');
    paginationSection.classList.add('hidden');

    try {
        const res  = await fetch(`${API_BASE}/productos/buscar?q=${encodeURIComponent(q)}&limit=200`);
        const data = await res.json();
        if (searchInput.value.trim() !== q) return;
        searchStatus.classList.add('hidden');
        searchStatus.classList.remove('flex');
        searchCount.classList.remove('hidden');
        searchCount.textContent = `${data.total} resultado${data.total !== 1 ? 's' : ''}`;
        renderResults(data.data || [], q);
    } catch {
        searchStatus.classList.add('hidden');
        searchStatus.classList.remove('flex');
        searchCount.classList.remove('hidden');
        searchCount.textContent = 'Error al buscar';
        searchCount.style.backgroundColor = '#fee2e2';
        searchCount.style.color = '#dc2626';
    }
}

function renderResults(productos, q) {
    document.querySelectorAll('[data-server-row]').forEach(r => r.classList.add('hidden'));
    document.querySelectorAll('[data-search-row]').forEach(r => r.remove());

    if (!productos.length) {
        const tr = document.createElement('tr');
        tr.setAttribute('data-search-row', '');
        tr.innerHTML = `<td colspan="19" class="px-6 py-12 text-center text-gray-400 text-sm">Sin resultados para &ldquo;<strong>${xss(q)}</strong>&rdquo;</td>`;
        tableBody.appendChild(tr);
        return;
    }

    productos.forEach((p, i) => {
        const fisico    = p.fisico != null ? parseFloat(p.fisico) : null;
        const fisicoStr = fisico !== null ? fisico.toLocaleString('es-MX', {minimumFractionDigits:2}) : '—';
        const puStr     = p.pu    != null ? parseFloat(p.pu).toLocaleString('es-MX', {minimumFractionDigits:2}) : '—';
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
            <td class="px-3 py-2 text-gray-600 whitespace-nowrap">${p.um || '—'}</td>
            <td class="px-3 py-2 text-gray-400 text-right whitespace-nowrap">—</td>
            <td class="px-3 py-2 text-gray-600 whitespace-nowrap">${hl(p.ubicacion || '—', q)}</td>
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
    searchStatus.classList.remove('flex');
    searchCount.classList.add('hidden');
    searchCount.style.backgroundColor = '#eef0f8';
    searchCount.style.color = '#4A568D';
    clearBtn.classList.add('hidden');
    clearBtn.classList.remove('flex');
    searchInput.focus();
}

function hl(text, q) {
    if (!text || !q) return xss(text || '');
    return xss(text).replace(new RegExp(`(${regEscape(q)})`, 'gi'), '<mark class="bg-yellow-200 px-0.5 rounded">$1</mark>');
}
function xss(s)       { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
function regEscape(s) { return s.replace(/[.*+?^${}()|[\]\\]/g,'\\$&'); }
</script>
@endsection
