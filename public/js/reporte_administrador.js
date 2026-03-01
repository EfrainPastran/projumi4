import { mostrarAlerta } from './alertas.js';
import { API_CONFIG } from './config.js';
document.addEventListener('DOMContentLoaded', function() {
    // Datos de ejemplo
    const reportData = [];

    // Inicializar gráficos
    initCharts();
    cargarGraficos();
    
    // Evento para generar reporte
    document.getElementById('generateReport').addEventListener('click', function () {
        const reportType = document.getElementById('reportType').value;
        const dateFrom = document.getElementById('dateFrom').value;
        const dateTo = document.getElementById('dateTo').value;
        fetch(API_CONFIG + '/reportes/reporteAdmin', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ reportType, dateFrom, dateTo })
        })
        .then(async response => {
            const contentType = response.headers.get('Content-Type');
    
            if (!response.ok) {
                if (contentType && contentType.includes('application/json')) {
                    const errorData = await response.json();
                    throw new Error(errorData.error || 'Error al generar el reporte');
                } else {
                    throw new Error('Error al generar el reporte');
                }
            }
    
            if (reportType === 'clientes') {
                const jsonData = await response.json();
                if (jsonData.success) {
                    renderTabla('clientes', jsonData.data);
                }
            }
            else if (reportType === 'productos') {
                const jsonData = await response.json();
                if (jsonData.success) {
                    renderTabla('productos', jsonData.data);
                }
            }
            else if (reportType === 'ventas') {
                const jsonData = await response.json();
                if (jsonData.success) {
                    renderTabla('ventas', jsonData.data);
                }
            }
             else {
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `reporte_${reportType}.pdf`;
                a.click();
                window.URL.revokeObjectURL(url);
            }
        })
        .catch(error => {
            mostrarAlerta('Error', error.message, 'error');
        });
    });
    
    function renderTabla(tipo, datos) {
        // Destruir instancia previa de DataTable si existe
        if ( $.fn.DataTable.isDataTable('#reportsTable') ) {
            $('#reportsTable').DataTable().clear().destroy();
        }

        const thead = document.querySelector('#reportsTable thead tr');
        const tbody = document.querySelector('#reportsTable tbody');
        thead.innerHTML = '';
        tbody.innerHTML = '';
    
        let columnas = '';
    
        if (tipo === 'clientes') {
            // Encabezados
            columnas = `
                <th>Cédula</th>
                <th>Nombre y Apellido</th>
                <th>Correo</th>
                <th>Dirección</th>
                <th>Teléfono</th>
                <th>Fecha de Nacimiento</th>
                <th>Estatus</th>
            `;
            thead.innerHTML = columnas;
    
            // Filas
            datos.forEach(cliente => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${cliente.cedula}</td>
                    <td>${cliente.nombre} ${cliente.apellido}</td>
                    <td>${cliente.correo}</td>
                    <td>${cliente.direccion}</td>
                    <td>${cliente.telefono}</td>
                    <td>${new Date(cliente.fecha_nacimiento).toLocaleDateString()}</td>
                    <td>${cliente.estatus === 1 ? 'Activo' : 'Inactivo'}</td>
                `;
                tbody.appendChild(row);
            });
        }
        else if (tipo === 'productos') {
            thead.innerHTML = `
                <th>Emprendimiento</th>
                <th>Producto</th>
                <th>Precio</th>
                <th>Descripción</th>
                <th>Stock</th>
                <th>Fecha Ingreso</th>
                <th>Status</th>
                <th>Categoría</th>
            `;
        
            datos.forEach(p => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${p.emprendedor}</td>
                    <td>${p.nombre}</td>
                    <td>${p.precio}</td>
                    <td>${p.descripcion}</td>
                    <td>${p.stock}</td>
                    <td>${new Date(p.fecha_ingreso).toLocaleDateString()}</td>
                    <td>${p.status == 1 ? 'Activo' : 'Inactivo'}</td>
                    <td>${p.nombre_categoria}</td>
                `;
                tbody.appendChild(row);
            });
        }
        else if (tipo === 'ventas') {
            thead.innerHTML = `
                <th>ID Pedido</th>
                <th>Fecha</th>
                <th>Estatus</th>
                <th>Emprendedor</th>
                <th>Cliente</th>
                <th>Total</th>
            `;
        
            datos.forEach(v => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${v.id_pedidos}</td>
                    <td>${new Date(v.fecha_pedido).toLocaleDateString()}</td>
                    <td>${v.estatus}</td>
                    <td>${v.emprendedor_nombre}</td>
                    <td>${v.cliente_nombre}</td>
                    <td>$${parseFloat(v.total_pedido).toFixed(2)}</td>
                `;
                tbody.appendChild(row);
            });
        }
        
    
        
        if (thead.querySelectorAll('th').length > 0) {
            $('#reportsTable').DataTable({
                responsive: true,
                pageLength: 10,
                language: {
                  search: "Buscar:",
                  lengthMenu: "Mostrar _MENU_ registros por página",
                  zeroRecords: "No se encontraron resultados",
                  info: "Mostrando página _PAGE_ de _PAGES_",
                  infoEmpty: "No hay registros disponibles",
                  infoFiltered: "(filtrado de _MAX_ registros totales)",
                  paginate: {
                    first: "Primero",
                    last: "Último",
                    next: "Siguiente",
                    previous: "Anterior"
                  }
                }
              });
              
        }
    }
    
    // Evento para exportar reporte
    document.getElementById('exportReport').addEventListener('click', function() {
        const exportModal = new bootstrap.Modal(document.getElementById('exportModal'));
        exportModal.show();
    });
    
    // Confirmar exportación
    document.getElementById('confirmExport').addEventListener('click', function () {
        const format = document.getElementById('exportFormat').value;
        const reportType = document.getElementById('reportType').value;
    
        const table = document.getElementById('reportsTable');
        const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.innerText);
        const rows = Array.from(table.querySelectorAll('tbody tr')).map(tr =>
            Array.from(tr.querySelectorAll('td')).map(td => td.innerText)
        );
    
        const reportTitles = {
            clientes: 'Reporte de Clientes',
            productos: 'Reporte de Productos',
            ventas: 'Reporte de Ventas',            
        };

        const titulo = reportTitles[reportType] || 'Reporte General';

        fetch(API_CONFIG + '/reportes/exportar', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ format, headers, rows, titulo })
        })
        .then(response => {
            if (!response.ok) throw new Error('Error al exportar');
            return response.blob();
        })
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `reporte.${format === 'pdf' ? 'pdf' : format === 'excel' ? 'xlsx' : 'csv'}`;
            a.click();
            window.URL.revokeObjectURL(url);
        })
        .catch(error => mostrarAlerta('Error', error.message, 'error'));
    
        // Cierra el modal
        bootstrap.Modal.getInstance(document.getElementById('exportModal')).hide();
    });    
    
    function cargarGraficos() {
        fetch( API_CONFIG + `/reportes/obtenerDatosGraficosGlobales`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log(data.productos);

                    actualizarGraficoVentas(data.ventas);
                    actualizarGraficoEnvios(data.envios);
                    actualizarGraficoProductos(data.productos);
                } else {
                    console.error('Error en datos:', data.message);
                }
            })
            .catch(error => {
                console.error('Error al cargar los datos:', error);
            });
    }

    function actualizarGraficoVentas(ventas) {
        const labels = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        const emprendedores = [...new Set(ventas.map(v => v.emprendedor))];
    
        // Colores dinámicos para cada emprendedor
        const colores = [            
            '#006400',
            '#77c1cf',
            '#cc0e20',
            '#ffd700',
            '#8b4513',
            '#6c757d',
            '#351b08',
            '#408a59',
            'rgba(100, 255, 132, 0.7)',
            'rgba(255, 99, 132, 0.7)',
        ];
    
        const datasets = emprendedores.map((emprendedor, index) => {
            const data = Array(12).fill(0);
            ventas
                .filter(v => v.emprendedor === emprendedor)
                .forEach(v => {
                    data[v.mes - 1] = parseFloat(v.total_dolares);
                });
    
            return {
                label: emprendedor,
                data,
                backgroundColor: colores[index % colores.length],
                borderColor: colores[index % colores.length],
                borderWidth: 1
            };
        });
    
        // Actualizar el gráfico
        salesChart.data.labels = labels;
        salesChart.data.datasets = datasets;
        salesChart.update();
    }      
    
    function actualizarGraficoEnvios(envios) {
        const labels = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        const emprendedores = [...new Set(envios.map(e => e.emprendedor))];
    
        const colores = [
            '#006400',
            '#77c1cf',
            '#cc0e20',
            '#ffd700',
            '#8b4513',
            '#6c757d',
            '#351b08',
            '#408a59',
            'rgba(100, 255, 132, 0.7)',
            'rgba(255, 99, 132, 0.7)',
        ];
    
        const datasets = emprendedores.map((emprendedor, index) => {
            const data = Array(12).fill(0);
    
            envios
                .filter(e => e.emprendedor === emprendedor)
                .forEach(e => {
                    data[e.mes - 1] = parseInt(e.cantidad_envios);
                });
    
            return {
                label: emprendedor,
                data,
                fill: false,
                tension: 0.3,
                backgroundColor: colores[index % colores.length],
                borderColor: colores[index % colores.length],
                borderWidth: 2
            };
        });
    
        shipmentsChart.data.labels = labels;
        shipmentsChart.data.datasets = datasets;
        shipmentsChart.update();
    }
    
    function actualizarGraficoProductos(productos) {
        if (!window.detailChart) return;
    
        const labels = productos.map(e => e.emprendimiento);
        const valores = productos.map(e => parseInt(e.total_vendidos));
    
        const colores = [
            '#006400',
            '#77c1cf',
            '#cc0e20',
            '#ffd700',
            '#8b4513',
            '#6c757d',
            '#351b08',
            '#408a59',
            'rgba(100, 255, 132, 0.7)',
            'rgba(255, 99, 132, 0.7)',
        ];
    
        window.detailChart.data.labels = labels;
        window.detailChart.data.datasets = [{
            label: 'Productos vendidos',
            data: valores,
            backgroundColor: colores.slice(0, labels.length),
            borderWidth: 1
        }];
    
        window.detailChart.update();
    }
        

    // Inicializar gráficos principales
    function initCharts() {
        // Etiquetas para los 12 meses
        const monthLabels = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
    
        // Gráfico de ventas (inicialmente vacío)
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        window.salesChart = new Chart(salesCtx, {
            type: 'bar',
            data: {
                labels: monthLabels,
                datasets: [{
                    label: 'Ventas mensuales',
                    data: Array(12).fill(0), // Datos iniciales vacíos
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
                        font: { size: 16 }
                    },
                    legend: {
                        display: true,
                        position: 'top'  // puedes usar 'right', 'bottom', etc.
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: value => '$' + value.toLocaleString('es-VE')
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
                labels: monthLabels,
                datasets: [{
                    label: 'Envíos mensuales',
                    data: Array(12).fill(0),
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
                        text: 'Envíos Mensuales',
                        font: { size: 16 }
                    },
                    legend: {
                        display: true,
                        position: 'top'
                    }                    
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    
        // Gráfico de estados
        const detailCtx = document.getElementById('detailChart').getContext('2d');
        window.detailChart = new Chart(detailCtx, {
            type: 'doughnut',
            data: {
                labels: [],
                datasets: []
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Distribución de Productos Vendidos por Emprendimiento'
                    },
                    legend: {
                        display: true,
                        position: 'right'
                    }
                }
            }
        });  
    }        
});
  
function getRandomColor() {
    const r = Math.floor(Math.random() * 200);
    const g = Math.floor(Math.random() * 200);
    const b = Math.floor(Math.random() * 200);
    return `rgba(${r}, ${g}, ${b}, 0.7)`;
}