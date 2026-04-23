@extends('layouts.app')

@section('title', 'Gestión de Usuarios')

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
            <h1 class="text-xl font-bold text-gray-800">Gestión de Usuarios</h1>
            <p class="text-xs text-gray-500 mt-0.5">Alta, asignación de roles y administración de usuarios del sistema</p>
        </div>
        <button onclick="abrirModalAlta()"
            class="flex items-center gap-1.5 px-4 py-2 text-white text-sm font-semibold rounded-lg shadow transition hover:opacity-90"
            style="background-color:#4A568D">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            Dar de Alta
        </button>
    </div>

    {{-- Tabla de Usuarios --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="background-color:#4A568D;">
                        <th class="px-4 py-2.5 text-left text-white font-semibold uppercase tracking-wide text-xs w-12"></th>
                        <th class="px-4 py-2.5 text-left text-white font-semibold uppercase tracking-wide text-xs">NOMBRE</th>
                        <th class="px-4 py-2.5 text-left text-white font-semibold uppercase tracking-wide text-xs">EMAIL</th>
                        <th class="px-4 py-2.5 text-center text-white font-semibold uppercase tracking-wide text-xs">ROL</th>
                        <th class="px-4 py-2.5 text-center text-white font-semibold uppercase tracking-wide text-xs">REGISTRO</th>
                        <th class="px-4 py-2.5 text-center text-white font-semibold uppercase tracking-wide text-xs w-32">ACCIONES</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($usuarios as $u)
                    <tr class="hover:bg-blue-50 transition-colors {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                        <td class="px-4 py-2.5">
                            @if($u->avatar)
                                <img src="{{ $u->avatar }}" alt="{{ $u->name }}" class="w-8 h-8 rounded-full border border-gray-300 object-cover">
                            @else
                                <div class="w-8 h-8 rounded-full flex items-center justify-center border border-gray-300 text-xs font-bold text-white" style="background-color:#4A568D;">
                                    {{ strtoupper(substr($u->name, 0, 1)) }}
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-2 font-medium text-gray-800">{{ $u->name }}</td>
                        <td class="px-4 py-2 text-gray-600 text-xs font-mono">{{ $u->email ?? '—' }}</td>
                        <td class="px-4 py-2 text-center">
                            @php $roleName = $u->roles->first()?->name ?? 'sin_rol'; @endphp
                            @switch($roleName)
                                @case('admin')
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold bg-red-100 text-red-700">Admin</span>
                                    @break
                                @case('admin_almacen')
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold bg-orange-100 text-orange-700">Admin Almacén</span>
                                    @break
                                @case('almacenista')
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-700">Almacenista</span>
                                    @break
                                @case('visitante')
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-600">Visitante</span>
                                    @break
                                @default
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold bg-yellow-100 text-yellow-700">Sin Rol</span>
                            @endswitch
                        </td>
                        <td class="px-4 py-2 text-center text-xs text-gray-500">{{ $u->created_at?->format('d/m/Y') }}</td>
                        <td class="px-4 py-2 text-center">
                            <div class="flex items-center justify-center gap-1">
                                {{-- Cambiar Rol --}}
                                <button onclick="abrirModalRol({{ $u->id }}, '{{ addslashes($u->name) }}', '{{ $roleName }}')"
                                    class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Cambiar Rol">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                </button>
                                {{-- Eliminar --}}
                                @if(auth()->id() !== $u->id)
                                <form method="POST" action="{{ route('usuarios.destroy', $u) }}"
                                    onsubmit="return confirm('¿Estás seguro de eliminar a «{{ $u->name }}»?')">
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
                        <td colspan="6" class="px-4 py-8 text-center text-gray-400 text-sm">No hay usuarios registrados</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-2 bg-gray-50 border-t border-gray-200 text-xs text-gray-500">
            Total: {{ $usuarios->count() }} usuarios
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════
     MODAL: Dar de Alta usuario (desde API RH)
═══════════════════════════════════════════════════════════════════ --}}
<div id="modalAlta" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background:rgba(0,0,0,0.45);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl flex flex-col" style="max-height:90vh;">
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 text-white rounded-t-2xl shrink-0" style="background-color:#4A568D">
            <div class="flex items-center gap-3">
                <div class="bg-white/20 p-2 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                </div>
                <div>
                    <h3 class="font-bold text-base">Dar de Alta Usuario</h3>
                    <p id="altaSubtitle" class="text-xs text-white opacity-90">Cargando directorio de empleados…</p>
                </div>
            </div>
            <button onclick="cerrarModalAlta()" class="text-white/80 hover:text-white hover:bg-white/10 p-2 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Paso 1: Seleccionar empleado --}}
        <div id="pasoLista" class="flex flex-col overflow-hidden flex-1">
            {{-- Buscador --}}
            <div class="px-6 pt-4 pb-2 shrink-0">
                <div class="relative">
                    <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" id="searchEmpleado" placeholder="Buscar por nombre, email o puesto…"
                        class="w-full border-2 border-gray-300 rounded-lg pl-9 pr-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400"
                        oninput="filtrarEmpleados(this.value)">
                </div>
                <p id="contadorResultados" class="text-xs text-gray-400 mt-1.5 pl-1"></p>
            </div>

            {{-- Lista de empleados --}}
            <div id="listaEmpleados" class="overflow-y-auto flex-1 px-4 pb-4 space-y-1.5">
                {{-- Skeleton loader --}}
                <div id="loadingState" class="py-10 text-center">
                    <svg class="animate-spin mx-auto h-8 w-8 mb-3" style="color:#4A568D" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <p class="text-sm text-gray-500">Consultando API de RH…</p>
                </div>
                <div id="errorState" class="hidden py-10 text-center">
                    <svg class="mx-auto h-10 w-10 text-red-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <p id="errorMsg" class="text-sm text-red-500"></p>
                    <button onclick="cargarEmpleados()" class="mt-3 text-xs text-indigo-600 hover:underline">Reintentar</button>
                </div>
                <div id="emptyState" class="hidden py-10 text-center">
                    <svg class="mx-auto h-10 w-10 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
                    <p class="text-sm text-gray-400">No se encontraron empleados</p>
                </div>
                <div id="empleadosCards" class="hidden space-y-1.5">
                    {{-- Tarjetas insertadas por JS --}}
                </div>
            </div>
        </div>

        {{-- Paso 2: Confirmar y asignar rol --}}
        <div id="pasoConfirmar" class="hidden flex-col overflow-y-auto flex-1 px-6 py-4 space-y-4">
            {{-- Botón Volver --}}
            <button type="button" onclick="volverALista()"
                class="flex items-center gap-1 text-sm text-indigo-600 hover:text-indigo-800 transition w-fit">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Volver a la lista
            </button>

            {{-- Card empleado seleccionado --}}
            <div class="border-2 rounded-xl p-4 bg-indigo-50/50" style="border-color:#4A568D20;">
                <div class="flex items-center gap-4">
                    <img id="selAvatar" src="" alt="" class="w-14 h-14 rounded-full border-2 border-white shadow object-cover"
                        onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22%234A568D%22><path d=%22M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z%22/%3E</svg>'">
                    <div class="flex-1 min-w-0">
                        <p id="selNombre" class="font-bold text-gray-800 text-base"></p>
                        <p id="selEmail" class="text-xs text-gray-500 font-mono mt-0.5"></p>
                        <p id="selPuesto" class="text-xs text-gray-500 mt-0.5"></p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-xs mt-3 pt-3 border-t border-indigo-100">
                    <div><span class="text-gray-400">Departamento:</span> <span id="selDepto" class="font-medium text-gray-700"></span></div>
                    <div><span class="text-gray-400">Área:</span> <span id="selArea" class="font-medium text-gray-700"></span></div>
                    <div class="col-span-2"><span class="text-gray-400">Razón social:</span> <span id="selRazon" class="font-medium text-gray-700"></span></div>
                </div>
            </div>

            <form method="POST" action="{{ route('usuarios.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="name" id="formName">
                <input type="hidden" name="email" id="formEmail">
                <input type="hidden" name="avatar" id="formAvatar">

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Rol a asignar <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-2" id="roleCards">
                        @foreach($roles as $role)
                        <label class="role-card cursor-pointer border-2 border-gray-200 rounded-xl p-3 flex items-center gap-3 hover:border-indigo-400 transition has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50">
                            <input type="radio" name="role" value="{{ $role->name }}" class="sr-only" required
                                {{ $loop->first ? 'checked' : '' }}>
                            <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0
                                @if($role->name === 'admin') bg-red-100 text-red-600
                                @elseif($role->name === 'admin_almacen') bg-orange-100 text-orange-600
                                @elseif($role->name === 'almacenista') bg-blue-100 text-blue-600
                                @else bg-gray-100 text-gray-500 @endif">
                                @if($role->name === 'admin')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                @elseif($role->name === 'admin_almacen')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                @elseif($role->name === 'almacenista')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-800">{{ $role->display_name ?? $role->name }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ $role->description ?? '' }}</p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-2 border-t border-gray-200">
                    <button type="button" onclick="cerrarModalAlta()"
                        class="px-5 py-2 bg-white border-2 border-gray-300 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-50 transition">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-6 py-2 text-white text-sm font-bold rounded-xl transition hover:opacity-90 shadow-lg flex items-center gap-2"
                        style="background-color:#4A568D">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Dar de Alta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════
     MODAL: Cambiar Rol
