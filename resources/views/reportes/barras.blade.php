@extends('layouts.app')
@section('title', 'Barras')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        height: 38px;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px;
        padding-left: 12px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    .select2-container {
        width: 100% !important;
    }
</style>
@endpush

@section('content')
@php 
    $acento = '#4A568D'; 
    $claro  = '#eef0f8';
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

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Barras</h1>
            <p class="text-xs text-gray-500 mt-0.5">Categoría: Barras (BR) · {{ number_format($registros->total()) }} registros</p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="abrirModalCargaMasiva()" 
                class="flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white text-sm font-semibold rounded-xl shadow transition hover:bg-green-700 active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                Carga Masiva
            </button>
            <button onclick="abrirModalBorrar()" 
                class="flex items-center gap-2 px-4 py-2.5 bg-amber-600 text-white text-sm font-semibold rounded-xl shadow transition hover:bg-amber-700 active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                </svg>
                Ocultar Todo
            </button>
            <button onclick="abrirModalNuevo()" 
                class="flex items-center gap-2 px-4 py-2.5 text-white text-sm font-semibold rounded-xl shadow transition hover:opacity-90 active:scale-95"
                style="background-color:{{ $acento }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo Producto Barra
            </button>
        </div>
    </div>

    <form method="GET" action="{{ route('reportes.barras') }}" class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-4 py-3 flex gap-3 items-center">
            <div class="flex-1 relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400 z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input 
                    type="text" 
                    id="searchInputBarras" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="Buscar por c&oacute;digo, NP, descripci&oacute;n, dimensiones..."
                    class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 transition"
                    autocomplete="off"
                    onkeyup="buscarDinamicoBarras(event)">
                
                {{-- Dropdown de Sugerencias --}}
                <div id="suggestionBoxBarras" class="hidden absolute top-full left-0 right-0 mt-1 bg-white border border-gray-300 rounded-lg shadow-lg z-50 max-h-80 overflow-y-auto">
                    <div id="loadingSuggestionsBarras" class="hidden px-4 py-3 text-sm text-gray-500 flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-indigo-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Buscando...
                    </div>
                    <div id="suggestionsContentBarras"></div>
                </div>
            </div>
            <button type="submit" class="px-4 py-2 text-white text-sm font-medium rounded-lg transition" style="background-color:{{ $acento }}">Buscar</button>
            @if(request('search'))
            <a href="{{ route('reportes.barras') }}" class="px-3 py-2 bg-gray-100 text-gray-600 text-sm rounded-lg hover:bg-gray-200 transition">✕</a>
            @endif
        </div>
        
        {{-- Tip de búsqueda --}}
        <div class="px-4 pb-3 pt-0">
            <p class="text-xs text-gray-500">
                Escribe al menos 2 caracteres para ver sugerencias en tiempo real
            </p>
        </div>
    </form>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto" style="max-height:calc(100vh - 240px);overflow-y:auto;">
            <table class="w-full text-xs border-collapse" style="min-width:1600px;">
                <thead class="sticky top-0 z-10">
                    <tr style="background-color:{{ $acento }};">
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">CODIGO</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600"># REQUISICI&Oacute;N</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">NP</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">DIMENSIONES</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">TIPO MATERIAL</th>
                        <th class="px-3 py-2.5 text-right text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">PZ</th>
                        <th class="px-3 py-2.5 text-right text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">FIS.</th>
                        <th class="px-3 py-2.5 text-center text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">U.M</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">UBIC.</th>
                        <th class="px-3 py-2.5 text-right text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">DIF</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">FACTURA</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">OC</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide border-r border-indigo-600" style="min-width:200px;">DESCRIPCIÓN INGRESO</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide border-r border-indigo-600" style="min-width:180px;">OBSERVACIONES</th>
                        <th class="px-3 py-2.5 text-center text-white font-semibold uppercase tracking-wide whitespace-nowrap">ACCIONES</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($registros as $p)
                    @php $dif = ($p->cantidad_entrada ?? 0) - ($p->cantidad_fisica ?? 0); @endphp
                    <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }} hover:bg-blue-50 transition-colors">
                        <td class="px-3 py-2 font-mono font-semibold whitespace-nowrap">
                            <a href="{{ route('productos.show', $p->id) }}" class="hover:underline" style="color:{{ $acento }}">{{ $p->codigo }}</a>
                        </td>
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $p->numero_requisicion ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $p->numero_parte ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $p->dimensiones ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $p->componente->codigo ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-800 text-right whitespace-nowrap">{{ $p->cantidad_entrada !== null ? number_format($p->cantidad_entrada, 2) : '—' }}</td>
                        <td class="px-3 py-2 text-right whitespace-nowrap font-semibold {{ $p->cantidad_fisica < 10 ? 'text-red-600' : 'text-gray-800' }}">
                            {{ $p->cantidad_fisica !== null ? number_format($p->cantidad_fisica, 2) : '—' }}
                        </td>
                        <td class="px-3 py-2 text-gray-700 text-center whitespace-nowrap">{{ $p->unidadMedida->codigo ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $p->ubicacion->codigo ?? '—' }}</td>
                        <td class="px-3 py-2 text-right whitespace-nowrap font-semibold {{ $dif > 0 ? 'text-orange-600' : 'text-gray-700' }}">
                            {{ number_format($dif, 2) }}
                        </td>
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $p->factura ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $p->orden_compra ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-800">{{ $p->descripcion }}</td>
                        <td class="px-3 py-2 text-gray-600">{{ $p->observaciones ?? '—' }}</td>
                        <td class="px-3 py-2 text-center">
                            <button onclick="abrirModalEditar({{ $p->id }})" 
                                class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-lg border border-indigo-300 hover:bg-indigo-50 transition"
                                style="color:{{ $acento }}"
                                title="Editar producto">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Editar
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="15" class="px-6 py-12 text-center text-gray-400">No hay registros de barras</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @include('reportes._paginacion', ['items' => $registros, 'acento' => $acento])
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════════════
     MODAL: Nuevo Producto Barra
