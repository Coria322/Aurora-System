import { createApp, defineAsyncComponent } from 'vue'
import '../css/Landing/carousel-servicios.css'
import '../css/app.css'

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

  // 🪶 Lazy loading del modal
  app.component(
    'ServicioModal',
    defineAsyncComponent(() => import('./components/ServicioModal.vue'))
  )

  app.directive('lazy', {
    mounted(el, binding) {
      el.dataset.lazy = 'true' // Marca el elemento como lazy
      const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            const img = new Image()
            img.src = binding.value
            img.onload = () => {
              el.src = binding.value
              el.classList.add('loaded') // dispara la animación
            }
            observer.unobserve(el)
          }
        })
      })
      observer.observe(el)
    }
  })

  app.mount(rootElement)
}
