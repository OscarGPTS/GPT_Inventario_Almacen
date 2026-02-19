<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido - Inventario Almacén</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full overflow-hidden border border-gray-200">
        <!-- Header -->
        <div class="bg-white p-8 text-center border-b border-gray-200">
            @if(file_exists(public_path('storage/img/logo_gpt.svg')))
                <img src="{{ asset('storage/img/logo_gpt.svg') }}" alt="Logo" class="h-24 mx-auto mb-4">
            @else
                <div class="h-24 w-24 mx-auto mb-4 bg-gray-100 rounded-lg flex items-center justify-center">
                    <svg class="h-16 w-16 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            @endif
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Sistema de Inventario</h1>
            <p class="text-gray-600">Gestión de Almacén</p>
        </div>

        <!-- Contenido -->
        <div class="p-8">
            @if(session('error'))
            <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded" role="alert">
                <p class="font-medium">Error</p>
                <p class="text-sm">{{ session('error') }}</p>
            </div>
            @endif

            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Bienvenido</h2>
                <p class="text-gray-600">Inicia sesión con tu cuenta de Google para continuar</p>
            </div>

            <!-- Botón de Google -->
            <a href="{{ route('google.redirect') }}" 
               class="flex items-center justify-center w-full bg-white border-2 border-gray-300 rounded-lg shadow-md px-6 py-4 text-base font-semibold text-gray-800 hover:bg-gray-50 hover:shadow-lg transition-all duration-200 transform hover:scale-105">
                <svg class="h-6 w-6 mr-3" viewBox="0 0 48 48">
                    <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                    <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                    <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                    <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                </svg>
                Iniciar sesión con Google
            </a>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-500">
                    Al iniciar sesión, aceptas nuestros términos y condiciones
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-8 py-4 border-t border-gray-200">
            <p class="text-center text-gray-600 text-sm">
                © {{ date('Y') }} Sistema de Inventario. Todos los derechos reservados.
            </p>
        </div>
    </div>
</body>
</html>
