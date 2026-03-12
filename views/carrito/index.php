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
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/carrito.css">

    
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


<div class="container mt-5">
  <h2 class="text-center mb-4">Proceso de compra</h2>

  <!-- Barra de progreso -->
  <div class="progress mb-4">
    <div id="barraProgreso" class="progress-bar" role="progressbar" style="width: 25%;">Paso 1 de 4</div>
  </div>

  <!-- Carrusel de pasos -->
  <div id="formCarrusel" class="carousel slide" data-bs-interval="false">
    <form id="formCompra" novalidate>
    <div class="carousel-inner">

      <!-- Paso 1 -->
      <div class="carousel-item active">
        <div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold mb-0"><i class="fas fa-shopping-cart"></i> Mi Carrito</h2>
            <p class="text-muted">Estos son los productos que has agregado a tu carrito</p>
        </div>
        <div class="col-md-6 d-flex justify-content-end align-items-start">
            <div class="input-group" style="width: 300px;">
                <input type="text" class="form-control" id="buscadorCarrito" placeholder="Buscar producto en carrito...">
                <button class="btn btn-primary" type="button">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Productos en el carrito (tabla) -->
    <div class="carrito-section">
      <div class="table-responsive">
          <table class="table table-hover align-middle">
              <thead>
                  <tr>
                      <th>Imagen</th>
                      <th>Nombre</th>
                      <th>Categoría</th>
                      <th>Descripción</th>
                      <th>Precio</th>
                      <th>Cantidad</th>
                      <th>Stock</th>
                      <th>Subtotal</th>
                      <th>Acciones</th>
                  </tr>
              </thead>
      <tbody id="carritoBody"></tbody>
          </table>
      </div>
      <!-- Total del carrito -->
    <div class="row align-items-center pt-5">
    <!-- Total de carrito alineado a la derecha -->
    <div class="col-md-12 text-md-end text-center">
        <h4 class="mb-0">Total: <span class="text-success fw-bold" id="total-carrito">$0.00</span></h4>
    </div>
    </div>
    </div>
  </form>    
</div>

<div class="modal fade" id="modalEliminarProducto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="deactivateRoleForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEliminarProductoLabel">
                        <span class="fa-stack">
                            <i class="fas fa-box-open fa-stack-1x"></i>
                            <i class="fas fa-ban fa-stack-2x text-danger"></i>
                        </span>
                        Eliminar producto
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id_producto" name="id">
                    <p>¿Estás seguro de que deseas eliminar este producto del carrito?</p>
                    <p class="text-danger">Esta acción puede revertirse.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="eliminar_producto_carrito">Eliminar</button>
                </div>
            </form>
        </div>
    </div>
</div>

      </div>

