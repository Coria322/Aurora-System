<template>
    <transition name="modal-fade">
        <div class="modal-overlay" @click="$emit('close')">
            <div class="modal-container" @click.stop>
                <button class="modal-close" @click="$emit('close')">&times;</button>

                <div class="modal-content" v-if="habitacion">
                    <div class="modal-imagen-container">
                        <img :src="habitacion.imagen" :alt="habitacion.nombre" class="modal-imagen" />
                    </div>

                    <div class="modal-info">
                        <h2 class="modal-titulo">{{ habitacion.nombre }}</h2>
                        <p class="modal-descripcion">{{ habitacion.descripcion }}</p>

                        <!-- Detalles principales -->
                        <div class="modal-detalles" v-if="habitacion.capacidad_maxima || habitacion.precio_noche || habitacion.servicios_incluidos">
                            <div v-if="habitacion.capacidad_maxima" class="detalle-item">
                                <strong>Capacidad:</strong> {{ habitacion.capacidad_maxima }} personas
                            </div>

                            <div v-if="habitacion.precio_noche" class="detalle-item">
                                <strong>Precio por noche:</strong> ${{ habitacion.precio_noche }}
                            </div>

                            <div v-if="habitacion.servicios_incluidos" class="detalle-item">
                                <strong>Servicios incluidos:</strong>
                                <ul class="servicios-lista">
                                    <li v-for="(servicio, index) in serviciosSeparados" :key="index">
                                        {{ servicio }}
                                    </li>
                                </ul>
                            </div>

                            <div v-if="habitacion.activo !== undefined" class="detalle-item">
                                <strong>Disponibilidad:</strong>
                                <span :class="habitacion.activo ? 'activo' : 'inactivo'">
                                    {{ habitacion.activo ? 'Disponible' : 'No disponible' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script setup lang="ts">
import { computed } from 'vue'

export interface Habitacion {
    id_tipo_habitacion: number
    nombre: string
    descripcion: string
    imagen: string
    capacidad_maxima?: number
    precio_noche?: number | string
    servicios_incluidos?: string
    activo?: boolean
}

const props = defineProps<{
    habitacion: Habitacion
}>()

// Convierte la lista de servicios en array limpio
const serviciosSeparados = computed(() => {
    if (!props.habitacion.servicios_incluidos) return []
    return props.habitacion.servicios_incluidos
        .split(',')
        .map(s => s.trim())
        .filter(s => s.length > 0)
})

</script>

<style scoped>
/* Estilos del Modal */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
    padding: 20px;
}

.modal-container {
    background: white;
    border-radius: 12px;
    max-width: 900px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    position: relative;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
}

.modal-close {
    position: absolute;
    top: 15px;
    right: 15px;
    background: rgba(0, 0, 0, 0.6);
    color: white;
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    font-size: 28px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
    transition: background 0.3s;
}

.modal-close:hover {
    background: rgba(0, 0, 0, 0.8);
}

.modal-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    padding: 30px;
}

.modal-imagen-container {
    width: 100%;
}

.modal-imagen {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 8px;
    max-height: 500px;
}

.modal-info {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.modal-titulo {
    font-size: 2rem;
    font-weight: bold;
    color: #333;
    margin: 0;
}

.modal-descripcion {
    font-size: 1rem;
    line-height: 1.6;
    color: #666;
}

.modal-detalles {
    display: flex;
    flex-direction: column;
    gap: 15px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
}

.detalle-item {
    font-size: 1.1rem;
    color: #333;
}

.detalle-item strong {
    color: #000;
}

.servicios-lista {
    margin-top: 10px;
    padding-left: 20px;
}

.servicios-lista li {
    list-style-type: disc;
    margin-bottom: 6px;
    color: #555;
}

.activo {
    color: green;
    font-weight: bold;
}

.inactivo {
    color: red;
    font-weight: bold;
}

/* Animación del Modal */
.modal-fade-enter-active,
.modal-fade-leave-active {
    transition: opacity 0.3s;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
    opacity: 0;
}

/* Responsive */
@media (max-width: 768px) {
    .modal-content {
        grid-template-columns: 1fr;
        padding: 20px;
    }

    .modal-titulo {
        font-size: 1.5rem;
    }
}
</style>
