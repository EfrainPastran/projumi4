<?php if (!isset($_SESSION['user']['cedula'])): ?>
    <script>window.location.href = '<?= APP_URL ?>home/index';</script>
    <?php exit(); ?>
<?php endif; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PROJUMI - Gestión de Envíos</title>
    <?php include 'views/componentes/head.php'; ?>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/envios.css">
</head>
<body>
<?php
// Asegúrate de iniciar la sesión también en la vista
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<?php include "views/navbar.php";?>
<br>
<br>
        <div class="container py-5">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h2 class="fw-bold"><?php echo $title; ?></h2>
                    <p class="text-muted">Revisa el estado de tus envíos pendientes y completados</p>
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-end align-items-end" style="height: 100%;">
                        <div class="input-group" style="width: 300px;">
                            <input type="text" id="busquedaEnvios" class="form-control" placeholder="Buscar envíos...">
                            <button class="btn btn-primary" type="button" id="buscarEnvios">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
        </div>
        </div>

        <!-- Tabla de envíos -->
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs mb-4" id="enviosTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active text-dark" id="envios-tab" data-bs-toggle="tab" data-bs-target="#envios" type="button" role="tab" aria-controls="envios" aria-selected="true">
                                    Envíos
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link text-dark" id="delivery-tab" data-bs-toggle="tab" data-bs-target="#delivery" type="button" role="tab" aria-controls="delivery" aria-selected="false">
                                    Delivery
                                </button>
                            </li>
                        </ul>
                        <!-- Tab panes -->
                        <div class="tab-content" id="enviosTabsContent">
                            <!-- TAB ENVÍOS -->
                            <div class="tab-pane fade show active" id="envios" role="tabpanel" aria-labelledby="envios-tab">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead id="tabla-envios-header">
                                          <!-- Las columnas se llenan dinámicamente con JS -->  
                                        </thead>
                                        <tbody id="tabla-envios">
                                          <!-- Aquí se insertan los datos dinámicamente -->
                                        </tbody>
                                    </table>
                                </div>
                                <nav aria-label="Page navigation" class="mt-4">
                                    <ul class="pagination justify-content-center" id="paginacion-envios">
                                        <!-- Aquí se llenará con JS -->
                                    </ul>
                                </nav>
                            </div>
                            <!-- TAB DELIVERY -->
                            <div class="tab-pane fade" id="delivery" role="tabpanel" aria-labelledby="delivery-tab">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                      <thead id="thead-delivery">
                                          <!-- Las columnas se llenan dinámicamente con JS -->
                                      </thead>
                                      <tbody id="tabla-delivery">
                                          <!-- Aquí se insertan los datos dinámicamente -->
                                      </tbody>
                                    </table>
                                </div>
                                <nav aria-label="Page navigation" class="mt-4">
                                    <ul class="pagination justify-content-center" id="paginacion-delivery">
                                        <!-- Aquí se llenará con JS -->
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

  <!-- Modal Envío -->
