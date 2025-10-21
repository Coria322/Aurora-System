<div id="serviciosApp" data-servicios='@json($servicios)'>
  <div class="carousel-container full-width">
    <button class="carousel-btn prev ser" @click="prevSlide">&#10094;</button>

    <div class="carousel ser">
      @forelse ($servicios as $srvc)
        <div class="servicio__card">
          <img 
            v-lazy="'{{ $srvc->imagen ? asset('images/Servicios/' . $srvc->imagen) : asset('images/Servicios/servicio_generico.png') }}'"
            class="servicio__imagen"
            alt="Imagen de {{ $srvc->nombre_servicio }}"
          >
          <div class="servicio__contenido">
            <h3 class="servicio__titulo">{{ $srvc->nombre_servicio }}</h3>
            <p class="servicio__descripcion">{{ Str::limit($srvc->descripcion, 30, '...') }}</p>
            <button class="servicio__boton" @click='openModal(@json($srvc))'>
              Ver Detalles
            </button>
          </div>
        </div>
      @empty
        <h2 class="message">Error al Recuperar los servicios</h2>
      @endforelse
    </div>

    <button class="carousel-btn next ser" @click="nextSlide">&#10095;</button>
  </div>

  <!-- Modal cargado solo cuando se usa -->
  <servicio-modal v-if="showModal" :servicio="selectedServicio" @close="closeModal" />
</div>

@vite(['resources/js/serviciosApp.ts'])
