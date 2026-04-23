@extends('layouts.app')
@section('title', 'Nueva Solicitud de Movimiento')
@section('content')
@php $acento = '#4A568D'; @endphp

<div class="max-w-3xl mx-auto space-y-4">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('tickets.index') }}" class="text-gray-500 hover:text-gray-700 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-800">Nueva Solicitud de Movimiento</h1>
            <p class="text-xs text-gray-500 mt-0.5">Describe detalladamente tu solicitud</p>
        </div>
    </div>

    {{-- Errores --}}
    @if($errors->any())
    <div class="px-4 py-3 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Formulario --}}
    <form action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data"
          class="bg-white rounded-xl border border-gray-200 shadow-sm">
        @csrf

        <div class="px-6 py-5 space-y-5">

            @if($producto)
            <div class="flex items-center gap-3 px-4 py-3 rounded-xl border" style="background-color: #eef0f8; border-color: #c7cfe7;">
                <svg class="w-5 h-5 shrink-0" style="color:{{ $acento }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <div class="text-sm">
                    <span class="text-gray-500">Producto:</span>
                    <span class="font-mono font-bold" style="color:{{ $acento }}">{{ $producto->codigo }}</span>
                    <span class="text-gray-700">— {{ $producto->descripcion }}</span>
                </div>
            </div>
            <input type="hidden" name="producto_id" value="{{ $producto->id }}">
            @endif

            {{-- Título --}}
            <div>
                <label for="title" class="block text-xs font-semibold text-gray-600 mb-1">
                    Título de la solicitud <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required
                       placeholder="Ej: Movimiento de piezas del almacén A a zona de producción"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 transition">
            </div>

            {{-- Descripción --}}
            <div>
                <label for="description" class="block text-xs font-semibold text-gray-600 mb-1">
                    Descripción detallada <span class="text-red-500">*</span>
                </label>
                <textarea name="description" id="description" rows="5" required
                    placeholder="Incluye: tipo de material, cantidad, origen, destino, instrucciones especiales..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 resize-none transition">{{ old('description') }}</textarea>
                <p class="text-xs text-gray-400 mt-1">Máximo 5,000 caracteres</p>
            </div>

            {{-- Imágenes --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">
                    Imágenes de referencia <span class="text-gray-400">(opcional, máx. 5)</span>
                </label>
                <div id="dropZone"
                     class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-indigo-400 transition cursor-pointer">
                    <svg class="mx-auto w-10 h-10 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-sm text-gray-500">Haz clic o arrastra imágenes aquí</p>
                    <p class="text-xs text-gray-400 mt-1">PNG, JPG, JPEG, GIF, WEBP — máx. 2 MB c/u</p>
                    <input type="file" name="images[]" id="images" multiple accept="image/*" class="hidden">
                </div>
                <div id="imageCounter" class="hidden mt-2 text-xs text-gray-500">
                    <span id="counterText">0 / 5 imágenes</span>
                </div>
                <div id="imagePreview" class="mt-3 grid grid-cols-3 sm:grid-cols-5 gap-2 hidden"></div>
            </div>
        </div>

        {{-- Botones --}}
        <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3 bg-gray-50 rounded-b-xl">
            <a href="{{ route('tickets.index') }}"
               class="px-5 py-2 bg-white border border-gray-300 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-50 transition">
                Cancelar
            </a>
            <button type="submit"
                    class="px-6 py-2 text-white text-sm font-semibold rounded-xl transition hover:opacity-90 flex items-center gap-2"
                    style="background-color:{{ $acento }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
                Enviar Solicitud
            </button>
        </div>
    </form>
</div>

<script>
(function() {
    const dropZone = document.getElementById('dropZone');
    const input    = document.getElementById('images');
    const preview  = document.getElementById('imagePreview');
    const counter  = document.getElementById('imageCounter');
    const counterText = document.getElementById('counterText');

    dropZone.addEventListener('click', () => input.click());

    dropZone.addEventListener('dragover', e => {
        e.preventDefault();
        dropZone.classList.add('border-indigo-400', 'bg-indigo-50');
    });
    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('border-indigo-400', 'bg-indigo-50');
    });
    dropZone.addEventListener('drop', e => {
        e.preventDefault();
        dropZone.classList.remove('border-indigo-400', 'bg-indigo-50');
        input.files = e.dataTransfer.files;
        renderPreview();
    });

    input.addEventListener('change', renderPreview);

    function renderPreview() {
        preview.innerHTML = '';
        const files = input.files;
        const count = Math.min(files.length, 5);

        if (count === 0) {
            preview.classList.add('hidden');
            counter.classList.add('hidden');
            return;
        }

        preview.classList.remove('hidden');
        counter.classList.remove('hidden');
        counterText.textContent = count + ' / 5 imágenes';

        for (let i = 0; i < count; i++) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative group';
                div.innerHTML = `<img src="${e.target.result}" class="w-full h-20 object-cover rounded-lg border border-gray-200">
                    <span class="absolute bottom-0.5 left-0.5 right-0.5 text-center text-[10px] text-white bg-black/50 rounded-b-lg px-1 truncate">${files[i].name}</span>`;
                preview.appendChild(div);
            };
            reader.readAsDataURL(files[i]);
        }
    }
})();
</script>
@endsection
