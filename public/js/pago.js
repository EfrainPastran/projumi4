import { API_CONFIG } from './config.js';
document.addEventListener("DOMContentLoaded", cargarPagos);

const pagosPorPagina = 10;
let pagosData = [];
let paginaActual = 1;
let datosFiltrados = [];

function renderPagosTabla(pagos) {
  let html = `
    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead>
          <tr>
            <th># Pedido</th> 
            <th>Fecha</th>
            ${
              pagosData.tipo === "emprendedor"
                ? "<th>Cliente</th>"
                : pagosData.tipo === "cliente"
                ? "<th>Emprendedor</th>"
                : "<th>Cliente</th><th>Emprendedor</th>"
            }
            <th>Total</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          ${
            pagos.length === 0
              ? `<tr><td colspan="7" class="text-center">No hay pagos registrados.</td></tr>`
              : pagos
                  .map((pago) => {
                    let cols = `
                      <td>#PED-${pago.pedido_id}</td>
                      <td>${pago.fecha_pago}</td>
                    `;
                    if (pagosData.tipo === "emprendedor") {
                      cols += `<td>${pago.cliente_nombre}</td>`;
                    } else if (pagosData.tipo === "cliente") {
                      cols += `<td>${pago.emprendedor_nombre}</td>`;
                    } else {
                      cols += `<td>${pago.cliente_nombre}</td>`;
                      cols += `<td>${pago.emprendedor_nombre}</td>`;
                    }
                    cols += `
                      <td>$${parseFloat(pago.total_pedido).toFixed(2)}</td>
                      <td>
                        <span class="badge bg-${
                          pago.estado_pago === "Aprobado"
                            ? "success"
                            : pago.estado_pago === "Pendiente"
                            ? "warning text-dark"
                            : pago.estado_pago === "pendiente"
                            ? "warning text-dark"
                            : "danger"
                        }">
                          ${pago.estado_pago}
                        </span>
                      </td>
                      <td>
                        <button 
                          class="btn btn-sm btn-outline-primary btn-detalle-pago" 
                          data-bs-toggle="modal" 
                          data-bs-target="#orderModal"
                          data-idpago="${pago.id_pagos}">
                          <i class="fas fa-eye"></i> Consultar
                        </button>
                        ${
                          pagosData.tipo !== "cliente"
                            ? `<button 
                                class="btn btn-sm btn-outline-dark btn-verificar-pago" 
                                data-bs-toggle="modal" 
                                data-bs-target="#verificarPagoModal"
                                data-idpago-verificar="${pago.id_pagos}"
                                data-id-pedido="${pago.pedido_id}">
                                <i class="fas fa-list-check"></i> Validar
                              </button>`
                            : ""
                        }
                      </td>
                    `;
                    return `<tr>${cols}</tr>`;
                  })
                  .join("")
          }
        </tbody>
      </table>
    </div>
  `;

  // Imprime la tabla completa en el contenedor principal
  document.getElementById("tablaPagosContainer").innerHTML = html;

  // Delegar eventos a los botones generados
  document.querySelectorAll(".btn-detalle-pago").forEach((btn) => {
    btn.addEventListener("click", mostrarDetallePago);
  });

  document.querySelectorAll(".btn-verificar-pago").forEach((btn) => {
    btn.addEventListener("click", function () {
      const idPago = this.getAttribute("data-idpago-verificar");
      const idPedido = this.getAttribute("data-id-pedido");
      document.getElementById("idpagoVerificar").value = idPago;
      document.getElementById("idPedido").value = idPedido;
    });
  });
}

