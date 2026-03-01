<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PROJUMI - Módulos del Sistema</title>
    <?php include 'views/componentes/head.php'; ?>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
      <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/config.css">
</head>
<body>
    <div class="config-container">
        <div class="config-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="fas fa-puzzle-piece"></i> Módulos del Sistema</h1>
                    <p class="subtitle">Administra los módulos disponibles en PROJUMI</p>
                </div>
                <a href="config-main.html" class="btn btn-back">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>

        <div class="config-content">
            <div class="config-actions mb-4">
                <button class="btn btn-add" data-bs-toggle="modal" data-bs-target="#addModuleModal">
                    <i class="fas fa-plus-circle"></i> Nuevo Módulo
                </button>
            </div>

            <div class="table-responsive">
                <table class="table config-table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Cedula</th>
                            <th>Rol</th>
                            <th>Modulos</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                           <?php if (isset($re)): ?>  
            <?php foreach($re as $user): ?>
                        <tr>
                            <input type="hidden" name="id_usuario" value="<?php echo e($user['id_usuario']); ?>">
                            <td><?php echo e($user['nombre']); ?></td>
                            <td><?php echo e($user['apellido']); ?></td>
                            <td><?php echo e($user['cedula']); ?></td>
                            <td><span class="badge bg-success"><?php 
                                        if ($user['rol_id'] === 1) {
                                            echo 'Administrador';
                                        } 
                                        if ($user['rol_id'] === 2) {
                                            echo 'Usuario';
                                        } 
                                        if ($user['rol_id'] === 3) {
                                            echo 'SuperUsuario';
                                        }     ?></span></td>
                            <td>
                            <td>
                                <button class="btn btn-sm edit-btn" data-bs-toggle="modal" data-bs-target="#addModuloModal"
                                data-id_usuario="<?= e($user['id_usuario']) ?>"
                                data-nombre="<?= e($user['nombre']) ?>"
                                data-apellido="<?= e($user['apellido']) ?>"
                                data-cedula="<?= e($user['cedula']) ?>"
                                data-rol_id="<?= e($user['rol_id']) ?>"> 
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

   <!-- Modal Agregar Módulo -->
<div class="modal fade" id="addModuloModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus-circle"></i> Asignar Módulos a: <span id="userName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="<?php echo APP_URL; ?>/modulos/asignar" method="post">
                    <input type="hidden" name="usuario_id" id="editId">
                    <div class="mb-3">
                        <label class="form-label">Usuario:</label>
                        <input type="text" class="form-control" id="editNom" style="pointer-events: none; background-color: #f0f0f0;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Módulos disponibles:</label>
                        <div id="modulesCheckboxContainer">
                            <!-- Los checkboxes se generarán aquí dinámicamente -->
                            <?php foreach($mod as $module): ?>
                                <div class="form-check">
                                     <input class="form-check-input" type="checkbox" name="modulos[]" 
                                        id="modulo_<?= $module['id'] ?>" value="<?= $module['id'] ?>">
                                    <label class="form-check-label" for="modulo_<?= $module['id'] ?>">
                                        <?= $module['nombre'] ?> - <?= $module['descripcion'] ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="saveModulesBtn">Guardar Módulos</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
     <script>
    $(document).ready(function() {
        // Configurar modal de edición
        $('.edit-btn').click(function() {
            const id_usuario = $(this).data('id_usuario');
            const nombre = $(this).data('nombre');
            const apellido = $(this).data('apellido');
            const cedula = $(this).data('cedula');
           
            
            $('#editId').val(id_usuario);
            $('#editNom').val(nombre);
            $('#editApe').val(apellido);
            $('#editCed').val(cedula);
        });
        
        // Configurar modal de eliminación
        $('.delete-btn').click(function() {
            const id_usuario = $(this).data('id_usuario');
            const nombre = $(this).data('nombre');
            $('#deleteId').val(id_usuario);
            $('#deleteNombre').val(nombre);
        });
    });
    </script>

    <?php else: ?>
    <p>No se encontraron usuarios registrados.</p>
<?php endif; ?>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>