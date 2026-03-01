import { API_CONFIG } from './config.js';
document.addEventListener("DOMContentLoaded", () => {
  const ITEMS_POR_PAGINA = 6;
  let paginaActual = 1;
  let emprendedoresGlobal = [];

  function renderizarEmprendedores() {
    const contenedor = document.getElementById("contenedor-emprendedores");
    contenedor.innerHTML = "";

    const inicio = (paginaActual - 1) * ITEMS_POR_PAGINA;
    const fin = inicio + ITEMS_POR_PAGINA;
    const paginaEmprendedores = emprendedoresGlobal.slice(inicio, fin);

    paginaEmprendedores.forEach((emprendedor) => {
      const {
        id_emprendedor,
        nombre_completo,
        emprendimiento,
        imagen,
        categorias,
      } = emprendedor;

      const colores = [
        "primary",
        "success",
        "danger",
        "warning",
        "info",
        "secondary",
        "dark",
      ];

      const categoriasHTML = categorias
        .map((cat, index) => {
          const color = colores[index % colores.length];
          return `<span class="badge bg-${color}">${cat.nombre} (${cat.cantidad_productos})</span>`;
        })
        .join(" ");

      const cardHTML = `
        <div class="col-md-4 col-lg-3 mb-4">
          <div class="card h-100">
            <div class="card-img-top" style="
                background-image: url( '${API_CONFIG}/${imagen}');
                height: 270px;
                background-size: cover;
                background-position: center;
                border-top-left-radius: .5rem;
                border-top-right-radius: .5rem;
                width: 100%;
            "></div>

            <div class="card-body">
              <h6 class="card-title">Nombre: <strong>${nombre_completo}</strong></h6>
              <p class="card-text">Emprendimiento: <strong>${emprendimiento}</strong></p>
              <div class="d-flex flex-wrap gap-1">${categoriasHTML}</div>
            </div>

            <div class="card-footer bg-transparent">
            <a 
                href="${APP_URL}/productos/mostrarProductosEmprendedor?id_emprendedor=${id_emprendedor}&nombre_completo=${encodeURIComponent(nombre_completo)}" 
                class="btn btn-sm btn-primary w-100"
                >
                Ver oferta del emprendedor
            </a>
            </div>
          </div>
        </div>
      `;

      contenedor.innerHTML += cardHTML;
    });

    renderizarPaginacion();
  }

  function renderizarPaginacion() {
    const totalPaginas = Math.ceil(emprendedoresGlobal.length / ITEMS_POR_PAGINA);
    const paginacion = document.getElementById("paginacion-emprendedores");
    paginacion.innerHTML = "";

    // Botón anterior
    paginacion.innerHTML += `
      <li class="page-item ${paginaActual === 1 ? "disabled" : ""}">
        <a class="page-link" href="#" onclick="cambiarPagina(${paginaActual - 1}); return false;">Anterior</a>
      </li>
    `;

    // Botones numéricos
    for (let i = 1; i <= totalPaginas; i++) {
      paginacion.innerHTML += `
        <li class="page-item ${paginaActual === i ? "active" : ""}">
          <a class="page-link text-white" href="#" onclick="cambiarPagina(${i}); return false;">${i}</a>
        </li>
      `;
    }

    paginacion.innerHTML += `
      <li class="page-item ${paginaActual === totalPaginas ? "disabled" : ""}">
        <a class="page-link" href="#" onclick="cambiarPagina(${paginaActual + 1}); return false;">Siguiente</a>
      </li>
    `;
  }

  // Esta función la dejo global para que los onclick puedan acceder
  window.cambiarPagina = function (pagina) {
    const totalPaginas = Math.ceil(emprendedoresGlobal.length / ITEMS_POR_PAGINA);
    if (pagina < 1 || pagina > totalPaginas) return; // evitar páginas inválidas
    paginaActual = pagina;
    renderizarEmprendedores();
  };

  fetch( API_CONFIG + "/emprendedor/mostrarEmprendedores")
    .then((res) => res.json())
    .then((data) => {
      emprendedoresGlobal = data;
      renderizarEmprendedores();
    })
    .catch((error) => {
      console.error("Error cargando emprendedores:", error);
    });

    fetch( API_CONFIG + "/tasa/registrarTasaBcv")
    .then((res) => res.json())
    .then((data) => {
    })
    .catch((error) => {
      console.error("Error cargando emprendedores:", error);
    });
});
