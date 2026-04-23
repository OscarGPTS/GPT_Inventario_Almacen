@extends('layouts.app')
@section('title', 'Historial de Movimientos')
@section('content')
@php
    $acento = '#4A568D';
    $claro  = '#eef0f8';

    // Config visual por tipo + fuente
    $tipoConfig = [
        'entrada' => [
            'label'  => 'Entrada',
            'bg'     => 'bg-emerald-100',
            'text'   => 'text-emerald-800',
            'border' => 'border-emerald-200',
            'dot'    => 'bg-emerald-500',
            'sign'   => '+',
            'signClass' => 'text-emerald-600 font-bold',
            'icon'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/>',
        ],
        'salida' => [
            'label'  => 'Salida',
            'bg'     => 'bg-red-100',
            'text'   => 'text-red-700',
            'border' => 'border-red-200',
            'dot'    => 'bg-red-500',
            'sign'   => '-',
            'signClass' => 'text-red-600 font-bold',
            'icon'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8V20m0 0l-4-4m4 4l4-4M7 16V4m0 0L3 8m4-4l4 4"/>',
        ],
        'transferencia' => [
            'label'  => 'Transferencia',
            'bg'     => 'bg-blue-100',
            'text'   => 'text-blue-800',
            'border' => 'border-blue-200',
            'dot'    => 'bg-blue-500',
            'sign'   => '↔',
            'signClass' => 'text-blue-600 font-bold',
            'icon'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4M4 17h12m0 0l-4-4m4 4l-4 4"/>',
        ],
        'ajuste' => [
            'label'  => 'Ajuste',
            'bg'     => 'bg-amber-100',
            'text'   => 'text-amber-800',
            'border' => 'border-amber-200',
            'dot'    => 'bg-amber-400',
            'sign'   => '~',
            'signClass' => 'text-amber-600 font-bold',
            'icon'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>',
        ],
    ];

    $fuenteConfig = [
        'excel'               => ['label'=>'Excel',          'bg'=>'bg-green-50 text-green-700 border-green-200',  'icon'=>'📊'],
        'json'                => ['label'=>'JSON',           'bg'=>'bg-purple-50 text-purple-700 border-purple-200','icon'=>'📁'],
        'barras'              => ['label'=>'Barras',         'bg'=>'bg-orange-50 text-orange-700 border-orange-200','icon'=>'🔩'],
        'solicitud_material'  => ['label'=>'Req. Material',  'bg'=>'bg-teal-50 text-teal-700 border-teal-200',     'icon'=>'📦'],
        'solicitud_movimiento'=> ['label'=>'Sol. Movimiento','bg'=>'bg-indigo-50 text-indigo-700 border-indigo-200','icon'=>'🔄'],
        'manual'              => ['label'=>'Manual',         'bg'=>'bg-gray-100 text-gray-600 border-gray-300',    'icon'=>'✏️'],
    ];
@endphp

