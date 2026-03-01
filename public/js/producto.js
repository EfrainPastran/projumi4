import { mostrarAlerta } from './alertas.js';
import { API_CONFIG } from './config.js';
document.addEventListener("DOMContentLoaded", function () {
    const checkboxPorcion = document.getElementById("productoPorPorcion");
    const porcionesFields = document.getElementById("porcionesFields");

    checkboxPorcion.addEventListener("change", function () {
        if (this.checked) {
            porcionesFields.style.display = "block";
            // Opcional: hacer requeridos los inputs
            document.getElementById("cantidadPorciones").required = true;
            document.getElementById("precioPorPorcion").required = true;
        } else {
            porcionesFields.style.display = "none";
            document.getElementById("cantidadPorciones").required = false;
            document.getElementById("precioPorPorcion").required = false;
            document.getElementById("cantidadPorciones").value = '';
            document.getElementById("precioPorPorcion").value = '';
        }
    });
});
document.addEventListener('DOMContentLoaded', function() {
    // Datos de ejemplo de productos
    let products = [];
    let filteredProducts = [];
    let currentPage = 1;
    const productsPerPage = 9;

    // Cargar productos desde la API
    function cargarProductos() {
    fetch( API_CONFIG + '/productos/mostrarProductosPorEmprendedor' )
    .then(response => response.json())
    .then(data => {
      products = data.map(p => ({
        id: p.id_producto,
        name: p.nombre,
        price: parseFloat(p.precio),
        description: p.descripcion,
        stock: parseInt(p.stock),
        category: p.nombre_categoria,
        idCategory: p.id_categoria,
        status: p.stock === 0 ? 'soldout' : 'active', 
        image: (p.imagenes && p.imagenes.length > 0)
        ? (p.imagenes[0].startsWith('http') ? p.imagenes[0] : `../${p.imagenes[0]}`)
        : 'https://via.placeholder.com/400x300?text=Sin+imagen',
        emprendedor: p.emprendedor
      }));
      filteredProducts = [...products]; 
      displayProducts();
      setupPagination();
    })
    .catch(error => {
      console.error('Error al cargar productos:', error);
    });
    }
    cargarProductos();
    displayProducts();
    setupPagination();

    // Evento de búsqueda
    document.getElementById('searchInput').addEventListener('input', function() {
        filterProducts();
    });

    // Evento de filtro por categoría
    document.getElementById('categoryFilter').addEventListener('change', function() {
        filterByCategory();
    });

    // Evento para abrir modal de agregar producto
    document.getElementById('addProductBtn').addEventListener('click', function() {
        openProductModal();
    });

    // Evento para registrar producto
    document.getElementById('saveProductBtn').addEventListener('click', function() {
        saveProduct();
    });

    // Evento para confirmar eliminación
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        deleteProduct();
    });

    // Función para filtrar productos
    function filterProducts() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();

        filteredProducts = products.filter(product => {
            const matchesSearch = product.name.toLowerCase().includes(searchTerm) || 
                                product.description.toLowerCase().includes(searchTerm);
            return matchesSearch;
        });

        currentPage = 1;
        displayProducts();
        setupPagination();
    }

    function filterByCategory() {
        const categoryId = document.getElementById('categoryFilter').value;
        if (categoryId === "") {
            filteredProducts = [...products]; // Mostrar todos
        } else {
            filteredProducts = products.filter(product => {
                return String(product.idCategory) === String(categoryId);
            });
        }
        currentPage = 1;
        displayProducts();
        setupPagination();
    }

    // Mostrar productos con paginación
    function displayProducts() {
        const productsGrid = document.getElementById('productsGrid');
        productsGrid.innerHTML = '';

        const startIndex = (currentPage - 1) * productsPerPage;
        const endIndex = startIndex + productsPerPage;
        const paginatedProducts = filteredProducts.slice(startIndex, endIndex);

        if (paginatedProducts.length === 0) {
            productsGrid.innerHTML = `
                <div class="col-12 text-center py-5">
                    <i class="fas fa-box-open fa-3x mb-3" style="color: var(--sky-blue);"></i>
                    <h4>No se encontraron productos</h4>
                    <p class="text-muted">Prueba con otros filtros o agrega nuevos productos</p>
                </div>
            `;
            return;
        }

        paginatedProducts.forEach(product => {
            const productElement = createProductElement(product);
            productsGrid.appendChild(productElement);
        });

        // Agregar eventos a los botones
        addProductEvents();
    }

    // Crear elemento HTML para un producto
    function createProductElement(product) {
        const col = document.createElement('div');
        col.className = 'col-md-4 col-sm-6 mb-4';

        const statusBadge = getStatusBadge(product.status);
        const statusText = getStatusText(product.status);

        // Creamos las variables de los botones vacías
        let btnEditar = '';
        let btnEliminar = '';

        // Verificamos permisos 
        if (PERMISOS_USUARIO['actualizar']) {
            btnEditar = `
                <button class="btn btn-edit edit-product" data-id="${product.id}">
                    <i class="fas fa-edit"></i> Editar
                </button>`;
        }

        if (PERMISOS_USUARIO['eliminar']) {
            btnEliminar = `
                <button class="btn btn-delete delete-product" data-id="${product.id}" data-name="${product.name}">
                    <i class="fas fa-trash-alt"></i> Eliminar
                </button>`;
        }

        col.innerHTML = `
            <div class="product-card">
                <div class="product-image">
                    <img src="${product.image}" alt="${product.name}">
                </div>
                <div class="product-info">
                    <h4 class="product-name">${product.name}</h4>
                    <div class="product-price">$${product.price.toFixed(2)}</div>
                    <div class="product-stock">Stock: ${product.stock} unidades | ${statusText}</div>
                    <p class="product-description">${product.description}</p>
                    <div class="product-actions">
                        ${btnEditar}
                        ${btnEliminar}
                    </div>
                </div>
            </div>
        `;

        return col;
    }

    // Obtener badge de estado
    function getStatusBadge(status) {
        switch(status) {
            case 'active': return { class: 'badge-active', text: 'Activo' };
            case 'inactive': return { class: 'badge-inactive', text: 'Inactivo' };
            case 'soldout': return { class: 'badge-soldout', text: 'Agotado' };
            default: return { class: 'badge-inactive', text: 'Inactivo' };
        }
    }

    // Obtener texto de estado
    function getStatusText(status) {
        switch(status) {
            case 'active': return 'Disponible';
            case 'inactive': return 'No disponible';
            case 'soldout': return 'Agotado';
            default: return 'No disponible';
        }
    }

    // Obtener nombre legible de categoría

    // Configurar paginación
    function setupPagination() {
        const pagination = document.getElementById('pagination');
        pagination.innerHTML = '';

        const pageCount = Math.ceil(filteredProducts.length / productsPerPage);

        if (pageCount <= 1) return;

        // Botón Anterior
        const prevLi = document.createElement('li');
        prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
        prevLi.innerHTML = `<a class="page-link" href="#" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>`;
        prevLi.addEventListener('click', (e) => {
            e.preventDefault();
            if (currentPage > 1) {
                currentPage--;
                displayProducts();
                setupPagination();
                window.scrollTo({ top: document.getElementById('productsGrid').offsetTop - 100, behavior: 'smooth' });
            }
        });
        pagination.appendChild(prevLi);

        // Números de página
        for (let i = 1; i <= pageCount; i++) {
            const pageLi = document.createElement('li');
            pageLi.className = `page-item ${i === currentPage ? 'active' : ''}`;
            pageLi.innerHTML = `<a class="page-link" href="#">${i}</a>`;
            pageLi.addEventListener('click', (e) => {
                e.preventDefault();
                currentPage = i;
                displayProducts();
                setupPagination();
                window.scrollTo({ top: document.getElementById('productsGrid').offsetTop - 100, behavior: 'smooth' });
            });
            pagination.appendChild(pageLi);
        }

        // Botón Siguiente
        const nextLi = document.createElement('li');
        nextLi.className = `page-item ${currentPage === pageCount ? 'disabled' : ''}`;
        nextLi.innerHTML = `<a class="page-link" href="#" aria-label="Next"><span aria-hidden="true">&raquo;</span></a>`;
        nextLi.addEventListener('click', (e) => {
            e.preventDefault();
            if (currentPage < pageCount) {
                currentPage++;
                displayProducts();
                setupPagination();
                window.scrollTo({ top: document.getElementById('productsGrid').offsetTop - 100, behavior: 'smooth' });
            }
        });
        pagination.appendChild(nextLi);
    }

    // Agregar eventos a los botones de productos
    function addProductEvents() {
        document.querySelectorAll('.edit-product').forEach(button => {
            button.addEventListener('click', function() {
                const productId = parseInt(this.getAttribute('data-id'));
                editProduct(productId);
            });
        });

        document.querySelectorAll('.delete-product').forEach(button => {
            button.addEventListener('click', function() {
                const productId = parseInt(this.getAttribute('data-id'));
                const productName = this.getAttribute('data-name');
                confirmDelete(productId, productName);
            });
        });
    }

    // Abrir modal para agregar/editar producto
    function openProductModal(product = null) {
        const modal = new bootstrap.Modal(document.getElementById('productModificarModal'));
        const form = document.getElementById('registrarProducto');
        
        if (product) {
            // Modo edición
            document.getElementById('modalTitleEdit').textContent = 'Editar Producto';
            document.getElementById('productId').value = product.id;
            document.getElementById('productName').value = product.name;
            document.getElementById('productPrice').value = product.price;
            document.getElementById('productCategoryUpdate').value = product.idCategory;
            document.getElementById('productStock').value = product.stock;
            if(product.status == "active"){
                document.getElementById('productStatusUpdate').value = 1;
            }else{
                document.getElementById('productStatusUpdate').value = 0;
            }
            document.getElementById('productDescription').value = product.description;
            modal.show();
        } else {
            // Modo agregar
            document.getElementById('modalTitle').textContent = 'Agregar Nuevo Producto';
            form.reset();
            //resetear div de imagenes
            document.getElementById('previewImages').innerHTML = '';
            document.getElementById('productId').value = '';
        }
    }

    // Editar producto
    function editProduct(productId) {
        const product = products.find(p => p.id === productId);
        if (product) {
            openProductModal(product);
        }
    }

    // registrar producto (agregar o editar)
    function saveProduct() {
        const form = document.getElementById('registrarProducto');
        
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const productData = {
            id: document.getElementById('productId').value ? parseInt(document.getElementById('productId').value) : generateId(),
            name: document.getElementById('productName').value,
            price: parseFloat(document.getElementById('productPrice').value),
            category: document.getElementById('productCategory').value,
            stock: parseInt(document.getElementById('productStock').value),
            status: document.getElementById('productStatus').value,
            description: document.getElementById('productDescription').value,
            image: "https://images.unsplash.com/photo-1563013544-824ae1b704d3?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80" // Imagen por defecto
        };

        // Manejar la imagen si se subió una nueva
        const imageInput = document.getElementById('productImage');
        if (imageInput.files.length > 0) {
            // En una aplicación real, aquí subirías la imagen al servidor
            productData.image = URL.createObjectURL(imageInput.files[0]);
        } else if (document.getElementById('productId').value) {
            // Mantener la imagen existente si estamos editando
            const existingProduct = products.find(p => p.id === parseInt(document.getElementById('productId').value));
            if (existingProduct) {
                productData.image = existingProduct.image;
            }
        }

        if (document.getElementById('productId').value) {
            // Editar producto existente
            const index = products.findIndex(p => p.id === parseInt(document.getElementById('productId').value));
            if (index !== -1) {
                products[index] = productData;
            }
        } else {
            // Agregar nuevo producto
            products.push(productData);
        }

        // Cerrar modal y actualizar vista
        const modal = bootstrap.Modal.getInstance(document.getElementById('productModal'));
        modal.hide();

        filterProducts();
    }

    // actualizar producto mediante un fetch


    // Generar nuevo ID para producto
    function generateId() {
        return products.length > 0 ? Math.max(...products.map(p => p.id)) + 1 : 1;
    }

    // Confirmar eliminación de producto
    function confirmDelete(productId, productName) {
        document.getElementById('productToDeleteId').value = productId;
        document.getElementById('productToDeleteName').textContent = productName;

        const modal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
        modal.show();
    }

    // Eliminar producto
    function deleteProduct() {
        const productId = parseInt(document.getElementById('productToDeleteId').value);
        fetch( API_CONFIG + '/productos/eliminarProducto', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id: productId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarAlerta('Producto','Eliminado correctamente','success');
                products = products.filter(p => p.id !== productId);
                filteredProducts = filteredProducts.filter(p => p.id !== productId);
                
                const modal = bootstrap.Modal.getInstance(document.getElementById('confirmDeleteModal'));
                modal.hide();
                
                displayProducts();
                setupPagination();
            } else {
               mostrarAlerta('Producto','Error al eliminar producto: '+data.message,'error');
            }
        })
        .catch(error => {
            mostrarAlerta('Producto','Error al eliminar producto: '+error.message,'error');
        });

        displayProducts();
        setupPagination();
    }

    const form = document.querySelector('#registrarProducto');
    const input = document.getElementById("productImage");
    const preview = document.getElementById("previewImages");
    let filesArray = [];

    // Maneja la selección y eliminación de imágenes
    input.addEventListener("change", function () {
        const nuevosArchivos = Array.from(this.files);
        nuevosArchivos.forEach(nuevo => {
            if (!filesArray.some(f => f.name === nuevo.name && f.size === nuevo.size)) {
                filesArray.push(nuevo);
            }
        });
        mostrarPrevisualizaciones();
        input.value = ""; // Permite volver a seleccionar el mismo archivo si se elimina
    });

    function mostrarPrevisualizaciones() {
        preview.innerHTML = "";
        filesArray.forEach((file, idx) => {
            if (file.type.startsWith("image/")) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const wrapper = document.createElement("div");
                    wrapper.style.position = "relative";
                    wrapper.style.display = "inline-block";

                    const img = document.createElement("img");
                    img.src = e.target.result;
                    img.className = "rounded border";
                    img.style.width = "80px";
                    img.style.height = "80px";
                    img.style.objectFit = "cover";
                    img.style.marginRight = "8px";

                    const btn = document.createElement("button");
                    btn.type = "button";
                    btn.innerHTML = "&times;";
                    btn.className = "btn btn-sm btn-danger";
                    btn.style.position = "absolute";
                    btn.style.top = "2px";
                    btn.style.right = "2px";
                    btn.style.padding = "0 8px";
                    btn.style.borderRadius = "25%";
                    btn.style.lineHeight = "1";
                    btn.style.fontSize = "16px";
                    btn.onclick = function () {
                        filesArray.splice(idx, 1);
                        mostrarPrevisualizaciones();
                    };

                    wrapper.appendChild(img);
                    wrapper.appendChild(btn);
                    preview.appendChild(wrapper);
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Unifica el envío: agrega manualmente los archivos de filesArray al FormData
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(form);

        formData.delete('imagenes[]');

        filesArray.forEach(file => {
            formData.append('imagenes[]', file);
        });

        fetch(API_CONFIG + '/productos/register', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarAlerta('Producto','Registrado correctamente','success');
                cargarProductos();

                // === Reiniciar checkbox de porción al registrar ===
                const checkboxPorcion = document.getElementById("productoPorPorcion");
                const porcionesFields = document.getElementById("porcionesFields");

                checkboxPorcion.checked = false;
                porcionesFields.style.display = "none";

                document.getElementById("cantidadPorciones").value = '';
                document.getElementById("precioPorPorcion").value = '';
                document.getElementById("cantidadPorciones").required = false;
                document.getElementById("precioPorPorcion").required = false;
                // ===================================================
            } else {
                mostrarAlerta('Error',data.message,'error');
            }
        })
        .catch(error => {
            console.error(error);
        });

    });