<div class="modal fade" id="modalEnvio" tabindex="-1" aria-labelledby="modalEnvioLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalEnvioLabel">
          <i class="fas fa-truck"></i> Detalle del Envío
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <form id="formEnvio" action="<?php echo APP_URL; ?>/envios/actualizarEnvio" method="post">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold"><i class="fas fa-hashtag text-success"></i> ID Envío</label>
              <input type="text" class="form-control" id="envio_id" disabled>
            </div>
            <div class="col-md-6 campo-cliente">
              <label class="form-label fw-semibold"><i class="fas fa-user text-success"></i> Cliente</label>
              <input type="text" class="form-control" id="envio_cliente" disabled>
            </div>
            <div class="col-md-6 campo-emprendedor">
              <label class="form-label fw-semibold"><i class="fas fa-user-tie text-success"></i> Emprendedor</label>
              <input type="text" class="form-control" id="envio_emprendedor" disabled>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold"><i class="fas fa-phone text-success"></i> Teléfono Empresa</label>
              <input type="text" class="form-control" id="envio_telefono_empresa" disabled>
            </div>
            <div class="col-md-6 campo-telefono">
              <label class="form-label fw-semibold"><i class="fas fa-phone text-success"></i>Teléfono del cliente</label>
              <input type="text" class="form-control" id="envio_telefono" disabled>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold"><i class="fas fa-map-marker-alt text-success"></i> Dirección</label>
              <input type="text" class="form-control" id="envio_direccion" disabled>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold"><i class="fas fa-info-circle text-success"></i> Estado</label>
              <select class="form-control" name="" id="envio_estado">
                <option value="En proceso">En proceso</option>
                <option value="Entregado">Entregado</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold"><i class="fas fa-building text-success"></i> Empresa</label>
              <input type="text" class="form-control" id="envio_empresa" disabled>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold"><i class="fas fa-barcode text-success"></i> N° Seguimiento</label>
              <input type="text" class="form-control" id="envio_seguimiento" placeholder="Ingrese o edite el número de seguimiento" data-tipo="numeros" data-min="5" data-max="15" >
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
          <i class="fas fa-times"></i> Cerrar
        </button>
        <button type="submit" class="btn btn-primary" id="guardarEnvio">
          <i class="fas fa-save"></i> Guardar
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Delivery -->
<div class="modal fade" id="modalDelivery" tabindex="-1" aria-labelledby="modalDeliveryLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header text-white">
        <h5 class="modal-title" id="modalDeliveryLabel">
          <i class="fas fa-motorcycle"></i> Detalle del Delivery
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <form id="formDelivery">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold"><i class="fas fa-hashtag text-success"></i> ID Delivery</label>
              <input type="text" class="form-control" id="delivery_id" disabled>
            </div>
            <!-- Campo Cliente -->
            <div class="col-md-6 campo-cliente1">
              <label class="form-label fw-semibold"><i class="fas fa-user text-success"></i> Cliente</label>
              <input type="text" class="form-control" id="delivery_cliente" disabled>
            </div>
            <!-- Campo Emprendedor -->
            <div class="col-md-6 campo-emprendedor1">
              <label class="form-label fw-semibold"><i class="fas fa-user-tie text-success"></i> Emprendedor</label>
              <input type="text" class="form-control" id="delivery_emprendedor" disabled>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold"><i class="fas fa-map-marker-alt text-success"></i> Dirección Exacta</label>
              <input type="text" class="form-control" id="delivery_direccion" disabled>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold"><i class="fas fa-user text-success"></i> Destinatario</label>
              <input type="text" class="form-control" id="delivery_destinatario" disabled>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold"><i class="fas fa-phone text-success"></i> Teléfono Destinatario</label>
              <input type="text" class="form-control" id="delivery_telefono_destinatario" disabled>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold"><i class="fas fa-envelope text-success"></i> Correo Destinatario</label>
              <input type="text" class="form-control" id="delivery_correo_destinatario" disabled>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold"><i class="fas fa-phone-volume text-success"></i> Teléfono Delivery</label>
              <input type="text" class="form-control" id="delivery_telefono_delivery" data-tipo="numeros" data-min="11" data-max="11" disabled>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold"><i class="fas fa-info-circle text-success"></i> Estatus</label>
              <input type="text" class="form-control" id="delivery_estatus" disabled>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
          <i class="fas fa-times"></i> Cerrar
        </button>
        <button type="button" class="btn btn-primary" id="guardarDelivery">
          <i class="fas fa-save"></i> Guardar
        </button>
      </div>
    </div>
  </div>
</div>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- JavaScript personalizado -->
        <script src="<?php echo APP_URL; ?>/public/js/alertas.js" type="module"></script>
    <script src="<?php echo APP_URL; ?>/public/js/envios.js" type="module"></script>
    <script src="<?php echo APP_URL; ?>/public/js/validaciones.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof Validaciones !== 'undefined') {
                Validaciones.init('#formEnvio');
                Validaciones.init('#formDelivery');
            }
        });
    </script> 
    
<script>

</script>
</body>
</html>