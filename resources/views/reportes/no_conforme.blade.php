@extends('layouts.app')
@section('title', 'No Conforme')
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

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-800">No Conforme</h1>
            <p class="text-xs text-gray-500 mt-0.5">Productos con incidencia registrada &middot; {{ number_format($registros->total()) }} elementos</p>
        </div>
        <button onclick="abrirModalNC()"
            class="flex items-center gap-2 px-4 py-2.5 text-white text-sm font-semibold rounded-xl shadow transition hover:opacity-90 active:scale-95"
            style="background-color:{{ $acento }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            Registrar Incidencia
        </button>
    </div>

    {{-- Buscador --}}
    <form method="GET" action="{{ route('reportes.no_conforme') }}" class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-4 py-3 flex gap-3 items-center">
            <div class="flex-1 relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Buscar por codigo, descripcion, factura, estatus/observacion..."
                    class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 transition"
                    autocomplete="off">
            </div>
            <button type="submit" class="px-4 py-2 text-white text-sm font-medium rounded-lg transition hover:opacity-90" style="background-color:{{ $acento }}">Buscar</button>
            @if(request('search'))
            <a href="{{ route('reportes.no_conforme') }}" class="px-3 py-2 bg-gray-100 text-gray-600 text-sm rounded-lg hover:bg-gray-200 transition">&times; Limpiar</a>
            @endif
        </div>
    </form>

    {{-- Tabla --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto" style="max-height:calc(100vh - 280px);overflow-y:auto;">
            <table class="w-full text-xs border-collapse" style="min-width:2000px;">
                <thead class="sticky top-0 z-10">
                    <tr style="background-color:{{ $acento }};">
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">CODIGO</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">COMP.</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">CAT.</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">FAM.</th>
                        <th class="px-3 py-2.5 text-center text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">CONS.</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide border-r border-indigo-600" style="min-width:200px;">DESCRIPCION</th>
                        <th class="px-3 py-2.5 text-center text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">UM</th>
                        <th class="px-3 py-2.5 text-right text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">ENTRADA</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">UBIC.</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">F. ENTRADA</th>
                        <th class="px-3 py-2.5 text-right text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">SALIDA</th>
                        <th class="px-3 py-2.5 text-right text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">FISICO</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">FECHA ACTUAL</th>
                        <th class="px-3 py-2.5 text-right text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">P.U</th>
                        <th class="px-3 py-2.5 text-center text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">MXN/USD</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">FACTURA</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide border-r border-indigo-600" style="min-width:220px;">ESTATUS / OBSERVACION</th>
                        <th class="px-3 py-2.5 text-center text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">H. SEGURIDAD</th>
                        <th class="px-3 py-2.5 text-right text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">DIAS TRANSCURRIDOS</th>
                        <th class="px-3 py-2.5 text-center text-white font-semibold uppercase tracking-wide whitespace-nowrap">ACCION</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" id="ncTableBody">
                    @forelse($registros as $p)
                    @php
                        $refDate = $p->fecha_nc ?? $p->fecha_entrada;
                        $dias    = $refDate ? (int) $refDate->diffInDays(now()) : null;
                        $diasClass = $dias === null ? 'text-gray-400'
                            : ($dias > 365 ? 'text-red-600 font-bold'
                            : ($dias > 180 ? 'text-orange-600 font-semibold'
                            : ($dias > 90  ? 'text-yellow-600 font-semibold'
                            : 'text-gray-700')));
                    @endphp
                    <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }} hover:bg-red-50 transition-colors" data-row-id="{{ $p->id }}">
                        <td class="px-3 py-2 font-mono font-semibold whitespace-nowrap">
                            <a href="{{ route('productos.show', $p->id) }}" class="hover:underline" style="color:{{ $acento }}">{{ $p->codigo }}</a>
                        </td>
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $p->componente->codigo ?? '&mdash;' }}</td>
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $p->categoria->codigo ?? '&mdash;' }}</td>
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $p->familia->codigo ?? '&mdash;' }}</td>
                        <td class="px-3 py-2 text-gray-700 text-center">{{ $p->consecutivo ?? '&mdash;' }}</td>
                        <td class="px-3 py-2 text-gray-800">{{ $p->descripcion }}</td>
                        <td class="px-3 py-2 text-gray-700 text-center whitespace-nowrap">{{ $p->unidadMedida->codigo ?? '&mdash;' }}</td>
                        <td class="px-3 py-2 text-gray-800 text-right whitespace-nowrap">{{ $p->cantidad_entrada !== null ? number_format($p->cantidad_entrada, 2) : '&mdash;' }}</td>
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $p->ubicacion->codigo ?? '&mdash;' }}</td>
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $p->fecha_entrada ? $p->fecha_entrada->format('d/m/Y') : '&mdash;' }}</td>
                        <td class="px-3 py-2 text-gray-800 text-right whitespace-nowrap">{{ $p->cantidad_salida !== null ? number_format($p->cantidad_salida, 2) : '&mdash;' }}</td>
                        <td class="px-3 py-2 text-right whitespace-nowrap font-semibold {{ ($p->cantidad_fisica !== null && $p->cantidad_fisica < 10) ? 'text-red-600' : 'text-gray-800' }}">
                            {{ $p->cantidad_fisica !== null ? number_format($p->cantidad_fisica, 2) : '&mdash;' }}
                        </td>
                        <td class="px-3 py-2 text-gray-600 whitespace-nowrap">{{ now()->format('d/m/Y') }}</td>
                        <td class="px-3 py-2 text-gray-800 text-right whitespace-nowrap">{{ $p->precio_unitario !== null ? number_format($p->precio_unitario, 2) : '&mdash;' }}</td>
                        <td class="px-3 py-2 text-gray-700 text-center whitespace-nowrap">{{ $p->moneda ?? '&mdash;' }}</td>
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $p->factura ?? '&mdash;' }}</td>
                        {{-- ESTATUS / OBSERVACION (editable inline) --}}
                        <td class="px-3 py-2" style="min-width:220px;">
                            <div class="group relative">
                                <span class="observacion-text text-gray-700 block truncate max-w-xs cursor-pointer hover:text-indigo-700 transition"
                                      data-id="{{ $p->id }}"
                                      title="{{ $p->observacion_nc ?: 'Click para editar' }}"
                                      onclick="editarObservacion(this)">
                                    {{ $p->observacion_nc ?: '(sin estatus - click para agregar)' }}
                                </span>
                                <div class="observacion-edit hidden flex gap-1.5 items-start mt-0.5">
                                    <textarea data-id="{{ $p->id }}"
                                        class="observacion-input flex-1 border border-indigo-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-indigo-400 resize-none"
                                        rows="2">{{ $p->observacion_nc }}</textarea>
                                    <div class="flex flex-col gap-1">
                                        <button onclick="guardarObservacion(this)" class="px-2 py-1 text-white text-xs rounded font-medium hover:opacity-90" style="background-color:{{ $acento }}">OK</button>
                                        <button onclick="cancelarObservacion(this)" class="px-2 py-1 bg-gray-200 text-gray-600 text-xs rounded hover:bg-gray-300">&times;</button>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-3 py-2 text-center">
                            @if($p->hoja_seguridad)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">S&iacute;</span>
                            @else
                                <span class="text-gray-400">&mdash;</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-right whitespace-nowrap {{ $diasClass }}">
                            {{ $dias !== null ? number_format($dias) . ' dias' : '&mdash;' }}
                            @if($p->fecha_nc)
                            <div class="text-gray-400 font-normal text-xs">desde {{ $p->fecha_nc->format('d/m/Y') }}</div>
                            @endif
                        </td>
                        {{-- Quitar de No Conforme --}}
                        <td class="px-3 py-2 text-center">
                            <button
                                class="btn-resolver inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-lg border border-green-300 text-green-700 hover:bg-green-50 transition"
                                data-id="{{ $p->id }}"
                                data-url="{{ route('productos.no_conforme', $p->id) }}"
                                title="Marcar como resuelto - quitar de No Conforme">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Resolver
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr id="emptyRow">
                        <td colspan="20" class="px-6 py-16 text-center text-gray-400">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-12 h-12 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="font-medium text-green-600">Sin incidencias registradas</span>
                                <button type="button" onclick="abrirModalNC()" class="text-sm font-medium hover:underline mt-1" style="color:{{ $acento }}">Registrar primera incidencia &rarr;</button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @include('reportes._paginacion', ['items' => $registros, 'acento' => $acento])
    </div>

