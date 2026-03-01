import { API_CONFIG } from './config.js';
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".btn-view-details").forEach((btn) => {
        btn.addEventListener("click", () => {
            const id = btn.dataset.id;
            const producto = productos.find((p) => p.id_producto == id);
            const imagen = producto.imagenes && producto.imagenes.length > 0
            ? producto.imagenes[0]
            : "public/img/default.jpg";
            if (producto) {
                document.getElementById("id_producto").value = producto.id_producto;
                document.getElementById("id_emprendedor").value = producto.id_emprededor;
                document.getElementById("modalProductImage").src = `${API_CONFIG}/${imagen}`;
                document.getElementById("modalProductName").textContent =
                    producto.nombre;
                document.getElementById("modalProductPrice").textContent =
                    "$" + parseFloat(producto.precio).toFixed(2);
                document.getElementById("modalProductDescription").textContent =
                    producto.descripcion;
                document.getElementById("modalProductStock").textContent =
                    producto.stock;
                document.getElementById("modalProductEmprendedor").textContent =
                    producto.nombre_categoria;
                document.getElementById("modalProductCategory").textContent =
                    producto.nombre_categoria;
                document.getElementById("productQuantity").value = 1;
            }
        });
    });
});
// Eventos para aumentar/disminuir cantidad
document
    .getElementById("increaseQuantity")
    .addEventListener("click", function () {
        const quantityInput = document.getElementById("productQuantity");
        quantityInput.value = parseInt(quantityInput.value) + 1;
    });

document
    .getElementById("decreaseQuantity")
    .addEventListener("click", function () {
        const quantityInput = document.getElementById("productQuantity");
        if (parseInt(quantityInput.value) > 1) {
            quantityInput.value = parseInt(quantityInput.value) - 1;
        }
    });

document.addEventListener('DOMContentLoaded', function () {
  const modal = document.getElementById("productModal");

  if (modal) {
    modal.addEventListener('shown.bs.modal', function () {
      const btnAdd = document.getElementById("addToCartBtn");
      if (btnAdd) {
        btnAdd.addEventListener("click", function () {
          const quantity = parseInt(document.getElementById("productQuantity").value);
          const productId = document.getElementById("id_producto").value;
          const id_emprededor = document.getElementById("id_emprendedor").value;
          const emprendedor = document.getElementById("nombre_completo").value;
          const product = {
            category: document.getElementById("modalProductCategory").textContent,
            description: document.getElementById("modalProductDescription").textContent,
            emprendedor: emprendedor,
            id: productId,
            id_emprendedor: id_emprededor,
            image: document.getElementById("modalProductImage").src,
            name: document.getElementById("modalProductName").textContent,
            price: parseFloat(document.getElementById("modalProductPrice").textContent.replace("$", "")),
            quantity: quantity,
            stock: parseInt(document.getElementById("modalProductStock").textContent),
          };

          let carritoLocal = JSON.parse(localStorage.getItem("carritoPorEmprendedor")) || {};
          const emprendedoresEnCarrito = Object.keys(carritoLocal);
          const mismoEmprendedor = emprendedoresEnCarrito.length === 0 || emprendedoresEnCarrito.includes(id_emprededor);

          if (!mismoEmprendedor) {
            const primerEmprendedorId = emprendedoresEnCarrito[0];
            const nombre_emprendedor = carritoLocal[primerEmprendedorId]?.[0]?.emprendedor || 'otro emprendedor';
            Swal.fire({
              title: '<span class="fw-bold">Producto no agregado</span>',
              text: 'Ya tienes productos en el carrito del emprendedor: "' + nombre_emprendedor + '". Finaliza esa compra antes de agregar productos de otro emprendedor.',
              icon: 'warning',
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
            return;
          }

          if (!carritoLocal[id_emprededor]) {
            carritoLocal[id_emprededor] = [];
          }

          const existente = carritoLocal[id_emprededor].find((p) => p.id === product.id);
          if (existente) {
            existente.quantity += quantity;
          } else {
            carritoLocal[id_emprededor].push(product);
          }

          localStorage.setItem("carritoPorEmprendedor", JSON.stringify(carritoLocal));
          localStorage.setItem("id_emprendedor", id_emprededor);
          actualizarContadorCarrito();

          if (!localStorage.getItem("carritoExpiracion")) {
            const expiracion = Date.now() + 24 * 60 * 60 * 1000; // 24 horas
            localStorage.setItem("carritoExpiracion", expiracion.toString());
          }

          const modalInstance = bootstrap.Modal.getInstance(document.getElementById("productModal"));
          if (modalInstance) {
            modalInstance.hide();
          }
        });
      }
    });
  }
});
