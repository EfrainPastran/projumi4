// Alerta básica
export function mostrarAlerta(titulo, mensaje, tipo = 'info') {
  return Swal.fire({
    title: `<span class="fw-bold">${titulo}</span>`,
    text: mensaje,
    icon: tipo,
    confirmButtonText: 'Aceptar',
    customClass: {
      confirmButton: 'btn btn-success px-4 fw-bold',
      popup: 'rounded-4 shadow',
      title: 'fs-4',
      icon: 'mt-2'
    },
    buttonsStyling: false,
    background: '#f8f9fa'
  });
}

export function mostrarAlertalogin(titulo, mensaje, tipo = 'info') {
  return Swal.fire({
    title: `<span class="fw-bold">${titulo}</span>`,
    text: mensaje,
    icon: tipo,
    showConfirmButton: false,
    customClass: {
      confirmButton: 'btn btn-success px-4 fw-bold',
      popup: 'rounded-4 shadow',
      title: 'fs-4',
      icon: 'mt-2'
    },
    buttonsStyling: false,
    background: '#f8f9fa'
  });
}

// Alerta con confirmación
export function mostrarConfirmacion(titulo, mensaje, callback) {
  Swal.fire({
    title: `<span class="fw-bold">${titulo}</span>`,
    text: mensaje,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí',
    cancelButtonText: 'Cancelar',
    customClass: {
      confirmButton: 'btn btn-success px-4 fw-bold',
      cancelButton: 'btn btn-outline-secondary px-4 fw-bold ms-2',
      popup: 'rounded-4 shadow',
      title: 'fs-4',
      icon: 'mt-2'
    },
    buttonsStyling: false,
    background: '#f8f9fa'
  }).then((result) => {
    if (result.isConfirmed) {
      callback(true);
    } else {
      callback(false);
    }
  });
}
// ...existing code...

// Alerta personalizada con HTML
export function alertaPersonalizada(opciones) {
  return Swal.fire(opciones);
}
