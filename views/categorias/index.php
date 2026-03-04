<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PROJUMI - Roles</title>
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
        include "views/navbar.php";

    ?>

    <br>
    <br>

    <div class="container py-5">
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

        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header">
                        <h2 class="mb-0">Gestión de Categorías - PROJUMI</h2>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-4">
                            <?php if (isset($permisos['registrar']) && $permisos['registrar'] === true): ?>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#agregarCategoriaModal">
                                    <i class="fas fa-plus me-2"></i>Nueva Categoría
                                </button>
                            <?php endif; ?>
                            <div class="input-group buscar-input">
                                <input type="text" class="form-control" placeholder="Buscar categoría..." id="buscarCategoria">
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
                                            <th>Descripción</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data as $categoria): ?>
                                            <tr>
                                                <td><?php echo $categoria['id_categoria']; ?></td>
                                                <td><?php echo htmlspecialchars($categoria['nombre']); ?></td>
                                                <td><?php echo htmlspecialchars($categoria['descripcion']); ?></td>
                                                <td>
                                                    <?php 
                                                    $status = $categoria['estatus'] == 1 ? 'activo' : 'inactivo'; 
                                                    $badgeClass = $categoria['estatus'] == 1 ? 'bg-success' : 'bg-danger';
                                                    ?>

                                                    <span class="badge <?php echo $badgeClass; ?>">
                                                        <?php echo ucfirst($status); ?>
                                                    </span>
                                                </td>
                                                <td class="acciones-btn">
                                                    <?php if (isset($permisos['actualizar']) && $permisos['actualizar'] === true): ?>
                                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editarCategoriaModal" 
                                                            data-id="<?php echo $categoria['id_categoria']; ?>"
                                                            data-nombre="<?php echo htmlspecialchars($categoria['nombre']); ?>"
                                                            data-descripcion="<?php echo htmlspecialchars($categoria['descripcion']); ?>"
                                                            data-estado="<?php echo $categoria['estatus']; ?>">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                    <?php if (isset($permisos['eliminar']) && $permisos['eliminar'] === true): ?>
                                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#eliminarCategoriaModal"
                                                            data-id="<?php echo $categoria['id_categoria']; ?>"
                                                            data-nombre="<?php echo htmlspecialchars($categoria['nombre']); ?>"
                                                            data-descripcion="<?php echo htmlspecialchars($categoria['descripcion']); ?>"
                                                            data-estado="<?php echo ($categoria['estatus']); ?>">
                                                            <i class="fas fa-trash"></i><div class="3"></div>
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

    <!-- Modal Agregar Categoría -->
    <div class="modal fade" id="agregarCategoriaModal" tabindex="-1" aria-labelledby="agregarCategoriaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="agregarCategoriaModalLabel">Agregar Nueva Categoría</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo APP_URL; ?>/categorias/register" method="post">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nombreCategoria" class="form-label">Nombre de la categoría</label>
                            <input type="text" class="form-control" id="nombreCategoria" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="descripcionCategoria" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcionCategoria" name="descripcion" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="estadoCategoria" class="form-label">Estado</label>
                            <select class="form-select" id="estadoCategoria" name="estado" required>
                                <option value="1" selected>Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Categoría</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Categoría -->
    <div class="modal fade" id="editarCategoriaModal" tabindex="-1" aria-labelledby="editarCategoriaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarCategoriaModalLabel">Editar Categoría</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo APP_URL; ?>/categorias/categorias_update" method="post">
                    <input type="hidden" id="idCategoriaEditar" name="id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nombreCategoriaEditar" class="form-label">Nombre de la categoría</label>
                            <input type="text" class="form-control" id="nombreCategoriaEditar" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="descripcionCategoriaEditar" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcionCategoriaEditar" name="descripcion" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="estadoCategoriaEditar" class="form-label">Estado</label>
                            <select class="form-select" id="estadoCategoriaEditar" name="estado" required>
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Actualizar Categoría</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Eliminar Categoría -->
    <div class="modal fade" id="eliminarCategoriaModal" tabindex="-1" aria-labelledby="eliminarCategoriaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eliminarCategoriaModalLabel">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo APP_URL; ?>/categorias/categorias_delete" method="post">
                    <input type="hidden" id="idCategoriaEliminar" name="id">
                    <input type="hidden" id="nombreCategoriaEliminar" name="nombre">
                    <input type="hidden" id="descripcionCategoriaEliminar" name="descripcion">
                    <input type="hidden" id="estadoCategoriaEliminar" name="estado">
                    <div class="modal-body">
                        <p>¿Estás seguro que deseas eliminar esta categoría? Esta acción no se puede deshacer.</p>
                        <p class="fw-bold" id="nombreCategoriaEliminar"></p>
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
            $('#editarCategoriaModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                var nombre = button.data('nombre');
                var descripcion = button.data('descripcion');
                var estado = button.data('estado');
                
                var modal = $(this);
                modal.find('#idCategoriaEditar').val(id);
                modal.find('#nombreCategoriaEditar').val(nombre);
                modal.find('#descripcionCategoriaEditar').val(descripcion);
                modal.find('#estadoCategoriaEditar').val(estado);
            });
            
            // Modal de eliminación
            $('#eliminarCategoriaModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                var nombre = button.data('nombre');
                var descripcion = button.data('descripcion');
                var estado = button.data('estado');
                
                var modal = $(this);
                modal.find('#idCategoriaEliminar').val(id);
                modal.find('#nombreCategoriaEliminar').text(nombre);
                modal.find('#descripcionCategoriaEliminar').text(descripcion);
                modal.find('#estadoCategoriaEliminar').text(estado);
            });
            
            // Búsqueda simple
            $('#btnBuscar').click(function() {
                var searchText = $('#buscarCategoria').val().toLowerCase();
                $('table tbody tr').each(function() {
                    var rowText = $(this).text().toLowerCase();
                    $(this).toggle(rowText.indexOf(searchText) > -1);
                });
            });
            
            $('#buscarCategoria').keyup(function(e) {
                if (e.keyCode === 13) {
                    $('#btnBuscar').click();
                }
            });
        });
    </script>
</body>
</html>