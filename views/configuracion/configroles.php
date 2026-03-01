<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PROJUMI - Gestión de Roles</title>
    <?php include 'views/componentes/head.php'; ?>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/config.css">
</head>
<?php
// Asegúrate de iniciar la sesión también en la vista
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<body>




    <div class="config-container">
        <div class="config-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="fas fa-user-tag"></i> Roles y Usuarios</h1>
                    <p class="subtitle">Administra los roles y asigna usuarios</p>
                </div>
                <a href="<?php echo APP_URL; ?>/configuracion/index" class="btn btn-back">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>

        <div class="config-content">
            <div class="config-actions mb-4">
                <button class="btn btn-add" data-bs-toggle="modal" data-bs-target="#addRoleModal">
                    <i class="fas fa-plus-circle"></i> Nuevo Rol
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
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                         <?php if (isset($result)): ?>  
            <?php foreach($result as $user): ?>
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
                                <button class="btn btn-sm edit-btn" data-bs-toggle="modal" data-bs-target="#addRoleModal"
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

    <!-- Modal Agregar Rol -->
    <div class="modal fade" id="addRoleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">

                                    
                    <h5 class="modal-title"><i class="fas fa-plus-circle"></i> Asignar Nuevo Rol</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                   <form action="<?php echo APP_URL; ?>/roles/actualizar" method="post">
                         <input type="hidden" name="id_usuario" id="editId">   
                        <div class="mb-3">
                            <input type="text" class="form-control" id="editNom" style="pointer-events: none; background-color: #f0f0f0;">
                        </div>
                        <div class="mb-3">
                             <input type="text" class="form-control" id="editApe" style="pointer-events: none; background-color: #f0f0f0;">
                        </div>
                        <div class="mb-3">
                             <input type="text" class="form-control" id="editCed" style="pointer-events: none; background-color: #f0f0f0;">
                        </div>
                        <div class="mb-3">
                            <label for="roleStatus" class="form-label">Rol a asignar</label>
                            <select class="form-select" id="roleStatus" name="rol_id" required>
                                <option value="1">Administrador</option>
                                <option value="2">Usuario</option>
                                <option value="3">SuperUsuario</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Rol</button>
                </div>
                    </form>
                </div>
                
            </div>
        </div>
    </div>

    <!-- Modal Asignar Usuarios -->
    <div class="modal fade" id="assignUsersModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-users"></i> Asignar Usuarios al Rol</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <input type="text" class="form-control" placeholder="Buscar usuarios...">
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Usuario</th>
                                    <th>Correo Electrónico</th>
                                    <th>Rol Actual</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input type="checkbox" class="form-check-input"></td>
                                    <td>Juan Pérez</td>
                                    <td>juan@projumi.org</td>
                                    <td>Administrador</td>
                                </tr>
                                <tr>
                                    <td><input type="checkbox" class="form-check-input"></td>
                                    <td>María Gómez</td>
                                    <td>maria@projumi.org</td>
                                    <td>Coordinador</td>
                                </tr>
                                <tr>
                                    <td><input type="checkbox" class="form-check-input"></td>
                                    <td>Carlos Rodríguez</td>
                                    <td>carlos@projumi.org</td>
                                    <td>Voluntario</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary">Guardar Asignaciones</button>
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