<div class="space-y-4">

    {{-- ── HEADER ── --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Historial de Movimientos</h1>
            <p class="text-xs text-gray-500 mt-0.5">Registro completo de todas las operaciones de inventario</p>
        </div>
        <a href="{{ route('movimientos.exportar', request()->only(['search','tipo_movimiento','fuente','fecha_desde','fecha_hasta'])) }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg border border-emerald-300 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
            </svg>
            Exportar Excel
        </a>
    </div>

    {{-- ── STATS ── --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-2">
        @foreach([
            ['label'=>'Total',          'val'=>$stats['total'],         'cls'=>'border-gray-200 bg-white',            'num'=>'text-gray-800'],
            ['label'=>'Entradas',       'val'=>$stats['entrada'],       'cls'=>'border-emerald-200 bg-emerald-50',    'num'=>'text-emerald-700'],
            ['label'=>'Salidas',        'val'=>$stats['salida'],        'cls'=>'border-red-200 bg-red-50',            'num'=>'text-red-700'],
            ['label'=>'Transferencias', 'val'=>$stats['transferencia'], 'cls'=>'border-blue-200 bg-blue-50',          'num'=>'text-blue-700'],
            ['label'=>'Ajustes',        'val'=>$stats['ajuste'],        'cls'=>'border-amber-200 bg-amber-50',        'num'=>'text-amber-700'],
            ['label'=>'Vía Excel',      'val'=>$stats['excel'],         'cls'=>'border-green-200 bg-green-50',        'num'=>'text-green-700'],
            ['label'=>'Req. Material',  'val'=>$stats['solicitud_mat'], 'cls'=>'border-teal-200 bg-teal-50',          'num'=>'text-teal-700'],
        ] as $s)
        <div class="rounded-xl border {{ $s['cls'] }} px-3 py-2.5 text-center shadow-sm">
            <p class="text-xs text-gray-500 leading-tight">{{ $s['label'] }}</p>
            <p class="text-xl font-bold {{ $s['num'] }} mt-0.5">{{ number_format($s['val']) }}</p>
        </div>
        @endforeach
    </div>

    {{-- ── FILTROS ── --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <form method="GET" action="{{ route('movimientos.index') }}" class="px-4 py-3 flex gap-3 items-center flex-wrap">
            <div class="flex-1 relative min-w-52">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0118 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Buscar producto, descripción, referencia..."
                    class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
                    autocomplete="off">
            </div>
            <select name="tipo_movimiento" class="border border-gray-300 rounded-lg text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="">Todos los tipos</option>
                <option value="entrada"       {{ request('tipo_movimiento') === 'entrada'       ? 'selected' : '' }}>Entrada</option>
                <option value="salida"        {{ request('tipo_movimiento') === 'salida'        ? 'selected' : '' }}>Salida</option>
                <option value="transferencia" {{ request('tipo_movimiento') === 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                <option value="ajuste"        {{ request('tipo_movimiento') === 'ajuste'        ? 'selected' : '' }}>Ajuste</option>
            </select>
            <select name="fuente" class="border border-gray-300 rounded-lg text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="">Todas las fuentes</option>
                <option value="excel"               {{ request('fuente') === 'excel'               ? 'selected' : '' }}>Excel</option>
                <option value="json"                {{ request('fuente') === 'json'                ? 'selected' : '' }}>JSON</option>
                <option value="barras"              {{ request('fuente') === 'barras'              ? 'selected' : '' }}>Barras</option>
                <option value="solicitud_material"  {{ request('fuente') === 'solicitud_material'  ? 'selected' : '' }}>Req. Material</option>
                <option value="solicitud_movimiento"{{ request('fuente') === 'solicitud_movimiento'? 'selected' : '' }}>Sol. Movimiento</option>
                <option value="manual"              {{ request('fuente') === 'manual'              ? 'selected' : '' }}>Manual</option>
            </select>
            <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}"
                class="border border-gray-300 rounded-lg text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
            <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}"
                class="border border-gray-300 rounded-lg text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
            <button type="submit"
                class="px-4 py-2 text-white text-sm font-medium rounded-lg transition hover:opacity-90"
                style="background-color:{{ $acento }}">Filtrar</button>
            @if(request()->hasAny(['search','tipo_movimiento','fuente','fecha_desde','fecha_hasta']))
            <a href="{{ route('movimientos.index') }}"
               class="px-3 py-2 bg-gray-100 text-gray-600 text-sm rounded-lg hover:bg-gray-200 transition">&times; Limpiar</a>
            @endif
        </form>
    </div>

    {{-- ── TABLA VISUAL ── --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs border-collapse" style="min-width:900px;">
                <thead>
                    <tr style="background-color:{{ $acento }};" class="text-white">
                        <th class="px-3 py-2.5 text-left font-semibold uppercase tracking-wide whitespace-nowrap w-32">Fecha</th>
                        <th class="px-3 py-2.5 text-center font-semibold uppercase tracking-wide whitespace-nowrap w-36">Tipo / Fuente</th>
                        <th class="px-3 py-2.5 text-left font-semibold uppercase tracking-wide" style="min-width:200px;">Producto</th>
                        <th class="px-3 py-2.5 text-center font-semibold uppercase tracking-wide whitespace-nowrap w-28">Movimiento</th>
                        <th class="px-3 py-2.5 text-left font-semibold uppercase tracking-wide" style="min-width:220px;">Detalle / Trazabilidad</th>
                        <th class="px-3 py-2.5 text-left font-semibold uppercase tracking-wide whitespace-nowrap">Referencia</th>
                        <th class="px-3 py-2.5 text-left font-semibold uppercase tracking-wide whitespace-nowrap">Usuario</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                @php $prevDate = null; @endphp
                @forelse($movimientos as $m)
                @php
                    $fecha = $m->created_at->format('d/m/Y');
                    $cfg   = $tipoConfig[$m->tipo_movimiento] ?? $tipoConfig['ajuste'];
                    $fuente = $m->fuente ?? 'manual';
                    $fcfg  = $fuenteConfig[$fuente] ?? $fuenteConfig['manual'];
                @endphp

                {{-- ── Separador de fecha ── --}}
                @if($fecha !== $prevDate)
                @php $prevDate = $fecha; @endphp
                <tr class="bg-gray-50">
                    <td colspan="7" class="px-3 py-1.5">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            {{ $m->created_at->isToday() ? 'Hoy' : ($m->created_at->isYesterday() ? 'Ayer' : $m->created_at->translatedFormat('l, d \d\e F \d\e Y')) }}
                        </span>
                    </td>
                </tr>
                @endif

                <tr class="{{ $loop->even ? 'bg-white' : 'bg-gray-50/40' }} hover:bg-indigo-50/40 transition-colors">

                    {{-- Fecha y hora --}}
                    <td class="px-3 py-2.5 whitespace-nowrap">
                        <p class="font-medium text-gray-700">{{ $m->created_at->format('d/m/Y') }}</p>
                        <p class="text-gray-400 text-[10px]">{{ $m->created_at->format('H:i') }}</p>
                    </td>

                    {{-- Tipo + fuente --}}
                    <td class="px-3 py-2.5 text-center">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold border {{ $cfg['bg'] }} {{ $cfg['text'] }} {{ $cfg['border'] }}">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $cfg['icon'] !!}</svg>
                            {{ $cfg['label'] }}
                        </span>
                        <div class="mt-1">
                            <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded text-[10px] font-medium border {{ $fcfg['bg'] }}">
                                {{ $fcfg['icon'] }} {{ $fcfg['label'] }}
                            </span>
                        </div>
                    </td>

                    {{-- Producto --}}
                    <td class="px-3 py-2.5">
                        @if($m->producto)
                        <a href="{{ route('productos.show', $m->producto) }}"
                           class="font-mono font-bold hover:underline text-xs" style="color:{{ $acento }}">
                            {{ $m->producto->codigo }}
                        </a>
                        <p class="text-gray-600 text-[11px] mt-0.5 leading-tight">{{ \Str::limit($m->producto->descripcion, 55) }}</p>
                        @else
                        <span class="text-gray-400">—</span>
                        @endif
                    </td>

                    {{-- Movimiento visual: antes → cantidad → después --}}
                    <td class="px-3 py-2.5 text-center whitespace-nowrap">
                        <div class="flex items-center justify-center gap-1.5">
                            <span class="text-gray-500 font-mono text-[11px]">{{ number_format($m->cantidad_anterior) }}</span>
                            <span class="flex items-center gap-0.5">
                                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </span>
                            <span class="{{ $cfg['signClass'] }} font-mono text-[11px]">
                                {{ $cfg['sign'] }}{{ number_format($m->cantidad) }}
                            </span>
                            <span class="flex items-center gap-0.5">
                                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </span>
                            <span class="font-mono font-bold text-gray-800 text-[11px]">{{ number_format($m->cantidad_nueva) }}</span>
                        </div>
                    </td>

                    {{-- Detalle / trazabilidad según fuente --}}
                    <td class="px-3 py-2.5">
                        @if($fuente === 'excel' || $fuente === 'json')
                            {{-- Importación masiva --}}
                            <div class="flex items-start gap-2">
                                <div class="shrink-0 w-6 h-6 rounded-lg flex items-center justify-center text-white text-[10px] font-bold" style="background:{{ $fuente === 'excel' ? '#217346' : '#6366f1' }}">
                                    {{ $fuente === 'excel' ? 'XL' : 'JS' }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-700 leading-tight">
                                        {{ $fuente === 'excel' ? 'Importación desde Excel' : 'Importación desde JSON' }}
                                    </p>
                                    @if($m->referencia)
                                    <p class="text-gray-400 text-[10px] mt-0.5 truncate max-w-[180px]" title="{{ $m->referencia }}">
                                        {{ $m->referencia }}
                                    </p>
                                    @endif
                                </div>
                            </div>

                        @elseif($fuente === 'solicitud_material' && $m->solicitud)
                            {{-- Flujo Req. Material: solicitud → entregada --}}
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-semibold bg-teal-50 text-teal-700 border border-teal-200">
                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    {{ $m->solicitud->folio ?? ('SOL-'.$m->solicitud->id) }}
                                </span>
                                <svg class="w-3 h-3 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                                </svg>
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-green-100 text-green-700">Entregado</span>
                                <span class="text-gray-500 text-[10px]">a {{ \Str::limit($m->solicitud->solicitante ?? '', 20) }}</span>
                            </div>
                            @if($m->descripcion)
                            <p class="text-gray-400 text-[10px] mt-1 truncate">{{ $m->descripcion }}</p>
                            @endif

                        @elseif($fuente === 'solicitud_movimiento' && $m->ticket)
                            {{-- Flujo Sol. Movimiento: ticket completado --}}
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <a href="{{ route('tickets.show', $m->ticket) }}"
                                   class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200 hover:bg-indigo-100 transition">
                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                                    </svg>
                                    {{ $m->ticket->formatted_code }}
                                </a>
                                <svg class="w-3 h-3 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                                </svg>
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-green-100 text-green-700">Completado</span>
                            </div>
                            <p class="text-gray-500 text-[10px] mt-1 truncate max-w-[220px]">
                                {{ \Str::limit($m->ticket->title, 55) }}
                            </p>
                            @if($m->ticket->assignedTo)
                            <p class="text-gray-400 text-[10px]">Resuelto por: {{ $m->ticket->assignedTo->name }}</p>
                            @endif

                        @elseif($fuente === 'barras')
                            {{-- Alta manual en Barras --}}
                            <div class="flex items-center gap-1.5">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-semibold bg-orange-50 text-orange-700 border border-orange-200">
                                    🔩 Entrada - Barras
                                </span>
                            </div>
                            @if($m->descripcion)
                            <p class="text-gray-400 text-[10px] mt-1">{{ $m->descripcion }}</p>
                            @endif

                        @else
                            {{-- Genérico --}}
                            <p class="text-gray-700 leading-snug">{{ \Str::limit($m->descripcion, 70) ?: '—' }}</p>
                        @endif
                    </td>

                    {{-- Referencia --}}
                    <td class="px-3 py-2.5 text-gray-500 whitespace-nowrap max-w-[120px]">
                        <span class="truncate block" title="{{ $m->referencia }}">{{ $m->referencia ?: '—' }}</span>
                    </td>

                    {{-- Usuario --}}
                    <td class="px-3 py-2.5 whitespace-nowrap">
                        @if($m->usuario)
                        <div class="flex items-center gap-1.5">
                            <div class="w-5 h-5 rounded-full flex items-center justify-center text-white text-[9px] font-bold shrink-0"
                                 style="background-color:{{ $acento }}">
                                {{ strtoupper(substr($m->usuario->name, 0, 1)) }}
                            </div>
                            <span class="text-gray-700 text-[11px]">{{ \Str::limit($m->usuario->name, 18) }}</span>
                        </div>
                        @else
                        <span class="text-gray-400 text-[11px]">Sistema</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-16 text-center text-gray-400">
                        <div class="flex flex-col items-center gap-2">
                            <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4M4 17h12m0 0l-4-4m4 4l-4 4"/>
                            </svg>
                            <p class="font-medium text-gray-500">No hay movimientos registrados</p>
                            @if(request()->hasAny(['search','tipo_movimiento','fuente','fecha_desde','fecha_hasta']))
                            <a href="{{ route('movimientos.index') }}" class="text-sm hover:underline" style="color:{{ $acento }}">Limpiar filtros</a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($movimientos->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $movimientos->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
