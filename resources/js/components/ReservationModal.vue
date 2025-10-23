<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { useReservationModal } from '@/composables/useReservation'

const carouselRef = ref<HTMLElement | null>(null)

const scrollLeft = () => {
  if (carouselRef.value) {
    carouselRef.value.scrollBy({ left: -300, behavior: 'smooth' })
  }
}

const scrollRight = () => {
  if (carouselRef.value) {
    carouselRef.value.scrollBy({ left: 300, behavior: 'smooth' })
  }
}

// Props y emits
const props = defineProps<{
  open?: boolean
}>()

const emit = defineEmits<{
  'update:open': [value: boolean]
  'reservation:submit': [data: any]
  'close': []
}>()

// Estado del modal
const isOpen = computed({
  get: () => props.open || false,
  set: (value) => {
    emit('update:open', value)
    if (!value) closeModal()
  }
})

// Usar el composable
const {
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
  weekDays,
  calendarDates,
  showNotification,
  isDateSelected,
  isDateInRange,
  isDateAvailable,
  selectCalendarDate,
  selectRoomType,
  formatDate,
  cargarTiposHabitaciones,
  
  buscarDisponibilidad,
  crearReserva,
  roomVisible,
  resetForm
} = useReservationModal()

// Función para cerrar modal
const closeModal = () => {
  resetForm()
  emit('update:open', false)
  emit('close')
}

// Función para manejar la creación de reserva
const handleCrearReserva = async () => {
  try {
    const reserva = await crearReserva()
    if (reserva) {
      showNotification('success', `¡Reserva creada exitosamente! Reserva #${reserva.id_reserva}`)
      emit('reservation:submit', reserva)
      setTimeout(() => {
        isOpen.value = false
        resetForm()
      }, 2000)
    }
  } catch (error) {
    // Error ya manejado en el composable
  }
}



// cuando carguen los tipos de habitación, aseguramos que el carrusel esté al inicio
watch(() => roomTypes.value.length, (len) => {
  if (len > 0 && carouselRef.value) {
    // pequeño delay para que el DOM pinte las cards
    setTimeout(() => {
      carouselRef.value!.scrollTo({ left: 0, behavior: 'smooth' })
    }, 50)
  }
})

</script>

