<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PROJUMI - Configuración del Sistema</title>
    <?php include 'views/componentes/head.php'; ?>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/carrito.css">

    
</head>
<body>

<?php
// Asegúrate de iniciar la sesión también en la vista
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<?php

        include "views/headeradmin.php";
    


?>
<br>
<br>
<div class="config-container" style="margin-top: 100px; margin-bottom: 100px; padding: 20px;">
        <div class="config-header">
            <h1><i class="fas fa-cog"></i> Configuración del Sistema</h1>
            <p class="subtitle">Administra los aspectos fundamentales de PROJUMI</p>
        </div>

        <div class="config-options">
            <div class="row g-4">
                <!-- Tarjeta de Roles -->
                <div class="col-md-4">
                    <div class="config-card roles-card">
                        <div class="card-icon">
                            <i class="fas fa-user-tag"></i>
                        </div>
                        <div class="card-content">
                            <h3>Usuarios</h3>
                            <p>Administra los usuarios y les asigna roles</p>
                            <a href="<?php echo APP_URL; ?>/roles/configroles" class="btn btn-config">Gestionar Roles</a>
                        </div>
                    </div>
                </div>

                <!-- Tarjeta de Módulos -->
                <div class="col-md-4">
                    <div class="config-card modules-card">
                        <div class="card-icon">
                            <i class="fas fa-puzzle-piece"></i>
                        </div>
                        <div class="card-content">
                            <h3>Módulos del Sistema</h3>
                            <p>Configura los módulos disponibles en la plataforma</p>
                            <a href="<?php echo APP_URL; ?>/modulos/configmodulos" class="btn btn-config">Gestionar Módulos</a>
                        </div>
                    </div>
                </div>

                <!-- Tarjeta de Permisos -->
                <div class="col-md-4">
                    <div class="config-card permissions-card">
                        <div class="card-icon">
                            <i class="fas fa-key"></i>
                        </div>
                        <div class="card-content">
                            <h3>Roles y Permisos</h3>
                            <p>Define los permisos para cada rol y módulo</p>
                            <a href="<?php echo APP_URL; ?>/roles/index" class="btn btn-config">Gestionar Permisos</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Custom JS -->
    <script src="<?php echo APP_URL; ?>/public/js/carrito.js"></script>
</body>
</html>