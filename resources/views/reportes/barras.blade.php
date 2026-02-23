@extends('layouts.app')
@section('title', 'Barras')
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
        <button onclick="abrirModalNuevo()" 
            class="flex items-center gap-2 px-4 py-2.5 text-white text-sm font-semibold rounded-xl shadow transition hover:opacity-90 active:scale-95"
            style="background-color:{{ $acento }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Producto Barra
        </button>
    </div>

    <form method="GET" action="{{ route('reportes.barras') }}" class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-4 py-3 flex gap-3 items-center">
            <div class="flex-1 relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Buscar por c&oacute;digo, descripci&oacute;n, NP, factura..."
                    class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 transition"
                    autocomplete="off">
            </div>
            <button type="submit" class="px-4 py-2 text-white text-sm font-medium rounded-lg transition" style="background-color:{{ $acento }}">Buscar</button>
            @if(request('search'))
            <a href="{{ route('reportes.barras') }}" class="px-3 py-2 bg-gray-100 text-gray-600 text-sm rounded-lg hover:bg-gray-200 transition">✕</a>
            @endif
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
                        <td class="px-3 py-2 text-gray-400 whitespace-nowrap">—</td>
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $p->observaciones ? \Str::limit($p->observaciones, 20) : '—' }}</td>
                        <td class="px-3 py-2 text-gray-400 whitespace-nowrap">—</td>
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
                        <td class="px-3 py-2 text-gray-400 whitespace-nowrap">—</td>
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

        <form method="POST" action="{{ route('productos.store') }}" class="px-6 py-5 space-y-4">
            @csrf
            <input type="hidden" name="categoria_id" value="{{ \App\Models\Categoria::where('codigo', 'BR')->first()->id ?? '' }}">
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Código <span class="text-red-500">*</span></label>
                    <input type="text" name="codigo" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Consecutivo</label>
                    <input type="number" name="consecutivo"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Descripción <span class="text-red-500">*</span></label>
                <textarea name="descripcion" rows="2" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 resize-none"></textarea>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Componente</label>
                    <select name="componente_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">-- Ninguno --</option>
                        @foreach(\App\Models\Componente::orderBy('codigo')->get() as $comp)
                        <option value="{{ $comp->id }}">{{ $comp->codigo }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Familia</label>
                    <select name="familia_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">-- Ninguna --</option>
                        @foreach(\App\Models\Familia::orderBy('codigo')->get() as $fam)
                        <option value="{{ $fam->id }}">{{ $fam->codigo }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Unidad Medida</label>
                    <select name="unidad_medida_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">-- Ninguna --</option>
                        @foreach(\App\Models\UnidadMedida::orderBy('codigo')->get() as $um)
                        <option value="{{ $um->id }}">{{ $um->codigo }} - {{ $um->descripcion }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Ubicación</label>
                    <select name="ubicacion_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">-- Ninguna --</option>
                        @foreach(\App\Models\Ubicacion::orderBy('codigo')->get() as $ub)
                        <option value="{{ $ub->id }}">{{ $ub->codigo }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Fecha Entrada</label>
                    <input type="date" name="fecha_entrada"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Cant. Entrada</label>
                    <input type="number" step="0.01" name="cantidad_entrada"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Cant. Salida</label>
                    <input type="number" step="0.01" name="cantidad_salida"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Cant. Física</label>
                    <input type="number" step="0.01" name="cantidad_fisica"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Precio Unitario</label>
                    <input type="number" step="0.01" name="precio_unitario"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Moneda</label>
                    <select name="moneda" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">--</option>
                        <option value="MXN">MXN</option>
                        <option value="USD">USD</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Factura</label>
                    <input type="text" name="factura"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Observaciones (DN/NP)</label>
                <textarea name="observaciones" rows="2"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 resize-none"
                    placeholder="Número de parte, dibujo, etc."></textarea>
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
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Código <span class="text-red-500">*</span></label>
                    <input type="text" name="codigo" id="edit_codigo" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Consecutivo</label>
                    <input type="number" name="consecutivo" id="edit_consecutivo"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Descripción <span class="text-red-500">*</span></label>
                <textarea name="descripcion" id="edit_descripcion" rows="2" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 resize-none"></textarea>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Componente</label>
                    <select name="componente_id" id="edit_componente_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">-- Ninguno --</option>
                        @foreach(\App\Models\Componente::orderBy('codigo')->get() as $comp)
                        <option value="{{ $comp->id }}">{{ $comp->codigo }}</option>
                        @endforeach
                    </select>
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
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Familia</label>
                    <select name="familia_id" id="edit_familia_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">-- Ninguna --</option>
                        @foreach(\App\Models\Familia::orderBy('codigo')->get() as $fam)
                        <option value="{{ $fam->id }}">{{ $fam->codigo }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Unidad Medida</label>
                    <select name="unidad_medida_id" id="edit_unidad_medida_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">-- Ninguna --</option>
                        @foreach(\App\Models\UnidadMedida::orderBy('codigo')->get() as $um)
                        <option value="{{ $um->id }}">{{ $um->codigo }} - {{ $um->descripcion }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Ubicación</label>
                    <select name="ubicacion_id" id="edit_ubicacion_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">-- Ninguna --</option>
                        @foreach(\App\Models\Ubicacion::orderBy('codigo')->get() as $ub)
                        <option value="{{ $ub->id }}">{{ $ub->codigo }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Fecha Entrada</label>
                    <input type="date" name="fecha_entrada" id="edit_fecha_entrada"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Cant. Entrada</label>
                    <input type="number" step="0.01" name="cantidad_entrada" id="edit_cantidad_entrada"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Cant. Salida</label>
                    <input type="number" step="0.01" name="cantidad_salida" id="edit_cantidad_salida"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Cant. Física</label>
                    <input type="number" step="0.01" name="cantidad_fisica" id="edit_cantidad_fisica"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Precio Unitario</label>
                    <input type="number" step="0.01" name="precio_unitario" id="edit_precio_unitario"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Moneda</label>
                    <select name="moneda" id="edit_moneda" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">--</option>
                        <option value="MXN">MXN</option>
                        <option value="USD">USD</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Factura</label>
                <input type="text" name="factura" id="edit_factura"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Observaciones (DN/NP)</label>
                <textarea name="observaciones" id="edit_observaciones" rows="2"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 resize-none"
                    placeholder="Número de parte, dibujo, etc."></textarea>
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
        document.getElementById('edit_consecutivo').value = producto.consecutivo || '';
        document.getElementById('edit_descripcion').value = producto.descripcion || '';
        document.getElementById('edit_componente_id').value = producto.componente_id || '';
        document.getElementById('edit_categoria_id').value = producto.categoria_id || '';
        document.getElementById('edit_familia_id').value = producto.familia_id || '';
        document.getElementById('edit_unidad_medida_id').value = producto.unidad_medida_id || '';
        document.getElementById('edit_ubicacion_id').value = producto.ubicacion_id || '';
        document.getElementById('edit_fecha_entrada').value = producto.fecha_entrada || '';
        document.getElementById('edit_cantidad_entrada').value = producto.cantidad_entrada || '';
        document.getElementById('edit_cantidad_salida').value = producto.cantidad_salida || '';
        document.getElementById('edit_cantidad_fisica').value = producto.cantidad_fisica || '';
        document.getElementById('edit_precio_unitario').value = producto.precio_unitario || '';
        document.getElementById('edit_moneda').value = producto.moneda || '';
        document.getElementById('edit_factura').value = producto.factura || '';
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
    } 
});
</script>
@endsection
