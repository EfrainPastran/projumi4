<?php if (!isset($_SESSION['user']['cedula'])): ?>
    <script>window.location.href = '<?= APP_URL ?>home/index';</script>
    <?php exit(); ?>
<?php endif; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PROJUMI - Mis Pagos</title>
    <?php include 'views/componentes/head.php'; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/pagos.css">
</head>
<body>

<?php
// Iniciar la sesión también en la vista
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
                <p class="text-muted">Revisa el estado de tus pagos realizados</p>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-end">
                    <div class="input-group" style="width: 300px;">
                        <input type="text" class="form-control" id="busquedaTexto" placeholder="Buscar..." oninput="aplicarFiltros()">
                        <button class="btn btn-primary" id="buscarPagos" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- <div class="col-md-6 d-flex align-items-center mb-4">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#registrarPagoModal">
                <i class="fas fa-plus me-1"></i> Registrar Pago
            </button>
        </div> -->
        <!-- Filtros -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="statusFilter" class="form-label">Estado</label>
                                <select id="statusFilter" class="form-select">
                                    <option value="all">Todos</option>
                                    <option value="procesando">Procesando</option>
                                    <option value="aprobado">Aprobado</option>
                                    <option value="rechazado">Rechazado</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="dateFilter" class="form-label">Fecha</label>
                                <select id="dateFilter" class="form-select">
                                    <option value="all">Todas las fechas</option>
                                    <option value="today">Hoy</option>
                                    <option value="week">Esta semana</option>
                                    <option value="month">Este mes</option>
                                    <option value="custom">Personalizado</option>
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
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
                       <div id="tablaPagosContainer"></div>

                        <!-- Paginación -->
                        <nav aria-label="Page navigation" class="mt-4">
                        <ul class="pagination justify-content-center" id="pagosPagination">
                            <!-- Paginación dinámica -->
                        </ul>
                    </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!--     <div class="modal fade" id="registrarPagoModal" tabindex="-1" aria-labelledby="registrarPagoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="formRegistrarPago" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="registrarPagoLabel"><i class="fas fa-credit-card"></i> Registrar Pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                <div class="mb-3">
                    <label for="selectPedido" class="form-label">Selecciona un pedido</label>
                    <select class="form-select" id="selectPedido" name="pedido_id" required>
                    <option value="">-- Selecciona un pedido --</option>
                    <?php foreach ($pedidos as $p) : ?>
                        <option value="<?php echo $p['id_pedidos']; ?>"><?php echo "#PED-" . $p['id_pedidos'] ." fecha:" . date('d/m/Y', strtotime($p['fecha_pedido'])); ?></option>
                    <?php endforeach; ?>
                    </select>
                </div>
                <div id="productosPedidoContainer" class="mb-3" style="display:none;">
                    <label class="form-label">Productos del pedido</label>
                    <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle">
                        <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio unitario</th>
                            <th>Subtotal</th>
                        </tr>
                        </thead>
                        <tbody id="tablaProductosPedido">
                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">Total a pagar:</th>
                            <th id="totalPagarPedido">$0.00</th>
                        </tr>
                        </tfoot>
                    </table>
                    </div>
                </div>
                <div class="mb-3" style="display:none;" id="desglosePagoContainer">
                    <label class="form-label">Desglose de pago</label>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle" id="tablaDesglosePago">
                            <thead>
                                <tr>
                                    <th>Método</th>
                                    <th>Moneda</th>
                                    <th>Monto</th>
                                    <th class="col-referencia">Referencia</th>
                                    <th>accion</th>
                                </tr>
                            </thead>
                            <tbody id="desglosePagoBody">
                                <tr>
                                    <td>
                                        <select class="form-select metodo-desglose" name="metodo_pago" required>
                                        <option value="">-- Selecciona método de pago --</option>
                                        <?php foreach ($metodos as $m) : ?>
                                            <option value="<?php echo $m['id_metodo_pago']; ?>"><?php echo $m['nombre']; ?></option>
                                        <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-select moneda-desglose" name="moneda" required>
                                        <option value="">-- Selecciona moneda --</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control monto-desglose" min="0" step="0.01" required>
                                    </td>
                                    <td class="col-referencia">
                                        <input type="text" class="form-control referencia-desglose" disabled>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm btn-remove-desglose" disabled>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-outline-primary btn-sm" id="btnAgregarDesglose">
                            <i class="fas fa-plus"></i> Agregar moneda
                        </button>
                    </div>
                    <div class="my-3">
                        <label for="montoPago" class="form-label">Monto a pagar</label>
                        <input type="number" class="form-control" id="montoPago" name="monto" min="0" step="0.01" required disabled>
                    </div>
                    <div class="mb-3">
                        <label for="faltaPagar" class="form-label">Falta por pagar</label>
                        <input type="text" class="form-control" id="faltaPagar" value="$0.00" disabled>
                    </div>
                    <div class="mt-2">
                        <strong>Total desglose: </strong>
                        <span id="totalDesglosePago">$0.00</span>
                        <span id="desgloseAdvertencia" class="text-danger ms-3" style="display:none;">El total del desglose debe coincidir con el total a pagar.</span>
                    </div>
                </div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success"><i class="fas fa-save me-2"></i>Registrar Pago</button>
                </div>
            </div>
            </form>
        </div>
    </div> -->

    <!-- Modal de Detalles de Pedido -->
<div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderModalLabel">Detalles del Pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div id="detallePagoBody"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

    <div class="modal fade" id="verificarPagoModal" tabindex="-1" aria-labelledby="verificarPagoLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verificarPagoLabel">Verificar Pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="idpagoVerificar">
                <input type="hidden" id="idPedido">
                <div id="tablaComprobantesPago" class="w-100"></div>
                
                <div class="d-flex justify-content-center gap-3 pt-3">
                    <button type="button" class="btn btn-success btn-lg" id="btnAprobar">
                        <i class="fas fa-check-circle me-2"></i> Aprobar
                    </button>
                    <button type="button" class="btn btn-danger btn-lg" id="btnRechazar">
                        <i class="fas fa-times-circle me-2"></i> Rechazar
                    </button>
                </div>
            </div>
            </div>
        </div>
    </div>

    <script>
       /*  const metodosPago = <?php //echo json_encode($metodos); ?>; */
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?php echo APP_URL; ?>/public/js/pago.js" type="module"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>