@extends('layouts.app')
@section('title', 'Resguardo de Almacén')
@section('content')
@php $acento = '#4A568D'; @endphp

<div class="space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Resguardo de Almacén</h1>
            <p class="text-xs text-gray-500 mt-0.5">{{ number_format($registros->total()) }} registros</p>
        </div>
    </div>

    <form method="GET" action="{{ route('reportes.resguardo') }}" class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-4 py-3 flex gap-3 items-center">
            <div class="flex-1 relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Buscar por descripción, código, observaciones..."
                    class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 transition"
                    autocomplete="off">
            </div>
            <button type="submit" class="px-4 py-2 text-white text-sm font-medium rounded-lg transition" style="background-color:{{ $acento }}">Buscar</button>
            @if(request('search'))
            <a href="{{ route('reportes.resguardo') }}" class="px-3 py-2 bg-gray-100 text-gray-600 text-sm rounded-lg hover:bg-gray-200 transition">✕</a>
            @endif
        </div>
    </form>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto" style="max-height:calc(100vh - 240px);overflow-y:auto;">
            <table class="w-full text-xs border-collapse" style="min-width:1200px;">
                <thead class="sticky top-0 z-10">
                    <tr style="background-color:{{ $acento }};">
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide border-r border-indigo-600" style="min-width:200px;">DESCRIPCIÓN</th>
                        <th class="px-3 py-2.5 text-center text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">UM</th>
                        <th class="px-3 py-2.5 text-right text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">ENTRADA</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">UBIC.</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">FECHA ENTRADA</th>
                        <th class="px-3 py-2.5 text-right text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">SALIDA</th>
                        <th class="px-3 py-2.5 text-right text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">FISICO</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">FECHA SALIDA</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">DN / NP</th>
                        <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap">NOMBRE DEL CLIENTE</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($registros as $p)
                    <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }} hover:bg-blue-50 transition-colors">
                        <td class="px-3 py-2 text-gray-800">
                            <a href="{{ route('productos.show', $p->id) }}" class="hover:underline" style="color:{{ $acento }}">{{ $p->descripcion }}</a>
                        </td>
                        <td class="px-3 py-2 text-gray-700 text-center whitespace-nowrap">{{ $p->unidadMedida->codigo ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-800 text-right whitespace-nowrap">{{ $p->cantidad_entrada !== null ? number_format($p->cantidad_entrada, 2) : '—' }}</td>
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $p->ubicacion->codigo ?? '—' }}</td>
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $p->fecha_entrada ? $p->fecha_entrada->format('d/m/Y') : '—' }}</td>
                        <td class="px-3 py-2 text-gray-800 text-right whitespace-nowrap">{{ $p->cantidad_salida !== null ? number_format($p->cantidad_salida, 2) : '—' }}</td>
                        <td class="px-3 py-2 text-right whitespace-nowrap font-semibold {{ ($p->cantidad_fisica !== null && $p->cantidad_fisica < 10) ? 'text-red-600' : 'text-gray-800' }}">
                            {{ $p->cantidad_fisica !== null ? number_format($p->cantidad_fisica, 2) : '—' }}
                        </td>
                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap">{{ $p->fecha_salida ? $p->fecha_salida->format('d/m/Y') : '—' }}</td>
                        <td class="px-3 py-2 text-gray-600 whitespace-nowrap">{{ $p->observaciones ? \Str::limit($p->observaciones, 25) : '—' }}</td>
                        <td class="px-3 py-2 text-gray-400 whitespace-nowrap">—</td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="px-6 py-12 text-center text-gray-400">No hay registros de resguardo</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @include('reportes._paginacion', ['items' => $registros, 'acento' => $acento])
    </div>
</div>
@endsection
