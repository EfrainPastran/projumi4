<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PROJUMI - Gestión de Permisos</title>
    <?php include 'views/componentes/head.php'; ?>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/config.css">
</head>
<body>
    <div class="config-container">
        <div class="config-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="fas fa-key"></i> Permisos del Sistema</h1>
                    <p class="subtitle">Administra los permisos por rol y módulo</p>
                </div>
                <a href="config-main.html" class="btn btn-back">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>

        <div class="config-content">
            <div class="config-actions mb-4">
                <button class="btn btn-add" data-bs-toggle="modal" data-bs-target="#editPermissionsModal">
                    <i class="fas fa-plus-circle"></i> Nuevos Permisos
                </button>
            </div>

        <div class="config-content">
            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="roleSelect" class="form-label">Seleccionar Rol</label>
                    <select class="form-select" id="roleSelect">
                        <option value="">Todos los roles</option>
                        <option value="admin">Administrador</option>
                        <option value="coord">Coordinador</option>
                        <option value="volunt">Voluntario</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="moduleSelect" class="form-label">Seleccionar Módulo</label>
                    <select class="form-select" id="moduleSelect">
                        <option value="">Todos los módulos</option>
                        <option value="dashboard">Dashboard</option>
                        <option value="users">Usuarios</option>
                        <option value="projects">Proyectos</option>
                        <option value="reports">Reportes</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table config-table">
                    <thead>
                        <tr>
                            <th>Módulo</th>
                            <th>Rol</th>
                            <th>Ver</th>
                            <th>Crear</th>
                            <th>Editar</th>
                            <th>Eliminar</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Dashboard</td>
                            <td>Administrador</td>
                            <td><i class="fas fa-check text-success"></i></td>
                            <td><i class="fas fa-check text-success"></i></td>
                            <td><i class="fas fa-check text-success"></i></td>
                            <td><i class="fas fa-check text-success"></i></td>
                            <td>
                                <button class="btn btn-sm btn-edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>Usuarios</td>
                            <td>Administrador</td>
                            <td><i class="fas fa-check text-success"></i></td>
                            <td><i class="fas fa-check text-success"></i></td>
                            <td><i class="fas fa-check text-success"></i></td>
                            <td><i class="fas fa-check text-success"></i></td>
                            <td>
                                <button class="btn btn-sm btn-edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>Proyectos</td>
                            <td>Coordinador</td>
                            <td><i class="fas fa-check text-success"></i></td>
                            <td><i class="fas fa-check text-success"></i></td>
                            <td><i class="fas fa-check text-success"></i></td>
                            <td><i class="fas fa-times text-danger"></i></td>
                            <td>
                                <button class="btn btn-sm btn-edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>Reportes</td>
                            <td>Voluntario</td>
                            <td><i class="fas fa-check text-success"></i></td>
                            <td><i class="fas fa-times text-danger"></i></td>
                            <td><i class="fas fa-times text-danger"></i></td>
                            <td><i class="fas fa-times text-danger"></i></td>
                            <td>
                                <button class="btn btn-sm btn-edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Editar Permisos -->
    <div class="modal fade" id="editPermissionsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-key"></i> Editar Permisos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editPermissionsForm">
                        <div class="mb-3">
                            <label class="form-label">Módulo: <strong>Usuarios</strong></label>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Rol: <strong>Coordinador</strong></label>
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="viewPermission" checked>
                                <label class="form-check-label" for="viewPermission">Ver</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="createPermission" checked>
                                <label class="form-check-label" for="createPermission">Crear</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="editPermission">
                                <label class="form-check-label" for="editPermission">Editar</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="deletePermission">
                                <label class="form-check-label" for="deletePermission">Eliminar</label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary">Guardar Permisos</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>