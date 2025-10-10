<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import { ref } from 'vue';
import ReservationModal from '../components/ReservationModal.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

// Estado del modal de reservas
const isReservationModalOpen = ref(false);

// Función para abrir el modal de reservas
const openReservationModal = () => {
    isReservationModalOpen.value = true;
};

// Función para manejar el envío de reservas
const handleReservationSubmit = (reservationData: any) => {
    console.log('Datos de reserva:', reservationData);
    alert('¡Reserva creada exitosamente!');
    isReservationModalOpen.value = false;
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <!-- Botón para abrir modal de reservas -->
            <div class="flex justify-end mb-4">
                <button 
                    @click="openReservationModal"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-200"
                >
                    Nueva Reserva
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
        
        <!-- Modal de Reservas -->
        <ReservationModal 
            :open="isReservationModalOpen"
            @reservation:submit="handleReservationSubmit"
            @close="isReservationModalOpen = false"
        />
    </AppLayout>
</template>
