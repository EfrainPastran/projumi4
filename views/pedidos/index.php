<?php if (!isset($_SESSION['user']['cedula'])): ?>
    <script>window.location.href = '<?= APP_URL ?>home/index';</script>
    <?php exit(); ?>
<?php endif; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PROJUMI - Mis Pedidos</title>
    <?php include 'views/componentes/head.php'; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/pedidos.css">
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
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-md-6">
                <h2 class="fw-bold"><?php echo $title; ?></h2>
                <p class="text-muted">Revisa el estado de tus pedidos realizados</p>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-end">
                    <div class="input-group" style="width: 300px;">
                        <input type="text" id="searchInput" class="form-control" placeholder="Buscar pedidos...">
                        <button class="btn btn-primary" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <!-- Filtro por fecha -->
                            <div class="col-md-4">
                                <label for="dateFilter" class="form-label">Fecha</label>
                                <select id="dateFilter" class="form-select">
                                    <option value="all">Todas las fechas</option>
                                    <option value="today">Hoy</option>
                                    <option value="week">Esta semana</option>
                                    <option value="month">Este mes</option>
                                </select>
                            </div>
                            <!-- Filtro por estado -->
                            <div class="col-md-4">
                                <label for="statusFilter" class="form-label">Estado</label>
                            <select id="statusFilter" class="form-select">
                                <option value="all">Todos</option>
                                <option value="en proceso">En proceso</option>
                                <option value="completado">Completado</option>
                                <option value="rechazado">Rechazado</option>
                            </select>
                            </div>
                            <!-- Rango de fecha personalizado -->
                            <div class="col-md-4 d-none" id="customDateRange">
                                <label for="startDate" class="form-label">Desde</label>
                                <input type="date" id="startDate" class="form-control">
                                <label for="endDate" class="form-label mt-2">Hasta</label>
                                <input type="date" id="endDate" class="form-control">
                            </div>

                            <!-- Botón aplicar filtros -->
                            <div class="col-md-3 d-flex align-items-end">
                                <button class="btn btn-primary w-100" id="applyFilters">
                                    <i class="fas fa-filter me-2"></i>Aplicar filtros
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de pedidos -->
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead id="pedidosHead">
                                <!-- Las columnas se llenan dinámicamente con JS -->
                                </thead>
                                <tbody id="pedidosBody">
                                <!-- Aquí se insertan los datos dinámicamente -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <nav aria-label="Page navigation" class="mt-4">
                            <nav aria-label="Page navigation" class="mt-4">
                            <ul class="pagination justify-content-center" id="pedidosPagination">
                                <!-- Paginación dinámica -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Modal de Detalles de Pedido -->
<div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="orderModalLabel">Detalles del Pedido</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div id="pedidoInfo"></div>
        <hr>
        <h6>Productos del pedido</h6>
        <div class="table-responsive">
          <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th>Producto</th>
                <th>Categoría</th>
                <th>Cantidad</th>
                <th>Precio unitario</th>
                <th>Total</th>
              </tr>
            </thead>
            <tbody id="detalleProductosBody"></tbody>
            <tfoot>
              <tr>
                <td colspan="4" class="text-end fw-bold">Total a pagar:</td>
                <td id="totalPedido" class="fw-bold text-success"></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?php echo APP_URL; ?>/public/js/pedidos.js" type="module"></script>
</body>
</html>