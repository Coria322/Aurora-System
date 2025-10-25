<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Notificación')</title>

    <!-- Importar EB Garamond desde Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;500;700&display=swap" rel="stylesheet">
</head>

<body
    style="margin:0;font-family:ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,'Noto Sans',sans-serif;background-color:#113f67;">
    <table width="100%" cellpadding="0" cellspacing="0" style="padding:20px;box-sizing:border-box;">
        <tr>
            <td align="center">
                <table width="100%"
                    style="max-width:500px;background-color:#ffffff;border-radius:20px;box-shadow:0 12px 30px rgba(0,0,0,0.25);padding:40px 30px;text-align:center;">

                    {{-- Logo embebido --}}
                    <tr>
                        <td style="margin-bottom:20px;">
                            <img src="{{ $message->embed($logoPath) }}" alt="Aurora Hotel" style="max-width:150px;height:auto;">
                        </td>
                    </tr>

                    {{-- Título --}}
                    <tr>
                        <td style="font-family:'EB Garamond', serif;font-size:1.7rem;color:#4a5568;margin-bottom:20px;">
                            @yield('title')
                        </td>
                    </tr>

                    {{-- Contenido --}}
                    <tr>
                        <td style="text-align:left;color:#1e3a8a;margin:20px 0;line-height:1.6;">
                            @yield('content')
                        </td>
                    </tr>

                    {{-- Footer --}}
                    @hasSection('footer')
                        <tr>
                            <td style="font-size:13px;color:gray;margin-top:20px;text-align:center;">
                                @yield('footer')
                            </td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>
</body>

</html>