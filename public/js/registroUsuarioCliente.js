import { API_CONFIG } from './config.js';

const PHONE_CODES = ['0412', '0422', '0416', '0426', '0414', '0424'];
const ADULT_AGE = 18;

const onlyDigits = (value) => String(value || '').replace(/\D/g, '');
const onlyLetters = (value) =>
  String(value || '').replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, '');

function getField(fieldId) {
  return document.getElementById(fieldId);
}

function showError(fieldId, message) {
  const input = fieldId === 'telefono' && getField('telefono_numero')
    ? getField('telefono_numero')
    : getField(fieldId);
  const errorDiv = getField(`error-${fieldId}`);

  if (input) input.classList.add('is-invalid');
  if (errorDiv) errorDiv.textContent = message;
}

function clearError(fieldId) {
  const input = fieldId === 'telefono' && getField('telefono_numero')
    ? getField('telefono_numero')
    : getField(fieldId);
  const errorDiv = getField(`error-${fieldId}`);

  if (input) input.classList.remove('is-invalid');
  if (errorDiv) errorDiv.textContent = '';
}

function calculateAge(dateValue) {
  if (!dateValue) return null;

  const birthDate = new Date(`${dateValue}T00:00:00`);
  if (Number.isNaN(birthDate.getTime())) return null;

  const today = new Date();
  let age = today.getFullYear() - birthDate.getFullYear();
  const monthDiff = today.getMonth() - birthDate.getMonth();

  if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
    age--;
  }

  return age;
}

function getAdultMaxDate() {
  const today = new Date();
  today.setFullYear(today.getFullYear() - ADULT_AGE);
  return today.toISOString().slice(0, 10);
}

function syncPhoneHiddenField() {
  const codeInput = getField('telefono_codigo');
  const numberInput = getField('telefono_numero');
  const hiddenInput = getField('telefono');

  if (!codeInput || !numberInput || !hiddenInput) return;

  hiddenInput.value = `${codeInput.value}${onlyDigits(numberInput.value).slice(0, 7)}`;
}

function validarCedula() {
  const input = getField('cedula');
  if (!input) return true;

  input.value = onlyDigits(input.value).slice(0, 10);

  const valido = /^\d{7,10}$/.test(input.value);
  if (!valido) showError('cedula', 'Ingrese una cedula valida de 7 a 10 digitos.');
  else clearError('cedula');

  return valido;
}

function validarFechaNacimiento() {
  const input = getField('fecha_nacimiento');
  const ageInput = getField('edad');
  if (!input) return true;

  const age = calculateAge(input.value);
  if (ageInput) ageInput.value = age !== null && age >= 0 ? age : '';

  if (age === null) {
    showError('fecha_nacimiento', 'Ingrese una fecha de nacimiento valida.');
    return false;
  }

  if (age < ADULT_AGE) {
    showError('fecha_nacimiento', 'El cliente debe ser mayor de edad.');
    return false;
  }

  clearError('fecha_nacimiento');
  return true;
}

function validarNombreApellido(fieldId) {
  const input = getField(fieldId);
  if (!input) return true;

  input.value = onlyLetters(input.value).replace(/\s{2,}/g, ' ').slice(0, 45);

  const valido = /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{2,45}$/.test(input.value.trim());
  if (!valido) showError(fieldId, 'Solo letras, entre 2 y 45 caracteres.');
  else clearError(fieldId);

  return valido;
}

function validarTelefono() {
  const codeInput = getField('telefono_codigo');
  const numberInput = getField('telefono_numero');

  if (!codeInput || !numberInput) {
    const legacyInput = getField('telefono');
    if (!legacyInput) return true;
    legacyInput.value = onlyDigits(legacyInput.value).slice(0, 11);
    const legacyValid = /^04\d{9}$/.test(legacyInput.value);
    if (!legacyValid) showError('telefono', 'Ingrese un telefono valido de 11 digitos.');
    else clearError('telefono');
    return legacyValid;
  }

  numberInput.value = onlyDigits(numberInput.value).slice(0, 7);
  syncPhoneHiddenField();

  const valido = PHONE_CODES.includes(codeInput.value) && /^\d{7}$/.test(numberInput.value);
  if (!valido) showError('telefono', 'Seleccione un codigo e ingrese 7 digitos.');
  else clearError('telefono');

  return valido;
}

