<script setup lang="ts">
import { ref } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import ReservationModal from '../components/ReservationModal.vue';
// import { Button } from '../components/ui/button'; // Removed unused import

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

// Estado del modal de reservas
const isReservationModalOpen = ref(false)

// Función para abrir el modal de reservas
const openReservationModal = () => {
    isReservationModalOpen.value = true
}

// Función para manejar el envío de la reserva
const handleReservationSubmit = (reservationData: any) => {
    console.log('Datos de la reserva:', reservationData)
    // Aquí puedes implementar la lógica para procesar la reserva
    alert(`Reserva creada para ${reservationData.roomName} del ${reservationData.checkIn} al ${reservationData.checkOut}`)
}
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <!-- Botón para abrir el modal de reservas -->
            <div class="flex justify-center mb-6">
                <button 
                    @click="openReservationModal"
                    class="bg-blue-900 hover:bg-blue-800 text-white px-8 py-3 text-lg font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-200"
                >
                    🏨 Nueva Reserva
                </button>
            </div>

            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
            </div>
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                <PlaceholderPattern />
            </div>
        </div>

        <!-- Modal de reservas -->
        <ReservationModal 
            v-model:open="isReservationModalOpen"
            @reservation:submit="handleReservationSubmit"
        />
    </AppLayout>
</template>
