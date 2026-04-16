@extends('layouts.app')

@section('title', 'Catálogos')

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
            <h1 class="text-xl font-bold text-gray-800">Catálogos del Sistema</h1>
            <p class="text-xs text-gray-500 mt-0.5">Gestiona componentes, categorías, familias, unidades de medida y ubicaciones</p>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        {{-- Tab Header --}}
        <div class="flex border-b border-gray-200 overflow-x-auto" id="tabHeader">
            @foreach($catalogs as $key => $cfg)
            <button onclick="switchTab('{{ $key }}')" id="tab_{{ $key }}"
                class="tab-btn flex items-center gap-2 px-5 py-3 text-sm font-medium whitespace-nowrap border-b-2 transition-colors
                {{ $loop->first ? 'border-indigo-600 text-indigo-700 bg-indigo-50/50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                @switch($key)
                    @case('componentes')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                        @break
                    @case('categorias')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        @break
                    @case('familias')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        @break
                    @case('unidades_medida')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                        @break
                    @case('ubicaciones')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        @break
                @endswitch
                {{ $cfg['label'] }}
                <span class="ml-1 px-1.5 py-0.5 bg-gray-200 text-gray-600 text-xs rounded-full font-semibold">{{ $data[$key]->count() }}</span>
            </button>
            @endforeach

            {{-- Tab Bitácora --}}
            <button onclick="switchTab('logs')" id="tab_logs"
                class="tab-btn flex items-center gap-2 px-5 py-3 text-sm font-medium whitespace-nowrap border-b-2 transition-colors border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Bitácora
                <span class="ml-1 px-1.5 py-0.5 bg-gray-200 text-gray-600 text-xs rounded-full font-semibold">{{ $logs->count() }}</span>
            </button>
        </div>

        {{-- Tab Content: Catálogos --}}
        @foreach($catalogs as $key => $cfg)
        <div id="content_{{ $key }}" class="tab-content {{ !$loop->first ? 'hidden' : '' }}">
            <div class="px-4 py-3 flex items-center justify-between border-b border-gray-100">
                <h3 class="font-semibold text-gray-700">{{ $cfg['label'] }}s</h3>
                @if(auth()->user()->hasPermission('catalogos-create') || auth()->user()->hasRole('admin'))
                <button onclick="abrirModalCrear('{{ $key }}', '{{ $cfg['label'] }}')"
                    class="flex items-center gap-1.5 px-3 py-1.5 text-white text-xs font-semibold rounded-lg shadow transition hover:opacity-90"
                    style="background-color:#4A568D">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Nuevo
                </button>
                @endif
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="background-color:#4A568D;">
                            <th class="px-4 py-2.5 text-left text-white font-semibold uppercase tracking-wide text-xs w-16">ID</th>
                            <th class="px-4 py-2.5 text-left text-white font-semibold uppercase tracking-wide text-xs">CÓDIGO</th>
                            <th class="px-4 py-2.5 text-left text-white font-semibold uppercase tracking-wide text-xs">DESCRIPCIÓN</th>
                            <th class="px-4 py-2.5 text-center text-white font-semibold uppercase tracking-wide text-xs w-32">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($data[$key] as $item)
                        <tr class="hover:bg-blue-50 transition-colors {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                            <td class="px-4 py-2 text-gray-400 font-mono text-xs">{{ $item->id }}</td>
                            <td class="px-4 py-2 font-mono font-semibold" style="color:#4A568D;">{{ $item->codigo }}</td>
                            <td class="px-4 py-2 text-gray-700">{{ $item->descripcion }}</td>
                            <td class="px-4 py-2 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    @if(auth()->user()->hasPermission('catalogos-update') || auth()->user()->hasRole('admin'))
                                    <button onclick="abrirModalEditar('{{ $key }}', '{{ $cfg['label'] }}', {{ $item->id }}, '{{ addslashes($item->codigo) }}', '{{ addslashes($item->descripcion) }}')"
                                        class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Editar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    @endif
                                    @if(auth()->user()->hasRole('admin'))
                                    <form method="POST" action="{{ route('catalogos.destroy', [$key, $item->id]) }}"
                                        onsubmit="return confirm('¿Estás seguro de eliminar «{{ $item->codigo }}»? Esta acción no se puede deshacer.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition" title="Eliminar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-400 text-sm">No hay registros en este catálogo</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endforeach

        {{-- Tab Content: Bitácora --}}
        <div id="content_logs" class="tab-content hidden">
            <div class="px-4 py-3 border-b border-gray-100">
                <h3 class="font-semibold text-gray-700">Bitácora de Auditoría</h3>
                <p class="text-xs text-gray-400 mt-0.5">Últimos 50 movimientos registrados</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="background-color:#4A568D;">
                            <th class="px-4 py-2.5 text-left text-white font-semibold uppercase tracking-wide text-xs">FECHA</th>
                            <th class="px-4 py-2.5 text-left text-white font-semibold uppercase tracking-wide text-xs">USUARIO</th>
                            <th class="px-4 py-2.5 text-center text-white font-semibold uppercase tracking-wide text-xs">ACCIÓN</th>
                            <th class="px-4 py-2.5 text-left text-white font-semibold uppercase tracking-wide text-xs">TABLA</th>
                            <th class="px-4 py-2.5 text-center text-white font-semibold uppercase tracking-wide text-xs">ID REG.</th>
                            <th class="px-4 py-2.5 text-left text-white font-semibold uppercase tracking-wide text-xs">VALORES ANTERIORES</th>
                            <th class="px-4 py-2.5 text-left text-white font-semibold uppercase tracking-wide text-xs">VALORES NUEVOS</th>
                            <th class="px-4 py-2.5 text-left text-white font-semibold uppercase tracking-wide text-xs">IP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($logs as $log)
                        <tr class="hover:bg-blue-50 transition-colors {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                            <td class="px-4 py-2 text-gray-600 whitespace-nowrap text-xs">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-2 text-gray-700 whitespace-nowrap text-xs">{{ $log->user->name ?? '—' }}</td>
                            <td class="px-4 py-2 text-center">
                                @if($log->action === 'created')
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold bg-green-100 text-green-700">Creado</span>
                                @elseif($log->action === 'updated')
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold bg-yellow-100 text-yellow-700">Editado</span>
                                @elseif($log->action === 'deleted')
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold bg-red-100 text-red-700">Eliminado</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-gray-600 text-xs font-mono">{{ $log->table_name }}</td>
                            <td class="px-4 py-2 text-center text-gray-500 text-xs">{{ $log->record_id }}</td>
                            <td class="px-4 py-2 text-xs text-gray-500 max-w-[200px] truncate" title="{{ json_encode($log->old_values) }}">
                                @if($log->old_values)
                                    <code class="text-xs">{{ Str::limit(json_encode($log->old_values, JSON_UNESCAPED_UNICODE), 60) }}</code>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-xs text-gray-500 max-w-[200px] truncate" title="{{ json_encode($log->new_values) }}">
                                @if($log->new_values)
                                    <code class="text-xs">{{ Str::limit(json_encode($log->new_values, JSON_UNESCAPED_UNICODE), 60) }}</code>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-gray-400 text-xs font-mono">{{ $log->ip_address }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-400 text-sm">No hay registros en la bitácora</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════════════
     MODAL: Crear / Editar Catálogo
═══════════════════════════════════════════════════════════════════════════════ --}}
<div id="modalCatalogo" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background:rgba(0,0,0,0.45);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md flex flex-col">
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 text-white rounded-t-2xl" style="background-color:#4A568D">
            <div class="flex items-center gap-3">
                <div class="bg-white/20 p-2 rounded-lg">
                    <svg id="modalIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <div>
                    <h3 id="modalTitle" class="font-bold text-base">Nuevo Registro</h3>
                    <p id="modalSubtitle" class="text-xs text-white opacity-90">Completa la información</p>
                </div>
            </div>
            <button onclick="cerrarModal()" class="text-white/80 hover:text-white hover:bg-white/10 p-2 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <form id="formCatalogo" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Código <span class="text-red-500">*</span>
                </label>
                <input type="text" name="codigo" id="inputCodigo" required
                    class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400"
                    placeholder="Ej: G, AF, 005...">
                <p id="hintCodigo" class="text-xs text-gray-400 mt-1"></p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Descripción <span class="text-red-500">*</span>
                </label>
                <input type="text" name="descripcion" id="inputDescripcion" required maxlength="100"
                    class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400"
                    placeholder="Descripción del registro">
                <p class="text-xs text-gray-400 mt-1">Máx. 100 caracteres</p>
            </div>

            {{-- Botones --}}
            <div class="flex justify-end gap-3 pt-3 border-t border-gray-200">
                <button type="button" onclick="cerrarModal()"
                    class="px-5 py-2 bg-white border-2 border-gray-300 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-50 transition">
                    Cancelar
                </button>
                <button type="submit" id="btnGuardar"
                    class="px-6 py-2 text-white text-sm font-bold rounded-xl transition hover:opacity-90 shadow-lg flex items-center gap-2"
                    style="background-color:#4A568D">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span id="btnTexto">Guardar</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
