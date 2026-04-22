@extends('layouts.app')

@section('title', 'Inventario')

@section('content')
<div class="space-y-4">

    {{-- Flash Messages --}}
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
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- Encabezado --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Inventario</h1>
            <p class="text-xs text-gray-500 mt-0.5">
                {{ number_format($registros->total()) }} registros
                @if(request()->hasAny(['search','componente_id','categoria_id','familia_id','unidad_medida_id','ubicacion_id']))
                    <span class="text-indigo-600 font-semibold">· filtrados</span>
                @endif
                <span class="text-gray-400">· con existencia &gt; 0 · más recientes primero</span>
            </p>
        </div>
        <div class="flex gap-2">

            @if(!auth()->user()->hasRole('visitante'))
            <button onclick="abrirModalNuevoProducto()" 
                class="flex items-center gap-2 px-4 py-2.5 text-white text-sm font-semibold rounded-xl shadow transition hover:opacity-90 active:scale-95"
                style="background-color:#4A568D">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo Producto
            </button>
            @endif

            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('admin_almacen'))
            <button onclick="abrirModalCargaMasiva()" 
                class="flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white text-sm font-semibold rounded-xl shadow transition hover:bg-green-700 active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                Carga Masiva Excel
            </button>
            @endif
        </div>
    </div>

    {{-- Filtros --}}
    <form method="GET" action="{{ route('reportes.entradas') }}" id="formFiltros" class="bg-white rounded-xl shadow-sm border border-gray-200">

        {{-- Fila 1: búsqueda de texto + botones --}}
        <div class="px-4 pt-3 pb-2 flex flex-wrap gap-3 items-center border-b border-gray-100">
            <div class="flex-1 min-w-[200px] relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400 z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Buscar por código, descripción, factura, observaciones..."
                    class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition"
                    autocomplete="off" id="searchInput" onkeyup="buscarDinamico(event)">
                <div id="suggestionBox" class="hidden absolute top-full left-0 right-0 mt-1 bg-white border border-gray-300 rounded-lg shadow-lg z-50 max-h-80 overflow-y-auto">
                    <div id="loadingSuggestions" class="hidden px-4 py-3 text-sm text-gray-500 flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-indigo-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Buscando...
                    </div>
                    <div id="suggestionsContent"></div>
                </div>
            </div>
            <button type="submit"
                class="px-4 py-2 text-white text-sm font-medium rounded-lg transition hover:opacity-90 shrink-0"
                style="background-color:#4A568D;">
                Buscar
            </button>
            @if(request()->hasAny(['search','componente_id','categoria_id','familia_id','unidad_medida_id','ubicacion_id']))
            <a href="{{ route('reportes.entradas') }}"
               class="px-4 py-2 bg-red-50 text-red-600 text-sm font-medium rounded-lg hover:bg-red-100 transition shrink-0 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Limpiar filtros
            </a>
            @endif
        </div>

        {{-- Fila 2: filtros por catálogo --}}
        <div class="px-4 py-2.5 flex flex-wrap gap-2 items-center">
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide shrink-0 mr-1">Filtrar por:</span>

            <select name="componente_id" onchange="document.getElementById('formFiltros').submit()"
                class="border border-gray-300 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-400 bg-white {{ request('componente_id') ? 'border-indigo-400 bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600' }}">
                <option value="">Componente</option>
                @foreach($componentes as $c)
                    <option value="{{ $c->id }}" {{ request('componente_id') == $c->id ? 'selected' : '' }}>
                        {{ $c->codigo }} – {{ $c->descripcion }}
                    </option>
                @endforeach
            </select>

            <select name="categoria_id" onchange="document.getElementById('formFiltros').submit()"
                class="border border-gray-300 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-400 bg-white {{ request('categoria_id') ? 'border-indigo-400 bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600' }}">
                <option value="">Categoría</option>
                @foreach($categorias as $c)
                    <option value="{{ $c->id }}" {{ request('categoria_id') == $c->id ? 'selected' : '' }}>
                        {{ $c->codigo }} – {{ $c->descripcion }}
                    </option>
                @endforeach
            </select>

            <select name="familia_id" onchange="document.getElementById('formFiltros').submit()"
                class="border border-gray-300 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-400 bg-white {{ request('familia_id') ? 'border-indigo-400 bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600' }}">
                <option value="">Familia</option>
                @foreach($familias as $f)
                    <option value="{{ $f->id }}" {{ request('familia_id') == $f->id ? 'selected' : '' }}>
                        {{ $f->codigo }} – {{ $f->descripcion }}
                    </option>
                @endforeach
            </select>

            <select name="unidad_medida_id" onchange="document.getElementById('formFiltros').submit()"
                class="border border-gray-300 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-400 bg-white {{ request('unidad_medida_id') ? 'border-indigo-400 bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600' }}">
                <option value="">Unidad de Medida</option>
                @foreach($unidadesMedida as $u)
                    <option value="{{ $u->id }}" {{ request('unidad_medida_id') == $u->id ? 'selected' : '' }}>
                        {{ $u->codigo }} – {{ $u->descripcion }}
                    </option>
                @endforeach
            </select>

            <select name="ubicacion_id" onchange="document.getElementById('formFiltros').submit()"
                class="border border-gray-300 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-400 bg-white {{ request('ubicacion_id') ? 'border-indigo-400 bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600' }}">
                <option value="">Ubicación</option>
                @foreach($ubicaciones as $u)
                    <option value="{{ $u->id }}" {{ request('ubicacion_id') == $u->id ? 'selected' : '' }}>
                        {{ $u->codigo }} – {{ $u->descripcion }}
                    </option>
                @endforeach
            </select>

            {{-- Badges de filtros activos --}}
            @foreach([
                'componente_id'    => ['label' => 'Componente',     'col' => $componentes],
                'categoria_id'     => ['label' => 'Categoría',      'col' => $categorias],
                'familia_id'       => ['label' => 'Familia',        'col' => $familias],
                'unidad_medida_id' => ['label' => 'UM',             'col' => $unidadesMedida],
                'ubicacion_id'     => ['label' => 'Ubicación',      'col' => $ubicaciones],
            ] as $param => $meta)
                @if(request($param))
                    @php $item = $meta['col']->firstWhere('id', request($param)); @endphp
                    @if($item)
                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-indigo-100 text-indigo-700 rounded-lg text-xs font-semibold">
                        {{ $meta['label'] }}: {{ $item->codigo }}
                        <a href="{{ request()->fullUrlWithQuery([$param => null]) }}"
                           class="ml-0.5 hover:text-indigo-900" title="Quitar filtro">×</a>
                    </span>
                    @endif
                @endif
            @endforeach
        </div>
    </form>

    {{-- Tabla --}}
    @php
        $esAlmacen   = auth()->user()->hasRole('almacenista') || auth()->user()->hasRole('admin_almacen');
        $puedeEditar = auth()->user()->hasRole('almacenista') || auth()->user()->hasRole('admin_almacen') || auth()->user()->hasRole('admin');
    @endphp
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto" style="max-height: calc(100vh - 260px); overflow-y: auto;">
            <table class="w-full text-xs border-collapse" style="min-width:{{ $esAlmacen ? '2300px' : '1600px' }};">
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
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">FECHA ENTRADA</th>
                        <th class="px-3 py-2.5 text-right text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">SALIDA</th>
                        <th class="px-3 py-2.5 text-right text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">FISICO</th>
                        <th class="px-3 py-2.5 text-right text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600" title="Reservado por solicitudes aprobadas">APARTADO</th>
                        <th class="px-3 py-2.5 text-right text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600" title="Físico menos apartado">DISPONIBLE</th>
                        @if($esAlmacen)
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">FECHA SALIDA</th>
                        <th class="px-3 py-2.5 text-right text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">P.U</th>
                        <th class="px-3 py-2.5 text-center text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">MXN/USD</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">FACTURA</th>
                        @endif
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide border-r border-indigo-600" style="min-width:180px;">DN/NP/OBSERVACI&Oacute;N</th>
                        @if($esAlmacen)
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">FECHA DE VENCIMIENTO</th>
                        <th class="px-3 py-2.5 text-center text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">HOJAS DE SEGURIDAD</th>
                        @endif
                        <th class="px-3 py-2.5 text-center text-white font-semibold uppercase tracking-wide whitespace-nowrap" style="min-width:130px;">OPCIONES</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($registros as $p)
                    @php
                        $editData = json_encode([
                            'id'                 => $p->id,
                            'codigo'             => $p->codigo,
                            'consecutivo'        => $p->consecutivo,
                            'descripcion'        => $p->descripcion,
                            'componente_id'      => $p->componente_id,
                            'categoria_id'       => $p->categoria_id,
                            'familia_id'         => $p->familia_id,
                            'unidad_medida_id'   => $p->unidad_medida_id,
                            'ubicacion_id'       => $p->ubicacion_id,
                            'cantidad_entrada'   => $p->cantidad_entrada,
                            'cantidad_salida'    => $p->cantidad_salida,
                            'cantidad_fisica'    => $p->cantidad_fisica,
                            'fecha_entrada'      => $p->fecha_entrada?->format('Y-m-d'),
                            'fecha_salida'       => $p->fecha_salida?->format('Y-m-d'),
                            'precio_unitario'    => $p->precio_unitario,
                            'moneda'             => $p->moneda ?? 'MXN',
                            'factura'            => $p->factura,
                            'numero_requisicion' => $p->numero_requisicion,
                            'numero_parte'       => $p->numero_parte,
                            'dimensiones'        => $p->dimensiones,
                            'orden_compra'       => $p->orden_compra,
                            'observaciones'      => $p->observaciones,
                            'fecha_vencimiento'  => $p->fecha_vencimiento?->format('Y-m-d'),
                            'hoja_seguridad'     => $p->hoja_seguridad,
                        ]);
                    @endphp
                    <tr class="hover:bg-blue-50 transition-colors {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                        <td class="px-3 py-2 font-mono font-semibold whitespace-nowrap">
                            <a href="{{ route('productos.show', $p->id) }}" class="hover:underline" style="color:#4A568D;">{{ $p->codigo }}</a>
                        </td>
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $p->componente->codigo ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $p->categoria->codigo ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $p->familia->codigo ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-700 text-center">{{ $p->consecutivo ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-800">{{ $p->descripcion }}</td>
                        <td class="px-3 py-2 text-gray-700 text-center whitespace-nowrap">{{ $p->unidadMedida->codigo ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-800 text-right whitespace-nowrap">{{ $p->cantidad_entrada !== null ? number_format($p->cantidad_entrada, 2) : '—' }}</td>
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $p->ubicacion->codigo ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $p->fecha_entrada ? $p->fecha_entrada->format('d/m/Y') : '—' }}</td>
                        <td class="px-3 py-2 text-gray-800 text-right whitespace-nowrap">{{ $p->cantidad_salida !== null ? number_format($p->cantidad_salida, 2) : '—' }}</td>
                        <td class="px-3 py-2 text-right whitespace-nowrap font-semibold {{ ($p->cantidad_fisica !== null && $p->cantidad_fisica < 10) ? 'text-red-600' : 'text-gray-800' }}">
                            {{ $p->cantidad_fisica !== null ? number_format($p->cantidad_fisica, 2) : '—' }}
                        </td>
                        @php
                            $apartado   = (float)($p->cantidad_apartada ?? 0);
                            $disponible = max(0, (float)($p->cantidad_fisica ?? 0) - $apartado);
                        @endphp
                        <td class="px-3 py-2 text-right whitespace-nowrap {{ $apartado > 0 ? 'text-amber-600 font-semibold' : 'text-gray-400' }}">
                            {{ $apartado > 0 ? number_format($apartado, 2) : '—' }}
                        </td>
                        <td class="px-3 py-2 text-right whitespace-nowrap font-semibold {{ $disponible < 5 ? 'text-red-600' : 'text-emerald-700' }}">
                            {{ number_format($disponible, 2) }}
                        </td>
                        @if($esAlmacen)
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $p->fecha_salida ? $p->fecha_salida->format('d/m/Y') : '—' }}</td>
                        <td class="px-3 py-2 text-gray-800 text-right whitespace-nowrap">{{ $p->precio_unitario !== null ? number_format($p->precio_unitario, 2) : '—' }}</td>
                        <td class="px-3 py-2 text-gray-700 text-center whitespace-nowrap">{{ $p->moneda ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $p->factura ?? '—' }}</td>
                        @endif
                        <td class="px-3 py-2 text-gray-600">{{ $p->observaciones ?? '—' }}</td>
                        @if($esAlmacen)
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
                        @endif
                        {{-- OPCIONES --}}
                        <td class="px-3 py-2 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1">
                                <button type="button"
                                    onclick="abrirModalRequisicion({{ $p->id }}, '{{ addslashes($p->codigo) }}', '{{ addslashes($p->descripcion) }}', {{ $p->unidad_medida_id ?? 'null' }}, '{{ addslashes($p->unidadMedida->codigo ?? '') }}', {{ $disponible }})"
                                    title="Solicitar producto / Crear requisición"
                                    class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold text-purple-700 bg-purple-50 border border-purple-200 rounded-lg hover:bg-purple-100 transition whitespace-nowrap">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    Solicitar Pieza
                                </button>
                                <button type="button"
                                    onclick="abrirModalMoverPieza({{ $p->id }}, '{{ addslashes($p->codigo) }}', '{{ addslashes($p->descripcion) }}')"
                                    title="Crear solicitud de movimiento para este producto"
                                    class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold text-teal-700 bg-teal-50 border border-teal-200 rounded-lg hover:bg-teal-100 transition whitespace-nowrap">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                    </svg>
                                    Mover Pieza
                                </button>
                                @if(!auth()->user()->hasRole('visitante'))
                                <button type="button"
                                    onclick="abrirModalEditar({{ $editData }})"
                                    title="Editar producto"
                                    class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Editar
                                </button>
                                <button type="button"
                                    onclick="abrirModalNC({{ $p->id }}, '{{ addslashes($p->codigo) }}', '{{ addslashes($p->descripcion) }}')"
                                    title="Registrar no conformidad"
                                    class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-200 rounded-lg hover:bg-amber-100 transition whitespace-nowrap">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    No Conforme
                                </button>
                                
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $esAlmacen ? 22 : 16 }}" class="px-6 py-12 text-center text-gray-400 text-sm">
                            @if(request('search'))
                                Sin resultados para "<strong>{{ request('search') }}</strong>"
                            @else
                                No hay productos registrados
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginaci&oacute;n --}}
        @include('reportes._paginacion', ['items' => $registros, 'acento' => '#4A568D'])
    </div>