<template>
  <Dialog v-model:open="isOpen">
    <DialogContent class="w-full md:w-[90vw] lg:max-w-7xl max-h-[90vh] overflow-y-auto">
      <DialogHeader class="bg-blue-900 text-white p-6 rounded-t-lg -m-6 mb-6">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-400 rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 2L3 7v11a1 1 0 001 1h12a1 1 0 001-1V7l-7-5z" />
              </svg>
            </div>
            <DialogTitle class="text-2xl font-bold">Sistema de Reservas</DialogTitle>
          </div>
        </div>
      </DialogHeader>

      <!-- Notificación Toast -->
      <div v-if="notification.show"
        class="sticky top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300" :class="{
          'bg-green-500 text-white': notification.type === 'success',
          'bg-red-500 text-white': notification.type === 'error',
          'bg-blue-500 text-white': notification.type === 'info'
        }">
        <div class="flex items-center gap-2">
          <span v-if="notification.type === 'success'">✓</span>
          <span v-else-if="notification.type === 'error'">✗</span>
          <span v-else>ℹ</span>
          <span>{{ notification.message }}</span>
        </div>
      </div>

      <div class="space-y-6">
        <!-- Selección de fechas -->
        <section class="space-y-4">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Selecciona tus fechas</h3>
            <Button @click="buscarDisponibilidad" :disabled="isLoadingAvailability || !checkInDate || !checkOutDate"
              class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400">
              <span v-if="isLoadingAvailability" class="flex items-center gap-2">
                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                  <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                </svg>
                Verificando...
              </span>
              <span v-else>Buscar Disponibilidad</span>
            </Button>
          </div>

          <div class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
              <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de entrada</label>
              <input v-model="checkInDate" type="date"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500" />
            </div>

            <div class="flex-1 min-w-[200px]">
              <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de salida</label>
              <input v-model="checkOutDate" type="date"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500" />
            </div>

            <div class="flex-1 min-w-[150px]">
              <label class="block text-sm font-medium text-gray-700 mb-2">Personas</label>
              <select v-model="cantidadPersonas"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                <option v-for="i in 10" :key="i" :value="i">{{ i }}</option>
              </select>
            </div>
          </div>
        </section>

        <!-- Información de disponibilidad -->
        <div v-if="availabilityData" class="bg-blue-50 border border-blue-200 rounded-lg p-4">
          <div class="flex items-center gap-2 text-blue-800 font-medium mb-2">
            <span>📊</span>
            <span>Información de Disponibilidad</span>
          </div>
          <div class="text-sm text-blue-700">
            <p><strong>Período:</strong> {{ availabilityData.fecha_inicio }} - {{ availabilityData.fecha_fin }}</p>
            <p><strong>Noches:</strong> {{ availabilityData.noches }}</p>
            <p><strong>Habitaciones disponibles:</strong> {{ availabilityData.total_habitaciones_disponibles }}</p>
          </div>
        </div>

        <!-- Calendario y habitaciones -->
        <div class="flex flex-col md:flex-row gap-6">
          <!-- Calendario -->
          <div class="flex-1 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Calendario de disponibilidad</h3>

            <div class="flex items-center gap-4 text-sm mb-4">
              <div class="flex items-center gap-2">
                <div class="w-3 h-3 bg-blue-500 rounded"></div><span>Entrada</span>
              </div>
              <div class="flex items-center gap-2">
                <div class="w-3 h-3 bg-green-500 rounded"></div><span>Salida</span>
              </div>
              <div class="flex items-center gap-2">
                <div class="w-3 h-3 bg-blue-200 rounded"></div><span>Rango</span>
              </div>
            </div>

            <div class="grid grid-cols-7 gap-1 mb-1">
              <div v-for="day in weekDays" :key="day"
                class="bg-blue-900 text-white text-center py-2 px-3 rounded font-medium">
                {{ day }}
              </div>
            </div>

            <div class="grid grid-cols-7 gap-1 mb-3">
              <div v-for="date in calendarDates" :key="date.toISOString()"
                class="border border-gray-200 h-12 flex items-center justify-center text-sm transition-all duration-200"
                :class="{
                  'bg-blue-500 text-white font-bold': checkInDate === formatDate(date),
                  'bg-green-500 text-white font-bold': checkOutDate === formatDate(date),
                  'bg-blue-200': isDateInRange(date),
                  'bg-gray-100 text-gray-400 cursor-not-allowed': !isDateAvailable(date),
                  'hover:bg-blue-50 cursor-pointer': isDateAvailable(date) && !isDateSelected(date) && !isDateInRange(date)
                }" @click="selectCalendarDate(date)">
                {{ date.getDate() }}
              </div>
            </div>

            <div class="text-sm text-gray-600 bg-blue-100 p-3 rounded-lg">
              <p v-if="isSelectingCheckIn" class="font-medium text-blue-800">📅 Selecciona tu fecha de entrada</p>
              <p v-else class="font-medium text-green-800">📅 Selecciona tu fecha de salida</p>
              <p class="mt-1">Haz clic en las fechas para definir tu estadía</p>
            </div>
          </div>

          <!-- Habitaciones -->
          <div class="flex-1 min-w-0 overflow-hidden">
            <div v-if="checkInDate && checkOutDate" class="space-y-4">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Selecciona tu habitación</h3>

              <div v-if="isLoadingRoomTypes" class="flex justify-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
              </div>

              <div v-else-if="roomVisible" class="relative">
                <!-- Carrusel -->
                <div ref="carouselRef"
                  class="flex gap-4 pb-4 snap-x snap-mandatory scroll-smooth hide-scrollbar overflow-x-auto max-w-2xl">
                  <Card v-for="room in roomTypes" :key="room.id"
                    class="flex-shrink-0 w-80 snap-center cursor-pointer transition-all duration-200 hover:shadow-lg transform hover:scale-105"
                    :class="{
                      'ring-2 ring-blue-500 bg-blue-50': selectedRoomType === room.id.toString(),
                      'bg-blue-900 text-white': selectedRoomType === room.id.toString(),
                      'hover:ring-2 hover:ring-blue-300': selectedRoomType !== room.id.toString(),
                      'opacity-50 cursor-not-allowed': room.available_rooms === 0
                    }" @click="room.available_rooms > 0 ? selectRoomType(room.id.toString()) : null">
                    <CardHeader class="pb-2">
                      <CardTitle class="text-lg flex items-center justify-between"
                        :class="selectedRoomType === room.id.toString() ? 'text-white' : 'text-gray-900'">
                        {{ room.name }}
                        <div v-if="selectedRoomType === room.id.toString()" class="text-green-400">✓</div>
                      </CardTitle>
                    </CardHeader>

                    <CardContent class="pt-0 flex flex-col h-full">
                      <CardDescription
                        :class="selectedRoomType === room.id.toString() ? 'text-blue-100' : 'text-gray-600'">
                        {{ room.description }}
                      </CardDescription>

                      <div class="mt-3 text-xl font-bold flex items-center justify-between"
                        :class="selectedRoomType === room.id.toString() ? 'text-white' : 'text-blue-600'">
                        <span>${{ room.price }}/noche</span>
                      </div>

                      <div class="mt-2 text-sm text-gray-500">
                        Capacidad: {{ room.capacity }} personas
                      </div>
                      <div class="text-sm" :class="room.available_rooms > 0 ? 'text-green-600' : 'text-red-600'">
                        Disponibles: {{ room.available_rooms }}
                      </div>

                      <!-- Aquí el mensaje abajo de todo -->
                      <div v-if="selectedRoomType === room.id.toString()"
                        class=" text-center text-sm bg-green-500 text-white px-2 py-1 rounded mt-3">
                        Seleccionada
                      </div>
                    </CardContent>
                  </Card>
                </div>

                <!-- Flechas opcionales -->
                <!-- Flechas de navegación modernas -->
                <button @click="scrollLeft"
                  class="absolute left-2 top-1/2 -translate-y-1/2 bg-white shadow-md rounded-full p-3 hover:bg-blue-100 transition-colors z-10"
                  aria-label="Anterior">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-blue-800">
                    <path d="M15 18l-6-6 6-6" />
                  </svg>
                </button>

                <button @click="scrollRight"
                  class="absolute right-2 top-1/2 -translate-y-1/2 bg-white shadow-md rounded-full p-3 hover:bg-blue-100 transition-colors z-10"
                  aria-label="Siguiente">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-blue-800">
                    <path d="M9 6l6 6-6 6" />
                  </svg>
                </button>

              </div>

            </div>

            <div v-else
              class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-yellow-800 text-sm font-medium">
              ⚠️ Selecciona las fechas primero para ver las habitaciones disponibles.
            </div>
          </div>
        </div>
      </div>

      <!-- Datos del huésped -->
      <div v-if="checkInDate && checkOutDate && selectedRoomType" class="space-y-4">
        <h3 class="text-lg font-semibold text-gray-900">Datos del Huésped</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Nombre *</label>
            <input v-model="huespedData.nombre" type="text" required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Apellido Paterno *</label>
            <input v-model="huespedData.apellido_paterno" type="text" required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Apellido Materno</label>
            <input v-model="huespedData.apellido_materno" type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
            <input v-model="huespedData.email" type="email" required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
            <input v-model="huespedData.telefono" type="tel"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500" />
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">Observaciones</label>
            <textarea v-model="huespedData.observaciones" rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500"></textarea>
          </div>
        </div>
      </div>

      <!-- Botones -->
      <div class="flex justify-between items-center pt-6 border-t">
        <Button variant="outline" @click="closeModal" class="px-6 py-2">Cancelar</Button>

        <div class="flex items-center gap-4">
          <div v-if="checkInDate && checkOutDate && selectedRoomType" class="text-sm text-gray-600 leading-tight">
            <div>📅 {{ checkInDate }} - {{ checkOutDate }}</div>
            <div>🏨 {{roomTypes.find(r => r.id.toString() === selectedRoomType)?.name}}</div>
            <div>👥 {{ cantidadPersonas }} personas</div>
          </div>

          <Button @click="handleCrearReserva"
            :disabled="!checkInDate || !checkOutDate || !selectedRoomType || isLoadingReservation"
            class="bg-blue-900 hover:bg-blue-800 text-white px-6 py-2 disabled:bg-gray-400">
            <span v-if="isLoadingReservation" class="flex items-center gap-2">
              <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                <path class="opacity-75" fill="currentColor"
                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
              </svg>
              Procesando...
            </span>
            <span v-else>Confirmar Reserva</span>
          </Button>
        </div>
      </div>
    </DialogContent>
  </Dialog>
</template>

<style scoped>
.hide-scrollbar::-webkit-scrollbar {
  display: none;
}

.hide-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

.hide-scrollbar::-webkit-scrollbar {
  display: none;
}

.hide-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

/* opcional: evita que el contenido sobresalga verticalmente */
.carousel-card {
  box-sizing: border-box;
}
</style>
