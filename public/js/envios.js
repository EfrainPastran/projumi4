import { mostrarAlerta } from './alertas.js';
import { API_CONFIG } from './config.js';
let tipoDelivery = null;
function obtenerDeliveries() {
fetch( API_CONFIG + "/delivery/obtenerDeliveries", {
  method: "GET",
  headers: {
    Accept: "application/json",
  },
  credentials: "include",
})
  .then((response) => response.json())
  .then((data) => {
    tipoDelivery = data.tipo;
    if (data.success && Array.isArray(data.data)) {
      const tbody = document.getElementById("tabla-delivery");
      const thead = document.getElementById("thead-delivery");
      tbody.innerHTML = "";

      // Construir columnas dinámicamente
      let columns = `
        <th>ID Delivery</th>
      `;
      if (data.tipo === "emprendedor") {
        columns += `<th>Cliente</th>`;
      } else if (data.tipo === "cliente") {
        columns += `<th>Emprendedor</th>`;
      } else {
        columns += `<th>Emprendedor</th><th>Cliente</th>`;
      }
      columns += `
        <th>ID Pedido</th>
        <th>Fecha</th>
        <th>Dirección Exacta</th>
        <th>Destinatario</th>
        <th>Teléfono Destinatario</th>
        <th>Correo Destinatario</th>
        <th>Teléfono Delivery</th>
        <th>Estatus</th>
        ${tipoDelivery !== "cliente" ? `<th>Acción</th>` : ""}
      `;
      thead.innerHTML = columns;

      data.data.forEach((delivery) => {
        let row = `
          <td>${delivery.id_delivery}</td>
        `;
        if (data.tipo === "emprendedor") {
          row += `<td>${delivery.nombre_cliente ?? ""} ${delivery.apellido_cliente ?? ""}</td>`;
        } else if (data.tipo === "cliente") {
          row += `<td>${delivery.nombre_emprendedor ?? ""} ${delivery.apellido_emprendedor ?? ""}</td>`;
        } else {
          row += `<td>${delivery.nombre_emprendedor ?? ""} ${delivery.apellido_emprendedor ?? ""}</td>`;
          row += `<td>${delivery.nombre_cliente ?? ""} ${delivery.apellido_cliente ?? ""}</td>`;
        }
        row += `
          <td>${'PED-' + delivery.id_pedido}</td>
          <td>${delivery.fecha_pedido ?? "-"}</td>
          <td>${delivery.direccion_exacta}</td>
          <td>${delivery.destinatario}</td>
          <td>${delivery.telefono_destinatario}</td>
          <td>${delivery.correo_destinatario}</td>
          <td>${delivery.telefono_delivery ?? "-"}</td>
          <td>${delivery.estatus}</td>
          <td>
            ${
              tipoDelivery !== "cliente"
                ? `<button class="btn btn-sm btn-info ver-delivery" data-delivery='${JSON.stringify(delivery)}'>
                  <i class="fas fa-eye"></i>
                </button>`
                : `<td style="display:none;"></td>`
            }
          </td>
        `;
        const tr = document.createElement("tr");
        tr.innerHTML = row;
        tbody.appendChild(tr);
      });
    }
  })
  .catch((error) => {
    console.error("Error al cargar deliveries:", error);
  });
}
obtenerDeliveries();

    document.addEventListener("click", function (e) {
    if (e.target.closest(".ver-delivery")) {
      const delivery = JSON.parse(e.target.closest(".ver-delivery").dataset.delivery);

      // Rellena los campos
      document.getElementById("delivery_id").value = delivery.id_delivery;
      document.getElementById("delivery_direccion").value = delivery.direccion_exacta ?? "";
      document.getElementById("delivery_destinatario").value = delivery.destinatario ?? "";
      document.getElementById("delivery_telefono_destinatario").value = delivery.telefono_destinatario ?? "";
      document.getElementById("delivery_correo_destinatario").value = delivery.correo_destinatario ?? "";
      document.getElementById("delivery_telefono_delivery").value = delivery.telefono_delivery ?? "";
      document.getElementById("delivery_estatus").value = delivery.estatus ?? "";
      document.getElementById("delivery_telefono_delivery").disabled = false;

      // Rellenar campos de cliente y emprendedor
      document.getElementById("delivery_cliente").value = (delivery.nombre_cliente ?? "") + " " + (delivery.apellido_cliente ?? "");
      document.getElementById("delivery_emprendedor").value = (delivery.nombre_emprendedor ?? "") + " " + (delivery.apellido_emprendedor ?? "");

      // Mostrar/ocultar campos según el tipo
      if (tipoDelivery === "emprendedor") {
        document.querySelector(".campo-cliente1").style.display = "";
        document.querySelector(".campo-emprendedor1").style.display = "none";
      } else if (tipoDelivery === "cliente") {
        document.querySelector(".campo-cliente1").style.display = "none";
        document.querySelector(".campo-emprendedor1").style.display = "";
      } else {
        document.querySelector(".campo-cliente1").style.display = "";
        document.querySelector(".campo-emprendedor1").style.display = "";
      }

      new bootstrap.Modal(document.getElementById("modalDelivery")).show();
    }
  });

  let tipoEnvio = null;

