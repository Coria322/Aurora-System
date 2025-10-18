document.addEventListener('DOMContentLoaded', () => {
    const carousel = document.querySelector('.carousel.ser');
    const prevBtn = document.querySelector('.carousel-btn.prev.ser');
    const nextBtn = document.querySelector('.carousel-btn.next.ser');
    
    const cardWidth = document.querySelector('.servicio__card').offsetWidth;
    const gap = 20; // el mismo gap que en CSS

    prevBtn.addEventListener('click', () => {
        carousel.scrollBy({ left: -(cardWidth + gap) * 3, behavior: 'smooth' }); // 3 cards hacia atrás
    });

    nextBtn.addEventListener('click', () => {
        carousel.scrollBy({ left: (cardWidth + gap) * 3, behavior: 'smooth' }); // 3 cards hacia adelante
    });
});
