import { mostrarAlerta, mostrarConfirmacion } from './alertas.js';
import { API_CONFIG } from './config.js';
document.addEventListener("DOMContentLoaded", function () {
  function getCategoryName(key) {
    const categories = {
      artesanias: "Artesanías",
      alimentos: "Alimentos",
      textiles: "Textiles",
      joyeria: "Joyería",
      Manualidades: "Manualidades",
    };
    return categories[key] ?? key;
  }

  function verificarCarritoExpirado() {
    const expiracion = parseInt(localStorage.getItem("carritoExpiracion"), 10);
    const ahora = Date.now();

    if (!expiracion || ahora > expiracion) {
      return true;
    }
    return false;
  }

// Evitar manualmente valores fuera del rango (input directo)
document.addEventListener('input', function (e) {
    if (e.target.classList.contains('cantidad-input')) {
        const input = e.target;
        const max = parseInt(input.getAttribute('max'));
        let value = parseInt(input.value);

        if (isNaN(value) || value < 1) {
            input.value = 1;
        } else if (value > max) {
            input.value = max;
        }
    }
});
  function renderCarritoTable(products) {
    const tbody = document.getElementById("carritoBody");
    let total = 0;
    tbody.innerHTML = "";
    products.forEach((product) => {
      const cantidad = product.quantity || 1; // por defecto 1 si no está definido
      const subtotal = product.price * cantidad;
      total += subtotal;
      const tr = document.createElement("tr");

      tr.innerHTML = `
      <td>
        <img src="${product.image}" alt="${product.name}" class="rounded" style="width:60px; height:60px; object-fit:cover;">
      </td>
      <td class="fw-semibold">${product.name}</td>
      <td>
        <span class="badge bg-info text-dark">${getCategoryName(product.category)}</span>
      </td>
      <td class="small">${product.description}</td>
      <td class="fw-bold">$${product.price.toFixed(2)}</td>
      <td>
        <div class="d-flex align-items-center gap-1">
          <button type="button" class="btn btn-outline-secondary btn-sm decrease-btn" data-index="${product.id}">
            <i class="fas fa-minus"></i>
          </button>
          <input type="number"
              class="form-control form-control-sm cantidad-input text-center"
              min="1" max="${product.stock}"
              value="${cantidad}"
              data-precio="${product.price}"
              data-index="${product.id}"
              style="width:50px;">
          <button type="button" class="btn btn-outline-secondary btn-sm increase-btn" data-index="${product.id}">
            <i class="fas fa-plus"></i>
          </button>
        </div>
      </td>
      <td>        
          ${product.stock}
      </td>
      <td class="subtotal" id="subtotal-${product.id}">
        $${subtotal.toFixed(2)}
      </td>
      <td>
        <button class="btn btn-outline-danger btn-delete btn-sm mb-1" data-id="${product.id}"><i class="fas fa-trash-alt"></i></button>
      </td>
    `;
      tbody.appendChild(tr);
    });

    document.getElementById("total-carrito").textContent = `$${total.toFixed(2)}`;

    document.querySelectorAll(".decrease-btn").forEach(function (btn) {
      btn.addEventListener("click", function () {
        const index = btn.getAttribute("data-index");
        const input = document.querySelector('.cantidad-input[data-index="' + index + '"]');

        if (!input) return;

        let val = parseInt(input.value);
        if (isNaN(val) || val < 1) val = 1;

        if (val > 1) {
          val -= 1;
          input.value = val;
          input.dispatchEvent(new Event("input"));

          const id_emprendedor = localStorage.getItem("id_emprendedor");
          let carritoLocal = JSON.parse(localStorage.getItem("carritoPorEmprendedor")) || {};

          if (carritoLocal[id_emprendedor]) {
            carritoLocal[id_emprendedor] = carritoLocal[id_emprendedor].map(producto => {
              if (String(producto.id) === index) {
                producto.quantity = val;
              }
              return producto;
            });

            localStorage.setItem("carritoPorEmprendedor", JSON.stringify(carritoLocal));
            actualizarContadorCarrito();
            recalcularTotales();
          }
        }
      });
    });

    document.querySelectorAll(".increase-btn").forEach(function (btn) {
      btn.addEventListener("click", function () {
        const index = btn.getAttribute("data-index");
        const input = document.querySelector('.cantidad-input[data-index="' + index + '"]');
        if (!input) return;
    
        let val = parseInt(input.value);
        const max = parseInt(input.getAttribute("max"));
    
        if (isNaN(val) || val < 1) val = 1;
    
        if (val < max) {
          val += 1;
          input.value = val;
          input.dispatchEvent(new Event("input"));
    
          const id_emprendedor = localStorage.getItem("id_emprendedor");
          let carritoLocal = JSON.parse(localStorage.getItem("carritoPorEmprendedor")) || {};
    
          if (carritoLocal[id_emprendedor]) {
            carritoLocal[id_emprendedor] = carritoLocal[id_emprendedor].map(producto => {
              if (String(producto.id) === index) {
                producto.quantity = val;
              }
              return producto;
            });
    
            localStorage.setItem("carritoPorEmprendedor", JSON.stringify(carritoLocal));
            actualizarContadorCarrito();
            recalcularTotales();
          }
        }
      });
    });
    


    document.querySelectorAll(".btn-delete").forEach(function (btn) {
      btn.addEventListener("click", function () {
        const id = btn.getAttribute('data-id');
        document.getElementById("id_producto").value = id;
        const modal = new bootstrap.Modal(
          document.getElementById("modalEliminarProducto")
        );
        modal.show();
      });
    });

    document.getElementById("eliminar_producto_carrito").addEventListener("click", function () {
      const productoAEliminar = document.getElementById("id_producto").value;
      if (!productoAEliminar) {
        console.error("No se ha seleccionado un producto para eliminar.");
        return;
      }

      const row = document.querySelector(`.cantidad-input[data-index="${productoAEliminar}"]`)?.closest("tr");
      if (row) row.remove();

      // Obtener datos del localStorage
      const id_emprendedor = localStorage.getItem("id_emprendedor");
      let carritoLocal = JSON.parse(localStorage.getItem("carritoPorEmprendedor")) || {};

      // Filtrar el producto que se quiere eliminar
      carritoLocal[id_emprendedor] = carritoLocal[id_emprendedor].filter(producto => producto.id !== productoAEliminar);

      // Verificar si ya no quedan productos en ese emprendedor
      if (carritoLocal[id_emprendedor].length === 0) {
        delete carritoLocal[id_emprendedor];
        localStorage.removeItem("carritoPorEmprendedor");
        localStorage.removeItem("id_emprendedor");
        localStorage.removeItem("carritoExpiracion");
        console.log("Carrito vacío. Eliminado completamente del almacenamiento.");
      } else {
        // Si aún hay productos, actualizar el carrito
        localStorage.setItem("carritoPorEmprendedor", JSON.stringify(carritoLocal));
      }

      // Recalcular total
      let total = 0;
      document.querySelectorAll(".subtotal").forEach(function (subtotal) {
        const subtotalValue = parseFloat(subtotal.textContent.replace("$", ""));
        total += subtotalValue;
      });
      document.getElementById("total-carrito").textContent = `$${total.toFixed(2)}`;

      mostrarAlerta('Producto eliminado', 'Producto eliminado del carrito', 'success');
      actualizarContadorCarrito();
      const modal = bootstrap.Modal.getInstance(document.getElementById("modalEliminarProducto"));
      if (modal) modal.hide();
    });

    // Evento para recalcular subtotal y total al cambiar cantidad
    document.querySelectorAll(".cantidad-input").forEach((input) => {
      input.addEventListener("input", function () {
        const id = this.getAttribute("data-index");
        const precio = parseFloat(this.getAttribute("data-precio"));
        let cantidad = parseInt(this.value);
        if (isNaN(cantidad) || cantidad < 1) cantidad = 1;
        this.value = cantidad;
        // Actualizar subtotal
        const subtotal = precio * cantidad;
        document.getElementById(
          `subtotal-${id}`
        ).textContent = `$${subtotal.toFixed(2)}`;
        // Actualizar el array de productos
        const prod = products.find((p) => p.id == id);
        if (prod) prod.cantidad = cantidad;
        // Recalcular total
        let total = 0;
        products.forEach((p) => {
          total += p.price * p.cantidad;
        });
        document.getElementById(
          "total-carrito"
        ).textContent = `$${total.toFixed(2)}`;
      });
    });
  }

  if (verificarCarritoExpirado()) {
    localStorage.removeItem("carritoPorEmprendedor");
    localStorage.removeItem("id_emprendedor");
    localStorage.removeItem("carritoExpiracion");
    console.log("Carrito eliminado por expiración");
  } else {
    const carritoLocal = JSON.parse(localStorage.getItem("carritoPorEmprendedor"));
    const id_emprendedor = localStorage.getItem("id_emprendedor");
    const productosEfrain = carritoLocal?.[id_emprendedor] || [];
    renderCarritoTable(productosEfrain);
  }

  function recalcularTotales() {
    let total = 0;
    document.querySelectorAll(".cantidad-input").forEach(function (input) {
      const precio = parseFloat(input.getAttribute("data-precio"));
      const cantidad = parseInt(input.value) || 1;
      const id = input.getAttribute("data-index");
      const subtotal = precio * cantidad;
      document.getElementById("subtotal-" + id).textContent =
        "$" + subtotal.toFixed(2);
      total += subtotal;
    });
    document.getElementById("total-carrito").textContent =
      "$" + total.toFixed(2);
  }
});