// Renderiza la tabla de comprobantes en el modal
function renderTablaComprobantes(comprobantes) {
  let html = `
    <div class="table-responsive">
      <table class="table table-bordered table-sm align-middle">
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Método</th>
            <th>Referencia</th>
            <th>Monto</th>
            <th>Comprobante</th>
            <th>Estado</th>
          </tr>
        </thead>
        <tbody>
          ${comprobantes
            .map(
              (c, i) => `
            <tr>
              <td>${c.fecha}</td>
              <td>${c.metodo}</td>
              <td>${c.referencia}</td>
              <td>${c.monto}</td>
              <td>
                ${
                  c.archivo
                    ? `<a href="${c.archivo}" target="_blank" class="btn btn-link p-0">
                          <i class="fas fa-file-alt"></i> Ver archivo
                       </a>`
                    : '<span class="text-muted">Sin archivo</span>'
                }
              </td>
              <td>
                <div class="form-check form-switch">
                  <input class="form-check-input switch-estado" type="checkbox" data-index="${i}" ${c.verificado ? "checked" : ""}>
                  <span class="ms-2">${c.verificado ? "Aprobado" : "Rechazado"}</span>
                </div>
              </td>
            </tr>
          `
            )
            .join("")}
        </tbody>
      </table>
    </div>
  `;
  document.getElementById("tablaComprobantesPago").innerHTML = html;

  document.querySelectorAll(".switch-estado").forEach((sw) => {
    sw.addEventListener("change", function () {
      const idx = this.dataset.index;
      comprobantes[idx].verificado = this.checked;
      this.nextElementSibling.textContent = this.checked ? "Aprobado" : "Rechazado";
      verificarTodosAprobados();
    });
  });
  verificarTodosAprobados();
}

function verificarTodosAprobados() {
  const todosAprobados = Array.from(document.querySelectorAll(".switch-estado"))
    .every(sw => sw.checked);
  document.getElementById("btnAprobar").disabled = !todosAprobados;
}

// Evento para abrir el modal y cargar la tabla simulada
document.addEventListener("click", function (e) {
  if (e.target.closest(".btn-verificar-pago")) {
    const idpago = document.getElementById("idpagoVerificar").value;
    fetch( API_CONFIG + "/pagos/consultarPago?idPago=" + idpago, {
      credentials: "include",
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.detalle_pago && Array.isArray(data.detalle_pago)) {
          // Mapea los datos reales al formato esperado por la tabla
          const comprobantes = data.detalle_pago.map((dp) => ({
            fecha: data.pago.fecha,
            metodo: dp.metodo_pago,
            referencia: dp.referencia,
            monto: `${dp.simbolo} ${parseFloat(dp.monto).toFixed(2)}`,
            archivo: dp.comprobante ?  API_CONFIG + "/" + dp.comprobante : "",
            verificado: data.pago.estatus === "Aprobado" ? true : false
          }));
          renderTablaComprobantes(comprobantes);
        } else {
          document.getElementById("tablaComprobantesPago").innerHTML =
            '<div class="alert alert-warning">No hay comprobantes para este pago.</div>';
        }
      })
      .catch((error) => {
        console.error("Error al obtener pago:", error);
        document.getElementById("tablaComprobantesPago").innerHTML =
          '<div class="alert alert-danger">Error al cargar comprobantes.</div>';
      });
  }
});

document
  .getElementById("applyFilters")
  .addEventListener("click", aplicarFiltros);
document
  .getElementById("buscarPagos")
  .addEventListener("click", aplicarFiltros);

  document.getElementById("btnAprobar").addEventListener("click", () => {
    const id_pago = document.getElementById("idpagoVerificar").value;
    const id_pedido = document.getElementById("idPedido").value;
    actualizarEstadoPago(id_pago, "Aprobado", id_pedido);
  });
  
  document.getElementById("btnRechazar").addEventListener("click", () => {
    const id_pago = document.getElementById("idpagoVerificar").value;
    const id_pedido = document.getElementById("idPedido").value;
    actualizarEstadoPago(id_pago, "Rechazado", id_pedido);
  });
