<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PROJUMI - Red de Emprendedores</title>
    <?php include 'views/componentes/head.php'; ?>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
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
        include "views/navbar.php";

?>
<br>
<br>
    <!-- Header con bienvenida -->
    <header class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                <h1 class="display-4 fw-bold">Bienvenido a nuestra Red de Emprendedores</h1>

                    <p class="lead">Donde podrás encontrar emprendedores únicos con productos únicos y apoyar el talento local.</p>
                    <a href="<?php echo APP_URL; ?>/productos/index" class="btn btn-primary btn-lg">Explorar productos</a>
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

  

    <!-- Filtros y productos -->
    <section class="products py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Nuestros Emprendimientos</h2>
            <div class="row" id="contenedor-emprendedores"></div>

            <!-- Paginación -->
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center" id="paginacion-emprendedores"></ul>
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
                    <form id="loginForm">
                
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
                    <div id="loginError" class="alert alert-danger mt-2 d-none" role="alert"></div>
                    <div class="text-center mt-3">
                        <a href="#" class="text-decoration-none">¿Olvidaste tu contraseña?</a>
                    </div>
                    <hr>
                    <div class="text-center">
                        <p>¿No tienes una cuenta? <a href="#" class="text-primary" data-bs-toggle="modal" data-bs-target="#registroClienteModal" data-bs-dismiss="modal">Regístrate</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Modal de Registro de Cliente -->
<div class="modal fade" id="registroClienteModal" tabindex="-1" aria-labelledby="registroClienteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable"> <!-- modal-lg para mayor ancho -->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="registroClienteModalLabel">Registro de Cliente</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body bg-light">

        <div class="container">
          <div class="register-container">
            <div class="form-header">
              <p class="text-muted">Complete todos los campos obligatorios</p>
            </div>

            <form id="formRegistrarUsuarioCliente" method="POST">
              <!-- Sección Información Personal -->
              <div class="form-section">
                <h5 class="section-title">Información Personal</h5>
                <div class="row g-3">
                  <div class="col-md-6">
                    <label for="cedula" class="form-label required-field">Cédula</label>
                    <input type="number" class="form-control" id="cedula" name="cedula" required>
                    <div class="invalid-feedback" id="error-cedula"></div>
                  </div>
                  <div class="col-md-6">
                    <label for="fecha_nacimiento" class="form-label required-field">Fecha de Nacimiento</label>
                    <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required>
                    <div class="invalid-feedback" id="error-fecha_nacimiento"></div>
                  </div>
                  <div class="col-md-6">
                    <label for="nombre" class="form-label required-field">Nombres</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                    <div class="invalid-feedback" id="error-nombre"></div>
                  </div>
                  <div class="col-md-6">
                    <label for="apellido" class="form-label required-field">Apellidos</label>
                    <input type="text" class="form-control" id="apellido" name="apellido" required>
                    <div class="invalid-feedback" id="error-apellido"></div>
                  </div>
                  <div class="col-md-6">
                    <label for="telefono" class="form-label required-field">Teléfono</label>
                    <input type="tel" class="form-control" id="telefono" name="telefono" required>
                    <div class="invalid-feedback" id="error-telefono"></div>
                  </div>
                  <div class="col-md-6">
                    <label for="edad" class="form-label">Edad</label>
                    <input type="number" class="form-control" id="edad" name="edad" readonly disabled>
                    <div class="invalid-feedback" id="error-edad"></div>
                  </div>
                </div>
              </div>

              <!-- Sección Contacto -->
              <div class="form-section mt-4">
                <h5 class="section-title">Información de Contacto</h5>
                <div class="row g-3">
                  <div class="col-md-6">
                    <label for="email" class="form-label required-field">Correo Electrónico</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                    <div class="invalid-feedback" id="error-email"></div>

                  </div>
                  <div class="col-md-6">
                    <label for="direccion" class="form-label required-field">Dirección</label>
                    <textarea class="form-control" id="direccion" name="direccion" rows="1" required></textarea>
                    <div class="invalid-feedback" id="error-direccion"></div>
                  </div>
                </div>
              </div>

              <!-- Sección Seguridad -->
              <div class="form-section mt-4">
                <h5 class="section-title">Seguridad</h5>
                <div class="row g-3">
                  <div class="col-md-6">
                    <label for="password" class="form-label required-field">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <div class="invalid-feedback" id="error-password"></div>                    
                  </div>
                  <div class="col-md-6">
                    <label for="confirm_pass" class="form-label required-field">Confirmar Contraseña</label>
                    <input type="password" class="form-control" id="confirm_pass" name="confirm_pass" required>
                    <div class="invalid-feedback" id="error-confirm_pass"></div>                                        
                  </div>
                </div>
              </div>

              <!-- Sección Términos -->
              <div class="form-section mt-4">
                <div class="form-check mb-3">
                  <input class="form-check-input" type="checkbox" id="acceptTerms" required>
                  <label class="form-check-label" for="acceptTerms">
                    Acepto los <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">términos y condiciones</a>
                  </label>
                </div>
              </div>

              <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg">
                  <i class="fas fa-user-plus me-2"></i>Registrarse
                </button>
              </div>
            </form>

            <div class="text-center mt-3">
              <p>¿Ya tienes una cuenta? <a href="#" class="text-primary" data-bs-toggle="modal" data-bs-target="#loginModal" data-bs-dismiss="modal">Inicia Sesión</a></p>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

    <!--<?php
    /*include "views/componentes/close.php";*/
    ?>-->

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
    <script>
        const APP_URL = "<?php echo APP_URL; ?>";
    </script>
    <!-- JavaScript personalizado -->
    <script src="<?php echo APP_URL; ?>/public/js/clienthome.js" type="module"></script>
    <script type="module" src="<?php echo APP_URL; ?>/public/js/login.js"></script>
    <script src="<?php echo APP_URL; ?>/public/js/registroUsuarioCliente.js" type="module"></script>
<!--    <script src="<?php echo url('URL') ?>public/js/validacionlogin.js"></script> -->
<script>
  
  </script>  
    
</body>
</html>