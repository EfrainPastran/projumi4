<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <title>Lista de usuarios</title>
    <?php include 'views/componentes/head.php'; ?>
</head>
<body>
<h2>Lista de Usuarios</h2>

<a href="<?php echo url('auth/register'); ?>" class="btn">Registrarse</a>

<a href="<?php echo url('home/index'); ?>" class="btn">Volver al inicio</a>

    <table class="table" border="1">
        <thead>
            <tr>
            <th>ID</th>
                <th>Cedula</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Fecha Registro</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php if (isset($usuarios)): ?>  
            <?php foreach($usuarios as $usuario): ?>


                <tr>
                <td><?php echo e($usuario['id_usuario']); ?></td>
                <td><?php echo e($usuario['cedula']); ?></td>
                <td><?php echo e($usuario['nombre']); ?></td>
                <td><?php echo e($usuario['email']); ?></td>
                <td><?php echo e($usuario['fecha']); ?></td>
                    <td>
                        <!-- Botón Editar con modal -->
                        <button type="button" class="btn btn-primary edit-btn" 
                                data-toggle="modal" data-target="#editModal"
                                data-id_usuario="<?= e($usuario['id_usuario']) ?>"
                                data-cedula="<?= e($usuario['cedula']) ?>"
                                data-nombre="<?= e($usuario['nombre']) ?>"
                                data-email="<?= e($usuario['email']) ?>"
                                data-pass="<?= e($usuario['pass']) ?>">
                                
                            Editar
                        </button>
                        
                        <!-- Botón Eliminar con modal -->
                        <button type="button" class="btn btn-danger delete-btn" 
                                data-toggle="modal" data-target="#deleteModal"
                                data-id_usuario="<?= e($usuario['id_usuario']) ?>"
                                data-nombre="<?= e($usuario['nombre']) ?>">
                            Eliminar
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Modal para Editar -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Editar Usuario</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="<?php echo url('auth/actualizar'); ?>" method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="editId" name="id_usuario">
                        <input type="hidden" id="editPass" name="pass">
                        <div class="form-group">
                            <label for="editCedula">Cedula</label>
                            <input type="number" class="form-control" id="editCedula" name="cedula" required>
                        </div>
                        <div class="form-group">
                            <label for="editNombre">Nombre</label>
                            <input type="text" class="form-control" id="editNombre" name="nombre" required>
                        </div>
                        <div class="form-group">
                            <label for="editEmail">Email</label>
                            <input type="email" class="form-control" id="editEmail" name="email" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <input type="submit" class="btn btn-primary" value="actualizar" name="actualizar" >
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Eliminar -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Eliminar Usuario</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="<?php echo url('auth/eliminar'); ?>" method="POST">
                    <div class="modal-body">
                        <input type="text" id="deleteId" name="id_usuario">
                        <input type="text" id="deleteNombre" name="nombre">
                        <p>¿Estás seguro que deseas eliminar este usuario?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <input type="submit" class="btn btn-danger" value="eliminar" name="eliminar">
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- JavaScript para manejar los modales -->
    <script>
    $(document).ready(function() {
        // Configurar modal de edición
        $('.edit-btn').click(function() {
            const id_usuario = $(this).data('id_usuario');
            const pass = $(this).data('pass');
            const cedula = $(this).data('cedula');
            const nombre = $(this).data('nombre');
            const email = $(this).data('email');
           
            
            $('#editId').val(id_usuario);
            $('#editPass').val(pass);
            $('#editCedula').val(cedula);
            $('#editNombre').val(nombre);
            $('#editEmail').val(email);
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


</body>
</html>

