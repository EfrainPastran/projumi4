import { API_CONFIG } from './config.js';
$(document).ready(function() {
    let logData = [];

    const bitacoraTable = $('#bitacoraTable').DataTable({
        ajax: {
            url:  API_CONFIG + '/bitacora/mostrarBitacora', // Asegúrate de que esta ruta es correcta
            dataSrc: function(json) {
                logData = json;
                return json;
            }
        },
        columns: [
            { data: 'id' },
            { 
                data: 'fecha_registro',
                render: function(data) {
                    return `<span class="fecha-hora">${data}</span>`;
                }
            },
            { data: 'usuario_nombre' }, // Asegúrate que este nombre venga desde el backend
            { data: 'modulo_accionado' }
        ],
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        pageLength: 10,
        order: [[0, 'desc']]
    });

    // Mostrar detalles en modal (opcional)
    $('#bitacoraTable tbody').on('click', 'tr', function() {
        const rowData = bitacoraTable.row(this).data();
        showDetails(rowData.id);
    });

    function showDetails(logId) {
        const logEntry = logData.find(entry => entry.id == logId);
        if (logEntry) {
            $('#detailId').text(logEntry.id);
            $('#detailDate').text(logEntry.fecha_registro);
            $('#detailUser').text(logEntry.usuario_nombre);
            $('#detailModule').text(logEntry.modulo_accionado);
            $('#detailIp').text(logEntry.ip || 'N/A');
            $('#detailFull pre').text(JSON.stringify(logEntry, null, 2));

            $('#detailModal').modal('show');
        }
    }

    // Filtros (si decides conservarlos)
    $('#applyFilters').click(function() {
        const user = $('#filterUser').val();
        const module = $('#filterModule').val();

        if (user) bitacoraTable.columns(2).search(user);
        if (module) bitacoraTable.columns(3).search(module);

        bitacoraTable.draw();
        $('#filterModal').modal('hide');
    });

    $('#resetFilters').click(function() {
        $('#filterForm')[0].reset();
        bitacoraTable.search('').columns().search('').draw();
    });
});