<!-- Paso 2 -->
<div class="carousel-item">
  <div class="container py-5" id="paso-envio">
    <div class="row justify-content-center">
      <div class="col-md-10 col-lg-8">
        <div class="card border-0 shadow rounded-4">
          <div class="card-header bg-success border-0 text-white rounded-top-4">
            <h5 class="mb-0 d-flex align-items-center">
              <i class="bi bi-truck me-2 fs-4"></i> Modo de Entrega
            </h5>
          </div>
          <div class="card-body p-4">
            <!-- Selección del modo de entrega -->
            <div class="row g-3 align-items-end mb-4">
              <div class="col-md-6">
                <select class="form-select shadow-sm" id="modoEntrega">
                  <option value="">Seleccione una opción</option>
                  <option value="Envio Nacional">Envío Nacional</option>
                  <option value="Delivery">Delivery</option>
                </select>
                <div class="form-text text-muted">
                  Elige cómo deseas recibir tu pedido.
                </div>
              </div>
              <div class="col-md-6">
                <div id="mensaje-retiro" class="alert alert-info d-none py-2 px-3 mb-0 rounded-3">
                  <i class="bi bi-shop me-1"></i>
                  <strong>Retiro en tienda seleccionado.</strong> No necesitas completar datos de envío.
                </div>
              </div>
            </div>

            <!-- Empresa de Envío y Dirección (Envio Nacional) -->
            <div class="row g-3 envio-nacional-campos d-none" id="camposEnvioNacional">
              <div class="col-md-6">
                <label for="empresaEnvio" class="form-label fw-semibold">
                  <i class="bi bi-box-seam me-1"></i> Empresa de Envío
                </label>
                <select class="form-select shadow-sm" id="empresaEnvio">
                  <option value="">Selecciona una empresa...</option>
                  <?php foreach ($empresas as $empresa): ?>
                    <option value="<?php echo $empresa['id_empresa_envio']; ?>"><?php echo $empresa['nombre']; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-6">
                <label for="direccionEnvio" class="form-label fw-semibold">
                  <i class="bi bi-geo-alt me-1"></i> Dirección de entrega
                </label>
                <textarea class="form-control shadow-sm" id="direccionEnvio" rows="2" placeholder="Escribe la dirección exacta..." data-min="5" data-max="100"></textarea>
                <div class="form-text text-muted">
                  Especifica la dirección donde deseas recibir tu pedido.
                </div>
              </div>
            </div>

            <!-- Datos destinatario (Delivery) -->
        <div class="row g-3 delivery-campos d-none mt-2" id="camposDelivery">
        <div class="col-md-6">
            <label for="destinatario" class="form-label fw-semibold">
            <i class="bi bi-person me-1"></i> Destinatario
            </label>
            <input type="text" class="form-control shadow-sm" id="destinatario" maxlength="45" placeholder="Nombre completo">
            <span id="destinatarioError" class="text-danger"></span>
        </div>
        <div class="col-md-6">
            <label for="telefono_destinatario" class="form-label fw-semibold">
            <i class="bi bi-telephone me-1"></i> Teléfono destinatario
            </label>
            <input type="text" class="form-control shadow-sm" id="telefono_destinatario" maxlength="45" placeholder="Ej: 0414-1234567" data-min="11" data-max="11">
            <span id="telefono_destinatarioError" class="text-danger"></span>
          </div>
        <div class="col-md-6">
            <label for="correo_destinatario" class="form-label fw-semibold">
            <i class="bi bi-envelope me-1"></i> Correo destinatario
            </label>
            <input type="email" class="form-control shadow-sm" id="correo_destinatario" maxlength="45" placeholder="correo@ejemplo.com" data-min="12" data-max="45">
            <span id="correo_destinatarioError" class="text-danger"></span>
          </div>
        <div class="col-md-6">
            <label for="direccion_exacta" class="form-label fw-semibold">
            <i class="bi bi-geo-alt me-1"></i> Dirección exacta
            </label>
            <textarea class="form-control shadow-sm" id="direccion_exacta" maxlength="60" rows="2" placeholder="Escribe la dirección exacta de entrega..." data-min="5" data-max="100"></textarea>
            <span id="direccion_exactaError" class="text-danger"></span>
            <div class="form-text text-muted">
            Especifica la dirección donde deseas recibir tu pedido.
            </div>
        </div>
        </div>
        </div>
        </div>
      </div>
    </div>
  </div>
</div>
      <!-- Paso 3 -->
      <div class="carousel-item">
        <!-- REGISTRAR PAGO (Versión sin modal) -->
