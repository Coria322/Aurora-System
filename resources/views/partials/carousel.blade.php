<div id="habitacionesApp" data-habitaciones='@json($tipoHab)'>
  <div class="carousel-container full-width">
    <button class="carousel-btn prev" @click="prevSlide">&#10094;</button>

    <div class="carousel">
      @forelse ($tipoHab as $tipo)
        <div class="habitacion__card">
          <img 
            class="habitacion__imagen"
            v-lazy="'{{ $tipo->imagen ? asset($tipo->imagen) : asset('images/Habitaciones/habitacion_generica.png') }}'"
            alt="Imagen de {{ $tipo->nombre }}"
          >
          <div class="habitacion__contenido">
            <h3 class="habitacion__titulo">{{ $tipo->nombre }}</h3>
            <p class="habitacion__descripcion">{{ Str::limit($tipo->descripcion, 30, '...') }}</p>
            <button 
              @click="openModal({{ json_encode($tipo) }})" 
              class="habitacion__boton"
            >
              Ver Detalles
            </button>
          </div>
        </div>
      @empty
        <h2 class="message">Error al Recuperar las habitaciones</h2>
      @endforelse
    </div>

    <button class="carousel-btn next" @click="nextSlide">&#10095;</button>
  </div>

  <!-- Modal cargado solo cuando se necesita -->
  <habitacion-modal 
    v-if="showModal" 
    :habitacion="selectedHabitacion" 
    @close="closeModal"
  />
</div>

@vite(['resources/js/habitacionesApp.ts'])
