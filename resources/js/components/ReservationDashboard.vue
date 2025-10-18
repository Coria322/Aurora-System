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
  showNotification
} = useReservationModal()

onMounted(async () => {
  await Promise.all([
    loadActiveReservation(),
    loadUserReservations()
  ])
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
</script>

<template>
  <div class="flex flex-col gap-4">
    <!-- Hero / Encabezado con logo -->
    <div class="flex items-center gap-3 rounded-xl border border-sidebar-border/70 bg-white/70 p-4 shadow-sm dark:border-sidebar-border dark:bg-black/30">
      <img src="/auroralogo.png" alt="Aurora" class="h-10 w-10" />
      <div>
        <h2 class="text-lg font-semibold">Panel de reservas</h2>
        <p class="text-sm text-muted-foreground">Revisa tu reserva activa y el historial reciente</p>
      </div>
    </div>

    <!-- Tarjeta de Reserva Activa -->
    <div class="rounded-xl border border-sidebar-border/70 bg-white/70 p-4 shadow-sm dark:border-sidebar-border dark:bg-black/30">
      <h3 class="mb-3 text-base font-semibold">Reserva activa</h3>
      <div v-if="activeReservation" class="grid grid-cols-1 gap-3 md:grid-cols-3">
        <div class="rounded-lg bg-white/70 p-3 shadow-sm dark:bg-black/30">
          <div class="text-xs text-muted-foreground">Fechas</div>
          <div class="text-sm">{{ formatDate(activeReservation.fecha_checkin) }} → {{ formatDate(activeReservation.fecha_checkout) }}</div>
        </div>
        <div class="rounded-lg bg-white/70 p-3 shadow-sm dark:bg-black/30">
          <div class="text-xs text-muted-foreground">Huésped</div>
          <div class="text-sm">{{ activeReservation.huesped?.nombre }} {{ activeReservation.huesped?.apellido_paterno }}</div>
        </div>
        <div class="rounded-lg bg-white/70 p-3 shadow-sm dark:bg-black/30">
          <div class="text-xs text-muted-foreground">Total</div>
          <div class="text-sm">{{ formatCurrency(activeReservation.total) }}</div>
        </div>
        <div class="md:col-span-3 rounded-lg bg-white/70 p-3 shadow-sm dark:bg-black/30">
          <div class="text-xs text-muted-foreground">Habitación</div>
          <div class="text-sm">
            {{ activeReservation?.detalleReservas?.[0]?.habitacion?.numero_habitacion || '-' }}
            <span class="text-xs text-muted-foreground">
              · {{ activeReservation?.detalleReservas?.[0]?.habitacion?.tipo_habitacion?.nombre || '—' }}
            </span>
          </div>
        </div>
      </div>
      <div v-else class="text-sm text-muted-foreground">No tienes una reserva activa en este momento.</div>
    </div>

    <!-- Tabla de Reservas -->
    <div class="rounded-xl border border-sidebar-border/70 bg-white/70 p-4 shadow-sm dark:border-sidebar-border dark:bg-black/30">
      <div class="mb-3 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
        <h3 class="text-base font-semibold">Tus reservas</h3>
        <div class="flex flex-col gap-2 md:flex-row md:items-center">
          <div class="flex items-center gap-2">
            <label class="text-xs text-muted-foreground">Estado</label>
            <select v-model="estadoFilter" class="rounded-md border border-gray-300 bg-white px-2 py-1 text-sm dark:border-white/10 dark:bg-black/30">
              <option value="">Todos</option>
              <option value="pendiente">Pendiente</option>
              <option value="confirmada">Confirmada</option>
              <option value="cancelada">Cancelada</option>
            </select>
          </div>
          <div class="flex items-center gap-2">
            <label class="text-xs text-muted-foreground">Desde</label>
            <input v-model="dateFrom" type="date" class="rounded-md border border-gray-300 bg-white px-2 py-1 text-sm dark:border-white/10 dark:bg-black/30" />
          </div>
          <div class="flex items-center gap-2">
            <label class="text-xs text-muted-foreground">Hasta</label>
            <input v-model="dateTo" type="date" class="rounded-md border border-gray-300 bg-white px-2 py-1 text-sm dark:border-white/10 dark:bg-black/30" />
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
              <td colspan="7" class="px-3 py-6 text-center text-sm text-muted-foreground">No hay reservas que coincidan con los filtros.</td>
            </tr>
            <tr v-for="res in filteredReservations" :key="res.id_reserva" class="border-t border-gray-200/60 odd:bg-white/40 hover:bg-gray-50/80 dark:border-white/10 dark:odd:bg-white/5 dark:hover:bg-white/10">
              <td class="px-3 py-2">RES-{{ String(res.id_reserva).padStart(6, '0') }}</td>
              <td class="px-3 py-2">{{ formatDate(res.fecha_checkin) }}</td>
              <td class="px-3 py-2">{{ formatDate(res.fecha_checkout) }}</td>
              <td class="px-3 py-2">
                <span class="rounded-full px-2 py-0.5 text-[11px] uppercase tracking-wide"
                  :class="{
                    'bg-yellow-100 text-yellow-700': res.estado === 'pendiente',
                    'bg-emerald-100 text-emerald-700': res.estado === 'confirmada',
                    'bg-red-100 text-red-700': res.estado === 'cancelada'
                  }"
                >{{ res.estado }}</span>
              </td>
              <td class="px-3 py-2">
                {{ res.detalleReservas?.[0]?.habitacion?.numero_habitacion || '-' }}
                <span class="text-xs text-muted-foreground">
                  {{ res.detalleReservas?.[0]?.habitacion?.tipo_habitacion?.nombre || '' }}
                </span>
              </td>
              <td class="px-3 py-2">{{ formatCurrency(res.total) }}</td>
              <td class="px-3 py-2">
                <div class="flex gap-2">
                  <button @click="openDetail(res)" class="rounded-md bg-blue-600 px-2 py-1 text-xs text-white hover:bg-blue-700">Ver</button>
                  <button
                    @click="handleCancel(res)"
                    :disabled="!canCancel(res) || cancellingId === res.id_reserva"
                    class="rounded-md px-2 py-1 text-xs text-white"
                    :class="{
                      'bg-red-600 hover:bg-red-700': canCancel(res) && cancellingId !== res.id_reserva,
                      'bg-gray-400 cursor-not-allowed': !canCancel(res) || cancellingId === res.id_reserva
                    }"
                  >
                    {{ cancellingId === res.id_reserva ? 'Cancelando...' : 'Cancelar' }}
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal Detalles -->
    <div v-if="detailOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
      <div class="w-full max-w-lg rounded-xl border border-sidebar-border/70 bg-white p-4 shadow-xl dark:border-white/10 dark:bg-neutral-900">
        <div class="mb-3 flex items-center justify-between">
          <h4 class="text-base font-semibold">Detalles de la reserva</h4>
          <button @click="closeDetail" class="rounded-md px-2 py-1 text-sm hover:bg-black/5 dark:hover:bg-white/10">Cerrar</button>
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
            <div class="text-sm">{{ selectedReservation.huesped?.nombre }} {{ selectedReservation.huesped?.apellido_paterno }}</div>
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