</div>

{{-- ============================================
     MODAL: Registrar Incidencia
============================================ --}}
<div id="modalNC" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background:rgba(0,0,0,0.45);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">

        <div class="flex items-center justify-between px-6 py-4 rounded-t-2xl text-white" style="background-color:{{ $acento }};">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span class="font-semibold">Registrar Incidencia (No Conforme)</span>
            </div>
            <button onclick="cerrarModalNC()" class="text-white opacity-70 hover:opacity-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="px-6 py-5 space-y-4">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Producto <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="text" id="nc_prod_search"
                        placeholder="Buscar por codigo o descripcion..."
                        autocomplete="off"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    <input type="hidden" id="nc_prod_id">
                    <div id="nc_prod_dropdown" class="hidden absolute left-0 right-0 top-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg z-50 max-h-52 overflow-y-auto"></div>
                </div>
                <div id="nc_prod_preview" class="hidden mt-2 flex items-center gap-2 px-3 py-2 rounded-lg text-xs border" style="background:{{ $claro }};border-color:#c7cfe7;">
                    <svg class="w-3.5 h-3.5 shrink-0" style="color:{{ $acento }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span id="nc_prod_preview_text" class="font-medium" style="color:{{ $acento }}"></span>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Estatus / Observacion de la incidencia <span class="text-red-500">*</span></label>
                <textarea id="nc_observacion" rows="3"
                    placeholder="Describe el motivo o tipo de incidencia..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 resize-none"></textarea>
            </div>
        </div>

        <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3 rounded-b-2xl bg-gray-50">
            <button type="button" onclick="cerrarModalNC()"
                class="px-5 py-2 bg-white border border-gray-300 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-50 transition">
                Cancelar
            </button>
            <button type="button" id="btnGuardarNC" onclick="guardarNC()"
                class="px-6 py-2 text-white text-sm font-semibold rounded-xl transition hover:opacity-90 flex items-center gap-2"
                style="background-color:{{ $acento }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                Registrar Incidencia
            </button>
        </div>
    </div>