function validarEmail() {
  const input = getField('email');
  if (!input) return true;

  input.value = input.value.trim().slice(0, 45);

  const valido = input.value.length <= 45 && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(input.value);
  if (!valido) showError('email', 'Correo invalido. Maximo 45 caracteres.');
  else clearError('email');

  return valido;
}

function validarDireccion() {
  const input = getField('direccion');
  if (!input) return true;

  input.value = input.value.slice(0, 60);

  const length = input.value.trim().length;
  if (length < 5 || length > 60) {
    showError('direccion', 'La direccion debe tener entre 5 y 60 caracteres.');
    return false;
  }

  clearError('direccion');
  return true;
}

function validarPassword() {
  const passInput = getField('password');
  const confirmInput = getField('confirm_pass');
  if (!passInput || !confirmInput) return true;

  if (passInput.value.length < 8) {
    showError('password', 'La contrasena debe tener minimo 8 caracteres.');
    return false;
  }
  clearError('password');

  if (passInput.value !== confirmInput.value) {
    showError('confirm_pass', 'Las contrasenas no coinciden.');
    return false;
  }
  clearError('confirm_pass');

  return true;
}

function validarFormulario() {
  const checks = [
    validarCedula(),
    validarFechaNacimiento(),
    validarNombreApellido('nombre'),
    validarNombreApellido('apellido'),
    validarTelefono(),
    validarEmail(),
    validarDireccion(),
    validarPassword(),
  ];

  return checks.every(Boolean);
}

function setupBirthDateLimit() {
  const input = getField('fecha_nacimiento');
  if (!input) return;

  input.max = getAdultMaxDate();
}

function setupListeners() {
  const cedula = getField('cedula');
  const fechaNacimiento = getField('fecha_nacimiento');
  const nombre = getField('nombre');
  const apellido = getField('apellido');
  const telefonoCodigo = getField('telefono_codigo');
  const telefonoNumero = getField('telefono_numero');
  const telefonoLegacy = getField('telefono');
  const email = getField('email');
  const direccion = getField('direccion');
  const password = getField('password');
  const confirmPass = getField('confirm_pass');

  cedula?.addEventListener('input', validarCedula);
  fechaNacimiento?.addEventListener('change', validarFechaNacimiento);
  nombre?.addEventListener('input', () => validarNombreApellido('nombre'));
  apellido?.addEventListener('input', () => validarNombreApellido('apellido'));
  telefonoCodigo?.addEventListener('change', validarTelefono);
  telefonoNumero?.addEventListener('input', validarTelefono);
  telefonoLegacy?.addEventListener('input', validarTelefono);
  email?.addEventListener('input', validarEmail);
  direccion?.addEventListener('input', validarDireccion);
  password?.addEventListener('input', validarPassword);
  confirmPass?.addEventListener('input', validarPassword);
}

$(document).ready(function () {
  setupBirthDateLimit();
  setupListeners();
  syncPhoneHiddenField();

  $('#formRegistrarUsuarioCliente').on('submit', function (e) {
    e.preventDefault();

    if (!validarFormulario()) {
      Swal.fire('Datos invalidos', 'Revise los campos marcados antes de continuar.', 'warning');
      return;
    }

    const submitButton = $(this).find('button[type="submit"], input[type="submit"]');
    submitButton.prop('disabled', true);

    $.ajax({
      type: 'POST',
      url: API_CONFIG + '/auth/registrarUsuarioCliente',
      data: $(this).serialize(),
      dataType: 'json',
      success: function (res) {
        if (res.success) {
          Swal.fire({
            title: 'Registro exitoso',
            text: 'Puede iniciar sesion',
            icon: 'success',
            confirmButtonText: 'Iniciar sesion',
          }).then(() => {
            window.location.href = API_CONFIG + '/home/principal';
          });
        } else {
          Swal.fire({
            title: 'Error',
            text: res.message || 'No se pudo registrar el cliente.',
            icon: 'error',
          });
        }
      },
      error: function (xhr) {
        const message =
          xhr.responseJSON?.message ||
          xhr.responseText ||
          'No se pudo registrar el cliente.';

        Swal.fire('Error de servidor', message, 'error');
      },
      complete: function () {
        submitButton.prop('disabled', false);
      },
    });
  });
});
