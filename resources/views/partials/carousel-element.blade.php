<div class="habitacion__card">
    @if ($tipoHab -> imagen)
        <img src="{{ asset('images/' . $tipoHab -> imagen) }}" alt="Imagen de {{ $tipoHab -> nombre }}">
    @else
        <img src="{{ asset('images/Habitaciones/habitacion_generica.png') }}" alt="imagen por defecto">
    @endif
</div>
