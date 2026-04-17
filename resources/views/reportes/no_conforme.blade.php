@extends('layouts.app')
@section('title', 'No Conforme')
@section('content')
@php
    $acento = '#4A568D';
    $claro  = '#eef0f8';
    $esVisitante = auth()->user()->hasRole('visitante');
    $estatusBadge = [
        'abierta'    => 'bg-red-100 text-red-700 border-red-200',
        'en_proceso' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
        'resuelta'   => 'bg-green-100 text-green-700 border-green-200',
        'cerrada'    => 'bg-gray-100 text-gray-600 border-gray-200',
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

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-800">No Conforme</h1>
            <p class="text-xs text-gray-500 mt-0.5">Productos con incidencia registrada &mdash; <b>{{ number_format($registros->total()) }}</b> elementos</p>
        </div>
        @if(!$esVisitante)
        <button onclick="abrirModalNC()"
            class="flex items-center gap-2 px-4 py-2.5 text-white text-sm font-semibold rounded-xl shadow transition hover:opacity-90 active:scale-95"
            style="background-color:{{ $acento }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            Registrar Incidencia
        </button>
        @endif
    </div>

    {{-- Buscador + filtro estatus --}}
    <form method="GET" action="{{ route('reportes.no_conforme') }}" class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-4 py-3 flex gap-3 items-center flex-wrap">
            <div class="flex-1 relative min-w-[200px]">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Buscar por código, descripción, resolución..."
                    class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 transition"
                    autocomplete="off">
            </div>
            <select name="estatus" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="">Todos los estatus</option>
                <option value="abierta" {{ request('estatus') === 'abierta' ? 'selected' : '' }}>Abierta</option>
                <option value="en_proceso" {{ request('estatus') === 'en_proceso' ? 'selected' : '' }}>En proceso</option>
                <option value="resuelta" {{ request('estatus') === 'resuelta' ? 'selected' : '' }}>Resuelta</option>
                <option value="cerrada" {{ request('estatus') === 'cerrada' ? 'selected' : '' }}>Cerrada</option>
            </select>
            <button type="submit" class="px-4 py-2 text-white text-sm font-medium rounded-lg transition hover:opacity-90" style="background-color:{{ $acento }}">Buscar</button>
            @if(request('search') || request('estatus'))
            <a href="{{ route('reportes.no_conforme') }}" class="px-3 py-2 bg-gray-100 text-gray-600 text-sm rounded-lg hover:bg-gray-200 transition">&times; Limpiar</a>
            @endif
        </div>
    </form>

    {{-- Tabla --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto" style="max-height:calc(100vh - 280px);overflow-y:auto;">
            <table class="w-full text-xs border-collapse" style="min-width:1400px;">
                <thead class="sticky top-0 z-10">
                    <tr style="background-color:{{ $acento }};">
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">CÓDIGO</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide border-r border-indigo-600" style="min-width:180px;">PRODUCTO</th>
                        <th class="px-3 py-2.5 text-right text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">CANTIDAD</th>
                        <th class="px-3 py-2.5 text-center text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">ESTATUS</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide border-r border-indigo-600" style="min-width:200px;">DESCRIPCIÓN</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide border-r border-indigo-600" style="min-width:200px;">RESOLUCIÓN</th>
                        <th class="px-3 py-2.5 text-center text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">F. DETECCIÓN</th>
                        <th class="px-3 py-2.5 text-center text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">F. RESOLUCIÓN</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">REGISTRADO POR</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">RESUELTO POR</th>
                        <th class="px-3 py-2.5 text-right text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">DÍAS</th>
                        @if(!$esVisitante)
                        <th class="px-3 py-2.5 text-center text-white font-semibold uppercase tracking-wide whitespace-nowrap">ACCIÓN</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" id="ncTableBody">
                    @forelse($registros as $nc)
                    @php
                        $p = $nc->producto;
                        $dias = $nc->fecha_deteccion ? (int) $nc->fecha_deteccion->diffInDays(now()) : null;
                        $diasClass = $dias === null ? 'text-gray-400'
                            : ($dias > 365 ? 'text-red-600 font-bold'
                            : ($dias > 180 ? 'text-orange-600 font-semibold'
                            : ($dias > 90  ? 'text-yellow-600 font-semibold'
                            : 'text-gray-700')));
                        $badgeClass = $estatusBadge[$nc->estatus] ?? 'bg-gray-100 text-gray-600 border-gray-200';
                    @endphp
                    <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }} hover:bg-blue-50 transition-colors" data-nc-id="{{ $nc->id }}">
                        <td class="px-3 py-2 font-mono font-semibold whitespace-nowrap">
                            @if($p)
                            <a href="{{ route('productos.show', $p->id) }}" class="hover:underline" style="color:{{ $acento }}">{{ $p->codigo }}</a>
                            @else
                            <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-gray-800">{{ $p->descripcion ?? '—' }}</td>
                        <td class="px-3 py-2 text-right whitespace-nowrap font-semibold text-gray-800">{{ number_format($nc->cantidad, 2) }}</td>
                        <td class="px-3 py-2 text-center whitespace-nowrap">
                            @if(!$esVisitante && !in_array($nc->estatus, ['cerrada']))
                            <select data-nc-id="{{ $nc->id }}" onchange="cambiarEstatus(this)"
                                class="text-xs font-medium px-2 py-1 rounded-lg border cursor-pointer focus:outline-none focus:ring-2 focus:ring-indigo-300 {{ $badgeClass }}">
                                <option value="abierta" {{ $nc->estatus === 'abierta' ? 'selected' : '' }}>Abierta</option>
                                <option value="en_proceso" {{ $nc->estatus === 'en_proceso' ? 'selected' : '' }}>En proceso</option>
                                <option value="resuelta" {{ $nc->estatus === 'resuelta' ? 'selected' : '' }}>Resuelta</option>
                                <option value="cerrada" {{ $nc->estatus === 'cerrada' ? 'selected' : '' }}>Cerrada</option>
                            </select>
                            @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium border {{ $badgeClass }}">
                                {{ ucfirst(str_replace('_', ' ', $nc->estatus)) }}
                            </span>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-gray-700" style="max-width:250px;">
                            <span class="block truncate" title="{{ $nc->descripcion }}">{{ $nc->descripcion }}</span>
                        </td>
                        <td class="px-3 py-2 text-gray-700" style="max-width:250px;">
                            @if($nc->resolucion)
                            <span class="block truncate" title="{{ $nc->resolucion }}">{{ $nc->resolucion }}</span>
                            @else
                            <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-center text-gray-700 whitespace-nowrap">{{ $nc->fecha_deteccion ? $nc->fecha_deteccion->format('d/m/Y') : '—' }}</td>
                        <td class="px-3 py-2 text-center text-gray-700 whitespace-nowrap">{{ $nc->fecha_resolucion ? $nc->fecha_resolucion->format('d/m/Y') : '—' }}</td>
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $nc->registrador->name ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $nc->resolutor->name ?? '—' }}</td>
                        <td class="px-3 py-2 text-right whitespace-nowrap {{ $diasClass }}">
                            {{ $dias !== null ? number_format($dias) . ' días' : '—' }}
                        </td>
                        @if(!$esVisitante)
                        <td class="px-3 py-2 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1">
                                @if(!in_array($nc->estatus, ['resuelta', 'cerrada']))
                                <button type="button"
                                    onclick="abrirModalResolver({{ $nc->id }})"
                                    title="Resolver esta no conformidad"
                                    class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded-lg border border-green-300 text-green-700 hover:bg-green-50 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Resolver
                                </button>
                                @endif
                            </div>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr id="emptyRow">
                        <td colspan="{{ $esVisitante ? 11 : 12 }}" class="px-6 py-16 text-center text-gray-400">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-12 h-12 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="font-medium text-green-600">Sin incidencias registradas</span>
                                @if(!$esVisitante)
                                <button type="button" onclick="abrirModalNC()" class="text-sm font-medium hover:underline mt-1" style="color:{{ $acento }}">Registrar primera incidencia &rarr;</button>
                                @endif
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

