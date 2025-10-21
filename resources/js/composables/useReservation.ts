import { ref, computed } from 'vue'
import axios from 'axios'

// ============================================
// ESTADO GLOBAL COMPARTIDO (fuera de la función)
// ============================================
const globalUserReservations = ref<any[]>([])
const globalActiveReservation = ref<any | null>(null)
let isLoadingReservationsGlobal = false

export function useReservationModal() {
  // Configuración de axios
  const api = axios.create({
    baseURL: '/api',
    withCredentials: true,
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
    }
  })

  // Estados de loading
  const isLoadingAvailability = ref(false)
  const isLoadingReservation = ref(false)
  const isLoadingRoomTypes = ref(false)

  // Estados de notificaciones
  const notification = ref({
    show: false,
    type: 'success' as 'success' | 'error' | 'info',
    message: ''
  })

  // Datos de la reserva
  const checkInDate = ref('')
  const checkOutDate = ref('')
  const selectedRoomType = ref('')
  const isSelectingCheckIn = ref(true)
  const cantidadPersonas = ref(2)

  // Datos del huésped
  const huespedData = ref({
    nombre: '',
    apellido_paterno: '',
    apellido_materno: '',
    email: '',
    telefono: '',
    observaciones: ''
  })

  // Tipos de habitación
  interface RoomType {
    id: number
    name: string
    description: string
    price: number
    capacity: number
    available_rooms: number
    services?: any
  }

  const roomTypes = ref<RoomType[]>([])
  const availabilityData = ref<any | null>(null)

  // ============================================
  // USAR REFERENCIAS GLOBALES (computed para reactividad)
  // ============================================
  const userReservations = computed(() => globalUserReservations.value)
  const activeReservation = computed(() => globalActiveReservation.value)

  // Días de la semana
  const weekDays = ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab']

  // Funciones de utilidad
  const formatDateForInput = (date: Date) => {
    const yyyy = date.getFullYear()
    const mm = (date.getMonth() + 1).toString().padStart(2, '0')
    const dd = date.getDate().toString().padStart(2, '0')
    return `${yyyy}-${mm}-${dd}`
  }

  const formatDate = (date: Date) => {
    return date.toLocaleDateString('es-ES', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric'
    })
  }

  const formatDateForAPI = (dateStr: string) => {
    if (dateStr.includes('-')) {
      return dateStr
    }
    const [day, month, year] = dateStr.split('/')
    return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`
  }

  const generateCalendarDates = () => {
    const today = new Date()
    const dates = []
    
    for (let i = 0; i < 35; i++) {
      const date = new Date(today)
      date.setDate(today.getDate() + i)
      dates.push(date)
    }
    
    return dates
  }

  const calendarDates = ref(generateCalendarDates())

  // Función para mostrar notificaciones
  const showNotification = (type: 'success' | 'error' | 'info', message: string) => {
    notification.value = {
      show: true,
      type,
      message
    }
    
    setTimeout(() => {
      notification.value.show = false
    }, 5000)
  }

  // Funciones de validación de fechas
  const isDateSelected = (date: Date) => {
    const dateStr = formatDate(date)
    return checkInDate.value === dateStr || checkOutDate.value === dateStr
  }

  const isDateInRange = (date: Date) => {
    if (!checkInDate.value || !checkOutDate.value) return false
    
    const checkIn = new Date(checkInDate.value)
    const checkOut = new Date(checkOutDate.value)
    const currentDate = new Date(date)
    
    return currentDate > checkIn && currentDate < checkOut
  }

  const isDateAvailable = (date: Date) => {
    const today = new Date()
    today.setHours(0, 0, 0, 0)
    return date >= today
  }

  const selectCalendarDate = (date: Date) => {
    if (!isDateAvailable(date)) return

    const dateStrInput = formatDateForInput(date)

    if (isSelectingCheckIn.value) {
      checkInDate.value = dateStrInput
      isSelectingCheckIn.value = false
    } else {
      if (new Date(dateStrInput) > new Date(checkInDate.value)) {
        checkOutDate.value = dateStrInput
        isSelectingCheckIn.value = true
      } else {
        checkOutDate.value = checkInDate.value
        checkInDate.value = dateStrInput
        isSelectingCheckIn.value = true
      }
    }
  }

  // Función para seleccionar habitación
  const selectRoomType = (roomId: string) => {
    selectedRoomType.value = roomId
  }

  // API Calls
  const cargarTiposHabitaciones = async () => {
    try {
      isLoadingRoomTypes.value = true
    
      console.log("fechas", checkInDate.value," ",checkOutDate.value)
      // Aquí pasamos las fechas al backend como query params
      const response = await api.get('/habitaciones/tipos', {
        params: {
          fecha_inicio: checkInDate.value || null,
          fecha_fin: checkOutDate.value || null
        }
      })

      if (response.data.success) {
        
        roomTypes.value = response.data.data.map((tipo: any) => ({
          id: tipo.id_tipo_habitacion,
          name: tipo.nombre,
          description: tipo.descripcion,
          price: tipo.precio_noche,
          capacity: tipo.capacidad_maxima,
          available_rooms: tipo.habitaciones_disponibles,
          services: tipo.servicios_incluidos
        }))
      }
    } catch (error) {
      console.error('Error cargando tipos de habitaciones:', error)
      showNotification('error', 'Error al cargar los tipos de habitaciones')
    } finally {
      isLoadingRoomTypes.value = false
    }
  }

  const buscarDisponibilidad = async () => {
    if (!checkInDate.value || !checkOutDate.value) {
      showNotification('error', 'Por favor, selecciona las fechas de entrada y salida')
      return
    }

    const fechaInicio = new Date(`${checkInDate.value}T00:00:00`)
    const fechaFin = new Date(`${checkOutDate.value}T00:00:00`)

    console.log("fechas", fechaInicio, " ", fechaFin)

    if (fechaFin <= fechaInicio) {
      showNotification('error', 'La fecha de salida debe ser posterior a la fecha de entrada')
      return
    }

    try {
      isLoadingAvailability.value = true
      
      const params = {
        fecha_inicio: formatDateForAPI(checkInDate.value),
        fecha_fin: formatDateForAPI(checkOutDate.value)
      }
      console.log(params)

      const response = await api.get('reservas/disponibilidad', { params })
      
      if (response.data.success) {
        availabilityData.value = response.data
        
        if (response.data.disponible) {
          showNotification('success', `¡Disponible! ${response.data.total_habitaciones_disponibles} habitaciones disponibles`)
          roomTypes.value = roomTypes.value.map(room => {
            const tipoDisponible = response.data.tipos_disponibles.find(
              (tipo: any) => tipo.id_tipo_habitacion === room.id
            )
            return {
              ...room,
              available_rooms: tipoDisponible ? tipoDisponible.habitaciones_disponibles : 0
            }
          })
        } else {
          showNotification('error', 'No hay habitaciones disponibles para las fechas seleccionadas')
        }
      }
    } catch (error) {
      console.error('Error verificando disponibilidad:', error)
      showNotification('error', 'Error al verificar disponibilidad. Intenta nuevamente.')
    } finally {
      isLoadingAvailability.value = false
    }
  }

  // ============================================
  // MODIFICADO: Recargar datos automáticamente después de crear
  // ============================================
  const crearReserva = async () => {
    if (!checkInDate.value || !checkOutDate.value || !selectedRoomType.value) {
      showNotification('error', 'Por favor, completa todos los campos requeridos')
      return
    }

    if (!huespedData.value.nombre || !huespedData.value.apellido_paterno || !huespedData.value.email) {
      showNotification('error', 'Por favor, completa los datos del huésped')
      return
    }

    try {
      isLoadingReservation.value = true
      
      console.log(
        "fechas al reservar =",
        formatDateForAPI(checkInDate.value),
        formatDateForAPI(checkOutDate.value)
      )

      const reservationData = {
        fecha_inicio: formatDateForAPI(checkInDate.value),
        fecha_fin: formatDateForAPI(checkOutDate.value),
        tipo_habitacion_id: parseInt(selectedRoomType.value),
        cantidad_personas: cantidadPersonas.value,
        nombre: huespedData.value.nombre,
        apellido_paterno: huespedData.value.apellido_paterno,
        apellido_materno: huespedData.value.apellido_materno,
        email: huespedData.value.email,
        telefono: huespedData.value.telefono || null
      }

      console.log("datos de reserva", reservationData)

      const response = await api.post('/reservas/crear', reservationData)
      console.log("respuesta de crear reserva", response)
      
      if (response.data.success) {
        // ============================================
        // CLAVE: Recargar automáticamente los datos globales
        // ============================================
        await Promise.all([
          loadUserReservations(),
          loadActiveReservation()
        ])
        
        return response.data.data
      }
    } catch (error: any) {
      console.error('Error creando reserva:', error)
      
      let errorMessage = 'Error al crear la reserva'
      if (error.response?.data?.message) {
        errorMessage = error.response.data.message
      } else if (error.response?.data?.errors) {
        const errors = Object.values(error.response.data.errors).flat()
        errorMessage = errors.join(', ')
      }
      
      showNotification('error', errorMessage)
      throw error
    } finally {
      isLoadingReservation.value = false
    }
  }

  // ============================================
  // MODIFICADO: Actualizar estado global
  // ============================================
  const loadUserReservations = async () => {
    // Prevenir múltiples cargas simultáneas
    if (isLoadingReservationsGlobal) return
    
    try {
      isLoadingReservationsGlobal = true
      const response = await api.get('/reservas')
      if (response.data.success) {
        globalUserReservations.value = response.data.data
      }
    } catch (error) {
      console.error('Error cargando reservas de usuario:', error)
      showNotification('error', 'No se pudieron cargar tus reservas')
    } finally {
      isLoadingReservationsGlobal = false
    }
  }

  // ============================================
  // MODIFICADO: Actualizar estado global
  // ============================================
  const loadActiveReservation = async () => {
    try {
      const response = await api.get('/reservas/activa')
      if (response.data.success) {
        globalActiveReservation.value = response.data.data
      }
    } catch (error) {
      console.error('Error cargando reserva activa:', error)
      // No mostrar notificación aquí, puede ser normal no tener reserva activa
    }
  }

  const getReservationById = async (id: number | string) => {
    try {
      const response = await api.get(`/reservas/${id}`)
      if (response.data.success) {
        return response.data.data
      }
      return null
    } catch (error) {
      console.error('Error obteniendo reserva:', error)
      showNotification('error', 'No se pudo cargar la reserva')
      return null
    }
  }

  // ============================================
  // MODIFICADO: Recargar automáticamente después de cancelar
  // ============================================
  const cancelReservation = async (id: number | string) => {
    try {
      const response = await api.patch(`/reservas/${id}/cancel`)
      if (response.data.success) {
        showNotification('success', 'Reserva cancelada exitosamente')
        
        // Refrescar listas globales
        await Promise.all([
          loadUserReservations(),
          loadActiveReservation()
        ])
        return true
      }
      showNotification('error', response.data.message || 'No se pudo cancelar la reserva')
      return false
    } catch (error: any) {
      console.error('Error cancelando reserva:', error)
      const message = error.response?.data?.message || 'No se pudo cancelar la reserva'
      showNotification('error', message)
      return false
    }
  }

  const resetForm = () => {
    checkInDate.value = ''
    checkOutDate.value = ''
    selectedRoomType.value = ''
    cantidadPersonas.value = 2
    huespedData.value = {
      nombre: '',
      apellido_paterno: '',
      apellido_materno: '',
      email: '',
      telefono: '',
      observaciones: ''
    }
    availabilityData.value = null
  }

  return {
    // Estados
    isLoadingAvailability,
    isLoadingReservation,
    isLoadingRoomTypes,
    notification,
    checkInDate,
    checkOutDate,
    selectedRoomType,
    isSelectingCheckIn,
    cantidadPersonas,
    huespedData,
    roomTypes,
    availabilityData,
    userReservations,
    activeReservation,
    weekDays,
    calendarDates,
    
    // Funciones
    showNotification,
    isDateSelected,
    isDateInRange,
    isDateAvailable,
    selectCalendarDate,
    selectRoomType,
    formatDate,
    formatDateForAPI,
    cargarTiposHabitaciones,
    buscarDisponibilidad,
    crearReserva,
    loadUserReservations,
    loadActiveReservation,
    getReservationById,
    cancelReservation,
    resetForm
  }
}