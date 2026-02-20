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
</body>
</html>
