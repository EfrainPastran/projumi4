<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PROJUMI - Clientes</title>
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
    include "views/".$menu.".php";
    ?>
    <br>
    <br>

    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header">
                        <h2 class="mb-0">Gestión de Clientes - PROJUMI</h2>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-4">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#agregarClienteModal">
                                <i class="fas fa-plus me-2"></i>Nuevo Cliente
                            </button>
                            <div class="input-group buscar-input">
                                <input type="text" class="form-control" placeholder="Buscar cliente..." id="buscarCliente">
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
                                            <th>Cédula</th>
                                            <th>Nombre</th>
                                            <th>Apellido</th>
                                            <th>Correo</th>
                                            <th>Teléfono</th>
                                            <th>Estado</th>
                                            <th>Fecha Registro</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data as $cliente): ?>
                                            <tr>
                                                <td><?php echo $cliente['cedula']; ?></td>
                                                <td><?php echo htmlspecialchars($cliente['nombre']); ?></td>
                                                <td><?php echo htmlspecialchars($cliente['apellido']); ?></td>
                                                <td><?php echo htmlspecialchars($cliente['correo']); ?></td>
                                                <td><?php echo htmlspecialchars($cliente['telefono']); ?></td>
                                                <td>
                                                    <?php 
                                                    $status = $cliente['estatus'] == 1 ? 'activo' : 'inactivo'; 
                                                    $badgeClass = $cliente['estatus'] == 1 ? 'bg-success' : 'bg-danger';
                                                    ?>
                                                    <span class="badge <?php echo $badgeClass; ?>">
                                                        <?php echo ucfirst($status); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('d/m/Y', strtotime($cliente['fecha_registro'])); ?></td>
                                                <td class="acciones-btn">
                                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editarClienteModal" 
                                                        data-id="<?php echo $cliente['id_cliente']; ?>"
                                                        data-cedula="<?php echo $cliente['cedula']; ?>"
                                                        data-nombre="<?php echo htmlspecialchars($cliente['nombre']); ?>"
                                                        data-apellido="<?php echo htmlspecialchars($cliente['apellido']); ?>"
                                                        data-correo="<?php echo htmlspecialchars($cliente['correo']); ?>"
                                                        data-telefono="<?php echo htmlspecialchars($cliente['telefono']); ?>"
                                                        data-fecha_nacimiento="<?php echo $cliente['fecha_nacimiento']; ?>"
                                                        data-estatus="<?php echo $cliente['estatus']; ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#eliminarClienteModal"
                                                        data-id="<?php echo $cliente['id_cliente']; ?>"
                                                        data-nombre="<?php echo htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido']); ?>">
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
            </div>
        </div>
    </div>

    <!-- Modal Agregar Cliente -->
    <div class="modal fade" id="agregarClienteModal" tabindex="-1" aria-labelledby="agregarClienteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="agregarClienteModalLabel">Agregar Nuevo Cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo APP_URL; ?>/clientes/register" method="post">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="cedulaCliente" class="form-label">Cédula</label>
                            <input type="number" class="form-control" id="cedulaCliente" name="cedula" required>
                        </div>
                        <div class="mb-3">
                            <label for="nombreCliente" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombreCliente" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="apellidoCliente" class="form-label">Apellido</label>
                            <input type="text" class="form-control" id="apellidoCliente" name="apellido" required>
                        </div>
                        <div class="mb-3">
                            <label for="correoCliente" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="correoCliente" name="correo" required>
                        </div>
                        <div class="mb-3">
                            <label for="telefonoCliente" class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" id="telefonoCliente" name="telefono" required>
                        </div>
                        <div class="mb-3">
                            <label for="fechaNacimientoCliente" class="form-label">Fecha de Nacimiento</label>
                            <input type="date" class="form-control" id="fechaNacimientoCliente" name="fecha_nacimiento" required>
                        </div>
                        <div class="mb-3">
                            <label for="estatusCliente" class="form-label">Estado</label>
                            <select class="form-select" id="estatusCliente" name="estatus" required>
                                <option value="1" selected>Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Registrar Cliente</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Cliente -->
    <div class="modal fade" id="editarClienteModal" tabindex="-1" aria-labelledby="editarClienteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarClienteModalLabel">Editar Cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo APP_URL; ?>/clientes/clientes_update" method="post">
                    <input type="hidden" id="idClienteEditar" name="id_cliente">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="cedulaClienteEditar" class="form-label">Cédula</label>
                            <input type="number" class="form-control" id="cedulaClienteEditar" name="cedula" required>
                        </div>
                        <div class="mb-3">
                            <label for="nombreClienteEditar" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombreClienteEditar" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="apellidoClienteEditar" class="form-label">Apellido</label>
                            <input type="text" class="form-control" id="apellidoClienteEditar" name="apellido" required>
                        </div>
                        <div class="mb-3">
                            <label for="correoClienteEditar" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="correoClienteEditar" name="correo" required>
                        </div>
                        <div class="mb-3">
                            <label for="telefonoClienteEditar" class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" id="telefonoClienteEditar" name="telefono" required>
                        </div>
                        <div class="mb-3">
                            <label for="fechaNacimientoClienteEditar" class="form-label">Fecha de Nacimiento</label>
                            <input type="date" class="form-control" id="fechaNacimientoClienteEditar" name="fecha_nacimiento" required>
                        </div>
                        <div class="mb-3">
                            <label for="estatusClienteEditar" class="form-label">Estado</label>
                            <select class="form-select" id="estatusClienteEditar" name="estatus" required>
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Actualizar Cliente</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Eliminar Cliente -->
    <div class="modal fade" id="eliminarClienteModal" tabindex="-1" aria-labelledby="eliminarClienteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eliminarClienteModalLabel">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo APP_URL; ?>/clientes/clientes_delete" method="post">
                    <input type="hidden" id="idClienteEliminar" name="id_cliente">
                    <div class="modal-body">
                        <p>¿Estás seguro que deseas eliminar al cliente <span id="nombreClienteEliminar" class="fw-bold"></span>? Esta acción no se puede deshacer.</p>
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
        $(document).ready(function() {
            // Modal de edición
            $('#editarClienteModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var modal = $(this);
                
                modal.find('#idClienteEditar').val(button.data('id'));
                modal.find('#cedulaClienteEditar').val(button.data('cedula'));
                modal.find('#nombreClienteEditar').val(button.data('nombre'));
                modal.find('#apellidoClienteEditar').val(button.data('apellido'));
                modal.find('#correoClienteEditar').val(button.data('correo'));
                modal.find('#telefonoClienteEditar').val(button.data('telefono'));
                modal.find('#fechaNacimientoClienteEditar').val(button.data('fecha_nacimiento'));
                modal.find('#estatusClienteEditar').val(button.data('estatus'));
            });
            
            // Modal de eliminación
            $('#eliminarClienteModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var modal = $(this);
                
                modal.find('#idClienteEliminar').val(button.data('id'));
                modal.find('#nombreClienteEliminar').text(button.data('nombre'));
            });
            
            // Búsqueda de clientes
            $('#btnBuscar').click(function() {
                var searchText = $('#buscarCliente').val().toLowerCase();
                $('table tbody tr').each(function() {
                    var rowText = $(this).text().toLowerCase();
                    $(this).toggle(rowText.indexOf(searchText) > -1);
                });
            });
            
            $('#buscarCliente').keyup(function(e) {
                if (e.keyCode === 13) {
                    $('#btnBuscar').click();
                }
            });
        });
    </script>
</body>
</html>