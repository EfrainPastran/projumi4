<?php if (!isset($_SESSION['user']['cedula'])): ?>
    <script>window.location.href = '<?= APP_URL ?>home/index';</script>
    <?php exit(); ?>
<?php endif; ?>

<?php
// Asegúrate de iniciar la sesión también en la vista
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PROJUMI - Mis Productos</title>
    <?php include 'views/componentes/head.php'; ?>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/productos.css">

</head>


<body>
<?php if (!empty($_SESSION['flash_success'])): ?>
    <script>
        Swal.fire({
            title: "Éxito",
            text: "<?php echo $_SESSION['flash_success']; ?>",
            icon: "success",
            confirmButtonText: "Aceptar"
        });
    </script>
    <?php unset($_SESSION['flash_success']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['flash_error'])): ?>
    <script>
        Swal.fire({
            title: "Error",
            text: "<?php echo $_SESSION['flash_error']; ?>",
            icon: "error",
            confirmButtonText: "Aceptar"
        });
    </script>
    <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>



    <?php include "views/navbar.php";?>
    <br>
    <br>
    <br>
    <div class="entrepreneur-container" style="margin: 3%;">
        <!-- Header -->
        <div class="entrepreneur-header">
            <h1><i class="fas fa-store"></i> Mis Productos</h1>
            <p class="subtitle">Administra los productos de tu emprendimiento</p>
        </div>

        <!-- Controles -->
        <div class="entrepreneur-controls">
            <div class="row g-3">
                <div class="col-md-5">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" placeholder="Buscar mis productos...">
                    </div>
                </div>

                <div class="col-md-4">
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
                <div class="col-md-3">
                    <?php if (isset($permisos['registrar'])): ?>
                        <button class="btn btn-add-product" id="addProductBtn" data-bs-toggle="modal" data-bs-target="#productModal">
                            <i class="fas fa-plus-circle"></i> Nuevo Producto
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <br>
        <br>
        <!-- Productos -->
        <div class="products-section">
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

    <!-- Modal Agregar/Editar Producto -->
    <div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Agregar Nuevo Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="registrarProducto" enctype="multipart/form-data">
                        <input type="hidden" id="" name="id_usuario" value="<?php echo htmlspecialchars($_SESSION['user']['id_usuario'] ?? ''); ?>">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="productName" class="form-label">Nombre del Producto*</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                                    <span id="nombreError" class="text-danger"></span>
                                </div>
                                <div class="mb-3">
                                    <label for="productPrice" class="form-label">Precio ($)*</label>
                                    <input type="text"class="form-control" name="precio" id="precio" required >
                                    <span id="precioError" class="text-danger"></span>
                                </div>
                                <div class="mb-3">
                                    <label for="productCategory" class="form-label">Categoría*</label>
                                    <select class="form-select" id="productCategory" name="id_categoria" required>
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
                                <!-- Checkbox para activar producto por porción -->
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="productoPorPorcion" name="es_porcion">
                                    <label class="form-check-label" for="productoPorPorcion">Producto por porción</label>
                                   
                                </div>

                                <!-- Contenedor oculto inicialmente con campos adicionales -->
                                <div id="porcionesFields" class="mb-3" style="display: none;">
                                    <div class="mb-3">
                                        <label for="cantidadPorciones" class="form-label">Cantidad de Porciones*</label>
                                        <input type="number" min="1" class="form-control" id="cantidadPorciones" name="cantidad_porciones" min="1">
                                        <span id="cantidadPorcionesError" class="text-danger"></span>
                                    </div>
                                    <div class="mb-3">
                                        <label for="precioPorPorcion" class="form-label">Precio por Porción ($)*</label>
                                        <input type="number" min="1" class="form-control" id="precioPorPorcion" name="precio_porcion" >
                                        <span id="precioPorPorcionError" class="text-danger"></span>
                                    </div>
                                    <div class="mb-3">
                                        <label for="imagenesPorcion" class="form-label">Imágenes del Producto por Porción</label>
                                        <input type="file" class="form-control" id="imagenesPorcion" name="imagenes_porcion[]" accept="image/jpeg, image/png, image/gif" multiple>
                                        <div id="previewPorcionImages" class="d-flex flex-wrap gap-2"></div>
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="productStock" class="form-label">Stock disponible*</label>
                                    <input type="number" min="1" class="form-control" id="stock" name="stock" required>
                                    <span id="stockError" class="text-danger"></span>
                                </div>

                                <div class="mb-3">
                                    <label for="productDescription" class="form-label">Descripción*</label>
                                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
                                    <span id="descripcionError" class="text-danger"></span>
                                </div>
                                <div class="mb-3">
                                    <label for="productStatus" class="form-label">Estado*</label>
                                    <select class="form-select" id="productStatus" name="status" required>
                                        <option value="1">Activo</option>
                                        <option value="0">Inactivo</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="productImage" class="form-label">Imágenes del Producto</label>
                                    <input type="file" class="form-control" id="productImage" name="imagenes[]" accept="image/jpeg, image/png, image/gif" multiple>
                                    <div id="previewImages" class="d-flex flex-wrap gap-2"></div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-success" id="saveProductBtn"><i class="fas fa-save me-2"></i>Guardar Producto</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="productModificarModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleEdit">Agregar Nuevo Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editarProducto" enctype="multipart/form-data">
                        <input type="hidden" id="id_usuario_update" name="id_usuario_update" value="<?php echo htmlspecialchars( $_SESSION['user_id'] ?? ''); ?>">
                        <input type="hidden" id="productId" name="id_producto" value="">
                        <input type="hidden" id="accion" name="accion" value="actualizar">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="productName" class="form-label">Nombre del Producto*</label>
                                    <input type="text" class="form-control" id="productName" name="nombre" required>
                                     <span id="productNameError" class="text-danger"></span>
                                </div>
                                <div class="mb-3">
                                    <label for="productPrice" class="form-label">Precio ($)*</label>
                                    <input type="text" class="form-control" name="precio" id="productPrice" required>
                                    <span id="productPriceError" class="text-danger"></span>
                                     
                                </div>
                                <div class="mb-3">
                                    <label for="productCategoryUpdate" class="form-label">Categoría*</label>
                                    <select class="form-select" id="productCategoryUpdate" name="id_categoria" required>
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
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="productStock" class="form-label">Stock disponible*</label>
                                    <input type="number" min="1" class="form-control" id="productStock" name="stock" required>
                                    <span id="productStockError" class="text-danger"></span>
                                </div>

                                <div class="mb-3">
                                    <label for="productDescription" class="form-label">Descripción*</label>
                                    <textarea class="form-control" id="productDescription" name="descripcion" rows="3" required></textarea>
                                    <span id="productDescriptionError" class="text-danger"></span>
                                </div>
                                <div class="mb-3">
                                    <label for="productStatusUpdate" class="form-label">Estado*</label>
                                    <select class="form-select" id="productStatusUpdate" name="status" required>
                                        <option value="1">Activo</option>
                                        <option value="0">Inactivo</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="productImage" class="form-label">Imagen del Producto</label>
                                    <input type="file" class="form-control" id="productEditImages" name="imagen_edit[]" accept="image/jpeg, image/png, image/gif" multiple>
                                    <div id="previewEditImages" class="d-flex flex-wrap gap-2"></div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-success" name="actualizar" id="updateProductBtn"><i class="fas fa-save me-2"></i>Actualizar Producto</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Confirmar Eliminación -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro que deseas eliminar el producto <strong id="productToDeleteName"></strong>?</p>
                    <p class="text-danger">Esta acción no se puede deshacer.</p>
                    <input type="hidden" id="productToDeleteId">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Eliminar Producto</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Convertimos el array de PHP a un objeto global accesible por JS
        const PERMISOS_USUARIO = <?php echo json_encode($permisos); ?>;
    </script>
    <!-- Custom JS -->
    <script src="<?php echo APP_URL; ?>/public/js/alertas.js" type="module"></script>                                        
    <script src="<?php echo APP_URL; ?>/public/js/producto.js" type="module"></script>
</body>

</html>