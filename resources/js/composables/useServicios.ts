import { ref, onMounted } from 'vue'

export interface Servicio {
  id_servicio: number
  nombre_servicio: string
  descripcion: string
  precio?: number
  tipo_servicio?: string
  imagen?: string
  activo?: boolean
}

export function useServicios(serviciosData: Servicio[]) {
  const showModal = ref(false)
  const selectedServicio = ref<Servicio | null>(null)
  const servicios = ref<Servicio[]>(serviciosData)
  const currentSlide = ref(0)

  const openModal = (servicio: Servicio) => {
    selectedServicio.value = servicio
    showModal.value = true
    document.body.style.overflow = 'hidden'
  }

  const closeModal = () => {
    showModal.value = false
    document.body.style.overflow = 'auto'
  }

  const prevSlide = () => {
    if (currentSlide.value > 0) currentSlide.value--
  }

  const nextSlide = () => {
    if (currentSlide.value < servicios.value.length - 1) currentSlide.value++
  }

  onMounted(() => {
    document.addEventListener('keydown', (e: KeyboardEvent) => {
      if (e.key === 'Escape' && showModal.value) closeModal()
    })
  })

  return {
    showModal,
    selectedServicio,
    servicios,
    currentSlide,
    openModal,
    closeModal,
    prevSlide,
    nextSlide
  }
}