═══════════════════════════════════════════════════════════════════ --}}
<div id="modalRol" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background:rgba(0,0,0,0.45);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 text-white rounded-t-2xl" style="background-color:#4A568D">
            <div class="flex items-center gap-3">
                <div class="bg-white/20 p-2 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                </div>
                <div>
                    <h3 class="font-bold text-base">Cambiar Rol</h3>
                    <p id="rolSubtitle" class="text-xs text-white opacity-90"></p>
                </div>
            </div>
            <button onclick="cerrarModalRol()" class="text-white/80 hover:text-white hover:bg-white/10 p-2 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="formRol" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nuevo rol</label>
                <select name="role" id="selectRol" required
                    class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400">
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}">{{ $role->display_name ?? $role->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end gap-3 pt-3 border-t border-gray-200">
                <button type="button" onclick="cerrarModalRol()"
                    class="px-5 py-2 bg-white border-2 border-gray-300 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-50 transition">
                    Cancelar
                </button>
                <button type="submit"
                    class="px-6 py-2 text-white text-sm font-bold rounded-xl transition hover:opacity-90 shadow-lg flex items-center gap-2"
                    style="background-color:#4A568D">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
/* ═══════════════════════════════════════════════════════════════════
   ESTADO GLOBAL
═══════════════════════════════════════════════════════════════════ */
let todosEmpleados = [];   // caché de la lista completa
let apiCargada = false;    // para no pedir la API dos veces