{{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     MODAL: Registrar Incidencia
â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
@if(!$esVisitante)
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
                        placeholder="Buscar por código o descripción..."
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
                <label class="block text-xs font-semibold text-gray-600 mb-1">Cantidad afectada <span class="text-red-500">*</span></label>
                <input type="number" id="nc_cantidad" step="0.01" min="0.01"
                    placeholder="Ej: 5.00"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Descripción de la incidencia <span class="text-red-500">*</span></label>
                <textarea id="nc_descripcion" rows="3"
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

{{-- MODAL: Resolver No Conformidad --}}
<div id="modalResolver" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background:rgba(0,0,0,0.45);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 rounded-t-2xl text-white bg-green-600">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span class="font-semibold">Resolver No Conformidad</span>
            </div>
            <button onclick="cerrarModalResolver()" class="text-white opacity-70 hover:opacity-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="px-6 py-5 space-y-4">
            <input type="hidden" id="resolver_nc_id">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Resolución aplicada <span class="text-red-500">*</span></label>
                <textarea id="resolver_resolucion" rows="3"
                    placeholder="Describe cómo se resolvió la no conformidad..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 resize-none"></textarea>
            </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3 rounded-b-2xl bg-gray-50">
            <button type="button" onclick="cerrarModalResolver()"
                class="px-5 py-2 bg-white border border-gray-300 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-50 transition">
                Cancelar
            </button>
            <button type="button" id="btnResolver" onclick="resolverNC()"
                class="px-6 py-2 bg-green-600 text-white text-sm font-semibold rounded-xl transition hover:bg-green-700 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Marcar como Resuelta
            </button>
        </div>
    </div>
</div>
@endif

<script>
const ACENTO = '{{ $acento }}';
const CSRF   = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

/* â”€â”€â”€ Modal NC â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
function abrirModalNC() {
    const m = document.getElementById('modalNC');
    if (!m) return;
    m.classList.remove('hidden');
    m.classList.add('flex');
    document.body.style.overflow = 'hidden';
    document.getElementById('nc_prod_id').value = '';
    document.getElementById('nc_prod_search').value = '';
    document.getElementById('nc_cantidad').value = '';
    document.getElementById('nc_descripcion').value = '';
    document.getElementById('nc_prod_preview').classList.add('hidden');
    setTimeout(() => document.getElementById('nc_prod_search').focus(), 100);
}
function cerrarModalNC() {
    const m = document.getElementById('modalNC');
    if (!m) return;
    m.classList.replace('flex','hidden');
    document.body.style.overflow = '';
}
document.getElementById('modalNC')?.addEventListener('click', e => { if (e.target === document.getElementById('modalNC')) cerrarModalNC(); });

/* â”€â”€â”€ Typeahead producto (modal NC) â”€â”€â”€â”€â”€ */
(function() {
    const input    = document.getElementById('nc_prod_search');
    const dropdown = document.getElementById('nc_prod_dropdown');
    const hiddenId = document.getElementById('nc_prod_id');
    if (!input) return;
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
                input.value    = `${item.codigo} — ${item.descripcion || ''}`;
                dropdown.classList.add('hidden');
                document.getElementById('nc_prod_preview_text').textContent = `${item.codigo} — ${item.descripcion || ''}`;
                document.getElementById('nc_prod_preview').classList.remove('hidden');
            });
            dropdown.appendChild(div);
        });
        if (dropdown.children.length) dropdown.classList.remove('hidden');
        else dropdown.classList.add('hidden');
    }
})();

