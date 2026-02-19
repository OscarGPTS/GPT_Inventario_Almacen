@extends('layouts.app')

@section('title', 'Preview de Importación')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Preview de Importación</h1>
            <p class="text-gray-600 mt-1">Revisa los datos antes de confirmar la importación</p>
        </div>
        <a href="{{ route('productos.import') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition">
            <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Cancelar
        </a>
    </div>

    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Información del archivo</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p><strong>Total de registros a importar:</strong> {{ number_format($total) }}</p>
                    <p class="mt-1"><strong>Mostrando:</strong> Primeros {{ count($preview) }} registros como muestra</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Table -->
    <div class="overflow-x-auto mb-6">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Código</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Comp.</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cat.</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fam.</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">UM</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Entrada</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Físico</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ubicación</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">P.U.</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($preview as $index => $item)
                <tr class="hover:bg-gray-50">
                    <td class="px-3 py-2 whitespace-nowrap text-gray-500">{{ $index + 1 }}</td>
                    <td class="px-3 py-2 whitespace-nowrap font-mono text-xs">
                        @if(empty($item['CODIGO']))
                            <span class="text-red-600 font-bold">VACÍO</span>
                        @else
                            {{ $item['CODIGO'] }}
                        @endif
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap">{{ $item['COMP.'] ?? '-' }}</td>
                    <td class="px-3 py-2 whitespace-nowrap">{{ $item['CAT.'] ?? '-' }}</td>
                    <td class="px-3 py-2 whitespace-nowrap">{{ $item['FAM.'] ?? '-' }}</td>
                    <td class="px-3 py-2 max-w-xs truncate" title="{{ $item['DESCRIPCIÓN'] ?? 'Sin descripción' }}">
                        {{ $item['DESCRIPCIÓN'] ?? 'Sin descripción' }}
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap">{{ $item['UM'] ?? '-' }}</td>
                    <td class="px-3 py-2 whitespace-nowrap text-center">{{ $item['ENTRADA'] ?? 0 }}</td>
                    <td class="px-3 py-2 whitespace-nowrap text-center">{{ $item['FISICO'] ?? 0 }}</td>
                    <td class="px-3 py-2 whitespace-nowrap">{{ $item['UBIC.'] ?? '-' }}</td>
                    <td class="px-3 py-2 whitespace-nowrap">
                        @if(!empty($item['P.U']))
                            ${{ number_format($item['P.U'], 2) }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($total > count($preview))
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700">
                    Hay <strong>{{ number_format($total - count($preview)) }}</strong> registros más que no se muestran en el preview. Todos serán importados al confirmar.
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Confirmation Form -->
    <form action="{{ route('productos.import.process') }}" method="POST" onsubmit="return confirm('¿Estás seguro de importar {{ number_format($total) }} productos? Esta acción puede tomar varios minutos.');">
        @csrf
        
        <div class="flex items-center justify-between border-t pt-6">
            <a href="{{ route('productos.import') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-md font-medium transition">
                ← Volver a seleccionar archivo
            </a>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-md font-bold transition shadow-lg transform hover:scale-105">
                <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Confirmar e Importar {{ number_format($total) }} Productos
            </button>
        </div>
    </form>
</div>

@if(session('import_errors'))
<div class="bg-white rounded-lg shadow-md p-6 mt-6">
    <h3 class="text-lg font-bold text-red-600 mb-4">Errores de importación anterior</h3>
    <div class="bg-red-50 border border-red-200 rounded p-4 max-h-96 overflow-y-auto">
        <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
            @foreach(session('import_errors') as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif
@endsection
