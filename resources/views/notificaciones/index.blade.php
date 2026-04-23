@extends('layouts.app')
@section('title', 'Notificaciones')
@section('content')
@php
    $acento = '#4A568D';

    $iconMap = [
        'ticket_pending_approval' => ['color' => '#7C3AED', 'icon' => 'clock'],
        'ticket_assigned'         => ['color' => '#3B82F6', 'icon' => 'ticket'],
        'ticket_assigned_to_you'  => ['color' => '#0d7a6b', 'icon' => 'ticket'],
        'ticket_completed'        => ['color' => '#16a34a', 'icon' => 'check'],
        'survey_completed'        => ['color' => '#D97706', 'icon' => 'star'],
        'nueva_solicitud'         => ['color' => '#4A568D', 'icon' => 'doc'],
    ];
@endphp

<div class="space-y-4 max-w-3xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Notificaciones</h1>
            <p class="text-xs text-gray-400 mt-0.5">Historial completo de notificaciones</p>
        </div>
        @if(auth()->user()->unreadNotifications()->count() > 0)
        <form method="POST" action="{{ route('notificaciones.leerTodas') }}">
            @csrf
            <button type="submit"
                class="text-sm font-medium text-white px-4 py-2 rounded-xl transition hover:opacity-90"
                style="background-color:{{ $acento }}">
                Marcar todo como leído
            </button>
        </form>
        @endif
    </div>

    {{-- Lista --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm divide-y divide-gray-100">
        @forelse($notificaciones as $notif)
        @php
            $isUnread  = is_null($notif->read_at);
            $data      = $notif->data;
            $type      = $data['type'] ?? 'general';
            $msg       = $data['message'] ?? 'Notificación';
            $codigo    = $data['codigo'] ?? ($data['folio'] ?? null);
            $ticketId  = $data['ticket_id'] ?? null;
            $meta      = $iconMap[$type] ?? ['color' => '#4A568D', 'icon' => 'doc'];
            $color     = $meta['color'];
            $destUrl   = '';
            if ($ticketId) {
                $destUrl = route('solicitudes.concentrado') . '?tab=movimiento';
            } elseif ($type === 'nueva_solicitud') {
                $destUrl = route('solicitudes.concentrado') . '?tab=material';
            }
            $linkLabel = match($type) {
                'nueva_solicitud'  => 'Ver requisición',
                'survey_completed' => 'Ver encuesta',
                default            => 'Ver solicitud',
            };
        @endphp
        <div class="flex gap-4 px-5 py-4 {{ $isUnread ? 'bg-blue-50/50' : '' }} hover:bg-gray-50 transition notif-row"
             data-id="{{ $notif->id }}"
             data-url="{{ $destUrl }}">

            {{-- Icono --}}
            <div class="flex-shrink-0 mt-0.5">
                <div class="w-10 h-10 rounded-full flex items-center justify-center"
                     style="background-color:{{ $color }}1A;">
                    @if($meta['icon'] === 'check')
                    <svg class="w-5 h-5" fill="none" stroke="{{ $color }}" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    @elseif($meta['icon'] === 'star')
                    <svg class="w-5 h-5" fill="{{ $color }}" viewBox="0 0 24 24">
                        <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                    @elseif($meta['icon'] === 'clock')
                    <svg class="w-5 h-5" fill="none" stroke="{{ $color }}" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    @else
                    <svg class="w-5 h-5" fill="none" stroke="{{ $color }}" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                    </svg>
                    @endif
                </div>
            </div>

            {{-- Contenido --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        @if($codigo)
                        <span class="text-xs font-bold" style="color:{{ $color }}">{{ $codigo }}</span>
                        @endif
                        <p class="text-sm text-gray-800 leading-snug {{ $isUnread ? 'font-semibold' : '' }} mt-0.5">{{ $msg }}</p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        @if($isUnread)
                        <div class="w-2 h-2 rounded-full bg-blue-500 notif-dot"></div>
                        @endif
                        <span class="text-[11px] text-gray-400 whitespace-nowrap">{{ $notif->created_at->diffForHumans() }}</span>
                        <button type="button"
                            onclick="event.stopPropagation(); eliminarNotif('{{ $notif->id }}', this)"
                            class="ml-1 text-gray-300 hover:text-red-400 transition p-0.5 rounded shrink-0"
                            title="Eliminar notificación">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
                @if($destUrl)
                <a href="{{ $destUrl }}" onclick="marcarLeida('{{ $notif->id }}')"
                   class="inline-flex items-center gap-1 mt-1.5 text-xs font-medium transition"
                   style="color:{{ $color }}">
                    {{ $linkLabel }}
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                @endif
            </div>
        </div>
        @empty
        <div class="px-5 py-16 text-center">
            <svg class="w-12 h-12 mx-auto text-gray-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <p class="text-gray-400 text-sm">No tienes notificaciones</p>
        </div>
        @endforelse
    </div>

    {{-- Paginación --}}
    @if($notificaciones->hasPages())
    <div>{{ $notificaciones->links() }}</div>
    @endif

</div>

<script>
const CSRF_N = document.querySelector('meta[name="csrf-token"]')?.content;

function marcarLeida(id) {
    fetch(`/notificaciones/${id}/leer`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF_N, 'Content-Type': 'application/json' }
    }).catch(() => {});
}

// Marcar como leídas al hacer clic en cualquier fila no leída
document.querySelectorAll('.notif-row').forEach(row => {
    row.style.cursor = 'pointer';
    row.addEventListener('click', function(e) {
        // Don't double-trigger if clicking the "Ver solicitud" link or delete button
        if (e.target.closest('a') || e.target.closest('button')) return;
        const id  = this.dataset.id;
        const url = this.dataset.url;
        if (this.classList.contains('bg-blue-50/50')) {
            marcarLeida(id);
            this.classList.remove('bg-blue-50/50');
            this.querySelector('.notif-dot')?.remove();
        }
        if (url) window.location.href = url;
    });
});

function eliminarNotif(id, btn) {
    const row = btn.closest('.notif-row');
    fetch(`/notificaciones/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF_N, 'Content-Type': 'application/json' }
    }).then(r => {
        if (r.ok) row.remove();
    }).catch(() => {});
}
</script>
@endsection
