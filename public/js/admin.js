$(document).ready(function() {
    // Inicializar DataTable con AJAX
    const usersTable = $('#usersTable').DataTable({
        ajax: {
            url: window.location.href, // La misma URL del controlador
            type: 'GET',
            dataSrc: 'data'
        },
        columns: [
            { data: 'id_usuario' }, // Asegúrate que coincida con tus campos de BD
            { 
                data: 'imagen', // Campo de la imagen en tu tabla
                render: function(data) {
                    // Usa tu función asset() o APP_URL + ruta
                    return `<img src="${APP_URL + data}" class="user-avatar" alt="Foto de perfil">`;
                },
                orderable: false
            },
            { 
                data: null,
                render: function(data) {
                    return `${data.nombre} ${data.apellido}`; // Ajusta según tus campos
                }
            },
            { data: 'email' },
            { 
                data: 'rol', // O el campo que indique el rol
                render: function(data) {
                    // Puedes mapear valores si es necesario
                    const roles = {
                        'admin': 'Administrador',
                        'coord': 'Coordinador',
                        // ... otros mapeos
                    };
                    return roles[data] || data;
                }
            },
            { 
                data: 'estado', // Ajusta según tu campo de estado
                render: function(data) {
                    let badgeClass = 'status-badge ';
                    if (data === 'activo') badgeClass += 'status-active';
                    else if (data === 'inactivo') badgeClass += 'status-inactive';
                    else badgeClass += 'status-pending';
                    
                    return `<span class="${badgeClass}">${data}</span>`;
                }
            },
            { 
                data: null,
                render: function(data) {
                    return `
                        <div class="action-buttons">
                            <button class="btn btn-sm btn-edit edit-user" data-id="${data.id_usuario}">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button class="btn btn-sm btn-delete delete-user" 
                                    data-id="${data.id_usuario}" 
                                    data-name="${data.nombre} ${data.apellido}">
                                <i class="fas fa-trash-alt"></i> Eliminar
                            </button>
                        </div>
                    `;
                },
                orderable: false
            }
        ],
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        dom: '<"top"lf>rt<"bottom"ip>',
        pageLength: 10
    });

    // Manejar edición (usando AJAX)
    $(document).on('click', '.edit-user', function() {
        const userId = $(this).data('id');
        
        // Cargar datos del usuario
        $.get(`${APP_URL}user/getUser/${userId}`, function(user) {
            $('#editUserId').val(user.id_usuario);
            $('#editNombre').val(user.nombre);
            $('#editApellido').val(user.apellido);
            $('#editEmail').val(user.email);
            $('#editPerfil').val(user.perfil);
            $('#editEstado').val(user.estado);
            
            $('#editUserModal').modal('show');
        });
    });

    // Guardar cambios
    $('#saveUserChanges').click(function() {
        const formData = {
            id_usuario: $('#editUserId').val(),
            nombre: $('#editNombre').val(),
            apellido: $('#editApellido').val(),
            email: $('#editEmail').val(),
            perfil: $('#editPerfil').val(),
            estado: $('#editEstado').val(),
            pass: $('#editPassword').val() // Si aplica
        };

        $.post(`${APP_URL}user/actualizar`, formData, function(response) {
            if (response.success) {
                usersTable.ajax.reload();
                $('#editUserModal').modal('hide');
                showAlert('success', 'Usuario actualizado correctamente');
            } else {
                showAlert('error', response.message || 'Error al actualizar');
            }
        });
    });

    // Eliminar usuario
    $('#confirmDeleteUser').click(function() {
        const userId = $('#userToDeleteId').val();
        
        $.post(`${APP_URL}user/eliminar`, { 
            id_usuario: userId 
        }, function(response) {
            if (response.success) {
                usersTable.ajax.reload();
                $('#deleteUserModal').modal('hide');
                showAlert('success', 'Usuario eliminado correctamente');
            } else {
                showAlert('error', response.message || 'Error al eliminar');
            }
        });
    });

    // Función para mostrar alertas
    function showAlert(type, message) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        $('#alertsContainer').html(alertHtml);
    }
});