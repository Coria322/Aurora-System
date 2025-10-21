document.addEventListener('DOMContentLoaded', () => {
  const video = document.getElementById('bannerVideo');
  const sources = [
    '/videos/Video_promocional_de_Aurora_Suites.mp4#t=5',
    '/videos/Generación_de_Video_de_Restaurantes.mp4'
  ];
  let current = 0;
  let firstVideo = true;

  // Establece el primer video
  video.src = sources[current];
  video.play();

  // Encadenar videos cuando termina
  video.addEventListener('ended', () => {
    current = (current + 1) % sources.length;
    video.src = sources[current];
    video.load();   // recarga el video
    video.play();   // reproduce el siguiente
  });
});
