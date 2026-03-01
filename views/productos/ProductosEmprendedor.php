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
//echo "<pre>";
//print_r($tipoUsuario);
//echo "</pre>";
//die;
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

        <?php if($tipoUsuario[0] != 'cliente' && $tipoUsuario[0] != 'administrador' && $tipoUsuario[0] != 'visitante'):?>
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
            <h2><i class="fas fa-box-open"></i> Productos de <?= $nombre_completo ?></h2>   
            <div class="row" id="productsGrid">
                <input type="hidden" id="nombre_completo" value="<?= $nombre_completo ?>">
                <?php foreach ($productosEmprendedor as $producto): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm h-100">
                            <img src="<?= APP_URL ?>/<?= $producto['imagenes'][0] ?>" class="card-img-top" alt="<?= htmlspecialchars($producto['nombre']) ?>" style="object-fit: cover; height: 200px;">
                            <div class="card-body d-flex flex-column justify-content-between">
                                <div>
                                    <p class="text-muted mb-2">Emprendimiento: <strong><?= $producto['emprendedor'] ?></strong></p>
                                    <h5 class="card-title mb-1"><?= htmlspecialchars($producto['nombre']) ?></h5>
                                    <p class="card-text small text-truncate"><?= htmlspecialchars($producto['descripcion']) ?></p>
                                </div>
                                <div>
                                    <p class="fw-bold text-success mb-2">$<?= number_format($producto['precio'], 2) ?></p>
                                    <button
                                        class="btn btn-view-details w-100"
                                        data-bs-toggle="modal"
                                        data-bs-target="#productModal"
                                        data-id="<?= $producto['id_producto'] ?>"
                                    >
                                        <i class="fas fa-eye"></i> Ver detalles
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <!-- Paginación -->
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center" id="pagination">
                                
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
                                <img src="" alt="Producto" id="modalProductImage">
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
                    <?php if ($menu != 'header2' && $menu != 'header' && $menu != 'headerCliente'): ?>
                        <button type="button" class="btn btn-add-to-cart" id="addToCartBtn">
                            <i class="fas fa-cart-plus"></i> Añadir al carrito
                        </button>
                    <?php else: ?>
                    <!-- Contenido alternativo si $menu es 'header' o 'headerCliente' -->
                        <button type="button" class="btn btn-add-to-cart" id="loginmodal" data-bs-toggle="modal" data-bs-target="#loginModal">
                            <i class="fas fa-cart-plus"></i> Iniciar Seccion
                        </button>
                    <?php endif; ?>
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
    <script>
        const productos = <?php echo json_encode($productosEmprendedor); ?>;
        const BASE_URL = "<?php echo APP_URL; ?>";
    </script>
    <script src="<?php echo APP_URL; ?>/public/js/productosEmprendedor.js" type="module"></script>
</body>
</html>