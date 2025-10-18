import { createApp } from 'vue'
import '../css/Landing/carousel-servicios.css'
import '../css/app.css'

import ServicioModal from './components/ServicioModal.vue'
import { useServicios } from './composables/useServicios'

const rootElement = document.querySelector('#serviciosApp') as HTMLElement

if (rootElement && !rootElement.dataset.vueMounted) {
    rootElement.dataset.vueMounted = 'true'

    const serviciosData = JSON.parse(rootElement.dataset.servicios || '[]')

    const app = createApp({
        setup() {
            return useServicios(serviciosData)
        },
    })

    app.component('ServicioModal', ServicioModal)
    app.mount(rootElement)
}
