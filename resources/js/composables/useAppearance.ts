import { onMounted, ref } from 'vue';

type Appearance = 'light' | 'dark' | 'system';

// Forzar modo claro: siempre elimina 'dark'
export function updateTheme() {
    if (typeof window === 'undefined') return;
    document.documentElement.classList.remove('dark'); // elimina dark
    document.documentElement.classList.add('light');   // opcional, si quieres clase light
}

// Función para inicializar tema
export function initializeTheme() {
    updateTheme(); // forzar claro desde el inicio
}

// Composable
const appearance = ref<Appearance>('light');

export function useAppearance() {
    onMounted(() => {
        appearance.value = 'light'; // siempre claro
        updateTheme();
    });

    function updateAppearance(value: Appearance) {
        // Ignoramos cualquier intento de cambiar a 'dark' o 'system'
        appearance.value = 'light';
        updateTheme();
    }

    return {
        appearance,
        updateAppearance,
    };
}
