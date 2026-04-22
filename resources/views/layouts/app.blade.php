<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Inventario Almacén')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    @auth
    @php $esVisitante = auth()->user()->hasRole('visitante'); @endphp
    <nav class="bg-white shadow-md border-b border-gray-200 sticky top-0 z-50">
        <div class="w-full px-4 sm:px-6">
            <div class="flex justify-between items-center" style="height:56px;">
                {{-- Logo --}}
                <div class="flex-shrink-0 flex items-center gap-2 pr-4 border-r border-gray-200">
                    @if(file_exists(public_path('storage/img/logo_gpt.svg')))
                    <a href="{{ route('reportes.entradas') }}" >
                        <img src="{{ asset('storage/img/logo_gpt.svg') }}" alt="Logo" class="h-8 w-auto">
                    </a>
                    @endif
                    <a href="{{ route('reportes.entradas') }}" class="text-base font-bold text-gray-800 hover:text-gray-600 transition whitespace-nowrap">
                        Inventario Almacén
                    </a>
                </div>

                {{-- Links principales (Desktop) --}}
                <div class="hidden lg:flex items-center gap-1 ml-2 flex-1 overflow-x-auto">
                    @if(!$esVisitante)
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center gap-1.5 text-gray-600 hover:bg-gray-100 hover:text-gray-900 px-3 py-1.5 rounded-md text-sm font-medium transition whitespace-nowrap {{ request()->routeIs('dashboard') ? 'bg-gray-100 text-gray-900' : '' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Dashboard
                    </a>
                    @endif
                    <a href="{{ route('reportes.entradas') }}"
                       class="flex items-center gap-1.5 text-gray-600 hover:bg-gray-100 hover:text-gray-900 px-3 py-1.5 rounded-md text-sm font-medium transition whitespace-nowrap {{ request()->routeIs('reportes.entradas') ? 'bg-gray-100 text-gray-900' : '' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        Inventario
                    </a>
                    <a href="{{ route('solicitudes.concentrado') }}"
                       class="flex items-center gap-1.5 text-gray-600 hover:bg-gray-100 hover:text-gray-900 px-3 py-1.5 rounded-md text-sm font-medium transition whitespace-nowrap {{ request()->routeIs('solicitudes.concentrado') || request()->routeIs('tickets.*') || request()->routeIs('reportes.requisiciones') ? 'bg-gray-100 text-gray-900' : '' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                        </svg>
                        Solicitudes
                    </a>
                    @if(!$esVisitante)
                    <a href="{{ route('reportes.no_conforme') }}"
                       class="flex items-center gap-1.5 text-gray-600 hover:bg-gray-100 hover:text-gray-900 px-3 py-1.5 rounded-md text-sm font-medium transition whitespace-nowrap {{ request()->routeIs('reportes.no_conforme') ? 'bg-gray-100 text-gray-900' : '' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        No Conforme
                    </a>
                    <a href="{{ route('reportes.inventario_general') }}"
                       class="flex items-center gap-1.5 text-gray-600 hover:bg-gray-100 hover:text-gray-900 px-3 py-1.5 rounded-md text-sm font-medium transition whitespace-nowrap {{ request()->routeIs('reportes.inventario_general') ? 'bg-gray-100 text-gray-900' : '' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Inventario General
                    </a>
                    <a href="{{ route('movimientos.index') }}"
                       class="flex items-center gap-1.5 text-gray-600 hover:bg-gray-100 hover:text-gray-900 px-3 py-1.5 rounded-md text-sm font-medium transition whitespace-nowrap {{ request()->routeIs('movimientos.*') ? 'bg-gray-100 text-gray-900' : '' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                        Movimientos
                    </a>
                    <a href="{{ route('catalogos.index') }}"
                       class="flex items-center gap-1.5 text-gray-600 hover:bg-gray-100 hover:text-gray-900 px-3 py-1.5 rounded-md text-sm font-medium transition whitespace-nowrap {{ request()->routeIs('catalogos.*') ? 'bg-gray-100 text-gray-900' : '' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Catálogos
                    </a>
                    @if(auth()->user()->hasRole('admin'))
                    <a href="{{ route('usuarios.index') }}"
                       class="flex items-center gap-1.5 text-gray-600 hover:bg-gray-100 hover:text-gray-900 px-3 py-1.5 rounded-md text-sm font-medium transition whitespace-nowrap {{ request()->routeIs('usuarios.*') ? 'bg-gray-100 text-gray-900' : '' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        Usuarios
                    </a>
                    @endif
                    @endif
                </div>

                {{-- Menu mobile (dropdown) --}}
                <div class="lg:hidden relative ml-2" id="mobileMenuDropdown">
                    <button onclick="toggleMobileMenu()"
                        class="flex items-center gap-1.5 text-gray-600 hover:bg-gray-100 hover:text-gray-900 px-3 py-1.5 rounded-md text-sm font-medium transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        Menú
                    </button>
                    <div id="mobileMenu"
                        class="hidden absolute right-0 top-full mt-1 w-64 bg-white rounded-xl shadow-lg border border-gray-200 py-1 z-50 max-h-[80vh] overflow-y-auto">
                        @if(!$esVisitante)
                        <a href="{{ route('dashboard') }}"
                           class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition {{ request()->routeIs('dashboard') ? 'bg-indigo-50 font-medium' : '' }}"
                           style="{{ request()->routeIs('dashboard') ? 'color:#4A568D;' : '' }}">
                            <svg class="w-4 h-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Dashboard
                        </a>
                        @endif
                        <a href="{{ route('reportes.entradas') }}"
                           class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition {{ request()->routeIs('reportes.entradas') ? 'bg-indigo-50 font-medium' : '' }}"
                           style="{{ request()->routeIs('reportes.entradas') ? 'color:#4A568D;' : '' }}">
                            <svg class="w-4 h-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            Inventario
                        </a>
                        <a href="{{ route('solicitudes.concentrado') }}"
                           class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition {{ request()->routeIs('solicitudes.concentrado') || request()->routeIs('tickets.*') || request()->routeIs('reportes.requisiciones') ? 'bg-indigo-50 font-medium' : '' }}"
                           style="{{ request()->routeIs('solicitudes.concentrado') || request()->routeIs('tickets.*') || request()->routeIs('reportes.requisiciones') ? 'color:#4A568D;' : '' }}">
                            <svg class="w-4 h-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                            </svg>
                            Solicitudes
                        </a>
                        @if(!$esVisitante)
                        <a href="{{ route('reportes.no_conforme') }}"
                           class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition {{ request()->routeIs('reportes.no_conforme') ? 'bg-indigo-50 font-medium' : '' }}"
                           style="{{ request()->routeIs('reportes.no_conforme') ? 'color:#4A568D;' : '' }}">
                            <svg class="w-4 h-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            No Conforme
                        </a>
                        <a href="{{ route('reportes.inventario_general') }}"
                           class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition {{ request()->routeIs('reportes.inventario_general') ? 'bg-indigo-50 font-medium' : '' }}"
                           style="{{ request()->routeIs('reportes.inventario_general') ? 'color:#4A568D;' : '' }}">
                            <svg class="w-4 h-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            Inventario General
                        </a>
                        <a href="{{ route('movimientos.index') }}"
                           class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition {{ request()->routeIs('movimientos.*') ? 'bg-indigo-50 font-medium' : '' }}"
                           style="{{ request()->routeIs('movimientos.*') ? 'color:#4A568D;' : '' }}">
                            <svg class="w-4 h-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            Movimientos
                        </a>
                        <a href="{{ route('catalogos.index') }}"
                           class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition {{ request()->routeIs('catalogos.*') ? 'bg-indigo-50 font-medium' : '' }}"
                           style="{{ request()->routeIs('catalogos.*') ? 'color:#4A568D;' : '' }}">
                            <svg class="w-4 h-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Catálogos
                        </a>
                        @if(auth()->user()->hasRole('admin'))
                        <a href="{{ route('usuarios.index') }}"
                           class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition {{ request()->routeIs('usuarios.*') ? 'bg-indigo-50 font-medium' : '' }}"
                           style="{{ request()->routeIs('usuarios.*') ? 'color:#4A568D;' : '' }}">
                            <svg class="w-4 h-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            Usuarios
                        </a>
                        @endif
                        @endif
                    </div>
                </div>

                {{-- Notificaciones --}}
                @php
                    $notifUnread = auth()->user()->unreadNotifications()->count();
                    $notifItems  = auth()->user()->notifications()->latest()->take(8)->get();
                @endphp
                <div class="relative flex-shrink-0" id="notifDropdownWrapper">
                    <button onclick="toggleNotifMenu()"
                        class="relative text-gray-500 hover:text-gray-700 hover:bg-gray-100 p-1.5 rounded-md transition"
                        aria-label="Notificaciones">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @if($notifUnread > 0)
                        <span class="absolute -top-0.5 -right-0.5 bg-red-500 text-white text-[10px] font-bold rounded-full min-w-[16px] h-4 flex items-center justify-center px-0.5 leading-none">
                            {{ $notifUnread > 99 ? '99+' : $notifUnread }}
                        </span>
                        @endif
                    </button>

                    {{-- Panel de notificaciones --}}
                    <div id="notifMenu" class="hidden absolute right-0 top-full mt-1 w-80 bg-white rounded-xl shadow-xl border border-gray-200 z-50 overflow-hidden">
                        {{-- Cabecera --}}
                        <div class="flex items-center justify-between px-4 py-2.5 border-b border-gray-100 bg-gray-50">
                            <span class="text-sm font-semibold text-gray-700">
                                Notificaciones
                                @if($notifUnread > 0)
                                <span class="ml-1.5 text-xs bg-red-100 text-red-600 px-1.5 py-0.5 rounded-full font-medium">{{ $notifUnread }}</span>
                                @endif
                            </span>
                            @if($notifUnread > 0)
                            <button onclick="marcarTodasLeidas()"
                                class="text-xs text-indigo-600 hover:text-indigo-800 font-medium transition"
                                id="btnMarcarTodas">
                                Marcar todo como leído
                            </button>
                            @endif
                        </div>

                        {{-- Lista --}}
                        <div class="max-h-72 overflow-y-auto">
                            @forelse($notifItems as $notif)
                            @php
                                $isUnread = is_null($notif->read_at);
                                $data     = $notif->data;
                                $type     = $data['type'] ?? 'general';
                                $msg      = $data['message'] ?? 'Notificación';
                                $codigo   = $data['codigo'] ?? null;
                                $ticketId = $data['ticket_id'] ?? null;
                                $destUrl  = $ticketId ? route('solicitudes.concentrado') . '?tab=movimiento' : '';
                                $iconColor = match($type) {
                                    'ticket_pending_approval'  => '#7C3AED',
                                    'ticket_assigned'          => '#3B82F6',
                                    'ticket_assigned_to_you'   => '#0d7a6b',
                                    'ticket_completed'         => '#16a34a',
                                    'survey_completed'         => '#D97706',
                                    default                    => '#4A568D',
                                };
                            @endphp
                            <div class="flex gap-3 px-4 py-3 border-b border-gray-50 hover:bg-gray-50 transition cursor-pointer notif-item {{ $isUnread ? 'bg-blue-50/60' : '' }}"
                                 data-id="{{ $notif->id }}"
                                 onclick="abrirNotif('{{ $notif->id }}', '{{ $destUrl }}')">
                                {{-- Icono --}}
                                <div class="flex-shrink-0 mt-0.5">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center"
                                         style="background-color:{{ $iconColor }}1A;">
                                        @if($type === 'ticket_completed')
                                        <svg class="w-4 h-4" fill="none" stroke="{{ $iconColor }}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @elseif($type === 'survey_completed')
                                        <svg class="w-4 h-4" fill="{{ $iconColor }}" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                        @elseif($type === 'ticket_pending_approval')
                                        <svg class="w-4 h-4" fill="none" stroke="{{ $iconColor }}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @else
                                        <svg class="w-4 h-4" fill="none" stroke="{{ $iconColor }}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                                        @endif
                                    </div>
                                </div>
                                {{-- Contenido --}}
                                <div class="flex-1 min-w-0">
                                    @if($codigo)
                                    <span class="text-xs font-bold" style="color:{{ $iconColor }}">{{ $codigo }}</span>
                                    @endif
                                    <p class="text-xs text-gray-700 leading-snug {{ $isUnread ? 'font-semibold' : '' }} mt-0.5">{{ $msg }}</p>
                                    <p class="text-[11px] text-gray-400 mt-0.5">{{ $notif->created_at->diffForHumans() }}</p>
                                </div>
                                @if($isUnread)
                                <div class="flex-shrink-0 self-center">
                                    <div class="w-2 h-2 rounded-full bg-blue-500 notif-dot"></div>
                                </div>
                                @endif
                            </div>
                            @empty
                            <div class="px-4 py-8 text-center">
                                <svg class="w-8 h-8 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                <p class="text-sm text-gray-400">Sin notificaciones</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Usuario --}}
                <div class="flex items-center gap-3">
                    <span class="text-gray-600 text-sm font-medium hidden md:block">{{ auth()->user()->name }}</span>
                    @if(auth()->user()->avatar)
                    <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}" class="h-8 w-8 rounded-full border-2 border-gray-300 shadow-sm">
                    @else
                    <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center border-2 border-gray-300 shadow-sm">
                        <span class="text-gray-700 font-bold text-sm">{{ substr(auth()->user()->name, 0, 1) }}</span>
                    </div>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-1.5 bg-gray-800 hover:bg-gray-900 text-white px-3 py-1.5 rounded-md text-sm font-medium transition shadow-sm">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Salir
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    @endauth

    <main class="py-5 flex-1">
        <div class="w-full px-4 sm:px-6">
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-8">
        <div class="w-full px-4 sm:px-6 py-4">
            <p class="text-center text-xs text-gray-400">© {{ date('Y') }} Sistema de Inventario de Almacén</p>
        </div>
    </footer>

    <script>
        // ── Menú hamburguesa ──────────────────────────────────
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            document.getElementById('notifMenu')?.classList.add('hidden');
            menu.classList.toggle('hidden');
        }
        document.addEventListener('click', function (e) {
            const wrapper = document.getElementById('mobileMenuDropdown');
            if (wrapper && !wrapper.contains(e.target)) {
                document.getElementById('mobileMenu')?.classList.add('hidden');
            }
        });

        // ── Campana de notificaciones ─────────────────────────
        function toggleNotifMenu() {
            const menu = document.getElementById('notifMenu');
            document.getElementById('mobileMenu')?.classList.add('hidden');
            menu.classList.toggle('hidden');
        }
        document.addEventListener('click', function (e) {
            const wrapper = document.getElementById('notifDropdownWrapper');
            if (wrapper && !wrapper.contains(e.target)) {
                document.getElementById('notifMenu')?.classList.add('hidden');
            }
        }, true);

        function abrirNotif(id, url) {
            const item = document.querySelector(`.notif-item[data-id="${id}"]`);
            if (item && item.classList.contains('bg-blue-50/60')) {
                fetch(`/notificaciones/${id}/leer`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                    }
                }).then(() => {
                    item.classList.remove('bg-blue-50/60');
                    item.querySelector('.notif-dot')?.remove();
                    _actualizarBadge(-1);
                }).catch(() => {});
            }
            if (url) window.location.href = url;
        }

        function marcarTodasLeidas() {
            fetch('/notificaciones/leer-todas', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                }
            }).then(() => {
                document.querySelectorAll('.notif-item').forEach(item => {
                    item.classList.remove('bg-blue-50/60');
                    item.querySelector('.notif-dot')?.remove();
                });
                document.querySelector('#btnMarcarTodas')?.remove();
                _actualizarBadge(0);
            }).catch(() => {});
        }

        function _actualizarBadge(delta) {
            const badge      = document.querySelector('#notifDropdownWrapper .bg-red-500');
            const headerBadge = document.querySelector('#notifMenu .bg-red-100');
            if (delta === 0) {
                badge?.remove();
                headerBadge?.parentElement?.remove();
                return;
            }
            if (badge) {
                const current = parseInt(badge.textContent) || 0;
                const next    = Math.max(0, current + delta);
                if (next === 0) { badge.remove(); headerBadge?.remove(); }
                else badge.textContent = next > 99 ? '99+' : next;
            }
        }
    </script>
</body>
</html>
