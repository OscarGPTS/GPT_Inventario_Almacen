@extends('layouts.app')

@section('title', 'Inspección ' . $inspeccion->folio)

@section('content')
@php $acento = '#4A568D'; @endphp
<div class="space-y-4 max-w-5xl mx-auto">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('inspecciones.index') }}" class="hover:underline transition" style="color:{{ $acento }}">Inspecciones</a>
        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="font-mono font-semibold text-gray-700">{{ $inspeccion->folio }}</span>
    </div>

    @if(session('success'))
    <div class="flex items-center gap-3 px-4 py-3 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm">
        <svg class="w-5 h-5 shrink-0 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

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

            {{-- Acciones --}}
            <div class="flex flex-wrap items-center justify-between gap-3">
                <p class="text-xs text-gray-500">
                    Registrado el {{ $inspeccion->created_at->format('d/m/Y H:i') }}
                    por <span class="font-medium text-gray-700">{{ $inspeccion->registradoPor?->name ?? '—' }}</span>
                </p>
                <div class="flex gap-2">
                    <a href="{{ route('inspecciones.edit', $inspeccion) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-300 text-gray-700 text-xs font-semibold rounded-lg hover:bg-gray-50 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                        Editar
                    </a>
                    <form action="{{ route('inspecciones.destroy', $inspeccion) }}" method="POST"
                          onsubmit="return confirm('¿Eliminar la inspección {{ $inspeccion->folio }}?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-red-200 text-red-600 text-xs font-semibold rounded-lg hover:bg-red-50 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Eliminar
                        </button>
                    </form>
                </div>
            </div>

            {{-- Datos generales --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-0.5">Fecha de recepción</p>
                    <p class="text-sm font-medium text-gray-800">{{ $inspeccion->fecha_recepcion?->format('d/m/Y') ?? '—' }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-0.5">Requisición</p>
                    <p class="text-sm font-medium text-gray-800">{{ $inspeccion->requisicion ?? '—' }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-0.5">O.C.</p>
                    <p class="text-sm font-medium text-gray-800">{{ $inspeccion->orden_compra ?? '—' }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-0.5">DN / NP / CP / Otro</p>
                    <p class="text-sm font-medium text-gray-800">
                        {{ $inspeccion->tipo_documento === 'Otro'
                            ? ($inspeccion->tipo_documento_otro ?? 'Otro')
                            : ($inspeccion->tipo_documento ?? '—') }}
                    </p>
                </div>
            </div>

            {{-- Dos columnas Solicitante / Calidad --}}
            <div class="grid grid-cols-2 gap-4">

                {{-- Solicitante --}}
                <div class="rounded-xl border border-gray-200 overflow-hidden">
                    <div class="py-2 px-4 text-center text-sm font-bold text-white" style="background-color:#3A8FC0;">
                        Solicitante
                    </div>
                    <div class="p-4 space-y-3" style="background-color:#EBF5FB;">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-0.5">Fecha de inspección</p>
                            <p class="text-sm text-gray-800">{{ $inspeccion->fecha_inspeccion_solicitante?->format('d/m/Y') ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-0.5">Inspeccionó (Nombre)</p>
                            <p class="text-sm text-gray-800">{{ $inspeccion->inspeccionado_solicitante ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-0.5">Departamento</p>
                            <p class="text-sm text-gray-800">{{ $inspeccion->departamento_solicitante ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-0.5">Observaciones</p>
                            <div class="bg-white rounded-lg p-3 text-sm text-gray-800 whitespace-pre-line leading-relaxed min-h-[60px]">{{ $inspeccion->observaciones_solicitante ?? '—' }}</div>
                        </div>
                    </div>
                </div>

                {{-- Control de Calidad --}}
                <div class="rounded-xl border border-gray-200 overflow-hidden">
                    <div class="py-2 px-4 text-center text-sm font-bold text-white" style="background-color:#4E8030;">
                        Control de Calidad
                    </div>
                    <div class="p-4 space-y-3" style="background-color:#EBF5EB;">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-0.5">Fecha de inspección</p>
                            <p class="text-sm text-gray-800">{{ $inspeccion->fecha_inspeccion_calidad?->format('d/m/Y') ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-0.5">Inspeccionó (Nombre)</p>
                            <p class="text-sm text-gray-800">{{ $inspeccion->inspeccionado_calidad ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-0.5">Departamento</p>
                            <p class="text-sm text-gray-800">{{ $inspeccion->departamento_calidad ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-0.5">Observaciones</p>
                            <div class="bg-white rounded-lg p-3 text-sm text-gray-800 whitespace-pre-line leading-relaxed min-h-[60px]">{{ $inspeccion->observaciones_calidad ?? '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sección inferior --}}
            <div class="bg-gray-50 rounded-xl border border-gray-200 p-4 space-y-4">

                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-white rounded-lg p-3">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Requiere Ctrl. Calidad</p>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-sm font-semibold border
                            {{ $inspeccion->requiere_ctrl_calidad
                                ? 'bg-blue-50 text-blue-800 border-blue-200'
                                : 'bg-gray-100 text-gray-600 border-gray-200' }}">
                            {{ $inspeccion->requiere_ctrl_calidad ? 'SÍ' : 'NO' }}
                        </span>
                    </div>
                    <div class="bg-white rounded-lg p-3">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-0.5">No. Solicitud</p>
                        <p class="text-sm font-medium text-gray-800">{{ $inspeccion->no_solicitud ?? '—' }}</p>
                    </div>
                    <div class="bg-white rounded-lg p-3">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-0.5">Fecha ingreso a inventario</p>
                        <p class="text-sm font-medium text-gray-800">{{ $inspeccion->fecha_ingreso_inventario?->format('d/m/Y') ?? '—' }}</p>
                    </div>
                </div>

                {{-- Resultados visuales --}}
                <div class="grid grid-cols-2 gap-4 pt-2 border-t border-gray-200">
                    @foreach([
                        ['Resultado — Solicitante', $inspeccion->resultado_solicitante, $inspeccion->resultado_solicitante_text],
                        ['Resultado — Calidad',     $inspeccion->resultado_calidad,     $inspeccion->resultado_calidad_text],
                    ] as [$titulo, $valor, $texto])
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3 text-center">{{ $titulo }}</p>
                        <div class="flex gap-3 justify-center">
                            @foreach([
                                'no_conforme' => ['No Conforme', 'bg-red-500'],
                                'conforme'    => ['Conforme',    'bg-green-600'],
                                'a_revision'  => ['A Revisión',  'bg-yellow-400'],
                            ] as $v => [$lab, $bg])
                            <div class="flex flex-col items-center gap-1.5">
                                <span class="w-20 h-8 rounded-lg {{ $bg }}
                                    {{ $valor === $v ? 'opacity-100 ring-2 ring-offset-1 ring-gray-400' : 'opacity-20' }}
                                    transition-all"></span>
                                <span class="text-xs {{ $valor === $v ? 'font-semibold text-gray-800' : 'text-gray-400' }}">{{ $lab }}</span>
                            </div>
                            @endforeach
                        </div>
                        @if($valor)
                        <p class="text-center mt-2">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium border {{ $inspeccion->getResultadoBadgeClass($valor) }}">
                                {{ $texto }}
                            </span>
                        </p>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="border-t border-gray-200 px-5 py-3 bg-gray-50">
            <a href="{{ route('inspecciones.index') }}"
               class="text-sm font-medium hover:underline transition"
               style="color:{{ $acento }}">
                ← Volver al listado
            </a>
        </div>
    </div>
</div>
@endsection
