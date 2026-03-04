@extends('layouts.app')
@section('title', 'Inventario General')
@section('content')
@php $acento = '#4A568D'; @endphp

<div class="space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Inventario General</h1>
            <p class="text-xs text-gray-500 mt-0.5">{{ number_format($registros->total()) }} productos</p>
        </div>
        <div class="flex items-center gap-4">
            <button onclick="abrirModalRequisicion()" 
                class="flex items-center gap-2 px-4 py-2.5 bg-purple-600 text-white text-sm font-semibold rounded-xl shadow transition hover:bg-purple-700 active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Nueva Requisición
            </button>
            {{-- Totales --}}
            <div class="flex gap-4">
                <div class="text-right">
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Total F&iacute;sico</p>
                    <p class="text-lg font-bold text-gray-800">{{ number_format($totales['sum_fisico'], 2) }}</p>
                </div>
                <div class="text-right border-l border-gray-200 pl-4">
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Total P.U</p>
                    <p class="text-lg font-bold" style="color:{{ $acento }}">$ {{ number_format($totales['sum_pu'], 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <form method="GET" action="{{ route('reportes.inventario_general') }}" class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-4 py-3 flex gap-3 items-center">
            <div class="flex-1 relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Buscar por c&oacute;digo o descripci&oacute;n..."
                    class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 transition"
                    autocomplete="off">
            </div>
            <button type="submit" class="px-4 py-2 text-white text-sm font-medium rounded-lg transition" style="background-color:{{ $acento }}">Buscar</button>
            @if(request('search'))
            <a href="{{ route('reportes.inventario_general') }}" class="px-3 py-2 bg-gray-100 text-gray-600 text-sm rounded-lg hover:bg-gray-200 transition">✕</a>
            @endif
        </div>
    </form>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto" style="max-height:calc(100vh - 260px);overflow-y:auto;">
            <table class="w-full text-xs border-collapse" style="min-width:700px;">
                <thead class="sticky top-0 z-10">
                    <tr style="background-color:{{ $acento }};">
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">CODIGO</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide border-r border-indigo-600" style="min-width:220px;">DESCRIPCI&Oacute;N</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">UBIC.</th>
                        <th class="px-3 py-2.5 text-center text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">UM</th>
                        <th class="px-3 py-2.5 text-right text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">SUM DE FISICO</th>
                        <th class="px-3 py-2.5 text-right text-white font-semibold uppercase tracking-wide whitespace-nowrap">SUM DE P.U</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($registros as $p)
                    <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }} hover:bg-blue-50 transition-colors">
                        <td class="px-3 py-2 font-mono font-semibold whitespace-nowrap">
                            <a href="{{ route('productos.show', $p->id) }}" class="hover:underline" style="color:{{ $acento }}">{{ $p->codigo }}</a>
                        </td>
                        <td class="px-3 py-2 text-gray-800">{{ $p->descripcion }}</td>
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $p->ubicacion->codigo ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-700 text-center whitespace-nowrap">{{ $p->unidadMedida->codigo ?? '—' }}</td>
                        <td class="px-3 py-2 text-right whitespace-nowrap font-semibold {{ ($p->sum_fisico !== null && $p->sum_fisico < 10) ? 'text-red-600' : 'text-gray-800' }}">
                            {{ $p->sum_fisico !== null ? number_format($p->sum_fisico, 2) : '—' }}
                        </td>
                        <td class="px-3 py-2 text-right whitespace-nowrap font-semibold text-gray-800">
                            {{ $p->sum_pu !== null ? '$ ' . number_format($p->sum_pu, 2) : '—' }}
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-6 py-12 text-center text-gray-400">No hay productos</td></tr>
                    @endforelse
                </tbody>
                {{-- Pie con totales de la p&aacute;gina actual --}}
                <tfoot>
                    <tr style="background-color:#f1f3fb;">
                        <td colspan="4" class="px-3 py-2 text-right text-xs font-semibold text-gray-600 uppercase tracking-wide">Totales globales →</td>
                        <td class="px-3 py-2 text-right text-xs font-bold text-gray-800">{{ number_format($totales['sum_fisico'], 2) }}</td>
                        <td class="px-3 py-2 text-right text-xs font-bold text-gray-800">$ {{ number_format($totales['sum_pu'], 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @include('reportes._paginacion', ['items' => $registros, 'acento' => $acento])
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════════════
     MODAL: Nueva Requisición
═══════════════════════════════════════════════════════════════════════════════ --}}
<div id="modalRequisicion" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background:rgba(0,0,0,0.45);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[92vh] flex flex-col">
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 rounded-t-2xl text-white bg-purple-600">
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
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Fecha <span class="text-red-500">*</span></label>
                    <input type="date" name="fecha" value="{{ now()->format('Y-m-d') }}" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400">
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

            {{-- Fila 2: Solicitante / Estado / Prioridad --}}
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Solicitante <span class="text-red-500">*</span></label>
                    <input type="text" name="solicitante" value="{{ auth()->user()->name }}" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Estado <span class="text-red-500">*</span></label>
                    <select name="estado" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400">
                        <option value="pendiente" selected>Pendiente</option>
                        <option value="aprobada">Aprobada</option>
                        <option value="entregada">Entregada</option>
                        <option value="cancelada">Cancelada</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Prioridad</label>
                    <select name="prioridad" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400">
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
                    <input type="text" id="req_depto_search" placeholder="Escribe para buscar o crear departamento..." autocomplete="off"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400">
                    <input type="hidden" name="departamento_id" id="req_depto_id">
                    <input type="hidden" name="departamento_nombre" id="req_depto_nombre">
                    <div id="req_depto_dropdown" class="hidden absolute left-0 right-0 top-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg z-50 max-h-44 overflow-y-auto"></div>
                </div>
                <p class="text-xs text-gray-400 mt-1">Si no existe, escríbelo y selecciona "Crear nuevo" — se creará automáticamente</p>
            </div>

            {{-- Producto typeahead --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Producto <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="text" id="req_prod_search" placeholder="Buscar por código o descripción..." autocomplete="off"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400">
                    <input type="hidden" name="producto_id" id="req_prod_id">
                    <div id="req_prod_dropdown" class="hidden absolute left-0 right-0 top-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg z-50 max-h-52 overflow-y-auto"></div>
                </div>
                <div id="req_prod_preview" class="hidden mt-2 flex items-center gap-2 px-3 py-2 bg-purple-50 border border-purple-200 rounded-lg text-xs">
                    <svg class="w-3.5 h-3.5 shrink-0 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span id="req_prod_preview_text" class="font-medium text-purple-800"></span>
                </div>
            </div>

            {{-- Cantidad + UM --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Cantidad <span class="text-red-500">*</span></label>
                    <input type="number" name="cantidad" min="0.01" step="any" placeholder="0" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Unidad de Medida</label>
                    <div class="relative">
                        <input type="text" id="req_um_search" placeholder="PZA, KG, MT..." autocomplete="off"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400">
                        <input type="hidden" name="unidad_medida_id" id="req_um_id">
                        <div id="req_um_dropdown" class="hidden absolute left-0 right-0 top-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg z-50 max-h-44 overflow-y-auto"></div>
                    </div>
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
                    class="px-6 py-2 bg-purple-600 text-white text-sm font-semibold rounded-xl transition hover:bg-purple-700 active:scale-95 flex items-center gap-2">
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
   MODAL REQUISICIÓN
═══════════════════════════════════════════════════════════════════ */
const CSRF_REQ = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

function abrirModalRequisicion() {
    const m = document.getElementById('modalRequisicion');
    m.classList.remove('hidden');
    m.classList.add('flex');
    document.body.style.overflow = 'hidden';
    document.getElementById('formRequisicion').reset();
    ['req_depto_id', 'req_depto_nombre', 'req_prod_id', 'req_um_id'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('req_prod_preview').classList.add('hidden');
    document.getElementById('deptoNuevoTag').classList.add('hidden');
    const btn = document.getElementById('btnGuardarReq');
    btn.disabled = false;
    btn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Guardar Solicitud`;
    setTimeout(() => document.getElementById('req_depto_search')?.focus(), 120);
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

/* ─── Typeahead genérico ─────────────────── */
function crearTypeaheadReq({ inputId, dropdownId, hiddenId, endpoint, renderItem, onSelect, allowCreate = false }) {
    const input = document.getElementById(inputId);
    const dropdown = document.getElementById(dropdownId);
    const hidden = document.getElementById(hiddenId);
    let timer = null;

    input?.addEventListener('input', function () {
        clearTimeout(timer);
        const q = this.value.trim();
        hidden.value = '';
        if (!q) {
            ocultarDropdown();
            return;
        }
        timer = setTimeout(() => buscar(q), 230);
    });

    input?.addEventListener('focus', function () {
        if (this.value.trim()) buscar(this.value.trim());
    });

    input?.addEventListener('blur', () => setTimeout(ocultarDropdown, 200));

    function ocultarDropdown() {
        dropdown.classList.add('hidden');
    }

    async function buscar(q) {
        try {
            const r = await fetch(`${endpoint}?q=${encodeURIComponent(q)}`);
            const json = await r.json();
            mostrarResultados(json.data || [], q);
        } catch (e) {
            console.error(e);
        }
    }

    function mostrarResultados(items, q) {
        dropdown.innerHTML = '';

        items.forEach(item => {
            const div = document.createElement('div');
            div.className = 'px-3 py-2 text-sm text-gray-700 hover:bg-purple-50 cursor-pointer flex items-center gap-2';
            div.innerHTML = renderItem(item, q);
            div.addEventListener('mousedown', e => {
                e.preventDefault();
                hidden.value = item.id;
                input.value = item.label || item.codigo || item.nombre || '';
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
            div.innerHTML = `<svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Crear departamento "<strong>${xssReq(q2)}</strong>"`;
            div.addEventListener('mousedown', e => {
                e.preventDefault();
                hidden.value = '';
                document.getElementById('req_depto_nombre').value = q2;
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

/* ─── Instancias de typeahead para requisición ────────────── */
// Departamento
crearTypeaheadReq({
    inputId: 'req_depto_search',
    dropdownId: 'req_depto_dropdown',
    hiddenId: 'req_depto_id',
    endpoint: '/api/v1/departamentos/buscar',
    allowCreate: true,
    renderItem: (item, q) =>
        `<svg class="w-3.5 h-3.5 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>${hlReq(item.label, q)}`,
    onSelect: item => {
        if (!item.isNew) {
            document.getElementById('req_depto_nombre').value = item.label;
            document.getElementById('deptoNuevoTag').classList.add('hidden');
        }
    }
});

// Producto
crearTypeaheadReq({
    inputId: 'req_prod_search',
    dropdownId: 'req_prod_dropdown',
    hiddenId: 'req_prod_id',
    endpoint: '/api/v1/productos/buscar',
    renderItem: (item, q) => {
        const c = item.codigo || '',
            d = item.descripcion || '';
        return `<span class="font-mono font-bold text-xs shrink-0 text-purple-700">${hlReq(c, q)}</span><span class="text-gray-600 truncate min-w-0">${hlReq(d, q)}</span>${item.um ? `<span class="ml-auto text-gray-400 text-xs shrink-0">${xssReq(item.um)}</span>` : ''}`;
    },
    onSelect: item => {
        document.getElementById('req_prod_preview_text').textContent = `${item.codigo} — ${item.descripcion || ''}`;
        document.getElementById('req_prod_preview').classList.remove('hidden');
        if (item.um) autoFillUMReq(item.um);
    }
});

// Unidad de Medida
crearTypeaheadReq({
    inputId: 'req_um_search',
    dropdownId: 'req_um_dropdown',
    hiddenId: 'req_um_id',
    endpoint: '/api/v1/unidades-medida/buscar',
    renderItem: (item, q) =>
        `<span class="font-mono font-bold text-xs shrink-0 text-purple-700">${hlReq(item.codigo || '', q)}</span><span class="text-gray-500 text-xs truncate">${xssReq(item.label?.split('—')[1]?.trim() || '')}</span>`,
    onSelect: () => {}
});

/* Auto-rellenar UM desde producto */
async function autoFillUMReq(umCodigo) {
    try {
        const r = await fetch(`/api/v1/unidades-medida/buscar?q=${encodeURIComponent(umCodigo)}`);
        const json = await r.json();
        const hit = (json.data || []).find(u => u.codigo === umCodigo);
        if (hit) {
            document.getElementById('req_um_id').value = hit.id;
            document.getElementById('req_um_search').value = hit.label || hit.codigo;
        }
    } catch (e) {}
}

/* ─── Validación pre-submit ──────────────── */
document.getElementById('formRequisicion')?.addEventListener('submit', function (e) {
    const prodId = document.getElementById('req_prod_id').value;
    const deptoId = document.getElementById('req_depto_id').value;
    const deptoNombre = document.getElementById('req_depto_nombre').value;

    if (!prodId) {
        e.preventDefault();
        const el = document.getElementById('req_prod_search');
        el.classList.add('border-red-400', 'ring-2', 'ring-red-200');
        el.focus();
        setTimeout(() => el.classList.remove('border-red-400', 'ring-2', 'ring-red-200'), 2500);
        return;
    }
    if (!deptoId && !deptoNombre) {
        e.preventDefault();
        const el = document.getElementById('req_depto_search');
        el.classList.add('border-red-400', 'ring-2', 'ring-red-200');
        el.focus();
        setTimeout(() => el.classList.remove('border-red-400', 'ring-2', 'ring-red-200'), 2500);
        return;
    }
    const btn = document.getElementById('btnGuardarReq');
    btn.disabled = true;
    btn.innerHTML = `<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Guardando...`;
});

/* ─── Helpers ─────────────────────────────── */
function hlReq(text, q) {
    if (!text || !q) return xssReq(String(text || ''));
    return xssReq(String(text)).replace(new RegExp(`(${escRegReq(q)})`, 'gi'), '<mark class="bg-yellow-200 px-0.5 rounded">$1</mark>');
}
function xssReq(s) {
    return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}
function escRegReq(s) {
    return s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}
</script>

@endsection
