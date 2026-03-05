<?php if (!isset($_SESSION['user']['cedula'])): ?>
    <script>window.location.href = '<?= APP_URL ?>home/index';</script>
    <?php exit(); ?>
<?php endif; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PROJUMI -Ventas presencial</title>
    <?php include 'views/componentes/head.php'; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/pagos.css">
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- CSS de Select2 -->
    

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
                <h2 class="fw-bold">Ventas presenciales</h2>
                <p class="text-muted">Revisa las ventas realizadas</p>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-end">
                    <div class="input-group" style="width: 300px;">
                        <input type="text" class="form-control" id="busquedaTexto" placeholder="Buscar...">
                        <button class="btn btn-primary" id="buscarVentas" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 d-flex align-items-center mb-4">
            <?php if (isset($permisos['registrar']) && $permisos['registrar'] === true): ?>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#registrarVentaModal">
                <i class="fas fa-plus me-1"></i> Registrar Ventas
            </button>
            <?php endif; ?>
        </div>
        <!-- Tabla de pedidos -->
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                       <div id="tablaVentasContainer"></div>
                        <!-- Paginación -->
                        <nav aria-label="Page navigation" class="mt-4">
                            <ul class="pagination justify-content-center" id="ventasPagination">
                                <!-- Paginación dinámica -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <!-- Modal para registrar una venta -->
        <div class="modal fade" id="registrarVentaModal" tabindex="-1" aria-labelledby="registrarPagoLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <form id="formRegistrarVenta" enctype="multipart/form-data">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" id="registrarPagoLabel"><i class="fas fa-credit-card"></i> Registrar Venta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                    <div class="row">
                        <div class="mb-3 col-6">
                            <label for="cedula" class="form-label">Cédula del cliente</label>
                            <input type="text" class="form-control" id="cedula" name="cedula" placeholder="Ingrese la cédula del cliente" required data-tipo="numeros" data-min="7" data-max="8">
                            <span id="cedulaError" class="text-danger"></span>
                        </div>
                        <div class="mb-3 col-6" id="mensaje">
                            
                        </div>
                    </div>
                    <div id="datosCliente">
                        <div class="row">
                            <div class="mb-3 col-md-6">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required data-tipo="letras" data-min="5" data-max="45">
                            <span id="nombreError" class="text-danger"></span>
                            </div>
                            <div class="mb-3 col-md-6">
                            <label for="apellido" class="form-label">Apellido</label>
                            <input type="text" class="form-control" id="apellido" name="apellido" required data-tipo="letras" data-min="5" data-max="45">
                            <span id="apellidoError" class="text-danger"></span>
                            </div>
                            <div class="mb-3 col-md-6">
                            <label for="correo" class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control" id="correo" name="correo" required data-tipo="letras" data-min="5" data-max="45">
                            <span id="correoError" class="text-danger"></span>
                            </div>
                            <div class="mb-3 col-md-6">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono" name="telefono" required data-tipo="numeros" data-min="11" data-max="11">
                            <span id="telefonoError" class="text-danger"></span>
                            </div>
                            <div class="mb-3 col-md-6">
                            <label for="direccion" class="form-label">Dirección</label>
                            <input type="text" class="form-control" id="direccion" name="direccion" required data-min="5" data-max="100">
                            <span id="direccionError" class="text-danger"></span>
                            </div>
                            <div class="mb-3 col-md-6">
                            <label for="fecha_nacimiento" class="form-label">Fecha de nacimiento</label>
                            <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required>
                            </div>
                            <input type="hidden" name="fk_rol" value="3">
                            <input type="hidden" name="estatus" value="1"> <!-- Estatus activo por defecto -->
                        </div>
                    </div>
                    
                    <div id="productosVendidosContainer" class="mb-3">
                        <label class="form-label">Agregar productos vendidos</label>
                        <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle">
                            <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio unitario</th>
                                <th>Subtotal</th>
                                <th>Acción</th>
                            </tr>
                            </thead>
                            <tbody id="tablaProductosVendidos">
                            </tbody>
                            <tfoot>
                            <tr>
                                <th colspan="4" class="text-end">Total a pagar:</th>
                                <th id="totalPagarPedido">$0.00</th>
                            </tr>
                            </tfoot>
                        </table>
                        </div>
                    </div>
                    <div class="mb-3" id="desglosePagoContainer">
                        <label class="form-label">Desglose de pago por moneda</label>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle" id="tablaDesglosePago">
                                <thead>
                                    <tr>
                                        <th>Método</th>
                                        <th>Moneda</th>
                                        <th>Monto</th>
                                        <th>Referencia</th>
                                        <th>accion</th>
                                    </tr>
                                </thead>
                                <tbody id="desglosePagoBody">
                                    <tr>
                                        <td>
                                            <select class="form-select metodo-desglose" name="metodo_pago" required>
                                            <option value="">-- Selecciona tipo de dinero --</option>
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
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm btn-remove-desglose" disabled>
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="mt-2 mb-4">
                                <button type="button" class="btn btn-success btn-sm" id="btnAgregarDesglose">
                                    <i class="fas fa-plus"></i> Agregar moneda
                                </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="fondoFaltanteDolar" class="form-label">Fondo faltante (Dólares)</label>
                            <input type="text" class="form-control" id="fondoFaltanteDolar" value="$0.00" disabled>
                            
                        </div>
                        <div class="mb-3">
                            <label for="fondoFaltantebs" class="form-label">Fondo faltante (Bolívares)</label>
                            <input type="text" class="form-control" id="fondoFaltantebs" value="$0.00" disabled>
                        </div>
                        <div class="mt-2">
                            <strong>Total desglose: </strong>
                            <span id="totalDesglosePago">$0.00</span>
                            <span id="desgloseAdvertencia" class="text-danger ms-3" style="display:none;">El total del desglose debe coincidir con el total del fondo.</span>
                        </div>
                    </div>
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-save me-2"></i>Registrar Ventas</button>
                    </div>
                </div>
                </form>
            </div>
        </div>


        <!--Modal para ver detalles de una venta-->
        <div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="orderModalLabel">Detalles de la Venta</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div id="detalleVentaBody"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const metodosPago = <?php echo json_encode($metodos); ?>;
    </script>


        <script src="<?php echo APP_URL; ?>/public/js/alertas.js" type="module"></script>  
    <script src="<?php echo APP_URL; ?>/public/js/ventaspresencial.js" type="module"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo APP_URL; ?>/public/js/validaciones.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof Validaciones !== 'undefined') {
                Validaciones.init('#formRegistrarVenta');
                Validaciones.limitarCalendario('#fecha_nacimiento');
            }
        });
    </script> 
</body>
</html>