function aplicarFiltros() {
  const estadoSeleccionado = document.getElementById("statusFilter").value;
  const fechaSeleccionada = document.getElementById("dateFilter").value;
  const textoBusqueda = document
    .getElementById("busquedaTexto")
    .value.trim()
    .toLowerCase();

  const hoy = new Date();
  const inicioSemana = new Date(hoy);
  inicioSemana.setDate(hoy.getDate() - hoy.getDay());
  const inicioMes = new Date(hoy.getFullYear(), hoy.getMonth(), 1);

  datosFiltrados = pagosData.data.filter((pago) => {
    let cumpleEstado = true;
    let cumpleFecha = true;
    let cumpleBusqueda = true;

    if (estadoSeleccionado !== "all") {
      cumpleEstado = pago.estado_pago.toLowerCase() === estadoSeleccionado;
    }

    if (fechaSeleccionada !== "all") {
      const fechaPago = new Date(pago.fecha_pago);
      if (fechaSeleccionada === "today") {
        cumpleFecha = fechaPago.toDateString() === hoy.toDateString();
      } else if (fechaSeleccionada === "week") {
        cumpleFecha = fechaPago >= inicioSemana && fechaPago <= hoy;
      } else if (fechaSeleccionada === "month") {
        cumpleFecha = fechaPago >= inicioMes && fechaPago <= hoy;
      }
    }

    if (textoBusqueda !== "") {
      const pedidoId = `#PED-${pago.pedido_id}`.toLowerCase();
      const fecha = pago.fecha_pago.toLowerCase();
      const cliente =
        `${pago.cliente_nombre} ${pago.cliente_apellido}`.toLowerCase();
      const total = pago.total_pedido.toString().toLowerCase();
      const estado = pago.estado_pago.toLowerCase();

      cumpleBusqueda =
        pedidoId.includes(textoBusqueda) ||
        fecha.includes(textoBusqueda) ||
        cliente.includes(textoBusqueda) ||
        total.includes(textoBusqueda) ||
        estado.includes(textoBusqueda);
    }

    return cumpleEstado && cumpleFecha && cumpleBusqueda;
  });

  paginaActual = 1;
  if (datosFiltrados.length === 0) {
    // Limpiar tabla y paginación si no hay datos filtrados
    document.getElementById("tablaPagosBody").innerHTML = "";
    document.getElementById("pagosPagination").innerHTML = "";
  } else {
    mostrarPagosPagina(paginaActual);
  }
}

// Renderiza la paginación
function renderPagosPaginacion(totalPagos) {
  const totalPaginas = Math.ceil(totalPagos / pagosPorPagina);
  const paginacion = document.getElementById("pagosPagination");
  paginacion.innerHTML = "";

  // Botón anterior
  const prevLi = document.createElement("li");
  prevLi.className = `page-item${paginaActual === 1 ? " disabled" : ""}`;
  prevLi.innerHTML = `<a class="page-link" href="#">Anterior</a>`;
  prevLi.addEventListener("click", function (e) {
    e.preventDefault();
    if (paginaActual > 1) {
      paginaActual--;
      mostrarPagosPagina(paginaActual);
    }
  });
  paginacion.appendChild(prevLi);

  // Números de página
  for (let i = 1; i <= totalPaginas; i++) {
    const pageLi = document.createElement("li");
    pageLi.className = `page-item${paginaActual === i ? " active" : ""}`;
    pageLi.innerHTML = `<a class="page-link" href="#">${i}</a>`;
    pageLi.addEventListener("click", function (e) {
      e.preventDefault();
      paginaActual = i;
      mostrarPagosPagina(paginaActual);
    });
    paginacion.appendChild(pageLi);
  }

  // Botón siguiente
  const nextLi = document.createElement("li");
  nextLi.className = `page-item${
    paginaActual === totalPaginas ? " disabled" : ""
  }`;
  nextLi.innerHTML = `<a class="page-link" href="#">Siguiente</a>`;
  nextLi.addEventListener("click", function (e) {
    e.preventDefault();
    if (paginaActual < totalPaginas) {
      paginaActual++;
      mostrarPagosPagina(paginaActual);
    }
  });
  paginacion.appendChild(nextLi);
}