const editForm = document.querySelector('#editarProducto');
    const editInput = document.getElementById("productEditImages");
    const editPreview = document.getElementById("previewEditImages");
    let editFilesArray = [];

    // Maneja la selección y eliminación de imágenes para editar
    if (editInput) {
        editInput.addEventListener("change", function () {
            const nuevosArchivos = Array.from(this.files);
            nuevosArchivos.forEach(nuevo => {
                if (!editFilesArray.some(f => f.name === nuevo.name && f.size === nuevo.size)) {
                    editFilesArray.push(nuevo);
                }
            });
            mostrarPrevisualizacionesEdicion();
            editInput.value = ""; // Permite volver a seleccionar el mismo archivo si se elimina
        });
    }

    function mostrarPrevisualizacionesEdicion() {
        editPreview.innerHTML = "";
        editFilesArray.forEach((file, idx) => {
            if (file.type.startsWith("image/")) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const wrapper = document.createElement("div");
                    wrapper.style.position = "relative";
                    wrapper.style.display = "inline-block";

                    const img = document.createElement("img");
                    img.src = e.target.result;
                    img.className = "rounded border";
                    img.style.width = "80px";
                    img.style.height = "80px";
                    img.style.objectFit = "cover";
                    img.style.marginRight = "8px";

                    const btn = document.createElement("button");
                    btn.type = "button";
                    btn.innerHTML = "&times;";
                    btn.className = "btn btn-sm btn-danger";
                    btn.style.position = "absolute";
                    btn.style.top = "2px";
                    btn.style.right = "2px";
                    btn.style.padding = "0 8px";
                    btn.style.borderRadius = "25%";
                    btn.style.lineHeight = "1";
                    btn.style.fontSize = "16px";
                    btn.onclick = function () {
                        editFilesArray.splice(idx, 1);
                        mostrarPrevisualizacionesEdicion();
                    };

                    wrapper.appendChild(img);
                    wrapper.appendChild(btn);
                    editPreview.appendChild(wrapper);
                };
                reader.readAsDataURL(file);
            }
        });
    }

    if (editForm) {
        editForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(editForm);
            
            formData.delete('imagen_edit[]');

            editFilesArray.forEach(file => {
                formData.append('imagen_edit[]', file);
            });

            fetch( API_CONFIG + '/productos/actualizar', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarAlerta('Producto','Actualizado correctamente','success');
                    cargarProductos();
                    const modal = bootstrap.Modal.getInstance(document.getElementById('productModificarModal'));
                    if (modal) {
                        modal.hide();
                    }
                } else {
                    mostrarAlerta('Error',data.message,'error');
                }
            })
            .catch(error => {
                console.error(error);
            });
        });
    }
});


