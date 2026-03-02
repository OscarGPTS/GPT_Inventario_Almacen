@extends('layouts.app')

@section('title', 'Entradas')

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
            <h1 class="text-xl font-bold text-gray-800">Entradas</h1>
            <p class="text-xs text-gray-500 mt-0.5">{{ number_format($registros->total()) }} registros encontrados</p>
        </div>
        <button onclick="abrirModalCargaMasiva()" 
            class="flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white text-sm font-semibold rounded-xl shadow transition hover:bg-green-700 active:scale-95">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
            </svg>
            Carga Masiva Excel
        </button>
    </div>

    {{-- Buscador --}}
    <form method="GET" action="{{ route('reportes.entradas') }}" class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-4 py-3 flex gap-3 items-center">
            <div class="flex-1 relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Buscar por c&oacute;digo, descripci&oacute;n, ubicaci&oacute;n, factura..."
                    class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition"
                    autocomplete="off">
            </div>
            <button type="submit"
                class="px-4 py-2 text-white text-sm font-medium rounded-lg transition hover:opacity-90"
                style="background-color:#4A568D;">
                Buscar
            </button>
            @if(request('search'))
            <a href="{{ route('reportes.entradas') }}"
               class="px-4 py-2 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition">
                Limpiar
            </a>
            @endif
        </div>
    </form>

    {{-- Tabla --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto" style="max-height: calc(100vh - 260px); overflow-y: auto;">
            <table class="w-full text-xs border-collapse" style="min-width:1900px;">
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
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">FECHA SALIDA</th>
                        <th class="px-3 py-2.5 text-right text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">P.U</th>
                        <th class="px-3 py-2.5 text-center text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">MXN/USD</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">FACTURA</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide border-r border-indigo-600" style="min-width:180px;">DN/NP/OBSERVACI&Oacute;N</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">FECHA DE VENCIMIENTO</th>
                        <th class="px-3 py-2.5 text-center text-white font-semibold uppercase tracking-wide whitespace-nowrap">HOJAS DE SEGURIDAD</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($registros as $p)
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
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $p->fecha_salida ? $p->fecha_salida->format('d/m/Y') : '—' }}</td>
                        <td class="px-3 py-2 text-gray-800 text-right whitespace-nowrap">{{ $p->precio_unitario !== null ? number_format($p->precio_unitario, 2) : '—' }}</td>
                        <td class="px-3 py-2 text-gray-700 text-center whitespace-nowrap">{{ $p->moneda ?? '—' }}</td>
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
                    <tr>
                        <td colspan="19" class="px-6 py-12 text-center text-gray-400 text-sm">
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
        <div class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-green-600 to-green-500 text-white">
            <div class="flex items-center gap-3">
                <div class="bg-white/20 p-2 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-lg">Carga Masiva - Entradas</h3>
                    <p class="text-xs text-green-100" id="modal_subtitle">Selecciona el archivo Excel para importar</p>
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
                            class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-400 transition"
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
                                class="px-8 py-2.5 bg-gradient-to-r from-green-600 to-green-500 text-white text-sm font-bold rounded-xl transition-all hover:from-green-700 hover:to-green-600 shadow-lg hover:shadow-xl flex items-center gap-2">
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

<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
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
</script>

@endsection
