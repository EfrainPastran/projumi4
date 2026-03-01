<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PROJUMI - Emprendedor</title>
    <?php include 'views/componentes/head.php'; ?>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/roles.css">
    <!-- Select2 CSS -->
    
    <style>
        .form-step {
            transition: all 0.3s ease;
        }
        .progress {
            height: 20px;
            margin-bottom: 20px;
        }
        .progress-bar {
            transition: width 0.3s ease;
        }
        .d-none {
            display: none !important;
        }
    </style>
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
    <div class="container mt-5">

        <?php if (!empty($_SESSION['mensaje'])): ?>
    <div class="alert alert-<?= $_SESSION['mensaje']['tipo'] ?>">
        <?= $_SESSION['mensaje']['texto'] ?>
    </div>
    <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

        <?php if (isset($_SESSION['flash_success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['flash_error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?></div>
        <?php endif; ?>
<div class="container py-5">
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header">
                        <h2 class="mb-0">Gestión de Emprendedores - PROJUMI</h2>
                    </div>
                    <div class="card-body">
                        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalAgregar">
            <i class="fas fa-plus"></i> Nuevo Emprendedor
        </button>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Cedula</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Emprendimiento</th>
                    <th>Aporte Projumi</th>
                    <th>Estado</th>
                    <th>Detalles</th>
                    <th></th>
                    <th>Acciones</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data)) : ?>
                    <?php foreach ($data as $emprendedor) : ?>
                        <tr>
                            <td><?= htmlspecialchars($emprendedor['cedula']) ?></td>
                            <td><?= htmlspecialchars($emprendedor['nombre']) ?></td>
                            <td><?= htmlspecialchars($emprendedor['apellido']) ?></td>
                            <td><?= htmlspecialchars($emprendedor['emprendimiento']) ?></td>
                            <td><?= htmlspecialchars($emprendedor['aporte_projumi']) ?></td>
                            <td>
                                <?php if ($emprendedor['estatus'] == 1): ?>
                                    <span class="badge bg-success">Activo</span>
                                <?php elseif($emprendedor['estatus'] == 0): ?>
                                    <span class="badge bg-warning">En proceso</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn bg-info btn-sm btnEditar"
                                    data-bs-toggle="modal" data-bs-target="#modalEditar">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                            <td>
                                <button class="btn btn-warning btn-sm btnEditar"
                                    data-id="<?= htmlspecialchars($emprendedor['id_emprededor']) ?>"
                                    data-cedula="<?= htmlspecialchars($emprendedor['cedula']) ?>"
                                    data-nombre="<?= htmlspecialchars($emprendedor['nombre']) ?>"
                                    data-apellido="<?= htmlspecialchars($emprendedor['apellido']) ?>"
                                    data-emprendimiento="<?= htmlspecialchars($emprendedor['emprendimiento']) ?>"
                                    data-estatus="<?= $emprendedor['estatus'] ?>"
                                    data-aporte="<?= htmlspecialchars($emprendedor['aporte_projumi']) ?>"
                                    data-bs-toggle="modal" data-bs-target="#modalEditar">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                            <td>
                                <button class="btn btn-danger btn-sm btnEliminar"
                                    data-id="<?= htmlspecialchars($emprendedor['id_emprededor']) ?>"
                                    data-cedula="<?= htmlspecialchars($emprendedor['cedula']) ?>"
                                    data-nombre="<?= htmlspecialchars($emprendedor['nombre']) . ' ' . htmlspecialchars($emprendedor['apellido']) ?>"
                                    data-bs-toggle="modal" data-bs-target="#modalEliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                            <td>
                                <?php if ($emprendedor['estatus'] == 0 || $emprendedor['estatus'] == 2): ?>
                                    <button class="btn btn-success btn-sm btnAprobar"
                                        data-id="<?= htmlspecialchars($emprendedor['id_emprededor']) ?>"
                                        data-bs-toggle="modal" data-bs-target="#modalAprobar">
                                        <i class="fas fa-check"></i> Aprobar
                                    </button>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($emprendedor['estatus'] == 0 || $emprendedor['estatus'] == 1): ?>
                                    <button class="btn btn-danger btn-sm btnrechazar"
                                        data-id="<?= htmlspecialchars($emprendedor['id_emprededor']) ?>"
                                        data-bs-toggle="modal" data-bs-target="#modalrechazar">
                                        <i class="fas fa-check"></i> Rechazar
                                    </button>  
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr><td colspan="8" class="text-center">No hay emprendedores registrados</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal Agregar Emprendedor -->
    <div class="modal fade" id="modalAgregar" tabindex="-1" aria-labelledby="modalAgregarLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form action="<?= APP_URL ?>/emprendedor/registrar" method="POST" class="modal-content" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAgregarLabel">Agregar Nuevo Emprendedor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Barra de progreso -->
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>
                    </div>

                    <!-- PASO 1: Datos Personales -->
                    <div class="form-step" id="step1">
                        <h5>Datos Personales</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="cedula" class="form-label">Cédula*</label>
                                <input type="text" name="cedula" id="cedula" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="nombre" class="form-label">Nombre*</label>
                                <input type="text" name="nombre" id="nombre" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="apellido" class="form-label">Apellido*</label>
                                <input type="text" name="apellido" id="apellido" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                                <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="lugar_nacimiento" class="form-label">Lugar de Nacimiento</label>
                                <input type="text" name="lugar_nacimiento" id="lugar_nacimiento" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="nacionalidad" class="form-label">Nacionalidad</label>
                                <select name="nacionalidad" id="nacionalidad" class="form-select">
                                    <option value="">Seleccione</option>
                                    <option value="V">Venezolano</option>
                                    <option value="E">Extranjero</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="sexo" class="form-label">Sexo</label>
                                <select name="sexo" id="sexo" class="form-select">
                                    <option value="">Seleccione</option>
                                    <option value="M">Masculino</option>
                                    <option value="F">Femenino</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="correo" class="form-label">Correo</label>
                                <input type="email" name="correo" id="correo" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="text" name="telefono" id="telefono" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="municipio" class="form-label">Municipio*</label>
                                <select class="form-select" id="municipio" name="municipio" required>
                                    <option value="">Seleccionar municipio...</option>
                                    <?php foreach ($municipios as $m): ?>
                                        <option value="<?= $m['id_municipio'] ?>"><?= $m['nombre'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="fk_parroquia" class="form-label">Parroquia*</label>
                                <select class="form-select" id="fk_parroquia" name="fk_parroquia" required disabled>
                                    <option value="">Primero seleccione un municipio</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- PASO 2: Datos Familiares y Salud -->
                    <div class="form-step d-none" id="step2">
                        <h5>Datos Familiares y Salud</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="estado_civil" class="form-label">Estado Civil</label>
                                <select name="estado_civil" id="estado_civil" class="form-select">
                                    <option value="">Seleccione</option>
                                    <option value="soltero">Soltero/a</option>
                                    <option value="casado">Casado/a</option>
                                    <option value="divorciado">Divorciado/a</option>
                                    <option value="viudo">Viudo/a</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="cantidad_hijos" class="form-label">Cantidad de Hijos</label>
                                <input type="number" name="cantidad_hijos" id="cantidad_hijos" class="form-control" min="0">
                            </div>
                            <div class="col-md-6">
                                <label for="carga_familiar" class="form-label">Carga Familiar</label>
                                <input type="number" name="carga_familiar" id="carga_familiar" class="form-control" min="0">
                            </div>
                            <div class="col-md-6">
                                <label for="casa_propia" class="form-label">¿Casa Propia?</label>
                                <select name="casa_propia" id="casa_propia" class="form-select">
                                    <option value="si">Sí</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="alquiler" class="form-label">¿Vivienda en alquiler?</label>
                                <select name="alquiler" id="alquiler" class="form-select">
                                    <option value="si">Sí</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="grupo_sangre" class="form-label">Grupo Sanguíneo</label>
                                <select name="grupo_sangre" id="grupo_sangre" class="form-select">
                                    <option value="">Seleccione</option>
                                    <option value="A+">A+</option>
                                    <option value="A-">A-</option>
                                    <option value="B+">B+</option>
                                    <option value="B-">B-</option>
                                    <option value="AB+">AB+</option>
                                    <option value="AB-">AB-</option>
                                    <option value="O+">O+</option>
                                    <option value="O-">O-</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="alergia_medicamento" class="form-label">Alergias a medicamentos</label>
                                <input type="text" name="alergia_medicamento" id="alergia_medicamento" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="alergia_alimento" class="form-label">Alergias a alimentos</label>
                                <input type="text" name="alergia_alimento" id="alergia_alimento" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="operado" class="form-label">¿Ha sido operado?</label>
                                <select name="operado" id="operado" class="form-select">
                                    <option value="si">Sí</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- PASO 3: Educación y Religión -->
                    <div class="form-step d-none" id="step3">
                        <h5>Educación y Religión</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="titulo_academico" class="form-label">Título Académico</label>
                                <input type="text" name="titulo_academico" id="titulo_academico" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="profesion" class="form-label">Profesión</label>
                                <input type="text" name="profesion" id="profesion" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="oficio" class="form-label">Oficio</label>
                                <input type="text" name="oficio" id="oficio" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="hobby" class="form-label">Hobby</label>
                                <input type="text" name="hobby" id="hobby" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="religion" class="form-label">Religión</label>
                                <input type="text" name="religion" id="religion" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="sacramento" class="form-label">Sacramentos</label>
                                <input type="text" name="sacramento" id="sacramento" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="grupo_activo" class="form-label">Grupo Activo</label>
                                <input type="text" name="grupo_activo" id="grupo_activo" class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- PASO 4: Emprendimiento y Projumi -->
                    <div class="form-step d-none" id="step4">
                        <h5>Emprendimiento y Projumi</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="emprendimiento" class="form-label">Nombre del Emprendimiento*</label>
                                <input type="text" name="emprendimiento" id="emprendimiento" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="imagen" class="form-label">Imagen del Emprendimiento</label>
                                <input type="file" name="imagen" id="imagen" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="conocimiento_projumi" class="form-label">¿Cómo conoció Projumi?</label>
                                <input type="text" name="conocimiento_projumi" id="conocimiento_projumi" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="motivo_projumi" class="form-label">Motivo de unirse</label>
                                <input type="text" name="motivo_projumi" id="motivo_projumi" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="aporte_projumi" class="form-label">Aporte a Projumi</label>
                                <input type="text" name="aporte_projumi" id="aporte_projumi" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="estatus" class="form-label">Estatus</label>
                                <select name="estatus" id="estatus" class="form-select">
                                    <option value="0" selected>En revisión</option>
                                    <option value="1">Activo</option>
                                    <option value="2">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="prevBtn">Atrás</button>
                    <button type="button" class="btn btn-primary" id="nextBtn">Siguiente</button>
                    <button type="submit" class="btn btn-success d-none" id="submitBtn">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Editar -->
    <div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel">
        <div class="modal-dialog modal-lg">
            <form action="<?= APP_URL ?>/emprendedor/update" method="POST" class="modal-content" enctype="multipart/form-data">
                <input type="hidden" name="id_emprendedor" id="edit_id_emprendedor" />
                <input type="hidden" name="cedula" id="edit_cedula" />
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarLabel">Editar Emprendedor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Barra de progreso -->
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>
                    </div>

                    <!-- PASO 1: Datos Personales -->
                    <div class="form-step" id="edit_step1">
                        <h5>Datos Personales</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="edit_nombre" class="form-label">Nombre*</label>
                                <input type="text" name="nombre" id="edit_nombre" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_apellido" class="form-label">Apellido*</label>
                                <input type="text" name="apellido" id="edit_apellido" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                                <input type="date" name="fecha_nacimiento" id="edit_fecha_nacimiento" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="edit_lugar_nacimiento" class="form-label">Lugar de Nacimiento</label>
                                <input type="text" name="lugar_nacimiento" id="edit_lugar_nacimiento" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="edit_nacionalidad" class="form-label">Nacionalidad</label>
                                <select name="nacionalidad" id="edit_nacionalidad" class="form-select">
                                    <option value="">Seleccione</option>
                                    <option value="V">Venezolano</option>
                                    <option value="E">Extranjero</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_sexo" class="form-label">Sexo</label>
                                <select name="sexo" id="edit_sexo" class="form-select">
                                    <option value="">Seleccione</option>
                                    <option value="M">Masculino</option>
                                    <option value="F">Femenino</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_correo" class="form-label">Correo</label>
                                <input type="email" name="correo" id="edit_correo" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="edit_telefono" class="form-label">Teléfono</label>
                                <input type="text" name="telefono" id="edit_telefono" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="edit_municipio" class="form-label">Municipio*</label>
                                <select class="form-select" id="edit_municipio" name="municipio" required>
                                    <option value="">Seleccionar municipio...</option>
                                    <?php foreach ($municipios as $m): ?>
                                        <option value="<?= $m['id_municipio'] ?>"><?= $m['nombre'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_fk_parroquia" class="form-label">Parroquia*</label>
                                <select class="form-select" id="edit_fk_parroquia" name="fk_parroquia" required>
                                    <option value="">Cargando parroquias...</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- PASO 2: Datos Familiares y Salud -->
                    <div class="form-step d-none" id="edit_step2">
                        <h5>Datos Familiares y Salud</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="edit_estado_civil" class="form-label">Estado Civil</label>
                                <select name="estado_civil" id="edit_estado_civil" class="form-select">
                                    <option value="">Seleccione</option>
                                    <option value="soltero">Soltero/a</option>
                                    <option value="casado">Casado/a</option>
                                    <option value="divorciado">Divorciado/a</option>
                                    <option value="viudo">Viudo/a</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_cantidad_hijos" class="form-label">Cantidad de Hijos</label>
                                <input type="number" name="cantidad_hijos" id="edit_cantidad_hijos" class="form-control" min="0">
                            </div>
                            <div class="col-md-6">
                                <label for="edit_carga_familiar" class="form-label">Carga Familiar</label>
                                <input type="number" name="carga_familiar" id="edit_carga_familiar" class="form-control" min="0">
                            </div>
                            <div class="col-md-6">
                                <label for="edit_casa_propia" class="form-label">¿Casa Propia?</label>
                                <select name="casa_propia" id="edit_casa_propia" class="form-select">
                                    <option value="si">Sí</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_alquiler" class="form-label">¿Vivienda en alquiler?</label>
                                <select name="alquiler" id="edit_alquiler" class="form-select">
                                    <option value="si">Sí</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_grupo_sangre" class="form-label">Grupo Sanguíneo</label>
                                <select name="grupo_sangre" id="edit_grupo_sangre" class="form-select">
                                    <option value="">Seleccione</option>
                                    <option value="A+">A+</option>
                                    <option value="A-">A-</option>
                                    <option value="B+">B+</option>
                                    <option value="B-">B-</option>
                                    <option value="AB+">AB+</option>
                                    <option value="AB-">AB-</option>
                                    <option value="O+">O+</option>
                                    <option value="O-">O-</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_alergia_medicamento" class="form-label">Alergias a medicamentos</label>
                                <input type="text" name="alergia_medicamento" id="edit_alergia_medicamento" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="edit_alergia_alimento" class="form-label">Alergias a alimentos</label>
                                <input type="text" name="alergia_alimento" id="edit_alergia_alimento" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="edit_operado" class="form-label">¿Ha sido operado?</label>
                                <select name="operado" id="edit_operado" class="form-select">
                                    <option value="si">Sí</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- PASO 3: Educación y Religión -->
                    <div class="form-step d-none" id="edit_step3">
                        <h5>Educación y Religión</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="edit_titulo_academico" class="form-label">Título Académico</label>
                                <input type="text" name="titulo_academico" id="edit_titulo_academico" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="edit_profesion" class="form-label">Profesión</label>
                                <input type="text" name="profesion" id="edit_profesion" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="edit_oficio" class="form-label">Oficio</label>
                                <input type="text" name="oficio" id="edit_oficio" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="edit_hobby" class="form-label">Hobby</label>
                                <input type="text" name="hobby" id="edit_hobby" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="edit_religion" class="form-label">Religión</label>
                                <input type="text" name="religion" id="edit_religion" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="edit_sacramento" class="form-label">Sacramentos</label>
                                <input type="text" name="sacramento" id="edit_sacramento" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="edit_grupo_activo" class="form-label">Grupo Activo</label>
                                <input type="text" name="grupo_activo" id="edit_grupo_activo" class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- PASO 4: Emprendimiento y Projumi -->
                    <div class="form-step d-none" id="edit_step4">
                        <h5>Emprendimiento y Projumi</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="edit_emprendimiento" class="form-label">Nombre del Emprendimiento*</label>
                                <input type="text" name="emprendimiento" id="edit_emprendimiento" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_imagen" class="form-label">Imagen del Emprendimiento</label>
                                <input type="file" name="imagen" id="edit_imagen" class="form-control">
                                <small class="text-muted">Dejar en blanco para mantener la imagen actual</small>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_conocimiento_projumi" class="form-label">¿Cómo conoció Projumi?</label>
                                <input type="text" name="conocimiento_projumi" id="edit_conocimiento_projumi" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="edit_motivo_projumi" class="form-label">Motivo de unirse</label>
                                <input type="text" name="motivo_projumi" id="edit_motivo_projumi" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="edit_aporte_projumi" class="form-label">Aporte a Projumi</label>
                                <input type="text" name="aporte_projumi" id="edit_aporte_projumi" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="edit_estatus" class="form-label">Estatus</label>
                                <select name="estatus" id="edit_estatus" class="form-select" required>
                                    <option value="0">En revisión</option>
                                    <option value="1">Activo</option>
                                    <option value="2">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="edit_prevBtn">Atrás</button>
                    <button type="button" class="btn btn-primary" id="edit_nextBtn">Siguiente</button>
                    <button type="submit" class="btn btn-success d-none" id="edit_submitBtn">Actualizar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Eliminar -->
    <div class="modal fade" id="modalEliminar" tabindex="-1" aria-labelledby="modalEliminarLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="<?= APP_URL ?>/emprendedor/delete" method="POST" class="modal-content">
                <input type="hidden" name="cedula" id="delete_cedula" />
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEliminarLabel">Eliminar Emprendedor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de eliminar al emprendedor <strong id="delete_emprendedor_nombre"></strong>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Aprobar -->
    <div class="modal fade" id="modalAprobar" tabindex="-1" aria-labelledby="modalAprobarLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="<?= APP_URL ?>/emprendedor/aprobar" method="POST" class="modal-content">
                <input type="hidden" name="id_emprendedor" id="aprobar_id_emprendedor" />
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAprobarLabel">Aprobar Emprendedor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de aprobar este emprendedor?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Aprobar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts Bootstrap y funcionalidad modales -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo APP_URL; ?>/public/js/emprendedor.js"></script>
    <script>
        // Función para cargar parroquias
        function cargarParroquias(municipioId, parroquiaSelectId) {
            const parroquiaSelect = document.getElementById(parroquiaSelectId);
            
            if (!municipioId) {
                parroquiaSelect.innerHTML = '<option value="">Primero seleccione un municipio</option>';
                parroquiaSelect.disabled = true;
                return;
            }
            
            parroquiaSelect.disabled = true;
            parroquiaSelect.innerHTML = '<option value="">Cargando parroquias...</option>';
            
            fetch(`<?= APP_URL ?>/emprendedor/obtenerParroquias?id_municipio=${municipioId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        parroquiaSelect.innerHTML = '<option value="">Seleccione una parroquia</option>';
                        data.data.forEach(parroquia => {
                            const option = document.createElement('option');
                            option.value = parroquia.id_parroquia;
                            option.textContent = parroquia.parroquia;
                            parroquiaSelect.appendChild(option);
                        });
                        parroquiaSelect.disabled = false;
                    } else {
                        parroquiaSelect.innerHTML = '<option value="">Error al cargar parroquias</option>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    parroquiaSelect.innerHTML = '<option value="">Error al cargar parroquias</option>';
                });
        }

        // Configuración para el modal de registro
        document.addEventListener('DOMContentLoaded', function() {
            // Selector de municipio en modal de registro
            const municipioSelect = document.getElementById('municipio');
            if (municipioSelect) {
                municipioSelect.addEventListener('change', function() {
                    cargarParroquias(this.value, 'fk_parroquia');
                });
            }

            // Configuración para el modal de edición
            const editarModal = document.getElementById('modalEditar');
            if (editarModal) {
                editarModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    
                    // Llenar datos básicos
                    document.getElementById('edit_id_emprendedor').value = button.getAttribute('data-id');
                    document.getElementById('edit_cedula').value = button.getAttribute('data-cedula');
                    document.getElementById('edit_nombre').value = button.getAttribute('data-nombre');
                    document.getElementById('edit_apellido').value = button.getAttribute('data-apellido');
                    document.getElementById('edit_emprendimiento').value = button.getAttribute('data-emprendimiento');
                    document.getElementById('edit_estatus').value = button.getAttribute('data-estatus');
                    document.getElementById('edit_aporte_projumi').value = button.getAttribute('data-aporte');
                    
                    // Configurar municipio y parroquia
                    const idMunicipio = button.getAttribute('data-id_municipio');
                    const idParroquia = button.getAttribute('data-fk_parroquia');
                    
                    if (idMunicipio) {
                        document.getElementById('edit_municipio').value = idMunicipio;
                        cargarParroquias(idMunicipio, 'edit_fk_parroquia');
                        
                        // Esperar un momento para seleccionar la parroquia correcta
                        setTimeout(() => {
                            if (idParroquia) {
                                document.getElementById('edit_fk_parroquia').value = idParroquia;
                            }
                        }, 500);
                    }
                    
                    // Resetear pasos en modal de edición
                    document.querySelectorAll('#modalEditar .form-step').forEach((step, index) => {
                        if (index === 0) {
                            step.classList.remove('d-none');
                        } else {
                            step.classList.add('d-none');
                        }
                    });
                    
                    // Resetear barra de progreso
                    document.querySelector('#modalEditar .progress-bar').style.width = '25%';
                    document.querySelector('#modalEditar .progress-bar').setAttribute('aria-valuenow', '25');
                    document.querySelector('#modalEditar .progress-bar').textContent = '25%';
                    
                    // Configurar botones
                    document.getElementById('edit_prevBtn').style.display = 'none';
                    document.getElementById('edit_nextBtn').style.display = 'inline-block';
                    document.getElementById('edit_submitBtn').classList.add('d-none');
                });
                
                // Selector de municipio en modal de edición
                const editMunicipioSelect = document.getElementById('edit_municipio');
                if (editMunicipioSelect) {
                    editMunicipioSelect.addEventListener('change', function() {
                        cargarParroquias(this.value, 'edit_fk_parroquia');
                    });
                }
            }

            // Configuración para el modal de eliminar
            const eliminarModal = document.getElementById('modalEliminar');
            if (eliminarModal) {
                eliminarModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    document.getElementById('delete_cedula').value = button.getAttribute('data-cedula');
                    document.getElementById('delete_emprendedor_nombre').textContent = button.getAttribute('data-nombre');
                });
            }

            // Configuración para el modal de aprobar
            const aprobarModal = document.getElementById('modalAprobar');
            if (aprobarModal) {
                aprobarModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    document.getElementById('aprobar_id_emprendedor').value = button.getAttribute('data-id');
                });
            }
        });
    </script>
</body>
</html>