let pasoActual = 0;

const carrusel = document.querySelector('#formCarrusel');
const instanciaCarrusel = new bootstrap.Carousel(carrusel);
const btnSiguiente = document.getElementById('btnSiguientePaso');
const btnAtras = document.getElementById('btnAtrasPaso');
const btnAtrasUltimoPaso = document.getElementById('btnAtrasUltimoPaso');
const barra = document.getElementById('barraProgreso');
const pasos = document.querySelectorAll('#formCarrusel .carousel-item');
const totalPasos = pasos.length;

const FK_CLIENTE = 1;

function actualizarUI() {
  barra.style.width = `${((pasoActual + 1) / totalPasos) * 100}%`;
  barra.textContent = `Paso ${pasoActual + 1} de ${totalPasos}`;

  btnSiguiente.style.display = 'inline-block';

  // Controla la visibilidad del botón atrás aquí
  if (pasoActual === 0) {
    btnAtras.style.visibility = 'hidden';
    btnAtras.disabled = true;
  } else if (pasoActual === totalPasos - 1) {
    btnAtras.style.display = 'none';
    btnAtras.disabled = false;
  } else {
    btnAtras.style.visibility = 'visible';
    btnAtras.style.display = 'inline-block';
    btnAtras.disabled = false;
  }

  switch (pasoActual) {
    case 0:
      btnSiguiente.textContent = 'Comprar';
      btnSiguiente.className = 'btn btn-primary';
      break;
    case 1:
      btnSiguiente.textContent = 'Confirmar Entrega';
      btnSiguiente.className = 'btn btn-primary';
      break;
    case totalPasos - 1:
      btnSiguiente.style.display = 'none';
      break;
    default:
      btnSiguiente.textContent = 'Siguiente';
      btnSiguiente.className = 'btn btn-primary';
      break;
  }
}