</div>

{{-- ═══════════════════════════════════════════════════════════════════════════════
     MODAL: Carga Masiva desde Excel con Preview
═══════════════════════════════════════════════════════════════════════════════ --}}
<div id="modalCargaMasiva" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background:rgba(0,0,0,0.45);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-6xl max-h-[90vh] overflow-hidden flex flex-col">
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 text-white" style="background-color:#4A568D">
            <div class="flex items-center gap-3">
                <div class="bg-white/20 p-2 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-lg">Carga Masiva - Entradas</h3>
                    <p class="text-xs text-white opacity-90" id="modal_subtitle">Selecciona el archivo Excel para importar</p>
                </div>
            </div>
            <button onclick="cerrarModalCargaMasiva()" class="text-white/80 hover:text-white hover:bg-white/10 p-2 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Content --}}
        <div class="flex-1 overflow-y-auto">
            <form id="formCargaMasiva" method="POST" action="{{ route('reportes.entradas.importar') }}" enctype="multipart/form-data" class="h-full">
                @csrf
                
                {{-- PASO 1: Selección de Archivo --}}
                <div id="paso1" class="px-6 py-5 space-y-5">
                    {{-- Instrucciones mejoradas --}}
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500 rounded-r-lg p-4">
                        <div class="flex gap-3">
                            <svg class="w-6 h-6 shrink-0 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="space-y-2">
                                <p class="font-bold text-blue-900 text-sm">📋 Instrucciones de Importación</p>
                                <ul class="space-y-1.5 text-xs text-blue-800">
                                    <li class="flex items-start gap-2">
                                        <span class="text-blue-500 font-bold mt-0.5">1.</span>
                                        <span>Selecciona el número de página (tab) del Excel donde están tus datos (por defecto: 1)</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <span class="text-blue-500 font-bold mt-0.5">2.</span>
                                        <span>Carga el archivo y revisa el preview de los datos</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <span class="text-blue-500 font-bold mt-0.5">3.</span>
                                        <span>Si todo se ve correcto, confirma la carga</span>
                                    </li>
                                </ul>
                                <div class="mt-3 pt-3 border-t border-blue-200">
                                    <p class="text-xs text-blue-700 font-semibold mb-1">Formatos aceptados:</p>
                                    <div class="flex gap-2">
                                        <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs font-mono">.xlsx</span>
                                        <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs font-mono">.xls</span>
                                        <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs font-mono">.csv</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Número de página --}}
                    <div class="bg-white border-2 border-gray-200 rounded-xl p-4">
                        <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Número de Página (Tab) en Excel
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="sheet_number" id="sheet_number" value="1" min="1" max="50" required
                            class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition"
                            placeholder="Ejemplo: 1, 2, 3, 4...">
                        <p class="text-xs text-gray-500 mt-2 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Indica el número de la pestaña donde se encuentran los datos (la primera pestaña es 1)
                        </p>
                    </div>

                    {{-- Modo de Importación --}}
                    <div class="bg-white border-2 border-gray-200 rounded-xl p-4">
                        <label class="block text-sm font-bold text-gray-700 mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            Modo de Importación
                            <span class="text-red-500">*</span>
                        </label>
                        
                        <div class="space-y-3">
                            <label class="flex items-start gap-3 p-3 bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-300 rounded-lg cursor-pointer hover:border-blue-500 transition-all group">
                                <input type="radio" name="import_mode" value="update_create" checked 
                                    class="mt-0.5 w-4 h-4 text-blue-600 focus:ring-blue-500">
                                <div class="flex-1">
                                    <p class="font-bold text-gray-800 text-sm group-hover:text-blue-700">🔄 Actualizar existentes y crear nuevos (Recomendado)</p>
                                    <p class="text-xs text-gray-600 mt-0.5">Si el código ya existe, se actualizan los datos. Si no existe, se crea un nuevo producto.</p>
                                </div>
                            </label>
                            
                            <label class="flex items-start gap-3 p-3 bg-white border-2 border-gray-300 rounded-lg cursor-pointer hover:border-green-500 transition-all group">
                                <input type="radio" name="import_mode" value="only_new" 
                                    class="mt-0.5 w-4 h-4 text-green-600 focus:ring-green-500">
                                <div class="flex-1">
                                    <p class="font-bold text-gray-800 text-sm group-hover:text-green-700">✨ Solo agregar nuevos productos</p>
                                    <p class="text-xs text-gray-600 mt-0.5">Ignora los códigos que ya existen en el sistema. Solo crea productos nuevos.</p>
                                </div>
                            </label>
                            
                            <label class="flex items-start gap-3 p-3 bg-white border-2 border-gray-300 rounded-lg cursor-pointer hover:border-amber-500 transition-all group">
                                <input type="radio" name="import_mode" value="only_update" 
                                    class="mt-0.5 w-4 h-4 text-amber-600 focus:ring-amber-500">
                                <div class="flex-1">
                                    <p class="font-bold text-gray-800 text-sm group-hover:text-amber-700">🔃 Solo actualizar existentes</p>
                                    <p class="text-xs text-gray-600 mt-0.5">Solo actualiza productos que ya existen. Ignora códigos nuevos.</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Selector de archivo mejorado --}}
                    <div class="bg-white border-2 border-dashed border-gray-300 rounded-xl p-8 hover:border-green-400 transition-all group">
                        <label for="archivo_excel" class="cursor-pointer block text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="bg-green-50 p-4 rounded-full group-hover:bg-green-100 transition">
                                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-700">Arrastra tu archivo aquí o haz clic para seleccionar</p>
                                    <p class="text-xs text-gray-500 mt-1">Tamaño máximo: 10MB</p>
                                </div>
                                <div id="file_info" class="hidden bg-green-50 border border-green-200 rounded-lg px-4 py-2 mt-2">
                                    <p class="text-sm text-green-800 font-semibold">📄 <span id="file_name"></span></p>
                                    <p class="text-xs text-green-600"><span id="file_size"></span> • <span id="file_sheets"></span></p>
                                </div>
                            </div>
                        </label>
                        <input type="file" name="archivo" id="archivo_excel" accept=".xlsx,.xls,.csv" required
                            onchange="handleFileSelect(event)" class="hidden">
                    </div>

                    {{-- Botón de preview --}}
                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" onclick="cerrarModalCargaMasiva()"
                            class="px-5 py-2.5 bg-white border-2 border-gray-300 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-50 transition-all">
                            Cancelar
                        </button>
                        <button type="button" id="btn_preview" onclick="generarPreview()" disabled
                            class="px-6 py-2.5 bg-green-600 text-white text-sm font-bold rounded-xl transition-all hover:bg-green-700 disabled:bg-gray-300 disabled:cursor-not-allowed flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Siguiente
                        </button>
                    </div>
                </div>

                {{-- PASO 2: Preview de Datos --}}
                <div id="paso2" class="hidden px-6 py-5 space-y-4">
                    {{-- Resumen --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-4">
                            <div class="flex items-center gap-3">
                                <div class="bg-blue-500 p-2 rounded-lg">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-blue-600 font-medium">Total Filas</p>
                                    <p class="text-2xl font-bold text-blue-900" id="preview_total">0</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-xl p-4">
                            <div class="flex items-center gap-3">
                                <div class="bg-green-500 p-2 rounded-lg">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-green-600 font-medium">Registros Válidos</p>
                                    <p class="text-2xl font-bold text-green-900" id="preview_validos">0</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Información del header detectado --}}
                    <div id="header_info" class="bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-200 rounded-lg p-3">
                        <div class="flex items-center gap-2 text-sm">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span class="text-indigo-700">
                                <strong>Header detectado automáticamente:</strong> 
                                <span class="font-mono bg-indigo-100 px-2 py-0.5 rounded" id="header_row_number">Fila 1</span>
                            </span>
                        </div>
                    </div>

                    {{-- Preview de la tabla --}}
                    <div class="bg-gray-50 border-2 border-gray-200 rounded-xl overflow-hidden">
                        <div class="bg-gray-100 px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                            <h4 class="font-bold text-sm text-gray-700 flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Vista Previa de Datos (Primeros 10 Registros)
                            </h4>
                            <div class="flex items-center gap-2 text-xs">
                                <span id="columns_count" class="bg-indigo-100 text-indigo-700 px-2 py-1 rounded font-semibold">0 columnas</span>
                                <span class="text-gray-500">📜 Scroll horizontal →</span>
                            </div>
                        </div>
                        <div class="overflow-x-auto overflow-y-auto" style="max-height: 400px;">
                            <table class="w-full text-xs" id="preview_table" style="min-width: max-content;">
                                <thead class="bg-gray-200 sticky top-0 z-20">
                                    <tr id="preview_header">
                                        <th class="px-3 py-2 text-left font-bold text-gray-700 border-r border-gray-300 bg-indigo-100 sticky left-0 z-30">#</th>
                                        <!-- Columnas dinámicas se generarán aquí -->
                                    </tr>
                                </thead>
                                <tbody id="preview_body" class="bg-white divide-y divide-gray-200">
                                    <!-- Contenido dinámico -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Botones de acción --}}
                    <div class="flex justify-between items-center gap-3 pt-4 border-t-2 border-gray-200">
                        <button type="button" onclick="volverPaso1()"
                            class="px-5 py-2.5 bg-white border-2 border-gray-300 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-50 transition-all flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Volver
                        </button>
                        <div class="flex gap-3">
                            <button type="button" onclick="cerrarModalCargaMasiva()"
                                class="px-5 py-2.5 bg-white border-2 border-gray-300 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-50 transition-all">
                                Cancelar
                            </button>
                            <button type="submit" id="btn_confirmar"
                                class="px-8 py-2.5 text-white text-sm font-bold rounded-xl transition-all hover:opacity-90 shadow-lg hover:shadow-xl flex items-center gap-2"
                                style="background-color:#4A568D">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                Confirmar Carga
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════════════
     MODAL: Nuevo Producto