/* ═══════════════════════════════════════════════════════════════════
   MODAL: Dar de Alta — abrir / cerrar
═══════════════════════════════════════════════════════════════════ */
function abrirModalAlta() {
    const m = document.getElementById('modalAlta');
    m.classList.remove('hidden');
    m.classList.add('flex');
    document.body.style.overflow = 'hidden';

    // Siempre mostrar paso 1
    mostrarPaso('lista');
    document.getElementById('searchEmpleado').value = '';

    // Cargar empleados si aún no se ha hecho (o recargar)
    if (!apiCargada) {
        cargarEmpleados();
    } else {
        renderEmpleados(todosEmpleados);
        setTimeout(() => document.getElementById('searchEmpleado').focus(), 100);
    }
}

function cerrarModalAlta() {
    const m = document.getElementById('modalAlta');
    m.classList.replace('flex', 'hidden');
    document.body.style.overflow = '';
}

document.getElementById('modalAlta')?.addEventListener('click', function(e) {
    if (e.target === this) cerrarModalAlta();
});

/* ═══════════════════════════════════════════════════════════════════
   PASOS: lista ↔ confirmar
═══════════════════════════════════════════════════════════════════ */
function mostrarPaso(paso) {
    document.getElementById('pasoLista').classList.toggle('hidden', paso !== 'lista');
    document.getElementById('pasoLista').classList.toggle('flex', paso === 'lista');
    document.getElementById('pasoConfirmar').classList.toggle('hidden', paso !== 'confirmar');
    document.getElementById('pasoConfirmar').classList.toggle('flex', paso === 'confirmar');
}

