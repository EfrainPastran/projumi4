import { mostrarAlertalogin } from './alertas.js';
import { API_CONFIG } from './config.js';
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('loginForm');
    const errorDiv = document.getElementById('loginError');

    form.addEventListener('submit', function (e) {
        e.preventDefault(); // Prevenir envío normal

        const formData = new FormData(form);

        fetch(API_CONFIG + '/home/api_login', { // API para el login
            method: 'POST',
            body: formData
        })
        .then(response => response.json()) // Esperamos JSON
        .then(data => {
            if (data.success) {
                // Mostrar modal de éxito
                mostrarAlertalogin('¡Éxito!', 'Sesión iniciada correctamente', 'success');            
                setTimeout(() => {
                    window.location.href = API_CONFIG + '/home/principal';
                }, 1000); // Espera 1 segundos antes de redirigir
            } else {
                errorDiv.textContent = data.message;
                errorDiv.classList.remove('d-none');
            
                // Pintar los campos en rojo
                document.getElementById('ced').classList.add('is-invalid');
                document.getElementById('pass').classList.add('is-invalid');
            }            
        })
        .catch(err => {
            console.error('Error:', err);
            errorDiv.textContent = 'Ocurrió un error inesperado.';
            errorDiv.classList.remove('d-none');
        });
    });
});
const cedulaInput = document.getElementById('ced');
const cedulaMsg = document.getElementById('scedula');

// Mostrar mensaje de error
function mostrarError(mensaje) {
    cedulaMsg.textContent = mensaje;
    cedulaMsg.style.color = 'red';
}

// Limpiar mensaje
function limpiarError() {
    cedulaMsg.textContent = '';
}

// Validar en cada pulsación
cedulaInput.addEventListener('keydown', function (e) {
    const allowedKeys = ['Backspace', 'Tab', 'Delete', 'ArrowLeft', 'ArrowRight', 'Home', 'End'];

    // Permitir Ctrl+A, Ctrl+C, Ctrl+V, etc.
    if ((e.ctrlKey || e.metaKey) && ['a', 'c', 'v', 'x'].includes(e.key.toLowerCase())) {
        return;
    }

    // Permitir números y teclas especiales
    if (/^\d$/.test(e.key) || allowedKeys.includes(e.key)) {
        limpiarError();
        return;
    }

    // Si no es permitido, prevenir e ingresar mensaje
    e.preventDefault();
    mostrarError('Solo se permite el ingreso de números');
});

// Validar al pegar
cedulaInput.addEventListener('paste', function (e) {
    const pastedData = (e.clipboardData || window.clipboardData).getData('text');

    if (!/^\d+$/.test(pastedData)) {
        e.preventDefault();
        mostrarError('Solo se permite el ingreso de números');
    } else {
        limpiarError();
    }
});

// Validar si el usuario borra letras usando desarrollador o navegador
cedulaInput.addEventListener('input', function () {
    if (/^\d*$/.test(cedulaInput.value)) {
        limpiarError();
    } else {
        mostrarError('Solo se permite el ingreso de números');
    }
});


