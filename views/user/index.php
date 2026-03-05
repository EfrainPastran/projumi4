<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PROJUMI - Usuario</title>
    <?php include 'views/componentes/head.php'; ?>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header">
                        <h2 class="mb-0">Gestión de Usuarios - PROJUMI</h2>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-4">
                            <?php if (isset($permisos['registrar']) && $permisos['registrar'] === true): ?>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#agregarUsuarioModal">
                                    <i class="fas fa-user-plus me-2"></i>Nuevo Usuario
                                </button>
                            <?php endif; ?>
                            <div class="input-group buscar-input">
                                <input type="text" class="form-control" placeholder="Buscar usuario..." id="buscarUsuario">
                                <?php if (isset($permisos['consultar'])): ?>
                                    <button class="btn btn-outline-secondary" type="button" id="btnBuscar">
                                        <i class="fas fa-search"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="table-container">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Cédula</th>
                                            <th>Nombre</th>
                                            <th>Correo</th>
                                            <th>Teléfono</th>
                                            <th>Rol</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data as $usuario): ?>
                                            <tr>
                                                <td><?php echo $usuario['id_usuario']; ?></td>
                                                <td><?php echo htmlspecialchars($usuario['cedula']); ?></td>
                                                <td><?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?></td>
                                                <td><?php echo htmlspecialchars($usuario['correo']); ?></td>
                                                <td><?php echo htmlspecialchars($usuario['telefono']); ?></td>
                                                <td><?php echo htmlspecialchars($usuario['rol_nombre'] ?? 'Sin rol'); ?></td>
                                                <td>
                                                    <?php 
                                                    $status = $usuario['estatus'] == 1 ? 'Activo' : 'Inactivo'; 
                                                    $badgeClass = $usuario['estatus'] == 1 ? 'bg-success' : 'bg-danger';
                                                    ?>
                                                    <span class="badge <?php echo $badgeClass; ?>">
                                                        <?php echo $status; ?>
                                                    </span>
                                                </td>
                                                <td class="acciones-btn">
                                                    <?php if (isset($permisos['actualizar']) && $permisos['actualizar'] === true): ?>
                                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editarUsuarioModal" 
                                                            data-id="<?php echo $usuario['id_usuario']; ?>"
                                                            data-cedula="<?php echo htmlspecialchars($usuario['cedula']); ?>"
                                                            data-nombre="<?php echo htmlspecialchars($usuario['nombre']); ?>"
                                                            data-apellido="<?php echo htmlspecialchars($usuario['apellido']); ?>"
                                                            data-correo="<?php echo htmlspecialchars($usuario['correo']); ?>"
                                                            data-direccion="<?php echo htmlspecialchars($usuario['direccion']); ?>"
                                                            data-telefono="<?php echo htmlspecialchars($usuario['telefono']); ?>"
                                                            data-fecha_nacimiento="<?php echo htmlspecialchars($usuario['fecha_nacimiento']); ?>"
                                                            data-estatus="<?php echo $usuario['estatus']; ?>"
                                                            data-fk_rol="<?php echo $usuario['fk_rol']; ?>">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                    <?php if (isset($permisos['eliminar']) && $permisos['eliminar'] === true): ?>
                                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#eliminarUsuarioModal"
                                                            data-id="<?php echo $usuario['id_usuario']; ?>"
                                                            data-nombre="<?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?>">
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

    <!-- Modal Agregar Usuario -->
    <div class="modal fade" id="agregarUsuarioModal" tabindex="-1" aria-labelledby="agregarUsuarioModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="agregarUsuarioModalLabel">Agregar Nuevo Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" id="formAgregarUsuario">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="cedula" class="form-label">Cédula</label>
                                    <input type="number" class="form-control" id="cedula" name="cedula" required data-tipo="numeros" data-min="7" data-max="8">                                    
                                </div>
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" required data-tipo="letras" data-min="5" data-max="45">                                    
                                </div>
                                <div class="mb-3">
                                    <label for="apellido" class="form-label">Apellido</label>
                                    <input type="text" class="form-control" id="apellido" name="apellido" required data-tipo="letras" data-min="5" data-max="45">                                    
                                </div>
                                <div class="mb-3">
                                    <label for="correo" class="form-label">Correo Electrónico</label>
                                    <input type="email" class="form-control" id="correo" name="correo" required data-min="5" data-max="45">                                    
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Contraseña</label>
                                    <input type="password" class="form-control" id="password" name="password" required>                                    
                                </div>
                                <div class="mb-3">
                                    <label for="direccion" class="form-label">Dirección</label>
                                    <input type="text" class="form-control" id="direccion" name="direccion" required data-tipo="direccion" data-min="10" data-max="100">                                    
                                </div>
                                <div class="mb-3">
                                    <label for="telefono" class="form-label">Teléfono</label>
                                    <input type="text" class="form-control" id="telefono" name="telefono" required data-tipo="numeros" data-min="11" data-max="11">                                    
                                </div>
                                <div class="mb-3">
                                    <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                                    <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required>                                    
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="estatus" class="form-label">Estado</label>
                                    <select class="form-select" id="estatus" name="estatus" required>
                                        <option value="1" selected>Activo</option>
                                        <option value="0">Inactivo</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="fk_rol" class="form-label">Rol</label>
                                    <select class="form-select" id="fk_rol" name="fk_rol" required>
                                        <?php foreach ($roles as $rol): ?>
                                            <option value="<?php echo $rol['id_rol']; ?>"><?php echo htmlspecialchars($rol['nombre']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Usuario</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Usuario -->
    <div class="modal fade" id="editarUsuarioModal" tabindex="-1" aria-labelledby="editarUsuarioModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarUsuarioModalLabel">Editar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editarUsuarioForm">
                    <input type="hidden" id="idUsuarioEditar" name="id_usuario">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="cedulaEditar" class="form-label">Cédula</label>
                                    <input type="number" class="form-control" id="cedulaEditar" name="cedula" disabled required data-tipo="numeros" data-min="7" data-max="8">
                                </div>
                                <div class="mb-3">
                                    <label for="nombreEditar" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="nombreEditar" name="nombre" required data-tipo="letras" data-min="5" data-max="45">
                                </div>
                                <div class="mb-3">
                                    <label for="apellidoEditar" class="form-label">Apellido</label>
                                    <input type="text" class="form-control" id="apellidoEditar" name="apellido" required data-tipo="letras" data-min="5" data-max="45">
                                </div>
                                <div class="mb-3">
                                    <label for="correoEditar" class="form-label">Correo Electrónico</label>
                                    <input type="email" class="form-control" id="correoEditar" name="correo" required data-min="5" data-max="45">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="direccionEditar" class="form-label">Dirección</label>
                                    <textarea name="direccion" id="direccionEditar" class="form-control" rows="3" required data-min="10" data-max="100"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="telefonoEditar" class="form-label">Teléfono</label>
                                    <input type="text" class="form-control" id="telefonoEditar" name="telefono" required data-tipo="numeros" data-min="11" data-max="11">
                                </div>
                                <div class="mb-3">
                                    <label for="fecha_nacimientoEditar" class="form-label">Fecha de Nacimiento</label>
                                    <input type="date" class="form-control" id="fecha_nacimientoEditar" name="fecha_nacimiento" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="estatusEditar" class="form-label">Estado</label>
                                    <select class="form-select" id="estatusEditar" name="estatus" required>
                                        <option value="1">Activo</option>
                                        <option value="0">Inactivo</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="fk_rolEditar" class="form-label">Rol</label>
                                    <select class="form-select" id="fk_rolEditar" name="fk_rol" required>
                                        <?php foreach ($roles as $rol): ?>
                                            <option value="<?php echo $rol['id_rol']; ?>"><?php echo htmlspecialchars($rol['nombre']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Eliminar Usuario -->
    <div class="modal fade" id="eliminarUsuarioModal" tabindex="-1" aria-labelledby="eliminarUsuarioModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eliminarUsuarioModalLabel">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="eliminarUsuarioForm">
                    <input type="hidden" id="idUsuarioEliminar" name="id_usuario">
                    <div class="modal-body">
                        <p>¿Estás seguro que deseas eliminar al usuario <span id="nombreUsuarioEliminar" class="fw-bold"></span>?</p>
                        <p class="text-danger">Esta acción no se puede deshacer y eliminará todos los datos asociados al usuario.</p>
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
    <script src="<?php echo APP_URL; ?>/public/js/usuario.js" type="module"></script>
    <script src="<?php echo APP_URL; ?>/public/js/validaciones.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof Validaciones !== 'undefined') {
                Validaciones.init('#formAgregarUsuario');
                Validaciones.init('#editarUsuarioForm');
                Validaciones.limitarCalendario('#fecha_nacimiento');
            }
        });
    </script> 
</body>
</html>