import { mostrarAlerta, mostrarAlertalogin } from './alertas.js';
import { API_CONFIG } from './config.js';

document.addEventListener('DOMContentLoaded', () => {
  const loginForm = document.getElementById('loginForm');
  const loginError = document.getElementById('loginError');
  const cedulaInput = document.getElementById('ced');
  const passwordInput = document.getElementById('pass');
  const cedulaMessage = document.getElementById('scedula');
  const forgotPasswordLink = document.querySelector('[data-password-reset-trigger="true"]')
    || document.querySelector('#loginModal .text-decoration-none');

  const loginModalElement = document.getElementById('loginModal');
  const resetModalElement = document.getElementById('passwordResetModal');
  const loginModal = loginModalElement && window.bootstrap
    ? window.bootstrap.Modal.getOrCreateInstance(loginModalElement)
    : null;
  const resetModal = resetModalElement && window.bootstrap
    ? window.bootstrap.Modal.getOrCreateInstance(resetModalElement)
    : null;

  const requestForm = document.getElementById('passwordResetRequestForm');
  const verifyForm = document.getElementById('passwordResetVerifyForm');
  const completeForm = document.getElementById('passwordResetCompleteForm');
  const resetCedulaInput = document.getElementById('passwordResetCedula');
  const resetCodeInput = document.getElementById('passwordResetCodigo');
  const resetPasswordInput = document.getElementById('passwordResetPassword');
  const resetPasswordConfirmInput = document.getElementById('passwordResetPasswordConfirm');
  const resetAlert = document.getElementById('passwordResetAlert');
  const resetEmailHint = document.getElementById('passwordResetEmailHint');
  const resetDescription = document.getElementById('passwordResetDescription');
  const changeCedulaButton = document.getElementById('passwordResetChangeCedulaButton');
  const closeToLoginButton = document.getElementById('passwordResetCloseToLoginButton');
  const resendButton = document.getElementById('passwordResetResendButton');
  const requestButton = document.getElementById('passwordResetRequestButton');
  const verifyButton = document.getElementById('passwordResetVerifyButton');
  const completeButton = document.getElementById('passwordResetCompleteButton');

  const resetFlow = {
    step: 1,
    cedula: '',
    maskedEmail: '',
  };

  if (loginForm && cedulaInput && passwordInput) {
    loginForm.addEventListener('submit', async (event) => {
      event.preventDefault();
      hideLoginError();

      const payload = {
        cedula: normalizeDigits(cedulaInput.value),
        password: passwordInput.value.trim(),
      };

      if (payload.cedula === '' || payload.password === '') {
        showLoginError('Debes completar la cedula y la contraseña.');
        return;
      }

      const submitButton = loginForm.querySelector('input[type="submit"], button[type="submit"]');
      const restoreButton = setButtonLoading(submitButton, true, 'Ingresando...');

      try {
        const response = await postJson('/home/api_login', payload);

        localStorage.setItem('jwt_token', response.token);
        if (loginModal) {
          loginModal.hide();
        }
        mostrarAlertalogin('Exito', 'Sesion iniciada correctamente', 'success');
        setTimeout(() => {
          window.location.href = `${API_CONFIG}/home/principal`;
        }, 1000);
      } catch (error) {
        console.error('Error en login:', error);
        showLoginError(error.message || 'Credenciales incorrectas.');
      } finally {
        restoreButton();
      }
    });
  }

  attachDigitsValidation(cedulaInput, cedulaMessage);
  attachDigitsValidation(resetCedulaInput);
  attachDigitsValidation(resetCodeInput);

  if (forgotPasswordLink && resetModal) {
    forgotPasswordLink.addEventListener('click', (event) => {
      event.preventDefault();
      hideLoginError();
      resetResetAlert();

      const initialCedula = normalizeDigits(cedulaInput ? cedulaInput.value : '');
      if (resetFlow.step === 1 && resetCedulaInput) {
        resetCedulaInput.value = initialCedula;
        resetCedulaInput.readOnly = false;
      } else if (resetCedulaInput && resetFlow.cedula !== '') {
        resetCedulaInput.value = resetFlow.cedula;
      }

      setResetStep(resetFlow.step);

      if (loginModal) {
        loginModal.hide();
      }
      resetModal.show();
    });
  }

  if (closeToLoginButton && resetModal) {
    closeToLoginButton.addEventListener('click', () => {
      resetModal.hide();
      if (loginModal) {
        loginModal.show();
      }
    });
  }

  if (changeCedulaButton) {
    changeCedulaButton.addEventListener('click', () => {
      resetFlow.step = 1;
      resetFlow.maskedEmail = '';
      if (resetCedulaInput) {
        resetCedulaInput.readOnly = false;
        resetCedulaInput.focus();
      }
      resetResetAlert();
      setResetStep(1);
    });
  }

  if (requestForm) {
    requestForm.addEventListener('submit', async (event) => {
      event.preventDefault();
      resetResetAlert();

      const cedula = normalizeDigits(resetCedulaInput ? resetCedulaInput.value : '');
      if (cedula.length < 6) {
        showResetAlert('Debes indicar una cedula valida.', 'danger');
        if (resetCedulaInput) {
          resetCedulaInput.focus();
        }
        return;
      }

      const restoreButton = setButtonLoading(requestButton, true, 'Enviando...');

      try {
        const response = await postJson('/home/request_password_reset_code', { cedula });
        resetFlow.step = 2;
        resetFlow.cedula = cedula;
        resetFlow.maskedEmail = response.masked_email || '';

        if (resetCedulaInput) {
          resetCedulaInput.value = cedula;
          resetCedulaInput.readOnly = true;
        }

        if (resetCodeInput) {
          resetCodeInput.value = '';
          resetCodeInput.focus();
        }

        showResetAlert(response.message || 'Enviamos el código de verificación.', 'success');
        setResetStep(2);
      } catch (error) {
        console.error('Error solicitando código:', error);
        showResetAlert(error.message || 'No se pudo enviar el código.', 'danger');
      } finally {
        restoreButton();
      }
    });
  }

  if (resendButton) {
    resendButton.addEventListener('click', async () => {
      resetResetAlert();
      const cedula = resetFlow.cedula || normalizeDigits(resetCedulaInput ? resetCedulaInput.value : '');

      if (cedula.length < 6) {
        showResetAlert('Debes indicar una cedula valida.', 'danger');
        setResetStep(1);
        return;
      }

      const restoreButton = setButtonLoading(resendButton, true, 'Reenviando...');

      try {
        const response = await postJson('/home/request_password_reset_code', { cedula });
        resetFlow.step = 2;
        resetFlow.cedula = cedula;
        resetFlow.maskedEmail = response.masked_email || '';
        showResetAlert(response.message || 'Codigo reenviado correctamente.', 'success');
        setResetStep(2);
      } catch (error) {
        console.error('Error reenviando codigo:', error);
        showResetAlert(error.message || 'No se pudo reenviar el codigo.', 'danger');
      } finally {
        restoreButton();
      }
    });
  }

  if (verifyForm) {
    verifyForm.addEventListener('submit', async (event) => {
      event.preventDefault();
      resetResetAlert();

      const cedula = resetFlow.cedula || normalizeDigits(resetCedulaInput ? resetCedulaInput.value : '');
      const codigo = normalizeDigits(resetCodeInput ? resetCodeInput.value : '');

      if (cedula.length < 6 || codigo.length !== 6) {
        showResetAlert('Ingresa la cedula y el codigo de 6 digitos.', 'danger');
        return;
      }

      const restoreButton = setButtonLoading(verifyButton, true, 'Verificando...');

      try {
        const response = await postJson('/home/verify_password_reset_code', { cedula, codigo });
        resetFlow.step = 3;
        showResetAlert(response.message || 'Codigo verificado correctamente.', 'success');
        setResetStep(3);
        if (resetPasswordInput) {
          resetPasswordInput.focus();
        }
      } catch (error) {
        console.error('Error verificando codigo:', error);
        showResetAlert(error.message || 'No se pudo verificar el codigo.', 'danger');
      } finally {
        restoreButton();
      }
    });
  }

  if (completeForm) {
    completeForm.addEventListener('submit', async (event) => {
      event.preventDefault();
      resetResetAlert();

      const cedula = resetFlow.cedula || normalizeDigits(resetCedulaInput ? resetCedulaInput.value : '');
      const password = resetPasswordInput ? resetPasswordInput.value.trim() : '';
      const confirmPassword = resetPasswordConfirmInput ? resetPasswordConfirmInput.value.trim() : '';

      if (password.length < 8) {
        showResetAlert('La nueva contraseña debe tener al menos 8 caracteres.', 'danger');
        return;
      }

      if (password !== confirmPassword) {
        showResetAlert('Las contraseñas no coinciden.', 'danger');
        return;
      }

      const restoreButton = setButtonLoading(completeButton, true, 'Guardando...');

      try {
        const response = await postJson('/home/complete_password_reset', {
          cedula,
          password,
          confirm_password: confirmPassword,
        });

        showResetAlert(response.message || 'Contraseña actualizada correctamente.', 'success');
        clearResetFlow();

        if (resetModal) {
          resetModal.hide();
        }

        if (cedulaInput) {
          cedulaInput.value = cedula;
        }

        if (loginModal) {
          loginModal.show();
        }

        mostrarAlerta('Contraseña actualizada', 'Ya puedes iniciar sesion con tu nueva contraseña.', 'success');
      } catch (error) {
        console.error('Error actualizando contraseña:', error);
        showResetAlert(error.message || 'No se pudo actualizar la contraseña.', 'danger');
      } finally {
        restoreButton();
      }
    });
  }

  if (resetModalElement) {
    resetModalElement.addEventListener('hidden.bs.modal', () => {
      hideLoginError();
    });
  }

  function setResetStep(step) {
    resetFlow.step = step;

    const sections = document.querySelectorAll('[data-reset-step]');
    sections.forEach((section) => {
      const sectionStep = Number(section.getAttribute('data-reset-step'));
      section.classList.toggle('d-none', sectionStep !== step);
    });

    if (changeCedulaButton) {
      changeCedulaButton.classList.toggle('d-none', step === 1);
    }

    if (resetEmailHint) {
      const shouldShowEmail = resetFlow.maskedEmail !== '' && step >= 2;
      resetEmailHint.classList.toggle('d-none', !shouldShowEmail);
      resetEmailHint.textContent = shouldShowEmail
        ? `Código enviado a ${resetFlow.maskedEmail}.`
        : '';
    }

    if (resetDescription) {
      const descriptions = {
        1: 'Te enviaremos un código al correo asociado a la cédula.',
        2: 'Revisa tu correo e ingresa el código recibido.',
        3: 'Crea una nueva contraseña para tu cuenta.',
      };
      resetDescription.textContent = descriptions[step] || descriptions[1];
    }
  }

  function clearResetFlow() {
    resetFlow.step = 1;
    resetFlow.cedula = '';
    resetFlow.maskedEmail = '';

    if (requestForm) {
      requestForm.reset();
    }
    if (verifyForm) {
      verifyForm.reset();
    }
    if (completeForm) {
      completeForm.reset();
    }
    if (resetCedulaInput) {
      resetCedulaInput.readOnly = false;
    }

    resetResetAlert();
    setResetStep(1);
  }

  function showLoginError(message) {
    if (loginError) {
      loginError.textContent = message;
      loginError.classList.remove('d-none');
    }
    if (cedulaInput) {
      cedulaInput.classList.add('is-invalid');
    }
    if (passwordInput) {
      passwordInput.classList.add('is-invalid');
    }
  }

  function hideLoginError() {
    if (loginError) {
      loginError.classList.add('d-none');
      loginError.textContent = '';
    }
    if (cedulaInput) {
      cedulaInput.classList.remove('is-invalid');
    }
    if (passwordInput) {
      passwordInput.classList.remove('is-invalid');
    }
  }

  function showResetAlert(message, type = 'info') {
    if (!resetAlert) {
      return;
    }
    resetAlert.className = `alert alert-${type}`;
    resetAlert.textContent = message;
    resetAlert.classList.remove('d-none');
  }

  function resetResetAlert() {
    if (!resetAlert) {
      return;
    }
    resetAlert.className = 'alert d-none';
    resetAlert.textContent = '';
  }
});