// Validación de campos del formulario
// //Para mostrar el error en el span
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

// Bloqueo de caracteres no permitidos en `keypress`, para validar en tiempo real
document.getElementById("nombre").addEventListener("keypress", function(event) {
    restrictInput(event, /^[A-Za-z0-9\s]$/, "nombre", "Solo se permiten letras y números.");
});

document.getElementById("descripcion").addEventListener("keypress", function(event) {
    restrictInput(event, /^[A-Za-z0-9\s]$/, "descripcion", "Solo se permiten letras y números.");
});
document.getElementById("precio").addEventListener("keypress", function (event) {
    restrictInput(event, /^[0-9.]$/, "precio", "Solo se permiten números y punto decimal.");
});

document.getElementById("cantidadPorciones").addEventListener("keypress", function(event) {
    restrictInput(event, /^[0-9]$/, "cantidadPorciones", "Solo se permiten números enteros.");
});

document.getElementById("precioPorPorcion").addEventListener("keypress", function (event) {
    restrictInput(event, /^[0-9.]$/, "precioPorPorcion", "Solo se permiten números y punto decimal.");
});
document.getElementById("stock").addEventListener("keypress", function(event) {
    restrictInput(event, /^[0-9]$/, "stock", "Solo se permiten números enteros.");
});

