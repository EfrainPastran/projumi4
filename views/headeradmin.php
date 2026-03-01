    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark-green fixed-top">
        <div class="container" style="padding-left: 0px !important; padding-right: 0px !important;">
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
                        <a class="nav-link" href="<?php echo APP_URL; ?>/productos/index"><i class="fas fa-store me-1"></i> Productos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo APP_URL; ?>/envios/index"><i class="fas fa-truck me-1"></i> Envios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo APP_URL; ?>/pedidos/index"><i class="fas fa-clipboard-list me-1"></i> Pedidos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo APP_URL; ?>/pagos/index"><i class="fas fa-dollar-sign me-1"></i> Pagos</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link" href="<?php echo APP_URL; ?>/reportes/index"><i class="fa-solid fa-file-pdf"></i> Reportes</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="categoriasDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-cog"></i> Configuración
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" id="">
                        <li><a class="dropdown-item" href="<?php echo APP_URL;?>/eventos/index"><i class="fa-solid fa-calendar-days"></i> Eventos</a></li>
                            <li><a class="dropdown-item" href="<?php echo APP_URL;?>/empresa/index"><i class="fa-solid fa-industry"></i> Empresa de envio</a></li>
                            <li><a class="dropdown-item" href="<?php echo APP_URL;?>/categorias/index"><i class="fas fa-tags me-1"></i> Categoría</a></li>
                            <li><a class="dropdown-item" href="<?php echo APP_URL;?>/emprendedor/index"><i class="fas fa-users me-1"></i> Emprendedores</a></li>
                            <li><a class="dropdown-item" href="<?php echo APP_URL;?>/clientes/index"><i class="fas fa-users me-1"></i> Clientes</a></li>
                        </ul>
                    </li>
                </ul>
                
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item me-2">
                        <a class="nav-link position-relative" href="<?php echo APP_URL; ?>/carrito/index">
                            <i class="fas fa-shopping-cart"></i>
                            <span id="carrito-count" class="position-absolute left-0 top-0 translate-middle badge rounded-pill bg-danger">0</span>

                        </a>
                    </li>
                    <li class="nav-item dropdown me-2">
                        <a class="nav-link dropdown-toggle position-relative" href="#" id="notificacionesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-bell"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="contadorNotificaciones">
                                0
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificacionesDropdown"
                            style="width: 300px; max-height: 300px; overflow-y: auto;" id="listaNotificaciones">
                            <!-- Las notificaciones se cargarán aquí -->
                        </ul>

                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i> <?php echo htmlspecialchars($_SESSION['user']['nombre'] ?? ''); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?php echo APP_URL; ?>/home/perfil"><i class="fas fa-user me-2"></i> Perfil</a></li>
                            <li class="dropdown-divider"></li>                                                        
                            <!--<li class="dropdown-header">Administración</li>-->   
                            <li><a class="dropdown-item" href="<?php echo APP_URL; ?>/auth/ayuda"><i class="fa-solid fa-handshake-angle"></i> Ayuda</a></li>
                            <li class="dropdown-divider"></li>                          
                            <li><a class="dropdown-item" id="cerrarSesion" href="<?php echo APP_URL; ?>/auth/logout"><i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión</a></li>                       
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

<script>
window.addEventListener("storage", function (e) {
  if (e.key === "carritoPorEmprendedor" || e.key === "id_emprendedor") {
    actualizarContadorCarrito();
  }
});

actualizarContadorCarrito();
function actualizarContadorCarrito() {
  const carritoLocal = JSON.parse(localStorage.getItem('carritoPorEmprendedor')) || {};
  const id_emprendedor = localStorage.getItem('id_emprendedor');
  let totalCantidad = 0;

  if (carritoLocal[id_emprendedor]) {
    carritoLocal[id_emprendedor].forEach(producto => {
      totalCantidad += producto.quantity || 0;
    });
  }

  const contador = document.getElementById('carrito-count');
  if (contador) {
    contador.textContent = totalCantidad;
  }
}

const cerrarSesionBtn = document.getElementById('cerrarSesion');
if (cerrarSesionBtn) {
  cerrarSesionBtn.addEventListener('click', function () {
    localStorage.removeItem('carritoPorEmprendedor');
    localStorage.removeItem('id_emprendedor');
    localStorage.removeItem('carritoExpiracion');
    actualizarContadorCarrito();
  });
}
</script>
<script src="<?php echo APP_URL; ?>/public/js/cargarNotificaciones.js" type="module"></script>
