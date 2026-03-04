<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PROJUMI - Empresas de Envío</title>
    <?php include 'views/componentes/head.php'; ?>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/roles.css">
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
    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <?php if (isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> <?= $_SESSION['flash_success']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
        <?php unset($_SESSION['flash_success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['flash_error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> <?= $_SESSION['flash_error']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
            <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>
      <h1 class="text-center text-md-start">
        <i class="fas fa-calendar-alt me-2" style="color: var(--verde-projumi);"></i>
        Gestión de Eventos
      </h1>
      <button class="btn btn-projumi" data-bs-toggle="modal" data-bs-target="#modalRegistrarEvento">
        <i class="fas fa-plus me-2"></i>Nuevo Evento
      </button>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
      <div class="card-body">
        <form class="row g-3">
          <div class="col-md-3">
            <label for="filtroNombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="filtroNombre" placeholder="Buscar evento...">
          </div>
          <div class="col-md-3">
            <label for="filtroFecha" class="form-label">Fecha</label>
            <input type="date" class="form-control" id="filtroFecha">
          </div>
          <div class="col-md-3">
            <label for="filtroEstado" class="form-label">Estado</label>
            <select id="filtroEstado" class="form-select">
              <option selected>Todos</option>
              <option>Activo</option>
              <option>Inactivo</option>
            </select>
          </div>
          <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-projumi w-100">
              <i class="fas fa-filter me-2"></i>Filtrar
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Lista de eventos (Tabla) -->
    <div class="card">
      <div class="card-header card-header-evento">
        <i class="fas fa-list me-2"></i>Eventos Registrados
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover table-eventos">
            <thead>
              <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>
                <th>Dirección</th>
                <th>Estado</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($data as $evento): ?>
                <tr>
                  <td><?php echo $evento['id_eventos']; ?></td>
                  <td><?php echo htmlspecialchars($evento['nombre']); ?></td>
                  <td><?php echo htmlspecialchars($evento['fecha_inicio']); ?></td>
                  <td><?php echo htmlspecialchars($evento['fecha_fin']); ?></td>
                  <td><?php echo htmlspecialchars($evento['direccion']); ?></td>
                  <td>
                    <?php 
                    $status = $evento['status'] == 1 ? 'activo' : 'inactivo'; 
                    $badgeClass = $evento['status'] == 1 ? 'bg-success' : 'bg-danger';
                    ?>
                    <span class="badge <?php echo $badgeClass; ?>">
                      <?php echo ucfirst($status); ?>
                    </span>
                  </td>
                  <td class="acciones-btn">
                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEditarEvento" 
                      data-id_eventos="<?php echo $evento['id_eventos']; ?>"
                      data-nombre="<?php echo htmlspecialchars($evento['nombre']); ?>"
                      data-direccion="<?php echo htmlspecialchars($evento['direccion']); ?>"
                      data-fecha_inicio="<?php echo htmlspecialchars($evento['fecha_inicio']); ?>"
                      data-fecha_fin="<?php echo htmlspecialchars($evento['fecha_fin']); ?>"
                      data-status="<?php echo $evento['status']; ?>">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#eliminarEventoModal"
                      data-id_eventos="<?php echo $evento['id_eventos']; ?>"
                      data-nombre="<?php echo htmlspecialchars($evento['nombre']); ?>">
                      <i class="fas fa-trash"></i>
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal para NUEVO evento -->
  <div class="modal fade" id="modalRegistrarEvento" tabindex="-1" aria-labelledby="modalRegistrarEventoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalRegistrarEventoLabel">
            <i class="fas fa-calendar-plus me-2"></i>Nuevo Evento
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="formAgregarEvento" action="<?php echo APP_URL; ?>/eventos/register" method="post"> 
              <div class="row g-3">
                  <div class="col-md-6">
                      <label for="nombre" class="form-label">Nombre del Evento</label>
                      <input type="text" class="form-control" id="nombre" name="nombre" 
                            required data-tipo="letras" data-min="5" data-max="45">
                  </div>
                  <div class="col-md-6">
                      <label for="direccion" class="form-label">Dirección</label>
                      <input type="text" class="form-control" id="direccion" name="direccion" 
                            required data-min="10" data-max="100">
                  </div>
                  <div class="col-md-6">
                      <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                      <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                  </div>
                  <div class="col-md-6">
                      <label for="fecha_fin" class="form-label">Fecha Fin</label>
                      <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
                  </div>
                  <div class="col-md-6">
                      <label for="status" class="form-label">Estado</label>
                      <select class="form-select" id="status" name="status" required>
                          <option value="1" selected>Activo</option>
                          <option value="0">Inactivo</option>
                      </select>
                  </div>
              </div>
              <div class="modal-footer mt-3">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                  <input type="submit" class="btn btn-projumi" value="Registrar Evento">
              </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal para EDITAR evento -->
  <div class="modal fade" id="modalEditarEvento" tabindex="-1" aria-labelledby="modalEditarEventoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalEditarEventoLabel">
            <i class="fas fa-edit me-2"></i>Editar Evento
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="formEditarEvento" action="<?php echo APP_URL; ?>/eventos/eventos_update" method="post"> 
            <input type="hidden" id="idEventoEditar" name="id_eventos">
            <div class="row g-3">
              <div class="col-md-6">
                <label for="nombreEditar" class="form-label">Nombre del Evento</label>
                <input type="text" class="form-control" id="nombreEditar" name="nombre" required data-min="5" data-max="45" data-tipo="letras">
              </div>
              <div class="col-md-6">
                <label for="direccionEditar" class="form-label">Dirección</label>
                <input type="text" class="form-control" id="direccionEditar" name="direccion" required data-min="10" data-max="100" data-tipo="texto">
              </div>
              <div class="col-md-6">
                <label for="fecha_inicioEditar" class="form-label">Fecha Inicio</label>
                <input type="date" class="form-control" id="fecha_inicioEditar" name="fecha_inicio" required>
              </div>
              <div class="col-md-6">
                <label for="fecha_finEditar" class="form-label">Fecha Fin</label>
                <input type="date" class="form-control" id="fecha_finEditar" name="fecha_fin" required>
              </div>
              <div class="col-md-6">
                <label for="statusEditar" class="form-label">Estado</label>
                <select class="form-select" id="statusEditar" name="status" required>
                  <option value="1">Activo</option>
                  <option value="0">Inactivo</option>
                </select>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <input type="submit" class="btn btn-projumi" value="Guardar Cambios">
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

     <!-- Modal para ELIMINAR evento -->
  <div class="modal fade" id="eliminarEventoModal" tabindex="-1" aria-labelledby="modalEliminarEventoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-danger">
  <h5 class="modal-title text-white" id="modalEliminarEventoLabel">
    <i class="fas fa-exclamation-triangle me-2"></i>¿Deseas eliminar este evento?
  </h5>
  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
        <div class="modal-body">
          <form action="<?php echo APP_URL; ?>/eventos/eventos_delete" method="post"> 
            <input type="hidden" id="idEventoEliminar" name="id_eventos">
            <div class="row g-3">
              <div class="col-md-6">
                <label for="nombreEditar" class="form-label">Nombre del Evento</label>
                <input type="text" class="form-control" id="nombreEliminar" disabled>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <input type="submit" class="btn btn-danger" value="Eliminar">
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS y dependencias -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="<?php echo APP_URL; ?>/public/js/validaciones.js"></script>
  <script>
        $(document).ready(function() {
            // Inicializar por ID de formulario
            Validaciones.init('#formAgregarEvento');
            Validaciones.init('#formEditarEvento');
        });
  </script>  
  <script>
    $(document).ready(function() {
        // Modal de edición
        $('#modalEditarEvento').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var id_eventos = button.data('id_eventos');
            var nombre = button.data('nombre');
            var direccion = button.data('direccion');
            var fecha_inicio = button.data('fecha_inicio');
            var fecha_fin = button.data('fecha_fin');
            var status = button.data('status');
            
            var modal = $(this);
            modal.find('#idEventoEditar').val(id_eventos);
            modal.find('#nombreEditar').val(nombre);
            modal.find('#direccionEditar').val(direccion);
            modal.find('#fecha_inicioEditar').val(fecha_inicio);
            modal.find('#fecha_finEditar').val(fecha_fin);
            modal.find('#statusEditar').val(status);
        });

        // Modal de eliminación
        $('#eliminarEventoModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var id_eventos = button.data('id_eventos');
            var nombre = button.data('nombre');
            
            var modal = $(this);
            modal.find('#idEventoEliminar').val(id_eventos);
            modal.find('#nombreEliminar').val(nombre);
        });
    });
  </script>
</body>
</html>