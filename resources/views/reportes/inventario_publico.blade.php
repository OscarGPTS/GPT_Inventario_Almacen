<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario General - Consulta P&uacute;blica</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    @php $acento = '#4A568D'; @endphp

    <!-- Header Public -->
    <nav class="bg-white shadow-md border-b border-gray-200 sticky top-0 z-50">
        <div class="w-full px-4 sm:px-6">
            <div class="flex justify-between items-center" style="height:56px;">
                {{-- Logo --}}
                <div class="flex items-center gap-2">
                    @if(file_exists(public_path('storage/img/logo_gpt.svg')))
                        <img src="{{ asset('storage/img/logo_gpt.svg') }}" alt="Logo" class="h-8 w-auto">
                    @endif
                    <div class="text-base font-bold text-gray-800 whitespace-nowrap">
                        Inventario Almac&eacute;n
                    </div>
                </div>

                {{-- Mostrar bot&oacute;n de login o informaci&oacute;n del usuario --}}
                @guest
                {{-- Bot&oacute;n Iniciar Sesi&oacute;n con Google (solo para no autenticados) --}}
                <div class="flex items-center gap-3">
                    <a href="{{ route('google.redirect') }}" 
                       class="flex items-center gap-3 bg-white border border-gray-300 rounded-md shadow-sm px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:shadow transition-all duration-200">
                        <svg class="h-5 w-5" viewBox="0 0 48 48">
                            <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                            <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                            <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                            <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                        </svg>
                        Iniciar sesi&oacute;n con Google
                    </a>
                </div>
                @endguest

                @auth
                {{-- Usuario autenticado --}}
                <div class="flex items-center gap-3">
                    <span class="text-gray-600 text-sm font-medium hidden md:block">{{ auth()->user()->name }}</span>
                    @if(auth()->user()->avatar)
                    <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}" class="h-8 w-8 rounded-full border-2 border-gray-300 shadow-sm">
                    @else
                    <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center border-2 border-gray-300 shadow-sm">
                        <span class="text-gray-700 font-bold text-sm">{{ substr(auth()->user()->name, 0, 1) }}</span>
                    </div>
                    @endif
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-md text-sm font-medium transition shadow-sm">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Dashboard
                    </a>
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
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="py-5">
        <div class="w-full px-4 sm:px-6">
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">Inventario General</h1>
                        <p class="text-xs text-gray-500 mt-0.5">{{ number_format($registros->total()) }} productos</p>
                    </div>
                    
                </div>

                <form method="GET" action="{{ route('inventario.publico') }}" class="bg-white rounded-xl border border-gray-200 shadow-sm">
                    <div class="px-4 py-3 flex gap-3 items-center">
                        <div class="flex-1 relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" 
                                id="searchInputPublico" 
                                name="search" 
                                value="{{ request('search') }}"
                                placeholder="Buscar por c&oacute;digo o descripci&oacute;n..."
                                class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 transition"
                                autocomplete="off"
                                onkeyup="buscarDinamicoPublico(event)">
                            
                            {{-- Dropdown de sugerencias --}}
                            <div id="suggestionBoxPublico" class="hidden absolute top-full left-0 right-0 mt-1 bg-white border border-gray-300 rounded-lg shadow-lg z-50 max-h-80 overflow-y-auto"></div>
                        </div>
                        <button type="submit" class="px-4 py-2 text-white text-sm font-medium rounded-lg transition" style="background-color:{{ $acento }}">Buscar</button>
                        @if(request('search'))
                        <a href="{{ route('inventario.publico') }}" class="px-3 py-2 bg-gray-100 text-gray-600 text-sm rounded-lg hover:bg-gray-200 transition">✕</a>
                        @endif
                    </div>
                </form>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto" style="max-height:calc(100vh - 260px);overflow-y:auto;">
                        <table class="w-full text-xs border-collapse" style="min-width:700px;">
                            <thead class="sticky top-0 z-10">
                                <tr style="background-color:{{ $acento }};">
                                    <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">CODIGO</th>
                                    <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide border-r border-indigo-600" style="min-width:220px;">DESCRIPCI&Oacute;N</th>
                                    <th class="px-3 py-2.5 text-left text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">UBIC.</th>
                                    <th class="px-3 py-2.5 text-center text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">UM</th>
                                    <th class="px-3 py-2.5 text-right text-white font-semibold uppercase tracking-wide whitespace-nowrap border-r border-indigo-600">SUM DE FISICO</th>
                                    <th class="px-3 py-2.5 text-right text-white font-semibold uppercase tracking-wide whitespace-nowrap">SUM DE P.U</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($registros as $p)
                                <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }} hover:bg-blue-50 transition-colors">
                                    <td class="px-3 py-2 font-mono font-semibold whitespace-nowrap" style="color:{{ $acento }}">
                                        {{ $p->codigo }}
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
                            {{-- Pie con totales globales --}}
                            <tfoot>
                                <tr style="background-color:#f1f3fb;">
                                    <td colspan="4" class="px-3 py-2 text-right text-xs font-semibold text-gray-600 uppercase tracking-wide">Totales globales →</td>
                                    <td class="px-3 py-2 text-right text-xs font-bold text-gray-800">{{ number_format($totales['sum_fisico'], 2) }}</td>
                                    <td class="px-3 py-2 text-right text-xs font-bold text-gray-800">$ {{ number_format($totales['sum_pu'], 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    {{-- Paginaci&oacute;n --}}
                    @if($registros->hasPages())
                    <div class="px-4 py-3 border-t border-gray-200 flex items-center justify-between text-xs">
                        <div class="text-gray-600">
                            Mostrando <span class="font-semibold">{{ $registros->firstItem() ?? 0 }}</span> a 
                            <span class="font-semibold">{{ $registros->lastItem() ?? 0 }}</span> de 
                            <span class="font-semibold">{{ $registros->total() }}</span> resultados
                        </div>
                        <div class="flex gap-1">
                            @if($registros->onFirstPage())
                            <span class="px-3 py-1.5 border border-gray-200 rounded text-gray-400 cursor-not-allowed">Anterior</span>
                            @else
                            <a href="{{ $registros->previousPageUrl() }}" class="px-3 py-1.5 border border-gray-200 rounded hover:bg-gray-50 transition" style="color:{{ $acento }}">Anterior</a>
                            @endif

                            @foreach($registros->getUrlRange(max(1, $registros->currentPage() - 2), min($registros->lastPage(), $registros->currentPage() + 2)) as $page => $url)
                                @if($page == $registros->currentPage())
                                <span class="px-3 py-1.5 border rounded text-white font-medium" style="background-color:{{ $acento }};border-color:{{ $acento }}">{{ $page }}</span>
                                @else
                                <a href="{{ $url }}" class="px-3 py-1.5 border border-gray-200 rounded hover:bg-gray-50 transition" style="color:{{ $acento }}">{{ $page }}</a>
                                @endif
                            @endforeach

                            @if($registros->hasMorePages())
                            <a href="{{ $registros->nextPageUrl() }}" class="px-3 py-1.5 border border-gray-200 rounded hover:bg-gray-50 transition" style="color:{{ $acento }}">Siguiente</a>
                            @else
                            <span class="px-3 py-1.5 border border-gray-200 rounded text-gray-400 cursor-not-allowed">Siguiente</span>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-8">
        <div class="w-full px-4 sm:px-6 py-4">
            <p class="text-center text-xs text-gray-400">© {{ date('Y') }} Sistema de Inventario de Almacén </p>
        </div>
    </footer>
</body>
</html>
