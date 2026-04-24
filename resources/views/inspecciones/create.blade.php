@extends('layouts.app')

@section('title', 'Nueva Inspección de Ingreso')

@section('content')
@php $acento = '#4A568D'; @endphp
<div class="space-y-4">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('inspecciones.index') }}" class="hover:underline transition" style="color:{{ $acento }}">Inspecciones</a>
        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-gray-700 font-medium">Nueva Inspección</span>
    </div>

    @if($errors->any())
    <div class="flex items-start gap-3 px-4 py-3 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm">
        <svg class="w-5 h-5 shrink-0 mt-0.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('inspecciones.store') }}" method="POST" id="formInspeccion">
        @csrf
        <input type="hidden" name="articulo_id" id="input_articulo_id" value="{{ $articulo?->id ?? old('articulo_id') }}">

        {{-- ── Tarjeta con formato físico ── --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

            {{-- Encabezado institucional --}}
            <div class="border-b border-gray-200">
                <div class="flex">
                    <div class="w-24 shrink-0 border-r border-gray-200 flex items-center justify-center p-3 bg-gray-50">
                        @if(file_exists(public_path('storage/img/logo_gpt.svg')))
                            <img src="{{ asset('storage/img/logo_gpt.svg') }}" alt="GPT" class="h-10 w-auto">
                        @else
                            <span class="text-xs font-bold text-orange-600 text-center leading-tight">GPT<br><span class="text-gray-500">SERVICES</span></span>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-center border-b border-gray-200 py-1.5 px-4 bg-gray-50">
                            <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">TECH ENERGY CONTROL S.A. DE C.V.</p>
                        </div>
                        <div class="text-center border-b border-gray-200 py-1.5 px-4" style="background-color:{{ $acento }}10;">
                            <p class="text-sm font-bold uppercase tracking-wide" style="color:{{ $acento }}">INSPECCIONES PARA INGRESO A INVENTARIO</p>
                        </div>
                        <div class="flex text-xs divide-x divide-gray-200 bg-white">
                            <div class="flex-1 px-3 py-1.5"><p class="text-gray-400 font-medium">TIPO DE DOCUMENTO</p><p class="text-gray-700">Formato</p></div>
                            <div class="w-20 px-3 py-1.5"><p class="text-gray-400 font-medium">REVISIÓN</p><p class="text-gray-700">0</p></div>
                            <div class="w-32 px-3 py-1.5"><p class="text-gray-400 font-medium">FECHA APROBACIÓN</p><p class="text-gray-700">Nov-25</p></div>
                            <div class="w-24 px-3 py-1.5"><p class="text-gray-400 font-medium">DEPARTAMENTO</p><p class="text-gray-700">Almacén</p></div>
                            <div class="w-36 px-3 py-1.5"><p class="text-gray-400 font-medium">CÓDIGO</p><p class="text-gray-700 font-mono font-semibold">FO-GPT-ALM-01-E</p></div>
                            <div class="w-20 px-3 py-1.5"><p class="text-gray-400 font-medium">PÁGINA</p><p class="text-gray-700">1 de 1</p></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-5 space-y-5">

                {{-- ── Fila 1: datos generales ── --}}
                <div class="flex flex-wrap gap-4 items-end">
                    <div class="w-44 shrink-0">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Fecha de recepción</label>
                        <input type="date" name="fecha_recepcion" value="{{ old('fecha_recepcion') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition
                                      @error('fecha_recepcion') border-red-400 @enderror">
                    </div>
                    <div class="flex-1 min-w-36">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Requisición</label>
                        <input type="text" name="requisicion" value="{{ old('requisicion') }}" placeholder="No. Requisición"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition">
                    </div>
                    <div class="flex-1 min-w-36">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">O.C.</label>
                        <input type="text" name="orden_compra" value="{{ old('orden_compra') }}" placeholder="Orden de compra"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition">
                    </div>
                    <div class="flex-[2] min-w-64">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">DN / NP / CP / Otro</label>
                        <div class="flex gap-2">
                            <select name="tipo_documento" id="tipo_documento"
                                    class="w-28 shrink-0 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 bg-white transition"
                                    onchange="toggleOtro()">
                                <option value="">— Tipo —</option>
                                @foreach(['DN','NP','CP','Otro'] as $td)
                                <option value="{{ $td }}" {{ old('tipo_documento') === $td ? 'selected' : '' }}>{{ $td }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="numero_documento" id="numero_documento"
                                   value="{{ old('numero_documento') }}" placeholder="Número (ej. 39191392)"
                                   class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition">
                        </div>
                        <input type="text" name="tipo_documento_otro" id="tipo_documento_otro"
                               value="{{ old('tipo_documento_otro') }}" placeholder="Especificar tipo…"
                               class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 transition
                                      {{ old('tipo_documento') === 'Otro' ? '' : 'hidden' }}">
                    </div>
                </div>

                {{-- ── Selector de producto (opcional) ── --}}
                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-200">
                    <div id="articulo-badge" class="{{ ($articulo || old('articulo_id')) ? '' : 'hidden' }} flex items-center gap-2 flex-1 min-w-0">
                        <div class="flex items-center gap-2 px-3 py-1.5 bg-indigo-50 border border-indigo-200 rounded-lg text-sm flex-1 min-w-0">
                            <svg class="w-4 h-4 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                            </svg>
                            <span id="articulo-codigo" class="font-mono font-bold text-indigo-900 shrink-0">{{ $articulo?->codigo ?? '' }}</span>
                            <span id="articulo-desc" class="text-indigo-700 truncate">{{ $articulo?->descripcion ?? '' }}</span>
                        </div>
                        <button type="button" onclick="limpiarArticulo()"
                                class="shrink-0 text-gray-400 hover:text-red-500 transition" title="Quitar artículo">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <span id="articulo-vacio" class="{{ ($articulo || old('articulo_id')) ? 'hidden' : '' }} text-xs text-gray-400 flex-1">
                        Sin artículo vinculado — opcional
                    </span>
                    <button type="button" onclick="abrirModalProducto()"
                            class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Vincular Producto
                    </button>
                </div>

                {{-- ── Cabeceras ── --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="rounded-t-lg py-2 px-4 text-center text-sm font-bold text-white" style="background-color:#3A8FC0;">
                        Solicitante
                    </div>
                    <div class="rounded-t-lg py-2 px-4 text-center text-sm font-bold text-white flex items-center justify-center gap-2" style="background-color:#6b7280;">
                        <svg class="w-3.5 h-3.5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Control de Calidad
                    </div>
                </div>

                {{-- ── Dos columnas ── --}}
                <div class="grid grid-cols-2 gap-4 -mt-2">

                    {{-- Solicitante --}}
                    <div class="border border-gray-200 rounded-b-xl rounded-tr-xl p-4 space-y-3" style="background-color:#EBF5FB;">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Fecha de inspección</label>
                            <input type="date" name="fecha_inspeccion_solicitante" value="{{ old('fecha_inspeccion_solicitante') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">
                                Inspeccionó (Nombre)
                                <span id="ac-loading-sol" class="hidden ml-1 text-blue-400 font-normal normal-case">cargando…</span>
                            </label>
                            <div class="relative">
                                <input type="text" id="nombre-sol" name="inspeccionado_solicitante"
                                       value="{{ old('inspeccionado_solicitante') }}"
                                       placeholder="Escriba para buscar empleado…"
                                       autocomplete="off"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition">
                                <div id="dropdown-sol"
                                     class="hidden absolute left-0 right-0 top-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg z-50 max-h-52 overflow-y-auto"></div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Departamento</label>
                            <input type="text" id="depto-sol" name="departamento_solicitante"
                                   value="{{ old('departamento_solicitante') }}"
                                   placeholder="Se llena al seleccionar nombre"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Observaciones</label>
                            <textarea name="observaciones_solicitante" rows="3"
                                      placeholder="Observaciones del solicitante…"
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition resize-none">{{ old('observaciones_solicitante') }}</textarea>
                        </div>
                    </div>

                    {{-- Calidad — bloqueada en fase 1 --}}
                    <div class="border border-dashed border-gray-300 rounded-b-xl rounded-tl-xl p-6 flex flex-col items-center justify-center gap-3 bg-gray-50 text-center">
                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <p class="text-sm font-semibold text-gray-400">Control de Calidad — Fase 2</p>
                        <p class="text-xs text-gray-400 max-w-xs leading-relaxed">
                            Si marcas <strong class="text-gray-500">Requiere Ctrl. Calidad = SÍ</strong>, el equipo de calidad recibirá una notificación y podrá completar esta sección.
                        </p>
                    </div>
                </div>

                {{-- ── Sección inferior ── --}}
                <div class="bg-gray-50 rounded-xl border border-gray-200 p-4 space-y-4">

                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 items-start">
                        {{-- Requiere Ctrl. Calidad --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Requiere Ctrl. Calidad</label>
                            <div class="flex gap-3">
                                <label class="flex items-center gap-1.5 cursor-pointer">
                                    <input type="radio" name="requiere_ctrl_calidad" value="1"
                                           {{ old('requiere_ctrl_calidad', '1') == '1' ? 'checked' : '' }}
                                           class="w-4 h-4 text-indigo-600 focus:ring-indigo-400">
                                    <span class="text-sm font-semibold text-gray-700 border border-gray-400 px-2.5 py-0.5 rounded-lg">SÍ</span>
                                </label>
                                <label class="flex items-center gap-1.5 cursor-pointer">
                                    <input type="radio" name="requiere_ctrl_calidad" value="0"
                                           {{ old('requiere_ctrl_calidad') == '0' ? 'checked' : '' }}
                                           class="w-4 h-4 text-indigo-600 focus:ring-indigo-400">
                                    <span class="text-sm font-semibold text-gray-700 border border-gray-400 px-2.5 py-0.5 rounded-lg">NO</span>
                                </label>
                            </div>
                        </div>

                        {{-- Fecha ingreso inventario --}}
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Fecha de ingreso a inventario</label>
                            <input type="date" name="fecha_ingreso_inventario" value="{{ old('fecha_ingreso_inventario') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition">
                        </div>
                    </div>

                    {{-- Resultado Solicitante --}}
                    <div class="pt-2 border-t border-gray-200">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3 text-center">
                            Resultado de inspección — Solicitante
                        </p>
                        <div class="flex gap-3 justify-center">
                            @foreach([
                                'no_conforme' => ['No Conforme', 'bg-red-500',   'ring-red-400'],
                                'conforme'    => ['Conforme',    'bg-green-600', 'ring-green-400'],
                                'a_revision'  => ['A Revisión',  'bg-yellow-400','ring-yellow-400'],
                            ] as $val => [$label, $bg, $ring])
                            <label class="flex flex-col items-center gap-1.5 cursor-pointer group">
                                <input type="radio" name="resultado_solicitante" value="{{ $val }}"
                                       {{ old('resultado_solicitante') === $val ? 'checked' : '' }}
                                       class="sr-only peer">
                                <span class="w-24 h-8 rounded-lg {{ $bg }} opacity-30 peer-checked:opacity-100 peer-checked:ring-2 peer-checked:{{ $ring }} peer-checked:ring-offset-1 transition-all group-hover:opacity-60"></span>
                                <span class="text-xs text-gray-500 peer-checked:font-semibold peer-checked:text-gray-800 transition">{{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                        @error('resultado_solicitante')
                        <p class="text-red-500 text-xs mt-1 text-center">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

            </div>

            {{-- Footer --}}
            <div class="border-t border-gray-200 px-5 py-3 bg-gray-50 flex justify-between items-center">
                <a href="{{ route('inspecciones.index') }}"
                   class="px-4 py-2 bg-white border border-gray-300 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                    ← Cancelar
                </a>
                <button type="submit"
                        class="px-5 py-2.5 text-white text-sm font-semibold rounded-xl shadow transition hover:opacity-90 active:scale-95"
                        style="background-color:{{ $acento }}">
                    Registrar Inspección
                </button>
            </div>
        </div>
    </form>
</div>

{{-- ── Modal selector de producto ── --}}
<div id="modal-producto" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.45);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl flex flex-col" style="max-height:85vh;">
        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
            <div>
                <h3 class="text-base font-bold text-gray-800">Vincular Producto</h3>
                <p class="text-xs text-gray-400 mt-0.5">Opcional — busca y selecciona el artículo inspeccionado</p>
            </div>
            <button type="button" onclick="cerrarModalProducto()"
                    class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Buscador --}}
        <div class="px-5 py-3 border-b border-gray-100">
            <div class="relative">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" id="mp-search" placeholder="Buscar por código o descripción…"
                       oninput="buscarProductos(this.value)"
                       class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition"
                       autocomplete="off">
                <span id="mp-spinner" class="hidden absolute right-3 top-1/2 -translate-y-1/2">
                    <svg class="animate-spin w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                </span>
            </div>
        </div>

        {{-- Resultados --}}
        <div id="mp-resultados" class="flex-1 overflow-y-auto divide-y divide-gray-100">
            <div id="mp-placeholder" class="flex flex-col items-center justify-center py-16 text-gray-400 gap-2">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <p class="text-sm">Escribe para buscar productos</p>
            </div>
        </div>

        {{-- Footer --}}
        <div class="px-5 py-3 border-t border-gray-200 bg-gray-50 rounded-b-2xl flex justify-end">
            <button type="button" onclick="cerrarModalProducto()"
                    class="px-4 py-2 text-sm text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                Cerrar sin seleccionar
            </button>
        </div>
    </div>
</div>

<script>
// ── Tipo documento ─────────────────────────────────────────
function toggleOtro() {
    const sel   = document.getElementById('tipo_documento');
    const input = document.getElementById('tipo_documento_otro');
    const show  = sel.value === 'Otro';
    input.classList.toggle('hidden', !show);
    if (!show) input.value = '';
}

// ── Modal selector de producto ─────────────────────────────
function abrirModalProducto() {
    document.getElementById('modal-producto').classList.remove('hidden');
    document.getElementById('mp-search').focus();
}

function cerrarModalProducto() {
    document.getElementById('modal-producto').classList.add('hidden');
    document.getElementById('mp-search').value = '';
    renderPlaceholder();
}

function limpiarArticulo() {
    document.getElementById('input_articulo_id').value = '';
    document.getElementById('articulo-badge').classList.add('hidden');
    document.getElementById('articulo-vacio').classList.remove('hidden');
}

function seleccionarProducto(id, codigo, descripcion) {
    document.getElementById('input_articulo_id').value   = id;
    document.getElementById('articulo-codigo').textContent = codigo;
    document.getElementById('articulo-desc').textContent   = descripcion;
    document.getElementById('articulo-badge').classList.remove('hidden');
    document.getElementById('articulo-vacio').classList.add('hidden');
    cerrarModalProducto();
}

function renderPlaceholder() {
    document.getElementById('mp-resultados').innerHTML = `
        <div id="mp-placeholder" class="flex flex-col items-center justify-center py-16 text-gray-400 gap-2">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <p class="text-sm">Escribe para buscar productos</p>
        </div>`;
}

let _mpTimer = null;
function buscarProductos(q) {
    clearTimeout(_mpTimer);
    if (q.trim().length < 2) { renderPlaceholder(); return; }

    _mpTimer = setTimeout(async () => {
        const spinner = document.getElementById('mp-spinner');
        spinner.classList.remove('hidden');
        try {
            const res  = await fetch(`/api/v1/productos/buscar?q=${encodeURIComponent(q.trim())}&limit=20`);
            const data = res.ok ? await res.json() : [];
            renderResultados(Array.isArray(data) ? data : (data.data ?? []));
        } catch {
            renderResultados([]);
        } finally {
            spinner.classList.add('hidden');
        }
    }, 300);
}

function renderResultados(items) {
    const cont = document.getElementById('mp-resultados');
    if (!items.length) {
        cont.innerHTML = `<div class="flex flex-col items-center justify-center py-16 text-gray-400 gap-1">
            <p class="text-sm font-medium">Sin resultados</p>
            <p class="text-xs">Intenta con otro código o descripción</p></div>`;
        return;
    }
    cont.innerHTML = items.map(p => {
        const codigo = (p.codigo || '').replace(/</g,'&lt;');
        const desc   = (p.descripcion || '').replace(/</g,'&lt;');
        const stock  = p.cantidad_fisica ?? p.stock ?? '—';
        const um     = p.unidad_medida?.codigo ?? p.um ?? '';
        return `<div class="px-5 py-3 hover:bg-indigo-50 cursor-pointer transition-colors flex items-center gap-4"
                     onclick="seleccionarProducto(${p.id}, '${codigo.replace(/'/g,"\\'")}', '${desc.replace(/'/g,"\\'")}')">
                    <div class="w-28 shrink-0">
                        <span class="font-mono text-xs font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded">${codigo}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">${desc}</p>
                    </div>
                    <div class="shrink-0 text-right">
                        <p class="text-xs font-semibold text-gray-700">${stock} <span class="text-gray-400 font-normal">${um}</span></p>
                        <p class="text-xs text-gray-400">en stock</p>
                    </div>
                </div>`;
    }).join('');
}

// Cerrar modal con Escape o click fuera
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') cerrarModalProducto();
});
document.getElementById('modal-producto').addEventListener('click', function(e) {
    if (e.target === this) cerrarModalProducto();
});

// ── Autocomplete empleados RH ───────────────────────────────
let _empleados = null;
let _cargando  = false;

async function cargarEmpleados() {
    if (_empleados !== null || _cargando) return;
    _cargando = true;
    document.querySelectorAll('[id^="ac-loading-"]').forEach(el => el.classList.remove('hidden'));
    try {
        const res  = await fetch('{{ route("inspecciones.empleados_rh") }}');
        _empleados = res.ok ? await res.json() : [];
    } catch {
        _empleados = [];
    } finally {
        _cargando = false;
        document.querySelectorAll('[id^="ac-loading-"]').forEach(el => el.classList.add('hidden'));
    }
}

function renderDropdown(dropdownId, matches, inputId, deptoId) {
    const dd = document.getElementById(dropdownId);
    if (!matches.length) { dd.classList.add('hidden'); return; }
    dd.innerHTML = matches.map(e => {
        const nombre = e.nombre.replace(/"/g, '&quot;');
        const depto  = (e.departamento || '').replace(/"/g, '&quot;');
        return `<div class="px-3 py-2.5 cursor-pointer hover:bg-indigo-50 border-b border-gray-100 last:border-0 transition-colors"
                     data-nombre="${nombre}" data-depto="${depto}"
                     onmousedown="seleccionarEmpleado('${inputId}','${deptoId}','${dropdownId}',this)">
                    <p class="text-sm font-medium text-gray-800 leading-tight">${e.nombre}</p>
                    <p class="text-xs text-gray-400 mt-0.5">${e.departamento || '—'}</p>
                </div>`;
    }).join('');
    dd.classList.remove('hidden');
}

function seleccionarEmpleado(inputId, deptoId, dropdownId, el) {
    document.getElementById(inputId).value = el.dataset.nombre;
    document.getElementById(deptoId).value = el.dataset.depto;
    document.getElementById(dropdownId).classList.add('hidden');
}

function initAutocomplete(inputId, deptoId, dropdownId) {
    const input    = document.getElementById(inputId);
    const dropdown = document.getElementById(dropdownId);
    input.addEventListener('focus', cargarEmpleados);
    input.addEventListener('input', function () {
        if (_empleados === null) { cargarEmpleados(); return; }
        const q = this.value.trim().toLowerCase();
        if (q.length < 2) { dropdown.classList.add('hidden'); return; }
        const matches = _empleados.filter(e => e.nombre.toLowerCase().includes(q)).slice(0, 10);
        renderDropdown(dropdownId, matches, inputId, deptoId);
    });
    input.addEventListener('blur', () => setTimeout(() => dropdown.classList.add('hidden'), 180));
    input.addEventListener('keydown', function (e) {
        const items = dropdown.querySelectorAll('[data-nombre]');
        if (!items.length) return;
        const active = dropdown.querySelector('.bg-indigo-100');
        let idx = active ? [...items].indexOf(active) : -1;
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (active) active.classList.replace('bg-indigo-100', 'hover:bg-indigo-50');
            idx = (idx + 1) % items.length;
            items[idx].classList.add('bg-indigo-100');
            items[idx].scrollIntoView({ block: 'nearest' });
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (active) active.classList.replace('bg-indigo-100', 'hover:bg-indigo-50');
            idx = (idx - 1 + items.length) % items.length;
            items[idx].classList.add('bg-indigo-100');
            items[idx].scrollIntoView({ block: 'nearest' });
        } else if (e.key === 'Enter' && active) {
            e.preventDefault();
            seleccionarEmpleado(inputId, deptoId, dropdownId, active);
        } else if (e.key === 'Escape') {
            dropdown.classList.add('hidden');
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initAutocomplete('nombre-sol', 'depto-sol', 'dropdown-sol');
});
</script>
@endsection
