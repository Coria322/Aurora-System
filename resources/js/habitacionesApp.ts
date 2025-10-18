import { createApp } from 'vue'
import '../css/Landing/carousel-habitaciones.css'
import '../css/app.css'

//TODO REPARAR LOS ESTILOS QUE NO SE APLICAN
import HabitacionModal from './components/Habitacionmodal.vue'
import { useHabitaciones } from './composables/useHabitaciones'

const rootElement = document.querySelector('#habitacionesApp') as HTMLElement

// 🛡️ Verifica que solo se monte una vez
if (rootElement && !rootElement.dataset.vueMounted) {
    rootElement.dataset.vueMounted = 'true'

    const habitacionesData = JSON.parse(rootElement.dataset.habitaciones || '[]')

    const app = createApp({
        setup() {
            return useHabitaciones(habitacionesData)
        },
        template: `
<div class="carousel-container full-width">
    <button class="carousel-btn prev" @click="prevSlide">&#10094;</button>

    <div class="carousel">
        <div v-for="habitacion in habitaciones" :key="habitacion.id" class="habitacion__card">
            <img 
                class="habitacion__imagen"
                :src="habitacion.imagen || '/images/Habitaciones/habitacion_generica.png'"
                :alt="habitacion.nombre"
            >
            <div class="habitacion__contenido">
                <h3 class="habitacion__titulo">{{ habitacion.nombre }}</h3>
                <p class="habitacion__descripcion">{{ habitacion.descripcion }}</p>
                <button @click="openModal(habitacion)" class="habitacion__boton">Ver Detalles</button>
            </div>
        </div>
    </div>

    <button class="carousel-btn next" @click="nextSlide">&#10095;</button>

    <HabitacionModal 
        v-if="showModal" 
        :habitacion="selectedHabitacion" 
        @close="closeModal" 
    />
</div>
`

    })

    app.component('HabitacionModal', HabitacionModal)
    app.mount(rootElement)
}