//VALDIACIONES DE CAMPOS DE EDITAR
document.getElementById("productName").addEventListener("keypress", function(event) {
    restrictInput(event, /^[A-Za-z0-9\s]$/, "productName", "Solo se permiten letras y números.");
});

document.getElementById("productPrice").addEventListener("keypress", function (event) {
    restrictInput(event, /^[0-9.]$/, "productPrice", "Solo se permiten números y punto decimal.");
});

document.getElementById("productStock").addEventListener("keypress", function(event) {
    restrictInput(event, /^[0-9]$/, "productStock", "Solo se permiten números enteros.");
});

document.getElementById("productDescription").addEventListener("keypress", function(event) {
    restrictInput(event, /^[A-Za-z0-9\s]$/, "productDescription", "Solo se permiten letras y números.");
});


// Validaciones completas en `input`, sin mensajes de error
function validateNombre() {
    const nombre = document.getElementById("nombre").value;
    const nombreRegex = /^[A-Za-z0-9\s]{5,50}$/;
    if (!nombreRegex.test(nombre)) {
        showError("nombre", "El nombre debe tener entre 5 y 50 caracteres, solo letras y números.");
        return false;
    } else {
        clearError("nombre");
        return true;
    }
}


function validateDescripcion() {
    const descripcion = document.getElementById("descripcion").value;
    if (descripcion.length > 50) {
        showError("descripcion", "La descripción no debe superar los 50 caracteres.");
        return false;
    } else {
        clearError("descripcion");
        return true;
    }
}



