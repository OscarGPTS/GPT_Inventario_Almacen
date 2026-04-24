@extends('layouts.app')

@section('title', 'Inspecciones de Ingreso')

@section('content')
@php $acento = '#4A568D'; @endphp
<div class="space-y-4">

    {{-- Encabezado --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Inspecciones de Ingreso</h1>
            <p class="text-xs text-gray-500 mt-0.5">
                {{ $inspecciones->total() }} registro{{ $inspecciones->total() !== 1 ? 's' : '' }}
                @if(request()->hasAny(['buscar','resultado']))
                    <span class="font-semibold" style="color:{{ $acento }}">· filtrados</span>
                @endif
                · FO-GPT-ALM-01-E
            </p>
        </div>
        <a href="{{ route('inspecciones.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 text-white text-sm font-semibold rounded-xl shadow transition hover:opacity-90 active:scale-95 shrink-0"
           style="background-color:{{ $acento }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nueva Inspección
        </a>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="flex items-center gap-3 px-4 py-3 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm">
        <svg class="w-5 h-5 shrink-0 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Filtros --}}
    <form method="GET" action="{{ route('inspecciones.index') }}"
          class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-4 py-3 flex gap-3 items-center flex-wrap">
            <div class="flex-1 relative min-w-48">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="buscar" value="{{ request('buscar') }}"
                       placeholder="Buscar por folio, requisición, O.C., departamento…"
                       class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition"
                       autocomplete="off">
            </div>
            <select name="resultado"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 bg-white transition
                           {{ request('resultado') ? 'border-indigo-400 bg-indigo-50 font-semibold' : 'text-gray-600' }}"
                    style="{{ request('resultado') ? 'color:'.$acento : '' }}">
                <option value="">Todos los resultados</option>
                <option value="conforme"    {{ request('resultado') === 'conforme'    ? 'selected' : '' }}>Conforme</option>
                <option value="no_conforme" {{ request('resultado') === 'no_conforme' ? 'selected' : '' }}>No Conforme</option>
                <option value="a_revision"  {{ request('resultado') === 'a_revision'  ? 'selected' : '' }}>A Revisión</option>
            </select>
            <button type="submit"
                    class="px-4 py-2 text-white text-sm font-medium rounded-lg transition hover:opacity-90"
                    style="background-color:{{ $acento }}">
                Buscar
            </button>
            @if(request()->hasAny(['buscar','resultado']))
            <a href="{{ route('inspecciones.index') }}"
               class="px-4 py-2 bg-red-50 text-red-600 text-sm font-medium rounded-lg hover:bg-red-100 transition flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Limpiar
            </a>
            @endif
        </div>
    </form>

    {{-- Tabla --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        @if($inspecciones->isEmpty())
        <div class="py-16 text-center">
            <div class="flex flex-col items-center gap-3">
                <div class="w-14 h-14 rounded-full flex items-center justify-center bg-gray-100">
                    <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <p class="text-gray-500 text-sm">No hay inspecciones registradas.</p>
                <a href="{{ route('inspecciones.create') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 text-white text-sm font-semibold rounded-xl shadow transition hover:opacity-90"
                   style="background-color:{{ $acento }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Registrar primera inspección
                </a>
            </div>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm border-collapse">
                <thead>
                    <tr style="background-color:{{ $acento }};">
                        <th class="px-4 py-3 text-left text-white font-semibold text-xs uppercase tracking-wide whitespace-nowrap">Folio</th>
                        <th class="px-4 py-3 text-left text-white font-semibold text-xs uppercase tracking-wide whitespace-nowrap">Recepción</th>
                        <th class="px-4 py-3 text-left text-white font-semibold text-xs uppercase tracking-wide">Requisición</th>
                        <th class="px-4 py-3 text-left text-white font-semibold text-xs uppercase tracking-wide">O.C.</th>
                        <th class="px-4 py-3 text-left text-white font-semibold text-xs uppercase tracking-wide whitespace-nowrap">Tipo Doc.</th>
                        <th class="px-4 py-3 text-center text-white font-semibold text-xs uppercase tracking-wide whitespace-nowrap">Res. Solicitante</th>
                        <th class="px-4 py-3 text-center text-white font-semibold text-xs uppercase tracking-wide whitespace-nowrap">Res. Calidad</th>
                        <th class="px-4 py-3 text-left text-white font-semibold text-xs uppercase tracking-wide whitespace-nowrap">Registrado por</th>
                        <th class="px-4 py-3 text-left text-white font-semibold text-xs uppercase tracking-wide whitespace-nowrap">Fecha</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($inspecciones as $inspeccion)
                    <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }} hover:bg-blue-50 transition-colors">
                        <td class="px-4 py-3 font-mono font-bold whitespace-nowrap" style="color:{{ $acento }}">
                            {{ $inspeccion->folio }}
                        </td>
                        <td class="px-4 py-3 text-gray-700 whitespace-nowrap">
                            {{ $inspeccion->fecha_recepcion?->format('d/m/Y') ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $inspeccion->requisicion ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $inspeccion->orden_compra ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-700 whitespace-nowrap">
                            {{ $inspeccion->tipo_documento === 'Otro'
                                ? ($inspeccion->tipo_documento_otro ?? 'Otro')
                                : ($inspeccion->tipo_documento ?? '—') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($inspeccion->resultado_solicitante)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium border {{ $inspeccion->getResultadoBadgeClass($inspeccion->resultado_solicitante) }}">
                                {{ $inspeccion->resultado_solicitante_text }}
                            </span>
                            @else
                            <span class="text-gray-400 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($inspeccion->resultado_calidad)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium border {{ $inspeccion->getResultadoBadgeClass($inspeccion->resultado_calidad) }}">
                                {{ $inspeccion->resultado_calidad_text }}
                            </span>
                            @else
                            <span class="text-gray-400 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600 whitespace-nowrap text-xs">
                            {{ $inspeccion->registradoPor?->name ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-gray-500 whitespace-nowrap text-xs">
                            {{ $inspeccion->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            <a href="{{ route('inspecciones.show', $inspeccion) }}"
                               class="text-xs font-semibold hover:underline transition"
                               style="color:{{ $acento }}">
                                Ver →
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($inspecciones->hasPages())
        <div class="px-4 py-3 border-t border-gray-100 bg-gray-50">
            {{ $inspecciones->links() }}
        </div>
        @endif
        @endif
    </div>
</div>
@endsection
