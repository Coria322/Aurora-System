<script setup lang="ts">
import { onMounted, ref, computed } from 'vue'
import { useReservationModal } from '@/components/../composables/useReservation'

const {
  userReservations,
  activeReservation,
  loadUserReservations,
  loadActiveReservation,
  getReservationById,
  cancelReservation,
  showNotification,
  currentPage,
  totalPages,
  nextPage,
  prevPage,
  goToPage,
  perPage
} = useReservationModal()


onMounted(async () => {
  await Promise.all([
    loadActiveReservation(),
    loadUserReservations()
  ])
  console.log('ActiveReservation:', activeReservation.value)
})


const formatCurrency = (value?: number | string) => {
  if (value === undefined || value === null) return '-'
  const num = typeof value === 'string' ? parseFloat(value) : value
  return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(num)
}

const formatDate = (date?: string) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('es-MX')
}

// Filtros
const estadoFilter = ref('') // '', 'pendiente', 'confirmada', 'cancelada'
const dateFrom = ref('')
const dateTo = ref('')

const filteredReservations = computed(() => {
  let list = userReservations.value || []
  if (estadoFilter.value) {
    list = list.filter((r: any) => r.estado === estadoFilter.value)
  }
  if (dateFrom.value) {
    const from = new Date(`${dateFrom.value}T00:00:00`)
    list = list.filter((r: any) => new Date(r.fecha_checkin) >= from)
  }
  if (dateTo.value) {
    const to = new Date(`${dateTo.value}T23:59:59`)
    list = list.filter((r: any) => new Date(r.fecha_checkout) <= to)
  }
  return list
})

// Modal de detalles
const detailOpen = ref(false)
const selectedReservation = ref<any | null>(null)

const openDetail = async (res: any) => {
  const data = await getReservationById(res.id_reserva)
  selectedReservation.value = data || res
  detailOpen.value = true
}

const closeDetail = () => {
  selectedReservation.value = null
  detailOpen.value = false
}

// Cancelación
const cancellingId = ref<number | null>(null)
const canCancel = (res: any) => ['pendiente', 'confirmada'].includes(res.estado)
const handleCancel = async (res: any) => {
  if (!canCancel(res)) return
  cancellingId.value = res.id_reserva
  const ok = await cancelReservation(res.id_reserva)
  if (ok) {
    showNotification('success', `Reserva RES-${String(res.id_reserva).padStart(6, '0')} cancelada`)
  }
  cancellingId.value = null
}

// Generar array de páginas para mostrar
const pageNumbers = computed(() => {
  const pages = []
  const total = totalPages.value
  const current = currentPage.value

  if (total <= 7) {
    // Mostrar todas las páginas si son pocas
    for (let i = 1; i <= total; i++) {
      pages.push(i)
    }
  } else {
    // Mostrar páginas con elipsis
    if (current <= 3) {
      pages.push(1, 2, 3, 4, '...', total)
    } else if (current >= total - 2) {
      pages.push(1, '...', total - 3, total - 2, total - 1, total)
    } else {
      pages.push(1, '...', current - 1, current, current + 1, '...', total)
    }
  }

  return pages
})
</script>

