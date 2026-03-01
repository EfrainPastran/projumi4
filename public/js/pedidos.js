import { API_CONFIG } from './config.js';
document.addEventListener("DOMContentLoaded", cargarPedidos);
let pedidosData = [];
let currentPage = 1;
let pedidosFiltrados = [];
document
  .getElementById("applyFilters")
  .addEventListener("click", aplicarFiltros);
document
  .getElementById("searchInput")
  .addEventListener("input", aplicarFiltros);
const pedidosPorPagina = 10;
let pedidosTipo = null;

function renderPedidosTable(pedidos) {
  const thead = document.getElementById("pedidosHead");
  let columns = `
    <th># Pedido</th>
    <th>Fecha</th>
  `;
  if (pedidosTipo === "emprendedor") {
    columns += `<th>Cliente</th>`;
  } else if (pedidosTipo === "cliente") {
    columns += `<th>Emprendedor</th>`;
  } else {
    columns += `<th>Cliente</th><th>Emprendedor</th>`;
  }
  columns += `
    <th>Total</th>
    <th>Estado</th>
    <th>Acciones</th>
  `;
  thead.innerHTML = `<tr>${columns}</tr>`;

  const tbody = document.getElementById("pedidosBody");
  tbody.innerHTML = "";
  pedidos.forEach((pedido) => {
    let row = `
      <td>#PED-${pedido.id_pedidos}</td>
      <td>${new Date(pedido.fecha_pedido).toLocaleDateString()}</td>
    `;
    if (pedidosTipo === "emprendedor") {
      row += `<td>${pedido.cliente_nombre}</td>`;
    } else if (pedidosTipo === "cliente") {
      row += `<td>${pedido.emprendedor_nombre} </td>`;
    } else {
      row += `<td>${pedido.cliente_nombre} </td>`;
      row += `<td>${pedido.emprendedor_nombre} </td>`;
    }
    row += `
      <td>$${pedido.total_pedido}</td>
      <td>
        <span class="badge ${getBadgeClass(pedido.estatus)}">${pedido.estatus}</span>
      </td>
    
      <td>
        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#orderModal" data-id="${pedido.id_pedidos}">
          <i class="fas fa-eye"></i> Detalles
        </button>
      </td>
    `;
    const tr = document.createElement("tr");
    tr.innerHTML = row;
    tbody.appendChild(tr);
  });
}

function getBadgeClass(estatus) {
  switch (estatus.toLowerCase()) {
    case 'pendiente':
      return 'bg-warning text-dark';
    case 'completado':
      return 'bg-success';
    case 'anulado':
      return 'bg-danger';
    default:
      return 'bg-info text-dark';
  }
}


function renderPagination(datos) {
  const totalPaginas = Math.ceil(datos.length / pedidosPorPagina);
  const paginacion = document.getElementById("pedidosPagination");
  paginacion.innerHTML = "";

  const prevLi = document.createElement("li");
  prevLi.className = `page-item${currentPage === 1 ? " disabled" : ""}`;
  prevLi.innerHTML = `<a class="page-link" href="#">Anterior</a>`;
  prevLi.addEventListener("click", function (e) {
    e.preventDefault();
    if (currentPage > 1) {
      currentPage--;
      mostrarPagina(currentPage);
      renderPagination(datos); // <-- importante actualizar
    }
  });
  paginacion.appendChild(prevLi);

  for (let i = 1; i <= totalPaginas; i++) {
    const pageLi = document.createElement("li");
    pageLi.className = `page-item${currentPage === i ? " active" : ""}`;
    pageLi.innerHTML = `<a class="page-link" href="#">${i}</a>`;
    pageLi.addEventListener("click", function (e) {
      e.preventDefault();
      currentPage = i;
      mostrarPagina(currentPage);
      renderPagination(datos); // <-- importante actualizar
    });
    paginacion.appendChild(pageLi);
  }

  const nextLi = document.createElement("li");
  nextLi.className = `page-item${
    currentPage === totalPaginas ? " disabled" : ""
  }`;
  nextLi.innerHTML = `<a class="page-link" href="#">Siguiente</a>`;
  nextLi.addEventListener("click", function (e) {
    e.preventDefault();
    if (currentPage < totalPaginas) {
      currentPage++;
      mostrarPagina(currentPage);
      renderPagination(datos); // <-- importante actualizar
    }
  });
  paginacion.appendChild(nextLi);
}

function mostrarPagina(page) {
  currentPage = page;
  const start = (currentPage - 1) * pedidosPorPagina;
  const end = start + pedidosPorPagina;
  const datosAMostrar =
    pedidosFiltrados.length > 0 ? pedidosFiltrados : pedidosData;
  renderPedidosTable(datosAMostrar.slice(start, end));
  renderPagination(datosAMostrar);
}

