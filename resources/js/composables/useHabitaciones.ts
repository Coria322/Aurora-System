import { ref, onMounted } from 'vue'

export interface Habitacion {
  id: number
  nombre: string
  descripcion: string
  imagen: string
  capacidad?: number
  precio?: number
}

export function useHabitaciones(habitacionesData: Habitacion[]) {
  const showModal = ref(false)
  const selectedHabitacion = ref<Habitacion | null>(null)
  const habitaciones = ref<Habitacion[]>(habitacionesData)
  const currentSlide = ref(0)

  const openModal = (habitacion: Habitacion) => {
    selectedHabitacion.value = habitacion
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
    if (currentSlide.value < habitaciones.value.length - 1) currentSlide.value++
  }

  onMounted(() => {
    document.addEventListener('keydown', (e: KeyboardEvent) => {
      if (e.key === 'Escape' && showModal.value) closeModal()
    })
  })

  return {
    showModal,
    selectedHabitacion,
    habitaciones,
    currentSlide,
    openModal,
    closeModal,
    prevSlide,
    nextSlide
  }
}
