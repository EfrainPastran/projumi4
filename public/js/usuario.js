import { mostrarAlerta, mostrarAlertalogin } from './alertas.js';
import { API_CONFIG } from './config.js';

$(document).ready(function() {
    // Limpiar mensajes flash después de 5 segundos
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
    
    // Modal de edición
    $('#editarUsuarioModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var modal = $(this);
        
        modal.find('#idUsuarioEditar').val(button.data('id'));
        modal.find('#cedulaEditar').val(button.data('cedula'));
        modal.find('#nombreEditar').val(button.data('nombre'));
        modal.find('#apellidoEditar').val(button.data('apellido'));
        modal.find('#correoEditar').val(button.data('correo'));
        modal.find('#direccionEditar').val(button.data('direccion'));
        modal.find('#telefonoEditar').val(button.data('telefono'));
        modal.find('#fecha_nacimientoEditar').val(button.data('fecha_nacimiento'));
        modal.find('#estatusEditar').val(button.data('estatus'));
        modal.find('#fk_rolEditar').val(button.data('fk_rol'));
    });
    
    // Modal de eliminación
    $('#eliminarUsuarioModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var modal = $(this);
        
        modal.find('#idUsuarioEliminar').val(button.data('id'));
        modal.find('#nombreUsuarioEliminar').text(button.data('nombre'));
    });
    
    // Modal de visualización
    $('#verUsuarioModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var userId = button.data('id');
        var modal = $(this);
        
        // Cargar detalles via AJAX
        $.get(API_CONFIG+'/usuarios/view/' + userId, function(data) {
            modal.find('#detallesUsuario').html(data);
        }).fail(function() {
            modal.find('#detallesUsuario').html('<div class="alert alert-danger">Error al cargar los detalles</div>');
        });
    });
    
    // Búsqueda simple
    $('#btnBuscar').click(function() {
        var searchText = $('#buscarUsuario').val().toLowerCase();
        $('table tbody tr').each(function() {
            var rowText = $(this).text().toLowerCase();
            $(this).toggle(rowText.indexOf(searchText) > -1);
        });
    });
    
    $('#buscarUsuario').keyup(function(e) {
        if (e.keyCode === 13) {
            $('#btnBuscar').click();
        }
    });
});

document.querySelector('#eliminarUsuarioForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);

    try {
        const response = await fetch(API_CONFIG+'/usuarios/delete', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const result = await response.json();

        if (result.success) {
            mostrarAlertalogin('Usuario','Usuario eliminado correctamente','success');
            setTimeout(() => {
                window.location.href = API_CONFIG + '/usuarios/index';
            }, 1000); // Espera 1 segundos antes de redirigir  
        } else {
            mostrarAlerta('Usuario', result.message, 'warning');
        }
    } catch (err) {
        mostrarAlerta('Usuario', 'Error al conectar con el servidor.', 'error');
    }
});
document.querySelector('#agregarUsuarioModal form').addEventListener('submit', async function (e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);

try {
    const response = await fetch(API_CONFIG + '/usuarios/register', {
        method: 'POST',
        body: formData
    });

    const result = await response.json();

    if (!response.ok || !result.success) {
        mostrarAlerta('Usuario', result.message || 'Error al registrar usuario', 'warning');
        return;
    }

    mostrarAlertalogin('Usuario', 'Usuario registrado correctamente', 'success');
    setTimeout(() => {
        window.location.href = API_CONFIG + '/usuarios/index';
    }, 1000);

} catch (err) {
    mostrarAlerta('Usuario', 'Error al conectar con el servidor.', 'error');
}

});

document.querySelector('#editarUsuarioForm').addEventListener('submit', async function(e) {
e.preventDefault();

const form = e.target;
const formData = new FormData(form);

try {
const response = await fetch(API_CONFIG+'/usuarios/update', {
    method: 'POST',
    body: formData,
    headers: {
        'X-Requested-With': 'XMLHttpRequest'
    }
});

const result = await response.json();

if (result.success) {
    mostrarAlertalogin('Usuario','Usuario actualizado correctamente','success');
    setTimeout(() => {
        window.location.href = API_CONFIG + '/usuarios/index';
    }, 1000); // Espera 1 segundos antes de redirigir        
} else {
    mostrarAlerta('Usuario', result.message, 'warning');
}

} catch (err) {
    mostrarAlerta('Usuario', 'Error al conectar con el servidor.', 'error');
}
});


function showError(fieldId, message) {
    const input = document.getElementById(fieldId);
    const errorDiv = document.getElementById(`error-${fieldId}`);
    input.classList.add('is-invalid');
    if (errorDiv) errorDiv.textContent = message;
}

function clearError(fieldId) {
    const input = document.getElementById(fieldId);
    const errorDiv = document.getElementById(`error-${fieldId}`);
    input.classList.remove('is-invalid');
    if (errorDiv) errorDiv.textContent = '';
}

// Validaciones individuales
function validarCedula() {
    const valor = document.getElementById("cedula").value.trim();
    const valido = /^\d{7,10}$/.test(valor);
    if (!valido) showError("cedula", "Ingrese una cédula válida (7 a 10 dígitos).");
    else clearError("cedula");
    return valido;
}

function validarNombreApellido(id) {
    const valor = document.getElementById(id).value.trim();
    const valido = /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{3,50}$/.test(valor);
    if (!valido) showError(id, "Solo letras. Mínimo 3 caracteres.");
    else clearError(id);
    return valido;
}

function validarTelefono() {
    const valor = document.getElementById("telefono").value.trim();
    const valido = /^\d{8,15}$/.test(valor);
    if (!valido) showError("telefono", "Teléfono inválido (8 a 15 dígitos).");
    else clearError("telefono");
    return valido;
}

function validarCorreo() {
    const valor = document.getElementById("correo").value.trim();
    const valido = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(valor);
    if (!valido) showError("correo", "Correo inválido.");
    else clearError("correo");
    return valido;
}

function validarDireccion() {
    const valor = document.getElementById("direccion").value.trim();
    if (valor.length < 5) {
        showError("direccion", "Dirección muy corta.");
        return false;
    }
    clearError("direccion");
    return true;
}

function validarPassword() {
    const pass = document.getElementById("password").value;
    if (pass.length < 8) {
        showError("password", "La contraseña debe tener mínimo 8 caracteres.");
        return false;
    }
    clearError("password");
    return true;
}

function validarFechaNacimiento() {
    const valor = document.getElementById("fecha_nacimiento").value;
    if (!valor) {
        showError("fecha_nacimiento", "Debe ingresar una fecha.");
        return false;
    }
    const hoy = new Date();
    const fecha = new Date(valor);
    if (fecha > hoy) {
        showError("fecha_nacimiento", "La fecha no puede ser futura.");
        return false;
    }
    clearError("fecha_nacimiento");
    return true;
}

// Escuchar eventos individuales
document.getElementById("cedula").addEventListener("input", validarCedula);
document.getElementById("nombre").addEventListener("input", () => validarNombreApellido("nombre"));
document.getElementById("apellido").addEventListener("input", () => validarNombreApellido("apellido"));
document.getElementById("telefono").addEventListener("input", validarTelefono);
document.getElementById("correo").addEventListener("input", validarCorreo);
document.getElementById("direccion").addEventListener("input", validarDireccion);
document.getElementById("password").addEventListener("input", validarPassword);
document.getElementById("fecha_nacimiento").addEventListener("change", validarFechaNacimiento);