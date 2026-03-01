<?php if (!isset($_SESSION['user']['cedula'])): ?>
    <script>window.location.href = '<?= APP_URL ?>home/index';</script>
    <?php exit(); ?>
<?php endif; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PROJUMI - Reportes y Estadísticas</title>
    <?php include 'views/componentes/head.php'; ?>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/reportes.css">
</head>
<body>

<?php
// Asegúrate de iniciar la sesión también en la vista
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<?php

        include "views/" . $menu . ".php";
    


?>
<br>
<br>
    <div class="reports-container">
        <div class="reports-header">
            <h1><i class="fas fa-chart-pie"></i> Reportes y Estadísticas</h1>
            <p class="subtitle">Visualiza y analiza los datos de tu emprendimiento</p>
        </div>

        <div class="filters-section">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="dateFrom" class="form-label">Desde</label>
                    <input type="date" class="form-control" id="dateFrom">
                </div>
                <div class="col-md-3">
                    <label for="dateTo" class="form-label">Hasta</label>
                    <input type="date" class="form-control" id="dateTo">
                </div>
                <div class="col-md-4">
                    <label for="reportType" class="form-label">Tipo de Reporte</label>
                    <select class="form-select" id="reportType">
                        <option value="all">Todos los reportes</option>
                        <option value="shipments">Reporte de producto mas vendido</option>
                        <option value="sales">Reporte de producto menos vendido</option>
                        <option value="users">Reporte de status de stock</option>
                        <option value="users">Reporte de clientes activos</option>
                        <option value="users">Reporte de monto total de ventas realizadas</option>
                        <option value="statistics">Reporte de envíos</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-generate" id="generateReport">
                        <i class="fas fa-sync-alt"></i> Generar
                    </button>
                </div>
            </div>
        </div>

        <div class="reports-dashboard">
            <!-- Tarjetas resumen -->
            <div class="summary-cards">
                <div class="summary-card shipments">
                    <div class="card-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <div class="card-content">
                        <h3>100</h3>
                        <p>Envíos realizados</p>
                    </div>
                </div>
                <div class="summary-card sales">
                    <div class="card-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="card-content">
                        <h3>$48,750</h3>
                        <p>Ventas totales</p>
                    </div>
                </div>
                <div class="summary-card users">
                    <div class="card-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="card-content">
                        <h3>328</h3>
                        <p>Clientes activos</p>
                    </div>
                </div>
                <div class="summary-card growth">
                    <div class="card-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="card-content">
                        <h3>24%</h3>
                        <p>Crecimiento mensual</p>
                    </div>
                </div>
            </div>

            <!-- Gráficos principales -->
            <div class="main-charts">
                <div class="chart-container">
                    <canvas id="salesChart"></canvas>
                </div>
                <div class="chart-container">
                    <canvas id="shipmentsChart"></canvas>
                </div>
            </div>

            <!-- Tabla de reportes -->
            <div class="reports-table-section">
                <div class="section-header">
                    <h3><i class="fas fa-table"></i> Detalle de Reportes</h3>
                    <button class="btn btn-export" id="exportReport">
                        <i class="fas fa-file-export"></i> Exportar
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table" id="reportsTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Descripción</th>
                                <th>Cantidad</th>
                                <th>Total</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Datos dinámicos -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Exportar -->
    <div class="modal fade" id="exportModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-file-export"></i> Exportar Reporte</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="exportFormat" class="form-label">Formato de exportación</label>
                        <select class="form-select" id="exportFormat">
                            <option value="pdf">PDF</option>
                            <option value="excel">Excel</option>
                            <option value="csv">CSV</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="exportRange" class="form-label">Rango a exportar</label>
                        <select class="form-select" id="exportRange">
                            <option value="current">Vista actual</option>
                            <option value="all">Todos los datos</option>
                            <option value="custom">Personalizado</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="confirmExport">Exportar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detalle Reporte -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-info-circle"></i> Detalle del Reporte</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="detail-content">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <span class="detail-label">ID Reporte:</span>
                                    <span class="detail-value" id="reportId">-</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Fecha:</span>
                                    <span class="detail-value" id="reportDate">-</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Tipo:</span>
                                    <span class="detail-value" id="reportTypeDetail">-</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <span class="detail-label">Cantidad:</span>
                                    <span class="detail-value" id="reportQuantity">-</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Total:</span>
                                    <span class="detail-value" id="reportTotal">-</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Estado:</span>
                                    <span class="detail-value" id="reportStatus">-</span>
                                </div>
                            </div>
                        </div>
                        <div class="detail-item full-width">
                            <span class="detail-label">Descripción completa:</span>
                            <div class="detail-value" id="reportDescription">-</div>
                        </div>
                        <div class="chart-container mt-4">
                            <canvas id="detailChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Custom JS -->
    <script src="<?php echo APP_URL; ?>/public/js/alertas.js" type="module"></script>
    <script src="<?php echo APP_URL; ?>/public/js/reportes.js" type="module"></script>
</body>
</html>