function obtenerEnvios() {
  fetch( API_CONFIG + "/envios/mostrarEnvios", {
  method: "GET",
  headers: {
    Accept: "application/json",
  },
  credentials: "include",
})
  .then((response) => response.json())
  .then((data) => {
    tipoEnvio = data.tipo;
    if (data.success && Array.isArray(data.data)) {
      const tbody = document.getElementById("tabla-envios");
      const thead = document.getElementById("tabla-envios-header");
      tbody.innerHTML = "";

      // Construir columnas dinámicamente
      let columns = `
        <th>ID Envío</th>
      `;
      if (data.tipo === "emprendedor") {
        columns += `<th>Cliente</th>`;
      } else if (data.tipo === "cliente") {
        columns += `<th>Emprendedor</th>`;
      } else {
        columns += `<th>Emprendedor</th><th>Cliente</th>`;
      }
      columns += `
        <th>ID Pedido</th>
        <th>Fecha</th>
        <th>Teléfono Empresa</th>
        <th>Dirección de Envío</th>
        <th>N° Seguimiento</th>
        <th>Empresa</th>
        <th>Estado</th>
        ${tipoEnvio !== "cliente" ? `<th>Acción</th>` : ""}
      `;
      thead.innerHTML = columns;

      data.data.forEach((envio) => {
        let row = `
          <td>${envio.id_envio}</td>
        `;
        if (data.tipo === "emprendedor") {
          row += `<td>${envio.nombre_cliente ?? ""} ${envio.apellido_cliente ?? ""}</td>`;
        } else if (data.tipo === "cliente") {
          row += `<td>${envio.nombre_emprendedor ?? ""} ${envio.apellido_emprendedor ?? ""}</td>`;
        } else {
          row += `<td>${envio.nombre_emprendedor ?? ""} ${envio.apellido_emprendedor ?? ""}</td>`;
          row += `<td>${envio.nombre_cliente ?? ""} ${envio.apellido_cliente ?? ""}</td>`;
        }
        row += `
          <td>${'PED-' + envio.id_pedido}</td>
          <td>${envio.fecha_pedido ?? "-"}</td>
          <td>${envio.telefono_empresa_envio ?? "-"}</td>
          <td>${envio.direccion_envio ?? "-"}</td>
          <td>${envio.numero_seguimiento ?? "-"}</td>
          <td>${envio.nombre_empresa_envio ?? "-"}</td>
          <td>${envio.estatus_envio ?? "-"}</td>
          <td>
            ${
              tipoEnvio !== "cliente"
                ? `<button class="btn btn-sm btn-info ver-envio" data-envio='${JSON.stringify(envio)}'>
                  <i class="fas fa-eye"></i>
                </button>`
                : `<td style="display:none;"></td>`
            }
          </td>
        `;
        const tr = document.createElement("tr");
        tr.innerHTML = row;
        tbody.appendChild(tr);
      });
    }
  })
  .catch((error) => {
    console.error("Error al cargar envíos:", error);
  });
}
obtenerEnvios();
document.addEventListener("click", function (e) {
  if (e.target.closest(".ver-envio")) {
    const envio = JSON.parse(e.target.closest(".ver-envio").dataset.envio);

    // Rellenar los campos
    document.getElementById("envio_id").value = envio.id_envio;
    document.getElementById("envio_cliente").value = (envio.nombre_cliente ?? "") + " " + (envio.apellido_cliente ?? "");
    document.getElementById("envio_emprendedor").value = (envio.nombre_emprendedor ?? "") + " " + (envio.apellido_emprendedor ?? "");
    document.getElementById("envio_telefono_empresa").value = envio.telefono_empresa_envio ?? "";
    document.getElementById("envio_direccion").value = envio.direccion_envio ?? "";
    document.getElementById("envio_telefono").value = envio.telefono_cliente ?? "";
    document.getElementById("envio_estado").value = envio.estatus_envio ?? "";
    document.getElementById("envio_seguimiento").value = envio.numero_seguimiento ?? "";
    document.getElementById("envio_empresa").value = envio.nombre_empresa_envio ?? "";

    const estadoSelect = document.getElementById("envio_estado");
    const seguimientoInput = document.getElementById("envio_seguimiento");
    const estado = (envio.estatus_envio ?? "").toLowerCase();

    // Aplicar restricciones por estado
    if (estado === "entregado") {
      estadoSelect.disabled = true;
      seguimientoInput.disabled = true;
    } else if (estado === "anulado") {
      estadoSelect.disabled = false;
      seguimientoInput.disabled = true;
      seguimientoInput.value = ""; // eliminar valor
    } else {
      estadoSelect.disabled = false;
      seguimientoInput.disabled = false;
    }

    // Mostrar/ocultar campos según el tipo
    if (tipoEnvio === "emprendedor") {
      document.querySelector(".campo-cliente").style.display = "";
      document.querySelector(".campo-emprendedor").style.display = "none";
    } else if (tipoEnvio === "cliente") {
      document.querySelector(".campo-cliente").style.display = "none";
      document.querySelector(".campo-emprendedor").style.display = "";
    } else {
      document.querySelector(".campo-cliente").style.display = "";
      document.querySelector(".campo-emprendedor").style.display = "";
    }

    // Mostrar el modal
    new bootstrap.Modal(document.getElementById("modalEnvio")).show();
  }
});


  function filtrarTablas() {
    const filtro = document
      .getElementById("busquedaEnvios")
      .value.toLowerCase();

    // Filtrar tabla de envíos
    document.querySelectorAll("#tabla-envios tr").forEach((tr) => {
      const texto = tr.textContent.toLowerCase();
      tr.style.display = texto.includes(filtro) ? "" : "none";
    });

    // Filtrar tabla de delivery
    document.querySelectorAll("#tabla-delivery tr").forEach((tr) => {
      const texto = tr.textContent.toLowerCase();
      tr.style.display = texto.includes(filtro) ? "" : "none";
    });
  }

  document
    .getElementById("busquedaEnvios")
    .addEventListener("input", filtrarTablas);
  document
    .getElementById("buscarEnvios")
    .addEventListener("click", filtrarTablas);


