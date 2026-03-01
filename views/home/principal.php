<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PROJUMI - Red de Emprendedores</title>
    <?php include 'views/componentes/head.php'; ?>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Estilos personalizados -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/p.css">
    
</head>
<body>

<?php
// Asegúrate de iniciar la sesión también en la vista
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<?php
    if (isset($_SESSION['user'])) {
        include "views/navbar.php";
    }
?>
<br>
<br>
    <!-- Header con bienvenida -->
    <header class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                <h1 class="display-4 fw-bold">Bienvenido a nuestra Red de Emprendedores</h1>

                    <p class="lead">Donde podrás encontrar productos únicos y apoyar el talento local.</p>
                    <a href="#" class="btn btn-primary btn-lg">Explorar productos</a>
                </div>
                <div class="col-md-6">
                    <img src="<?php echo APP_URL; ?>/public/img/equipo.jpeg" alt="Emprendedores PROJUMI" class="img-fluid rounded">
                    
                </div>
            </div>
        </div>
    </header>

     <!-- Barra de búsqueda avanzada -->
     <section class="search-section py-4 bg-light">
        <div class="container">
            <div class="row g-3">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" placeholder="Buscar productos...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select">
                        <option value="">Todas las categorías</option>
                        <option value="reposteria">Repostería</option>
                        <option value="muñequeria">Muñequería</option>
                        <option value="bisuteria">Bisutería</option>
                        <option value="paleteria">Paletería</option>
                        <option value="arte">Arte móvil</option>
                        <option value="decoraciones">Decoraciones</option>
                        <option value="manualidades">Manualidades</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select">
                        <option value="">Precio</option>
                        <option value="0-20">$0 - $20</option>
                        <option value="20-50">$20 - $50</option>
                        <option value="50-100">$50 - $100</option>
                        <option value="100+">Más de $100</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100"><i class="fas fa-filter me-2"></i> Filtrar</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Carrusel de productos destacados -->
    <section class="featured-products py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Productos Destacados</h2>
            <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <div class="row">
                            <!-- Producto 1 -->
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <img src="img/product1.jpg" class="card-img-top" alt="Producto 1">
                                    <div class="card-body">
                                        <h5 class="card-title">Pastel de Chocolate</h5>
                                        <p class="card-text">Delicioso pastel de chocolate con relleno de crema.</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-warning">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star-half-alt"></i>
                                            </span>
                                            <span class="fw-bold">$25.00</span>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-transparent">
                                        <button class="btn btn-sm btn-primary w-100" data-bs-toggle="modal" data-bs-target="#productModal">Ver detalles</button>
                                    </div>
                                </div>
                            </div>
                            <!-- Producto 2 -->
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <img src="img/product2.jpg" class="card-img-top" alt="Producto 2">
                                    <div class="card-body">
                                        <h5 class="card-title">Muñeca Artesanal</h5>
                                        <p class="card-text">Hermosa muñeca hecha a mano con materiales naturales.</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-warning">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="far fa-star"></i>
                                            </span>
                                            <span class="fw-bold">$35.00</span>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-transparent">
                                        <button class="btn btn-sm btn-primary w-100" data-bs-toggle="modal" data-bs-target="#productModal">Ver detalles</button>
                                    </div>
                                </div>
                            </div>
                            <!-- Producto 3 -->
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <img src="img/product3.jpg" class="card-img-top" alt="Producto 3">
                                    <div class="card-body">
                                        <h5 class="card-title">Collar de Plata</h5>
                                        <p class="card-text">Elegante collar de plata con detalles artesanales.</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-warning">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                            </span>
                                            <span class="fw-bold">$45.00</span>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-transparent">
                                        <button class="btn btn-sm btn-primary w-100" data-bs-toggle="modal" data-bs-target="#productModal">Ver detalles</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="row">
                            <!-- Producto 4 -->
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <img src="img/product4.jpg" class="card-img-top" alt="Producto 4">
                                    <div class="card-body">
                                        <h5 class="card-title">Paletas Artesanales</h5>
                                        <p class="card-text">Paletas de sabores exóticos hechas con frutas naturales.</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-warning">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star-half-alt"></i>
                                                <i class="far fa-star"></i>
                                            </span>
                                            <span class="fw-bold">$12.00</span>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-transparent">
                                        <button class="btn btn-sm btn-primary w-100" data-bs-toggle="modal" data-bs-target="#productModal">Ver detalles</button>
                                    </div>
                                </div>
                            </div>
                            <!-- Producto 5 -->
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <img src="img/product5.jpg" class="card-img-top" alt="Producto 5">
                                    <div class="card-body">
                                        <h5 class="card-title">Pintura en Lienzo</h5>
                                        <p class="card-text">Obra de arte original pintada a mano por artistas locales.</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-warning">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="far fa-star"></i>
                                            </span>
                                            <span class="fw-bold">$120.00</span>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-transparent">
                                        <button class="btn btn-sm btn-primary w-100" data-bs-toggle="modal" data-bs-target="#productModal">Ver detalles</button>
                                    </div>
                                </div>
                            </div>
                            <!-- Producto 6 -->
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <img src="img/product6.jpg" class="card-img-top" alt="Producto 6">
                                    <div class="card-body">
                                        <h5 class="card-title">Centro de Mesa</h5>
                                        <p class="card-text">Decoración floral para eventos especiales, hecho a mano.</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-warning">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star-half-alt"></i>
                                            </span>
                                            <span class="fw-bold">$28.00</span>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-transparent">
                                        <button class="btn btn-sm btn-primary w-100" data-bs-toggle="modal" data-bs-target="#productModal">Ver detalles</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Siguiente</span>
                </button>
            </div>
        </div>
    </section>

    <!-- Categorías de productos -->
    <section class="categories py-5">
        <div class="container">
            <h2 class="text-center mb-5">Categorías de Emprendimientos</h2>
            <div class="row">
                <div class="col-md-4 col-lg-2 mb-4">
                    <div class="card category-card h-100">
                        <img src="<?php echo APP_URL; ?>/public/img/reposteria.jpg" class="card-img-top" alt="Repostería">
                        
                        <div class="card-body text-center">
                            <h5 class="card-title">Repostería</h5>
                            <a href="#" class="btn btn-sm btn-outline-primary">Ver productos</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-2 mb-4">
                    <div class="card category-card h-100">
                        <img src="<?php echo APP_URL; ?>/public/img/munequeria.jpg" class="card-img-top" alt="Muñequería">
                        
                        <div class="card-body text-center">
                            <h5 class="card-title">Muñequería</h5>
                            <a href="#" class="btn btn-sm btn-outline-primary">Ver productos</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-2 mb-4">
                    <div class="card category-card h-100">
                        <img src="<?php echo APP_URL; ?>/public/img/bisuteria.jpg" class="card-img-top" alt="Bisutería">
                        
                        <div class="card-body text-center">
                            <h5 class="card-title">Bisutería</h5>
                            <a href="#" class="btn btn-sm btn-outline-primary">Ver productos</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-2 mb-4">
                    <div class="card category-card h-100">
                        <img src="<?php echo APP_URL; ?>/public/img/paleteria.png" class="card-img-top" alt="Paletería">
                        
                        <div class="card-body text-center">
                            <h5 class="card-title">Paletería</h5>
                            <a href="#" class="btn btn-sm btn-outline-primary">Ver productos</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-2 mb-4">
                    <div class="card category-card h-100">
                        <img src="<?php echo APP_URL; ?>/public/img/artemovil.jpeg" class="card-img-top" alt="Arte Móvil">
                        
                        <div class="card-body text-center">
                            <h5 class="card-title">Arte Móvil</h5>
                            <a href="#" class="btn btn-sm btn-outline-primary">Ver productos</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-2 mb-4">
                    <div class="card category-card h-100">
                        <img src="<?php echo APP_URL; ?>/public/img/decoraciones.jpg" class="card-img-top" alt="Decoraciones">
                        
                        <div class="card-body text-center">
                            <h5 class="card-title">Decoraciones</h5>
                            <a href="#" class="btn btn-sm btn-outline-primary">Ver productos</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Filtros y productos -->
    <section class="products py-5 bg-light">
        <div class="container">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h2>Nuestros Productos</h2>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6">
                            <select class="form-select">
                                <option selected>Ordenar por</option>
                                <option>Precio: Menor a Mayor</option>
                                <option>Precio: Mayor a Menor</option>
                                <option>Mejor valorados</option>
                                <option>Más recientes</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <select class="form-select">
                                <option selected>Todas las categorías</option>
                                <option>Repostería</option>
                                <option>Muñequería</option>
                                <option>Bisutería</option>
                                <option>Paletería</option>
                                <option>Arte Móvil</option>
                                <option>Decoraciones</option>
                                <option>Manualidades</option>
                                <option>Cosméticos</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <!-- Producto 1 -->
                <div class="col-md-4 col-lg-3 mb-4">
                    <div class="card h-100">
                        <div class="badge bg-danger position-absolute" style="top: 10px; right: 10px">Oferta</div>
                        <img src="<?php echo APP_URL; ?>/public/img/jabones-naturales.jpg" class="card-img-top" alt="Producto 7">
                        
                        <div class="card-body">
                            <h5 class="card-title">Jabones Artesanales</h5>
                            <p class="card-text">Jabones naturales con aceites esenciales y aromas relajantes.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-warning">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                    <i class="far fa-star"></i>
                                </span>
                                <div>
                                    <span class="text-muted text-decoration-line-through me-2">$15.00</span>
                                    <span class="fw-bold text-danger">$12.00</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent">
                            <button class="btn btn-sm btn-primary w-100" data-bs-toggle="modal" data-bs-target="#productModal">Ver detalles</button>
                        </div>
                    </div>
                </div>
                <!-- Producto 2 -->
                <div class="col-md-4 col-lg-3 mb-4">
                    <div class="card h-100">
                        <img src="<?php echo APP_URL; ?>/public/img/velasaromaticas.png" class="card-img-top" alt="Producto 8">
                        
                        <div class="card-body">
                            <h5 class="card-title">Velas Aromáticas</h5>
                            <p class="card-text">Velas hechas a mano con cera de soja y aromas naturales.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-warning">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="far fa-star"></i>
                                </span>
                                <span class="fw-bold">$18.00</span>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent">
                            <button class="btn btn-sm btn-primary w-100" data-bs-toggle="modal" data-bs-target="#productModal">Ver detalles</button>
                        </div>
                    </div>
                </div>
                <!-- Producto 3 -->
                <div class="col-md-4 col-lg-3 mb-4">
                    <div class="card h-100">
                        <div class="badge bg-success position-absolute" style="top: 10px; right: 10px">Nuevo</div>
                        <img src="<?php echo APP_URL; ?>/public/img/bolsotejido.jpg" class="card-img-top" alt="Producto 9">
                        
                        <div class="card-body">
                            <h5 class="card-title">Bolso Tejido</h5>
                            <p class="card-text">Bolso tejido a mano con materiales naturales y resistentes.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-warning">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                </span>
                                <span class="fw-bold">$40.00</span>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent">
                            <button class="btn btn-sm btn-primary w-100" data-bs-toggle="modal" data-bs-target="#productModal">Ver detalles</button>
                        </div>
                    </div>
                </div>
                <!-- Producto 4 -->
                <div class="col-md-4 col-lg-3 mb-4">
                    <div class="card h-100">
                        <img src="<?php echo APP_URL; ?>/public/img/tazaspersonalizada.jpg" class="card-img-top" alt="Producto 10">
                        
                        <div class="card-body">
                            <h5 class="card-title">Taza Personalizada</h5>
                            <p class="card-text">Taza de cerámica con diseños personalizados y duraderos.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-warning">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="far fa-star"></i>
                                    <i class="far fa-star"></i>
                                </span>
                                <span class="fw-bold">$15.00</span>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent">
                            <button class="btn btn-sm btn-primary w-100" data-bs-toggle="modal" data-bs-target="#productModal">Ver detalles</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Paginación -->
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1">Anterior</a>
                    </li>
                    <li class="page-item"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">Siguiente</a>
                    </li>
                </ul>
            </nav>
        </div>
    </section>