<template>
  <div class="flex flex-col gap-4">
    <!-- Hero / Encabezado con logo -->
    <div
      class="flex items-center gap-3 rounded-xl border border-sidebar-border/70 bg-white/70 p-4 shadow-sm dark:border-sidebar-border dark:bg-black/30">
      <img src="/images/auroralogo.png" alt="Aurora" class="h-10 w-10" />
      <div>
        <h2 class="text-lg font-semibold">Panel de reservas</h2>
        <p class="text-sm text-muted-foreground">Revisa tu reserva activa y el historial reciente</p>
      </div>
    </div>

    <!-- Tarjeta de Reserva Activa -->
    <div
      class="rounded-xl border border-sidebar-border/70 bg-white/70 p-4 shadow-sm dark:border-sidebar-border dark:bg-black/30">
      <h3 class="mb-3 text-base font-semibold">Reserva activa</h3>
      <div v-if="activeReservation" class="grid grid-cols-1 gap-3 md:grid-cols-3">
        <div class="rounded-lg bg-white/70 p-3 shadow-sm dark:bg-black/30">
          <div class="text-xs text-muted-foreground">Fechas</div>
          <div class="text-sm">{{ formatDate(activeReservation.fecha_checkin) }} → {{
            formatDate(activeReservation.fecha_checkout) }}</div>
        </div>
        <div class="rounded-lg bg-white/70 p-3 shadow-sm dark:bg-black/30">
          <div class="text-xs text-muted-foreground">Huésped</div>
          <div class="text-sm">{{ activeReservation.huesped?.nombre }} {{ activeReservation.huesped?.apellido_paterno }}
          </div>
        </div>
        <div class="rounded-lg bg-white/70 p-3 shadow-sm dark:bg-black/30">
          <div class="text-xs text-muted-foreground">Total</div>
          <div class="text-sm">{{ formatCurrency(activeReservation.total) }}</div>
        </div>
        <div class="md:col-span-3 rounded-lg bg-white/70 p-3 shadow-sm dark:bg-black/30">
          <div class="text-xs text-muted-foreground">Habitación</div>
          <div class="text-sm">
            {{ activeReservation?.detalle_reservas?.[0]?.habitacion?.numero_habitacion || '-' }}
            <span class="text-xs text-muted-foreground">
              · {{ activeReservation?.detalle_reservas?.[0]?.habitacion?.tipo_habitacion?.nombre || '—' }}
            </span>
          </div>
        </div>
      </div>
      <div v-else class="text-sm text-muted-foreground">No tienes una reserva activa en este momento.</div>
    </div>

    <!-- Tabla de Reservas -->
    <div
      class="rounded-xl border border-sidebar-border/70 bg-white/70 p-4 shadow-sm dark:border-sidebar-border dark:bg-black/30">
      <div class="mb-3 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
        <h3 class="text-base font-semibold">Tus reservas</h3>
        <div class="flex flex-col gap-2 md:flex-row md:items-center">
          <!-- Filtro por estado -->
          <div class="flex items-center gap-2">
            <label class="text-xs text-muted-foreground">Estado</label>
            <select v-model="estadoFilter"
              class="rounded-md border border-gray-300 bg-white px-2 py-1 text-sm dark:border-white/10 dark:bg-black/30">
              <option value="">Todos</option>
              <option value="pendiente">Pendiente</option>
              <option value="confirmada">Confirmada</option>
              <option value="cancelada">Cancelada</option>
            </select>
          </div>

          <!-- Filtro por fecha desde -->
          <div class="flex items-center gap-2">
            <label class="text-xs text-muted-foreground">Desde</label>
            <input v-model="dateFrom" type="date"
              class="rounded-md border border-gray-300 bg-white px-2 py-1 text-sm dark:border-white/10 dark:bg-black/30" />
          </div>

          <!-- Filtro por fecha hasta -->
          <div class="flex items-center gap-2">
            <label class="text-xs text-muted-foreground">Hasta</label>
            <input v-model="dateTo" type="date"
              class="rounded-md border border-gray-300 bg-white px-2 py-1 text-sm dark:border-white/10 dark:bg-black/30" />
          </div>

          <!-- Selector de reservas por página -->
          <div class="flex items-center gap-2">
            <label class="text-xs text-muted-foreground">Por página</label>
            <select v-model="perPage" @change ="loadUserReservations(1)"
              class="rounded-md border border-gray-300 bg-white px-2 py-1 text-sm dark:border-white/10 dark:bg-black/30">
              <option v-for="n in [5, 10, 15, 20, 25, 50]" :key="n" :value="n">{{ n }}</option>
            </select>
          </div>
        </div>
      </div>

      <div class="overflow-x-auto rounded-lg">
        <table class="min-w-full text-left text-sm">
          <thead class="sticky top-0 bg-gray-50 text-xs uppercase text-gray-500 dark:bg-white/10 dark:text-gray-300">
            <tr>
              <th class="px-3 py-2">#</th>
              <th class="px-3 py-2">Check-In</th>
              <th class="px-3 py-2">Check-Out</th>
              <th class="px-3 py-2">Estado</th>
              <th class="px-3 py-2">Habitación</th>
              <th class="px-3 py-2">Total</th>
              <th class="px-3 py-2">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="filteredReservations.length === 0">
              <td colspan="7" class="px-3 py-6 text-center text-sm text-muted-foreground">No hay reservas que coincidan
                con los filtros.</td>
            </tr>
            <tr v-for="res in filteredReservations" :key="res.id_reserva"
              class="border-t border-gray-200/60 odd:bg-white/40 hover:bg-gray-50/80 dark:border-white/10 dark:odd:bg-white/5 dark:hover:bg-white/10">
              <td class="px-3 py-2">RES-{{ String(res.id_reserva).padStart(6, '0') }}</td>
              <td class="px-3 py-2">{{ formatDate(res.fecha_checkin) }}</td>
              <td class="px-3 py-2">{{ formatDate(res.fecha_checkout) }}</td>
              <td class="px-3 py-2">
                <span class="rounded-full px-2 py-0.5 text-[11px] uppercase tracking-wide" :class="{
                  'bg-yellow-100 text-yellow-700': res.estado === 'pendiente',
                  'bg-emerald-100 text-emerald-700': res.estado === 'confirmada',
                  'bg-red-100 text-red-700': res.estado === 'cancelada'
                }">{{ res.estado }}</span>
              </td>
              <td class="px-3 py-2">
                {{ res.detalle_reservas?.[0]?.habitacion?.numero_habitacion || '-' }}
                <span class="text-xs text-muted-foreground">
                  {{ res.detalle_reservas?.[0]?.habitacion?.tipo_habitacion?.nombre || '' }}
                </span>
              </td>
              <td class="px-3 py-2">{{ formatCurrency(res.total) }}</td>
              <td class="px-3 py-2">
                <div class="flex gap-2">
                  <button @click="openDetail(res)"
                    class="rounded-md bg-blue-600 px-2 py-1 text-xs text-white hover:bg-blue-700">Ver</button>
                  <button @click="handleCancel(res)" :disabled="!canCancel(res) || cancellingId === res.id_reserva"
                    class="rounded-md px-2 py-1 text-xs text-white" :class="{
                      'bg-red-600 hover:bg-red-700': canCancel(res) && cancellingId !== res.id_reserva,
                      'bg-gray-400 cursor-not-allowed': !canCancel(res) || cancellingId === res.id_reserva
                    }">
                    {{ cancellingId === res.id_reserva ? 'Cancelando...' : 'Cancelar' }}
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Controles de paginación -->
      <div v-if="totalPages > 1"
        class="mt-4 flex items-center justify-between border-t border-gray-200/60 pt-4 dark:border-white/10">
        <div class="text-sm text-muted-foreground">
          Página {{ currentPage }} de {{ totalPages }}
        </div>

        <div class="flex items-center gap-1">
          <!-- Botón anterior -->
          <button @click="prevPage" :disabled="currentPage === 1"
            class="rounded-md px-3 py-1.5 text-sm font-medium transition-colors" :class="currentPage === 1
              ? 'cursor-not-allowed bg-gray-100 text-gray-400 dark:bg-white/5'
              : 'bg-white text-gray-700 hover:bg-gray-50 dark:bg-black/30 dark:text-gray-300 dark:hover:bg-white/10'">
            Anterior
          </button>

          <!-- Números de página -->
          <template v-for="(page, idx) in pageNumbers" :key="idx">
            <button v-if="page !== '...'" @click="goToPage(page as number)"
              class="rounded-md px-3 py-1.5 text-sm font-medium transition-colors"
              :class="page === currentPage
                ? 'bg-blue-600 text-white'
                : 'bg-white text-gray-700 hover:bg-gray-50 dark:bg-black/30 dark:text-gray-300 dark:hover:bg-white/10'">
              {{ page }}
            </button>
            <span v-else class="px-2 text-gray-500">...</span>
          </template>

          <!-- Botón siguiente -->
          <button @click="nextPage" :disabled="currentPage === totalPages"
            class="rounded-md px-3 py-1.5 text-sm font-medium transition-colors" :class="currentPage === totalPages
              ? 'cursor-not-allowed bg-gray-100 text-gray-400 dark:bg-white/5'
              : 'bg-white text-gray-700 hover:bg-gray-50 dark:bg-black/30 dark:text-gray-300 dark:hover:bg-white/10'">
            Siguiente
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Detalles -->
    <div v-if="detailOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
      <div
        class="w-full max-w-lg rounded-xl border border-sidebar-border/70 bg-white p-4 shadow-xl dark:border-white/10 dark:bg-neutral-900">
        <div class="mb-3 flex items-center justify-between">
          <h4 class="text-base font-semibold">Detalles de la reserva</h4>
          <button @click="closeDetail"
            class="rounded-md px-2 py-1 text-sm hover:bg-black/5 dark:hover:bg-white/10">Cerrar</button>
        </div>
        <div v-if="selectedReservation" class="grid grid-cols-1 gap-3 md:grid-cols-2">
          <div>
            <div class="text-xs text-muted-foreground">Reserva</div>
            <div class="text-sm">RES-{{ String(selectedReservation.id_reserva).padStart(6, '0') }}</div>
          </div>
          <div>
            <div class="text-xs text-muted-foreground">Estado</div>
            <div class="text-sm">{{ selectedReservation.estado }}</div>
          </div>
          <div>
            <div class="text-xs text-muted-foreground">Check-In</div>
            <div class="text-sm">{{ formatDate(selectedReservation.fecha_checkin) }}</div>
          </div>
          <div>
            <div class="text-xs text-muted-foreground">Check-Out</div>
            <div class="text-sm">{{ formatDate(selectedReservation.fecha_checkout) }}</div>
          </div>
          <div>
            <div class="text-xs text-muted-foreground">Huésped</div>
            <div class="text-sm">{{ selectedReservation.huesped?.nombre }} {{
              selectedReservation.huesped?.apellido_paterno }}</div>
          </div>
          <div>
            <div class="text-xs text-muted-foreground">Total</div>
            <div class="text-sm">{{ formatCurrency(selectedReservation.total) }}</div>
          </div>
          <div class="md:col-span-2">
            <div class="text-xs text-muted-foreground">Observaciones</div>
            <div class="text-sm">{{ selectedReservation.observaciones || '-' }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>