function validatePrecio() {
    const precio = document.getElementById("precio").value;
    const valor = parseFloat(precio);
    return !isNaN(valor) && valor > 0;
}
function validatePrecioPorPorcion() {
    const precioPorPorcion = document.getElementById("precioPorPorcion").value;
    return Number.isInteger(Number(precioPorPorcion)) && precioPorPorcion > 0;
}

function validatecantidadPorciones() {
    const cantidadPorciones = document.getElementById("cantidadPorciones").value;
    const valor = parseFloat(cantidadPorciones);
    return !isNaN(valor) && valor > 0;
}
function validateStock() {
    const stock = document.getElementById("stock").value;
    return Number.isInteger(Number(stock)) && stock > 0;
}

//FUNCIONES EDITAR 
function validateEditar() {
    const nombre = document.getElementById("productName").value;
    const nombreRegex = /^[A-Za-z0-9\s]{5,50}$/;
    if (!nombreRegex.test(nombre)) {
        showError("productName", "El nombre debe tener entre 5 y 50 caracteres, solo letras y números.");
        return false;
    } else {
        clearError("productName");
        return true;
    }
}
function validatePrecioEditar() {
    const precio = document.getElementById("productPrice").value;
    const valor = parseFloat(precio);
    return !isNaN(valor) && valor > 0;
}

