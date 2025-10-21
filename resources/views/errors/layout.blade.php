<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Error')</title>
    @vite(['resources/css/errors.css'])
</head>
<body class="err-body">
    <div class="error-card">
        @php
            // Obtiene el código de error de forma segura
            $code = trim($__env->yieldContent('code', '404'));
        @endphp

        <div class="error-code">
            @foreach(str_split($code) as $char)
                @if($char == '0')
                    <img src="{{ asset('images/auroralogo.png') }}" alt="Logo">
                @else
                    {{ $char }}
                @endif
            @endforeach
        </div>

        <div class="error-message">
            @yield('message', 'Página no encontrada')
        </div>

        <a href="{{ url('/') }}" class="error-btn">Volver al inicio</a>
    </div>
</body>
</html>
