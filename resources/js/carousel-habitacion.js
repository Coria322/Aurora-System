document.addEventListener('DOMContentLoaded', () => {
    const carousel = document.querySelector('.carousel');
    const prevBtn = document.querySelector('.carousel-btn.prev');
    const nextBtn = document.querySelector('.carousel-btn.next');
    
    const cardWidth = document.querySelector('.habitacion__card').offsetWidth;
    const gap = 20; // el mismo gap que en CSS

    prevBtn.addEventListener('click', () => {
        carousel.scrollBy({ left: -(cardWidth + gap) * 3, behavior: 'smooth' }); // 3 cards hacia atrás
    });

    nextBtn.addEventListener('click', () => {
        carousel.scrollBy({ left: (cardWidth + gap) * 3, behavior: 'smooth' }); // 3 cards hacia adelante
    });
});