function validateStockEditar() {
    const stock = document.getElementById("productStock").value;
    return Number.isInteger(Number(stock)) && stock > 0;
}
function validateDescripcionEditar() {
    const nombre = document.getElementById("productDescription").value;
    const nombreRegex = /^[A-Za-z0-9\s]{5,50}$/;
    if (!nombreRegex.test(nombre)) {
        showError("productDescription", "La descripción tener entre 5 y 50 caracteres.");
        return false;
    } else {
        clearError("productDescription");
        return true;
    }
}
//Se valida de manera general
function enableSubmit() {
    //Se validan en funciones que cumplan todas con las exp reg
    const isFormValid =
        validateNombre() &&
        validatePrecio() &&
        validateStock() &&
        validateDescripcion() &&
        validatePrecioPorPorcion() &&
        validatecantidadPorciones() &&
      
        //validateDescripcion() &&
        document.getElementById("descripcion").value;//Y aqui se validan que realmente tengan un valor estos campos
       
}

// Asignar eventos `input` para validar el formulario y habilitar el botón de guardar sin mostrar mensajes de error

document.getElementById("nombre").addEventListener("input", enableSubmit);
document.getElementById("precio").addEventListener("input", enableSubmit);
document.getElementById("cantidadPorciones").addEventListener("input", enableSubmit);
document.getElementById("precioPorPorcion").addEventListener("input", enableSubmit);
document.getElementById("stock").addEventListener("input", enableSubmit);
document.getElementById("descripcion").addEventListener("input", enableSubmit);



function enableSubmit_editar() {
    //Se validan en funciones que cumplan todas con las exp reg
    const isFormValid =

        validateEditar () &&
        validatePrecioEditar() &&
        validateStockEditar () &&
        validateDescripcionEditar() &&

        document.getElementById("productName").value.trim() !== "";
        document.getElementById("productDescription").value.trim() !== "";
        document.getElementById("productStock").value.trim() !== "";
        document.getElementById("productPrice").value.trim() !== "";
        document.getElementById("updateProductBtn").disabled = !isFormValid;
    
        
}

document.getElementById("productName").addEventListener("input", enableSubmit_editar);
document.getElementById("productStock").addEventListener("input", enableSubmit_editar);
document.getElementById("productPrice").addEventListener("input", enableSubmit_editar);
document.getElementById("productDescription").addEventListener("input", enableSubmit_editar);


