@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Tarjetas resumen --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="rounded-xl p-5 text-white flex items-center gap-4 shadow" style="background-color:#4A568D;">
            <div class="bg-white bg-opacity-20 rounded-lg p-3">
                <svg width="25px" height="25px" viewBox="0 0 24 24" id="meteor-icon-kit__regular-inventory" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g clip-path="url(#clip0_525_147)">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M2 1C2 0.447715 1.55228 0 1 0C0.447715 0 0 0.447715 0 1V23C0 23.5523 0.447715 24 1 24C1.55228 24 2 23.5523 2 23V22H22V23C22 23.5523 22.4477 24 23 24C23.5523 24 24 23.5523 24 23V1C24 0.447715 23.5523 0 23 0C22.4477 0 22 0.447715 22 1V8H20V3C20 2.44772 19.5523 2 19 2H11C10.4477 2 10 2.44772 10 3V4H5C4.44772 4 4 4.44772 4 5V8H2V1ZM10 6H6V8H10V6ZM2 10V20H4V13C4 12.4477 4.44772 12 5 12H13C13.5523 12 14 12.4477 14 13V14H19C19.5523 14 20 14.4477 20 15V20H22V10H2ZM18 8V4H12V8H18ZM12 20H6V14H12V20ZM14 20V16H18V20H14Z" fill="#758CA3"/>
                    </g>
                    <defs>
                    <clipPath id="clip0_525_147">
                    <rect width="24" height="24" fill="white"/>
                    </clipPath>
                    </defs>
                </svg>
            </div>
            <div>
                <p class="text-xs opacity-80 font-medium uppercase tracking-wide">Total Productos</p>
                <p class="text-3xl font-bold">{{ number_format($totalProductos) }}</p>
            </div>
        </div>


        <div class="rounded-xl p-5 flex items-center gap-4 shadow bg-white border-l-4" style="border-color:#4A568D;">
            <div class="rounded-lg p-3" style="background-color:#eef0f8;">
                <svg class="w-7 h-7" style="color:#4A568D;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Movimientos Hoy</p>
                <p class="text-3xl font-bold text-gray-800">{{ number_format($totalMovimientos) }}</p>
            </div>
        </div>
    </div>

    {{-- Buscador dinámico --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-4 py-3 flex gap-3 items-center">
            <div class="flex-1 relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" id="searchInput"
                    placeholder="Buscar por código, descripción, ubicación, factura... (Esc para limpiar)"
                    class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition"
                    autocomplete="off">
            </div>
            <div id="searchStatus" class="flex items-center gap-1.5 text-sm text-gray-400 whitespace-nowrap hidden">
                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                Buscando...
            </div>
            <span id="searchCount" class="text-xs font-semibold px-2 py-1 rounded-full hidden" style="background-color:#eef0f8;color:#4A568D;"></span>
            <button id="clearBtn" onclick="clearSearch()" class="hidden flex items-center gap-1 px-3 py-1.5 bg-gray-100 text-gray-500 text-xs rounded-lg hover:bg-gray-200 transition font-medium">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Limpiar
            </button>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto" style="max-height: calc(100vh - 280px); overflow-y: auto;">
            <table class="w-full text-xs border-collapse" style="min-width:1800px;">
                <thead class="sticky top-0 z-10">
                    <tr style="background-color:#4A568D;">
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">CODIGO</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">COMP.</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">CAT.</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">FAM.</th>
                        <th class="px-3 py-2.5 text-center text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">CONS.</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide border-r border-indigo-600" style="min-width:200px;">DESCRIPCIÓN</th>
                        <th class="px-3 py-2.5 text-center text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">UM</th>
                        <th class="px-3 py-2.5 text-right text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">ENTRADA</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">UBIC.</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">F. ENTRADA</th>
                        <th class="px-3 py-2.5 text-right text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">SALIDA</th>
                        <th class="px-3 py-2.5 text-right text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">FISICO</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">F. SALIDA</th>
                        <th class="px-3 py-2.5 text-right text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">P.U</th>
                        <th class="px-3 py-2.5 text-center text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">MXN/USD</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">FACTURA</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide border-r border-indigo-600" style="min-width:180px;">DN/NP/OBSERVACIÓN</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">F. VENCIMIENTO</th>
                        <th class="px-3 py-2.5 text-center text-white font-semibold uppercase tracking-wide whitespace-nowrap">H. SEGURIDAD</th>
                    </tr>
                </thead>
                <tbody id="tableBody" class="divide-y divide-gray-100">
                    @forelse($productos as $p)
                    <tr class="hover:bg-blue-50 transition-colors {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}" data-server-row>
                        <td class="px-3 py-2 font-mono font-semibold whitespace-nowrap">
                            <a href="{{ route('productos.show', $p->id) }}" class="hover:underline" style="color:#4A568D;">{{ $p->codigo }}</a>
                        </td>
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $p->componente->codigo ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $p->categoria->codigo ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $p->familia->codigo ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-700 text-center">{{ $p->consecutivo ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-800">{{ $p->descripcion }}</td>
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $p->unidadMedida->codigo ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-800 text-right whitespace-nowrap">{{ $p->cantidad_entrada !== null ? number_format($p->cantidad_entrada, 2) : '—' }}</td>
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $p->ubicacion->codigo ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $p->fecha_entrada ? $p->fecha_entrada->format('d/m/Y') : '—' }}</td>
                        <td class="px-3 py-2 text-gray-800 text-right whitespace-nowrap">{{ $p->cantidad_salida !== null ? number_format($p->cantidad_salida, 2) : '—' }}</td>
                        <td class="px-3 py-2 text-right whitespace-nowrap font-semibold {{ ($p->cantidad_fisica !== null && $p->cantidad_fisica < 10) ? 'text-red-600' : 'text-gray-800' }}">
                            {{ $p->cantidad_fisica !== null ? number_format($p->cantidad_fisica, 2) : '—' }}
                        </td>
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $p->fecha_salida ? $p->fecha_salida->format('d/m/Y') : '—' }}</td>
                        <td class="px-3 py-2 text-gray-800 text-right whitespace-nowrap">{{ $p->precio_unitario !== null ? number_format($p->precio_unitario, 2) : '—' }}</td>
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $p->moneda ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $p->factura ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-600">{{ $p->observaciones ?? '—' }}</td>
                        <td class="px-3 py-2 whitespace-nowrap {{ ($p->fecha_vencimiento && $p->fecha_vencimiento->lte(now()->addDays(30))) ? 'text-red-600 font-semibold' : 'text-gray-700' }}">
                            {{ $p->fecha_vencimiento ? $p->fecha_vencimiento->format('d/m/Y') : '—' }}
                        </td>
                        <td class="px-3 py-2 text-center">
                            @if($p->hoja_seguridad)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Sí</span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr data-server-row>
                        <td colspan="19" class="px-6 py-12 text-center text-gray-400 text-sm">No hay productos registrados</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div id="paginationSection">
            @if($productos->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 flex items-center justify-between">
                <p class="text-xs text-gray-500">
                    Mostrando {{ $productos->firstItem() }}–{{ $productos->lastItem() }} de {{ number_format($productos->total()) }} productos
                </p>
                <div class="flex gap-1">
                    @if($productos->onFirstPage())
                        <span class="px-3 py-1 text-xs border border-gray-200 rounded text-gray-300 cursor-not-allowed">‹ Ant.</span>
                    @else
                        <a href="{{ $productos->previousPageUrl() }}" class="px-3 py-1 text-xs border border-gray-300 rounded text-gray-600 hover:bg-gray-50 transition">‹ Ant.</a>
                    @endif

                    @foreach($productos->getUrlRange(max(1, $productos->currentPage() - 2), min($productos->lastPage(), $productos->currentPage() + 2)) as $page => $url)
                        @if($page == $productos->currentPage())
                            <span class="px-3 py-1 text-xs border rounded text-white font-semibold" style="background-color:#4A568D;border-color:#4A568D;">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="px-3 py-1 text-xs border border-gray-300 rounded text-gray-600 hover:bg-gray-50 transition">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if($productos->hasMorePages())
                        <a href="{{ $productos->nextPageUrl() }}" class="px-3 py-1 text-xs border border-gray-300 rounded text-gray-600 hover:bg-gray-50 transition">Sig. ›</a>
                    @else
                        <span class="px-3 py-1 text-xs border border-gray-200 rounded text-gray-300 cursor-not-allowed">Sig. ›</span>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

</div>

<script>
const API_BASE = '/api/v1';
let debounceTimer = null;
let currentQuery = '';

const searchInput       = document.getElementById('searchInput');
const tableBody         = document.getElementById('tableBody');
const paginationSection = document.getElementById('paginationSection');
const searchStatus      = document.getElementById('searchStatus');
const searchCount       = document.getElementById('searchCount');
const clearBtn          = document.getElementById('clearBtn');

searchInput.addEventListener('input', function () {
    const q = this.value.trim();
    clearTimeout(debounceTimer);
    if (q === '') { clearSearch(); return; }
    debounceTimer = setTimeout(() => doSearch(q), 300);
});

searchInput.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') clearSearch();
});