<!-- FOOTER -->


<?php
include "views/footer.php";
?>
    
    <!-- Modal de Login -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel">Iniciar Sesión</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                <form action="<?php echo APP_URL; ?>/home/principal" method="post">
                
                        <div class="mb-3">
                            <label for="loginCedula" class="form-label">Cedula</label>
                            <input type="text" class="form-control" id="ced" name="cedula" required>
                           <span id="scedula" class="small-text"></span>
                        </div>
                        <div class="mb-3">
                            <label for="loginPassword" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="pass" name="password" required>
                            <span id="spass" class="small-text"></span>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="rememberMe">
                            <label class="form-check-label" for="rememberMe">Recordarme</label>
                        </div>
                        <input type="submit" class="btn btn-primary w-100" name="iniciar" value="Iniciar Sesión">
                    </form>
                    <div class="text-center mt-3">
                        <a href="#" class="text-decoration-none">¿Olvidaste tu contraseña?</a>
                    </div>
                    <hr>
                    <div class="text-center">
                        <p>¿No tienes una cuenta? <a href="#" class="text-primary" data-bs-toggle="modal" data-bs-target="#registerModal" data-bs-dismiss="modal">Regístrate</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Registro -->
    <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registerModalLabel">Registrarse</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                <form action="<?php echo APP_URL; ?>/auth/register" method="post">
                
                        <div class="row">
                        <div class="mb-3">
                            <label for="cedula" class="form-label">Cedula</label>
                            <input type="number" class="form-control" name="cedula" required>
                        </div>

                            <div class="col-md-6 mb-3">
                                <label for="firstName" class="form-label">Nombres</label>
                                <input type="text" class="form-control" name="nombre" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="lastname" class="form-label">Apellidos</label>
                                <input type="text" class="form-control" name="apellido" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="firstName" class="form-label">Nombres</label>
                                <input type="text" class="form-control" name="nombre" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="registerEmail" class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="registerPassword" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" name="pass" required>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="acceptTerms" required>
                            <label class="form-check-label" for="acceptTerms">Acepto los términos y condiciones</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Registrarse</button>
                    </form>
                    <hr>
                    <div class="text-center">
                        <p>¿Ya tienes una cuenta? <a href="#" class="text-primary" data-bs-toggle="modal" data-bs-target="#loginModal" data-bs-dismiss="modal">Inicia Sesión</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Logout -->
    <div class="modal fade" id="CerrarModal" tabindex="-1" aria-labelledby="CerrarModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="CerrarModalLabel">Cerrar Sesión</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                <form action="<?php echo APP_URL; ?>/home/logout" method="post">
               
                <h5 class="modal-title" id="CerrarModalLabel">Estas seguro que desea salir de la Sesión?</h5>
                    <br>
                    <br>

                    <input type="submit" class="btn btn-primary w-100" name="logout" value="Cerrar Sesion" >
                    <br>
                    <br>

                        <button type="button" class="btn btn-primary w-100" data-bs-dismiss="modal" aria-label="Close">Volver</button>
                    </form>
                    <hr>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal de Producto -->
    <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productModalLabel">Detalles del Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div id="productImages" class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-inner">
                                    <div class="carousel-item active">
                                        <img src="img/product-detail1.jpg" class="d-block w-100" alt="Producto">
                                    </div>
                                    <div class="carousel-item">
                                        <img src="img/product-detail2.jpg" class="d-block w-100" alt="Producto">
                                    </div>
                                    <div class="carousel-item">
                                        <img src="img/product-detail3.jpg" class="d-block w-100" alt="Producto">
                                    </div>
                                </div>
                                <button class="carousel-control-prev" type="button" data-bs-target="#productImages" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Anterior</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#productImages" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Siguiente</span>
                                </button>
                            </div>
                            <div class="row mt-2">
                                <div class="col-4">
                                    <img src="img/product-detail1.jpg" class="img-thumbnail" style="cursor: pointer" onclick="changeMainImage(this)">
                                </div>
                                <div class="col-4">
                                    <img src="img/product-detail2.jpg" class="img-thumbnail" style="cursor: pointer" onclick="changeMainImage(this)">
                                </div>
                                <div class="col-4">
                                    <img src="img/product-detail3.jpg" class="img-thumbnail" style="cursor: pointer" onclick="changeMainImage(this)">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h3>Pastel de Chocolate Premium</h3>
                            <div class="d-flex align-items-center mb-3">
                                <span class="text-warning me-2">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                </span>
                                <span class="text-muted">(24 reseñas)</span>
                            </div>
                            <h4 class="text-primary mb-3">$25.00</h4>
                            <p class="mb-4">Delicioso pastel de chocolate con tres capas de bizcocho esponjoso, relleno de crema de chocolate belga y cubierto con ganache de chocolate negro. Decorado con virutas de chocolate y frutos rojos.</p>
                            
                            <div class="mb-4">
                                <h5>Emprendedor</h5>
                                <div class="d-flex align-items-center">
                                    <img src="img/entrepreneur.jpg" class="rounded-circle me-3" width="50" height="50" alt="Emprendedor">
                                    <div>
                                        <h6 class="mb-0">Dulces Tentaciones</h6>
                                        <small class="text-muted">Repostería Artesanal</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Cantidad</label>
                                <div class="input-group" style="width: 150px;">
                                    <button class="btn btn-outline-secondary" type="button">-</button>
                                    <input type="text" class="form-control text-center" value="1">
                                    <button class="btn btn-outline-secondary" type="button">+</button>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex">
                                <button class="btn btn-primary me-md-2">
                                    <i class="fas fa-shopping-cart me-2"></i>Añadir al carrito
                                </button>
                                <button class="btn btn-outline-primary">
                                    <i class="far fa-heart me-2"></i>Guardar
                                </button>
                            </div>
                            
                            <hr class="my-4">
                            
                            <div>
                                <h5>Detalles del producto</h5>
                                <ul class="list-unstyled">
                                    <li><strong>Categoría:</strong> Repostería</li>
                                    <li><strong>Peso:</strong> 1.5 kg</li>
                                    <li><strong>Tamaño:</strong> 20 cm de diámetro</li>
                                    <li><strong>Ingredientes:</strong> Chocolate, harina, huevos, mantequilla, azúcar, crema</li>
                                    <li><strong>Alérgenos:</strong> Contiene gluten, huevo, lácteos</li>
                                    <li><strong>Tiempo de entrega:</strong> 2-3 días hábiles</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Reseñas</h4>
                            <div class="mb-4">
                                <div class="d-flex mb-2">
                                    <img src="img/user1.jpg" class="rounded-circle me-3" width="50" height="50" alt="Usuario">
                                    <div>
                                        <h6 class="mb-0">María González</h6>
                                        <div class="text-warning mb-1">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                        </div>
                                        <small class="text-muted">Hace 2 semanas</small>
                                    </div>
                                </div>
                                <p>El pastel estaba increíble, muy rico y bien presentado. Lo pedí para el cumpleaños de mi hija y a todos les encantó. Definitivamente lo volveré a pedir.</p>
                            </div>
                            
                            <div class="mb-4">
                                <div class="d-flex mb-2">
                                    <img src="img/user2.jpg" class="rounded-circle me-3" width="50" height="50" alt="Usuario">
                                    <div>
                                        <h6 class="mb-0">Carlos Martínez</h6>
                                        <div class="text-warning mb-1">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="far fa-star"></i>
                                        </div>
                                        <small class="text-muted">Hace 1 mes</small>
                                    </div>
                                </div>
                                <p>Muy buen sabor y textura, aunque me hubiera gustado que tuviera un poco más de relleno. La presentación era hermosa y llegó a tiempo.</p>
                            </div>
                            
                            <form>
                                <h5>Deja tu reseña</h5>
                                <div class="mb-3">
                                    <label class="form-label">Calificación</label>
                                    <div class="rating">
                                        <i class="far fa-star" data-rating="1"></i>
                                        <i class="far fa-star" data-rating="2"></i>
                                        <i class="far fa-star" data-rating="3"></i>
                                        <i class="far fa-star" data-rating="4"></i>
                                        <i class="far fa-star" data-rating="5"></i>
                                        <input type="hidden" name="rating" id="rating-value" value="0">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="reviewText" class="form-label">Comentario</label>
                                    <textarea class="form-control" id="reviewText" rows="3"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Enviar reseña</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- JavaScript personalizado -->
    <script src="<?php echo APP_URL; ?>/public/js/p.js" type="module"></script>
    
<!--    <script src="<?php echo url('URL') ?>public/js/validacionlogin.js" type="module"></script> -->  
    
</body>
</html>