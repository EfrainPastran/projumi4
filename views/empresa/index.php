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
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    include "views/navbar.php";
    ?>
    <br>
    <br>

    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header">
                        <h2 class="mb-0">Gestión de Empresas de Envío - PROJUMI</h2>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-4">
                            <?php if (!empty($permisos['registrar'])): ?>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#agregarEmpresaModal">
                                    <i class="fas fa-plus me-2"></i>Nueva Empresa
                                </button>
                            <?php endif; ?>
                            <div class="input-group buscar-input">
                                <input type="text" class="form-control" placeholder="Buscar empresa..." id="buscarEmpresa">
                                <button class="btn btn-outline-secondary" type="button" id="btnBuscar">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>

                        <div class="table-container">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Teléfono</th>
                                            <th>Dirección</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data as $empresa): ?>
                                            <tr>
                                                <td><?php echo $empresa['id_empresa_envio']; ?></td>
                                                <td><?php echo htmlspecialchars($empresa['nombre']); ?></td>
                                                <td><?php echo htmlspecialchars($empresa['telefono']); ?></td>
                                                <td><?php echo htmlspecialchars($empresa['direccion']); ?></td>
                                                <td>
                                                    <?php
                                                    $status = $empresa['estatus'] == 1 ? 'activo' : 'inactivo';
                                                    $badgeClass = $empresa['estatus'] == 1 ? 'bg-success' : 'bg-danger';
                                                    ?>
                                                    <span class="badge <?php echo $badgeClass; ?>">
                                                        <?php echo ucfirst($status); ?>
                                                    </span>
                                                </td>
                                                <td class="acciones-btn">
                                                    <?php if (!empty($permisos['actualizar'])): ?>
                                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editarEmpresaModal"
                                                            data-id="<?php echo $empresa['id_empresa_envio']; ?>"
                                                            data-nombre="<?php echo htmlspecialchars($empresa['nombre']); ?>"
                                                            data-telefono="<?php echo htmlspecialchars($empresa['telefono']); ?>"
                                                            data-direccion="<?php echo htmlspecialchars($empresa['direccion']); ?>"
                                                            data-estado="<?php echo $empresa['estatus']; ?>">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    <?php endif; 
                                                    if (!empty($permisos['eliminar'])):
                                                    ?>
                                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#eliminarEmpresaModal"
                                                            data-id="<?php echo $empresa['id_empresa_envio']; ?>">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Agregar Empresa -->
    <div class="modal fade" id="agregarEmpresaModal" tabindex="-1" aria-labelledby="agregarEmpresaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="agregarEmpresaModalLabel">Agregar Nueva Empresa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo APP_URL; ?>/empresa/register" method="post">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nombreEmpresa" class="form-label">Nombre de la empresa</label>
                            <input type="text" class="form-control" id="nombreEmpresa" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="telefonoEmpresa" class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" id="telefonoEmpresa" name="telefono" required>
                        </div>
                        <div class="mb-3">
                            <label for="direccionEmpresa" class="form-label">Dirección</label>
                            <textarea class="form-control" id="direccionEmpresa" name="direccion" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="estadoEmpresa" class="form-label">Estado</label>
                            <select class="form-select" id="estadoEmpresa" name="estado" required>
                                <option value="1" selected>Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Empresa</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Empresa -->
    <div class="modal fade" id="editarEmpresaModal" tabindex="-1" aria-labelledby="editarEmpresaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarEmpresaModalLabel">Editar Empresa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo APP_URL; ?>/empresa/update" method="post">
                    <input type="hidden" id="idEmpresaEditar" name="id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nombreEmpresaEditar" class="form-label">Nombre de la empresa</label>
                            <input type="text" class="form-control" id="nombreEmpresaEditar" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="telefonoEmpresaEditar" class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" id="telefonoEmpresaEditar" name="telefono" required>
                        </div>
                        <div class="mb-3">
                            <label for="direccionEmpresaEditar" class="form-label">Dirección</label>
                            <textarea class="form-control" id="direccionEmpresaEditar" name="direccion" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="estadoEmpresaEditar" class="form-label">Estado</label>
                            <select class="form-select" id="estadoEmpresaEditar" name="estado" required>
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Actualizar Empresa</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Eliminar Empresa -->
    <div class="modal fade" id="eliminarEmpresaModal" tabindex="-1" aria-labelledby="eliminarEmpresaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eliminarEmpresaModalLabel">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo APP_URL; ?>/empresa/delete" method="post">
                    <input type="hidden" id="idEmpresaEliminar" name="id">
                    <div class="modal-body">
                        <p>¿Estás seguro que deseas eliminar esta empresa de envío? Esta acción no se puede deshacer.</p>
                        <p class="fw-bold" id="nombreEmpresaEliminar"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Script para manejar los modales de edición y eliminación
        $(document).ready(function() {
            // Modal de edición
            $('#editarEmpresaModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                var nombre = button.data('nombre');
                var telefono = button.data('telefono');
                var direccion = button.data('direccion');
                var estado = button.data('estado');
               
                var modal = $(this);
                modal.find('#idEmpresaEditar').val(id);
                modal.find('#nombreEmpresaEditar').val(nombre);
                modal.find('#telefonoEmpresaEditar').val(telefono);
                modal.find('#direccionEmpresaEditar').val(direccion);
                modal.find('#estadoEmpresaEditar').val(estado);
            });
           
            // Modal de eliminación
            $('#eliminarEmpresaModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                var nombre = button.data('nombre');
               
                var modal = $(this);
                modal.find('#idEmpresaEliminar').val(id);
                modal.find('#nombreEmpresaEliminar').text('Empresa: ' + nombre);
            });
           
            // Búsqueda simple
            $('#btnBuscar').click(function() {
                var searchText = $('#buscarEmpresa').val().toLowerCase();
                $('table tbody tr').each(function() {
                    var rowText = $(this).text().toLowerCase();
                    $(this).toggle(rowText.indexOf(searchText) > -1);
                });
            });
           
            $('#buscarEmpresa').keyup(function(e) {
                if (e.keyCode === 13) {
                    $('#btnBuscar').click();
                }
            });
        });
    </script>
</body>
</html>