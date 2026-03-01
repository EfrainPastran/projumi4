<?php if (!isset($_SESSION['user']['cedula'])): ?>
    <script>window.location.href = '<?= APP_URL ?>home/index';</script>
    <?php exit(); ?>
<?php endif; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PROJUMI - Mantenimiento de Base de Datos</title>
    <?php include 'views/componentes/head.php'; ?>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/db.css">
</head>
<body>
<?php
// Asegúrate de iniciar la sesión también en la vista
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<?php include "views/navbar.php";?>

<br>
<br>

    <div class="db-main-container">
        <div class="db-card">
            <div class="db-header">
                <h1><i class="fas fa-database"></i> Mantenimiento de Base de Datos</h1>
                <p class="db-subtitle">Administra los respaldos y restauraciones del sistema</p>
            </div>
            
            <div class="db-actions">
                <!-- Tarjeta de Respaldo -->
                <div class="db-action-card backup-card">
                    <div class="db-icon-container">
                        <i class="fas fa-database"></i>
                        <i class="fas fa-arrow-up"></i>
                    </div>
                    <h3>Respaldo de BD</h3>
                    <p>Crea una copia de seguridad completa de la base de datos actual</p>
                    <button class="btn db-action-btn" id="backupBtn" data-bs-toggle="modal" data-bs-target="#confirmBackupModal">
                        <i class="fas fa-play"></i> Ejecutar Respaldo
                    </button>
                </div>

                <!-- Tarjeta de Restauración -->
                <div class="db-action-card restore-card">
                    <div class="db-icon-container">
                        <i class="fas fa-database"></i>
                        <i class="fas fa-arrow-down"></i>
                    </div>
                    <h3>Restauración de BD</h3>
                    <p>Restaura la base de datos desde un respaldo anterior</p>
                    <button class="btn db-action-btn" id="restoreBtn" data-bs-toggle="modal" data-bs-target="#confirmRestoreModal">
                        <i class="fas fa-play"></i> Ejecutar Restauración
                    </button>
                </div>
            </div>
            
            <div class="db-history">
                <h4><i class="fas fa-history"></i> Historial de Operaciones</h4>
                <div class="table-responsive">
                    <table class="table db-history-table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Archivo</th>
                                <th>Tamaño</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody id="historyTableBody">
                            <!-- Datos dinámicos se insertarán aquí -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Confirmar Respaldo -->
    <div class="modal fade" id="confirmBackupModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">

                    <form action="<?php echo APP_URL; ?>/mantenimiento/backup" method="post" >
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Confirmar Respaldo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro que deseas crear un respaldo de la base de datos?</p>
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle"></i> Esta operación puede tomar varios minutos dependiendo del tamaño de la base de datos.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <input type="submit" class="btn btn-primary" id="confirmBackup" name="backup" value="Confirmar Respaldo">
                </div>
            </form>
            </div>
        </div>
    </div>

    <!-- Modal Confirmar Restauración -->
    <div class="modal fade" id="confirmRestoreModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Confirmar Restauración</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="backupFile" class="form-label">Seleccionar archivo de respaldo</label>
                        <select class="form-select" id="backupFile">
                            <option value="">Seleccione un archivo...</option>
                            <!-- Opciones se llenarán dinámicamente -->
                        </select>
                    </div>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> Advertencia: Todos los datos posteriores a este respaldo se perderán.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmRestore">Confirmar Restauración</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Progreso -->
    <div class="modal fade" id="progressModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="progressModalTitle">Procesando operación...</h5>
                </div>
                <div class="modal-body">
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" id="operationProgress" role="progressbar" style="width: 0%"></div>
                    </div>
                    <p class="text-center mt-3" id="progressText">Iniciando proceso...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="<?php echo APP_URL; ?>/public/js/db.js" type="module"></script>
    
</body>
</html>