import { createApp, defineAsyncComponent } from 'vue'
import '../css/Landing/carousel-habitaciones.css'
import '../css/app.css'
import { useHabitaciones } from './composables/useHabitaciones'

const rootElement = document.querySelector('#habitacionesApp') as HTMLElement

if (rootElement && !rootElement.dataset.vueMounted) {
  rootElement.dataset.vueMounted = 'true'

  const habitacionesData = JSON.parse(rootElement.dataset.habitaciones || '[]')

  const app = createApp({
    setup() {
      return useHabitaciones(habitacionesData)
    },
  })

  // Lazy loading del modal
  app.component(
    'HabitacionModal',
    defineAsyncComponent(() => import('./components/Habitacionmodal.vue'))
  )

  // Directiva global para imágenes con fade-in + blur
  app.directive('lazy', {
    mounted(el, binding) {
      el.dataset.lazy = 'true'
      el.style.filter = 'blur(15px)'
      el.style.opacity = '0'
      el.style.transition = 'opacity 0.5s ease, filter 0.5s ease'

      const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            const img = new Image()
            img.src = binding.value
            img.onload = () => {
              el.src = binding.value
              el.style.filter = 'blur(0)'
              el.style.opacity = '1'
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
