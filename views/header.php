
      <!-- Barra de navegación -->
      <nav class="navbar navbar-expand-lg navbar-dark bg-dark-green fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="<?php echo APP_URL; ?>/public/img/unnamed.jpg" alt="PROJUMI" width="40" height="40" class="rounded-circle me-2">
                
                <span class="fw-bold">PROJUMI</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo APP_URL; ?>/home/principal"><i class="fas fa-home me-1"></i> Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#loginModal"><i class="fas fa-sign-in-alt"></i> Inicia sesion</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#registroClienteModal"><i class="fas fa-user-plus"></i> Registrarse</a>
                    </li>
                   
                </ul>
                
                <ul class="navbar-nav ms-auto">
                    
                </ul>
            </div>
        </div>
    </nav>