</div>

<script>
const ACENTO = '{{ $acento }}';
const CSRF   = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

/* --- Modal NC --- */
function abrirModalNC() {
    const m = document.getElementById('modalNC');
    m.classList.remove('hidden');
    m.classList.add('flex');
    document.body.style.overflow = 'hidden';
    document.getElementById('nc_prod_id').value = '';
    document.getElementById('nc_prod_search').value = '';
    document.getElementById('nc_observacion').value = '';
    document.getElementById('nc_prod_preview').classList.add('hidden');
    setTimeout(() => document.getElementById('nc_prod_search').focus(), 100);
}
function cerrarModalNC() {
    document.getElementById('modalNC').classList.replace('flex','hidden');
    document.body.style.overflow = '';
}
document.getElementById('modalNC').addEventListener('click', e => { if (e.target === document.getElementById('modalNC')) cerrarModalNC(); });
document.addEventListener('keydown', e => { if (e.key === 'Escape') cerrarModalNC(); });

/* --- Typeahead producto (modal NC) --- */
(function() {
    const input    = document.getElementById('nc_prod_search');
    const dropdown = document.getElementById('nc_prod_dropdown');
    const hiddenId = document.getElementById('nc_prod_id');
    let timer = null;

    input.addEventListener('input', function() {
        clearTimeout(timer);
        const q = this.value.trim();
        hiddenId.value = '';
        if (!q) { dropdown.classList.add('hidden'); return; }
        timer = setTimeout(() => buscar(q), 230);
    });
    input.addEventListener('blur', () => setTimeout(() => dropdown.classList.add('hidden'), 200));

    async function buscar(q) {
        const r    = await fetch(`/api/v1/productos/buscar?q=${encodeURIComponent(q)}&limit=30`);
        const json = await r.json();
        dropdown.innerHTML = '';
        (json.data || []).forEach(item => {
            const div = document.createElement('div');
            div.className = 'px-3 py-2 hover:bg-indigo-50 cursor-pointer flex items-center gap-2 text-sm';
            div.innerHTML = `<span class="font-mono font-bold text-xs shrink-0" style="color:${ACENTO}">${xss(item.codigo||'')}</span> <span class="text-gray-600 truncate">${xss(item.descripcion||'')}</span>${item.um?`<span class="ml-auto text-gray-400 text-xs shrink-0">${xss(item.um)}</span>`:''}`;
            div.addEventListener('mousedown', e => {
                e.preventDefault();
                hiddenId.value = item.id;
                input.value    = `${item.codigo} - ${item.descripcion || ''}`;
                dropdown.classList.add('hidden');
                document.getElementById('nc_prod_preview_text').textContent = `${item.codigo} - ${item.descripcion || ''}`;
                document.getElementById('nc_prod_preview').classList.remove('hidden');
            });
            dropdown.appendChild(div);
        });
        if (dropdown.children.length) dropdown.classList.remove('hidden');
        else dropdown.classList.add('hidden');
    }
})();

