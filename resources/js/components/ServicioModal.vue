<template>
    <transition name="modal-fade">
        <div class="modal-overlay" @click="$emit('close')">
            <div class="modal-container" @click.stop>
                <button class="modal-close" @click="$emit('close')">&times;</button>

                <div class="modal-content" v-if="servicio">
                    <div class="modal-imagen-container">
                        <img 
                        :src="detalleImagen(servicio.imagen)" 
                        :alt="servicio.nombre_servicio" 
                        class="modal-imagen"
                        @error="onImageError" 
                        />

                    </div>

                    <div class="modal-info">
                        <h2 class="modal-titulo">{{ servicio.nombre_servicio }}</h2>
                        <p class="modal-descripcion">{{ servicio.descripcion }}</p>

                        <div class="modal-detalles">
                            <div v-if="servicio.precio" class="detalle-item">
                                <strong>Precio:</strong> ${{ servicio.precio }}
                            </div>

                            <div v-if="servicio.tipo_servicio" class="detalle-item">
                                <strong>Tipo de servicio:</strong> {{ servicio.tipo_servicio }}
                            </div>

                            <div v-if="servicio.activo !== undefined" class="detalle-item">
                                <strong>Disponibilidad:</strong>
                                <span :class="servicio.activo ? 'activo' : 'inactivo'">
                                    {{ servicio.activo ? 'Disponible' : 'No disponible' }}
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
export interface Servicio {
    id_servicio: number
    nombre_servicio: string
    descripcion: string
    precio?: number
    tipo_servicio?: string
    imagen?: string
    activo?: boolean
}

const defaultImage = '/images/Servicios/servicio_generico.png'

const onImageError = (event: Event) => {
  const img = event.target as HTMLImageElement
  // Si la imagen de detalle falla, intenta con la original
  if (!img.dataset.fallbackTried) {
    img.dataset.fallbackTried = 'true'
    const original = img.src.replace('-detail', '')
    console.log(original)
    img.src = original
  } else {
    // Si la original también falla, usa la genérica
    img.src = defaultImage
  }
}


const props = defineProps<{
    servicio: Servicio
}>()

const detalleImagen = (imagen?: string) => {
    if (!imagen) return '/images/Servicios/servicio_generico.png'
    
    const base = '/images/Servicios/Detalle/'
    
    // Extraer solo el nombre del archivo si es una URL absoluta
    let nombreArchivo = imagen
    if (imagen.startsWith('http://') || imagen.startsWith('https://')) {
        // Obtener solo el nombre del archivo desde la URL
        nombreArchivo = imagen.split('/').pop() || imagen
    }
    
    // Remover el base path si ya lo tiene (por si acaso)
    nombreArchivo = nombreArchivo.replace(/^\/images\/Servicios\//i, '')
    
    // Agregar el sufijo -detail antes de la extensión
    const parts = nombreArchivo.split('.')
    const ext = parts.pop()
    
    return `${base}${parts.join('.')}-detail.${ext}`
}
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