btnSiguiente.addEventListener('click', async () => {
  const carritoBody = document.getElementById('carritoBody');
  if (!carritoBody || carritoBody.innerHTML.trim() === '') {
    mostrarAlerta('Carrito vacío', 'Tu carrito está vacío. Agrega productos antes de continuar.', 'warning');
    return;
  }

  // PASO 0: Confirmar carrito
  if (pasoActual === 0) {

    const confirmar =await new Promise((resolve) => {
      mostrarConfirmacion(
        "Confirmar carrito",
        "¿Estás seguro de que deseas continuar con este carrito?",
        (confirmarCarrito) => {
          resolve(confirmarCarrito); // true o false
          //if (!confirmarCarrito) {
            //actualizarUI();
            //resolve(false);
          //} else {
            //resolve(true);
          //}
        }
      );
    });

    if (!confirmar) {
    actualizarUI(); // si canceló, no hacemos nada más
    return;
  }

    // Recolectar productos del carrito
    const detalle = [];
    document.querySelectorAll(".cantidad-input").forEach(function (input) {
      const id = parseInt(input.getAttribute("data-index"));
      const cantidad = parseInt(input.value) || 1;
      const precio = parseFloat(input.getAttribute("data-precio"));
      detalle.push({ id, cantidad, precio });
    });

    const pedido = {
      fk_cliente: FK_CLIENTE,
      detalle: detalle,
    };  
    localStorage.setItem('datosPedido', JSON.stringify(pedido));
    pasoActual++;
    instanciaCarrusel.to(pasoActual);
    actualizarUI();
    return;
  }

  // PASO 1: Validar envío (sin confirmación)
  const modoEntrega = document.getElementById('modoEntrega').value;

  if (pasoActual === 1) {
    if (modoEntrega === '') {
      mostrarAlerta('Modo de entrega requerido', 'Por favor selecciona un modo de entrega', 'warning');
      return;
    }

    if (modoEntrega === 'Envio Nacional') {
      const empresa = document.getElementById('empresaEnvio').value.trim();
      const direccion = document.getElementById('direccionEnvio').value.trim();

      if (empresa === '' || direccion === '') {
        await mostrarAlerta('Datos requeridos', 'Por favor completa los datos de envío.', 'warning');
        return;
      }
    }

    if (modoEntrega === 'Delivery') {
      const destinatario = document.getElementById('destinatario').value.trim();
      const telefono_destinatario = document.getElementById('telefono_destinatario').value.trim();
      const correo_destinatario = document.getElementById('correo_destinatario').value.trim();
      const direccion_exacta = document.getElementById('direccion_exacta').value.trim();
      if (
        destinatario === '' ||
        telefono_destinatario === '' ||
        correo_destinatario === '' ||
        direccion_exacta === ''
      ) {
        await mostrarAlerta('Datos requeridos', 'Por favor completa todos los datos de delivery.', 'warning');
        return;
      }
    }

    pasoActual++;
    instanciaCarrusel.next();
    actualizarUI();
    return;
  }

  if (pasoActual < totalPasos - 1) {
    pasoActual++;
    instanciaCarrusel.next();
    actualizarUI();
  }
});

btnAtrasUltimoPaso.addEventListener('click', () => {
  if (pasoActual > 0) {
    pasoActual--;
    instanciaCarrusel.prev();
    actualizarUI();
  }
});

btnAtras.addEventListener('click', () => {
  if (pasoActual > 0) {
    pasoActual--;
    instanciaCarrusel.prev();
    actualizarUI();
  }
});

actualizarUI();

//al obtener el pedido en el local, se carga el contenido de los productos
// Ejecutar al cargar el documento
window.addEventListener('DOMContentLoaded', function () {
  manejarCambioPedidoId();
});