// Muestra los pagos de la página actual
function mostrarPagosPagina(pagina) {
  const start = (pagina - 1) * pagosPorPagina;
  const end = start + pagosPorPagina;

  const datosAMostrar = datosFiltrados.length > 0 ? datosFiltrados : pagosData;

  renderPagosTabla(datosAMostrar.slice(start, end));
  renderPagosPaginacion(datosAMostrar.length);
}

// Carga los pagos desde el servidor
function cargarPagos() {
  fetch( API_CONFIG + "/pagos/mostrarPagos", {
    credentials: "include",
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success" && Array.isArray(data.data)) {
        pagosData = data;
        datosFiltrados = [...pagosData.data];
      } else {
        pagosData = [];
      }
      paginaActual = 1;
      mostrarPagosPagina(paginaActual);
    })
    .catch((error) => {
      console.error("Error al cargar pagos:", error);
      pagosData = [];
      mostrarPagosPagina(1);
    });
}

// Consulta detalle de un pago y muestra en modal
function mostrarDetallePago() {
  const idPago = this.getAttribute("data-idpago");
  const modalBody = document.querySelector("#orderModal #detallePagoBody");
  modalBody.innerHTML =
    '<div class="text-center my-4"><div class="spinner-border"></div></div>';

  const valorDolar = 100.5; // Tasa de cambio actual

  fetch( API_CONFIG + `/pagos/consultarPago?idPago=${idPago}`, {
    credentials: "include",
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.pago) {
        let totalBs = 0;
        let totalUsd = 0;

        data.detalle_pago.forEach((dp) => {
          const monto = parseFloat(dp.monto);
          if (dp.simbolo === "Bs") {
            totalBs += monto;
          } else if (dp.simbolo === "$") {
            totalUsd += monto;
          }
        });

        const totalEnUsdFinal = totalUsd + totalBs / valorDolar;

        const html = `
                <ul class="list-group mb-3">
                    <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">Fecha:</span><span>${
                      data.pago.fecha
                    }</span></li>
                    <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">Estado:</span><span>${
                      data.pago.estatus
                    }</span></li>
                    <li class="list-group-item d-flex justify-content-between"><span class="fw-bold">Cliente:</span><span>${
                      data.pago.cliente.nombre
                    } ${data.pago.cliente.apellido} (${
          data.pago.cliente.correo
        })</span></li>
                </ul>
                <h6>Detalle del Pago</h6>
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Método</th>
                            <th>Moneda</th>
                            <th>Monto</th>
                            <th>Referencia</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${data.detalle_pago
                          .map(
                            (dp) => `
                            <tr>
                                <td>${dp.metodo_pago}</td>
                                <td>${dp.simbolo} (${dp.moneda})</td>
                                <td>${dp.simbolo} ${parseFloat(
                              dp.monto
                            ).toFixed(2)}</td>
                                <td>${dp.referencia || "-"}</td>
                            </tr>
                        `
                          )
                          .join("")}
                        <tr class="table-secondary fw-bold">
                            <td colspan="2" class="text-end">Total en Bs:</td>
                            <td colspan="2">Bs ${totalBs.toFixed(2)}</td>
                        </tr>
                        <tr class="table-secondary fw-bold">
                            <td colspan="2" class="text-end">Total en $:</td>
                            <td colspan="2">$ ${totalUsd.toFixed(2)}</td>
                        </tr>
                        <tr class="table-success fw-bold">
                            <td colspan="2" class="text-end">Total general en $:</td>
                            <td colspan="2">$ ${totalEnUsdFinal.toFixed(2)}</td>
                        </tr>
                    </tbody>
                </table>
            `;
        modalBody.innerHTML = html;
      } else {
        modalBody.innerHTML =
          '<div class="alert alert-danger">No se encontraron detalles para este pago.</div>';
      }
    })
    .catch(() => {
      modalBody.innerHTML =
        '<div class="alert alert-danger">Error al consultar el pago.</div>';
    });
}

const valorDolar = 100.5;

