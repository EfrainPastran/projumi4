import { API_CONFIG } from './config.js';
$(document).ready(function () {
  $('#formRegistrarUsuarioCliente').on('submit', function (e) {
    e.preventDefault();

    $.ajax({
      type: 'POST',
      url:  API_CONFIG + '/auth/registrarUsuarioCliente',
      data: $(this).serialize(),
      dataType: 'json',
      success: function (res) {
        if (res.success) {
          Swal.fire({
            title: '¡Registro exitoso!',
            text: 'Puede iniciar sesión',
            icon: 'success',
            confirmButtonText: 'Iniciar sesión',
          }).then(() => {
            window.location.href =  API_CONFIG + '/home/principal';
          });
        } else {
          Swal.fire({
            title: 'Error',
            text: res.message,
            icon: 'error',
          });
        }
      },
      error: function () {
        Swal.fire( 'Error de servidor', 'No se pudo registrar', 'error');
      },
    });
  });
  // Calcular edad automáticamente
  const fechaNacimientoInput = document.getElementById('fecha_nacimiento');
  if (fechaNacimientoInput) {
    fechaNacimientoInput.addEventListener('change', function () {
      const fechaNacimiento = new Date(this.value);
      const hoy = new Date();
      let edad = hoy.getFullYear() - fechaNacimiento.getFullYear();
      const mes = hoy.getMonth() - fechaNacimiento.getMonth();

      if (mes < 0 || (mes === 0 && hoy.getDate() < fechaNacimiento.getDate())) {
        edad--;
      }

      document.getElementById('edad').value = edad;
    });
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

function validarEmail() {
  const valor = document.getElementById("email").value.trim();
  const valido = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(valor);
  if (!valido) showError("email", "Correo inválido.");
  else clearError("email");
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
  const confirm = document.getElementById("confirm_pass").value;
  if (pass.length < 8) {
      showError("password", "La contraseña debe tener mínimo 8 caracteres.");
      return false;
  }
  clearError("password");

  if (pass !== confirm) {
      showError("confirm_pass", "Las contraseñas no coinciden.");
      return false;
  }
  clearError("confirm_pass");
  return true;
}

document.getElementById("cedula").addEventListener("input", validarCedula);
document.getElementById("nombre").addEventListener("input", () => validarNombreApellido("nombre"));
document.getElementById("apellido").addEventListener("input", () => validarNombreApellido("apellido"));
document.getElementById("telefono").addEventListener("input", validarTelefono);
document.getElementById("email").addEventListener("input", validarEmail);
document.getElementById("direccion").addEventListener("input", validarDireccion);
document.getElementById("password").addEventListener("input", validarPassword);
document.getElementById("confirm_pass").addEventListener("input", validarPassword);