document.getElementById('guardarEnvio').addEventListener('click', function () {
  const payload = {
      id_envio: document.getElementById('envio_id').value,
      direccion_envio: document.getElementById('envio_direccion').value,
      estatus_envio: document.getElementById('envio_estado').value,
      numero_seguimiento: document.getElementById('envio_seguimiento').value,
      empresa_envio: document.getElementById('envio_empresa').value
  };

  fetch( API_CONFIG + '/envios/actualizarEnvio', {
      method: 'POST',
      headers: {
          'Content-Type': 'application/json'
      },
      body: JSON.stringify(payload)
  })
  .then(response => response.json())
  .then(data => {
      if (data.success) {
          mostrarAlerta('Envío', 'fue actualizado correctamente', 'success');
          $('#modalEnvio').modal('hide');
          obtenerEnvios();
      } else {
          alert(' Error: ' + data.message);
      }
  })
  .catch(error => {
      alert('Error al enviar la solicitud: ' + error);
  });
});


document.getElementById('guardarDelivery').addEventListener('click', function () {
    const payload = new FormData();
    payload.append('id_delivery', document.getElementById('delivery_id').value);
    payload.append('telefono_delivery', document.getElementById('delivery_telefono_delivery').value);

    fetch( API_CONFIG + '/delivery/aprobarDelivery', {
        method: 'POST',
        body: payload
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Cerrar el modal sin jQuery
            const modalElement = document.getElementById('modalDelivery');
            const modalInstance = bootstrap.Modal.getInstance(modalElement);
            modalInstance.hide();
            mostrarAlerta('Delivery', 'fue aprobado correctamente', 'success');
            obtenerDeliveries();
        } else {
            alert('⚠️ Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('❌ Error al enviar la solicitud: ' + error);
    });
});