function manejarCambioPedidoId() {
  const idEmprendedor = localStorage.getItem('id_emprendedor'); // Esto es el id_emprendedor
  const carritoPorEmprendedor = localStorage.getItem('carritoPorEmprendedor');

  if (!idEmprendedor || !carritoPorEmprendedor) {
    ocultarCamposPago();
    return;
  }

  const carrito = JSON.parse(carritoPorEmprendedor);
  const productos = carrito[idEmprendedor];
  const tbody = document.getElementById('tablaProductosPedido');
  const container = document.getElementById('productosPedidoContainer');
  tbody.innerHTML = '';

  if (Array.isArray(productos) && productos.length > 0) {
    document.getElementById('desglosePagoContainer').style.display = '';
    productos.forEach(prod => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${prod.name}</td>
        <td>${prod.quantity}</td>
        <td>$${Number(prod.price).toFixed(2)}</td>
        <td>$${Number(prod.price * prod.quantity).toFixed(2)}</td>
      `;
      tbody.appendChild(tr);
    });
    container.style.display = '';
    calcularTotalProductos(productos);
  } else {
    document.getElementById('desglosePagoContainer').style.display = 'none';
    ocultarCamposPago();
  }
}

let valorDolar = 0;

function actualizarValorDolar() {
  fetch( API_CONFIG + '/tasa/consultarTasaBcv', {
    method: 'GET',
    headers: {
      'Accept': 'application/json'
    },
    credentials: 'include'
  })
  .then(response => response.json())
  .then(data => {
    if (data.success && data.data?.tasa_cambio) {
      valorDolar = parseFloat(data.data.tasa_cambio);
      actualizarTotalDesglose();
    } else {
      console.warn('No se pudo obtener la tasa de cambio');
    }
  })
  .catch(error => {
    console.error('Error al consultar la tasa de cambio:', error);
  });
}

actualizarValorDolar();

function calcularTotalProductos(productos) {
  let total = 0;
  productos.forEach(prod => {
    total += Number(prod.price) * Number(prod.quantity);
  });
  document.getElementById('montoPago').value = total.toFixed(2);
  document.getElementById('totalPagarPedido').textContent = `$${total.toFixed(2)}`;
  document.getElementById('faltaPagar').value = `$${total.toFixed(2)}`;
  actualizarTotalDesglose();
}

function ocultarCamposPago() {
  document.getElementById('productosPedidoContainer').style.display = 'none';
  document.getElementById('totalPagarPedido').textContent = '$0.00';
  document.getElementById('montoPago').value = '';
  document.getElementById('faltaPagar').value = '$0.00';
  document.querySelectorAll('#tablaDesglosePago tbody tr').forEach(tr => {
    tr.querySelectorAll('input').forEach(input => {
      input.value = '';
    });
    tr.querySelectorAll('select').forEach(select => {
      select.selectedIndex = 0;
    });
  });
  actualizarTotalDesglose();
}

// Evento para agregar fila de desglose
const btnAgregar = document.getElementById('btnAgregarDesglose');
const tablaDesglose = document.getElementById('tablaDesglosePago');

// Validar si se puede habilitar el botón
function validarCamposFilaActual() {
  const filas = tablaDesglose.querySelectorAll('tbody tr');
  if (filas.length === 0) {
    btnAgregar.disabled = true;
    return;
  }

  const ultimaFila = filas[filas.length - 1];
  const metodo = ultimaFila.querySelector('.metodo-desglose')?.value.trim();
  const moneda = ultimaFila.querySelector('.moneda-desglose')?.value.trim();
  const monto = ultimaFila.querySelector('.monto-desglose')?.value.trim();

  const requiereReferencia = metodo === '2' || metodo === '3';
  const referencia = ultimaFila.querySelector('.referencia-desglose')?.value.trim();
  // Si requiere comprobante, debe estar seleccionado un archivo
  const comprobanteInput = ultimaFila.querySelector('.comprobante-desglose');
  const requiereComprobante = requiereReferencia;
  const comprobanteValido = !requiereComprobante || (comprobanteInput && comprobanteInput.files.length > 0);

  const camposCompletos = metodo && moneda && monto && (!requiereReferencia || referencia) && comprobanteValido;

  btnAgregar.disabled = !camposCompletos;
}

// Evento global para validar cuando cambie algo en la tabla
tablaDesglose.addEventListener('input', validarCamposFilaActual);
tablaDesglose.addEventListener('change', validarCamposFilaActual);

// Llamar a validar inicialmente (por si ya hay una fila)
validarCamposFilaActual();

// Al hacer clic en agregar fila, agregar la fila y deshabilitar el botón nuevamente
btnAgregar.addEventListener('click', function () {
  agregarFilaDesglose(); // Tu función existente
  setTimeout(validarCamposFilaActual, 100); // Esperar a que la fila se agregue antes de validar
});


// Evento para eliminar fila de desglose
document.getElementById('tablaDesglosePago').addEventListener('click', function (e) {
  if (e.target.closest('.btn-remove-desglose')) {
    const tr = e.target.closest('tr');
    if (document.querySelectorAll('#desglosePagoBody tr').length > 1) {
      tr.remove();
      actualizarTotalDesglose();
      mostrarValorDolarSiBs();
    }
  }
});

document.querySelectorAll('#desglosePagoBody tr').forEach(tr => {
  const monto = tr.querySelector('.monto-desglose')?.value;
  if (monto) {
    tr.querySelector('.monto-desglose').addEventListener('input', function () {
      actualizarTotalDesglose();
    });
  }
});

const btnBancoAPagar = document.getElementById('bancoAPagar');
// Evento para cargar monedas según método de pago en cada fila
document.getElementById('tablaDesglosePago').addEventListener('change', function (e) {
  const fila = e.target.closest('tr');

  if (e.target.classList.contains('metodo-desglose')) {
    const idMetodo = e.target.value;
    const monedaSelect = fila.querySelector('.moneda-desglose');
    const celdaReferencia = fila.querySelector('.col-referencia');
    const inputReferencia = celdaReferencia?.querySelector('.referencia-desglose');
    const inputComprobante = fila.querySelector('.comprobante-desglose');

    // Obtener el método anterior
    const metodoAnterior = e.target.getAttribute('data-metodo-anterior');

    // Si ya tenía un método seleccionado y cambia, reiniciar campos
    if (metodoAnterior && metodoAnterior !== idMetodo) {
      monedaSelect.selectedIndex = 0;
      if (inputReferencia) inputReferencia.value = '';
      if (inputComprobante) inputComprobante.value = '';
      fila.querySelector('.monto-desglose').value = '';
    }

    // Guardar el método actual como anterior para la próxima vez
    e.target.setAttribute('data-metodo-anterior', idMetodo);

    // Solo habilitar/deshabilitar referencia y comprobante según método
    const activar = idMetodo === '2' || idMetodo === '3';
    if (inputReferencia) inputReferencia.disabled = !activar;
    if (inputComprobante) inputComprobante.disabled = !activar;

    // Reset y cargar monedas
    monedaSelect.innerHTML = '<option value="">-- Selecciona moneda --</option>';
    if (!idMetodo) return;

    fetch( API_CONFIG + `/pagos/monedasPorMetodo?idMetodo=${idMetodo}`, { credentials: 'include' })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          data.data.forEach(m => {
            const opt = document.createElement('option');
            opt.value = m.id_detalle_pago;
            opt.textContent = m.simbolo;
            monedaSelect.appendChild(opt);
          });
          if (data.data.length === 1) {
            monedaSelect.selectedIndex = 1;
            mostrarValorDolarSiBs();
            actualizarTotalDesglose();
          }
        }
      });
  }

  // Mostrar/ocultar el botón de banco a pagar solo si alguna fila tiene método 2 o 3
  const filas = document.querySelectorAll('#tablaDesglosePago tbody tr');
  let mostrarBoton = false;
  filas.forEach(f => {
    const metodo = f.querySelector('.metodo-desglose')?.value;
    if (metodo === '2' || metodo === '3') {
      mostrarBoton = true;
    }
  });
  if (mostrarBoton) {
    btnBancoAPagar.classList.remove('d-none');
  } else {
    btnBancoAPagar.classList.add('d-none');
  }

  if (e.target.classList.contains('moneda-desglose')) {
    mostrarValorDolarSiBs();
    actualizarTotalDesglose();
  }
});

const modalBancoAPagar = new bootstrap.Modal(document.getElementById("modalBancoAPagar"));

btnBancoAPagar.addEventListener('click', function () {
  let metodo = null;
  document.querySelectorAll('#tablaDesglosePago tbody tr').forEach(tr => {
    const val = tr.querySelector('.metodo-desglose')?.value;
    if (val === '2' || val === '3') metodo = val;
  });

  const id_emprendedor = localStorage.getItem('id_emprendedor');

  if (!metodo || !id_emprendedor) return;

  const url =  API_CONFIG + `/datos/consultarDatos?id_emprendedor=${id_emprendedor}&id_metodo_pago=${metodo}`;

  fetch(url, { credentials: 'include' })
    .then(response => response.json())
    .then(res => {
      const datos = res.data[0] || {};
      // Oculta todos los campos primero
      document.getElementById('liNumeroCuenta').classList.add('d-none');
      document.getElementById('liCedula').classList.add('d-none');
      document.getElementById('liTelefono').classList.add('d-none');
      document.getElementById('correoDestino').parentElement.classList.add('d-none');

      if (metodo === '2') { // Transferencia
        document.getElementById('liNumeroCuenta').classList.remove('d-none');
        document.getElementById('liCedula').classList.remove('d-none');
        document.getElementById('correoDestino').parentElement.classList.remove('d-none');
      } else if (metodo === '3') { // Pago móvil
        document.getElementById('liCedula').classList.remove('d-none');
        document.getElementById('liTelefono').classList.remove('d-none');
        document.getElementById('correoDestino').parentElement.classList.remove('d-none');
      }

      document.getElementById('tituloModalBanco').textContent = datos.metodo_pago_nombre || '';
      document.getElementById('bancoDestino').textContent = datos.banco || '';
      document.getElementById('numeroCuentaDestino').textContent = datos.numero_cuenta || '';
      document.getElementById('cedulaDestino').textContent = datos.cedula || '';
      document.getElementById('telefonoDestino').textContent = datos.telefono || '';
      document.getElementById('correoDestino').textContent = datos.correo || '';

      modalBancoAPagar.show();
    })
    .catch(error => {
      console.error("Error al obtener los datos:", error);
      mostrarAlerta('Los datos no se pudieron obtener', 'El emprendedor no tiene datos de pago disponibles, por favor contacta con el emprendedor para obtener más información.', 'warning');
    });
});


// Actualizar total al cambiar monto o moneda
document.getElementById('tablaDesglosePago').addEventListener('input', function (e) {
  if (e.target.classList.contains('monto-desglose') || e.target.classList.contains('moneda-desglose')) {
    actualizarTotalDesglose();
  }
});

// Función para agregar fila de desglose
function agregarFilaDesglose() {
  const tbody = document.getElementById('desglosePagoBody');
  const tr = document.createElement('tr');
  tr.innerHTML = `
        <td>
            <select class="form-select metodo-desglose" required>
                <option value="">-- Selecciona método --</option>
                ${metodosPago.map(m => `
                    <option value="${m.id_metodo_pago}">${m.nombre}</option>
                `).join('')}    
            </select>
        </td>
        <td>
            <select class="form-select moneda-desglose" required>
                <option value="">-- Selecciona moneda --</option>
            </select>
        </td>
        <td>
            <input type="number" class="form-control monto-desglose" min="0" step="0.01" required>
        </td>
        <td class="col-referencia">
            <input type="number" class="form-control referencia-desglose" disabled>
        </td>
        <td>
          <input type="file" class="form-control form-control-sm comprobante-desglose" accept="image/*,application/pdf" disabled>
        </td>
        <td>
            <button type="button" class="btn btn-danger btn-sm btn-remove-desglose" ${tbody.children.length === 0 ? 'disabled' : ''}>
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;
  tbody.appendChild(tr);
  actualizarTotalDesglose();
  mostrarValorDolarSiBs();
}

document.getElementById('tablaDesglosePago').addEventListener('input', function (e) {
  if (
    e.target.classList.contains('monto-desglose') ||
    e.target.classList.contains('moneda-desglose') ||
    e.target.classList.contains('referencia-desglose') ||
    e.target.classList.contains('comprobante-desglose')
  ) {
    actualizarTotalDesglose();
  }
});
document.getElementById('tablaDesglosePago').addEventListener('change', function (e) {
  if (
    e.target.classList.contains('monto-desglose') ||
    e.target.classList.contains('moneda-desglose') ||
    e.target.classList.contains('referencia-desglose') ||
    e.target.classList.contains('comprobante-desglose')
  ) {
    actualizarTotalDesglose();
  }
});

function actualizarTotalDesglose() {
  let totalUSD = 0;
  let totalBs = 0;
  let filasValidas = true;

  document.querySelectorAll('#desglosePagoBody tr').forEach(tr => {
    const monedaSelect = tr.querySelector('.moneda-desglose');
    const monto = parseFloat(tr.querySelector('.monto-desglose').value) || 0;
    const simbolo = monedaSelect.options[monedaSelect.selectedIndex]?.text?.trim();

    if (simbolo === '$') totalUSD += monto;
    if (simbolo && simbolo.toLowerCase().includes('bs')) totalBs += monto;

    // Validación de referencia y comprobante
    const metodo = tr.querySelector('.metodo-desglose')?.value;
    const requiereReferencia = metodo === '2' || metodo === '3';
    const referencia = tr.querySelector('.referencia-desglose')?.value.trim();
    const comprobanteInput = tr.querySelector('.comprobante-desglose');
    const comprobanteValido = !requiereReferencia || (comprobanteInput && comprobanteInput.files.length > 0);

    if (
      !tr.querySelector('.moneda-desglose').value ||
      !tr.querySelector('.monto-desglose').value ||
      (requiereReferencia && !referencia) ||
      !comprobanteValido
    ) {
      filasValidas = false;
    }
  });

  document.getElementById('totalDesglosePago').textContent =
    `$${totalUSD.toFixed(2)} + Bs ${totalBs.toFixed(2)}`;

  const totalPedido = parseFloat((document.getElementById('montoPago').value || "0").replace(',', '.')) || 0;
  const sumaDesglose = totalUSD + (totalBs / valorDolar);
  const totalPedidoBs = totalPedido * valorDolar;
  let falta = totalPedido - sumaDesglose;
  let faltaTexto = '';

  if (falta > 0.01) {
    faltaTexto = `Falta $${falta.toFixed(2)}`;
  } else {
    faltaTexto = '$0.00';
  }
  document.getElementById('montoPagoBs').value = totalPedidoBs.toFixed(2);
  document.getElementById('faltaPagar').value = faltaTexto;

  const advertencia = document.getElementById('desgloseAdvertencia');
  const btnRegistrar = document.querySelector('#formRegistrarPago button[type="submit"]');

  if (falta > 0.01 || !filasValidas) {
    // Falta dinero o datos incompletos: mostrar advertencia y desactivar botón
    advertencia.style.display = '';
    btnRegistrar.disabled = true;
  } else {
    // Exacto o Excedido y datos completos: ocultar advertencia y permitir registrar
    advertencia.style.display = 'none';
    btnRegistrar.disabled = false;
  }
}


function mostrarValorDolarSiBs() {
  let mostrar = false;
  document.querySelectorAll('.moneda-desglose').forEach(select => {
    const valor = select.value;
    const texto = select.options[select.selectedIndex]?.text?.toLowerCase();
    if (valor === '1' || valor === 'bs' || texto === 'bs') {
      mostrar = true;
    }
  });
}


document.getElementById('formRegistrarPago').addEventListener('submit', function (e) {
  e.preventDefault();
  const cedula = document.getElementById('cedula').value;
  const detallePedido = JSON.parse(localStorage.getItem('datosPedido'));
  const detallePago = [];
  const formData = new FormData();

  // Recolectar desglose de pago y archivos
  document.querySelectorAll('#desglosePagoBody tr').forEach((tr, idx) => {
    const moneda = tr.querySelector('.moneda-desglose').value;
    const fk_detalle_metodo_pago = moneda;
    const monto = parseFloat(tr.querySelector('.monto-desglose').value);
    const referenciaInput = tr.querySelector('.referencia-desglose');
    const referencia = referenciaInput ? referenciaInput.value : "";
    const comprobanteInput = tr.querySelector('.comprobante-desglose');
    let comprobante = null;
    if (comprobanteInput && comprobanteInput.files.length > 0) {
      comprobante = comprobanteInput.files[0];
      formData.append(`comprobante_${idx}`, comprobante);
    }
    if (fk_detalle_metodo_pago && monto > 0) {
      detallePago.push({
        fk_detalle_metodo_pago,
        monto,
        referencia,
        comprobante: comprobante ? `comprobante_${idx}` : null // nombre del campo en formData
      });
    }
  });

  // --- RECOLECTAR DATOS DEL PASO 2 ---
  const modoEntrega = document.getElementById('modoEntrega')?.value || '';
  let detalleEnvio = {};

  if (modoEntrega === 'Envio Nacional') {
    detalleEnvio = {
      modoEntrega,
      empresaEnvio: document.getElementById('empresaEnvio')?.value || '',
      direccionEnvio: document.getElementById('direccionEnvio')?.value || ''
    };
  } else if (modoEntrega === 'Delivery') {
    detalleEnvio = {
      modoEntrega,
      destinatario: document.getElementById('destinatario')?.value || '',
      telefono_destinatario: document.getElementById('telefono_destinatario')?.value || '',
      correo_destinatario: document.getElementById('correo_destinatario')?.value || '',
      direccion_exacta: document.getElementById('direccion_exacta')?.value || ''
    };
  } else if (modoEntrega === 'Presencial') {
    detalleEnvio = { modoEntrega };
  }

  // Validación básica
  if (!detalleEnvio || detallePago.length === 0) {
    mostrarAlerta('Datos requeridos', 'Completa todos los campos requeridos.', 'warning');
    return;
  }

  // Deshabilitar botón para evitar doble envío
  const btn = this.querySelector('button[type="submit"]');
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Registrando...';

  // Agrega los datos JSON al FormData
  formData.append('cedula', cedula);
  formData.append('detallePedido', JSON.stringify(detallePedido));
  formData.append('detallePago', JSON.stringify(detallePago));
  formData.append('detalleEnvio', JSON.stringify(detalleEnvio));

  // Mostrar en consola el contenido del FormData (debug)
  for (let pair of formData.entries()) {
    console.log(pair[0], pair[1]);
  }

  const idEmprendedor = localStorage.getItem('id_emprendedor');
  // Agregar el id_emprendedor al body de la petición
  formData.append('id_emprendedor', idEmprendedor);

  fetch( API_CONFIG + '/pedidos/registrar', {
    method: 'POST',
    credentials: 'include',
    body: formData
  })
    .then(response => response.json())
    .then(data => {
      console.log(data);
      btn.disabled = false;
      btn.innerHTML = '<i class="fas fa-save me-2"></i>Registrar Pedido';
      if (data.success) {
        localStorage.removeItem('carritoPorEmprendedor');
        localStorage.removeItem('id_emprendedor');
        localStorage.removeItem('carritoExpiracion');
        mostrarAlerta('¡Éxito!', data.message || 'Pago registrado correctamente', 'success')
        .then(() => {
          const modal = bootstrap.Modal.getInstance(document.getElementById('registrarPagoModal'));
          if (modal) modal.hide();

          ocultarCamposPago();
          mostrarValorDolarSiBs();

          window.location.href = API_CONFIG + '/pagos';
        });
      } else {
        mostrarAlerta('Error al registrar el pedido', data.message, 'danger');
      }
    })
    .catch(error => {
      // Error de red o de servidor
      console.error('Error en la solicitud:', error);
      btn.disabled = false;
      btn.innerHTML = '<i class="fas fa-save me-2"></i>Registrar Pago';
      mostrarAlerta('Error inesperado', error.message || 'No se pudo completar la solicitud.', 'info');
    });
});

document.getElementById('modoEntrega').addEventListener('change', function () {
  const modo = this.value;
  const envioNacional = document.getElementById('camposEnvioNacional');
  const delivery = document.getElementById('camposDelivery');

  // Oculta todo primero
  envioNacional.classList.add('d-none');
  delivery.classList.add('d-none');

  if (modo === 'Envio Nacional') {
    envioNacional.classList.remove('d-none');
  } else if (modo === 'Delivery') {
    delivery.classList.remove('d-none');
  }
}); 
 //VALIDACION DE FORMULARIO
  //Para mostrar el error en el span
function showError(field, message) {
    //El contenido del mensaje se mostrará en el span que tenga el id field+Error (ejemplo nombreError)
    document.getElementById(field + "Error").textContent = message;
}

//Para limpiar el error en el span 
function clearError(field) {
    document.getElementById(field + "Error").textContent = "";
}

//Se evalua el campo y depende de eso se muestra o se limpia el error en el span correspondiente
//Se envia como parametros: el event, la expresion regular, el campo, y el mensaje de error que se mostrará
function restrictInput(event, regex, field, errorMsg) {
    const key = event.key;
    //Si se está recibiendo por teclado una tecla que no este en la exp reg, que no sea tecla de borrar ni tab    
    if (!regex.test(key) && key !== "Backspace" && key !== "Tab") {
        event.preventDefault();
        showError(field, errorMsg); // Muestra mensaje solo si el caracter es incorrecto
    } 
    //En caso que todas las teclas que se esten ingresando sean correctar
    else {
        clearError(field); // Limpia el mensaje si el caracter es permitido
    }
}

document.getElementById("destinatario").addEventListener("keypress", function(event) {
    restrictInput(event, /^[A-Za-z\s]$/, "destinatario", "Solo se permiten letras.");
});

document.getElementById("correo_destinatario").addEventListener("input", function() {
    const correo = this.value;
    const correoRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!correoRegex.test(correo)) {
        showError("correo_destinatario", "El correo debe incluir una direccion como @gmail.com, @hotmail.com, @yahoo.com, @outlook.com.");
    } else {
        clearError("correo_destinatario");
    }
});

document.getElementById("direccion_exacta").addEventListener("input", function() {
    const direccion = this.value;
    const direccionRegex = /^[A-Za-z0-9#\-\.\s]+$/;
    if (!direccionRegex.test(direccion)) {
        showError("direccion_exacta", "Solo se aceptan numeros, letras, espacios, # y -.");
    } else {
        clearError("direccion_exacta");
    }
});


document.getElementById("telefono_destinatario").addEventListener("keypress", function(event) {
    restrictInput(event, /^[0-9]$/, "telefono_destinatario", "Solo se permiten números.");
});

function validateNombre() {
    const nombre_cliente = document.getElementById("destinatario").value;
    const nombreRegex = /^[A-Za-z\s]{5,50}$/;
    if (!nombreRegex.test(nombre_cliente)) {
        showError("destinatario", "El nombre debe tener entre 5 y 50 caracteres y solo letras.");
        return false;
    } else {
        clearError("destinatario");
        return true;
    }
} 


function validateCorreo() {
    const correo = document.getElementById("correo_destinatario").value;
    const nombreRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!nombreRegex.test(correo)) {
        showError("correo_destinatario", "El correo debe incluir una direccion como @gmail.com, @hotmail.com, @yahoo.com, @outlook.com");
     
        return false;
    } else {
        clearError("correo_destinatario");
        return true;
    }
} 

function validateDireccion() {
    const direccion = document.getElementById("direccion_exacta").value;
    const nombreRegex = /^[A-Za-z0-9#\-\.\,\s]+$/ ;
    if (!nombreRegex.test(direccion)) {
        showError("direccion_exacta", "Ingrese su direccion.");
     
        return false;
    } else {
        clearError("direccion_exacta");
        return true;
    }
}


function validateTelefono() {
    const telefono= document.getElementById("telefono_destinatario").value;
    const valor = parseFloat(telefono);
    return !isNaN(valor) && valor > 0;
}


function enableSubmit_crear() {
    //Se validan en funciones que cumplan todas con las exp reg
    const isFormValid =
        validateNombre()&&
        validateCorreo()  &&
        validateDireccion() &&
        validateTelefono() && 
  
        document.getElementById("destinatario").value.trim() !== "" &&
        document.getElementById("correo_destinatario").value.trim() !== "" &&
        document.getElementById("direccion_exacta").value.trim() !== "" &&
        document.getElementById("telefono_destinatario").value.trim() !== "";
        
        
}
        document.getElementById("destinatario").addEventListener("input", enableSubmit_crear);
        document.getElementById("correo_destinatario").addEventListener("input", enableSubmit_crear);
        document.getElementById("direccion_exacta").addEventListener("input", enableSubmit_crear);
        document.getElementById("telefono_destinatario").addEventListener("input", enableSubmit_crear);     
       

  
