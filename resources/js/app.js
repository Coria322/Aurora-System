import '../js/bannervid';
import '../js/carousel'

document.addEventListener('DOMContentLoaded', function () {
    const navbarToggle = document.getElementById('navbarToggle');
    const navbarMenu = document.getElementById('navbarMenu');

    // Verifica que ambos elementos existan
    if (navbarToggle && navbarMenu) {
        // Agrega un evento de clic al botón de hamburguesa
        navbarToggle.addEventListener('click', () => {
            // Alterna la clase 'is-active' en el menú y en el botón
            navbarMenu.classList.toggle('is-active');
            navbarToggle.classList.toggle('is-active');
        });
    }
});