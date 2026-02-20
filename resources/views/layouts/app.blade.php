<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Inventario Almacén')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    @auth
    <nav class="bg-white shadow-md border-b border-gray-200 sticky top-0 z-50">
        <div class="w-full px-4 sm:px-6">
            <div class="flex justify-between" style="height:56px;">
                {{-- Logo + Links --}}
                <div class="flex items-center gap-2">
                    <div class="flex-shrink-0 flex items-center gap-2 pr-4 border-r border-gray-200">
                        @if(file_exists(public_path('storage/img/logo_gpt.svg')))
                            <img src="{{ asset('storage/img/logo_gpt.svg') }}" alt="Logo" class="h-8 w-auto">
                        @endif
                        <a href="{{ route('dashboard') }}" class="text-base font-bold text-gray-800 hover:text-gray-600 transition whitespace-nowrap">
                            Inventario Almacén
                        </a>
                    </div>
                    <div class="hidden sm:flex items-center gap-1 ml-2">
                        <a href="{{ route('dashboard') }}"
                           class="flex items-center gap-1.5 text-gray-600 hover:bg-gray-100 hover:text-gray-900 px-3 py-1.5 rounded-md text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-gray-100 text-gray-900' : '' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Dashboard
                        </a>
                        <a href="{{ route('productos.index') }}"
                           class="flex items-center gap-1.5 text-gray-600 hover:bg-gray-100 hover:text-gray-900 px-3 py-1.5 rounded-md text-sm font-medium transition {{ request()->routeIs('productos.*') ? 'bg-gray-100 text-gray-900' : '' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            Productos
                        </a>
                        <a href="{{ route('solicitudes.index') }}"
                           class="flex items-center gap-1.5 text-gray-600 hover:bg-gray-100 hover:text-gray-900 px-3 py-1.5 rounded-md text-sm font-medium transition {{ request()->routeIs('solicitudes.*') ? 'bg-gray-100 text-gray-900' : '' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Solicitudes
                        </a>
                        <a href="{{ route('movimientos.index') }}"
                           class="flex items-center gap-1.5 text-gray-600 hover:bg-gray-100 hover:text-gray-900 px-3 py-1.5 rounded-md text-sm font-medium transition {{ request()->routeIs('movimientos.*') ? 'bg-gray-100 text-gray-900' : '' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            Movimientos
                        </a>

                        {{-- Dropdown Secciones --}}
                        <div class="relative" id="seccionesDropdown">
                            <button onclick="toggleDropdown()"
                                class="flex items-center gap-1.5 text-gray-600 hover:bg-gray-100 hover:text-gray-900 px-3 py-1.5 rounded-md text-sm font-medium transition {{ request()->routeIs('reportes.*') ? 'bg-gray-100 text-gray-900' : '' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Secciones
                                <svg class="w-3 h-3 shrink-0 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div id="dropdownMenu"
                                class="hidden absolute left-0 top-full mt-1 w-64 bg-white rounded-xl shadow-lg border border-gray-200 py-1 z-50">
                                <div class="px-3 py-1.5 border-b border-gray-100">
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Reportes y Secciones</p>
                                </div>
                                <a href="{{ route('reportes.entradas') }}"
                                   class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition {{ request()->routeIs('reportes.entradas') ? 'bg-indigo-50 font-medium' : '' }}"
                                   style="{{ request()->routeIs('reportes.entradas') ? 'color:#4A568D;' : '' }}">
                                    <svg class="w-4 h-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                    Entradas
                                </a>
                                <a href="{{ route('reportes.requisiciones') }}"
                                   class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition {{ request()->routeIs('reportes.requisiciones') ? 'bg-indigo-50 font-medium' : '' }}"
                                   style="{{ request()->routeIs('reportes.requisiciones') ? 'color:#4A568D;' : '' }}">
                                    <svg class="w-4 h-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    Concentrado de Requisiciones
                                </a>
                                <a href="{{ route('reportes.barras') }}"
                                   class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition {{ request()->routeIs('reportes.barras') ? 'bg-indigo-50 font-medium' : '' }}"
                                   style="{{ request()->routeIs('reportes.barras') ? 'color:#4A568D;' : '' }}">
                                    <svg class="w-4 h-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                    </svg>
                                    Barras
                                </a>
                                <a href="{{ route('reportes.resguardo') }}"
                                   class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition {{ request()->routeIs('reportes.resguardo') ? 'bg-indigo-50 font-medium' : '' }}"
                                   style="{{ request()->routeIs('reportes.resguardo') ? 'color:#4A568D;' : '' }}">
                                    <svg class="w-4 h-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                    Resguardo de Almacén
                                </a>
                                <a href="{{ route('reportes.no_conforme') }}"
                                   class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition {{ request()->routeIs('reportes.no_conforme') ? 'bg-indigo-50 font-medium' : '' }}"
                                   style="{{ request()->routeIs('reportes.no_conforme') ? 'color:#4A568D;' : '' }}">
                                    <svg class="w-4 h-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    No Conforme
                                </a>
                                <div class="border-t border-gray-100 mt-1 pt-1">
                                    <a href="{{ route('reportes.inventario_general') }}"
                                       class="flex items-center gap-2.5 px-3 py-2 text-sm font-medium hover:bg-gray-50 transition {{ request()->routeIs('reportes.inventario_general') ? 'bg-indigo-50' : '' }}"
                                       style="color:#4A568D;">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                        </svg>
                                        Inventario General
                                    </a>
                                </div>
                            </div>
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

    <main class="py-5">
        <div class="w-full px-4 sm:px-6">
            @if(session('success'))
            <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-sm" role="alert">
                <div class="flex items-center">
                    <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            </div>
            @endif

            @if(session('error'))
            <div class="mb-6 bg-red-100 border-l-4 border-red-600 text-red-700 p-4 rounded-lg shadow-sm" role="alert">
                <div class="flex items-center">
                    <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            </div>
            @endif

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
        function toggleDropdown() {
            const menu = document.getElementById('dropdownMenu');
            menu.classList.toggle('hidden');
        }
        document.addEventListener('click', function (e) {
            const wrapper = document.getElementById('seccionesDropdown');
            if (wrapper && !wrapper.contains(e.target)) {
                document.getElementById('dropdownMenu').classList.add('hidden');
            }
        });
    </script>
</body>
</html>
