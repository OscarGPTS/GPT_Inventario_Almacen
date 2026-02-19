@extends('layouts.app')

@section('title', 'Importar Productos')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Importar Productos desde JSON</h1>
            <p class="text-gray-600 mt-1">Carga un archivo JSON con los datos de productos para importar masivamente</p>
        </div>
        <a href="{{ route('productos.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition">
            <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver
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
                <h3 class="text-sm font-medium text-blue-800">Formato del archivo JSON</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p>El archivo debe contener un array de objetos con los siguientes campos:</p>
                    <ul class="list-disc list-inside mt-2 space-y-1">
                        <li><strong>CODIGO</strong>: Código único del producto</li>
                        <li><strong>COMP.</strong>: Código de componente</li>
                        <li><strong>CAT.</strong>: Código de categoría</li>
                        <li><strong>FAM.</strong>: Código de familia</li>
                        <li><strong>CONS.</strong>: Consecutivo</li>
                        <li><strong>DESCRIPCIÓN</strong>: Descripción del producto</li>
                        <li><strong>UM</strong>: Unidad de medida</li>
                        <li><strong>ENTRADA</strong>: Cantidad de entrada</li>
                        <li><strong>UBIC.</strong>: Ubicación</li>
                        <li><strong>FISICO</strong>: Cantidad física</li>
                        <li><strong>P.U</strong>: Precio unitario</li>
                        <li><strong>MXN/USD</strong>: Moneda (MXN o USD)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Stats -->
    <div id="file-stats" class="hidden bg-green-50 border-l-4 border-green-500 p-4 mb-6">
        <div class="flex items-start">
            <svg class="h-5 w-5 text-green-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <div class="ml-3 flex-1">
                <h3 class="text-sm font-medium text-green-800">Archivo cargado correctamente</h3>
                <div class="mt-2 text-sm text-green-700">
                    <p><strong>Nombre:</strong> <span id="file-name-display"></span></p>
                    <p><strong>Total de registros:</strong> <span id="total-records"></span></p>
                    <p><strong>Tamaño:</strong> <span id="file-size"></span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Table -->
    <div id="preview-section" class="hidden mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-3">Preview (Primeros 10 registros)</h3>
        <div class="overflow-x-auto border rounded-lg">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">Código</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">UM</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">Entrada</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">Físico</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ubicación</th>
                    </tr>
                </thead>
                <tbody id="preview-tbody" class="bg-white divide-y divide-gray-200">
                </tbody>
            </table>
        </div>
    </div>

    <form id="import-form" action="{{ route('productos.import.process') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div>
            <label for="json_file" class="block text-sm font-medium text-gray-700 mb-2">
                Seleccionar archivo JSON
            </label>
            <div class="flex items-center justify-center w-full">
                <label for="json_file" class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition">
                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                        <svg class="w-12 h-12 mb-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Click para seleccionar</span> o arrastra el archivo</p>
                        <p class="text-xs text-gray-500">Archivo JSON (Máximo 100MB)</p>
                    </div>
                    <input id="json_file" name="json_file" type="file" accept=".json,.txt" class="hidden" required onchange="previewFile(this)"/>
                </label>
            </div>
            @error('json_file')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div id="submit-section" class="hidden flex items-center justify-end space-x-3">
            <button type="button" onclick="resetForm()" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2 rounded-md font-medium transition">
                Cancelar
            </button>
            <button type="submit" id="submit-btn" class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-md font-bold transition shadow-lg">
                <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                <span id="submit-text">Importar Productos</span>
            </button>
        </div>
    </form>
</div>

<script>
let totalRecords = 0;

function previewFile(input) {
    const file = input.files[0];
    if (!file) return;

    // Show loading
    document.getElementById('submit-text').textContent = 'Procesando archivo...';
    document.getElementById('submit-btn').disabled = true;

    const reader = new FileReader();
    reader.onload = function(e) {
        try {
            const data = JSON.parse(e.target.result);
            
            if (!Array.isArray(data)) {
                alert('El archivo no contiene un array JSON válido');
                resetForm();
                return;
            }

            totalRecords = data.length;

            // Show stats
            document.getElementById('file-name-display').textContent = file.name;
            document.getElementById('total-records').textContent = totalRecords.toLocaleString();
            document.getElementById('file-size').textContent = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
            document.getElementById('file-stats').classList.remove('hidden');

            // Show preview table
            const previewData = data.slice(0, 10);
            const tbody = document.getElementById('preview-tbody');
            tbody.innerHTML = '';

            previewData.forEach((item, index) => {
                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-50';
                row.innerHTML = `
                    <td class="px-2 py-2 whitespace-nowrap text-gray-500">${index + 1}</td>
                    <td class="px-2 py-2 whitespace-nowrap font-mono">${item.CODIGO || '<span class="text-red-600">VACÍO</span>'}</td>
                    <td class="px-2 py-2 max-w-xs truncate" title="${item['DESCRIPCIÓN'] || ''}">${item['DESCRIPCIÓN'] || 'Sin descripción'}</td>
                    <td class="px-2 py-2 whitespace-nowrap">${item.UM || '-'}</td>
                    <td class="px-2 py-2 whitespace-nowrap text-center">${item.ENTRADA || 0}</td>
                    <td class="px-2 py-2 whitespace-nowrap text-center">${item.FISICO || 0}</td>
                    <td class="px-2 py-2 whitespace-nowrap">${item['UBIC.'] || '-'}</td>
                `;
                tbody.appendChild(row);
            });

            document.getElementById('preview-section').classList.remove('hidden');
            document.getElementById('submit-section').classList.remove('hidden');
            
            // Update button
            document.getElementById('submit-text').textContent = `Importar ${totalRecords.toLocaleString()} Productos`;
            document.getElementById('submit-btn').disabled = false;

        } catch (error) {
            alert('Error al leer el archivo: ' + error.message);
            resetForm();
        }
    };

    reader.readAsText(file);
}

function resetForm() {
    document.getElementById('import-form').reset();
    document.getElementById('file-stats').classList.add('hidden');
    document.getElementById('preview-section').classList.add('hidden');
    document.getElementById('submit-section').classList.add('hidden');
    document.getElementById('submit-text').textContent = 'Importar Productos';
    document.getElementById('submit-btn').disabled = false;
    totalRecords = 0;
}

// Confirm before submit
document.getElementById('import-form').onsubmit = function(e) {
    if (!confirm(`¿Estás seguro de importar ${totalRecords.toLocaleString()} productos? Esta acción puede tomar varios minutos.`)) {
        e.preventDefault();
        return false;
    }
    document.getElementById('submit-btn').disabled = true;
    document.getElementById('submit-text').textContent = 'Importando... Por favor espera';
};
</script>
@endsection
