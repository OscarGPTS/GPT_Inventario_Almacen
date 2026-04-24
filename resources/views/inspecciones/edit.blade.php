@extends('layouts.app')

@section('title', 'Editar Inspección ' . $inspeccion->folio)

@section('content')
@php $acento = '#4A568D'; @endphp
<div class="space-y-4">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('inspecciones.index') }}" class="hover:underline transition" style="color:{{ $acento }}">Inspecciones</a>
        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <a href="{{ route('inspecciones.show', $inspeccion) }}"
           class="font-mono font-semibold hover:underline transition" style="color:{{ $acento }}">{{ $inspeccion->folio }}</a>
        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-gray-700 font-medium">Editar</span>
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

    {{-- Banner calidad pendiente --}}
    @if($inspeccion->requiere_ctrl_calidad && !$inspeccion->resultado_calidad)
    <div class="flex items-center gap-3 px-4 py-3 bg-amber-50 border border-amber-200 rounded-xl text-amber-800 text-sm">
        <svg class="w-5 h-5 shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <span><strong>Pendiente:</strong> Esta inspección requiere que el equipo de Control de Calidad complete la Fase 2.</span>
    </div>
    @endif

    <form action="{{ route('inspecciones.update', $inspeccion) }}" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" name="articulo_id" value="{{ $inspeccion->articulo_id }}">

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
                            <div class="w-20 px-3 py-1.5"><p class="text-gray-400 font-medium">FOLIO</p><p class="font-mono font-bold" style="color:{{ $acento }}">{{ $inspeccion->folio }}</p></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-5 space-y-5">

                {{-- Fila datos generales --}}
                <div class="flex flex-wrap gap-4 items-end">
                    <div class="w-44 shrink-0">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Fecha de recepción</label>
                        <input type="date" name="fecha_recepcion"
                               value="{{ old('fecha_recepcion', $inspeccion->fecha_recepcion?->format('Y-m-d')) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition">
                    </div>
                    <div class="flex-1 min-w-36">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Requisición</label>
                        <input type="text" name="requisicion"
                               value="{{ old('requisicion', $inspeccion->requisicion) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition">
                    </div>
                    <div class="flex-1 min-w-36">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">O.C.</label>
                        <input type="text" name="orden_compra"
                               value="{{ old('orden_compra', $inspeccion->orden_compra) }}"
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
                                <option value="{{ $td }}" {{ old('tipo_documento', $inspeccion->tipo_documento) === $td ? 'selected' : '' }}>{{ $td }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="numero_documento" id="numero_documento"
                                   value="{{ old('numero_documento', $inspeccion->numero_documento) }}" placeholder="Número (ej. 39191392)"
                                   class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition">
                        </div>
                        <input type="text" name="tipo_documento_otro" id="tipo_documento_otro"
                               value="{{ old('tipo_documento_otro', $inspeccion->tipo_documento_otro) }}"
                               placeholder="Especificar tipo…"
                               class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 transition
                                      {{ old('tipo_documento', $inspeccion->tipo_documento) === 'Otro' ? '' : 'hidden' }}">
                    </div>
                </div>

                {{-- Artículo vinculado --}}
                @if($inspeccion->articulo)
                <div class="flex items-center gap-2 px-3 py-2 bg-indigo-50 border border-indigo-200 rounded-xl text-sm">
                    <svg class="w-4 h-4 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                    </svg>
                    <span class="text-indigo-700 font-medium text-xs">Artículo vinculado:</span>
                    <span class="font-mono text-indigo-900 font-semibold text-xs">{{ $inspeccion->articulo->codigo }}</span>
                    <span class="text-indigo-600 text-xs truncate">— {{ $inspeccion->articulo->descripcion }}</span>
                </div>
                @endif

                {{-- Cabeceras columnas --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="rounded-t-lg py-2 px-4 text-center text-sm font-bold text-white flex items-center justify-center gap-2" style="background-color:#3A8FC0;">
                        Solicitante
                        <svg class="w-3 h-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    @if($inspeccion->requiere_ctrl_calidad)
                    <div class="rounded-t-lg py-2 px-4 text-center text-sm font-bold text-white" style="background-color:#4E8030;">Control de Calidad</div>
                    @else
                    <div class="rounded-t-lg py-2 px-4 text-center text-sm font-bold text-white flex items-center justify-center gap-2" style="background-color:#6b7280;">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Control de Calidad
                    </div>
                    @endif
                </div>

                {{-- Dos columnas --}}
                <div class="grid grid-cols-2 gap-4 -mt-2">

                    {{-- Solicitante — readonly, solo lectura --}}
                    <div class="border border-gray-200 rounded-b-xl rounded-tr-xl p-4 space-y-3 bg-gray-50">
                        <p class="flex items-center gap-1.5 text-xs text-gray-400 font-medium mb-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Solo lectura — completado por el solicitante
                        </p>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Fecha de inspección</label>
                            <input type="date" name="fecha_inspeccion_solicitante" readonly
                                   value="{{ old('fecha_inspeccion_solicitante', $inspeccion->fecha_inspeccion_solicitante?->format('Y-m-d')) }}"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-100 text-gray-600 cursor-not-allowed focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Inspeccionó (Nombre)</label>
                            <input type="text" name="inspeccionado_solicitante" readonly
                                   value="{{ old('inspeccionado_solicitante', $inspeccion->inspeccionado_solicitante) }}"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-100 text-gray-600 cursor-not-allowed focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Departamento</label>
                            <input type="text" name="departamento_solicitante" readonly
                                   value="{{ old('departamento_solicitante', $inspeccion->departamento_solicitante) }}"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-100 text-gray-600 cursor-not-allowed focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Observaciones</label>
                            <textarea name="observaciones_solicitante" rows="3" readonly
                                      class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-100 text-gray-600 cursor-not-allowed focus:outline-none resize-none">{{ old('observaciones_solicitante', $inspeccion->observaciones_solicitante) }}</textarea>
                        </div>
                    </div>

                    {{-- Calidad — condicional --}}
                    @if($inspeccion->requiere_ctrl_calidad)
                    <div class="border border-gray-200 rounded-b-xl rounded-tl-xl p-4 space-y-3" style="background-color:#EBF5EB;">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Fecha de inspección</label>
                            <input type="date" name="fecha_inspeccion_calidad"
                                   value="{{ old('fecha_inspeccion_calidad', $inspeccion->fecha_inspeccion_calidad?->format('Y-m-d')) }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-400 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">
                                Inspeccionó (Nombre)
                                <span id="ac-loading-cal" class="hidden ml-1 text-green-600 font-normal normal-case">cargando…</span>
                            </label>
                            <div class="relative">
                                <input type="text" id="nombre-cal" name="inspeccionado_calidad"
                                       value="{{ old('inspeccionado_calidad', $inspeccion->inspeccionado_calidad) }}"
                                       placeholder="Escriba para buscar empleado…"
                                       autocomplete="off"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-400 transition">
                                <div id="dropdown-cal"
                                     class="hidden absolute left-0 right-0 top-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg z-50 max-h-52 overflow-y-auto"></div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Departamento</label>
                            <input type="text" id="depto-cal" name="departamento_calidad"
                                   value="{{ old('departamento_calidad', $inspeccion->departamento_calidad) }}"
                                   placeholder="Se llena al seleccionar nombre"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-400 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Observaciones</label>
                            <textarea name="observaciones_calidad" rows="3"
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-400 transition resize-none">{{ old('observaciones_calidad', $inspeccion->observaciones_calidad) }}</textarea>
                        </div>
                    </div>
                    @else
                    <div class="border border-dashed border-gray-300 rounded-b-xl rounded-tl-xl p-6 flex flex-col items-center justify-center gap-3 bg-gray-50 text-center">
                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <p class="text-sm font-semibold text-gray-400">No requiere Control de Calidad</p>
                    </div>
                    @endif
                </div>

                {{-- Sección inferior --}}
                <div class="bg-gray-50 rounded-xl border border-gray-200 p-4 space-y-4">

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 items-start">
                        {{-- Requiere Ctrl. Calidad --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Requiere Ctrl. Calidad</label>
                            <div class="flex gap-3">
                                <label class="flex items-center gap-1.5 cursor-pointer">
                                    <input type="radio" name="requiere_ctrl_calidad" value="1"
                                           {{ old('requiere_ctrl_calidad', $inspeccion->requiere_ctrl_calidad ? '1' : '0') == '1' ? 'checked' : '' }}
                                           class="w-4 h-4 text-indigo-600 focus:ring-indigo-400">
                                    <span class="text-sm font-semibold text-gray-700 border border-gray-400 px-2.5 py-0.5 rounded-lg">SÍ</span>
                                </label>
                                <label class="flex items-center gap-1.5 cursor-pointer">
                                    <input type="radio" name="requiere_ctrl_calidad" value="0"
                                           {{ old('requiere_ctrl_calidad', $inspeccion->requiere_ctrl_calidad ? '1' : '0') == '0' ? 'checked' : '' }}
                                           class="w-4 h-4 text-indigo-600 focus:ring-indigo-400">
                                    <span class="text-sm font-semibold text-gray-700 border border-gray-400 px-2.5 py-0.5 rounded-lg">NO</span>
                                </label>
                            </div>
                        </div>

                        {{-- No. Solicitud — solo visible cuando requiere calidad --}}
                        @if($inspeccion->requiere_ctrl_calidad)
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">No. Solicitud</label>
                            <input type="text" name="no_solicitud"
                                   value="{{ old('no_solicitud', $inspeccion->no_solicitud) }}"
                                   placeholder="Número de solicitud"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition">
                        </div>
                        @endif

                        {{-- Fecha ingreso inventario --}}
                        <div class="{{ $inspeccion->requiere_ctrl_calidad ? 'md:col-span-2' : 'md:col-span-3' }}">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Fecha de ingreso a inventario</label>
                            <input type="date" name="fecha_ingreso_inventario"
                                   value="{{ old('fecha_ingreso_inventario', $inspeccion->fecha_ingreso_inventario?->format('Y-m-d')) }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition">
                        </div>
                    </div>

                    {{-- Resultados --}}
                    <div class="grid {{ $inspeccion->requiere_ctrl_calidad ? 'grid-cols-2' : 'grid-cols-1' }} gap-4 pt-2 border-t border-gray-200">

                        {{-- Resultado Solicitante --}}
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3 text-center">Resultado — Solicitante</p>
                            <div class="flex gap-3 justify-center">
                                @foreach([
                                    'no_conforme' => ['No Conforme', 'bg-red-500',   'ring-red-400'],
                                    'conforme'    => ['Conforme',    'bg-green-600', 'ring-green-400'],
                                    'a_revision'  => ['A Revisión',  'bg-yellow-400','ring-yellow-400'],
                                ] as $val => [$label, $bg, $ring])
                                <label class="flex flex-col items-center gap-1.5 cursor-pointer group">
                                    <input type="radio" name="resultado_solicitante" value="{{ $val }}"
                                           {{ old('resultado_solicitante', $inspeccion->resultado_solicitante) === $val ? 'checked' : '' }}
                                           class="sr-only peer">
                                    <span class="w-20 h-8 rounded-lg {{ $bg }} opacity-30 peer-checked:opacity-100 peer-checked:ring-2 peer-checked:{{ $ring }} peer-checked:ring-offset-1 transition-all group-hover:opacity-60"></span>
                                    <span class="text-xs text-gray-500 peer-checked:font-semibold peer-checked:text-gray-800 transition">{{ $label }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Resultado Calidad — solo si requiere --}}
                        @if($inspeccion->requiere_ctrl_calidad)
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3 text-center">Resultado — Calidad</p>
                            <div class="flex gap-3 justify-center">
                                @foreach([
                                    'no_conforme' => ['No Conforme', 'bg-red-500',   'ring-red-400'],
                                    'conforme'    => ['Conforme',    'bg-green-600', 'ring-green-400'],
                                    'a_revision'  => ['A Revisión',  'bg-yellow-400','ring-yellow-400'],
                                ] as $val => [$label, $bg, $ring])
                                <label class="flex flex-col items-center gap-1.5 cursor-pointer group">
                                    <input type="radio" name="resultado_calidad" value="{{ $val }}"
                                           {{ old('resultado_calidad', $inspeccion->resultado_calidad) === $val ? 'checked' : '' }}
                                           class="sr-only peer">
                                    <span class="w-20 h-8 rounded-lg {{ $bg }} opacity-30 peer-checked:opacity-100 peer-checked:ring-2 peer-checked:{{ $ring }} peer-checked:ring-offset-1 transition-all group-hover:opacity-60"></span>
                                    <span class="text-xs text-gray-500 peer-checked:font-semibold peer-checked:text-gray-800 transition">{{ $label }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

            </div>

            {{-- Footer --}}
            <div class="border-t border-gray-200 px-5 py-3 bg-gray-50 flex justify-between items-center">
                <a href="{{ route('inspecciones.show', $inspeccion) }}"
                   class="px-4 py-2 bg-white border border-gray-300 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                    ← Cancelar
                </a>
                <button type="submit"
                        class="px-5 py-2.5 text-white text-sm font-semibold rounded-xl shadow transition hover:opacity-90 active:scale-95"
                        style="background-color:{{ $acento }}">
                    Guardar Cambios
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function toggleOtro() {
    const sel   = document.getElementById('tipo_documento');
    const input = document.getElementById('tipo_documento_otro');
    const show  = sel.value === 'Otro';
    input.classList.toggle('hidden', !show);
    if (!show) input.value = '';
}

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
    if (!input) return;
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
    initAutocomplete('nombre-cal', 'depto-cal', 'dropdown-cal');
});
</script>
@endsection
