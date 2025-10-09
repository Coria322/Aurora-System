<nav class="navbar">
    <div class="navbar__brand">
        <div class="navbar__logo">
            <img src="{{ asset('images/auroralogo.png') }}" alt="Logo-Hotel">
        </div>
        <a href="/" class="navbar__brand-name">
            <strong>AURORA</strong>
            <span>Suites & Resorts</span>
        </a>
    </div>

    <button class="navbar__toggle" id="navbarToggle" aria-label="Abrir menú">
        <span class="navbar__toggle-bar"></span>
        <span class="navbar__toggle-bar"></span>
        <span class="navbar__toggle-bar"></span>
    </button>

    <div class="navbar__menu" id="navbarMenu">
        <ul class="navbar__links">
            <li><a href="#inicio" class="navbar__link">Inicio</a></li>
            <li><a href="#habitaciones" class="navbar__link">Habitaciones</a></li>
            <li><a href="#servicios" class="navbar__link">Servicios</a></li>
            <li><a href="#contacto" class="navbar__link">Contacto</a></li>
        </ul>
        <a href="#reservar" class="navbar__reserve-btn">Reservar Ahora</a>
    </div>
</nav>