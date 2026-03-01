import { API_CONFIG } from './config.js';
document.addEventListener('DOMContentLoaded', function() {
    // Datos de ejemplo de productos
let products = [];
let filteredProducts = [];
let currentPage = 1;
const productsPerPage = 9;

// Cargar productos desde la API
fetch( API_CONFIG + '/productos/mostrarProductos')
    .then(response => response.json())
    .then(data => {
        products = data.map(p => ({
            id: p.id_producto,
            name: p.nombre,
            price: parseFloat(p.precio),
            description: p.descripcion,
            stock: parseInt(p.stock),
            category: p.categoria,
            idCategory: p.id_categoria,
            status: p.stock === 0 ? 'soldout' : 'active',
            // Si hay imágenes, toma la primera como principal, si no, usa un placeholder
            image: (p.imagenes && p.imagenes.length > 0) 
                ? (p.imagenes[0].startsWith('http') ? p.imagenes[0] : `../${p.imagenes[0]}`) 
                : 'https://via.placeholder.com/400x300?text=Sin+imagen',
            imagenes: p.imagenes || [],
            id_emprendedor: p.id_emprededor,
            emprendedor: p.emprendedor
        }));
        filteredProducts = [...products];
        displayAllProducts();
        setupPagination();
    })
    .catch(error => {
        console.error('Error al cargar productos:', error);
    });


    // Evento de búsqueda
    document.getElementById('searchInput').addEventListener('input', function() {
        filterProducts();
    });

    // Evento de filtro por categoría
    document.getElementById('categoryFilter').addEventListener('change', function() {
        filterByCategory();
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
        displayAllProducts();
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
        displayAllProducts();
        setupPagination();
    }

    // Mostrar todos los productos con paginación
    function displayAllProducts() {
        const productsGrid = document.getElementById('productsGrid');
        productsGrid.innerHTML = '';

        const startIndex = (currentPage - 1) * productsPerPage;
        const endIndex = startIndex + productsPerPage;
        const paginatedProducts = filteredProducts.slice(startIndex, endIndex);

        if (paginatedProducts.length === 0) {
            productsGrid.innerHTML = '<div class="col-12 text-center py-5"><h4>No se encontraron productos</h4></div>';
            return;
        }

        paginatedProducts.forEach(product => {
            const productElement = createProductElement(product, false);
            productsGrid.appendChild(productElement);
        });

        // Agregar eventos a los botones
        addViewDetailsEvents();
    }

    // Crear elemento HTML para un producto
    function createProductElement(product, isFeatured) {
        const col = document.createElement('div');
        col.className = isFeatured ? '' : 'col-md-4 col-sm-6 mb-4';

        const productCard = document.createElement('div');
        productCard.className = isFeatured ? 'product-card featured-product' : 'product-card';

        productCard.innerHTML = `
            <div class="card shadow-sm h-100">
                <img src="${product.image}" class="card-img-top" alt="${product.name}" style="object-fit: cover; height: 200px;">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                    <p class="text-muted mb-2">Emprendimiento: <strong>${product.emprendedor}</strong></p>
                        <h5 class="card-title mb-1">${product.name}</h5>
                        <p class="card-text small text-truncate">${product.description}</p>
                    </div>
                    <div>
                        <p class="fw-bold text-success mb-2">$${product.price.toFixed(2)}</p>
                        <button class="btn btn-view-details" data-id="${product.id}">
                            <i class="fas fa-eye"></i> Ver detalles
                        </button>
                    </div>
                </div>
            </div>
        `;

        if (isFeatured) {
            col.appendChild(productCard);
            return col;
        } else {
            productCard.classList.add('h-100');
            col.appendChild(productCard);
            return col;
        }
    }

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
                displayAllProducts();
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
                displayAllProducts();
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
                displayAllProducts();
                setupPagination();
                window.scrollTo({ top: document.getElementById('productsGrid').offsetTop - 100, behavior: 'smooth' });
            }
        });
        pagination.appendChild(nextLi);
    }

    // Agregar eventos a los botones "Ver detalles"
    function addViewDetailsEvents() {
        document.querySelectorAll('.btn-view-details').forEach(button => {
            button.addEventListener('click', function() {
                const productId = parseInt(this.getAttribute('data-id'));
                showProductDetails(productId);
            });
        });
    }

    // Mostrar detalles del producto en modal
    function showProductDetails(productId) {
        const product = products.find(p => p.id === productId);
        if (!product) return;
        document.getElementById("id_producto").value = product.id;
        document.getElementById("id_emprendedor").value = product.id_emprendedor;

        // Generar carrusel dinámicamente
        const carouselContainer = document.getElementById('modalProductCarousel');
        let imagenes = product.imagenes && product.imagenes.length > 0 ? product.imagenes : [product.image];
        let carouselId = 'carouselProductImages';

        let carouselHtml = `
        <div id="${carouselId}" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            ${imagenes.map((img, idx) => `
            <div class="carousel-item${idx === 0 ? ' active' : ''}">
                <img src="${img.startsWith('http') ? img : '../' + img}" class="d-block w-100" alt="Imagen ${idx + 1}" style="object-fit:cover;max-height:300px;">
            </div>
            `).join('')}
        </div>
        ${imagenes.length > 1 ? `
        <button class="carousel-control-prev" type="button" data-bs-target="#${carouselId}" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#${carouselId}" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
        ` : ''}
        </div>
        `;
        carouselContainer.innerHTML = carouselHtml;

        document.getElementById('modalProductEmprendedor').textContent = product.emprendedor;
        document.getElementById('modalProductName').textContent = product.name;
        document.getElementById('modalProductPrice').textContent = `$${product.price.toFixed(2)}`;
        document.getElementById('modalProductDescription').textContent = product.description;
        document.getElementById('modalProductCategory').textContent = getCategoryName(product.category);
        document.getElementById('modalProductStock').textContent = product.stock;
        document.getElementById('productQuantity').value = 1;
        actualizarEstadoBotonCarrito();

        const modal = new bootstrap.Modal(document.getElementById('productModal'));
        modal.show();
    }

    // Obtener nombre legible de categoría
    function getCategoryName(categoryKey) {
        const categories = {
            'artesanias': 'Artesanías',
            'alimentos': 'Alimentos Orgánicos',
            'textiles': 'Textiles',
            'joyeria': 'Joyería'
        };
        return categories[categoryKey] || categoryKey;
    }

    // Eventos para aumentar/disminuir cantidad
    document.getElementById('increaseQuantity').addEventListener('click', function() {
        const quantityInput = document.getElementById('productQuantity');
        quantityInput.value = parseInt(quantityInput.value) + 1;
    });

    document.getElementById('decreaseQuantity').addEventListener('click', function() {
        const quantityInput = document.getElementById('productQuantity');
        if (parseInt(quantityInput.value) > 1) {
            quantityInput.value = parseInt(quantityInput.value) - 1;
        }
    });

