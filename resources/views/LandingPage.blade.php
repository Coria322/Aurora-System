<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config("app.name") }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<header>
    @include('partials.navbar')
</header>
<body>
    <section id="inicio">
        @include('partials.vidBanner')
    </section>
    <section id="habitaciones">
        <h1 class="habitaciones__title">
            Vive el lujo de Hospedarte aquí
        </h1>
        @include('partials.carousel-element')
    </section>
    
</body>
</html>