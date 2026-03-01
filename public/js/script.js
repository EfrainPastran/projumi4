document.addEventListener('DOMContentLoaded', function() {
    // Manejo del modal de producto
    const productModal = document.getElementById('productModal');
    if (productModal) {
        productModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget; // Botón que disparó el modal
            const productId = button.getAttribute('data-product-id');
            
            // Aquí iría la lógica para cargar los datos del producto según el ID
            // Por ahora usaremos datos estáticos de ejemplo
            loadProductData(productId);
        });
    }
    
    // Contador del carrito
    updateCartCount();
    
    // Incrementar/decrementar cantidad en modal
    const quantityInput = document.getElementById('quantity');
    if (quantityInput) {
        document.getElementById('increment').addEventListener('click', function() {
            quantityInput.value = parseInt(quantityInput.value) + 1;
        });
        
        document.getElementById('decrement').addEventListener('click', function() {
            if (parseInt(quantityInput.value) > 1) {
                quantityInput.value = parseInt(quantityInput.value) - 1;
            }
        });
    }
    
    // Botones "Añadir al carrito"
    document.querySelectorAll('.btn-add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            addToCart(productId);
        });
    });
});

// Función para cargar datos del producto (simulada)
function loadProductData(productId) {
    // En una aplicación real, esto haría una petición AJAX para obtener los datos del producto
    console.log('Cargando datos del producto:', productId);
    
    // Simulamos datos diferentes según el ID
    const products = {
        '1': {
            name: 'Pastel de Chocolate',
            price: '$15.00',
            description: 'Delicioso pastel de chocolate hecho con ingredientes 100% naturales, decorado con fresas frescas y crema batida. Perfecto para celebraciones especiales.',
            category: 'Repostería'
        },
        '2': {
            name: 'Muñeca Artesanal',
            price: '$25.00',
            description: 'Muñeca tejida a mano con materiales hipoalergénicos, ideal para niños. Cada pieza es única y lleva aproximadamente 15 horas de trabajo.',
            category: 'Muñequería'
        },
        // Agregar más productos según sea necesario
    };
    
    const productData = products[productId] || {
        name: 'Producto Ejemplo',
        price: '$0.00',
        description: 'Descripción del producto no disponible.',
        category: 'General'
    };
    
    // Actualizar el modal con los datos del producto
    document.getElementById('productName').textContent = productData.name;
    document.getElementById('productPrice').textContent = productData.price;
    document.getElementById('productDescription').textContent = productData.description;
    document.querySelector('.badge.bg-brown').textContent = productData.category;
}

// Función para añadir producto al carrito
function addToCart(productId) {
    // Lógica para añadir al carrito
    console.log('Añadiendo al carrito producto:', productId);
    
    // Simulamos añadir al carrito
    const cartCount = localStorage.getItem('cartCount') || 0;
    localStorage.setItem('cartCount', parseInt(cartCount) + 1);
    
    updateCartCount();
    
    // Mostrar notificación
    alert('Producto añadido al carrito');
}

// Función para actualizar el contador del carrito
function updateCartCount() {
    const cartCount = localStorage.getItem('cartCount') || 0;
    document.querySelectorAll('.cart-count').forEach(element => {
        element.textContent = cartCount;
    });
}