═══════════════════════════════════════════════════════════════════════════════ --}}
<div id="modalNuevoProducto" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background:rgba(0,0,0,0.45);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-6xl max-h-[90vh] flex flex-col">
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 text-white" style="background-color:#4A568D">
            <div class="flex items-center gap-3">
                <div class="bg-white/20 p-2 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-lg">Nuevo Producto</h3>
                    <p class="text-xs text-white opacity-90">Completa la información del producto</p>
                </div>
            </div>
            <button onclick="cerrarModalNuevoProducto()" class="text-white/80 hover:text-white hover:bg-white/10 p-2 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Content --}}
        <div class="flex-1 overflow-y-auto px-6 py-5">
            <form id="formNuevoProducto" method="POST" action="{{ route('reportes.entradas.guardar_producto') }}">
                @csrf
                
                {{-- Sección: Identificación --}}
                <div class="mb-6">
                    <h4 class="font-bold text-gray-800 mb-3 flex items-center gap-2 border-b pb-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        Identificación
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Código <span class="text-red-500">*</span>
                                <span class="text-xs font-normal text-indigo-500 ml-1">(se calcula automáticamente)</span>
                            </label>
                            <input type="text" name="codigo" id="input_codigo" required readonly
                                class="w-full border-2 border-indigo-200 bg-indigo-50 rounded-lg px-3 py-2 text-sm font-mono font-bold text-indigo-700 cursor-not-allowed"
                                placeholder="Se generará al seleccionar Componente, Categoría y Familia">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Consecutivo <span class="text-red-500">*</span>
                                <span class="text-xs font-normal text-indigo-500 ml-1">(se calcula automáticamente)</span>
                            </label>
                            <input type="text" name="consecutivo" id="input_consecutivo" required readonly
                                class="w-full border-2 border-indigo-200 bg-indigo-50 rounded-lg px-3 py-2 text-sm font-mono font-bold text-indigo-700 cursor-not-allowed"
                                placeholder="Ej: 0022">
                        </div>
                    </div>
                </div>

                {{-- Sección: Clasificación --}}
                <div class="mb-6">
                    <h4 class="font-bold text-gray-800 mb-3 flex items-center gap-2 border-b pb-2">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        Clasificación
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Componente <span class="text-red-500">*</span>
                            </label>
                            <select name="componente_id" id="sel_componente" required 
                                class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400"
                                onchange="calcularCodigo()">
                                <option value="">Selecciona...</option>
                                @foreach($componentes as $comp)
                                    <option value="{{ $comp->id }}" data-codigo="{{ $comp->codigo }}">{{ $comp->codigo }} – {{ $comp->descripcion }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Categoría <span class="text-red-500">*</span>
                            </label>
                            <select name="categoria_id" id="sel_categoria" required 
                                class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400"
                                onchange="calcularCodigo()">
                                <option value="">Selecciona...</option>
                                @foreach($categorias as $cat)
                                    <option value="{{ $cat->id }}" data-codigo="{{ $cat->codigo }}">{{ $cat->codigo }} – {{ $cat->descripcion }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Familia <span class="text-red-500">*</span>
                            </label>
                            <select name="familia_id" id="sel_familia" required 
                                class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400"
                                onchange="calcularCodigo()">
                                <option value="">Selecciona...</option>
                                @foreach($familias as $fam)
                                    <option value="{{ $fam->id }}" data-codigo="{{ $fam->codigo }}">{{ $fam->codigo }} – {{ $fam->descripcion }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Unidad de Medida <span class="text-red-500">*</span>
                            </label>
                            <select name="unidad_medida_id" required 
                                class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400">
                                <option value="">Selecciona...</option>
                                @foreach($unidadesMedida as $um)
                                    <option value="{{ $um->id }}">{{ $um->codigo }} – {{ $um->descripcion }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Sección: Descripción y Ubicación --}}
                <div class="mb-6">
                    <h4 class="font-bold text-gray-800 mb-3 flex items-center gap-2 border-b pb-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Descripción y Ubicación
                    </h4>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Descripción <span class="text-red-500">*</span>
                            </label>
                            <textarea name="descripcion" required rows="2"
                                class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400"
                                placeholder="Descripción detallada del producto"></textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">
                                    Ubicación <span class="text-gray-400 font-normal">(opcional)</span>
                                </label>
                                <select name="ubicacion_id" 
                                    class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400">
                                    <option value="">Sin ubicación...</option>
                                    @foreach($ubicaciones as $ubi)
                                        <option value="{{ $ubi->id }}">{{ $ubi->codigo }} – {{ $ubi->descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">
                                    Dimensiones <span class="text-gray-400 font-normal">(opcional)</span>
                                </label>
                                <input type="text" name="dimensiones" 
                                    class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400"
                                    placeholder="Ej: 10x20x30 cm">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sección: Cantidades --}}
                <div class="mb-6">
                    <h4 class="font-bold text-gray-800 mb-3 flex items-center gap-2 border-b pb-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                        </svg>
                        Cantidades
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Cantidad Entrada <span class="text-gray-400 font-normal">(opcional)</span>
                            </label>
                            <input type="number" name="cantidad_entrada" id="input_cantidad_entrada" value="0" step="0.01"
                                oninput="document.getElementById('input_cantidad_fisica').value = this.value"
                                class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400">
                        </div>
                        <div class="flex items-end">
                            <p class="text-xs text-gray-500 italic pb-2">
                                La <strong>Cantidad Física</strong> se establecerá igual a la Cantidad Entrada automáticamente.
                            </p>
                        </div>
                    </div>
                    {{-- Campos ocultos: valores derivados --}}
                    <input type="hidden" name="cantidad_salida" value="0">
                    <input type="hidden" name="cantidad_fisica" id="input_cantidad_fisica" value="0">
                </div>

                {{-- Sección: Fechas --}}
                <div class="mb-6">
                    <h4 class="font-bold text-gray-800 mb-3 flex items-center gap-2 border-b pb-2">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Fechas
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Fecha Entrada <span class="text-gray-400 font-normal">(opcional)</span>
                            </label>
                            <input type="date" name="fecha_entrada" 
                                class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Fecha Vencimiento <span class="text-gray-400 font-normal">(opcional)</span>
                            </label>
                            <input type="date" name="fecha_vencimiento" 
                                class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400">
                        </div>
                    </div>
                </div>

                {{-- Sección: Información Financiera --}}
                <div class="mb-6">
                    <h4 class="font-bold text-gray-800 mb-3 flex items-center gap-2 border-b pb-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Información Financiera
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Precio Unitario <span class="text-gray-400 font-normal">(opcional)</span>
                            </label>
                            <input type="number" name="precio_unitario" step="0.01" 
                                class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400"
                                placeholder="0.00">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Moneda <span class="text-gray-400 font-normal">(opcional)</span>
                            </label>
                            <select name="moneda" 
                                class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400">
                                <option value="MXN" selected>MXN - Peso Mexicano</option>
                                <option value="USD">USD - Dólar</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Factura <span class="text-gray-400 font-normal">(opcional)</span>
                            </label>
                            <input type="text" name="factura" 
                                class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400"
                                placeholder="Número de factura">
                        </div>
                    </div>
                </div>

                {{-- Sección: Referencias --}}
                <div class="mb-6">
                    <h4 class="font-bold text-gray-800 mb-3 flex items-center gap-2 border-b pb-2">
                        <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Referencias y Documentos
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Número de Requisición <span class="text-gray-400 font-normal">(opcional)</span>
                            </label>
                            <input type="text" name="numero_requisicion" 
                                class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400"
                                placeholder="Número de requisición">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Número de Parte <span class="text-gray-400 font-normal">(opcional)</span>
                            </label>
                            <input type="text" name="numero_parte" 
                                class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400"
                                placeholder="Número de parte">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Orden de Compra <span class="text-gray-400 font-normal">(opcional)</span>
                            </label>
                            <input type="text" name="orden_compra" 
                                class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400"
                                placeholder="Número de orden de compra">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Hoja de Seguridad <span class="text-gray-400 font-normal">(opcional)</span>
                            </label>
                            <input type="text" name="hoja_seguridad" 
                                class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400"
                                placeholder="Hoja de seguridad">
                        </div>
                    </div>
                </div>

                {{-- Sección: Observaciones --}}
                <div class="mb-6">
                    <h4 class="font-bold text-gray-800 mb-3 flex items-center gap-2 border-b pb-2">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Observaciones
                    </h4>
                    <div>
                        <textarea name="observaciones" rows="3"
                            class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400"
                            placeholder="Observaciones adicionales (opcional)"></textarea>
                    </div>
                </div>

                {{-- Botones de acción --}}
                <div class="flex justify-end gap-3 pt-4 border-t-2 border-gray-200">
                    <button type="button" onclick="cerrarModalNuevoProducto()"
                        class="px-6 py-2.5 bg-white border-2 border-gray-300 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-50 transition-all">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-8 py-2.5 text-white text-sm font-bold rounded-xl transition-all hover:opacity-90 shadow-lg hover:shadow-xl flex items-center gap-2"
                        style="background-color:#4A568D">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Guardar Producto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
/* ═══════════════════════════════════════════════════════════════════
   MODAL NUEVO PRODUCTO
═══════════════════════════════════════════════════════════════════ */
function abrirModalNuevoProducto() {
    const modal = document.getElementById('modalNuevoProducto');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function cerrarModalNuevoProducto() {
    const modal = document.getElementById('modalNuevoProducto');
    modal.classList.replace('flex', 'hidden');
    document.body.style.overflow = '';
    document.getElementById('formNuevoProducto').reset();
}

/* ═══════════════════════════════════════════════════════════════════
   AUTO-CÁLCULO DE CÓDIGO Y CONSECUTIVO
═══════════════════════════════════════════════════════════════════ */
function calcularCodigo() {
    const compSel = document.getElementById('sel_componente');
    const catSel  = document.getElementById('sel_categoria');
    const famSel  = document.getElementById('sel_familia');
    const inputCodigo = document.getElementById('input_codigo');
    const inputCons   = document.getElementById('input_consecutivo');

    if (!compSel.value || !catSel.value || !famSel.value) {
        inputCodigo.value = '';
        inputCons.value   = '';
        inputCodigo.placeholder = 'Se generará al seleccionar Componente, Categoría y Familia';
        return;
    }

    inputCodigo.value = '…';
    inputCons.value   = '…';

    const url = `{{ route('reportes.entradas.proximo_consecutivo') }}?componente_id=${compSel.value}&categoria_id=${catSel.value}&familia_id=${famSel.value}`;

    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            if (data.error) {
                inputCodigo.value = '';
                inputCons.value   = '';
                return;
            }
            inputCodigo.value = data.codigo;
            inputCons.value   = data.siguiente;
        })
        .catch(() => {
            inputCodigo.value = '';
            inputCons.value   = '';
        });
}

/* ═══════════════════════════════════════════════════════════════════
   MODAL CARGA MASIVA CON PREVIEW - ENTRADAS
═══════════════════════════════════════════════════════════════════ */

// Variables globales
let currentWorkbook = null;
let currentFile = null;

function abrirModalCargaMasiva() {
    const m = document.getElementById('modalCargaMasiva');
    m.classList.remove('hidden');
    m.classList.add('flex');
    document.body.style.overflow = 'hidden';
    resetModal();
}

function cerrarModalCargaMasiva() {
    document.getElementById('modalCargaMasiva').classList.replace('flex','hidden');
    document.body.style.overflow = '';
    resetModal();
}

function resetModal() {
    document.getElementById('paso1').classList.remove('hidden');
    document.getElementById('paso2').classList.add('hidden');
    document.getElementById('archivo_excel').value = '';
    document.getElementById('file_info').classList.add('hidden');
    document.getElementById('btn_preview').disabled = true;
    currentWorkbook = null;
    currentFile = null;
    document.getElementById('modal_subtitle').textContent = 'Selecciona el archivo Excel para importar';
}

function volverPaso1() {
    document.getElementById('paso1').classList.remove('hidden');
    document.getElementById('paso2').classList.add('hidden');
    document.getElementById('modal_subtitle').textContent = 'Selecciona el archivo Excel para importar';
}

function handleFileSelect(event) {
    const file = event.target.files[0];
    if (!file) return;
    
    currentFile = file;
    document.getElementById('file_name').textContent = file.name;
    document.getElementById('file_size').textContent = formatFileSize(file.size);
    document.getElementById('file_info').classList.remove('hidden');
    document.getElementById('btn_preview').disabled = false;
    
    const reader = new FileReader();
    reader.onload = function(e) {
        try {
            const data = new Uint8Array(e.target.result);
            const workbook = XLSX.read(data, {type: 'array'});
            currentWorkbook = workbook;
            const numSheets = workbook.SheetNames.length;
            document.getElementById('file_sheets').textContent = `${numSheets} ${numSheets === 1 ? 'hoja' : 'hojas'}`;
        } catch (error) {
            console.error('Error leyendo archivo:', error);
            alert('Error al leer el archivo. Verifica que sea un archivo Excel válido.');
        }
    };
    reader.readAsArrayBuffer(file);
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

function generarPreview() {
    if (!currentWorkbook) {
        alert('Por favor selecciona un archivo primero');
        return;
    }
    
    const sheetNumber = parseInt(document.getElementById('sheet_number').value);
    if (!sheetNumber || sheetNumber < 1) {
        alert('Por favor ingresa un número de página válido');
        return;
    }
    
    const sheetIndex = sheetNumber - 1;
    if (sheetIndex >= currentWorkbook.SheetNames.length) {
        alert(`El archivo solo tiene ${currentWorkbook.SheetNames.length} hoja(s)`);
        return;
    }
    
    const sheetName = currentWorkbook.SheetNames[sheetIndex];
    const worksheet = currentWorkbook.Sheets[sheetName];
    const data = XLSX.utils.sheet_to_json(worksheet, {header: 1, defval: ''});
    
    if (data.length === 0) {
        alert('❌ La hoja seleccionada está vacía');
        return;
    }
    
    // Detectar header
    let headerRowIndex = -1;
    let headers = [];
    
    for (let i = 0; i < Math.min(10, data.length); i++) {
        const row = data[i];
        if (!row || row.every(cell => !cell)) continue;
        
        const potentialHeaders = row.map(h => h ? h.toString().toUpperCase().trim() : '');
        const tieneCodigoCol = potentialHeaders.includes('CODIGO');
        const tieneDescripcionCol = potentialHeaders.some(h => h.includes('DESCRIPCIÓN') || h.includes('DESCRIPCION'));
        
        if (tieneCodigoCol && tieneDescripcionCol) {
            headerRowIndex = i;
            headers = potentialHeaders;
            break;
        }
    }
    
    if (headerRowIndex === -1) {
        alert('❌ No se pudo detectar el encabezado. Asegúrate de que hay una fila con CODIGO y DESCRIPCIÓN');
        return;
    }
    
    const rows = data.slice(headerRowIndex + 1);
    const codigoIndex = headers.findIndex(h => h === 'CODIGO');
    
    // Contar válidos
    let validos = rows.filter(row => {
        const codigo = row[codigoIndex] ? row[codigoIndex].toString().trim() : '';
        return codigo && row.some(cell => cell !== '');
    }).length;
    
    // Actualizar vista
    mostrarPreviewTabla(rows, headers, codigoIndex);
    document.getElementById('preview_total').textContent = rows.length;
    document.getElementById('preview_validos').textContent = validos;
    document.getElementById('columns_count').textContent = `${headers.length} columnas`;
    document.getElementById('header_row_number').textContent = `Fila ${headerRowIndex + 1}`;
    document.getElementById('modal_subtitle').textContent = `Preview: ${validos} registros listos`;
    
    document.getElementById('paso1').classList.add('hidden');
    document.getElementById('paso2').classList.remove('hidden');
}

function mostrarPreviewTabla(rows, headers, codigoIndex) {
    const theadRow = document.getElementById('preview_header');
    theadRow.innerHTML = '<th class="px-3 py-2 text-left font-bold text-gray-700 border-r border-gray-300 bg-indigo-100 sticky left-0 z-30">#</th>';
    
    headers.forEach((header, index) => {
        const isCodigoCol = index === codigoIndex;
        const bgClass = isCodigoCol ? 'bg-green-100' : 'bg-gray-200';
        theadRow.innerHTML += `<th class="px-3 py-2 text-left font-bold text-gray-700 border-r border-gray-300 ${bgClass} whitespace-nowrap">${header || `Col ${index + 1}`}</th>`;
    });
    
    const tbody = document.getElementById('preview_body');
    tbody.innerHTML = '';
    
    rows.slice(0, 10).forEach((row, index) => {
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-green-50';
        
        let cellsHTML = `<td class="px-3 py-2 border-r border-gray-200 text-gray-500 font-mono bg-indigo-50 sticky left-0 z-10">${index + 1}</td>`;
        
        headers.forEach((header, colIndex) => {
            let cellValue = row[colIndex] !== undefined && row[colIndex] !== null ? row[colIndex].toString() : '-';
            let cellClass = 'px-3 py-2 border-r border-gray-200 text-gray-700';
            
            if (colIndex === codigoIndex) {
                cellValue = cellValue !== '-' ? `<span class="font-bold text-green-700">${cellValue}</span>` : cellValue;
            } else if (header && (header.includes('ENTRADA') || header.includes('SALIDA') || header.includes('FISICO'))) {
                cellClass = 'px-3 py-2 border-r border-gray-200 font-semibold text-blue-700 text-right';
            }
            
            cellsHTML += `<td class="${cellClass}">${cellValue}</td>`;
        });
        
        tr.innerHTML = cellsHTML;
        tbody.appendChild(tr);
    });
}

document.getElementById('modalCargaMasiva').addEventListener('click', e => { 
    if (e.target === document.getElementById('modalCargaMasiva')) cerrarModalCargaMasiva(); 
});

document.getElementById('formCargaMasiva').addEventListener('submit', function() {
    const btn = document.getElementById('btn_confirmar');
    btn.disabled = true;
    btn.innerHTML = `<svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Cargando...`;
});

// ==================== BÚSQUEDA DINÁMICA CON API ====================
let searchTimeout = null;
let currentSearchQuery = '';

function buscarDinamico(event) {
    const input = event.target;
    const query = input.value.trim();
    
    // Ocultar sugerencias si query está vacío o es muy corto
    if (query.length < 2) {
        ocultarSugerencias();
        return;
    }
    
    // Si es Enter, enviar formulario
    if (event.key === 'Enter') {
        ocultarSugerencias();
        input.form.submit();
        return;
    }
    
    // Si es Escape, ocultar sugerencias
    if (event.key === 'Escape') {
        ocultarSugerencias();
        return;
    }
    
    // Evitar búsquedas repetidas
    if (query === currentSearchQuery) {
        return;
    }
    
    currentSearchQuery = query;
    
    // Cancelar búsqueda anterior (debounce)
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    
    // Esperar 300ms antes de buscar
    searchTimeout = setTimeout(() => {
        realizarBusquedaAPI(query);
    }, 300);
}

async function realizarBusquedaAPI(query) {
    const suggestionBox = document.getElementById('suggestionBox');
    const loadingDiv = document.getElementById('loadingSuggestions');
    const contentDiv = document.getElementById('suggestionsContent');
    
    // Mostrar loading
    suggestionBox.classList.remove('hidden');
    loadingDiv.classList.remove('hidden');
    contentDiv.innerHTML = '';
    
    try {
        const response = await fetch(`/api/v1/productos/buscar?q=${encodeURIComponent(query)}&limit=10`);
        const data = await response.json();
        
        loadingDiv.classList.add('hidden');
        
        if (data.total === 0) {
            contentDiv.innerHTML = `
                <div class="px-4 py-3 text-sm text-gray-500 text-center">
                    <svg class="inline-block w-5 h-5 text-gray-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    No se encontraron resultados para "<strong>${escapeHtml(query)}</strong>"
                </div>
            `;
        } else {
            renderizarSugerencias(data.data, query, data.total);
        }
    } catch (error) {
        loadingDiv.classList.add('hidden');
        contentDiv.innerHTML = `
            <div class="px-4 py-3 text-sm text-red-600">
                <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Error al buscar productos
            </div>
        `;
    }
}

function renderizarSugerencias(productos, query, total) {
    const contentDiv = document.getElementById('suggestionsContent');
    
    let html = '';
    
    // Header con total de resultados
    html += `
        <div class="sticky top-0 bg-indigo-50 px-4 py-2 border-b border-gray-200 flex justify-between items-center">
            <span class="text-xs font-semibold text-indigo-800">Resultados de búsqueda</span>
            <span class="text-xs font-bold text-indigo-600">${total} encontrados</span>
        </div>
    `;
    
    // Productos
    productos.forEach((producto, index) => {
        const codigo = highlightText(producto.codigo, query);
        const descripcion = highlightText(producto.descripcion || 'Sin descripción', query);
        const ubicacion = producto.ubicacion ? highlightText(producto.ubicacion, query) : '<span class="text-gray-400">S/U</span>';
        const um = producto.um || '-';
        const fisico = producto.fisico || 0;
        const pu = parseFloat(producto.pu || 0).toFixed(2);
        
        html += `
            <div class="suggestion-item px-4 py-3 hover:bg-indigo-50 cursor-pointer border-b border-gray-100 transition"
                 onclick="seleccionarSugerencia('${escapeHtml(producto.codigo)}')">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                        <span class="text-xs font-bold text-indigo-600">${index + 1}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-mono text-sm font-bold text-indigo-600">${codigo}</span>
                            <span class="text-xs px-2 py-0.5 bg-gray-100 text-gray-600 rounded">${um}</span>
                        </div>
                        <p class="text-sm text-gray-900 truncate">${descripcion}</p>
                        <div class="flex items-center gap-3 mt-1 text-xs text-gray-500">
                            <span>📍 ${ubicacion}</span>
                            <span>📦 Stock: <strong class="text-gray-700">${fisico}</strong></span>
                            <span>💰 $${pu}</span>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    // Footer - Ver todos
    if (total > 10) {
        html += `
            <div class="px-4 py-2 bg-gray-50 text-center">
                <button type="submit" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                    Ver todos los ${total} resultados →
                </button>
            </div>
        `;
    }
    
    contentDiv.innerHTML = html;
}

function seleccionarSugerencia(codigo) {
    const input = document.getElementById('searchInput');
    input.value = codigo;
    input.form.submit();
}

function ocultarSugerencias() {
    document.getElementById('suggestionBox').classList.add('hidden');
}

function highlightText(text, query) {
    if (!text) return '';
    const regex = new RegExp(`(${escapeRegExp(query)})`, 'gi');
    return text.replace(regex, '<mark class="bg-yellow-200 px-0.5 font-semibold">$1</mark>');
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function escapeRegExp(string) {
    return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

// Cerrar sugerencias al hacer clic fuera
document.addEventListener('click', function(event) {
    const suggestionBox = document.getElementById('suggestionBox');
    const searchInput = document.getElementById('searchInput');
    
    if (!suggestionBox.contains(event.target) && event.target !== searchInput) {
        ocultarSugerencias();
    }
});

// Navegación con teclado en sugerencias
document.getElementById('searchInput').addEventListener('keydown', function(event) {
    const suggestionBox = document.getElementById('suggestionBox');
    
    if (suggestionBox.classList.contains('hidden')) return;
    
    const items = suggestionBox.querySelectorAll('.suggestion-item');
    if (items.length === 0) return;
    
    let currentIndex = -1;
    items.forEach((item, index) => {
        if (item.classList.contains('bg-indigo-100')) {
            currentIndex = index;
        }
    });
    
    // Flecha abajo
    if (event.key === 'ArrowDown') {
        event.preventDefault();
        if (currentIndex < items.length - 1) {
            if (currentIndex >= 0) items[currentIndex].classList.remove('bg-indigo-100');
            items[currentIndex + 1].classList.add('bg-indigo-100');
            items[currentIndex + 1].scrollIntoView({ block: 'nearest' });
        }
    }
    
    // Flecha arriba
    if (event.key === 'ArrowUp') {
        event.preventDefault();
        if (currentIndex > 0) {
            items[currentIndex].classList.remove('bg-indigo-100');
            items[currentIndex - 1].classList.add('bg-indigo-100');
            items[currentIndex - 1].scrollIntoView({ block: 'nearest' });
        }
    }
    
    // Enter en un item seleccionado
    if (event.key === 'Enter' && currentIndex >= 0) {
        event.preventDefault();
        items[currentIndex].click();
    }
});
</script>

{{-- ═══════════════════════════════════════════════════════════════════════════════
     MODAL: Editar Producto
═══════════════════════════════════════════════════════════════════════════════ --}}
<div id="modalEditarProducto" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background:rgba(0,0,0,0.45);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl max-h-[92vh] flex flex-col">
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 text-white rounded-t-2xl" style="background-color:#4A568D">
            <div class="flex items-center gap-3">
                <div class="bg-white/20 p-2 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-base">Editar Producto</h3>
                    <p id="editSubtitle" class="text-xs text-white opacity-90">Modificar información del producto</p>
                </div>
            </div>
            <button onclick="cerrarModalEditar()" class="text-white/80 hover:text-white hover:bg-white/10 p-2 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="flex-1 overflow-y-auto px-6 py-5">
            <form id="formEditarProducto" method="POST">
                @csrf
                <input type="hidden" name="_method" value="PATCH">

                {{-- Sección: Identificación --}}
                <div class="mb-5">
                    <h4 class="font-bold text-gray-800 mb-3 flex items-center gap-2 border-b pb-2 text-sm">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        Identificación
                    </h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Código <span class="text-gray-400 font-normal">(solo lectura)</span></label>
                            <input type="text" id="edit_codigo" readonly
                                class="w-full border-2 border-gray-200 bg-gray-50 rounded-lg px-3 py-2 text-sm font-mono font-bold text-gray-500 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Consecutivo <span class="text-gray-400 font-normal">(solo lectura)</span></label>
                            <input type="text" id="edit_consecutivo" readonly
                                class="w-full border-2 border-gray-200 bg-gray-50 rounded-lg px-3 py-2 text-sm font-mono text-gray-500 cursor-not-allowed">
                        </div>
                    </div>
                </div>

                {{-- Sección: Clasificación --}}
                <div class="mb-5">
                    <h4 class="font-bold text-gray-800 mb-3 flex items-center gap-2 border-b pb-2 text-sm">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        Clasificación
                    </h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Componente <span class="text-red-500">*</span></label>
                            <select name="componente_id" id="edit_componente_id" required
                                class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                <option value="">Selecciona...</option>
                                @foreach($componentes as $comp)
                                    <option value="{{ $comp->id }}">{{ $comp->codigo }} – {{ $comp->descripcion }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Categoría <span class="text-red-500">*</span></label>
                            <select name="categoria_id" id="edit_categoria_id" required
                                class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                <option value="">Selecciona...</option>
                                @foreach($categorias as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->codigo }} – {{ $cat->descripcion }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Familia <span class="text-red-500">*</span></label>
                            <select name="familia_id" id="edit_familia_id" required
                                class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                <option value="">Selecciona...</option>
                                @foreach($familias as $fam)
                                    <option value="{{ $fam->id }}">{{ $fam->codigo }} – {{ $fam->descripcion }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Unidad de Medida <span class="text-red-500">*</span></label>
                            <select name="unidad_medida_id" id="edit_unidad_medida_id" required
                                class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                <option value="">Selecciona...</option>
                                @foreach($unidadesMedida as $um)
                                    <option value="{{ $um->id }}">{{ $um->codigo }} – {{ $um->descripcion }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Sección: Descripción y Ubicación --}}
                <div class="mb-5">
                    <h4 class="font-bold text-gray-800 mb-3 flex items-center gap-2 border-b pb-2 text-sm">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Descripción, Ubicación y Dimensiones
                    </h4>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Descripción <span class="text-red-500">*</span></label>
                            <textarea name="descripcion" id="edit_descripcion" required rows="2"
                                class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Ubicación</label>
                                <select name="ubicacion_id" id="edit_ubicacion_id"
                                    class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                    <option value="">Sin ubicación</option>
                                    @foreach($ubicaciones as $ubi)
                                        <option value="{{ $ubi->id }}">{{ $ubi->codigo }} – {{ $ubi->descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Dimensiones</label>
                                <input type="text" name="dimensiones" id="edit_dimensiones"
                                    class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
                                    placeholder="Ej: 10x20x30 cm">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sección: Cantidades --}}
                <div class="mb-5">
                    <h4 class="font-bold text-gray-800 mb-3 flex items-center gap-2 border-b pb-2 text-sm">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                        </svg>
                        Cantidades
                    </h4>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Cantidad Entrada</label>
                            <input type="number" name="cantidad_entrada" id="edit_cantidad_entrada" step="0.01" min="0"
                                class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Cantidad Salida</label>
                            <input type="number" name="cantidad_salida" id="edit_cantidad_salida" step="0.01" min="0"
                                class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Cantidad Física</label>
                            <input type="number" name="cantidad_fisica" id="edit_cantidad_fisica" step="0.01" min="0"
                                class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>
                    </div>
                </div>

                {{-- Sección: Fechas --}}
                <div class="mb-5">
                    <h4 class="font-bold text-gray-800 mb-3 flex items-center gap-2 border-b pb-2 text-sm">
                        <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Fechas
                    </h4>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Fecha Entrada</label>
                            <input type="date" name="fecha_entrada" id="edit_fecha_entrada"
                                class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Fecha Salida</label>
                            <input type="date" name="fecha_salida" id="edit_fecha_salida"
                                class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Fecha Vencimiento</label>
                            <input type="date" name="fecha_vencimiento" id="edit_fecha_vencimiento"
                                class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>
                    </div>
                </div>

                {{-- Sección: Financiera --}}
                <div class="mb-5">
                    <h4 class="font-bold text-gray-800 mb-3 flex items-center gap-2 border-b pb-2 text-sm">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Información Financiera
                    </h4>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Precio Unitario</label>
                            <input type="number" name="precio_unitario" id="edit_precio_unitario" step="0.01" min="0"
                                class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
                                placeholder="0.00">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Moneda</label>
                            <select name="moneda" id="edit_moneda"
                                class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                <option value="MXN">MXN – Peso Mexicano</option>
                                <option value="USD">USD – Dólar</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Factura</label>
                            <input type="text" name="factura" id="edit_factura" maxlength="50"
                                class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
                                placeholder="Número de factura">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Orden de Compra</label>
                            <input type="text" name="orden_compra" id="edit_orden_compra" maxlength="50"
                                class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
                                placeholder="Número de O.C.">
                        </div>
                    </div>
                </div>

                {{-- Sección: Referencias --}}
                <div class="mb-5">
                    <h4 class="font-bold text-gray-800 mb-3 flex items-center gap-2 border-b pb-2 text-sm">
                        <svg class="w-4 h-4 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Referencias y Documentos
                    </h4>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Núm. Requisición</label>
                            <input type="text" name="numero_requisicion" id="edit_numero_requisicion" maxlength="50"
                                class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Número de Parte</label>
                            <input type="text" name="numero_parte" id="edit_numero_parte" maxlength="100"
                                class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Hoja de Seguridad</label>
                            <input type="text" name="hoja_seguridad" id="edit_hoja_seguridad" maxlength="255"
                                class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>
                    </div>
                </div>

                {{-- Sección: Observaciones --}}
                <div class="mb-5">
                    <h4 class="font-bold text-gray-800 mb-3 flex items-center gap-2 border-b pb-2 text-sm">
                        <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Observaciones (DN / NP)
                    </h4>
                    <textarea name="observaciones" id="edit_observaciones" rows="3"
                        class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
                        placeholder="Observaciones adicionales (opcional)"></textarea>
                </div>

                {{-- Botones --}}
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                    <button type="button" onclick="cerrarModalEditar()"
                        class="px-5 py-2 bg-white border-2 border-gray-300 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-50 transition">
                        Cancelar
                    </button>
                    <button type="submit" id="btnGuardarEditar"
                        class="px-6 py-2 text-white text-sm font-bold rounded-xl transition hover:opacity-90 shadow-lg flex items-center gap-2"
                        style="background-color:#4A568D">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════════════
     MODAL: Nueva Requisición
═══════════════════════════════════════════════════════════════════════════════ --}}
<div id="modalRequisicion" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background:rgba(0,0,0,0.45);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[92vh] flex flex-col">
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 rounded-t-2xl text-white bg-[#4A568D]">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span class="font-semibold text-base">Nueva Solicitud de Material</span>
            </div>
            <button onclick="cerrarModalRequisicion()" class="text-white opacity-70 hover:opacity-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <form method="POST" action="{{ route('solicitudes.nueva') }}" id="formRequisicion" class="overflow-y-auto flex-1 px-6 py-5 space-y-4">
            @csrf

            {{-- Fila 1: Fecha / Fecha req. / Folio --}}
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Fecha de Solicitud</label>
                    <input type="date" name="fecha" value="{{ now()->format('Y-m-d') }}" readonly 
                        class="w-full border bg-gray-100 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Fecha Requerida</label>
                    <input type="date" name="fecha_requerida"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Folio <span class="text-gray-400 font-normal">(opcional)</span></label>
                    <input type="text" name="folio" placeholder="Ej. REQ-001"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400">
                </div>
            </div>

            {{-- Solicitante --}}

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Solicitante</label>
                <input type="text" name="solicitante" value="{{ auth()->user()->name }}" required
                    class="w-full border bg-gray-100 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400" readonly>
            </div>

            {{-- Departamento (tomado automáticamente de la API de RH) --}}
            <input type="hidden" name="departamento_id" id="req_depto_id">
            <input type="hidden" name="departamento_nombre" id="req_depto_nombre">

            {{-- Producto (solo lectura) --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Producto <span class="text-red-500">*</span></label>
                <div class="flex items-center gap-2 px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm">
                    <svg class="w-4 h-4 shrink-0 text-[#4A568D]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <span id="req_prod_display" class="font-medium text-gray-800">—</span>
                </div>
                <input type="hidden" name="producto_id" id="req_prod_id">
            </div>

            {{-- Cantidad + UM --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Cantidad <span class="text-red-500">*</span> <span id="req_max_label" class="text-gray-400 font-normal"></span></label>
                    <input type="number" name="cantidad" id="req_cantidad" min="0.01" step="any" placeholder="0" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Unidad de Medida</label>
                    <div class="flex items-center px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700 min-h-[38px]">
                        <span id="req_um_display">—</span>
                    </div>
                    <input type="hidden" name="unidad_medida_id" id="req_um_id">
                </div>
            </div>

            {{-- Observaciones --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">DN / NP / Observaciones</label>
                <textarea name="observaciones" rows="2" placeholder="Número de parte, número de diseño u observaciones adicionales..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 resize-none"></textarea>
            </div>
        </form>

        {{-- Footer --}}
        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between gap-3 rounded-b-2xl bg-gray-50">
            <p class="text-xs text-gray-400"><span class="text-red-500">*</span> Campos requeridos</p>
            <div class="flex gap-3">
                <button type="button" onclick="cerrarModalRequisicion()"
                    class="px-5 py-2 bg-white border border-gray-300 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-50 transition">
                    Cancelar
                </button>
                <button type="submit" form="formRequisicion" id="btnGuardarReq"
                    class="px-6 py-2 bg-[#4A568D] text-white text-sm font-semibold rounded-xl transition hover:bg-[#3B466D] active:scale-95 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Guardar Solicitud
                </button>
            </div>
        </div>
    </div>
</div>

<script>
/* ═══════════════════════════════════════════════════════════════════
   MODAL EDITAR PRODUCTO
═══════════════════════════════════════════════════════════════════ */
function abrirModalEditar(data) {
    const form = document.getElementById('formEditarProducto');
    form.action = '/reportes/entradas/' + data.id;

    document.getElementById('editSubtitle').textContent = 'Modificar «' + data.codigo + '»';
    document.getElementById('edit_codigo').value       = data.codigo       ?? '';
    document.getElementById('edit_consecutivo').value  = data.consecutivo  ?? '';
    document.getElementById('edit_descripcion').value  = data.descripcion  ?? '';
    document.getElementById('edit_dimensiones').value  = data.dimensiones  ?? '';
    document.getElementById('edit_cantidad_entrada').value  = data.cantidad_entrada  ?? '';
    document.getElementById('edit_cantidad_salida').value   = data.cantidad_salida   ?? '';
    document.getElementById('edit_cantidad_fisica').value   = data.cantidad_fisica   ?? '';
    document.getElementById('edit_fecha_entrada').value     = data.fecha_entrada     ?? '';
    document.getElementById('edit_fecha_salida').value      = data.fecha_salida      ?? '';
    document.getElementById('edit_fecha_vencimiento').value = data.fecha_vencimiento ?? '';
    document.getElementById('edit_precio_unitario').value   = data.precio_unitario   ?? '';
    document.getElementById('edit_factura').value           = data.factura           ?? '';
    document.getElementById('edit_orden_compra').value      = data.orden_compra      ?? '';
    document.getElementById('edit_numero_requisicion').value= data.numero_requisicion?? '';
    document.getElementById('edit_numero_parte').value      = data.numero_parte      ?? '';
    document.getElementById('edit_hoja_seguridad').value    = data.hoja_seguridad    ?? '';
    document.getElementById('edit_observaciones').value     = data.observaciones     ?? '';

    // Selects
    setSelectVal('edit_componente_id',    data.componente_id);
    setSelectVal('edit_categoria_id',     data.categoria_id);
    setSelectVal('edit_familia_id',       data.familia_id);
    setSelectVal('edit_unidad_medida_id', data.unidad_medida_id);
    setSelectVal('edit_ubicacion_id',     data.ubicacion_id);
    setSelectVal('edit_moneda',           data.moneda ?? 'MXN');

    const btn = document.getElementById('btnGuardarEditar');
    btn.disabled = false;
    btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Guardar Cambios';

    const m = document.getElementById('modalEditarProducto');
    m.classList.remove('hidden');
    m.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function setSelectVal(id, val) {
    const sel = document.getElementById(id);
    if (!sel) return;
    const str = val !== null && val !== undefined ? String(val) : '';
    for (const opt of sel.options) {
        opt.selected = opt.value === str;
    }
}

function cerrarModalEditar() {
    document.getElementById('modalEditarProducto').classList.replace('flex','hidden');
    document.body.style.overflow = '';
}

document.getElementById('modalEditarProducto')?.addEventListener('click', e => {
    if (e.target === document.getElementById('modalEditarProducto')) cerrarModalEditar();
});

document.getElementById('formEditarProducto')?.addEventListener('submit', function () {
    const btn = document.getElementById('btnGuardarEditar');
    btn.disabled = true;
    btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Guardando...';
});

/* ═══════════════════════════════════════════════════════════════════
   MODAL REQUISICIÓN (con pre-llenado opcional de producto)
═══════════════════════════════════════════════════════════════════ */
const CSRF_REQ = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

function abrirModalRequisicion(productoId, productoCodigo, productoDesc, umId, umCodigo, disponible) {
    const m = document.getElementById('modalRequisicion');
    m.classList.remove('hidden');
    m.classList.add('flex');
    document.body.style.overflow = 'hidden';
    document.getElementById('formRequisicion').reset();

    // Producto (solo lectura)
    document.getElementById('req_prod_id').value            = productoId   || '';
    document.getElementById('req_prod_display').textContent = productoCodigo + (productoDesc ? ' — ' + productoDesc : '');

    // Unidad de medida del producto (solo lectura)
    document.getElementById('req_um_id').value           = umId     || '';
    document.getElementById('req_um_display').textContent = umCodigo || '—';

    // Limitar cantidad máxima a la disponible
    const cantidadInput = document.getElementById('req_cantidad');
    const maxLabel = document.getElementById('req_max_label');
    if (disponible !== undefined && disponible !== null) {
        cantidadInput.max = disponible;
        maxLabel.textContent = '(máx. ' + Number(disponible).toFixed(2) + ')';
    } else {
        cantidadInput.removeAttribute('max');
        maxLabel.textContent = '';
    }

    const btn = document.getElementById('btnGuardarReq');
    btn.disabled = false;
    btn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Guardar Solicitud`;

    // Obtener departamento del usuario desde la API de RH
    const deptoIdEl  = document.getElementById('req_depto_id');
    const deptoNomEl = document.getElementById('req_depto_nombre');
    deptoIdEl.value  = '';
    deptoNomEl.value = '';

    fetch('{{ route("usuarios.info_rh") }}', {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(info => {
        if (info.departamento_nombre) {
            deptoNomEl.value = info.departamento_nombre;
            deptoIdEl.value  = info.departamento_id ?? '';
        }
    })
    .catch(() => {})
    .finally(() => {
        setTimeout(() => document.querySelector('#formRequisicion [name="cantidad"]')?.focus(), 120);
    });
}

function cerrarModalRequisicion() {
    const m = document.getElementById('modalRequisicion');
    m.classList.add('hidden');
    m.classList.remove('flex');
    document.body.style.overflow = '';
}

// Cerrar modal con Escape o clic fuera
document.getElementById('modalRequisicion')?.addEventListener('click', e => {
    if (e.target === document.getElementById('modalRequisicion')) cerrarModalRequisicion();
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape' && !document.getElementById('modalRequisicion').classList.contains('hidden')) {
        cerrarModalRequisicion();
    }
});



/* ─── Validación pre-submit ──────────────── */
document.getElementById('formRequisicion')?.addEventListener('submit', function (e) {
    if (!document.getElementById('req_prod_id').value) { e.preventDefault(); return; }
    const btn = document.getElementById('btnGuardarReq');
    btn.disabled = true;
    btn.innerHTML = `<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Guardando...`;
});

/* ─── Modal No Conformidad ─────────────────── */
function abrirModalNC(prodId, codigo, descripcion) {
    document.getElementById('nc_producto_id').value = prodId;
    document.getElementById('nc_prod_info').textContent = codigo + ' — ' + descripcion;
    document.getElementById('nc_cantidad').value = '';
    document.getElementById('nc_descripcion').value = '';
    const m = document.getElementById('modalNCEntradas');
    m.classList.remove('hidden');
    m.classList.add('flex');
    document.body.style.overflow = 'hidden';
    setTimeout(() => document.getElementById('nc_cantidad').focus(), 100);
}
function cerrarModalNCEntradas() {
    document.getElementById('modalNCEntradas').classList.replace('flex','hidden');
    document.body.style.overflow = '';
}
document.getElementById('modalNCEntradas')?.addEventListener('click', e => { if (e.target.id === 'modalNCEntradas') cerrarModalNCEntradas(); });

async function guardarNCEntradas() {
    const prodId = document.getElementById('nc_producto_id').value;
    const cantidad = document.getElementById('nc_cantidad').value.trim();
    const descripcion = document.getElementById('nc_descripcion').value.trim();
    const btn = document.getElementById('btnGuardarNCEntradas');

    if (!cantidad || parseFloat(cantidad) <= 0) {
        document.getElementById('nc_cantidad').classList.add('border-red-400','ring-2','ring-red-200');
        document.getElementById('nc_cantidad').focus();
        setTimeout(() => document.getElementById('nc_cantidad').classList.remove('border-red-400','ring-2','ring-red-200'), 2500);
        return;
    }
    if (!descripcion) {
        document.getElementById('nc_descripcion').classList.add('border-red-400','ring-2','ring-red-200');
        document.getElementById('nc_descripcion').focus();
        setTimeout(() => document.getElementById('nc_descripcion').classList.remove('border-red-400','ring-2','ring-red-200'), 2500);
        return;
    }

    btn.disabled = true;
    btn.textContent = 'Guardando...';

    try {
        const res = await fetch('/no-conformidades', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ producto_id: prodId, cantidad: cantidad, descripcion: descripcion })
        });
        if (!res.ok) throw new Error('HTTP ' + res.status);
        cerrarModalNCEntradas();
        // Show quick success feedback
        const flash = document.createElement('div');
        flash.className = 'fixed top-4 right-4 z-[60] flex items-center gap-2 px-4 py-3 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm shadow-lg';
        flash.innerHTML = '<svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> No conformidad registrada correctamente.';
        document.body.appendChild(flash);
        setTimeout(() => flash.remove(), 3500);
    } catch(err) {
        alert('Error al registrar la no conformidad.');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg> Registrar';
    }
}


</script>

{{-- MODAL: Registrar No Conformidad desde Entradas --}}
@if(!auth()->user()->hasRole('visitante'))
<div id="modalNCEntradas" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background:rgba(0,0,0,0.45);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 rounded-t-2xl text-white" style="background-color:#4A568D;">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span class="font-semibold">Registrar No Conformidad</span>
            </div>
            <button onclick="cerrarModalNCEntradas()" class="text-white opacity-70 hover:opacity-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="px-6 py-5 space-y-4">
            <input type="hidden" id="nc_producto_id">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Producto</label>
                <div id="nc_prod_info" class="px-3 py-2 rounded-lg text-sm font-medium border" style="background:#eef0f8;border-color:#c7cfe7;color:#4A568D;"></div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Cantidad afectada <span class="text-red-500">*</span></label>
                <input type="number" id="nc_cantidad" step="0.01" min="0.01"
                    placeholder="Ej: 5.00"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Descripción de la incidencia <span class="text-red-500">*</span></label>
                <textarea id="nc_descripcion" rows="3"
                    placeholder="Describe el motivo de la no conformidad..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 resize-none"></textarea>
            </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3 rounded-b-2xl bg-gray-50">
            <button type="button" onclick="cerrarModalNCEntradas()"
                class="px-5 py-2 bg-white border border-gray-300 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-50 transition">
                Cancelar
            </button>
            <button type="button" id="btnGuardarNCEntradas" onclick="guardarNCEntradas()"
                class="px-6 py-2 text-white text-sm font-semibold rounded-xl transition hover:opacity-90 flex items-center gap-2"
                style="background-color:#4A568D;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                Registrar
            </button>
        </div>
    </div>
</div>
@endif

{{-- ═══════════════════════════════════════════════════════════════
     MODAL MOVER PIEZA (Solicitud de Movimiento)
═══════════════════════════════════════════════════════════════ --}}
<div id="modalMoverPieza" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background:rgba(0,0,0,0.45);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[92vh] flex flex-col">
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 rounded-t-2xl text-white" style="background-color:#0d7a6b;">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
                <span class="font-semibold text-base">Nueva Solicitud de Movimiento</span>
            </div>
            <button onclick="cerrarModalMoverPieza()" class="text-white opacity-70 hover:opacity-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <form method="POST" action="{{ route('tickets.store') }}" id="formMoverPieza"
              enctype="multipart/form-data" class="overflow-y-auto flex-1 px-6 py-5 space-y-4">
            @csrf
            <input type="hidden" name="producto_id" id="mp_producto_id">

            {{-- Producto (solo lectura) --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Producto</label>
                <div class="flex items-center gap-2 px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm">
                    <svg class="w-4 h-4 shrink-0" style="color:#0d7a6b" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <span id="mp_prod_display" class="font-medium text-gray-800">—</span>
                </div>
            </div>

            {{-- Título --}}
            <div>
                <label for="mp_title" class="block text-xs font-semibold text-gray-600 mb-1">
                    Título de la solicitud <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" id="mp_title" required
                       placeholder="Ej: Mover piezas del almacén A a zona de producción"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 transition" style="--tw-ring-color:#0d7a6b;">
            </div>

            {{-- Descripción --}}
            <div>
                <label for="mp_description" class="block text-xs font-semibold text-gray-600 mb-1">
                    Descripción detallada <span class="text-red-500">*</span>
                </label>
                <textarea name="description" id="mp_description" rows="4" required
                    placeholder="Incluye: tipo de movimiento, cantidad, origen, destino, instrucciones especiales..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 resize-none transition"></textarea>
                <p class="text-xs text-gray-400 mt-1">Máximo 5,000 caracteres</p>
            </div>

            {{-- Imágenes --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">
                    Imágenes de referencia <span class="text-gray-400 font-normal">(opcional, máx. 5)</span>
                </label>
                <div id="mp_dropZone"
                     class="border-2 border-dashed border-gray-300 rounded-xl p-5 text-center hover:border-teal-400 transition cursor-pointer">
                    <svg class="mx-auto w-8 h-8 text-gray-300 mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-sm text-gray-500">Haz clic o arrastra imágenes aquí</p>
                    <p class="text-xs text-gray-400 mt-0.5">PNG, JPG, JPEG, GIF — máx. 2 MB c/u</p>
                    <input type="file" name="images[]" id="mp_images" multiple accept="image/*" class="hidden">
                </div>
                <div id="mp_imagePreview" class="mt-2 grid grid-cols-5 gap-2 hidden"></div>
            </div>
        </form>

        {{-- Footer --}}
        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between gap-3 rounded-b-2xl bg-gray-50">
            <p class="text-xs text-gray-400"><span class="text-red-500">*</span> Campos requeridos</p>
            <div class="flex gap-3">
                <button type="button" onclick="cerrarModalMoverPieza()"
                    class="px-5 py-2 bg-white border border-gray-300 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-50 transition">
                    Cancelar
                </button>
                <button type="submit" form="formMoverPieza" id="btnGuardarMoverPieza"
                    class="px-6 py-2 text-white text-sm font-semibold rounded-xl transition hover:opacity-90 active:scale-95 flex items-center gap-2"
                    style="background-color:#0d7a6b;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    Enviar Solicitud
                </button>
            </div>
        </div>
    </div>
</div>

<script>
/* ═══════════════════════════════════════════════════════════════════
   MODAL MOVER PIEZA
═══════════════════════════════════════════════════════════════════ */
function abrirModalMoverPieza(productoId, codigo, descripcion) {
    const m = document.getElementById('modalMoverPieza');
    m.classList.remove('hidden');
    m.classList.add('flex');
    document.body.style.overflow = 'hidden';

    document.getElementById('formMoverPieza').reset();
    document.getElementById('mp_imagePreview').innerHTML = '';
    document.getElementById('mp_imagePreview').classList.add('hidden');

    document.getElementById('mp_producto_id').value    = productoId;
    document.getElementById('mp_prod_display').textContent = codigo + (descripcion ? ' — ' + descripcion : '');

    const btn = document.getElementById('btnGuardarMoverPieza');
    btn.disabled = false;
    btn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg> Enviar Solicitud`;

    setTimeout(() => document.getElementById('mp_title')?.focus(), 120);
}

function cerrarModalMoverPieza() {
    const m = document.getElementById('modalMoverPieza');
    m.classList.add('hidden');
    m.classList.remove('flex');
    document.body.style.overflow = '';
}

// Cerrar al hacer clic en el fondo
document.getElementById('modalMoverPieza').addEventListener('click', function(e) {
    if (e.target === this) cerrarModalMoverPieza();
});

// Drag & drop de imágenes
(function() {
    const dropZone = document.getElementById('mp_dropZone');
    const input    = document.getElementById('mp_images');
    const preview  = document.getElementById('mp_imagePreview');

    dropZone.addEventListener('click', () => input.click());
    dropZone.addEventListener('dragover', e => {
        e.preventDefault();
        dropZone.classList.add('border-teal-400', 'bg-teal-50');
    });
    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('border-teal-400', 'bg-teal-50');
    });
    dropZone.addEventListener('drop', e => {
        e.preventDefault();
        dropZone.classList.remove('border-teal-400', 'bg-teal-50');
        input.files = e.dataTransfer.files;
        renderMpPreview();
    });
    input.addEventListener('change', renderMpPreview);

    function renderMpPreview() {
        preview.innerHTML = '';
        const files = Array.from(input.files).slice(0, 5);
        if (!files.length) { preview.classList.add('hidden'); return; }
        preview.classList.remove('hidden');
        files.forEach(file => {
            const reader = new FileReader();
            reader.onload = e => {
                const div = document.createElement('div');
                div.className = 'relative group rounded-lg overflow-hidden border border-gray-200';
                div.innerHTML = `<img src="${e.target.result}" class="w-full h-16 object-cover">
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01"/></svg>
                    </div>`;
                preview.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    }
})();

// Deshabilitar doble submit
document.getElementById('formMoverPieza').addEventListener('submit', function() {
    const btn = document.getElementById('btnGuardarMoverPieza');
    btn.disabled = true;
    btn.innerHTML = `<svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Enviando...`;
});
</script>

@endsection
