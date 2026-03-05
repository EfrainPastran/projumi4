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
if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
include "views/navbar.php"; 
?>
<br><br>
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

    <!-- Tabla de roles -->
    <div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header">
                <h2 class="mb-0"><i class="fas fa-user-shield me-2"></i>Gestión de Roles</h2>
            </div>

            <div class="card-body">

                <div class="d-flex justify-content-between mb-4">
                    <p class="text-muted mb-0">Gestiona los roles y sus permisos en el sistema</p>
                    <?php if (isset($permisos['registrar']) && $permisos['registrar'] === true): ?>
                    <button class="btn btn-primary" id="addRoleBtn" data-bs-toggle="modal" data-bs-target="#registerRoleModal">
                        <i class="fas fa-plus me-2"></i>Nuevo Rol
                    </button>
                    <?php endif; ?>
                </div>

                <div class="table-container">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Nombre del Rol</th>
                                    <th>Descripción</th>
                                    <th>Editar</th>
                                    <th>Eliminar</th>
                                    <th>Configurar Permisos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($roles as $rol): ?>
                                <tr>
                                    <td class="fw-semibold"><?php echo htmlspecialchars($rol['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($rol['descripcion_rol']); ?></td>

                                    <td>
                                        <?php if (isset($permisos['actualizar']) && $permisos['actualizar'] === true): ?>
                                        <button class="btn btn-sm btn-warning edit-role-btn"
                                            data-bs-toggle="modal" data-bs-target="#editRoleModal"
                                            data-id="<?php echo $rol['id_rol']; ?>"
                                            data-nombre="<?php echo htmlspecialchars($rol['nombre']); ?>"
                                            data-descripcion="<?php echo htmlspecialchars($rol['descripcion_rol']); ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?php if ($rol['estatus']): ?>
                                            <?php if (isset($permisos['eliminar']) && $permisos['eliminar'] === true): ?>
                                            <button class="btn btn-sm btn-danger deactivate-role-btn"
                                                data-bs-toggle="modal" data-bs-target="#deactivateRoleModal"
                                                data-id="<?php echo $rol['id_rol']; ?>"
                                                data-nombre="<?php echo htmlspecialchars($rol['nombre']); ?>"
                                                data-descripcion="<?php echo htmlspecialchars($rol['descripcion_rol']); ?>">
                                                <i class="fas fa-user-slash"></i>
                                            </button>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Inactivo</span>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary config-role-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#configRoleModal"
                                            data-id="<?php echo $rol['id_rol']; ?>">
                                            Editar permisos
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

<!-- Modal Registrar Rol -->
<div class="modal fade" id="registerRoleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="registerRoleForm" action="<?php echo APP_URL; ?>/roles/register" method="post">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus"></i> Registrar Nuevo Rol</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="newRoleName" class="form-label">Nombre del Rol</label>
                        <input type="text" class="form-control" id="newRoleName" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="newRoleDescripcion" class="form-label">Descripción del Rol</label>
                        <textarea class="form-control" id="newRoleDescripcion" name="descripcion_rol" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="newRoleEstatus" class="form-label">Estatus del Rol</label>
                        <select class="form-select" id="newRoleEstatus" name="estatus" required>
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Registrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Rol -->
<div class="modal fade" id="editRoleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editRoleForm" action="<?php echo APP_URL; ?>/roles/update" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Editar Rol</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <input type="hidden" name="id_rol" id="editRoleId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editRoleName" class="form-label">Nombre del Rol</label>
                        <input type="text" class="form-control" id="editRoleName" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="editRoleDescription" class="form-label">Descripción del Rol</label>
                        <textarea class="form-control" id="editRoleDescription" name="descripcion_rol" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="editRoleStatus" class="form-label">Estatus del Rol</label>
                        <select class="form-select" id="editRoleStatus" name="estatus">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Desactivar Rol -->
<div class="modal fade" id="deactivateRoleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="deactivateRoleForm" action="<?php echo APP_URL; ?>/roles/delete" method="POST">
                <input type="hidden" id="deactivateRoleId" name="id_rol">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-user-slash"></i> Desactivar Rol</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="deactivateRoleId" name="id">
                    <p>¿Estás seguro que deseas desactivar el rol <strong id="deactivateRoleName"></strong>?</p>
                    <p class="text-danger">Esta acción puede revertirse desde la administración de roles.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Desactivar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Configurar Permisos del Rol -->
<div class="modal fade" id="configRoleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="rolePermissionsForm" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-cogs"></i> Configurar Permisos del Rol</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="fk_rol" id="permisoRoleId">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th>Módulo</th>
                                    <th>Consultar</th>
                                    <th>Registrar</th>
                                    <th>Actualizar</th>
                                    <th>Eliminar</th>
                                </tr>
                            </thead>
                            <tbody>
                            

                            </tbody>

                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const todosLosModulos = <?php echo json_encode($modulos); ?>;
    const todosLosPermisos = ['consultar', 'registrar', 'actualizar', 'eliminar'];
</script>
<script>
function cargarPermisosDelRol(fk_rol) {
    document.getElementById('permisoRoleId').value = fk_rol;

    fetch(`<?php echo APP_URL; ?>/permisos/obtenerPermisosPorRol?fk_rol=${fk_rol}`)
        .then(res => res.json())
        .then(data => {
            const permisosRol = data.success ? data.data : [];

            // Agrupar permisos por módulo
            const permisosMap = {};
            permisosRol.forEach(p => {
                if (!permisosMap[p.modulo_id]) permisosMap[p.modulo_id] = {};
                permisosMap[p.modulo_id][p.permiso] = true;
            });

            // Renderizar tabla de permisos
            const tbody = document.querySelector('#configRoleModal tbody');
            tbody.innerHTML = '';

            todosLosModulos.forEach(modulo => {
                const fila = document.createElement('tr');
                const celdaModulo = document.createElement('td');
                celdaModulo.textContent = modulo.nombre;
                fila.appendChild(celdaModulo);

                todosLosPermisos.forEach(permiso => {
                    const celda = document.createElement('td');
                    const check = document.createElement('input');
                    check.type = 'checkbox';
                    check.name = `permisos[${modulo.id_modulo}][${permiso}]`;
                    check.value = 1;
                    check.checked = permisosMap[modulo.id_modulo]?.[permiso] || false;
                    celda.classList.add('text-center');

                    check.classList.add('form-check-input');

                    celda.appendChild(check);
                    fila.appendChild(celda);
                });

                tbody.appendChild(fila);
            });
        })
        .catch(error => {
            console.error('Error al cargar permisos:', error);
        });
}
</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.config-role-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const rolId = this.getAttribute('data-id');
            cargarPermisosDelRol(rolId);
        });
    });
});
</script>
<script>
document.getElementById('rolePermissionsForm').addEventListener('submit', function (e) {
    e.preventDefault(); // Previene el submit clásico

    const formData = new FormData(this);

    fetch('<?php echo APP_URL; ?>/permisos/guardarPermisosPorRol', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Permisos guardados',
                text: 'Los permisos del rol han sido actualizados correctamente.'
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudieron guardar los permisos.',
            });
        }
    })
    .catch(err => {
        console.error('Error en el envío de permisos:', err);
        alert('Hubo un error al enviar los datos.');
    });
});
</script>







</body>
</html>