/* â”€â”€â”€ Guardar nueva incidencia â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
async function guardarNC() {
    const prodId      = document.getElementById('nc_prod_id').value;
    const cantidad    = document.getElementById('nc_cantidad').value.trim();
    const descripcion = document.getElementById('nc_descripcion').value.trim();
    const btn         = document.getElementById('btnGuardarNC');

    if (!prodId) {
        document.getElementById('nc_prod_search').classList.add('border-red-400','ring-2','ring-red-200');
        document.getElementById('nc_prod_search').focus();
        setTimeout(() => document.getElementById('nc_prod_search').classList.remove('border-red-400','ring-2','ring-red-200'), 2500);
        return;
    }
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
            method : 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body   : JSON.stringify({ producto_id: prodId, cantidad: cantidad, descripcion: descripcion }),
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

/* â”€â”€â”€ Cambiar estatus inline â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
async function cambiarEstatus(select) {
    const ncId    = select.dataset.ncId;
    const estatus = select.value;

    if (estatus === 'resuelta' || estatus === 'cerrada') {
        abrirModalResolver(ncId);
        // Reset select to previous until modal confirms
        const prev = select.querySelector('option[selected]');
        if (prev) select.value = prev.value;
        return;
    }

    try {
        const res = await fetch(`/no-conformidades/${ncId}`, {
            method : 'PATCH',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body   : JSON.stringify({ estatus: estatus }),
        });
        if (!res.ok) throw new Error();
        const badgeClasses = {
            'abierta': 'bg-red-100 text-red-700 border-red-200',
            'en_proceso': 'bg-yellow-100 text-yellow-700 border-yellow-200',
            'resuelta': 'bg-green-100 text-green-700 border-green-200',
            'cerrada': 'bg-gray-100 text-gray-600 border-gray-200',
        };
        select.className = 'text-xs font-medium px-2 py-1 rounded-lg border cursor-pointer focus:outline-none focus:ring-2 focus:ring-indigo-300 ' + (badgeClasses[estatus] || '');
    } catch(e) {
        alert('Error al cambiar el estatus.');
        location.reload();
    }
}

/* â”€â”€â”€ Modal Resolver â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
function abrirModalResolver(ncId) {
    const m = document.getElementById('modalResolver');
    if (!m) return;
    document.getElementById('resolver_nc_id').value = ncId;
    document.getElementById('resolver_resolucion').value = '';
    m.classList.remove('hidden');
    m.classList.add('flex');
    document.body.style.overflow = 'hidden';
    setTimeout(() => document.getElementById('resolver_resolucion').focus(), 100);
}
function cerrarModalResolver() {
    const m = document.getElementById('modalResolver');
    if (!m) return;
    m.classList.replace('flex','hidden');
    document.body.style.overflow = '';
}
document.getElementById('modalResolver')?.addEventListener('click', e => { if (e.target === document.getElementById('modalResolver')) cerrarModalResolver(); });

async function resolverNC() {
    const ncId       = document.getElementById('resolver_nc_id').value;
    const resolucion = document.getElementById('resolver_resolucion').value.trim();
    const btn        = document.getElementById('btnResolver');

    if (!resolucion) {
        document.getElementById('resolver_resolucion').classList.add('border-red-400','ring-2','ring-red-200');
        document.getElementById('resolver_resolucion').focus();
        setTimeout(() => document.getElementById('resolver_resolucion').classList.remove('border-red-400','ring-2','ring-red-200'), 2500);
        return;
    }

    btn.disabled = true;
    btn.textContent = 'Guardando...';

    try {
        const res = await fetch(`/no-conformidades/${ncId}`, {
            method : 'PATCH',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body   : JSON.stringify({ estatus: 'resuelta', resolucion: resolucion }),
        });
        if (!res.ok) throw new Error();
        cerrarModalResolver();
        location.reload();
    } catch(e) {
        alert('Error al resolver la no conformidad.');
        btn.disabled = false;
        btn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Marcar como Resuelta`;
    }
}

/* â”€â”€â”€ Escape handler â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        if (typeof cerrarModalNC === 'function') cerrarModalNC();
        if (typeof cerrarModalResolver === 'function') cerrarModalResolver();
    }
});

/* â”€â”€â”€ Helpers â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
function xss(s) { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
</script>
@endsection