<section class="container mt-4" id="seccionRegistrarPago">
    <form id="formRegistrarPago" enctype="multipart/form-data">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-credit-card"></i> Registrar Pago</h5>
            </div>
            <div class="card-body">
                <!-- Tabla de productos del pedido -->
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
                                <!-- Productos se cargan dinámicamente -->
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

                <!-- Desglose de pago en USD y Bs -->
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
                                    <th class="col-comprobante">Comprobante</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody id="desglosePagoBody">
                                <tr>
                                    <td>
                                        <select class="form-select metodo-desglose" name="metodo_pago" required>
                                            <option value="">-- Selecciona método de pago --</option>
                                            <?php foreach ($metodos as $m) : 
                                              if ($m['id_metodo_pago'] != '1') :
                                              ?>
                                                <option value="<?php echo $m['id_metodo_pago']; ?>"><?php echo $m['nombre']; ?></option>
                                            <?php endif;  endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-select moneda-desglose" name="moneda" required>
                                            <option value="">-- Selecciona moneda --</option>
                                            <!-- Monedas se cargan dinámicamente -->
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control monto-desglose" min="0" step="0.01" required>
                                    </td>
                                    <td class="col-referencia">
                                        <input type="text" class="form-control referencia-desglose" disabled data-tipo="numeros" data-min="5" data-max="15">
                                    </td>
                                    <td>
                                      <input type="file" class="form-control form-control-sm comprobante-desglose" accept="image/*,application/pdf" disabled>
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
                            <i class="fas fa-plus"></i> Agregar metodo de pago
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm d-none" id="bancoAPagar">
                            <i class="fas fa-plus"></i> <span>Datos de pago</span>
                        </button>
                    </div>

                    <!-- Monto a pagar -->
                    <div class="my-3">
                        <label for="montoPago" class="form-label">Monto a pagar en $</label>
                        <input type="number" class="form-control" id="montoPago" name="monto" min="0"  required disabled>
                        <span id="montoPagoError" class="text-danger"></span>
                    </div>
                     <div class="my-3">
                        <label for="montoPagoBs" class="form-label">Monto a pagar en Bs.</label>
                        <input type="number" class="form-control" id="montoPagoBs" name="monto" min="0"  required disabled>
                        <span id="montoPagoBsError" class="text-danger"></span>
                    </div>

                    <!-- Falta por pagar -->
                    <div class="mb-3">
                        <label for="faltaPagar" class="form-label">Falta por pagar</label>
                        <input type="text" class="form-control" id="faltaPagar" value="$0.00" disabled>
                        <span id="faltaPagarError" class="text-danger"></span>
                        <input type="hidden" id="cedula" name="cedula" value="<?php echo htmlspecialchars($_SESSION['user']['cedula'] ?? ''); ?>">
                    </div>

                    <div class="mt-2">
                        <strong>Total desglose: </strong>
                        <span id="totalDesglosePago">$0.00</span>
                        <span id="desgloseAdvertencia" class="text-danger ms-3" style="display:none;">
                          Por favor, comprueba los datos y el monto tolal a pagar.
                        </span>
                    </div>
                </div>
            </div>

            <div class="card-footer d-flex justify-content-between">
                <button id="btnAtrasUltimoPaso" class="btn btn-secondary me-2" type="button">Atrás</button>
                <button type="submit" class="btn btn-success"><i class="fas fa-save me-2"></i>Registrar Pago</button>
            </div>
        </div>
    </form>
</section>
<div class="modal fade" id="modalBancoAPagar" tabindex="-1" aria-labelledby="modalBancoAPagarLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content rounded-4">
      <div class="modal-header rounded-top-4">
        <h5 class="modal-title" id="modalBancoAPagarLabel">
          <i class="fas fa-university me-2"></i> <span id="tituloModalBanco"></span>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <ul class="list-group" id="datosBancoDestino">
            <li class="list-group-item"><strong>Banco:</strong> <span id="bancoDestino"></span></li>
            <li class="list-group-item d-none" id="liNumeroCuenta"><strong>Número de cuenta:</strong> <span id="numeroCuentaDestino"></span></li>
            <li class="list-group-item d-none" id="liCedula"><strong>Cédula:</strong> <span id="cedulaDestino"></span></li>
            <li class="list-group-item d-none" id="liTelefono"><strong>Teléfono:</strong> <span id="telefonoDestino"></span></li>
            <li class="list-group-item" id="liCorreo"><strong>Correo:</strong> <span id="correoDestino"></span></li>
        </ul>
      </div>
    </div>
  </div>
</div>
    </div>
    </div>
    <!-- Botones de navegación -->
    <div class="d-flex justify-content-between mt-4">
      <button id="btnAtrasPaso" class="btn btn-secondary me-2" type="button" style="visibility: hidden;">Atrás</button>
        <?php if ($tipoUsuario[0] != 'visitante'): ?>
          <button class="btn btn-primary" id="btnSiguientePaso" type="button"></button> 
        <?php else: ?>
          <!-- Contenido alternativo si $menu es 'header' o 'headerCliente' -->
          <button type="button" class="btn btn-primary" id="loginmodal" data-bs-toggle="modal" data-bs-target="#loginModal">
            <i class="fas fa-cart-plus"></i> Iniciar Sesion
          </button>
        <?php endif; ?>   
    </div>
  </div>
</div>

 <?php 
     include 'views/componentes/login.php';
  ?>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Custom JS -->
     <script type="module" src="<?php echo APP_URL; ?>/public/js/login.js"></script>
    <script src="<?php echo APP_URL; ?>/public/js/registroUsuarioCliente.js" type="module"></script>
    <script>
        const metodosPago = <?php echo json_encode($metodos); ?>;
    </script>
    <script src="<?php echo APP_URL; ?>/public/js/alertas.js" type="module"></script>
    <script src="<?php echo APP_URL; ?>/public/js/carrito.js" type="module"></script>
    <script src="<?php echo APP_URL; ?>/public/js/validaciones.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof Validaciones !== 'undefined') {
                Validaciones.init('#formCompra');
            }
        });
    </script> 
</body>
</html>