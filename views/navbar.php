<?php
if (!isset($_SESSION)) session_start();

//echo "<pre>";
//print_r($_SESSION['user']);
//echo "</pre>";
//die;

        //<?php if (
        //  $_SESSION['user']['tipo'][0] != 'cliente' && 
        //  $_SESSION['user']['tipo'][0] != 'administrador' && 
        //  $_SESSION['user']['tipo'][0] != 'visitante'): ?mayorcierre
        //  <button class="btn btn-primary" id="btnSiguientePaso" type="button"></button> 
        //<?php else: ?mayorcierre
        //  <!-- Contenido alternativo si $menu es 'header' o 'headerCliente' -->
        //  <button type="button" class="btn btn-add-to-cart" id="loginmodal" data-bs-toggle="modal" data-bs-target="#loginModal">
        //    <i class="fas fa-cart-plus"></i> Iniciar Seccion
        //  </button>
        //<?php endif; ?mayorcierre

function generarMenuDesdeSesion() {
    if (!isset($_SESSION['user']['rol'])) {
        return [];
    }

    // Acceder dentro de 'rol'
    $modulos = $_SESSION['user']['rol']['modulos'] ?? [];
    $permisos = $_SESSION['user']['rol']['permisos'] ?? [];

    $menu = [];

    foreach ($modulos as $id => $info) {
        $menu[$id] = [
            'id_modulo' => $id,
            'nombre'    => $info['nombre'],
            'ruta'      => $info['ruta'],
            'icono'     => $info['icono'],
            'permisos'  => $permisos[$id] ?? []
        ];
    }

    return $menu;
}

$menu = generarMenuDesdeSesion();
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark-green fixed-top">
    <div class="container" style="padding-left: 0px !important; padding-right: 0px !important;">
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="<?php echo APP_URL; ?>/public/img/unnamed.jpg" width="40" height="40" class="rounded-circle me-2">
            <span class="fw-bold">PROJUMI</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMain">
            
            <ul class="navbar-nav me-auto">

<?php
// Primero separamos módulos padres e hijos
$modulosPadre = [];
$modulosHijos = [];

foreach ($menu as $id => $item) {
    if (!empty($_SESSION['user']['rol']['modulos'][$id]['menu_padre'])) {
        $modulosHijos[$item['id_modulo']] = $item;
        $padreId = $_SESSION['user']['rol']['modulos'][$id]['menu_padre'];
        $modulosPadre[$padreId]['hijos'][$id] = $item;
    } else {
        $modulosPadre[$id] = $item;
    }
}

// Ordenamos por 'orden'
uasort($modulosPadre, function($a, $b) {
    return ($a['orden'] ?? 0) <=> ($b['orden'] ?? 0);
});

// Recorremos los módulos padre
foreach ($modulosPadre as $id => $item):

    // Si tiene hijos, hacemos dropdown
    if (!empty($item['hijos']) && $item['hijos'] != 0): ?>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                <i class="<?php echo $item['icono']; ?>"></i>
                <?php echo $item['nombre']; ?>
            </a>
            <ul class="dropdown-menu">
                <?php foreach ($item['hijos'] as $hijo): ?>
                    <li>
                        <a class="dropdown-item" href="<?php echo APP_URL . '/' . $hijo['ruta']; ?>">
                            <i class="<?php echo $hijo['icono']; ?>"></i>
                            <?php echo $hijo['nombre']; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </li>
    <?php
    // Si no tiene hijos, módulo normal
    elseif ($item['ruta'] !== "#"): ?>
        <li class="nav-item">
            <a class="nav-link" href="<?php echo APP_URL . '/' . $item['ruta']; ?>">
                <i class="<?php echo $item['icono']; ?>"></i>
                <?php echo $item['nombre']; ?>
            </a>
        </li>
    <?php
    // Si no tiene hijos, pero es nulo modal
    elseif ($item['hijos'] = 0): ?>
        <li class="nav-item">
            <a class="nav-link" href="<?php echo $item['ruta']; ?>">
                <i class="<?php echo $item['icono']; ?>"></i>
                <?php echo $item['nombre']; ?>
            </a>
        </li>
    <?php
    // Si no tiene ruta, pero no tiene hijos, mostramos permisos
    else: ?>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                <i class="<?php echo $item['icono']; ?>"></i>
                <?php echo $item['nombre']; ?>
            </a>
            <?php if (!empty($item['permisos'])): ?>
                <ul class="dropdown-menu">
                    <?php foreach ($item['permisos'] as $permiso): ?>
                        <li><span class="dropdown-item disabled"><?php echo ucfirst($permiso); ?></span></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </li>
    <?php endif; ?>

<?php endforeach; ?>

</ul>

            <!-- Carrito y notificaciones -->
            <ul class="navbar-nav ms-auto">
                <li class="nav-item me-2">
                    <a class="nav-link position-relative" href="<?php echo APP_URL; ?>/carrito/index">
                        <i class="fas fa-shopping-cart"></i>
                        <span id="carrito-count" class="position-absolute left-0 top-0 translate-middle badge rounded-pill bg-danger">0</span>
                    </a>
                </li>
    <?php
        if ($_SESSION['user']['tipo'][0] != 'visitante'){
            echo('
                <li class="nav-item dropdown me-2">
                    <a class="nav-link dropdown-toggle position-relative" href="#" id="notificacionesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bell"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="contadorNotificaciones">0</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" style="width: 300px; max-height: 300px; overflow-y: auto;" id="listaNotificaciones">
                        <!-- Notificaciones se cargarán aquí -->
                    </ul>
                </li>

                <!-- Perfil -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-1"></i> '.htmlspecialchars($_SESSION['user']['nombre'] ?? '').'
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="'.APP_URL.'/home/perfil"><i class="fas fa-user me-2"></i> Perfil</a></li>
                        <li class="dropdown-divider"></li>
            ');
            if ('emprendedor' == $_SESSION['user']['tipo'][0]||'super_usuario' == $_SESSION['user']['tipo'][0]) {
                echo('
                            <li><a class="dropdown-item" id="datospago" href="'.APP_URL.'/datos/index"><i class="fa-solid fa-money-bill"></i> Mis métodos de pago</a></li>                            
                            <li class="dropdown-divider"></li>                                                        
                            <li><a class="dropdown-item" href="'.APP_URL.'/clientes/index"><i class="fa-solid fa-users"></i> Mis clientes</a></li>                            
                            <li class="dropdown-divider"></li>'
                    );
            } 
            echo('
                            <li><a class="dropdown-item" href="'.APP_URL.'/auth/ayuda"><i class="fa-solid fa-handshake-angle me-2"></i> Ayuda</a></li>
                            <li class="dropdown-divider"></li>
                            <li><a class="dropdown-item" id="cerrarSesion" href="'.APP_URL.'/auth/logout"><i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión</a></li>
                        </ul>
                    </li>
            ');
        }?> 
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
