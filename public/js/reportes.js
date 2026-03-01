import { mostrarAlerta } from './alertas.js';
document.addEventListener('DOMContentLoaded', function() {
    // Datos de ejemplo
    const reportData = [
        {
            id: 1001,
            date: '2023-11-15',
            type: 'Venta',
            description: 'Venta de productos artesanales',
            quantity: 15,
            total: 1250,
            status: 'Completado'
        },
        {
            id: 1002,
            date: '2023-11-14',
            type: 'Envío',
            description: 'Envío a zona norte',
            quantity: 8,
            total: 450,
            status: 'En proceso'
        },
        {
            id: 1003,
            date: '2023-11-13',
            type: 'Usuario',
            description: 'Nuevo usuario registrado',
            quantity: 1,
            total: 0,
            status: 'Completado'
        },
        {
            id: 1004,
            date: '2023-11-12',
            type: 'Venta',
            description: 'Venta de productos agrícolas',
            quantity: 22,
            total: 1875,
            status: 'Completado'
        },
        {
            id: 1005,
            date: '2023-11-11',
            type: 'Envío',
            description: 'Envío internacional',
            quantity: 3,
            total: 1200,
            status: 'Entregado'
        }
    ];

    // Inicializar gráficos
    initCharts();
    
    // Llenar tabla de reportes
    fillReportsTable();
    
    // Evento para generar reporte
    document.getElementById('generateReport').addEventListener('click', function() {
        const dateFrom = document.getElementById('dateFrom').value;
        const dateTo = document.getElementById('dateTo').value;
        const reportType = document.getElementById('reportType').value;
        
        // Aquí iría la lógica para filtrar los datos
        mostrarAlerta('Generando reporte', 'Generando reporte de ' + reportType + ' desde ' + dateFrom + ' hasta ' + dateTo, 'success');    
        updateCharts();
    });
    
    // Evento para exportar reporte
    document.getElementById('exportReport').addEventListener('click', function() {
        const exportModal = new bootstrap.Modal(document.getElementById('exportModal'));
        exportModal.show();
    });
    
    // Confirmar exportación
    document.getElementById('confirmExport').addEventListener('click', function() {
        const format = document.getElementById('exportFormat').value;
        const range = document.getElementById('exportRange').value;
        
        mostrarAlerta('Exportando reporte', 'Exportando reporte en formato ' + format.toUpperCase() + ' (' + range + ')', 'success');   
        const exportModal = bootstrap.Modal.getInstance(document.getElementById('exportModal'));
        exportModal.hide();
    });

    // Función para llenar la tabla de reportes
    function fillReportsTable() {
        const tableBody = document.querySelector('#reportsTable tbody');
        tableBody.innerHTML = '';
        
        reportData.forEach(report => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${report.id}</td>
                <td>${report.date}</td>
                <td>${report.type}</td>
                <td>${report.description}</td>
                <td>${report.quantity}</td>
                <td>${report.total > 0 ? '$' + report.total : '-'}</td>
                <td>
                    <button class="btn btn-details view-detail" data-id="${report.id}">
                        <i class="fas fa-eye"></i> Ver
                    </button>
                </td>
            `;
            tableBody.appendChild(row);
        });
        
        // Agregar eventos a los botones de detalle
        document.querySelectorAll('.view-detail').forEach(button => {
            button.addEventListener('click', function() {
                const reportId = this.getAttribute('data-id');
                showReportDetail(reportId);
            });
        });
    }
    
    // Función para mostrar el detalle de un reporte
    function showReportDetail(reportId) {
        const report = reportData.find(r => r.id == reportId);
        
        if (report) {
            document.getElementById('reportId').textContent = report.id;
            document.getElementById('reportDate').textContent = report.date;
            document.getElementById('reportTypeDetail').textContent = report.type;
            document.getElementById('reportQuantity').textContent = report.quantity;
            document.getElementById('reportTotal').textContent = report.total > 0 ? '$' + report.total : '-';
            document.getElementById('reportStatus').textContent = report.status;
            document.getElementById('reportDescription').textContent = report.description;
            
            // Actualizar gráfico de detalle
            updateDetailChart(report.type);
            
            const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
            detailModal.show();
        }
    }
    
    // Inicializar gráficos principales
    function initCharts() {
        // Gráfico de ventas
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        window.salesChart = new Chart(salesCtx, {
            type: 'bar',
            data: {
                labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov'],
                datasets: [{
                    label: 'Ventas mensuales',
                    data: [12500, 19000, 15300, 17800, 14200, 21000, 18500, 19800, 22400, 18700, 23500],
                    backgroundColor: 'rgba(255, 215, 0, 0.7)',
                    borderColor: 'rgba(255, 215, 0, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Ventas Mensuales',
                        font: {
                            size: 16
                        }
                    },
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
        
        // Gráfico de envíos
        const shipmentsCtx = document.getElementById('shipmentsChart').getContext('2d');
        window.shipmentsChart = new Chart(shipmentsCtx, {
            type: 'line',
            data: {
                labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov'],
                datasets: [{
                    label: 'Envíos mensuales',
                    data: [45, 68, 72, 65, 78, 92, 85, 88, 94, 87, 102],
                    backgroundColor: 'rgba(135, 206, 235, 0.2)',
                    borderColor: 'rgba(135, 206, 235, 1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Envios Mensuales',
                        font: {
                            size: 16
                        }
                    },
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        // Gráfico de detalle (inicialmente oculto)
        const detailCtx = document.getElementById('detailChart').getContext('2d');
        window.detailChart = new Chart(detailCtx, {
            type: 'doughnut',
            data: {
                labels: ['Completado', 'En proceso', 'Pendiente'],
                datasets: [{
                    data: [75, 15, 10],
                    backgroundColor: [
                        'rgba(0, 100, 0, 0.7)',
                        'rgba(255, 215, 0, 0.7)',
                        'rgba(139, 69, 19, 0.7)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Distribución de estados',
                        font: {
                            size: 14
                        }
                    }
                }
            }
        });
    }
    
    // Actualizar gráficos con nuevos datos
    function updateCharts() {
        // Simular actualización de datos
        const newSalesData = window.salesChart.data.datasets[0].data.map(value => {
            return value * (0.9 + Math.random() * 0.3); // Variación aleatoria
        });
        
        const newShipmentsData = window.shipmentsChart.data.datasets[0].data.map(value => {
            return Math.floor(value * (0.8 + Math.random() * 0.4)); // Variación aleatoria
        });
        
        window.salesChart.data.datasets[0].data = newSalesData;
        window.shipmentsChart.data.datasets[0].data = newShipmentsData;
        
        window.salesChart.update();
        window.shipmentsChart.update();
    }
    
    // Actualizar gráfico de detalle según tipo de reporte
    function updateDetailChart(reportType) {
        let data;
        let title;
        
        switch(reportType) {
            case 'Venta':
                data = [65, 20, 15];
                title = 'Estados de ventas';
                break;
            case 'Envío':
                data = [45, 30, 25];
                title = 'Estados de envíos';
                break;
            case 'Usuario':
                data = [80, 10, 10];
                title = 'Estados de usuarios';
                break;
            default:
                data = [60, 20, 20];
                title = 'Distribución de estados';
        }
        
        window.detailChart.data.datasets[0].data = data;
        window.detailChart.options.plugins.title.text = title;
        window.detailChart.update();
    }
});