function attachDigitsValidation(input, messageElement = null) {
  if (!input) {
    return;
  }

  input.addEventListener('input', () => {
    const digitsOnly = normalizeDigits(input.value);
    if (input.value !== digitsOnly) {
      input.value = digitsOnly;
    }

    if (messageElement) {
      messageElement.textContent = '';
      messageElement.style.color = '';
    }
  });

  input.addEventListener('paste', (event) => {
    const pastedData = (event.clipboardData || window.clipboardData).getData('text');
    if (normalizeDigits(pastedData) !== pastedData) {
      event.preventDefault();
      if (messageElement) {
        messageElement.textContent = 'Solo se permite el ingreso de numeros';
        messageElement.style.color = 'red';
      }
    }
  });
}

function normalizeDigits(value) {
  return String(value || '').replace(/\D+/g, '');
}

function setButtonLoading(button, isLoading, loadingText) {
  if (!button) {
    return () => {};
  }

  const originalText = button.tagName === 'INPUT' ? button.value : button.textContent;
  button.disabled = isLoading;

  if (isLoading) {
    if (button.tagName === 'INPUT') {
      button.value = loadingText;
    } else {
      button.textContent = loadingText;
    }
  }

  return () => {
    button.disabled = false;
    if (button.tagName === 'INPUT') {
      button.value = originalText;
    } else {
      button.textContent = originalText;
    }
  };
}

async function postJson(path, payload) {
  const response = await fetch(`${API_CONFIG}${path}`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      Accept: 'application/json',
    },
    body: JSON.stringify(payload),
  });

  let data = {};
  try {
    data = await response.json();
  } catch (error) {
    data = {};
  }

  if (!response.ok || data.success === false) {
    throw new Error(data.message || 'No se pudo completar la solicitud.');
  }

  return data;
}
