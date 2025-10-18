import { createApp } from 'vue'
import '../css/Landing/carousel-habitaciones.css'
import '../css/app.css'

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
       

    })

    app.component('HabitacionModal', HabitacionModal)
    app.mount(rootElement)
}