function volverALista() {
    mostrarPaso('lista');
    document.getElementById('altaSubtitle').textContent =
        todosEmpleados.length + ' empleados disponibles';
    setTimeout(() => document.getElementById('searchEmpleado').focus(), 50);
}

/* ═══════════════════════════════════════════════════════════════════
   CARGA DESDE API (una sola llamada al servidor)
═══════════════════════════════════════════════════════════════════ */
function cargarEmpleados() {
    setEstado('loading');
    document.getElementById('altaSubtitle').textContent = 'Cargando directorio de empleados…';

    fetch('/usuarios/buscar-api')
        .then(r => {
            if (!r.ok) throw new Error('Error ' + r.status);
            return r.json();
        })
        .then(data => {
            if (data.error) throw new Error(data.error);
            todosEmpleados = data;
            apiCargada = true;
            renderEmpleados(todosEmpleados);
            setTimeout(() => document.getElementById('searchEmpleado').focus(), 100);
        })
        .catch(err => {
            setEstado('error');
            document.getElementById('errorMsg').textContent = err.message || 'No se pudo cargar el directorio';
            document.getElementById('altaSubtitle').textContent = 'Error al cargar';
        });
}

/* ═══════════════════════════════════════════════════════════════════
   FILTRO CLIENT-SIDE
═══════════════════════════════════════════════════════════════════ */
function filtrarEmpleados(q) {
    q = q.trim().toLowerCase();
    if (!q) {
        renderEmpleados(todosEmpleados);
        return;
    }
    const filtrados = todosEmpleados.filter(emp =>
        (emp.nombre_completo || '').toLowerCase().includes(q) ||
        (emp.email || '').toLowerCase().includes(q) ||
        (emp.puesto?.nombre || '').toLowerCase().includes(q) ||
        (emp.departamento?.nombre || '').toLowerCase().includes(q)
    );
    renderEmpleados(filtrados);
}

