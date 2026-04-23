@extends('layouts.app')
@section('title', 'Ticket ' . $ticket->formatted_code)
@section('content')
@php
    $acento   = '#4A568D';
    $user     = auth()->user();
    $esGestor = $user->hasRole(['admin', 'admin_almacen']);
    $esAlmacenista = $user->hasRole(['almacenista', 'admin_almacen']);
    $esDueño  = $ticket->user_id === $user->id;
    $badgeMap = [
        'pendiente'  => 'bg-yellow-100 text-yellow-800 border-yellow-200',
        'en_proceso' => 'bg-blue-100 text-blue-800 border-blue-200',
        'finalizado' => 'bg-green-100 text-green-800 border-green-200',
        'cancelado'  => 'bg-red-100 text-red-800 border-red-200',
    ];
@endphp

<div class="max-w-4xl mx-auto space-y-5">

    {{-- Flash --}}
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
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('tickets.index') }}" class="text-gray-500 hover:text-gray-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <p class="text-xs text-gray-500">Ticket <span class="font-mono font-bold" style="color:{{ $acento }}">{{ $ticket->formatted_code }}</span></p>
                <h1 class="text-xl font-bold text-gray-800">{{ $ticket->title }}</h1>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold border {{ $badgeMap[$ticket->status] ?? '' }}">
                {{ $ticket->status_text }}
            </span>

            @if($esDueño && $ticket->isPendiente())
            <form method="POST" action="{{ route('tickets.destroy', $ticket) }}" onsubmit="return confirm('¿Eliminar esta solicitud?')">
                @csrf @method('DELETE')
                <button type="submit" class="px-3 py-1.5 text-xs font-medium rounded-lg border border-red-300 text-red-600 hover:bg-red-50 transition">
                    Eliminar
                </button>
            </form>
            @endif

            @if(($esDueño || $esGestor) && $ticket->canBeCancelled())
            <button type="button" onclick="document.getElementById('modalCancelar').classList.remove('hidden');document.getElementById('modalCancelar').classList.add('flex');"
                    class="px-3 py-1.5 text-xs font-medium rounded-lg border border-red-300 text-red-600 hover:bg-red-50 transition">
                Cancelar Ticket
            </button>
            @endif
        </div>
    </div>

    {{-- Stepper --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <div class="flex items-center justify-between text-xs">
            @php
                $steps = [
                    ['label' => 'Creado', 'done' => true, 'date' => $ticket->created_at],
                    ['label' => 'Asignado', 'done' => (bool)$ticket->assigned_at, 'date' => $ticket->assigned_at, 'extra' => $ticket->assignedTo->name ?? null],
                    ['label' => $ticket->isCancelado() ? 'Cancelado' : 'Completado', 'done' => $ticket->isFinalizado() || $ticket->isCancelado(), 'date' => $ticket->completed_at ?? $ticket->cancelled_at],
                ];
            @endphp
            @foreach($steps as $i => $step)
            <div class="flex-1 flex items-center {{ $i < count($steps) - 1 ? '' : '' }}">
                <div class="flex flex-col items-center text-center flex-1">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $step['done'] ? 'text-white' : 'bg-gray-200 text-gray-500' }}"
                         style="{{ $step['done'] ? 'background-color:'.$acento : '' }}">
                        @if($step['done'])
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        @else
                        <span class="text-xs font-bold">{{ $i + 1 }}</span>
                        @endif
                    </div>
                    <span class="mt-1 font-semibold {{ $step['done'] ? 'text-gray-800' : 'text-gray-400' }}">{{ $step['label'] }}</span>
                    @if($step['date'])
                    <span class="text-gray-400">{{ $step['date']->format('d/m/Y H:i') }}</span>
                    @endif
                    @if(!empty($step['extra']))
                    <span class="text-gray-500 font-medium">{{ $step['extra'] }}</span>
                    @endif
                </div>
                @if($i < count($steps) - 1)
                <div class="flex-1 h-0.5 {{ $steps[$i+1]['done'] ? '' : 'bg-gray-200' }}" style="{{ $steps[$i+1]['done'] ? 'background-color:'.$acento : '' }}"></div>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    {{-- Info --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-bold text-gray-800">Información de la Solicitud</h2>
        </div>
        <div class="px-6 py-4 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                <div class="bg-gray-50 rounded-lg p-3">
                    <span class="text-gray-500">Solicitante</span>
                    <p class="font-medium text-gray-800">{{ $ticket->user->name ?? '—' }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <span class="text-gray-500">Creado el</span>
                    <p class="font-medium text-gray-800">{{ $ticket->created_at->format('d/m/Y H:i') }}</p>
                </div>
                @if($ticket->producto)
                <div class="bg-gray-50 rounded-lg p-3 sm:col-span-2">
                    <span class="text-gray-500">Producto relacionado</span>
                    <p class="font-medium text-gray-800">
                        <a href="{{ route('productos.show', $ticket->producto->id) }}" class="hover:underline" style="color:{{ $acento }}">
                            {{ $ticket->producto->codigo }}
                        </a>
                        — {{ $ticket->producto->descripcion }}
                    </p>
                </div>
                @endif
                @if($ticket->assignedTo)
                <div class="bg-gray-50 rounded-lg p-3">
                    <span class="text-gray-500">Asignado a</span>
                    <p class="font-medium text-gray-800">{{ $ticket->assignedTo->name }}</p>
                </div>
                @endif
                @if($ticket->completed_at)
                <div class="bg-gray-50 rounded-lg p-3">
                    <span class="text-gray-500">Completado el</span>
                    <p class="font-medium text-gray-800">{{ $ticket->completed_at->format('d/m/Y H:i') }}</p>
                </div>
                @endif
            </div>

            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Descripción</p>
                <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-800 whitespace-pre-line leading-relaxed">{{ $ticket->description }}</div>
            </div>

            {{-- Imágenes de solicitud --}}
            @if($ticket->solicitudImages->count())
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Imágenes adjuntas</p>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @foreach($ticket->solicitudImages as $img)
                    <a href="{{ $img->url }}" target="_blank" class="block group">
                        <img src="{{ $img->url }}" alt="{{ $img->original_name }}"
                             class="w-full h-28 object-cover rounded-lg border border-gray-200 group-hover:ring-2 group-hover:ring-indigo-400 transition">
                        <span class="text-[10px] text-gray-400 truncate block mt-1">{{ $img->original_name }}</span>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Cancelación --}}
    @if($ticket->isCancelado())
    <div class="bg-red-50 border border-red-200 rounded-xl p-5">
        <h3 class="text-sm font-bold text-red-800 mb-1">Ticket Cancelado</h3>
        <p class="text-sm text-red-700">{{ $ticket->cancellation_reason ?: 'Sin motivo especificado.' }}</p>
    </div>
    @endif

    {{-- Evidencia de trabajo --}}
    @if($ticket->isFinalizado() && $ticket->work_evidence)
    <div class="bg-green-50 border border-green-200 rounded-xl p-5">
        <h3 class="text-sm font-bold text-green-800 mb-2">Evidencia del Trabajo Realizado</h3>
        <p class="text-sm text-green-900 whitespace-pre-line">{{ $ticket->work_evidence }}</p>

        @if($ticket->evidenciaImages->count())
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-3">
            @foreach($ticket->evidenciaImages as $img)
            <a href="{{ $img->url }}" target="_blank" class="block group">
                <img src="{{ $img->url }}" alt="{{ $img->original_name }}"
                     class="w-full h-28 object-cover rounded-lg border border-green-200 group-hover:ring-2 group-hover:ring-green-400 transition">
            </a>
            @endforeach
        </div>
        @endif
    </div>
    @endif

    {{-- ACCIONES PARA GESTORES / ALMACENISTAS --}}
    @if(($esGestor || $esAlmacenista) && !$ticket->isFinalizado() && !$ticket->isCancelado())
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-bold text-gray-800">Acciones</h2>
        </div>
        <div class="px-6 py-4 space-y-5">

            {{-- Asignar --}}
            @if($ticket->isPendiente() || ($esGestor && $ticket->isEnProceso()))
            <form method="POST" action="{{ route('tickets.assign', $ticket) }}" class="flex items-end gap-3 flex-wrap">
                @csrf
                @if($esGestor && $almacenUsers)
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Asignar a</label>
                    <select name="assigned_to" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">Seleccionar almacenista...</option>
                        @foreach($almacenUsers as $au)
                        <option value="{{ $au->id }}" {{ $ticket->assigned_to == $au->id ? 'selected' : '' }}>{{ $au->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <button type="submit"
                        class="px-5 py-2 text-white text-sm font-semibold rounded-xl transition hover:opacity-90"
                        style="background-color:{{ $acento }}">
                    {{ $esGestor ? 'Asignar' : 'Tomar Ticket' }}
                </button>
            </form>
            @endif

            {{-- Completar --}}
            @if($ticket->isEnProceso() || ($esGestor && $ticket->isPendiente()))
            <div class="border-t border-gray-100 pt-5">
                <h3 class="text-xs font-semibold text-gray-600 mb-3 uppercase tracking-wide">Completar Ticket</h3>
                <form method="POST" action="{{ route('tickets.complete', $ticket) }}" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Evidencia del trabajo <span class="text-red-500">*</span></label>
                        <textarea name="work_evidence" rows="3" required
                                  placeholder="Describe el trabajo realizado..."
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Fotos de evidencia <span class="text-gray-400">(opcional)</span></label>
                        <input type="file" name="evidence_images[]" multiple accept="image/*"
                               class="block w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>
                    <div class="flex justify-end">
                        <button type="submit"
                                class="px-6 py-2 bg-green-600 text-white text-sm font-semibold rounded-xl transition hover:bg-green-700 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Marcar como Completado
                        </button>
                    </div>
                </form>
            </div>
            @endif

        </div>
    </div>
    @endif

    {{-- ENCUESTA DE SATISFACCIÓN --}}
    @if($ticket->isFinalizado() && $esDueño && !$ticket->survey)
    <div class="bg-amber-50 border-2 border-amber-300 rounded-xl p-5">
        <h3 class="text-sm font-bold text-amber-800 mb-1">📋 Encuesta de Satisfacción</h3>
        <p class="text-sm text-amber-700 mb-4">Tu opinión es importante. Califica el servicio recibido para ayudarnos a mejorar.</p>
        <form method="POST" action="{{ route('tickets.survey', $ticket) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-2">Calificación <span class="text-red-500">*</span></label>
                <div class="flex gap-2" id="starRating">
                    @for($i = 1; $i <= 5; $i++)
                    <button type="button" onclick="setRating({{ $i }})"
                            class="star-btn w-10 h-10 rounded-lg border-2 border-gray-200 text-gray-300 text-xl flex items-center justify-center hover:border-amber-400 hover:text-amber-400 transition"
                            data-star="{{ $i }}">★</button>
                    @endfor
                </div>
                <input type="hidden" name="rating" id="ratingInput" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Comentarios <span class="text-gray-400">(opcional)</span></label>
                <textarea name="comments" rows="2"
                          placeholder="¿Algún comentario sobre el servicio?"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 resize-none"></textarea>
            </div>
            <div class="flex justify-end">
                <button type="submit"
                        class="px-6 py-2 bg-amber-500 text-white text-sm font-semibold rounded-xl transition hover:bg-amber-600 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Enviar Calificación
                </button>
            </div>
        </form>
    </div>

    <script>
    function setRating(val) {
        document.getElementById('ratingInput').value = val;
        document.querySelectorAll('.star-btn').forEach(btn => {
            const s = parseInt(btn.dataset.star);
            if (s <= val) {
                btn.classList.remove('border-gray-200', 'text-gray-300');
                btn.classList.add('border-amber-400', 'text-amber-400', 'bg-amber-50');
            } else {
                btn.classList.remove('border-amber-400', 'text-amber-400', 'bg-amber-50');
                btn.classList.add('border-gray-200', 'text-gray-300');
            }
        });
    }
    </script>
    @endif

    {{-- ENCUESTA YA RESPONDIDA --}}
    @if($ticket->isFinalizado() && $ticket->survey)
    <div class="bg-green-50 border border-green-200 rounded-xl p-5">
        <h3 class="text-sm font-bold text-green-800 mb-2">Encuesta de Satisfacción</h3>
        <div class="flex items-center gap-3 mb-2">
            <div class="flex gap-0.5 text-xl">
                @for($i = 1; $i <= 5; $i++)
                <span class="{{ $i <= $ticket->survey->rating ? 'text-amber-400' : 'text-gray-300' }}">★</span>
                @endfor
            </div>
            <span class="text-sm font-semibold text-gray-700">{{ $ticket->survey->rating }}/5</span>
            <span class="text-xs text-gray-400">· {{ $ticket->survey->completed_at?->format('d/m/Y H:i') }}</span>
        </div>
        @if($ticket->survey->comments)
        <p class="text-sm text-green-900 mt-1">{{ $ticket->survey->comments }}</p>
        @endif
    </div>
    @endif
</div>

{{-- Modal cancelar --}}
@if(($esDueño || $esGestor) && $ticket->canBeCancelled())
<div id="modalCancelar" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background:rgba(0,0,0,0.45);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 rounded-t-2xl text-white bg-red-600">
            <span class="font-semibold">Cancelar Ticket</span>
            <button onclick="document.getElementById('modalCancelar').classList.replace('flex','hidden');" class="text-white opacity-70 hover:opacity-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('tickets.cancel', $ticket) }}">
            @csrf
            <div class="px-6 py-5">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Motivo de cancelación</label>
                <textarea name="cancellation_reason" rows="3"
                          placeholder="Opcional — describe el motivo..."
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 resize-none"></textarea>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3 bg-gray-50 rounded-b-2xl">
                <button type="button" onclick="document.getElementById('modalCancelar').classList.replace('flex','hidden');"
                        class="px-5 py-2 bg-white border border-gray-300 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-50 transition">
                    Volver
                </button>
                <button type="submit"
                        class="px-6 py-2 bg-red-600 text-white text-sm font-semibold rounded-xl transition hover:bg-red-700">
                    Confirmar Cancelación
                </button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
