<div class="carousel-container full-width">
    <button class="carousel-btn prev ser">&#10094;</button>

    <div class="carousel ser">
        @forelse ($servicios as $srvc)
            <div class="servicio__card">
                @if ($srvc->imagen)
                    <img class="servicio__imagen" src="{{ asset($srvc->imagen) }}" alt="Imagen de {{ $srvc->nombre }}">
                @else
                    <img class="servicio__imagen" src="{{ asset('images/Servicios/servicio_generico.png') }}" alt="Imagen por defecto">
                @endif

                <div class="servicio__contenido">
                    <h3 class="servicio__titulo">{{ $srvc->nombre_servicio }}</h3>
                    <p class="servicio__descripcion">{{ Str::limit($srvc->descripcion, 30, '...') }}</p>
                    <a href="#" class="servicio__boton">Ver Detalles</a>
                </div>
            </div>
        @empty
            <h2 class="message">Error al Recuperar las servicios</h2>
        @endforelse
    </div>

    <button class="carousel-btn next ser">&#10095;</button>
</div>