/* ═══════════════════════════════════════════════════════════════════
   RENDER DE TARJETAS
═══════════════════════════════════════════════════════════════════ */
function renderEmpleados(lista) {
    const disponibles = lista.filter(e => !e.ya_registrado);
    const registrados = lista.filter(e => e.ya_registrado);
    const total = disponibles.length;

    document.getElementById('altaSubtitle').textContent =
        total + ' empleado' + (total !== 1 ? 's' : '') + ' disponible' + (total !== 1 ? 's' : '');
    document.getElementById('contadorResultados').textContent =
        lista.length + ' resultado' + (lista.length !== 1 ? 's' : '') +
        (registrados.length ? ' · ' + registrados.length + ' ya registrado' + (registrados.length !== 1 ? 's' : '') : '');

    if (lista.length === 0) {
        setEstado('empty');
        return;
    }
    setEstado('cards');

    const container = document.getElementById('empleadosCards');
    container.innerHTML = '';

    // Primero los disponibles, luego los ya registrados
    [...disponibles, ...registrados].forEach(emp => {
        const card = document.createElement('button');
        card.type = 'button';
        const disabled = emp.ya_registrado;
        card.disabled = disabled;
        card.className = 'w-full flex items-center gap-3 p-3 rounded-xl border-2 transition text-left ' +
            (disabled
                ? 'border-gray-100 bg-gray-50/50 opacity-60 cursor-not-allowed'
                : 'border-gray-200 hover:border-indigo-400 hover:bg-indigo-50/30 cursor-pointer');

        const avatarFallback = `data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%234A568D'><path d='M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z'/%3E</svg>`;

        card.innerHTML = `
            <img src="${emp.foto_perfil || avatarFallback}" alt="${emp.nombre_completo}"
                class="w-10 h-10 rounded-full border border-gray-200 object-cover shrink-0"
                onerror="this.src='${avatarFallback}'">
            <div class="min-w-0 flex-1">
                <p class="font-semibold text-gray-800 text-sm truncate">${emp.nombre_completo}</p>
                <p class="text-xs text-gray-500 font-mono truncate">${emp.email || '—'}</p>
                <p class="text-xs text-gray-400 truncate">${emp.puesto?.nombre || ''} · ${emp.departamento?.nombre || ''}</p>
            </div>
            ${disabled ? '<span class="text-xs bg-gray-200 text-gray-500 px-2 py-0.5 rounded-full shrink-0">Registrado</span>' : ''}
        `;

        if (!disabled) {
            card.onclick = () => seleccionarEmpleado(emp);
        }
        container.appendChild(card);
    });
}

function setEstado(estado) {
    ['loadingState', 'errorState', 'emptyState', 'empleadosCards'].forEach(id => {
        document.getElementById(id)?.classList.add('hidden');
    });
    document.getElementById(
        estado === 'loading' ? 'loadingState' :
        estado === 'error'   ? 'errorState' :
        estado === 'empty'   ? 'emptyState' : 'empleadosCards'
    )?.classList.remove('hidden');
}

/* ═══════════════════════════════════════════════════════════════════
   SELECCIONAR EMPLEADO → paso 2
═══════════════════════════════════════════════════════════════════ */
function seleccionarEmpleado(emp) {
    // Rellenar datos
    document.getElementById('selAvatar').src = emp.foto_perfil || '';
    document.getElementById('selNombre').textContent = emp.nombre_completo;
    document.getElementById('selEmail').textContent = emp.email || '—';
    document.getElementById('selPuesto').textContent = emp.puesto?.nombre || '—';
    document.getElementById('selDepto').textContent = emp.departamento?.nombre || '—';
    document.getElementById('selArea').textContent = emp.area?.nombre || '—';
    document.getElementById('selRazon').textContent = emp.razon_social?.nombre || '—';

    document.getElementById('formName').value = emp.nombre_completo;
    document.getElementById('formEmail').value = emp.email || '';
    document.getElementById('formAvatar').value = emp.foto_perfil || '';

    document.getElementById('altaSubtitle').textContent = 'Asignar rol a ' + emp.nombre_completo.split(' ')[0];
    mostrarPaso('confirmar');
}

/* ═══════════════════════════════════════════════════════════════════
   MODAL: Cambiar Rol
═══════════════════════════════════════════════════════════════════ */
function abrirModalRol(userId, userName, currentRole) {
    document.getElementById('formRol').action = '/usuarios/' + userId + '/rol';
    document.getElementById('rolSubtitle').textContent = userName;
    document.getElementById('selectRol').value = currentRole;
    const m = document.getElementById('modalRol');
    m.classList.remove('hidden');
    m.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function cerrarModalRol() {
    const m = document.getElementById('modalRol');
    m.classList.replace('flex', 'hidden');
    document.body.style.overflow = '';
}

document.getElementById('modalRol')?.addEventListener('click', function(e) {
    if (e.target === this) cerrarModalRol();
});
</script>
@endsection
