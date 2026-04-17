@extends('layouts.app')
@section('title', 'Solicitudes de Movimiento')
@section('content')
@php
    $acento  = '#4A568D';
    $user    = auth()->user();
    $esGestor = $user->hasRole(['admin', 'admin_almacen']);
    $badgeMap = [
        'pendiente'  => 'bg-yellow-100 text-yellow-800 border-yellow-200',
        'en_proceso' => 'bg-blue-100 text-blue-800 border-blue-200',
        'finalizado' => 'bg-green-100 text-green-800 border-green-200',
        'cancelado'  => 'bg-red-100 text-red-800 border-red-200',
    ];
    $statusLabel = [
        'pendiente'  => 'Pendiente',
        'en_proceso' => 'En Proceso',
        'finalizado' => 'Finalizado',
        'cancelado'  => 'Cancelado',
    ];
@endphp

<div class="space-y-4">

    {{-- Flash --}}
    @if(session('success'))
    <div class="flex items-center gap-3 px-4 py-3 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm">
        <svg class="w-5 h-5 shrink-0 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-center gap-3 px-4 py-3 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm">
        <svg class="w-5 h-5 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Solicitudes de Movimiento</h1>
            <p class="text-xs text-gray-500 mt-0.5">
                {{ $esGestor ? 'Todas las solicitudes' : 'Mis solicitudes' }} &mdash;
                <b>{{ $tickets->total() }}</b> registros
            </p>
        </div>
        <a href="{{ route('tickets.create') }}"
           class="flex items-center gap-2 px-4 py-2.5 text-white text-sm font-semibold rounded-xl shadow transition hover:opacity-90 active:scale-95"
           style="background-color:{{ $acento }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nueva Solicitud
        </a>
    </div>

    {{-- Estadísticas --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        @foreach([
            ['label' => 'Todos', 'key' => 'total', 'param' => '', 'color' => 'indigo'],
            ['label' => 'Pendientes', 'key' => 'pendiente', 'param' => 'pendiente', 'color' => 'yellow'],
            ['label' => 'En Proceso', 'key' => 'en_proceso', 'param' => 'en_proceso', 'color' => 'blue'],
            ['label' => 'Finalizados', 'key' => 'finalizado', 'param' => 'finalizado', 'color' => 'green'],
            ['label' => 'Cancelados', 'key' => 'cancelado', 'param' => 'cancelado', 'color' => 'red'],
        ] as $card)
        <a href="{{ route('tickets.index', $card['param'] ? ['status' => $card['param']] : []) }}"
           class="bg-white rounded-xl border-2 {{ request('status') === $card['param'] || (!request('status') && !$card['param']) ? 'border-'.$card['color'].'-400' : 'border-gray-200' }} p-3 hover:shadow transition text-center">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ $card['label'] }}</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats[$card['key']] }}</p>
        </a>
        @endforeach
    </div>

    {{-- Buscador --}}
    <form method="GET" action="{{ route('tickets.index') }}" class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-4 py-3 flex gap-3 items-center flex-wrap">
            @if(request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <div class="flex-1 relative min-w-[200px]">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Buscar por título, descripción o ID..."
                       class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 transition"
                       autocomplete="off">
            </div>
            <button type="submit" class="px-4 py-2 text-white text-sm font-medium rounded-lg transition hover:opacity-90" style="background-color:{{ $acento }}">Buscar</button>
            @if(request('search') || request('status'))
            <a href="{{ route('tickets.index') }}" class="px-3 py-2 bg-gray-100 text-gray-600 text-sm rounded-lg hover:bg-gray-200 transition">&times; Limpiar</a>
            @endif
        </div>
    </form>

    {{-- Tabla --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm border-collapse">
                <thead>
                    <tr style="background-color:{{ $acento }};">
                        <th class="px-4 py-3 text-left text-white font-semibold text-xs uppercase tracking-wide whitespace-nowrap">ID</th>
                        <th class="px-4 py-3 text-left text-white font-semibold text-xs uppercase tracking-wide" style="min-width:220px;">Título</th>
                        <th class="px-4 py-3 text-left text-white font-semibold text-xs uppercase tracking-wide whitespace-nowrap">Producto</th>
                        @if($esGestor)
                        <th class="px-4 py-3 text-left text-white font-semibold text-xs uppercase tracking-wide whitespace-nowrap">Solicitante</th>
                        @endif
                        <th class="px-4 py-3 text-center text-white font-semibold text-xs uppercase tracking-wide whitespace-nowrap">Estado</th>
                        <th class="px-4 py-3 text-left text-white font-semibold text-xs uppercase tracking-wide whitespace-nowrap">Asignado a</th>
                        <th class="px-4 py-3 text-center text-white font-semibold text-xs uppercase tracking-wide whitespace-nowrap">Imágenes</th>
                        <th class="px-4 py-3 text-left text-white font-semibold text-xs uppercase tracking-wide whitespace-nowrap">Creado</th>
                        <th class="px-4 py-3 text-center text-white font-semibold text-xs uppercase tracking-wide whitespace-nowrap">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($tickets as $t)
                    <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }} hover:bg-blue-50 transition-colors">
                        <td class="px-4 py-3 font-mono font-bold whitespace-nowrap" style="color:{{ $acento }}">
                            {{ $t->formatted_code }}
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('tickets.show', $t) }}" class="font-medium text-gray-800 hover:underline" style="color:{{ $acento }}">
                                {{ Str::limit($t->title, 50) }}
                            </a>
                            <p class="text-xs text-gray-500 mt-0.5 truncate max-w-xs">{{ Str::limit($t->description, 80) }}</p>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-xs">
                            @if($t->producto)
                            <span class="font-mono font-bold" style="color:{{ $acento }}">{{ $t->producto->codigo }}</span>
                            @else
                            <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        @if($esGestor)
                        <td class="px-4 py-3 text-gray-700 whitespace-nowrap text-xs">{{ $t->user->name ?? '—' }}</td>
                        @endif
                        <td class="px-4 py-3 text-center whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium border {{ $badgeMap[$t->status] ?? '' }}">
                                {{ $statusLabel[$t->status] ?? $t->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-700 whitespace-nowrap text-xs">{{ $t->assignedTo->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-center text-gray-600">
                            @if($t->solicitudImages->count())
                            <span class="inline-flex items-center gap-1 text-xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ $t->solicitudImages->count() }}
                            </span>
                            @else
                            <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600 whitespace-nowrap text-xs">
                            {{ $t->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('tickets.show', $t) }}"
                               class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Ver
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $esGestor ? 9 : 8 }}" class="px-6 py-16 text-center text-gray-400">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <span class="font-medium text-gray-500">Sin solicitudes</span>
                                <a href="{{ route('tickets.create') }}" class="text-sm font-medium hover:underline mt-1" style="color:{{ $acento }}">Crear primera solicitud &rarr;</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if($tickets->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $tickets->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
