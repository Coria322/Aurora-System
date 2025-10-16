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
        <h1 class="landing__title">
            Vive el lujo de Hospedarte con Nosotros.
        </h1>
        @include('partials.carousel')
    </section>
    <section id="servicios">
        <h1 class="landing__title">
            Disfruta con Nuestros Exclusivos Servicios
        </h1>
        @include('partials.carousel-servicios')
    </section>
</body>
<footer id="contacto" class="bg-gray-900 text-gray-300 py-10" style="--color-of-sand: #fdf5AA;">
    @include('partials.footer')
</footer>
</html>