function enforcePositiveNumber(fieldId, errorFieldId, fieldName = "valor") {
    const input = document.getElementById(fieldId);
    const value = parseFloat(input.value);
    if (value < 1 || isNaN(value)) {
        input.value = "";
        showError(fieldId, `El ${fieldName} debe ser mayor que 0.`);
    } else {
        clearError(fieldId);
    }
}

document.getElementById("precio").addEventListener("input", function () {
    enforcePositiveNumber("precio", "precioError", "precio");
});
document.getElementById("stock").addEventListener("input", function () {
    enforcePositiveNumber("stock", "stockError", "stock");
});
document.getElementById("cantidadPorciones").addEventListener("input", function () {
    enforcePositiveNumber("cantidadPorciones", "cantidadPorcionesError", "cantidad de porciones");
});
document.getElementById("precioPorPorcion").addEventListener("input", function () {
    enforcePositiveNumber("precioPorPorcion", "precioPorPorcionError", "precio por porción");
});


//Nuevas validaciones
function restrictCharacters(event, allowedRegex, maxLength = null, maxNumber = null) {
    const key = event.key;
    const input = event.target;

    // Permitir teclas especiales
    if (key === "Backspace" || key === "Tab") return;

    // Bloquear caracteres no permitidos
    if (!allowedRegex.test(key)) {
        event.preventDefault();
        return;
    }

    // Evitar que se sobrepase la longitud máxima
    if (maxLength !== null && input.value.length >= maxLength) {
        event.preventDefault();
        return;
    }

    // Evitar superar un valor numérico máximo (ej. precio <= 10000)
    if (maxNumber !== null) {
        const newValue = input.value + key;

        // Intentar convertir a número ignorando errores
        const numericValue = parseFloat(newValue);
        if (!isNaN(numericValue) && numericValue > maxNumber) {
            event.preventDefault();
            return;
        }
    }
}

function restrictPrecioPorPorcion(event) {
    const key = event.key;
    const input = document.getElementById("precioPorPorcion");
    const precio = parseInt(document.getElementById("precio").value);

    // Permitir borrar y tab
    if (key === "Backspace" || key === "Tab") return;

    // Permitir solo números
    if (!/[0-9]/.test(key)) {
        event.preventDefault();
        return;
    }

    // Construir el valor futuro
    const futuro = input.value + key;
    const valor = parseInt(futuro);

    // Si no hay precio, no validar todavía
    if (isNaN(precio) || precio <= 0) return;

    // Bloquear si es >= precio
    if (valor >= precio) {
        event.preventDefault();
        return;
    }
}



// Bloqueo de caracteres no permitidos en `keypress`, para validar en tiempo real
// Campo de nombre 
document.getElementById("nombre").addEventListener("keypress", function(event) {
    restrictCharacters(event, /^[A-Za-z\s]$/, 50);
});

// Campo de descripción
document.getElementById("descripcion").addEventListener("keypress", function(event) {
    restrictCharacters(event, /^[A-Za-z0-9\s]$/, 50);
});

//Campo de precio
document.getElementById("precio").addEventListener("keypress", function(event) {
    restrictCharacters(event, /^[0-9.]$/, 7, 10000);
});

//Campo de cantidad de porciones
document.getElementById("cantidadPorciones").addEventListener("keypress", function(event) {
    restrictCharacters(event, /^[0-9]$/, 3, 100);
});

//Campo de precio por porción
document.getElementById("precioPorPorcion").addEventListener("keypress", restrictPrecioPorPorcion);


//Campo de stock
document.getElementById("stock").addEventListener("keypress", function(event) {
    restrictCharacters(event, /^[0-9]$/, 3, 999);
});

//Para los campos de editar
// Campo de nombre
document.getElementById("productName").addEventListener("keypress", function(event) {
    restrictCharacters(event, /^[A-Za-z\s]$/, 50);
});

// campo de descripción
document.getElementById("productDescription").addEventListener("keypress", function(event) {
    restrictCharacters(event, /^[A-Za-z0-9\s]$/, 50);
});

//campo de precio
document.getElementById("productPrice").addEventListener("keypress", function(event) {
    restrictCharacters(event, /^[0-9.]$/, 7, 10000);
});

//campo de cantidad de porciones
document.getElementById("productStock").addEventListener("keypress", function(event) {
    restrictCharacters(event, /^[0-9]$/, 3, 999);
});



