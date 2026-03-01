<?php if (!isset($_SESSION['user']['cedula'])): ?>
    <script>window.location.href = '<?= APP_URL ?>home/index';</script>
    <?php exit(); ?>
<?php endif; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PROJUMI - Administración de Usuarios</title>
    <?php include 'views/componentes/head.php'; ?>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo APP_URL; ?>public/css/admin.css">

</head>

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
<br>
<body>
    <div class="admin-container">
        <div class="card admin-card">
            <div class="card-header admin-header">
                <h2><i class="fas fa-users-cog"></i> Administración de Usuarios</h2>
                <button class="btn btn-add-user" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="fas fa-user-plus"></i> Agregar Usuario
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="usersTable" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Foto</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Agregar Usuario -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel"><i class="fas fa-user-plus"></i> Agregar Nuevo Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addUserForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="addFirstName" class="form-label">Nombres</label>
                                    <input type="text" class="form-control" id="addFirstName" required>
                                </div>
                                <div class="mb-3">
                                    <label for="addLastName" class="form-label">Apellidos</label>
                                    <input type="text" class="form-control" id="addLastName" required>
                                </div>
                                <div class="mb-3">
                                    <label for="addEmail" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="addEmail" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="addRole" class="form-label">Rol</label>
                                    <select class="form-select" id="addRole" required>
                                        <option value="">Seleccionar...</option>
                                        <option value="Administrador">Administrador</option>
                                        <option value="Coordinador">Coordinador</option>
                                        <option value="Voluntario">Voluntario</option>
                                        <option value="Beneficiario">Beneficiario</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="addPassword" class="form-label">Contraseña</label>
                                    <input type="password" class="form-control" id="addPassword" required>
                                </div>
                                <div class="mb-3">
                                    <label for="addStatus" class="form-label">Estado</label>
                                    <select class="form-select" id="addStatus" required>
                                        <option value="Activo">Activo</option>
                                        <option value="Inactivo">Inactivo</option>
                                        <option value="Pendiente">Pendiente</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-save" id="saveNewUser">Guardar Usuario</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Editar Usuario -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel"><i class="fas fa-user-edit"></i> Editar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editUserForm">
                        <input type="hidden" id="editUserId">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editFirstName" class="form-label">Nombres</label>
                                    <input type="text" class="form-control" id="editFirstName" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editLastName" class="form-label">Apellidos</label>
                                    <input type="text" class="form-control" id="editLastName" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editEmail" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="editEmail" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editRole" class="form-label">Rol</label>
                                    <select class="form-select" id="editRole" required>
                                        <option value="Administrador">Administrador</option>
                                        <option value="Coordinador">Coordinador</option>
                                        <option value="Voluntario">Voluntario</option>
                                        <option value="Beneficiario">Beneficiario</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="editStatus" class="form-label">Estado</label>
                                    <select class="form-select" id="editStatus" required>
                                        <option value="Activo">Activo</option>
                                        <option value="Inactivo">Inactivo</option>
                                        <option value="Pendiente">Pendiente</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Cambiar Contraseña (opcional)</label>
                                    <input type="password" class="form-control" id="editPassword" placeholder="Dejar vacío para mantener la actual">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-save" id="saveUserChanges">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Confirmar Eliminación -->
    <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteUserModalLabel"><i class="fas fa-exclamation-triangle"></i> Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro que deseas eliminar al usuario <strong id="userToDeleteName"></strong>?</p>
                    <p class="text-danger">Esta acción no se puede deshacer.</p>
                    <input type="hidden" id="userToDeleteId">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteUser">Eliminar Usuario</button>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <!-- Custom JS -->

    <script src="<?php echo APP_URL; ?>public/js/admin.js" type="module"></script>

</body>
</html>