/* --- Guardar nueva incidencia --- */
async function guardarNC() {
    const prodId    = document.getElementById('nc_prod_id').value;
    const observ    = document.getElementById('nc_observacion').value.trim();
    const btn       = document.getElementById('btnGuardarNC');

    if (!prodId) {
        document.getElementById('nc_prod_search').classList.add('border-red-400','ring-2','ring-red-200');
        document.getElementById('nc_prod_search').focus();
        setTimeout(() => document.getElementById('nc_prod_search').classList.remove('border-red-400','ring-2','ring-red-200'), 2500);
        return;
    }
    if (!observ) {
        document.getElementById('nc_observacion').classList.add('border-red-400','ring-2','ring-red-200');
        document.getElementById('nc_observacion').focus();
        setTimeout(() => document.getElementById('nc_observacion').classList.remove('border-red-400','ring-2','ring-red-200'), 2500);
        return;
    }

    btn.disabled = true;
    btn.textContent = 'Guardando...';

    try {
        const res = await fetch(`/productos/${prodId}/no-conforme`, {
            method : 'PATCH',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body   : JSON.stringify({ no_conforme: true, observacion_nc: observ }),
        });
        if (!res.ok) throw new Error('HTTP ' + res.status);
        cerrarModalNC();
        location.reload();
    } catch(err) {
        alert('Error al registrar la incidencia.');
        btn.disabled = false;
        btn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg> Registrar Incidencia`;
    }
}

/* --- Resolver (quitar de NC) --- */
document.querySelectorAll('.btn-resolver').forEach(btn => {
    btn.addEventListener('click', async function() {
        if (!confirm('Marcar como resuelto? El producto se retirara de la lista de No Conformes.')) return;
        const url = this.dataset.url;
        const row = this.closest('tr');
        this.disabled = true;
        this.textContent = '...';
        try {
            const res = await fetch(url, {
                method : 'PATCH',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body   : JSON.stringify({ no_conforme: false }),
            });
            if (!res.ok) throw new Error();
            // Animar y eliminar fila
            row.style.transition = 'opacity 0.4s, transform 0.4s';
            row.style.opacity    = '0';
            row.style.transform  = 'translateX(30px)';
            setTimeout(() => row.remove(), 420);
        } catch(e) {
            alert('Error al actualizar. Recarga la pagina.');
            this.disabled = false;
            this.textContent = 'Resolver';
        }
    });
});

/* --- Edicion inline de observacion_nc --- */
function editarObservacion(span) {
    const container = span.closest('.group');
    span.classList.add('hidden');
    container.querySelector('.observacion-edit').classList.remove('hidden');
    container.querySelector('.observacion-input').focus();
}
function cancelarObservacion(btn) {
    const container = btn.closest('.group');
    container.querySelector('.observacion-edit').classList.add('hidden');
    container.querySelector('.observacion-text').classList.remove('hidden');
}
async function guardarObservacion(btn) {
    const container = btn.closest('.group');
    const textarea  = container.querySelector('.observacion-input');
    const span      = container.querySelector('.observacion-text');
    const prodId    = textarea.dataset.id;
    const valor     = textarea.value.trim();

    btn.disabled = true;
    btn.textContent = '...';

    try {
        const res = await fetch(`/productos/${prodId}/no-conforme`, {
            method : 'PATCH',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body   : JSON.stringify({ no_conforme: true, observacion_nc: valor }),
        });
        if (!res.ok) throw new Error();
        span.textContent = valor || '(sin estatus - click para agregar)';
        span.title       = valor || 'Click para editar';
        container.querySelector('.observacion-edit').classList.add('hidden');
        span.classList.remove('hidden');
    } catch(e) {
        alert('Error al guardar. Reintentar.');
    } finally {
        btn.disabled = false;
        btn.textContent = 'OK';
    }
}

/* --- Helpers --- */
function xss(s) { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
</script>
@endsection