═══════════════════════════════════════════════════════════════════════════════ --}}
<div id="modalNuevo" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background:rgba(0,0,0,0.45);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 rounded-t-2xl text-white" style="background-color:{{ $acento }};">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="font-semibold">Nuevo Producto - Categoría Barras</span>
            </div>
            <button onclick="cerrarModalNuevo()" class="text-white opacity-70 hover:opacity-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('reportes.barras.guardar_producto') }}" class="px-6 py-5 space-y-4">
            @csrf
            <input type="hidden" name="categoria_id" value="{{ \App\Models\Categoria::where('codigo', 'BR')->first()->id ?? '' }}">
            
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Código <span class="text-red-500">*</span></label>
                <input type="text" name="codigo" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1"># Requisición</label>
                    <input type="text" name="numero_requisicion"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">NP (Número de Parte)</label>
                    <input type="text" name="numero_parte"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Dimensiones</label>
                    <input type="text" name="dimensiones"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Tipo Material (Componente) <span class="text-xs text-gray-500">(puedes escribir para crear nuevo)</span></label>
                <select name="componente_id" id="componente_select" class="select2-tags w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    <option value="">-- Ninguno o escribe para crear --</option>
                    @foreach(\App\Models\Componente::orderBy('codigo')->get() as $comp)
                    <option value="{{ $comp->id }}">{{ $comp->codigo }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">PZ (Cant. Entrada)</label>
                    <input type="number" step="0.01" name="cantidad_entrada"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">FIS. (Cant. Física)</label>
                    <input type="number" step="0.01" name="cantidad_fisica"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Unidad Medida (U.M) <span class="text-xs text-gray-500">(puedes escribir para crear nueva)</span></label>
                    <select name="unidad_medida_id" id="unidad_medida_select" class="select2-tags w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">-- Ninguna o escribe para crear --</option>
                        @foreach(\App\Models\UnidadMedida::orderBy('codigo')->get() as $um)
                        <option value="{{ $um->id }}">{{ $um->codigo }} - {{ $um->descripcion }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Ubicación (UBIC.) <span class="text-xs text-gray-500">(puedes escribir para crear nueva)</span></label>
                    <select name="ubicacion_id" id="ubicacion_select" class="select2-tags w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">-- Ninguna o escribe para crear --</option>
                        @foreach(\App\Models\Ubicacion::orderBy('codigo')->get() as $ub)
                        <option value="{{ $ub->id }}">{{ $ub->codigo }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Factura</label>
                    <input type="text" name="factura"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">OC (Orden de Compra)</label>
                    <input type="text" name="orden_compra"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Descripción Ingreso <span class="text-red-500">*</span></label>
                <textarea name="descripcion" rows="2" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 resize-none"></textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Observaciones</label>
                <textarea name="observaciones" rows="2"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 resize-none"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <button type="button" onclick="cerrarModalNuevo()"
                    class="px-5 py-2 bg-white border border-gray-300 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-50 transition">
                    Cancelar
                </button>
                <button type="submit"
                    class="px-6 py-2 text-white text-sm font-semibold rounded-xl transition hover:opacity-90"
                    style="background-color:{{ $acento }}">
                    Guardar Producto
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════════════
     MODAL: Editar Producto
═══════════════════════════════════════════════════════════════════════════════ --}}
<div id="modalEditar" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background:rgba(0,0,0,0.45);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 rounded-t-2xl text-white" style="background-color:{{ $acento }};">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <span class="font-semibold">Editar Producto</span>
            </div>
            <button onclick="cerrarModalEditar()" class="text-white opacity-70 hover:opacity-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form method="POST" id="formEditar" class="px-6 py-5 space-y-4">
            @csrf
            @method('PATCH')
            
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Código <span class="text-red-500">*</span></label>
                <input type="text" name="codigo" id="edit_codigo" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1"># Requisición</label>
                    <input type="text" name="numero_requisicion" id="edit_numero_requisicion"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">NP (Número de Parte)</label>
                    <input type="text" name="numero_parte" id="edit_numero_parte"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Dimensiones</label>
                    <input type="text" name="dimensiones" id="edit_dimensiones"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Tipo Material (Componente) <span class="text-xs text-gray-500">(puedes escribir para crear nuevo)</span></label>
                <select name="componente_id" id="edit_componente_id" class="select2-tags-edit w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    <option value="">-- Ninguno o escribe para crear --</option>
                    @foreach(\App\Models\Componente::orderBy('codigo')->get() as $comp)
                    <option value="{{ $comp->id }}">{{ $comp->codigo }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">PZ (Cant. Entrada)</label>
                    <input type="number" step="0.01" name="cantidad_entrada" id="edit_cantidad_entrada"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">FIS. (Cant. Física)</label>
                    <input type="number" step="0.01" name="cantidad_fisica" id="edit_cantidad_fisica"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Unidad Medida (U.M) <span class="text-xs text-gray-500">(puedes escribir para crear nueva)</span></label>
                    <select name="unidad_medida_id" id="edit_unidad_medida_id" class="select2-tags-edit w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">-- Ninguna o escribe para crear --</option>
                        @foreach(\App\Models\UnidadMedida::orderBy('codigo')->get() as $um)
                        <option value="{{ $um->id }}">{{ $um->codigo }} - {{ $um->descripcion }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Ubicación (UBIC.) <span class="text-xs text-gray-500">(puedes escribir para crear nueva)</span></label>
                    <select name="ubicacion_id" id="edit_ubicacion_id" class="select2-tags-edit w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">-- Ninguna o escribe para crear --</option>
                        @foreach(\App\Models\Ubicacion::orderBy('codigo')->get() as $ub)
                        <option value="{{ $ub->id }}">{{ $ub->codigo }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Factura</label>
                    <input type="text" name="factura" id="edit_factura"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">OC (Orden de Compra)</label>
                    <input type="text" name="orden_compra" id="edit_orden_compra"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Categoría <span class="text-red-500">*</span></label>
                <select name="categoria_id" id="edit_categoria_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    <option value="">-- Ninguna --</option>
                    @foreach(\App\Models\Categoria::orderBy('codigo')->get() as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->codigo }} - {{ $cat->descripcion }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Descripción Ingreso <span class="text-red-500">*</span></label>
                <textarea name="descripcion" id="edit_descripcion" rows="2" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 resize-none"></textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Observaciones</label>
                <textarea name="observaciones" id="edit_observaciones" rows="2"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 resize-none"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <button type="button" onclick="cerrarModalEditar()"
                    class="px-5 py-2 bg-white border border-gray-300 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-50 transition">
                    Cancelar
                </button>
                <button type="submit"
                    class="px-6 py-2 text-white text-sm font-semibold rounded-xl transition hover:opacity-90"
                    style="background-color:{{ $acento }}">
                    Actualizar
                </button>
            </div>
        </form>
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
                    <h3 class="font-bold text-lg">Carga Masiva - Barras</h3>
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
            <form id="formCargaMasiva" method="POST" action="{{ route('reportes.barras.importar') }}" enctype="multipart/form-data" class="h-full" onsubmit="capturarAccionSinCodigo(event)">
                @csrf
                <input type="hidden" name="action_sin_codigo" id="action_sin_codigo_hidden" value="descartar">
                
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
                                        <span>Selecciona el número de página (tab) del Excel donde están tus datos</span>
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
                        <input type="number" name="sheet_number" id="sheet_number" value="4" min="1" max="50" required
                            class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-400 transition"
                            placeholder="Ejemplo: 1, 2, 3, 4...">
                        <p class="text-xs text-gray-500 mt-2 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Indica el número de la pestaña donde se encuentran los datos (la primera pestaña es 1)
                        </p>
                    </div>

                    {{-- Info sobre importación --}}
                    <div class="bg-gradient-to-r from-blue-50 to-cyan-50 border-l-4 border-blue-500 rounded-r-lg p-4">
                        <div class="flex gap-3">
                            <svg class="w-5 h-5 shrink-0 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <p class="text-sm font-bold text-blue-900 mb-1">Modo de Importación</p>
                                <p class="text-xs text-blue-800">
                                    🔄 Los productos con códigos existentes serán <strong>actualizados</strong><br>
                                    ✨ Los productos con códigos nuevos serán <strong>creados</strong>
                                </p>
                            </div>
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
                    <div class="grid grid-cols-3 gap-4">
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
                                    <p class="text-xs text-green-600 font-medium">Con Código</p>
                                    <p class="text-2xl font-bold text-green-900" id="preview_validos">0</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gradient-to-br from-amber-50 to-amber-100 border border-amber-200 rounded-xl p-4">
                            <div class="flex items-center gap-3">
                                <div class="bg-amber-500 p-2 rounded-lg">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-amber-600 font-medium">Sin Código</p>
                                    <p class="text-2xl font-bold text-amber-900" id="preview_invalidos">0</p>
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

                    {{-- Advertencias --}}
                    <div id="preview_warnings" class="hidden"></div>

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

{{-- ═══════════════════════════════════════════════════════════════════════════════
     MODAL: Confirmar Borrado de Todos los Registros
═══════════════════════════════════════════════════════════════════════════════ --}}
<div id="modalBorrar" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background:rgba(0,0,0,0.45);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 rounded-t-2xl text-white bg-amber-600">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                </svg>
                <span class="font-semibold">Ocultar Productos</span>
            </div>
            <button onclick="cerrarModalBorrar()" class="text-white opacity-70 hover:opacity-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('reportes.barras.borrar') }}" class="px-6 py-5 space-y-4">
            @csrf
            @method('DELETE')
            
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                <div class="flex gap-3">
                    <svg class="w-6 h-6 shrink-0 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="space-y-2">
                        <p class="text-sm font-semibold text-amber-800">¿Deseas ocultar todos los productos?</p>
                        <p class="text-xs text-amber-700">
                            Se ocultarán <strong>{{ number_format($registros->total()) }} registros</strong> usando <em>Soft Delete</em>.<br>
                            Los productos NO se eliminarán permanentemente y podrás recuperarlos desde la base de datos si es necesario.
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                <div class="flex items-start gap-2">
                    <svg class="w-4 h-4 shrink-0 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-xs text-blue-800">
                        <strong>Soft Delete:</strong> Los registros se marcarán como eliminados pero permanecerán en la base de datos con un campo <code class="bg-blue-100 px-1 rounded">deleted_at</code>.
                    </p>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <button type="button" onclick="cerrarModalBorrar()"
                    class="px-5 py-2 bg-white border border-gray-300 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-50 transition">
                    Cancelar
                </button>
                <button type="submit"
                    class="px-6 py-2 bg-amber-600 text-white text-sm font-semibold rounded-xl transition hover:bg-amber-700">
                    <svg class="w-4 h-4 inline-block mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                    Ocultar Todo (Soft Delete)
                </button>
            </div>
        </form>
    </div>
</div>

{{-- SheetJS Library para leer Excel en el cliente --}}
<script src="https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js"></script>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

/* ─── Modal Nuevo ──────────────────────── */
function abrirModalNuevo() {
    const m = document.getElementById('modalNuevo');
    m.classList.remove('hidden');
    m.classList.add('flex');
    document.body.style.overflow = 'hidden';
}
function cerrarModalNuevo() {
    document.getElementById('modalNuevo').classList.replace('flex','hidden');
    document.body.style.overflow = '';
}
document.getElementById('modalNuevo').addEventListener('click', e => { 
    if (e.target === document.getElementById('modalNuevo')) cerrarModalNuevo(); 
});

/* ─── Modal Editar ──────────────────────── */
async function abrirModalEditar(id) {
    const m = document.getElementById('modalEditar');
    const form = document.getElementById('formEditar');
    
    try {
        const res = await fetch(`/productos/${id}`);
        const html = await res.text();
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        
        // Extraer datos del producto de la página de detalle (simplificado - en producción usar API)
        const codigo = doc.querySelector('meta[name="producto-codigo"]')?.content || '';
        
        // Mejor manera: hacer un fetch a una ruta JSON
        const jsonRes = await fetch(`/api/v1/productos/${id}`);
        const producto = await jsonRes.json();
        
        form.action = `/productos/${id}`;
        document.getElementById('edit_codigo').value = producto.codigo || '';
        document.getElementById('edit_numero_requisicion').value = producto.numero_requisicion || '';
        document.getElementById('edit_numero_parte').value = producto.numero_parte || '';
        document.getElementById('edit_dimensiones').value = producto.dimensiones || '';
        document.getElementById('edit_componente_id').value = producto.componente_id || '';
        document.getElementById('edit_cantidad_entrada').value = producto.cantidad_entrada || '';
        document.getElementById('edit_cantidad_fisica').value = producto.cantidad_fisica || '';
        document.getElementById('edit_unidad_medida_id').value = producto.unidad_medida_id || '';
        document.getElementById('edit_ubicacion_id').value = producto.ubicacion_id || '';
        document.getElementById('edit_factura').value = producto.factura || '';
        document.getElementById('edit_orden_compra').value = producto.orden_compra || '';
        document.getElementById('edit_categoria_id').value = producto.categoria_id || '';
        document.getElementById('edit_descripcion').value = producto.descripcion || '';
        document.getElementById('edit_observaciones').value = producto.observaciones || '';
        
        m.classList.remove('hidden');
        m.classList.add('flex');
        document.body.style.overflow = 'hidden';
    } catch(err) {
        alert('Error al cargar el producto');
        console.error(err);
    }
}

function cerrarModalEditar() {
    document.getElementById('modalEditar').classList.replace('flex','hidden');
    document.body.style.overflow = '';
}
document.getElementById('modalEditar').addEventListener('click', e => { 
    if (e.target === document.getElementById('modalEditar')) cerrarModalEditar(); 
});
document.addEventListener('keydown', e => { 
    if (e.key === 'Escape') { 
        cerrarModalNuevo(); 
        cerrarModalEditar();
        cerrarModalCargaMasiva();
        cerrarModalBorrar(); 
    } 
});

/* ═══════════════════════════════════════════════════════════════════
   MODAL CARGA MASIVA CON PREVIEW
═══════════════════════════════════════════════════════════════════ */

// Variables globales
let currentWorkbook = null;
let currentFile = null;

function abrirModalCargaMasiva() {
    const m = document.getElementById('modalCargaMasiva');
    m.classList.remove('hidden');
    m.classList.add('flex');
    document.body.style.overflow = 'hidden';
    
    // Resetear modal
    resetModal();
}

function cerrarModalCargaMasiva() {
    document.getElementById('modalCargaMasiva').classList.replace('flex','hidden');
    document.body.style.overflow = '';
    resetModal();
}

function resetModal() {
    // Resetear a paso 1
    document.getElementById('paso1').classList.remove('hidden');
    document.getElementById('paso2').classList.add('hidden');
    
    // Limpiar archivo
    document.getElementById('archivo_excel').value = '';
    document.getElementById('file_info').classList.add('hidden');
    document.getElementById('btn_preview').disabled = true;
    
    // Limpiar variables
    currentWorkbook = null;
    currentFile = null;
    
    // Resetear subtítulo
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
    
    // Mostrar info del archivo
    document.getElementById('file_name').textContent = file.name;
    document.getElementById('file_size').textContent = formatFileSize(file.size);
    document.getElementById('file_info').classList.remove('hidden');
    
    // Habilitar botón de preview
    document.getElementById('btn_preview').disabled = false;
    
    // Leer el archivo para obtener número de hojas
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

function capturarAccionSinCodigo(event) {
    // Capturar el valor del radio button seleccionado si existe
    const radioSelected = document.querySelector('input[name="action_sin_codigo"]:checked');
    if (radioSelected) {
        document.getElementById('action_sin_codigo_hidden').value = radioSelected.value;
    }
    
    return true; // Permitir que el formulario se envíe
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

function actualizarPreviewTabla(action, rows, validos, data) {
    const { headers, codigoIndex } = data;
    
    // Generar header de la tabla dinámicamente
    const theadRow = document.getElementById('preview_header');
    theadRow.innerHTML = '<th class="px-3 py-2 text-left font-bold text-gray-700 border-r border-gray-300 bg-indigo-100 sticky left-0 z-10">#</th>';
    
    headers.forEach((header, index) => {
        const isCodigoCol = index === codigoIndex;
        const bgClass = isCodigoCol ? 'bg-green-100' : 'bg-gray-200';
        const thElement = `
            <th class="px-3 py-2 text-left font-bold text-gray-700 border-r border-gray-300 ${bgClass} whitespace-nowrap">
                ${header || `Col ${index + 1}`}
            </th>
        `;
        theadRow.innerHTML += thElement;
    });
    
    // Generar filas de datos
    const tbody = document.getElementById('preview_body');
    tbody.innerHTML = '';
    
    let tempCounter = 1;
    rows.forEach((row, index) => {
        const codigo = row[codigoIndex] ? row[codigoIndex].toString().trim() : '';
        
        if (index < 10) {
            const tr = document.createElement('tr');
            
            let rowClass = 'hover:bg-green-50';
            let codigoDisplay = codigo;
            
            if (!codigo) {
                if (action === 'generar') {
                    // Mostrar código temporal que se generará
                    const tempCode = 'TEM' + String(tempCounter).padStart(4, '0');
                    codigoDisplay = `<span class="text-green-600 font-semibold">${tempCode}</span>
                                    <span class="block text-xs text-green-500 mt-0.5">✨ Generado</span>`;
                    rowClass = 'bg-green-50 hover:bg-green-100';
                    tempCounter++;
                } else {
                    // Mostrar que será descartado
                    codigoDisplay = `<span class="text-red-500 italic line-through">Sin código</span>
                                    <span class="block text-xs text-red-400 mt-0.5">🚫 Descartado</span>`;
                    rowClass = 'bg-red-50 hover:bg-red-100 opacity-60';
                }
            } else {
                codigoDisplay = `<span class="font-bold text-green-700">${codigo}</span>`;
            }
            
            tr.className = rowClass;
            
            // Generar celdas dinámicamente
            let cellsHTML = `<td class="px-3 py-2 border-r border-gray-200 text-gray-500 font-mono bg-indigo-50 sticky left-0 z-10">${index + 1}</td>`;
            
            headers.forEach((header, colIndex) => {
                let cellValue = row[colIndex] !== undefined && row[colIndex] !== null ? row[colIndex].toString() : '-';
                let cellClass = 'px-3 py-2 border-r border-gray-200 text-gray-700';
                
                // Formatear columna CODIGO especialmente
                if (colIndex === codigoIndex) {
                    cellValue = codigoDisplay;
                    cellClass = 'px-3 py-2 border-r border-gray-200';
                }
                // Resaltar columnas numéricas
                else if (header && (header.includes('PZ') || header.includes('FIS') || header.includes('CANTIDAD'))) {
                    cellClass = 'px-3 py-2 border-r border-gray-200 font-semibold text-blue-700 text-right';
                }
                // Hacer más pequeño el texto de descripciones largas
                else if (header && (header.includes('DESCRIPCION') || header.includes('OBSERV'))) {
                    cellClass = 'px-3 py-2 border-r border-gray-200 text-gray-600 text-xs max-w-xs truncate';
                    cellValue = cellValue.length > 50 ? cellValue.substring(0, 50) + '...' : cellValue;
                }
                
                cellsHTML += `<td class="${cellClass}" title="${cellValue}">${cellValue}</td>`;
            });
            
            tr.innerHTML = cellsHTML;
            tbody.appendChild(tr);
        } else if (!codigo && action === 'generar') {
            tempCounter++;
        }
    });
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
    
    // Verificar que la hoja exista
    const sheetIndex = sheetNumber - 1;
    if (sheetIndex >= currentWorkbook.SheetNames.length) {
        alert(`El archivo solo tiene ${currentWorkbook.SheetNames.length} hoja(s). Por favor ingresa un número entre 1 y ${currentWorkbook.SheetNames.length}`);
        return;
    }
    
    // Obtener la hoja
    const sheetName = currentWorkbook.SheetNames[sheetIndex];
    const worksheet = currentWorkbook.Sheets[sheetName];
    
    // Convertir a array
    const data = XLSX.utils.sheet_to_json(worksheet, {header: 1, defval: ''});
    
    if (data.length === 0) {
        alert('❌ La hoja seleccionada está vacía');
        return;
    }
    
    // DETECCIÓN AUTOMÁTICA DEL HEADER
    let headerRowIndex = -1;
    let headers = [];
    
    // Buscar en las primeras 10 filas
    for (let i = 0; i < Math.min(10, data.length); i++) {
        const row = data[i];
        
        // Saltar filas vacías
        if (!row || row.every(cell => !cell)) {
            continue;
        }
        
        // Convertir a mayúsculas para comparación
        const potentialHeaders = row.map(h => h ? h.toString().toUpperCase().trim() : '');
        
        // Verificar si contiene columnas clave
        const tieneCodigoCol = potentialHeaders.includes('CODIGO');
        const tieneDescripcionCol = potentialHeaders.some(h => 
            h.includes('DESCRIPCIÓN') || h.includes('DESCRIPCION')
        );
        const tieneNPCol = potentialHeaders.includes('NP');
        const tienePZCol = potentialHeaders.includes('PZ');
        
        // Si tiene CODIGO y al menos otra columna clave, es el header
        if (tieneCodigoCol && (tieneDescripcionCol || tieneNPCol || tienePZCol)) {
            headerRowIndex = i;
            headers = potentialHeaders;
            console.log(`✅ Header detectado automáticamente en la fila ${i + 1}`);
            break;
        }
    }
    
    // Si no se encontró el header
    if (headerRowIndex === -1) {
        const preview = data.slice(0, 3).map((row, i) => 
            `Fila ${i + 1}: ${row.slice(0, 5).join(', ')}`
        ).join('\n');
        
        alert(`❌ No se pudo detectar el encabezado automáticamente.\n\nAsegúrate de que hay una fila con columnas como:\n- CODIGO\n- DESCRIPCION\n- NP\n- PZ\n\nPrimeras filas del archivo:\n${preview}`);
        return;
    }
    
    // Extraer solo las filas de datos (después del header)
    const rows = data.slice(headerRowIndex + 1);
    
    // Buscar índice de columnas
    const codigoIndex = headers.findIndex(h => h === 'CODIGO');
    
    if (codigoIndex === -1) {
        alert('❌ Error interno: No se encontró la columna CODIGO después de detectar el header');
        return;
    }
    
    // Buscar otros índices
    const requisicionIndex = headers.findIndex(h => 
        h && (h.includes('REQUISICIÓN') || h.includes('REQUISICION'))
    );
    const npIndex = headers.findIndex(h => h === 'NP');
    const dimensionesIndex = headers.findIndex(h => h.includes('DIMENSIONES'));
    const pzIndex = headers.findIndex(h => h === 'PZ');
    const descripcionIndex = headers.findIndex(h => 
        h && (h.includes('DESCRIPCIÓN') || h.includes('DESCRIPCION'))
    );
    
    console.log(`📊 Procesando ${rows.length} filas de datos (header en fila ${headerRowIndex + 1})`);
    
    // Contar registros válidos e inválidos
    let validos = 0;
    let invalidos = 0;
    
    rows.forEach((row) => {
        const codigo = row[codigoIndex] ? row[codigoIndex].toString().trim() : '';
        if (codigo) {
            validos++;
        } else if (row.some(cell => cell !== '')) {
            invalidos++;
        }
    });
    
    // Renderizar tabla inicial con opción "descartar" por defecto
    actualizarPreviewTabla('descartar', rows, validos, {
        headers: headers,
        codigoIndex: codigoIndex
    });
    
    // Actualizar estadísticas
    document.getElementById('preview_total').textContent = rows.length;
    document.getElementById('preview_validos').textContent = validos;
    document.getElementById('preview_invalidos').textContent = invalidos;
    
    // Actualizar contador de columnas
    document.getElementById('columns_count').textContent = `${headers.length} columnas`;
    
    // Actualizar información del header
    document.getElementById('header_row_number').textContent = `Fila ${headerRowIndex + 1}`;
    
    // Actualizar subtítulo con información del header
    document.getElementById('modal_subtitle').textContent = `Preview: ${validos} registros listos para importar`;
    
    // Mostrar advertencias si hay registros sin código
    const warningsDiv = document.getElementById('preview_warnings');
    if (invalidos > 0) {
        warningsDiv.innerHTML = `
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 border-2 border-amber-400 rounded-xl p-5">
                <div class="flex gap-4">
                    <div class="bg-amber-100 p-3 rounded-lg h-fit">
                        <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-bold text-amber-900 text-base mb-2">⚠️ Se encontraron ${invalidos} fila(s) sin código</p>
                        <p class="text-sm text-amber-800 mb-4">
                            ¿Qué deseas hacer con los registros que no tienen código?
                        </p>
                        
                        <div class="space-y-3">
                            <label class="flex items-start gap-3 p-3 bg-white border-2 border-gray-300 rounded-lg cursor-pointer hover:border-amber-500 transition-all group">
                                <input type="radio" name="action_sin_codigo" value="descartar" checked 
                                    class="mt-0.5 w-4 h-4 text-amber-600 focus:ring-amber-500">
                                <div class="flex-1">
                                    <p class="font-bold text-gray-800 text-sm group-hover:text-amber-700">🚫 Descartar registros sin código</p>
                                    <p class="text-xs text-gray-600 mt-0.5">Los registros sin código serán ignorados y no se importarán</p>
                                </div>
                            </label>
                            
                            <label class="flex items-start gap-3 p-3 bg-white border-2 border-gray-300 rounded-lg cursor-pointer hover:border-green-500 transition-all group">
                                <input type="radio" name="action_sin_codigo" value="generar" 
                                    class="mt-0.5 w-4 h-4 text-green-600 focus:ring-green-500">
                                <div class="flex-1">
                                    <p class="font-bold text-gray-800 text-sm group-hover:text-green-700">✨ Generar códigos temporales automáticamente</p>
                                    <p class="text-xs text-gray-600 mt-0.5">Se asignarán códigos temporales únicos (TEM0001, TEM0002, etc.) a los ${invalidos} registros</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        `;
        warningsDiv.classList.remove('hidden');
        
        // Agregar event listeners para actualizar preview al cambiar opción
        setTimeout(() => {
            const radios = document.querySelectorAll('input[name="action_sin_codigo"]');
            radios.forEach(radio => {
                radio.addEventListener('change', function() {
                    actualizarPreviewTabla(this.value, rows, validos, {
                        headers: headers,
                        codigoIndex: codigoIndex
                    });
                });
            });
        }, 100);
    } else {
        warningsDiv.classList.add('hidden');
    }
    
    // Cambiar a paso 2
    document.getElementById('paso1').classList.add('hidden');
    document.getElementById('paso2').classList.remove('hidden');
    document.getElementById('modal_subtitle').textContent = `Preview: ${validos} registros listos para importar`;
}

document.getElementById('modalCargaMasiva').addEventListener('click', e => { 
    if (e.target === document.getElementById('modalCargaMasiva')) cerrarModalCargaMasiva(); 
});

/* ─── Modal Borrar ──────────────────────── */
function abrirModalBorrar() {
    const m = document.getElementById('modalBorrar');
    m.classList.remove('hidden');
    m.classList.add('flex');
    document.body.style.overflow = 'hidden';
}
function cerrarModalBorrar() {
    document.getElementById('modalBorrar').classList.replace('flex','hidden');
    document.body.style.overflow = '';
    document.getElementById('confirmar_borrado').checked = false;
}
document.getElementById('modalBorrar').addEventListener('click', e => { 
    if (e.target === document.getElementById('modalBorrar')) cerrarModalBorrar(); 
});

// ─────────────────────────────────────────────────────────────────
// Inicializar Select2 con opción de crear nuevos valores (tags)
// ─────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    // Cargar jQuery y Select2
    if (!window.jQuery) {
        const script = document.createElement('script');
        script.src = 'https://code.jquery.com/jquery-3.6.0.min.js';
        script.onload = initSelect2;
        document.head.appendChild(script);
    } else {
        initSelect2();
    }
});

function initSelect2() {
    // Cargar Select2 si no está cargado
    if (!window.jQuery.fn.select2) {
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js';
        script.onload = setupSelects;
        document.head.appendChild(script);
    } else {
        setupSelects();
    }
}

function setupSelects() {
    // Modal Nuevo - Configurar Select2 con tags
    jQuery('.select2-tags').select2({
        tags: true,
        placeholder: 'Selecciona o escribe para crear nuevo',
        allowClear: true,
        createTag: function (params) {
            const term = jQuery.trim(params.term);
            if (term === '') {
                return null;
            }
            return {
                id: 'new:' + term,
                text: '✨ Crear: ' + term,
                newTag: true
            };
        },
        templateResult: function(data) {
            if (data.newTag) {
                return jQuery('<span style="color: #10b981; font-weight: 600;">' + data.text + '</span>');
            }
            return data.text;
        }
    });

    // Modal Editar - Configurar Select2 con tags
    jQuery('.select2-tags-edit').select2({
        tags: true,
        placeholder: 'Selecciona o escribe para crear nuevo',
        allowClear: true,
        dropdownParent: jQuery('#modalEditar'),
        createTag: function (params) {
            const term = jQuery.trim(params.term);
            if (term === '') {
                return null;
            }
            return {
                id: 'new:' + term,
                text: '✨ Crear: ' + term,
                newTag: true
            };
        },
        templateResult: function(data) {
            if (data.newTag) {
                return jQuery('<span style="color: #10b981; font-weight: 600;">' + data.text + '</span>');
            }
            return data.text;
        }
    });

    console.log('✅ Select2 inicializado con soporte para crear nuevos valores');
}

/* ═══════════════════════════════════════════════════════════════════
   DRAG & DROP SUPPORT FOR FILE UPLOAD
═══════════════════════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', function() {
    const dropZone = document.querySelector('label[for="archivo_excel"]')?.parentElement;
    
    if (!dropZone) return;
    
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => {
            dropZone.classList.add('border-green-500', 'bg-green-50');
        }, false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => {
            dropZone.classList.remove('border-green-500', 'bg-green-50');
        }, false);
    });
    
    dropZone.addEventListener('drop', function(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        
        if (files.length > 0) {
            const fileInput = document.getElementById('archivo_excel');
            fileInput.files = files;
            
            // Trigger change event
            const event = new Event('change', { bubbles: true });
            fileInput.dispatchEvent(event);
        }
    }, false);
});

// ==================== BÚSQUEDA DINÁMICA CON API (BARRAS) ====================
let searchTimeoutBarras = null;
let currentSearchQueryBarras = '';

function buscarDinamicoBarras(event) {
    const input = event.target;
    const query = input.value.trim();
    
    if (query.length < 2) {
        ocultarSugerenciasBarras();
        return;
    }
    
    if (event.key === 'Enter') {
        ocultarSugerenciasBarras();
        input.form.submit();
        return;
    }
    
    if (event.key === 'Escape') {
        ocultarSugerenciasBarras();
        return;
    }
    
    if (query === currentSearchQueryBarras) {
        return;
    }
    
    currentSearchQueryBarras = query;
    
    if (searchTimeoutBarras) {
        clearTimeout(searchTimeoutBarras);
    }
    
    searchTimeoutBarras = setTimeout(() => {
        realizarBusquedaAPIBarras(query);
    }, 300);
}

async function realizarBusquedaAPIBarras(query) {
    const suggestionBox = document.getElementById('suggestionBoxBarras');
    const loadingDiv = document.getElementById('loadingSuggestionsBarras');
    const contentDiv = document.getElementById('suggestionsContentBarras');
    
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
                    No se encontraron resultados para "<strong>${escapeHtmlBarras(query)}</strong>"
                </div>
            `;
        } else {
            renderizarSugerenciasBarras(data.data, query, data.total);
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

function renderizarSugerenciasBarras(productos, query, total) {
    const contentDiv = document.getElementById('suggestionsContentBarras');
    
    let html = `
        <div class="sticky top-0 bg-indigo-50 px-4 py-2 border-b border-gray-200 flex justify-between items-center">
            <span class="text-xs font-semibold text-indigo-800">Resultados de búsqueda</span>
            <span class="text-xs font-bold text-indigo-600">${total} encontrados</span>
        </div>
    `;
    
    productos.forEach((producto, index) => {
        const codigo = highlightTextBarras(producto.codigo, query);
        const descripcion = highlightTextBarras(producto.descripcion || 'Sin descripción', query);
        const ubicacion = producto.ubicacion ? highlightTextBarras(producto.ubicacion, query) : '<span class="text-gray-400">S/U</span>';
        const um = producto.um || '-';
        const fisico = producto.fisico || 0;
        const pu = parseFloat(producto.pu || 0).toFixed(2);
        
        html += `
            <div class="suggestion-item-barras px-4 py-3 hover:bg-indigo-50 cursor-pointer border-b border-gray-100 transition"
                 onclick="seleccionarSugerenciaBarras('${escapeHtmlBarras(producto.codigo)}')">
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

function seleccionarSugerenciaBarras(codigo) {
    const input = document.getElementById('searchInputBarras');
    input.value = codigo;
    input.form.submit();
}

function ocultarSugerenciasBarras() {
    document.getElementById('suggestionBoxBarras').classList.add('hidden');
}

function highlightTextBarras(text, query) {
    if (!text) return '';
    const regex = new RegExp(`(${escapeRegExpBarras(query)})`, 'gi');
    return text.replace(regex, '<mark class="bg-yellow-200 px-0.5 font-semibold">$1</mark>');
}

function escapeHtmlBarras(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function escapeRegExpBarras(string) {
    return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

// Cerrar sugerencias al hacer clic fuera
document.addEventListener('click', function(event) {
    const suggestionBox = document.getElementById('suggestionBoxBarras');
    const searchInput = document.getElementById('searchInputBarras');
    
    if (suggestionBox && !suggestionBox.contains(event.target) && event.target !== searchInput) {
        ocultarSugerenciasBarras();
    }
});

// Navegación con teclado en sugerencias
const searchInputBarras = document.getElementById('searchInputBarras');
if (searchInputBarras) {
    searchInputBarras.addEventListener('keydown', function(event) {
        const suggestionBox = document.getElementById('suggestionBoxBarras');
        
        if (suggestionBox.classList.contains('hidden')) return;
        
        const items = suggestionBox.querySelectorAll('.suggestion-item-barras');
        if (items.length === 0) return;
        
        let currentIndex = -1;
        items.forEach((item, index) => {
            if (item.classList.contains('bg-indigo-100')) {
                currentIndex = index;
            }
        });
        
        if (event.key === 'ArrowDown') {
            event.preventDefault();
            if (currentIndex < items.length - 1) {
                if (currentIndex >= 0) items[currentIndex].classList.remove('bg-indigo-100');
                items[currentIndex + 1].classList.add('bg-indigo-100');
                items[currentIndex + 1].scrollIntoView({ block: 'nearest' });
            }
        }
        
        if (event.key === 'ArrowUp') {
            event.preventDefault();
            if (currentIndex > 0) {
                items[currentIndex].classList.remove('bg-indigo-100');
                items[currentIndex - 1].classList.add('bg-indigo-100');
                items[currentIndex - 1].scrollIntoView({ block: 'nearest' });
            }
        }
        
        if (event.key === 'Enter' && currentIndex >= 0) {
            event.preventDefault();
            items[currentIndex].click();
        }
    });
}
</script>
@endsection