/* ═══════════════════════════════════════════════════════════════════
   TABS
═══════════════════════════════════════════════════════════════════ */
function switchTab(key) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(el => {
        el.classList.remove('border-indigo-600', 'text-indigo-700', 'bg-indigo-50/50');
        el.classList.add('border-transparent', 'text-gray-500');
    });
    document.getElementById('content_' + key).classList.remove('hidden');
    const btn = document.getElementById('tab_' + key);
    btn.classList.add('border-indigo-600', 'text-indigo-700', 'bg-indigo-50/50');
    btn.classList.remove('border-transparent', 'text-gray-500');
}

/* ═══════════════════════════════════════════════════════════════════
   MODAL CRUD
═══════════════════════════════════════════════════════════════════ */
const CODIGO_LIMITS = {
    componentes:    1,
    categorias:    10,
    familias:      10,
    unidades_medida: 10,
    ubicaciones:   20
};

function aplicarLimiteCodigo(catalogo) {
    const max   = CODIGO_LIMITS[catalogo] ?? 10;
    const input = document.getElementById('inputCodigo');
    input.maxLength = max;
    const hint  = document.getElementById('hintCodigo');
    hint.textContent = 'Máx. ' + max + ' caracter' + (max === 1 ? '' : 'es');
}

function abrirModalCrear(catalogo, label) {
    document.getElementById('modalTitle').textContent = 'Nuevo ' + label;
    document.getElementById('modalSubtitle').textContent = 'Agregar un nuevo registro al catálogo';
    document.getElementById('formCatalogo').action = '/catalogos/' + catalogo;
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('inputCodigo').value = '';
    document.getElementById('inputDescripcion').value = '';
    document.getElementById('btnTexto').textContent = 'Guardar';
    document.getElementById('modalIcon').innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>';
    aplicarLimiteCodigo(catalogo);
    mostrarModal();
}

function abrirModalEditar(catalogo, label, id, codigo, descripcion) {
    document.getElementById('modalTitle').textContent = 'Editar ' + label;
    document.getElementById('modalSubtitle').textContent = 'Modificar «' + codigo + '»';
    document.getElementById('formCatalogo').action = '/catalogos/' + catalogo + '/' + id;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('inputCodigo').value = codigo;
    document.getElementById('inputDescripcion').value = descripcion;
    document.getElementById('btnTexto').textContent = 'Actualizar';
    document.getElementById('modalIcon').innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>';
    aplicarLimiteCodigo(catalogo);
    mostrarModal();
}

function mostrarModal() {
    const m = document.getElementById('modalCatalogo');
    m.classList.remove('hidden');
    m.classList.add('flex');
    document.body.style.overflow = 'hidden';
    setTimeout(() => document.getElementById('inputCodigo').focus(), 100);
}

function cerrarModal() {
    const m = document.getElementById('modalCatalogo');
    m.classList.replace('flex', 'hidden');
    document.body.style.overflow = '';
}

// Cerrar modal al hacer clic fuera
document.getElementById('modalCatalogo')?.addEventListener('click', function(e) {
    if (e.target === this) cerrarModal();
});
</script>
@endsection
