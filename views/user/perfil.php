<?php if (!isset($_SESSION['user']['cedula'])): ?>
    <script>window.location.href = '<?= APP_URL ?>home/index';</script>
    <?php exit(); ?>
<?php endif; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PROJUMI - Perfil</title>
    <?php include 'views/componentes/head.php'; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/profile.css">
    
</head>
<body>
<?php
// Asegúrate de iniciar la sesión también en la vista
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<?php

        include "views/navbar.php";
    


?>
    <!-- Perfil de usuario -->
    <br>
    <br>
    <br>
    <br>
        <div class="container">
            <div class="row">
                <div class="col-lg-4">
                    <div class="profile-card text-center">
                        <div class="position-relative mx-auto" style="width: fit-content;">
                            <?php
                            $imagenPerfil = !empty($_SESSION['user']['imagen']) 
                                ? APP_URL . htmlspecialchars($_SESSION['user']['imagen']) 
                                : APP_URL . '/public/img/default_profile.png';
                            ?>
                            <img src="<?= $imagenPerfil ?>" class="profile-img border border-light rounded-circle" style="object-fit: cover; width: 160px; height: 160px;" alt="Foto de perfil">
                            <button class="btn btn-sm btn-primary position-absolute" style="bottom: 10px; right: 10px;">
                                <i class="fas fa-camera"></i>
                            </button>
                        </div>
                        <h3><?php echo htmlspecialchars($_SESSION['user']['nombre'] ?? ''); ?></h3>
                        <p class="text-muted">miembro desde: <?php echo htmlspecialchars($_SESSION['user']['fecha'] ?? ''); ?></p>
                        
                        <div class="d-flex justify-content-center mb-3">
                            <div class="text-warning me-2">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="far fa-star"></i>
                            </div>
                            <span>4.2 (12 reseñas)</span>
                        </div>
                        
                        <hr>
                        
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link active" href="#perfil"><i class="fas fa-user me-2"></i>Mi perfil</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo APP_URL; ?>/pedidos/index"><i class="fas fa-clipboard-list me-2"></i>Mis pedidos</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo APP_URL; ?>/pagos/index"><i class="fa-solid fa-wallet"></i>Mis pagos</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#passusuario"><i class="fas fa-cog me-2"></i>Configuración</a>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-lg-8 mt-4 mt-lg-0">
                    <div class="profile-card">
                        <h4 class="mb-4">Información personal</h4>
                        
                        <form action="<?php echo APP_URL; ?>/home/perfil" method="post">
                        
                            <div class="row mb-3">

                            <input type="hidden" name="id_usuario" value="<?php echo htmlspecialchars($_SESSION['user']['id_usuario'] ?? ''); ?>">

                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" class="form-control" name="nombre" value="<?php echo htmlspecialchars($_SESSION['user']['nombre'] ?? ''); ?>">
                                </div>
                             <div class="col-md-6">
                                    <label class="form-label">Apellido</label>
                                    <input type="text" class="form-control" name="apellido"  value="<?php echo htmlspecialchars($_SESSION['user']['apellido'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" name="cedula" value="<?php echo htmlspecialchars($_SESSION['user']['cedula'] ?? ''); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Correo electrónico</label>
                                <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($_SESSION['user']['email'] ?? ''); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" name="cedula" value="<?php echo htmlspecialchars($_SESSION['user']['telefono'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Direccion</label>
                                <input type="text" class="form-control" name="direccion" value="<?php echo htmlspecialchars($_SESSION['user']['direccion'] ?? ''); ?>">
                            </div>
                           
                            <div class="mb-4">
                                <label class="form-label">Fecha de nacimiento</label>
                                <input type="date" class="form-control" name="fecha_nacimiento" value="<?php echo htmlspecialchars($_SESSION['user']['fecha_nacimiento'] ?? ''); ?>">
                            </div>
                            
                            <div class="d-flex justify-content-end">
                                <input type="submit" class="btn btn-custom" name="actualizar" value="Guardar cambios">
                            </div>
                        </form>
                        
                        <hr class="my-4">
                        
                        <h4 class="mb-4">Cambiar contraseña</h4>
                        
                        <form id="passusuario" action="<?php echo APP_URL; ?>/home/perfil" method="post">
                            

                            <input type="hidden" name="id_usuario" value="<?php echo htmlspecialchars($_SESSION['user']['id_usuario'] ?? ''); ?>">
                            <div class="mb-3">
                                <label class="form-label">Contraseña actual</label>
                                <input type="password" name="pass" class="form-control" required>
                                
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Nueva contraseña</label>
                                <input type="password" name="nuevopass" class="form-control" id="nuevopass" required minlength="8">
                                
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Confirmar nueva contraseña</label>
                                <input type="password" name="confirmpass" class="form-control" required>
                            </div>
                            
                            <div class="d-flex justify-content-end">
                                <input type="submit" class="btn btn-custom" name="Cambiarpassword" value="Cambiar contraseña">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
   <!-- Modal cerrar Sesion -->
   <div class="modal fade" id="sessionModal" tabindex="-1" aria-labelledby="sessionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background-color: var(--darker-bg); color: var(--text-light);">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title" id="sessionModalLabel"><i class="fas fa-exclamation-triangle"></i>Desea salir</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro que Salir?</p>
                <p class="text-danger">Saldras del sistema.</p>
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger">
                    <i ></i> Salir
                </button>
            </div>
        </div>
    </div>
</div>


    <!-- Pie de página (igual que en index.html) -->
    <footer class="bg-dark text-white py-4 mt-5">
        <!-- ... mismo código que en index.html ... -->
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    
</body>
</html>