// Calcula el total de productos y actualiza el monto a pagar
function calcularTotalProductos(productos) {
  let total = 0;
  productos.forEach((prod) => {
    total += Number(prod.total_producto);
  });
  document.getElementById("montoPago").value = total.toFixed(2);
  document.getElementById("totalPagarPedido").textContent = `$${total.toFixed(
    2
  )}`;
  document.getElementById("faltaPagar").value = `$${total.toFixed(2)}`;
  actualizarTotalDesglose();
}

document.addEventListener("change", function (e) {
  if (e.target && e.target.id === "selectPedido") {
    const pedidoId = parseInt(e.target.value);
    const tbody = document.getElementById("tablaProductosPedido");
    const container = document.getElementById("productosPedidoContainer");
    tbody.innerHTML = "";
    if (pedidoId) {
      document.getElementById("desglosePagoContainer").style.display = "";
      // Solo ocultar si NO hay pedido seleccionado
      fetch(
         API_CONFIG + `/pagos/detallesPedido?idPedido=${pedidoId}`,
        { credentials: "include" }
      )
        .then((response) => response.json())
        .then((productos) => {
          if (Array.isArray(productos) && productos.length > 0) {
            productos.forEach((prod) => {
              const tr = document.createElement("tr");
              tr.innerHTML = `
                            <td>${prod.nombre_producto}</td>
                            <td>${prod.cantidad}</td>
                            <td>$${Number(prod.precio_unitario).toFixed(2)}</td>
                            <td>$${Number(prod.total_producto).toFixed(2)}</td>
                        `;
              tbody.appendChild(tr);
            });
            container.style.display = "";
            calcularTotalProductos(productos);
          } else {
            // Solo ocultar si NO hay productos
            ocultarCamposPago();
          }
        });
    } else {
      // Solo ocultar si el select está vacío
      document.getElementById("desglosePagoContainer").style.display = "none";
      ocultarCamposPago();
    }
  }
});

function ocultarCamposPago() {
  document.getElementById("productosPedidoContainer").style.display = "none";
  document.getElementById("totalPagarPedido").textContent = "$0.00";
  document.getElementById("montoPago").value = "";
  document.getElementById("faltaPagar").value = "$0.00";
  document.querySelectorAll("#tablaDesglosePago tbody tr").forEach((tr) => {
    tr.querySelectorAll("input").forEach((input) => {
      input.value = "";
    });
    tr.querySelectorAll("select").forEach((select) => {
      select.selectedIndex = 0;
    });
  });
  actualizarTotalDesglose();
}

function actualizarEstadoPago(id_pago, estatus, id_pedido) {
  fetch( API_CONFIG + "/pagos/actualizarEstadoPago", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      estatus: estatus,
      id_pagos: id_pago,
      id_pedido: id_pedido
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        Swal.fire({
          title: '<span class="fw-bold">¡Éxito!</span>',
          text: 'El pago ha sido actualizado con éxito.',
          icon: 'success',
          confirmButtonText: 'Aceptar',
          customClass: {
            confirmButton: 'btn btn-success px-4 fw-bold',
            popup: 'rounded-4 shadow',
            title: 'fs-4',
            icon: 'mt-2'
          },
          buttonsStyling: false,
          background: '#f8f9fa'
        }).then(() => {
          cargarPagos();
          const modal = bootstrap.Modal.getInstance(
            document.getElementById("verificarPagoModal")
          );
          if (modal) modal.hide();
        });
      } else {
        Swal.fire({
          title: '<span class="fw-bold">Error</span>',
          text: 'Error al actualizar el estado del pago.',
          icon: 'error',
          confirmButtonText: 'Aceptar',
          customClass: {
            confirmButton: 'btn btn-danger px-4 fw-bold',
            popup: 'rounded-4 shadow',
            title: 'fs-4',
            icon: 'mt-2'
          },
          buttonsStyling: false,
          background: '#f8f9fa'
        });
      }
    })
    .catch((error) => {
      console.error(error);
    });
}