function aplicarFiltros() {
  const estado = document.getElementById("statusFilter").value.trim().toLowerCase();
  const fecha = document.getElementById("dateFilter").value;
  const busqueda = document.getElementById("searchInput").value.toLowerCase();

  const hoy = new Date();
  const inicioSemana = new Date(hoy);
  inicioSemana.setDate(hoy.getDate() - hoy.getDay());
  const inicioMes = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
  


  pedidosFiltrados = pedidosData.filter((pedido) => {
    const cumpleEstado =
      estado === "all" || (pedido.estatus || "").toLowerCase() === estado;

    const fechaPedido = new Date(pedido.fecha_pedido);
    let cumpleFecha = true;
    if (fecha === "today") {
      cumpleFecha =
        fechaPedido.getFullYear() === hoy.getFullYear() &&
        fechaPedido.getMonth() === hoy.getMonth() &&
        fechaPedido.getDate() === hoy.getDate();
    } else if (fecha === "week") {
      cumpleFecha = fechaPedido >= inicioSemana;
    } else if (fecha === "month") {
      cumpleFecha = fechaPedido >= inicioMes;
    }

    const cumpleBusqueda =
      `ped-${pedido.id_pedidos}`.toLowerCase().includes(busqueda) ||
      new Date(pedido.fecha_pedido).toLocaleDateString().toLowerCase().includes(busqueda) ||
      (pedido.emprendedor_nombre && pedido.emprendedor_nombre.toLowerCase().includes(busqueda)) ||
      (pedido.cliente_nombre && pedido.cliente_nombre.toLowerCase().includes(busqueda)) ||
      pedido.total_pedido.toString().toLowerCase().includes(busqueda);

    return cumpleEstado && cumpleFecha && cumpleBusqueda;
  });

  currentPage = 1;

  if (pedidosFiltrados.length === 0) {
    document.getElementById("pedidosBody").innerHTML = "";
    document.getElementById("pedidosPagination").innerHTML = "";
  } else {
    mostrarPagina(currentPage);
  }
}


function cargarPedidos() {
  fetch( API_CONFIG + "/pedidos/mostrarPedidos", {
    credentials: "include",
  })
    .then((response) => response.json())
    .then((result) => {
      if (result.status === "success") {
        pedidosTipo = result.tipo; // <-- Aquí guardas el tipo
        pedidosData = result.data;
        aplicarFiltros();
        mostrarPagina(1);
      } else {
        let html = `
    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead>
          <tr>
            <th># Pedido</th> 
            <th>Fecha</th>
            <th>Usuario</th>
            <th>Total</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
            <tr><td colspan="7" class="text-center">No hay pedidos registrados.</td></tr>
        </tbody>
      </table>
    </div>
  `;
        document.getElementById("pedidosBody").innerHTML = html;
        //alert("No se pudieron cargar los pedidos");
      }
    })
    .catch((error) => {
      alert("Error al cargar pedidos");
      console.error(error);
    });
}

// Evento para paginación
document.querySelector(".pagination").addEventListener("click", function (e) {
  if (e.target.classList.contains("page-link")) {
    e.preventDefault();
    const page = parseInt(e.target.getAttribute("data-page"));
    const totalPages = Math.ceil(pedidosData.length / pedidosPorPagina);
    if (page >= 1 && page <= totalPages) {
      mostrarPagina(page);
    }
  }
});

document.getElementById("pedidosBody").addEventListener("click", function (e) {
  if (e.target.closest("button[data-id]")) {
    const btn = e.target.closest("button[data-id]");
    const idPedido = btn.getAttribute("data-id");

    fetch(API_CONFIG + `/pedidos/consultarPedido?idPedido=${idPedido}`, {
      credentials: "include",
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success && data.pedido) {
          const pedido = data.pedido;
          let infoCards = "";

          if (pedidosTipo === "emprendedor") {
            // Solo muestra la tarjeta de Cliente
            infoCards = `
              <div class="col-md-6 d-flex">
                <div class="card shadow-sm flex-fill">
                  <div class="card-body d-flex flex-column justify-content-between h-100">
                    <h6 class="card-title mb-2"><i class="fas fa-user"></i> Cliente</h6>
                    <p class="mb-1"><strong>Nombre:</strong> ${pedido.cliente_nombre} ${pedido.cliente_apellido}</p>
                    <p class="mb-0"><strong>Correo:</strong> ${pedido.correo}</p>
                  </div>
                </div>
              </div>
            `;
          }

          document.getElementById("pedidoInfo").innerHTML = `
            <div class="row g-3 align-items-stretch">
              <div class="${pedidosTipo === "emprendedor" ? "col-md-6" : "col-md-12"} d-flex">
                <div class="card shadow-sm flex-fill">
                  <div class="card-body d-flex flex-column justify-content-between h-100">
                    <h6 class="card-title mb-2"><i class="fas fa-receipt"></i> Pedido</h6>
                    <p class="mb-1"><strong>ID:</strong> #PED-${pedido.id_pedidos}</p>
                    <p class="mb-1"><strong>Fecha:</strong> ${new Date(pedido.fecha_pedido).toLocaleString()}</p>
                    <p class="mb-0"><strong>Estatus:</strong> <span class="badge bg-primary">${pedido.estatus}</span></p>
                  </div>
                </div>
              </div>
              ${infoCards}
            </div>
          `;

          // === Productos ===
          const detalleBody = document.getElementById("detalleProductosBody");
          detalleBody.innerHTML = "";

          let total = 0;
          data.detalle_productos.forEach((prod) => {
            detalleBody.innerHTML += `
              <tr>
                <td>${prod.nombre_producto}</td>
                <td>${prod.categoria}</td>
                <td>${prod.cantidad}</td>
                <td>$${prod.precio_unitario}</td>
                <td>$${prod.total_producto}</td>
              </tr>
            `;
            total += Number(prod.total_producto);
          });

          // Mostrar total
          document.getElementById("totalPedido").textContent = `$${total.toFixed(2)}`;
        } else {
          document.getElementById("pedidoInfo").innerHTML =
            '<span class="text-danger">No se encontró información del pedido.</span>';
        }
      })
      .catch((error) => {
        console.error("Error al cargar pedido:", error);
        document.getElementById("pedidoInfo").innerHTML =
          '<span class="text-danger">No se pudo cargar el pedido.</span>';
        document.getElementById("detalleProductosBody").innerHTML = "";
        document.getElementById("totalPedido").textContent = "";
      });
  }
});

