@extends('layouts.app')
@section('title', 'Concentrado de Requisiciones')
@section('content')
@php
    $acento = '#4A568D';
    $claro  = '#eef0f8';

    $estadoBadge = [
        'pendiente'  => 'bg-yellow-100 text-yellow-800',
        'aprobada'   => 'bg-blue-100 text-blue-800',
        'entregada'  => 'bg-green-100 text-green-800',
        'cancelada'  => 'bg-red-100 text-red-800',
    ];
    $prioridadBadge = [
        'urgente' => 'bg-red-100 text-red-700',
        'alta'    => 'bg-orange-100 text-orange-700',
        'normal'  => 'bg-gray-100 text-gray-600',
        'baja'    => 'bg-slate-100 text-slate-500',
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

    @if($errors->any())
    <div class="flex items-start gap-3 px-4 py-3 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm">
        <svg class="w-5 h-5 shrink-0 mt-0.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <ul class="list-disc ml-2 space-y-0.5">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Concentrado de Requisiciones</h1>
            <p class="text-xs text-gray-500 mt-0.5">{{ number_format($registros->total()) }} registros</p>
        </div>
        <button onclick="abrirModal()"
            class="flex items-center gap-2 px-4 py-2.5 text-white text-sm font-semibold rounded-xl shadow transition hover:opacity-90 active:scale-95"
            style="background-color:{{ $acento }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nueva Solicitud
        </button>
    </div>

    {{-- Filtros --}}
    <form method="GET" action="{{ route('reportes.requisiciones') }}" class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-4 py-3 flex gap-3 items-center flex-wrap">
            <div class="flex-1 relative min-w-56">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Buscar por folio, solicitante, código, departamento..."
                    class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 transition"
                    autocomplete="off">
            </div>
            <select name="estado" class="border border-gray-300 rounded-lg text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="">Todos los estados</option>
                <option value="pendiente"  {{ request('estado') === 'pendiente'  ? 'selected' : '' }}>Pendiente</option>
                <option value="aprobada"   {{ request('estado') === 'aprobada'   ? 'selected' : '' }}>Aprobada</option>
                <option value="entregada"  {{ request('estado') === 'entregada'  ? 'selected' : '' }}>Entregada</option>
                <option value="cancelada"  {{ request('estado') === 'cancelada'  ? 'selected' : '' }}>Cancelada</option>
            </select>
            <select name="prioridad" class="border border-gray-300 rounded-lg text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="">Toda prioridad</option>
                <option value="urgente" {{ request('prioridad') === 'urgente' ? 'selected' : '' }}>Urgente</option>
                <option value="alta"    {{ request('prioridad') === 'alta'    ? 'selected' : '' }}>Alta</option>
                <option value="normal"  {{ request('prioridad') === 'normal'  ? 'selected' : '' }}>Normal</option>
                <option value="baja"    {{ request('prioridad') === 'baja'    ? 'selected' : '' }}>Baja</option>
            </select>
            <button type="submit" class="px-4 py-2 text-white text-sm font-medium rounded-lg transition hover:opacity-90" style="background-color:{{ $acento }}">Filtrar</button>
            @if(request()->hasAny(['search','estado','prioridad']))
            <a href="{{ route('reportes.requisiciones') }}" class="px-3 py-2 bg-gray-100 text-gray-600 text-sm rounded-lg hover:bg-gray-200 transition">✕ Limpiar</a>
            @endif
        </div>
    </form>

    {{-- Tabla --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto" style="max-height:calc(100vh - 280px);overflow-y:auto;">
            <table class="w-full text-xs border-collapse" style="min-width:1200px;">
                <thead class="sticky top-0 z-10">
                    <tr style="background-color:{{ $acento }};">
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">FECHA</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">F. REQUERIDA</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">FOLIO</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">SOLICITANTE</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">DEPARTAMENTO</th>
                        <th class="px-3 py-2.5 text-right text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">CANTIDAD</th>
                        <th class="px-3 py-2.5 text-center text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">U.M</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">CODIGO</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">DN / NP</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide border-r border-indigo-600" style="min-width:200px;">DESCRIPCIÓN</th>
                        <th class="px-3 py-2.5 text-center text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">PRIORIDAD</th>
                        <th class="px-3 py-2.5 text-center text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">ESTADO</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap">REGISTRADO POR</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($registros as $r)
                    <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }} hover:bg-blue-50 transition-colors">
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $r->fecha ? $r->fecha->format('d/m/Y') : '—' }}</td>
                        <td class="px-3 py-2 whitespace-nowrap {{ ($r->fecha_requerida && $r->fecha_requerida->isPast()) ? 'text-red-600 font-semibold' : 'text-gray-600' }}">
                            {{ $r->fecha_requerida ? $r->fecha_requerida->format('d/m/Y') : '—' }}
                        </td>
                        <td class="px-3 py-2 font-mono font-semibold whitespace-nowrap" style="color:{{ $acento }}">{{ $r->folio ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $r->solicitante ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $r->departamento->nombre ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-800 text-right whitespace-nowrap font-semibold">{{ $r->cantidad !== null ? number_format($r->cantidad, 2) : '—' }}</td>
                        <td class="px-3 py-2 text-gray-700 text-center whitespace-nowrap">{{ $r->unidadMedida->codigo ?? '—' }}</td>
                        <td class="px-3 py-2 font-mono text-gray-800 whitespace-nowrap">{{ $r->producto->codigo ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-600 whitespace-nowrap max-w-xs truncate">{{ $r->observaciones ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-800">{{ $r->producto->descripcion ?? '—' }}</td>
                        {{-- PRIORIDAD badge --}}
                        <td class="px-3 py-2 text-center whitespace-nowrap">
                            @if($r->prioridad)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $prioridadBadge[$r->prioridad] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ ucfirst($r->prioridad) }}
                            </span>
                            @else<span class="text-gray-400">—</span>@endif
                        </td>
                        {{-- ESTADO inline editable --}}
                        <td class="px-3 py-2 text-center whitespace-nowrap">
                            <select
                                class="estado-select text-xs border-0 rounded-full px-2 py-0.5 font-semibold cursor-pointer {{ $estadoBadge[$r->estado] ?? 'bg-gray-100 text-gray-600' }}"
                                data-id="{{ $r->id }}"
                                data-url="{{ route('solicitudes.cambiarEstado', $r->id) }}"
                                style="appearance:none;-webkit-appearance:none;">
                                <option value="pendiente" {{ $r->estado === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="aprobada"  {{ $r->estado === 'aprobada'  ? 'selected' : '' }}>Aprobada</option>
                                <option value="entregada" {{ $r->estado === 'entregada' ? 'selected' : '' }}>Entregada</option>
                                <option value="cancelada" {{ $r->estado === 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                            </select>
                        </td>
                        <td class="px-3 py-2 text-gray-500 whitespace-nowrap text-xs">{{ $r->usuarioRegistro->name ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="13" class="px-6 py-16 text-center text-gray-400">
                        <div class="flex flex-col items-center gap-2">
                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <span>No hay requisiciones{{ request()->hasAny(['search','estado','prioridad']) ? ' para este filtro' : '' }}</span>
                            <button type="button" onclick="abrirModal()" class="text-sm font-medium hover:underline" style="color:{{ $acento }}">Crear primera solicitud →</button>
                        </div>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @include('reportes._paginacion', ['items' => $registros, 'acento' => $acento])
    </div>

</div>

{{-- ═══════════════════════════════════════
     MODAL: Nueva Solicitud
═══════════════════════════════════════ --}}
<div id="modalSolicitud"
    class="fixed inset-0 z-50 hidden items-center justify-center p-4"
    style="background:rgba(0,0,0,0.45);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[92vh] flex flex-col">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 rounded-t-2xl text-white" style="background-color:{{ $acento }};">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span class="font-semibold text-base">Nueva Solicitud de Material</span>
            </div>
            <button onclick="cerrarModal()" class="text-white opacity-70 hover:opacity-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <form method="POST" action="{{ route('solicitudes.nueva') }}" id="formSolicitud"
              class="overflow-y-auto flex-1 px-6 py-5 space-y-4">
            @csrf

            {{-- Fila 1: Fecha / Fecha req. / Folio --}}
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Fecha <span class="text-red-500">*</span></label>
                    <input type="date" name="fecha" value="{{ now()->format('Y-m-d') }}" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Fecha Requerida</label>
                    <input type="date" name="fecha_requerida"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Folio <span class="text-gray-400 font-normal">(opcional)</span></label>
                    <input type="text" name="folio" placeholder="Ej. REQ-001"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            </div>

            {{-- Fila 2: Solicitante / Estado / Prioridad --}}
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Solicitante <span class="text-red-500">*</span></label>
                    <input type="text" name="solicitante" value="{{ auth()->user()->name }}" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Estado <span class="text-red-500">*</span></label>
                    <select name="estado" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="pendiente" selected>Pendiente</option>
                        <option value="aprobada">Aprobada</option>
                        <option value="entregada">Entregada</option>
                        <option value="cancelada">Cancelada</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Prioridad</label>
                    <select name="prioridad" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="urgente">🔴 Urgente</option>
                        <option value="alta">🟠 Alta</option>
                        <option value="normal" selected>⚪ Normal</option>
                        <option value="baja">🔵 Baja</option>
                    </select>
                </div>
            </div>

            {{-- Departamento typeahead --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">
                    Departamento <span class="text-red-500">*</span>
                    <span id="deptoNuevoTag" class="hidden ml-1 text-xs font-medium px-1.5 py-0.5 rounded-full bg-green-100 text-green-700">✦ Se creará nuevo</span>
                </label>
                <div class="relative">
                    <input type="text" id="depto_search"
                        placeholder="Escribe para buscar o crear departamento..."
                        autocomplete="off"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    <input type="hidden" name="departamento_id" id="depto_id">
                    <input type="hidden" name="departamento_nombre" id="depto_nombre">
                    <div id="depto_dropdown" class="hidden absolute left-0 right-0 top-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg z-50 max-h-44 overflow-y-auto"></div>
                </div>
                <p class="text-xs text-gray-400 mt-1">Si no existe, escríbelo y selecciona "Crear nuevo" — se creará automáticamente</p>
            </div>

            {{-- Producto typeahead --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Producto <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="text" id="prod_search"
                        placeholder="Buscar por código o descripción..."
                        autocomplete="off"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    <input type="hidden" name="producto_id" id="prod_id">
                    <div id="prod_dropdown" class="hidden absolute left-0 right-0 top-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg z-50 max-h-52 overflow-y-auto"></div>
                </div>
                <div id="prod_preview" class="hidden mt-2 flex items-center gap-2 px-3 py-2 rounded-lg text-xs border" style="background:{{ $claro }};border-color:#c7cfe7;">
                    <svg class="w-3.5 h-3.5 shrink-0" style="color:{{ $acento }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span id="prod_preview_text" class="font-medium" style="color:{{ $acento }}"></span>
                </div>
            </div>

            {{-- Cantidad + UM --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Cantidad <span class="text-red-500">*</span></label>
                    <input type="number" name="cantidad" min="0.01" step="any" placeholder="0" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Unidad de Medida</label>
                    <div class="relative">
                        <input type="text" id="um_search"
                            placeholder="PZA, KG, MT..."
                            autocomplete="off"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <input type="hidden" name="unidad_medida_id" id="um_id">
                        <div id="um_dropdown" class="hidden absolute left-0 right-0 top-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg z-50 max-h-44 overflow-y-auto"></div>
                    </div>
                </div>
            </div>

            {{-- Observaciones --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">DN / NP / Observaciones</label>
                <textarea name="observaciones" rows="2"
                    placeholder="Número de parte, número de diseño u observaciones adicionales..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 resize-none"></textarea>
            </div>
        </form>

        {{-- Footer --}}
        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between gap-3 rounded-b-2xl bg-gray-50">
            <p class="text-xs text-gray-400"><span class="text-red-500">*</span> Campos requeridos</p>
            <div class="flex gap-3">
                <button type="button" onclick="cerrarModal()"
                    class="px-5 py-2 bg-white border border-gray-300 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-50 transition">
                    Cancelar
                </button>
                <button type="submit" form="formSolicitud" id="btnGuardar"
                    class="px-6 py-2 text-white text-sm font-semibold rounded-xl transition hover:opacity-90 active:scale-95 flex items-center gap-2"
                    style="background-color:{{ $acento }}">
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
const ACENTO = '{{ $acento }}';
const CSRF   = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

/* ─── Modal ─────────────────────────────────── */
function abrirModal() {
    const m = document.getElementById('modalSolicitud');
    m.classList.remove('hidden');
    m.classList.add('flex');
    document.body.style.overflow = 'hidden';
    document.getElementById('formSolicitud').reset();
    ['depto_id','depto_nombre','prod_id','um_id'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('prod_preview').classList.add('hidden');
    document.getElementById('deptoNuevoTag').classList.add('hidden');
    const btn = document.getElementById('btnGuardar');
    btn.disabled = false;
    btn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Guardar Solicitud`;
    setTimeout(() => document.getElementById('depto_search').focus(), 120);
}
function cerrarModal() {
    const m = document.getElementById('modalSolicitud');
    m.classList.add('hidden');
    m.classList.remove('flex');
    document.body.style.overflow = '';
}
document.getElementById('modalSolicitud').addEventListener('click', e => { if (e.target === document.getElementById('modalSolicitud')) cerrarModal(); });
document.addEventListener('keydown', e => { if (e.key === 'Escape') cerrarModal(); });

/* ─── Typeahead genérico ─────────────────── */
function crearTypeahead({ inputId, dropdownId, hiddenId, endpoint, renderItem, onSelect, allowCreate = false }) {
    const input    = document.getElementById(inputId);
    const dropdown = document.getElementById(dropdownId);
    const hidden   = document.getElementById(hiddenId);
    let timer = null;

    input.addEventListener('input', function() {
        clearTimeout(timer);
        const q = this.value.trim();
        hidden.value = '';
        if (!q) { ocultarDropdown(); return; }
        timer = setTimeout(() => buscar(q), 230);
    });
    input.addEventListener('focus', function() {
        if (this.value.trim()) buscar(this.value.trim());
    });
    input.addEventListener('blur', () => setTimeout(ocultarDropdown, 200));

    function ocultarDropdown() { dropdown.classList.add('hidden'); }

    async function buscar(q) {
        try {
            const r    = await fetch(`${endpoint}?q=${encodeURIComponent(q)}`);
            const json = await r.json();
            mostrarResultados(json.data || [], q);
        } catch(e) { console.error(e); }
    }

    function mostrarResultados(items, q) {
        dropdown.innerHTML = '';

        items.forEach(item => {
            const div = document.createElement('div');
            div.className = 'px-3 py-2 text-sm text-gray-700 hover:bg-indigo-50 cursor-pointer flex items-center gap-2';
            div.innerHTML = renderItem(item, q);
            div.addEventListener('mousedown', e => {
                e.preventDefault();
                hidden.value  = item.id;
                input.value   = item.label || item.codigo || item.nombre || '';
                delete input.dataset.newNombre;
                ocultarDropdown();
                if (onSelect) onSelect(item);
            });
            dropdown.appendChild(div);
        });

        if (items.length === 0 && allowCreate) {
            const q2 = input.value.trim();
            const div = document.createElement('div');
            div.className = 'px-3 py-2.5 text-sm font-semibold cursor-pointer flex items-center gap-2 hover:bg-green-50 text-green-700';
            div.innerHTML = `<svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Crear departamento "<strong>${xss(q2)}</strong>"`;
            div.addEventListener('mousedown', e => {
                e.preventDefault();
                hidden.value = '';
                document.getElementById('depto_nombre').value = q2;
                document.getElementById('deptoNuevoTag').classList.remove('hidden');
                input.dataset.newNombre = q2;
                ocultarDropdown();
                if (onSelect) onSelect({ id: null, label: q2, isNew: true });
            });
            dropdown.appendChild(div);
        }

        if (dropdown.children.length > 0) dropdown.classList.remove('hidden');
        else ocultarDropdown();
    }
}

/* ─── Instancias de typeahead ────────────── */
// Departamento
crearTypeahead({
    inputId: 'depto_search', dropdownId: 'depto_dropdown', hiddenId: 'depto_id',
    endpoint: '/api/v1/departamentos/buscar', allowCreate: true,
    renderItem: (item, q) => `<svg class="w-3.5 h-3.5 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>${hl(item.label, q)}`,
    onSelect: (item) => {
        if (!item.isNew) {
            document.getElementById('depto_nombre').value = item.label;
            document.getElementById('deptoNuevoTag').classList.add('hidden');
        }
    },
});

// Producto
crearTypeahead({
    inputId: 'prod_search', dropdownId: 'prod_dropdown', hiddenId: 'prod_id',
    endpoint: '/api/v1/productos/buscar',
    renderItem: (item, q) => {
        const c = item.codigo || '', d = item.descripcion || '';
        return `<span class="font-mono font-bold text-xs shrink-0" style="color:${ACENTO}">${hl(c,q)}</span><span class="text-gray-600 truncate min-w-0">${hl(d,q)}</span>${item.um?`<span class="ml-auto text-gray-400 text-xs shrink-0">${xss(item.um)}</span>`:''}`;
    },
    onSelect: (item) => {
        document.getElementById('prod_preview_text').textContent = `${item.codigo} — ${item.descripcion || ''}`;
        document.getElementById('prod_preview').classList.remove('hidden');
        if (item.um) autoFillUM(item.um);
    },
});

// Unidad de Medida
crearTypeahead({
    inputId: 'um_search', dropdownId: 'um_dropdown', hiddenId: 'um_id',
    endpoint: '/api/v1/unidades-medida/buscar',
    renderItem: (item, q) => `<span class="font-mono font-bold text-xs shrink-0" style="color:${ACENTO}">${hl(item.codigo||'',q)}</span><span class="text-gray-500 text-xs truncate">${xss(item.label?.split('—')[1]?.trim()||'')}</span>`,
    onSelect: () => {},
});

/* Auto-rellenar UM desde producto */
async function autoFillUM(umCodigo) {
    try {
        const r    = await fetch(`/api/v1/unidades-medida/buscar?q=${encodeURIComponent(umCodigo)}`);
        const json = await r.json();
        const hit  = (json.data||[]).find(u => u.codigo === umCodigo);
        if (hit) {
            document.getElementById('um_id').value     = hit.id;
            document.getElementById('um_search').value = hit.label || hit.codigo;
        }
    } catch(e) {}
}

/* ─── Validación pre-submit ──────────────── */
document.getElementById('formSolicitud').addEventListener('submit', function(e) {
    const prodId      = document.getElementById('prod_id').value;
    const deptoId     = document.getElementById('depto_id').value;
    const deptoNombre = document.getElementById('depto_nombre').value;

    if (!prodId) {
        e.preventDefault();
        const el = document.getElementById('prod_search');
        el.classList.add('border-red-400','ring-2','ring-red-200');
        el.focus();
        setTimeout(() => el.classList.remove('border-red-400','ring-2','ring-red-200'), 2500);
        return;
    }
    if (!deptoId && !deptoNombre) {
        e.preventDefault();
        const el = document.getElementById('depto_search');
        el.classList.add('border-red-400','ring-2','ring-red-200');
        el.focus();
        setTimeout(() => el.classList.remove('border-red-400','ring-2','ring-red-200'), 2500);
        return;
    }
    const btn = document.getElementById('btnGuardar');
    btn.disabled = true;
    btn.innerHTML = `<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Guardando...`;
});

/* ─── Estado inline AJAX ──────────────────── */
const ESTADO_CLASES = {
    pendiente: 'bg-yellow-100 text-yellow-800',
    aprobada : 'bg-blue-100 text-blue-800',
    entregada: 'bg-green-100 text-green-800',
    cancelada: 'bg-red-100 text-red-800',
};
document.querySelectorAll('.estado-select').forEach(sel => {
    sel.addEventListener('change', async function() {
        const url    = this.dataset.url;
        const estado = this.value;
        const orig   = this.dataset.orig || this.value;
        try {
            const res = await fetch(url, {
                method : 'PATCH',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body   : JSON.stringify({ estado }),
            });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            this.className = `estado-select text-xs border-0 rounded-full px-2 py-0.5 font-semibold cursor-pointer ${ESTADO_CLASES[estado]||'bg-gray-100 text-gray-600'}`;
            this.style.appearance = 'none';
        } catch(err) {
            alert('Error al actualizar el estado.');
        }
    });
});

/* ─── Helpers ─────────────────────────────── */
function hl(text, q) {
    if (!text||!q) return xss(String(text||''));
    return xss(String(text)).replace(new RegExp(`(${escReg(q)})`, 'gi'), '<mark class="bg-yellow-200 px-0.5 rounded">$1</mark>');
}
function xss(s)     { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
function escReg(s)  { return s.replace(/[.*+?^${}()|[\]\\]/g,'\\$&'); }
</script>
@endsection
