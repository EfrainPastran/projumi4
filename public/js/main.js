document.addEventListener('DOMContentLoaded', function() {
    console.log('Sistema MVC cargado');
    
    // Ejemplo de funcionalidad JavaScript
    document.querySelectorAll('input').forEach(input => {
        input.addEventListener('focus', function() {
            this.style.borderColor = '#007bff';
        });
        
        input.addEventListener('blur', function() {
            this.style.borderColor = '#ddd';
        });
    });
});