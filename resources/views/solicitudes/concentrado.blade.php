@extends('layouts.app')
@section('title', 'Solicitudes')
@section('content')
@php
    $acento = '#4A568D';
    $claro  = '#eef0f8';

    $estadoBadgeSol = [
        'pendiente' => 'bg-yellow-100 text-yellow-800',
        'aprobada'  => 'bg-blue-100 text-blue-800',
        'entregada' => 'bg-green-100 text-green-800',
        'cancelada' => 'bg-red-100 text-red-800',
    ];
    $prioridadBadge = [
        'urgente' => 'bg-red-100 text-red-700',
        'alta'    => 'bg-orange-100 text-orange-700',
        'normal'  => 'bg-gray-100 text-gray-600',
        'baja'    => 'bg-slate-100 text-slate-500',
    ];
    $badgeMapTck = [
        'pendiente'  => 'bg-yellow-100 text-yellow-800 border-yellow-200',
        'en_proceso' => 'bg-blue-100 text-blue-800 border-blue-200',
        'finalizado' => 'bg-green-100 text-green-800 border-green-200',
        'cancelado'  => 'bg-red-100 text-red-800 border-red-200',
    ];
    $statusLabelTck = [
        'pendiente'  => 'Pendiente',
        'en_proceso' => 'En Proceso',
        'finalizado' => 'Finalizado',
        'cancelado'  => 'Cancelado',
    ];

    // Helpers para URLs de tab conservando filtros
    $tabUrl = fn($t) => route('solicitudes.concentrado', array_merge(request()->except('tab','page'), ['tab' => $t]));
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

    {{-- ══════════════════════════════════════
         HEADER
    ══════════════════════════════════════ --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Solicitudes</h1>
            <p class="text-xs text-gray-500 mt-0.5">
                {{ $esGestor ? 'Todas las solicitudes del sistema' : 'Mis solicitudes' }}
                &mdash; <b>{{ $stats['sol']['total'] + $stats['tck']['total'] }}</b> en total
            </p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            @if(!auth()->user()->hasRole('visitante'))
            <button onclick="abrirModalMaterial()"
                class="flex items-center gap-2 px-4 py-2.5 text-white text-sm font-semibold rounded-xl shadow transition hover:opacity-90 active:scale-95 border-2 border-white/30"
                style="background-color:#0d7a6b;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                Nueva Req. Material
            </button>
            <a href="{{ route('tickets.create') }}"
               class="flex items-center gap-2 px-4 py-2.5 text-white text-sm font-semibold rounded-xl shadow transition hover:opacity-90 active:scale-95"
               style="background-color:{{ $acento }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                </svg>
                Nueva Sol. Movimiento
            </a>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════
         STATS — dos grupos
    ══════════════════════════════════════ --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        {{-- Material --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-3">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-2 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                Requisiciones de Material
            </p>
            <div class="grid grid-cols-5 gap-2">
                @foreach([
                    ['label'=>'Total',     'val'=>$stats['sol']['total'],     'color'=>'indigo'],
                    ['label'=>'Pendiente', 'val'=>$stats['sol']['pendiente'], 'color'=>'yellow'],
                    ['label'=>'Aprobada',  'val'=>$stats['sol']['aprobada'],  'color'=>'blue'],
                    ['label'=>'Entregada', 'val'=>$stats['sol']['entregada'], 'color'=>'green'],
                    ['label'=>'Cancelada', 'val'=>$stats['sol']['cancelada'], 'color'=>'red'],
                ] as $c)
                <div class="text-center">
                    <p class="text-xs text-gray-500">{{ $c['label'] }}</p>
                    <p class="text-lg font-bold text-gray-800">{{ $c['val'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
        {{-- Movimiento --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-3">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-2 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                </svg>
                Solicitudes de Movimiento
            </p>
            <div class="grid grid-cols-5 gap-2">
                @foreach([
                    ['label'=>'Total',      'val'=>$stats['tck']['total'],      'color'=>'indigo'],
                    ['label'=>'Pendiente',  'val'=>$stats['tck']['pendiente'],  'color'=>'yellow'],
                    ['label'=>'En Proceso', 'val'=>$stats['tck']['en_proceso'], 'color'=>'blue'],
                    ['label'=>'Finalizado', 'val'=>$stats['tck']['finalizado'], 'color'=>'green'],
                    ['label'=>'Cancelado',  'val'=>$stats['tck']['cancelado'],  'color'=>'red'],
                ] as $c)
                <div class="text-center">
                    <p class="text-xs text-gray-500">{{ $c['label'] }}</p>
                    <p class="text-lg font-bold text-gray-800">{{ $c['val'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         TABS + BÚSQUEDA COMPARTIDA
    ══════════════════════════════════════ --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        {{-- Tabs --}}
        <div class="flex border-b border-gray-200 overflow-x-auto">
            @foreach([
                ['key'=>'todos',      'label'=>'Todos', 'total'=>$stats['sol']['total']+$stats['tck']['total']],
                ['key'=>'material',   'label'=>'Req. Material',    'total'=>$stats['sol']['total']],
                ['key'=>'movimiento', 'label'=>'Sol. Movimiento',  'total'=>$stats['tck']['total']],
            ] as $t)
            <a href="{{ $tabUrl($t['key']) }}"
               class="flex items-center gap-2 px-5 py-3 text-sm font-medium whitespace-nowrap border-b-2 transition
                      {{ $tab === $t['key'] ? 'border-[#4A568D] text-[#4A568D]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                {{ $t['label'] }}
                <span class="px-1.5 py-0.5 rounded-full text-xs {{ $tab === $t['key'] ? 'bg-[#4A568D] text-white' : 'bg-gray-100 text-gray-500' }}">
                    {{ $t['total'] }}
                </span>
            </a>
            @endforeach
        </div>

        {{-- Búsqueda compartida --}}
        <form method="GET" action="{{ route('solicitudes.concentrado') }}" class="px-4 py-3 border-b border-gray-100 flex gap-3 items-center flex-wrap">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <div class="flex-1 relative min-w-56">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0118 0z"/>
                </svg>
                <input type="text" name="search" value="{{ $search }}"
                    placeholder="{{ $tab === 'movimiento' ? 'Buscar por título, descripción o ID...' : 'Buscar por folio, solicitante, código, departamento...' }}"
                    class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 transition"
                    autocomplete="off">
            </div>

            {{-- Filtros específicos por tab --}}
            @if($tab === 'material' || $tab === 'todos')
            <select name="estado_mat" class="border border-gray-300 rounded-lg text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="">Estado material</option>
                <option value="pendiente"  {{ request('estado_mat') === 'pendiente'  ? 'selected' : '' }}>Pendiente</option>
                <option value="aprobada"   {{ request('estado_mat') === 'aprobada'   ? 'selected' : '' }}>Aprobada</option>
                <option value="entregada"  {{ request('estado_mat') === 'entregada'  ? 'selected' : '' }}>Entregada</option>
                <option value="cancelada"  {{ request('estado_mat') === 'cancelada'  ? 'selected' : '' }}>Cancelada</option>
            </select>
            <select name="prioridad" class="border border-gray-300 rounded-lg text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="">Prioridad</option>
                <option value="urgente" {{ request('prioridad') === 'urgente' ? 'selected' : '' }}>Urgente</option>
                <option value="alta"    {{ request('prioridad') === 'alta'    ? 'selected' : '' }}>Alta</option>
                <option value="normal"  {{ request('prioridad') === 'normal'  ? 'selected' : '' }}>Normal</option>
                <option value="baja"    {{ request('prioridad') === 'baja'    ? 'selected' : '' }}>Baja</option>
            </select>
            @endif
            @if($tab === 'movimiento' || $tab === 'todos')
            <select name="status_tck" class="border border-gray-300 rounded-lg text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="">Estado movimiento</option>
                <option value="pendiente"  {{ request('status_tck') === 'pendiente'  ? 'selected' : '' }}>Pendiente</option>
                <option value="en_proceso" {{ request('status_tck') === 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                <option value="finalizado" {{ request('status_tck') === 'finalizado' ? 'selected' : '' }}>Finalizado</option>
                <option value="cancelado"  {{ request('status_tck') === 'cancelado'  ? 'selected' : '' }}>Cancelado</option>
            </select>
            @endif

            <button type="submit" class="px-4 py-2 text-white text-sm font-medium rounded-lg transition hover:opacity-90" style="background-color:{{ $acento }}">Filtrar</button>
            @if($search || request()->hasAny(['estado_mat','prioridad','status_tck']))
            <a href="{{ $tabUrl($tab) }}" class="px-3 py-2 bg-gray-100 text-gray-600 text-sm rounded-lg hover:bg-gray-200 transition">&times; Limpiar</a>
            @endif
        </form>
    </div>

    {{-- ══════════════════════════════════════
         TAB: TODOS (overview compacto)
    ══════════════════════════════════════ --}}
    @if($tab === 'todos')
    {{-- Requisiciones de Material recientes --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
            <div class="flex items-center gap-2">
                <div class="w-2.5 h-2.5 rounded-full bg-teal-500"></div>
                <span class="font-semibold text-sm text-gray-700">Requisiciones de Material</span>
                <span class="text-xs text-gray-400">(últimas {{ $recentSol->count() }})</span>
            </div>
            <a href="{{ $tabUrl('material') }}" class="text-xs font-medium hover:underline" style="color:{{ $acento }}">Ver todas &rarr;</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs border-collapse" style="min-width:700px;">
                <thead>
                    <tr class="bg-teal-600 text-white">
                        <th class="px-3 py-2 text-left font-semibold uppercase tracking-wide whitespace-nowrap">Fecha</th>
                        <th class="px-3 py-2 text-left font-semibold uppercase tracking-wide whitespace-nowrap">Folio</th>
                        <th class="px-3 py-2 text-left font-semibold uppercase tracking-wide whitespace-nowrap">Solicitante</th>
                        <th class="px-3 py-2 text-left font-semibold uppercase tracking-wide whitespace-nowrap">Departamento</th>
                        <th class="px-3 py-2 text-left font-semibold uppercase tracking-wide whitespace-nowrap">Producto</th>
                        <th class="px-3 py-2 text-right font-semibold uppercase tracking-wide whitespace-nowrap">Cant.</th>
                        <th class="px-3 py-2 text-center font-semibold uppercase tracking-wide whitespace-nowrap">Prioridad</th>
                        <th class="px-3 py-2 text-center font-semibold uppercase tracking-wide whitespace-nowrap">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentSol as $r)
                    <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }} hover:bg-teal-50 transition-colors">
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $r->fecha ? $r->fecha->format('d/m/Y') : '—' }}</td>
                        <td class="px-3 py-2 font-mono font-semibold whitespace-nowrap" style="color:#0d7a6b">{{ $r->folio ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $r->solicitante ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-600 whitespace-nowrap">{{ $r->departamento->nombre ?? '—' }}</td>
                        <td class="px-3 py-2">
                            <span class="font-mono text-gray-800">{{ $r->producto->codigo ?? '—' }}</span>
                            @if($r->producto)<span class="text-gray-500 ml-1">{{ \Str::limit($r->producto->descripcion, 30) }}</span>@endif
                        </td>
                        <td class="px-3 py-2 text-right font-semibold text-gray-800 whitespace-nowrap">{{ $r->cantidad !== null ? number_format($r->cantidad, 0) : '—' }}</td>
                        <td class="px-3 py-2 text-center whitespace-nowrap">
                            @if($r->prioridad)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $prioridadBadge[$r->prioridad] ?? '' }}">{{ ucfirst($r->prioridad) }}</span>
                            @else<span class="text-gray-400">—</span>@endif
                        </td>
                        <td class="px-3 py-2 text-center whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $estadoBadgeSol[$r->estado] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ ucfirst($r->estado) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400 text-sm">Sin requisiciones de material</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Tickets de Movimiento recientes --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
            <div class="flex items-center gap-2">
                <div class="w-2.5 h-2.5 rounded-full" style="background-color:{{ $acento }}"></div>
                <span class="font-semibold text-sm text-gray-700">Solicitudes de Movimiento</span>
                <span class="text-xs text-gray-400">(últimas {{ $recentTck->count() }})</span>
            </div>
            <a href="{{ $tabUrl('movimiento') }}" class="text-xs font-medium hover:underline" style="color:{{ $acento }}">Ver todas &rarr;</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs border-collapse" style="min-width:700px;">
                <thead>
                    <tr style="background-color:{{ $acento }};" class="text-white">
                        <th class="px-3 py-2 text-left font-semibold uppercase tracking-wide whitespace-nowrap">ID</th>
                        <th class="px-3 py-2 text-left font-semibold uppercase tracking-wide">T&iacute;tulo</th>
                        <th class="px-3 py-2 text-left font-semibold uppercase tracking-wide whitespace-nowrap">Producto</th>
                        @if($esGestor)<th class="px-3 py-2 text-left font-semibold uppercase tracking-wide whitespace-nowrap">Solicitante</th>@endif
                        <th class="px-3 py-2 text-center font-semibold uppercase tracking-wide whitespace-nowrap">Estado</th>
                        <th class="px-3 py-2 text-left font-semibold uppercase tracking-wide whitespace-nowrap">Asignado a</th>
                        <th class="px-3 py-2 text-left font-semibold uppercase tracking-wide whitespace-nowrap">Creado</th>
                        <th class="px-3 py-2 text-center font-semibold uppercase tracking-wide whitespace-nowrap">Ver</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentTck as $t)
                    <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }} hover:bg-indigo-50 transition-colors">
                        <td class="px-3 py-2 font-mono font-bold whitespace-nowrap" style="color:{{ $acento }}">{{ $t->formatted_code }}</td>
                        <td class="px-3 py-2">
                            <a href="{{ route('tickets.show', $t) }}" class="font-medium text-gray-800 hover:underline">{{ \Str::limit($t->title, 50) }}</a>
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap">
                            @if($t->producto)<span class="font-mono font-bold" style="color:{{ $acento }}">{{ $t->producto->codigo }}</span>
                            @else<span class="text-gray-400">—</span>@endif
                        </td>
                        @if($esGestor)<td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $t->user->name ?? '—' }}</td>@endif
                        <td class="px-3 py-2 text-center whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-medium border {{ $badgeMapTck[$t->status] ?? '' }}">
                                {{ $statusLabelTck[$t->status] ?? $t->status }}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-gray-600 whitespace-nowrap">{{ $t->assignedTo->name ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-500 whitespace-nowrap">{{ $t->created_at->format('d/m/Y') }}</td>
                        <td class="px-3 py-2 text-center">
                            <a href="{{ route('tickets.show', $t) }}" class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition">Ver</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="{{ $esGestor ? 8 : 7 }}" class="px-4 py-8 text-center text-gray-400 text-sm">Sin solicitudes de movimiento</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════
         TAB: MATERIAL (tabla completa)
    ══════════════════════════════════════ --}}
    @if($tab === 'material')
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs border-collapse" style="min-width:1200px;">
                <thead class="sticky top-0 z-10">
                    <tr class="bg-teal-700 text-white">
                        <th class="px-3 py-2.5 text-left font-semibold uppercase tracking-wide whitespace-nowrap border-r border-teal-600">FECHA</th>
                        <th class="px-3 py-2.5 text-left font-semibold uppercase tracking-wide whitespace-nowrap border-r border-teal-600">F. REQ.</th>
                        <th class="px-3 py-2.5 text-left font-semibold uppercase tracking-wide whitespace-nowrap border-r border-teal-600">FOLIO</th>
                        <th class="px-3 py-2.5 text-left font-semibold uppercase tracking-wide whitespace-nowrap border-r border-teal-600">SOLICITANTE</th>
                        <th class="px-3 py-2.5 text-left font-semibold uppercase tracking-wide whitespace-nowrap border-r border-teal-600">DEPARTAMENTO</th>
                        <th class="px-3 py-2.5 text-right font-semibold uppercase tracking-wide whitespace-nowrap border-r border-teal-600">CANT.</th>
                        <th class="px-3 py-2.5 text-center font-semibold uppercase tracking-wide whitespace-nowrap border-r border-teal-600">U.M.</th>
                        <th class="px-3 py-2.5 text-left font-semibold uppercase tracking-wide whitespace-nowrap border-r border-teal-600">C&Oacute;DIGO</th>
                        <th class="px-3 py-2.5 text-left font-semibold uppercase tracking-wide border-r border-teal-600" style="min-width:200px;">DESCRIPCI&Oacute;N</th>
                        <th class="px-3 py-2.5 text-left font-semibold uppercase tracking-wide whitespace-nowrap border-r border-teal-600">DN / NP</th>
                        <th class="px-3 py-2.5 text-center font-semibold uppercase tracking-wide whitespace-nowrap border-r border-teal-600">PRIORIDAD</th>
                        <th class="px-3 py-2.5 text-center font-semibold uppercase tracking-wide whitespace-nowrap border-r border-teal-600">ESTADO</th>
                        <th class="px-3 py-2.5 text-left font-semibold uppercase tracking-wide whitespace-nowrap">REGISTRADO POR</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($solicitudes as $r)
                    <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }} hover:bg-teal-50 transition-colors">
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $r->fecha ? $r->fecha->format('d/m/Y') : '—' }}</td>
                        <td class="px-3 py-2 whitespace-nowrap {{ ($r->fecha_requerida && $r->fecha_requerida->isPast() && $r->estado === 'pendiente') ? 'text-red-600 font-semibold' : 'text-gray-600' }}">
                            {{ $r->fecha_requerida ? $r->fecha_requerida->format('d/m/Y') : '—' }}
                        </td>
                        <td class="px-3 py-2 font-mono font-semibold whitespace-nowrap text-teal-700">{{ $r->folio ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $r->solicitante ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $r->departamento->nombre ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-800 text-right whitespace-nowrap font-semibold">{{ $r->cantidad !== null ? number_format($r->cantidad, 2) : '—' }}</td>
                        <td class="px-3 py-2 text-gray-700 text-center whitespace-nowrap">{{ $r->unidadMedida->codigo ?? '—' }}</td>
                        <td class="px-3 py-2 font-mono text-gray-800 whitespace-nowrap">{{ $r->producto->codigo ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-800">{{ $r->producto->descripcion ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-600 whitespace-nowrap max-w-xs truncate">{{ $r->observaciones ?? '—' }}</td>
                        <td class="px-3 py-2 text-center whitespace-nowrap">
                            @if($r->prioridad)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $prioridadBadge[$r->prioridad] ?? '' }}">{{ ucfirst($r->prioridad) }}</span>
                            @else<span class="text-gray-400">—</span>@endif
                        </td>
                        <td class="px-3 py-2 text-center whitespace-nowrap">
                            @if($esGestor)
                            <select
                                class="estado-select-mat text-xs border-0 rounded-full px-2 py-0.5 font-semibold cursor-pointer {{ $estadoBadgeSol[$r->estado] ?? 'bg-gray-100 text-gray-600' }}"
                                data-id="{{ $r->id }}"
                                data-url="{{ route('solicitudes.cambiarEstado', $r->id) }}"
                                data-orig="{{ $r->estado }}"
                                style="appearance:none;-webkit-appearance:none;">
                                <option value="pendiente" {{ $r->estado === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="aprobada"  {{ $r->estado === 'aprobada'  ? 'selected' : '' }}>Aprobada</option>
                                <option value="entregada" {{ $r->estado === 'entregada' ? 'selected' : '' }}>Entregada</option>
                                <option value="cancelada" {{ $r->estado === 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                            </select>
                            @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $estadoBadgeSol[$r->estado] ?? '' }}">{{ ucfirst($r->estado) }}</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-gray-500 whitespace-nowrap">{{ $r->usuarioRegistro->name ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="13" class="px-6 py-16 text-center text-gray-400">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                <span>Sin requisiciones de material</span>
                                @if(!auth()->user()->hasRole('visitante'))
                                <button type="button" onclick="abrirModalMaterial()" class="text-sm font-medium hover:underline text-teal-700">Crear primera requisici&oacute;n &rarr;</button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($solicitudes->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $solicitudes->links() }}
        </div>
        @endif
    </div>
    @endif

    {{-- ══════════════════════════════════════
         TAB: MOVIMIENTO (tabla completa)
    ══════════════════════════════════════ --}}
    @if($tab === 'movimiento')
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm border-collapse">
                <thead>
                    <tr style="background-color:{{ $acento }};">
                        <th class="px-4 py-3 text-left text-white font-semibold text-xs uppercase tracking-wide whitespace-nowrap">ID</th>
                        <th class="px-4 py-3 text-left text-white font-semibold text-xs uppercase tracking-wide" style="min-width:220px;">T&iacute;tulo</th>
                        <th class="px-4 py-3 text-left text-white font-semibold text-xs uppercase tracking-wide whitespace-nowrap">Producto</th>
                        @if($esGestor)
                        <th class="px-4 py-3 text-left text-white font-semibold text-xs uppercase tracking-wide whitespace-nowrap">Solicitante</th>
                        @endif
                        <th class="px-4 py-3 text-center text-white font-semibold text-xs uppercase tracking-wide whitespace-nowrap">Estado</th>
                        <th class="px-4 py-3 text-left text-white font-semibold text-xs uppercase tracking-wide whitespace-nowrap">Asignado a</th>
                        <th class="px-4 py-3 text-center text-white font-semibold text-xs uppercase tracking-wide whitespace-nowrap">Im&aacute;genes</th>
                        <th class="px-4 py-3 text-left text-white font-semibold text-xs uppercase tracking-wide whitespace-nowrap">Creado</th>
                        <th class="px-4 py-3 text-center text-white font-semibold text-xs uppercase tracking-wide whitespace-nowrap">Acci&oacute;n</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($tickets as $t)
                    <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }} hover:bg-blue-50 transition-colors">
                        <td class="px-4 py-3 font-mono font-bold whitespace-nowrap" style="color:{{ $acento }}">{{ $t->formatted_code }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('tickets.show', $t) }}" class="font-medium text-gray-800 hover:underline" style="color:{{ $acento }}">
                                {{ \Str::limit($t->title, 50) }}
                            </a>
                            <p class="text-xs text-gray-500 mt-0.5 truncate max-w-xs">{{ \Str::limit($t->description, 80) }}</p>
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
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium border {{ $badgeMapTck[$t->status] ?? '' }}">
                                {{ $statusLabelTck[$t->status] ?? $t->status }}
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
                            @else<span class="text-gray-400">—</span>@endif
                        </td>
                        <td class="px-4 py-3 text-gray-600 whitespace-nowrap text-xs">{{ $t->created_at->format('d/m/Y H:i') }}</td>
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                                </svg>
                                <span class="font-medium text-gray-500">Sin solicitudes de movimiento</span>
                                @if(!auth()->user()->hasRole('visitante'))
                                <a href="{{ route('tickets.create') }}" class="text-sm font-medium hover:underline mt-1" style="color:{{ $acento }}">Crear primera solicitud &rarr;</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tickets->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $tickets->links() }}
        </div>
        @endif
    </div>
    @endif

</div>

{{-- ═══════════════════════════════════════
     MODAL: Nueva Requisición de Material
═══════════════════════════════════════ --}}
<div id="modalMaterial"
    class="fixed inset-0 z-50 hidden items-center justify-center p-4"
    style="background:rgba(0,0,0,0.45);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[92vh] flex flex-col">

        <div class="flex items-center justify-between px-6 py-4 rounded-t-2xl text-white" style="background-color:#0d7a6b;">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <span class="font-semibold text-base">Nueva Requisici&oacute;n de Material</span>
            </div>
            <button onclick="cerrarModalMaterial()" class="text-white opacity-70 hover:opacity-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('solicitudes.nueva') }}" id="formMaterial"
              class="overflow-y-auto flex-1 px-6 py-5 space-y-4">
            @csrf

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Fecha <span class="text-red-500">*</span></label>
                    <input type="date" name="fecha" value="{{ now()->format('Y-m-d') }}" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Fecha Requerida</label>
                    <input type="date" name="fecha_requerida"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Folio <span class="text-gray-400 font-normal">(opcional)</span></label>
                    <input type="text" name="folio" placeholder="Ej. REQ-001"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Solicitante <span class="text-red-500">*</span></label>
                <input type="text" name="solicitante" value="{{ auth()->user()->name }}" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">
                    Departamento <span class="text-red-500">*</span>
                    <span id="deptoNuevoTagM" class="hidden ml-1 text-xs font-medium px-1.5 py-0.5 rounded-full bg-green-100 text-green-700">✦ Se crear&aacute; nuevo</span>
                </label>
                <div class="relative">
                    <input type="text" id="depto_search_m"
                        placeholder="Escribe para buscar o crear departamento..."
                        autocomplete="off"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
                    <input type="hidden" name="departamento_id" id="depto_id_m">
                    <input type="hidden" name="departamento_nombre" id="depto_nombre_m">
                    <div id="depto_dropdown_m" class="hidden absolute left-0 right-0 top-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg z-50 max-h-44 overflow-y-auto"></div>
                </div>
                <p class="text-xs text-gray-400 mt-1">Si no existe, escr&iacute;belo y selecciona "Crear nuevo"</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Producto <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="text" id="prod_search_m"
                        placeholder="Buscar por c&oacute;digo o descripci&oacute;n..."
                        autocomplete="off"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
                    <input type="hidden" name="producto_id" id="prod_id_m">
                    <div id="prod_dropdown_m" class="hidden absolute left-0 right-0 top-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg z-50 max-h-52 overflow-y-auto"></div>
                </div>
                <div id="prod_preview_m" class="hidden mt-2 flex items-center gap-2 px-3 py-2 rounded-lg text-xs border" style="background:#eef0f8;border-color:#c7cfe7;">
                    <svg class="w-3.5 h-3.5 shrink-0 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span id="prod_preview_text_m" class="font-medium text-teal-700"></span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Cantidad <span class="text-red-500">*</span></label>
                    <input type="number" name="cantidad" min="0.01" step="any" placeholder="0" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Unidad de Medida</label>
                    <div class="relative">
                        <input type="text" id="um_search_m" placeholder="PZA, KG, MT..." autocomplete="off"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
                        <input type="hidden" name="unidad_medida_id" id="um_id_m">
                        <div id="um_dropdown_m" class="hidden absolute left-0 right-0 top-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg z-50 max-h-44 overflow-y-auto"></div>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">DN / NP / Observaciones</label>
                <textarea name="observaciones" rows="2"
                    placeholder="N&uacute;mero de parte, dise&ntilde;o u observaciones adicionales..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 resize-none"></textarea>
            </div>

            <input type="hidden" name="estado" value="pendiente">
        </form>

        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between gap-3 rounded-b-2xl bg-gray-50">
            <p class="text-xs text-gray-400"><span class="text-red-500">*</span> Campos requeridos</p>
            <div class="flex gap-3">
                <button type="button" onclick="cerrarModalMaterial()"
                    class="px-5 py-2 bg-white border border-gray-300 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-50 transition">Cancelar</button>
                <button type="submit" form="formMaterial" id="btnGuardarMat"
                    class="px-6 py-2 text-white text-sm font-semibold rounded-xl transition hover:opacity-90 active:scale-95 flex items-center gap-2"
                    style="background-color:#0d7a6b;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Guardar Requisici&oacute;n
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const ACENTO_C = '{{ $acento }}';
const CSRF_C   = document.querySelector('meta[name="csrf-token"]')?.content || '';

/* ─── Modal Material ───────────────────────── */
function abrirModalMaterial() {
    const m = document.getElementById('modalMaterial');
    m.classList.remove('hidden');
    m.classList.add('flex');
    document.body.style.overflow = 'hidden';
    document.getElementById('formMaterial').reset();
    ['depto_id_m','depto_nombre_m','prod_id_m','um_id_m'].forEach(id => {
        const el = document.getElementById(id); if(el) el.value = '';
    });
    document.getElementById('prod_preview_m').classList.add('hidden');
    document.getElementById('deptoNuevoTagM').classList.add('hidden');
    const btn = document.getElementById('btnGuardarMat');
    btn.disabled = false;
    btn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Guardar Requisición`;
    setTimeout(() => document.getElementById('depto_search_m')?.focus(), 120);
}
function cerrarModalMaterial() {
    const m = document.getElementById('modalMaterial');
    m.classList.add('hidden');
    m.classList.remove('flex');
    document.body.style.overflow = '';
}
document.getElementById('modalMaterial').addEventListener('click', e => {
    if (e.target === document.getElementById('modalMaterial')) cerrarModalMaterial();
});
document.addEventListener('keydown', e => { if (e.key === 'Escape') cerrarModalMaterial(); });

/* ─── Typeahead genérico ──────────────────── */
function crearTypeaheadC({ inputId, dropdownId, hiddenId, endpoint, renderItem, onSelect, allowCreate = false, hiddenNombreId = null, nuevoTagId = null }) {
    const input    = document.getElementById(inputId);
    const dropdown = document.getElementById(dropdownId);
    const hidden   = document.getElementById(hiddenId);
    if (!input || !dropdown || !hidden) return;
    let timer = null;

    input.addEventListener('input', function() {
        clearTimeout(timer);
        const q = this.value.trim();
        hidden.value = '';
        if (!q) { ocultarDD(); return; }
        timer = setTimeout(() => buscar(q), 230);
    });
    input.addEventListener('focus', function() { if (this.value.trim()) buscar(this.value.trim()); });
    input.addEventListener('blur', () => setTimeout(ocultarDD, 200));

    function ocultarDD() { dropdown.classList.add('hidden'); }

    async function buscar(q) {
        try {
            const r = await fetch(`${endpoint}?q=${encodeURIComponent(q)}`);
            const j = await r.json();
            mostrar(j.data || [], q);
        } catch(e) { console.error(e); }
    }

    function mostrar(items, q) {
        dropdown.innerHTML = '';
        items.forEach(item => {
            const div = document.createElement('div');
            div.className = 'px-3 py-2 text-sm text-gray-700 hover:bg-teal-50 cursor-pointer flex items-center gap-2';
            div.innerHTML = renderItem(item, q);
            div.addEventListener('mousedown', e => {
                e.preventDefault();
                hidden.value = item.id;
                input.value = item.label || item.codigo || item.nombre || '';
                if (nuevoTagId) document.getElementById(nuevoTagId)?.classList.add('hidden');
                if (hiddenNombreId) document.getElementById(hiddenNombreId).value = input.value;
                ocultarDD();
                if (onSelect) onSelect(item);
            });
            dropdown.appendChild(div);
        });

        if (items.length === 0 && allowCreate) {
            const q2 = input.value.trim();
            const div = document.createElement('div');
            div.className = 'px-3 py-2.5 text-sm font-semibold cursor-pointer flex items-center gap-2 hover:bg-green-50 text-green-700';
            div.innerHTML = `<svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Crear "<strong>${escHtml(q2)}</strong>"`;
            div.addEventListener('mousedown', e => {
                e.preventDefault();
                hidden.value = '';
                if (hiddenNombreId) document.getElementById(hiddenNombreId).value = q2;
                if (nuevoTagId) document.getElementById(nuevoTagId)?.classList.remove('hidden');
                ocultarDD();
                if (onSelect) onSelect({ id: null, label: q2, isNew: true });
            });
            dropdown.appendChild(div);
        }

        if (dropdown.children.length > 0) dropdown.classList.remove('hidden');
        else ocultarDD();
    }
}

function escHtml(s) { return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
function hlText(t, q) { if (!q) return escHtml(t); const re = new RegExp(`(${q.replace(/[.*+?^${}()|[\]\\]/g,'\\$&')})`, 'gi'); return escHtml(t).replace(re, '<mark class="bg-yellow-100">$1</mark>'); }

// Departamento
crearTypeaheadC({
    inputId: 'depto_search_m', dropdownId: 'depto_dropdown_m', hiddenId: 'depto_id_m',
    hiddenNombreId: 'depto_nombre_m', nuevoTagId: 'deptoNuevoTagM',
    endpoint: '/api/v1/departamentos/buscar', allowCreate: true,
    renderItem: (item, q) => `<svg class="w-3.5 h-3.5 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>${hlText(item.label||item.nombre||'', q)}`,
    onSelect: () => {},
});

// Producto
crearTypeaheadC({
    inputId: 'prod_search_m', dropdownId: 'prod_dropdown_m', hiddenId: 'prod_id_m',
    endpoint: '/api/v1/productos/buscar',
    renderItem: (item, q) => {
        const c = item.codigo||'', d = item.descripcion||'';
        return `<span class="font-mono font-bold text-xs shrink-0 text-teal-700">${hlText(c,q)}</span><span class="text-gray-600 truncate min-w-0">${hlText(d,q)}</span>${item.um?`<span class="ml-auto text-gray-400 text-xs shrink-0">${escHtml(item.um)}</span>`:''}`;
    },
    onSelect: (item) => {
        document.getElementById('prod_preview_text_m').textContent = `${item.codigo} — ${item.descripcion||''}`;
        document.getElementById('prod_preview_m').classList.remove('hidden');
        if (item.um) autoFillUMM(item.um);
    },
});

// Unidad de Medida
crearTypeaheadC({
    inputId: 'um_search_m', dropdownId: 'um_dropdown_m', hiddenId: 'um_id_m',
    endpoint: '/api/v1/unidades-medida/buscar',
    renderItem: (item, q) => `<span class="font-mono font-bold text-xs shrink-0 text-teal-700">${hlText(item.codigo||'',q)}</span><span class="text-gray-500 text-xs truncate">${escHtml(item.label?.split('—')[1]?.trim()||'')}</span>`,
    onSelect: () => {},
});

async function autoFillUMM(umCodigo) {
    try {
        const r = await fetch(`/api/v1/unidades-medida/buscar?q=${encodeURIComponent(umCodigo)}`);
        const j = await r.json();
        const hit = (j.data||[]).find(u => u.codigo === umCodigo);
        if (hit) {
            document.getElementById('um_id_m').value     = hit.id;
            document.getElementById('um_search_m').value = hit.label || hit.codigo;
        }
    } catch(e) {}
}

/* ─── Validación pre-submit ─────────────── */
document.getElementById('formMaterial').addEventListener('submit', function(e) {
    const prodId  = document.getElementById('prod_id_m').value;
    const deptoId = document.getElementById('depto_id_m').value;
    const deptoNom = document.getElementById('depto_nombre_m').value;

    if (!prodId) {
        e.preventDefault();
        const el = document.getElementById('prod_search_m');
        el.classList.add('border-red-400','ring-2','ring-red-200');
        el.focus();
        setTimeout(() => el.classList.remove('border-red-400','ring-2','ring-red-200'), 2500);
        return;
    }
    if (!deptoId && !deptoNom) {
        e.preventDefault();
        const el = document.getElementById('depto_search_m');
        el.classList.add('border-red-400','ring-2','ring-red-200');
        el.focus();
        setTimeout(() => el.classList.remove('border-red-400','ring-2','ring-red-200'), 2500);
        return;
    }
    const btn = document.getElementById('btnGuardarMat');
    btn.disabled = true;
    btn.innerHTML = `<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Guardando...`;
});

/* ─── Estado inline AJAX (tab Material) ── */
const ESTADO_CLASES_MAT = {
    pendiente: 'bg-yellow-100 text-yellow-800',
    aprobada : 'bg-blue-100 text-blue-800',
    entregada: 'bg-green-100 text-green-800',
    cancelada: 'bg-red-100 text-red-800',
};
document.querySelectorAll('.estado-select-mat').forEach(sel => {
    sel.addEventListener('change', async function() {
        const url    = this.dataset.url;
        const estado = this.value;
        const orig   = this.dataset.orig;
        try {
            const res = await fetch(url, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_C, 'Accept': 'application/json' },
                body: JSON.stringify({ estado })
            });
            if (!res.ok) throw new Error('Error');
            this.dataset.orig = estado;
            const cls = ESTADO_CLASES_MAT[estado] || 'bg-gray-100 text-gray-600';
            this.className = `estado-select-mat text-xs border-0 rounded-full px-2 py-0.5 font-semibold cursor-pointer ${cls}`;
        } catch(e) {
            this.value = orig;
            alert('No se pudo actualizar el estado.');
        }
    });
});
</script>
@endsection
