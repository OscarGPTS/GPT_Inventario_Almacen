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
        {{-- Totales --}}
        <div class="flex gap-4">
            <div class="text-right">
                <p class="text-xs text-gray-400 uppercase tracking-wide">Total Físico</p>
                <p class="text-lg font-bold text-gray-800">{{ number_format($totales['sum_fisico'], 2) }}</p>
            </div>
            <div class="text-right border-l border-gray-200 pl-4">
                <p class="text-xs text-gray-400 uppercase tracking-wide">Total P.U</p>
                <p class="text-lg font-bold" style="color:{{ $acento }}">$ {{ number_format($totales['sum_pu'], 2) }}</p>
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
                    placeholder="Buscar por código o descripción..."
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
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide border-r border-indigo-600" style="min-width:220px;">DESCRIPCIÓN</th>
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
                {{-- Pie con totales de la página actual --}}
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
@endsection