async function doSearch(q) {
    currentQuery = q;
    searchStatus.classList.remove('hidden');
    searchCount.classList.add('hidden');
    clearBtn.classList.remove('hidden');
    paginationSection.classList.add('hidden');

    try {
        const res  = await fetch(`${API_BASE}/productos/buscar?q=${encodeURIComponent(q)}&limit=200`);
        const data = await res.json();

        if (searchInput.value.trim() !== q) return; // query cambió

        searchStatus.classList.add('hidden');
        searchCount.classList.remove('hidden');
        searchCount.textContent = `${data.total} resultado${data.total !== 1 ? 's' : ''}`;

        renderResults(data.data || [], q);
    } catch {
        searchStatus.classList.add('hidden');
        searchCount.classList.remove('hidden');
        searchCount.textContent = 'Error al buscar';
        searchCount.style.backgroundColor = '#fee2e2';
        searchCount.style.color = '#dc2626';
    }
}

function renderResults(productos, q) {
    document.querySelectorAll('[data-server-row]').forEach(r => r.classList.add('hidden'));
    document.querySelectorAll('[data-search-row]').forEach(r => r.remove());

    if (productos.length === 0) {
        const tr = document.createElement('tr');
        tr.setAttribute('data-search-row', '');
        tr.innerHTML = `<td colspan="19" class="px-6 py-12 text-center text-gray-400 text-sm">
            Sin resultados para "<strong>${escape(q)}</strong>"
        </td>`;
        tableBody.appendChild(tr);
        return;
    }

    productos.forEach((p, i) => {
        const fisico    = p.fisico != null ? parseFloat(p.fisico) : null;
        const fisicoStr = fisico !== null ? fisico.toLocaleString('es-MX', {minimumFractionDigits: 2}) : '—';
        const puStr     = p.pu != null    ? parseFloat(p.pu).toLocaleString('es-MX', {minimumFractionDigits: 2}) : '—';
        const fisicoClass = (fisico !== null && fisico < 10) ? 'text-red-600 font-semibold' : 'text-gray-800';

        const tr = document.createElement('tr');
        tr.setAttribute('data-search-row', '');
        tr.className = (i % 2 === 0 ? 'bg-white' : 'bg-gray-50') + ' hover:bg-blue-50 transition-colors';
        tr.innerHTML = `
            <td class="px-3 py-2 font-mono font-semibold whitespace-nowrap">
                <a href="/productos/${p.id}" class="hover:underline" style="color:#4A568D;">${hl(p.codigo, q)}</a>
            </td>
            <td class="px-3 py-2 text-gray-400 whitespace-nowrap">—</td>
            <td class="px-3 py-2 text-gray-400 whitespace-nowrap">—</td>
            <td class="px-3 py-2 text-gray-400 whitespace-nowrap">—</td>
            <td class="px-3 py-2 text-gray-400 text-center">—</td>
            <td class="px-3 py-2 text-gray-800">${hl(p.descripcion || '', q)}</td>
            <td class="px-3 py-2 text-gray-700 whitespace-nowrap">${p.um || '—'}</td>
            <td class="px-3 py-2 text-gray-400 text-right whitespace-nowrap">—</td>
            <td class="px-3 py-2 text-gray-700 whitespace-nowrap">${hl(p.ubicacion || '—', q)}</td>
            <td class="px-3 py-2 text-gray-400 whitespace-nowrap">—</td>
            <td class="px-3 py-2 text-gray-400 text-right whitespace-nowrap">—</td>
            <td class="px-3 py-2 text-right whitespace-nowrap ${fisicoClass}">${fisicoStr}</td>
            <td class="px-3 py-2 text-gray-400 whitespace-nowrap">—</td>
            <td class="px-3 py-2 text-gray-800 text-right whitespace-nowrap">${puStr}</td>
            <td class="px-3 py-2 text-gray-400 whitespace-nowrap">—</td>
            <td class="px-3 py-2 text-gray-400 whitespace-nowrap">—</td>
            <td class="px-3 py-2 text-gray-400">—</td>
            <td class="px-3 py-2 text-gray-400 whitespace-nowrap">—</td>
            <td class="px-3 py-2 text-center text-gray-400">—</td>
        `;
        tableBody.appendChild(tr);
    });
}

function clearSearch() {
    searchInput.value = '';
    currentQuery = '';
    clearTimeout(debounceTimer);
    document.querySelectorAll('[data-server-row]').forEach(r => r.classList.remove('hidden'));
    document.querySelectorAll('[data-search-row]').forEach(r => r.remove());
    paginationSection.classList.remove('hidden');
    searchStatus.classList.add('hidden');
    searchCount.classList.add('hidden');
    searchCount.style.backgroundColor = '#eef0f8';
    searchCount.style.color = '#4A568D';
    clearBtn.classList.add('hidden');
    searchInput.focus();
}

function hl(text, q) {
    if (!text || !q) return xss(text || '');
    return xss(text).replace(new RegExp(`(${xssReg(q)})`, 'gi'), '<mark class="bg-yellow-200 px-0.5 rounded">$1</mark>');
}
function xss(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function xssReg(s) { return s.replace(/[.*+?^${}()|[\]\\]/g,'\\$&'); }
function escape(s) { return xss(s); }
</script>
@endsection

