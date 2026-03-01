document.addEventListener('DOMContentLoaded', function() {
    // Cambiar imagen principal al hacer clic en miniaturas
    window.changeMainImage = function(element) {
        const mainCarousel = document.querySelector('#productImages .carousel-inner');
        const newActiveItem = document.createElement('div');
        newActiveItem.className = 'carousel-item active';
        
        const img = document.createElement('img');
        img.src = element.src;
        img.className = 'd-block w-100';
        img.alt = 'Producto';
        
        newActiveItem.appendChild(img);
        mainCarousel.innerHTML = '';
        mainCarousel.appendChild(newActiveItem);
    };

    // Sistema de rating
    const stars = document.querySelectorAll('.rating i');
    stars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = this.getAttribute('data-rating');
            document.getElementById('rating-value').value = rating;
            
            stars.forEach(s => {
                s.classList.remove('fas', 'active');
                s.classList.add('far');
            });
            
            for (let i = 0; i < rating; i++) {
                stars[i].classList.remove('far');
                stars[i].classList.add('fas', 'active');
            }
        });
        
        star.addEventListener('mouseover', function() {
            const hoverRating = this.getAttribute('data-rating');
            
            stars.forEach(s => {
                s.classList.remove('fas');
                s.classList.add('far');
            });
            
            for (let i = 0; i < hoverRating; i++) {
                stars[i].classList.remove('far');
                stars[i].classList.add('fas');
            }
        });
        
        star.addEventListener('mouseout', function() {
            const currentRating = document.getElementById('rating-value').value;
            
            stars.forEach(s => {
                s.classList.remove('fas', 'active');
                s.classList.add('far');
            });
            
            if (currentRating > 0) {
                for (let i = 0; i < currentRating; i++) {
                    stars[i].classList.remove('far');
                    stars[i].classList.add('fas', 'active');
                }
            }
        });
    });

    // Contador de cantidad
    const quantityInput = document.querySelector('.input-group input');
    const minusBtn = document.querySelector('.input-group button:first-child');
    const plusBtn = document.querySelector('.input-group button:last-child');
    
    if (quantityInput && minusBtn && plusBtn) {
        minusBtn.addEventListener('click', function() {
            let value = parseInt(quantityInput.value);
            if (value > 1) {
                quantityInput.value = value - 1;
            }
        });
        
        plusBtn.addEventListener('click', function() {
            let value = parseInt(quantityInput.value);
            quantityInput.value = value + 1;
        });
    }

    // Añade esta parte justo antes del cierre del </body>
document.addEventListener('DOMContentLoaded', function() {
    const navbar = document.querySelector('.navbar');
    
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
});

    // Inicializar tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Mostrar mensaje al añadir al carrito
    const addToCartButtons = document.querySelectorAll('.btn-primary');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (this.textContent.includes('carrito')) {
                e.preventDefault();
                alert('Producto añadido al carrito');
            }
        });
    });
});