document.getElementById('addToCartBtn').addEventListener('click', function () {
    const quantity = parseInt(document.getElementById('productQuantity').value);
    const productId = document.getElementById("id_producto").value;
    const productData = products.find(p => p.id == productId);
    const product = {
        id: productId,
        id_emprendedor: document.getElementById("id_emprendedor").value,
        image: (productData.imagenes && productData.imagenes.length > 0)
            ? (productData.imagenes[0].startsWith('http') ? productData.imagenes[0] : '../' + productData.imagenes[0])
            : 'https://via.placeholder.com/400x300?text=Sin+imagen',
        emprendedor: document.getElementById('modalProductEmprendedor').textContent,
        name: document.getElementById('modalProductName').textContent,
        price: parseFloat(document.getElementById('modalProductPrice').textContent.replace('$', '')),
        description: document.getElementById('modalProductDescription').textContent,
        category: document.getElementById('modalProductCategory').textContent,
        stock: parseInt(document.getElementById('modalProductStock').textContent),
        quantity: quantity
    };


    let carritoLocal = JSON.parse(localStorage.getItem('carritoPorEmprendedor')) || {};
    const emprendedoresEnCarrito = Object.keys(carritoLocal);
    const mismoEmprendedor = emprendedoresEnCarrito.length === 0 || emprendedoresEnCarrito.includes(product.id_emprendedor);

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


    if (!carritoLocal[product.id_emprendedor]) {
        carritoLocal[product.id_emprendedor] = [];
    }

    const existente = carritoLocal[product.id_emprendedor].find(p => p.id === product.id);
    const cantidadTotal = (existente ? existente.quantity : 0) + quantity;
    
    if (cantidadTotal > product.stock) {
        Swal.fire({
            title: '<span class="fw-bold">Stock insuficiente</span>',
            text: `Solo hay ${product.stock} unidades disponibles. Ya tienes ${existente ? existente.quantity : 0} en el carrito.`,
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
    
    if (existente) {
        existente.quantity += quantity;
    } else {
        carritoLocal[product.id_emprendedor].push(product);
    }
    

    localStorage.setItem('carritoPorEmprendedor', JSON.stringify(carritoLocal));
    localStorage.setItem('id_emprendedor', product.id_emprendedor);
    actualizarContadorCarrito();
    if (!localStorage.getItem('carritoExpiracion')) {
        const expiracion = Date.now() + 24 * 60 * 60 * 1000;
        localStorage.setItem('carritoExpiracion', expiracion.toString());
    }
    const modal = bootstrap.Modal.getInstance(document.getElementById('productModal'));
    if (modal) {
        modal.hide();   
    }
});

});

function actualizarEstadoBotonCarrito() {
    const stockElement = document.getElementById('modalProductStock');
    const addToCartBtn = document.getElementById('addToCartBtn');

    const stock = parseInt(stockElement.textContent, 10);

    if (isNaN(stock) || stock <= 0) {
        addToCartBtn.disabled = true;
        addToCartBtn.classList.add('disabled');
        addToCartBtn.textContent = 'Sin stock';
    } else {
        addToCartBtn.disabled = false;
        addToCartBtn.classList.remove('disabled');
        addToCartBtn.innerHTML = '<i class="fas fa-cart-plus"></i> Añadir al carrito';
    }
}