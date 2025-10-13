<div class="carousel-container full-width">
    <button class="carousel-btn prev">&#10094;</button>

    <div class="carousel">
        @forelse ($tipoHab as $tipo)
            <div class="habitacion__card">
                @if ($tipo->imagen)
                    <img class="habitacion__imagen" src="{{ asset('images/' . $tipo->imagen) }}" alt="Imagen de {{ $tipo->nombre }}">
                @else
                    <img class="habitacion__imagen" src="{{ asset('images/Habitaciones/habitacion_generica.png') }}" alt="Imagen por defecto">
                @endif

                <div class="habitacion__contenido">
                    <h3 class="habitacion__titulo">{{ $tipo->nombre }}</h3>
                    <p class="habitacion__descripcion">{{ Str::limit($tipo->descripcion, 30, '...') }}</p>
                    <a href="#" class="habitacion__boton">Ver Detalles</a>
                </div>
            </div>
        @empty
            <h2 class="message">Error al Recuperar las habitaciones</h2>
        @endforelse
    </div>

    <button class="carousel-btn next">&#10095;</button>
</div>
