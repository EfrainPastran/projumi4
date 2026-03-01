<?php if (!isset($_SESSION['user']['tipo'])): ?>
    <script>window.location.href = '<?= APP_URL ?>home/index';</script>
    <?php exit(); ?>
<?php endif; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PROJUMI - Nuestros Productos</title>
    <?php include 'views/componentes/head.php'; ?>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/productos.css">

    
</head>
<body>

<?php
// Asegúrate de iniciar la sesión también en la vista
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$tipo = $_SESSION['user']['tipo'][0];
?>

<?php
        include "views/navbar.php";
?>
<br>
<br>
    <div class="products-container">
        <!-- Hero Section -->
        <div class="products-hero">
            <h1><i class="fas fa-gift"></i> Nuestros Productos</h1>
            <p class="subtitle">Descubre lo mejor de la fundación PROJUMI</p>
        </div>
        <?php if($tipo == 'emprendedor'):?>
        <div class="col-md-3 d-flex align-items-end">
                                <a class="btn btn-primary w-100" id="applyFilters" href="<?php echo APP_URL; ?>/productos/producto">
                                    Agregar Productos
                                </a>
                            </div>
        <?php endif;?>
        <!-- Filtros y búsqueda -->
        <div class="products-filters">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" placeholder="Buscar productos...">
                    </div>
                </div>
                <div class="col-md-6">
                    <select class="form-select" id="categoryFilter" name="id_categoria" required>
                        <option value="">Seleccionar categoría...</option>
                        <?php if (isset($categorias)) : ?>
                            <?php foreach ($categorias as $c) : ?>
                                <option value="<?php echo $c['id_categoria']; ?>"><?php echo $c['nombre']; ?></option>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <p>No se encontraron Categorias registradas.</p>
                        <?php endif; ?>

                    </select>
                </div>
            </div>
        </div>

        <!-- Todos los productos -->
        <div class="all-products-section">
            <h2><i class="fas fa-box-open"></i> Todos los Productos</h2>
            <div class="row" id="productsGrid">
                <!-- Productos se cargarán aquí -->
            </div>

            <!-- Paginación -->
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center" id="pagination">
                    <!-- Paginación se generará dinámicamente -->
                </ul>
            </nav>
        </div>
    </div>

    <!-- Modal Detalle Producto -->
    <div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <input type="hidden" id="id_producto" name="id_producto">
                    <input type="hidden" id="id_emprendedor" name="id_emprendedor">
                    <h5 class="modal-title" id="productModalTitle">Detalle del Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="product-modal-image">
                                <div id="modalProductCarousel"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-2">Emprendimiento: <strong id="modalProductEmprendedor"></strong></p>
                            <h3 id="modalProductName"></h3>
                            <div class="product-price" id="modalProductPrice"></div>
                            <p class="product-description" id="modalProductDescription"></p>
                            <div class="product-details">
                                <div><strong>Categoría:</strong> <span id="modalProductCategory"></span></div>
                                <div><strong>Disponibilidad:</strong> <span id="modalProductStock"></span></div>
                            </div>
                            <div class="quantity-selector mt-4">
                                <button class="btn btn-quantity" id="decreaseQuantity">-</button>
                                <input type="number" value="1" min="1" id="productQuantity">
                                <button class="btn btn-quantity" id="increaseQuantity">+</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-add-to-cart" id="addToCartBtn">
                        <i class="fas fa-cart-plus"></i> Añadir al carrito
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php 
        include 'views/componentes/login.php';
    ?>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Custom JS -->
    <script type="module" src="<?php echo APP_URL; ?>/public/js/login.js"></script>
    <script src="<?php echo APP_URL; ?>/public/js/registroUsuarioCliente.js" type="module"></script>
    <script src="<?php echo APP_URL; ?>/public/js/productos.js" type="module"></script>
    
</body>
</html>