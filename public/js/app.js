document.addEventListener('DOMContentLoaded', function() {
    // Validación mejorada del formulario
    const registerForm = document.querySelector('form');
    if(registerForm) {
        registerForm.addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const cedula = document.getElementById('cedula').value;
            
            // Validar cédula
            if(!/^[0-9]{6,12}$/.test(cedula)) {
                alert('La cédula debe contener solo números (6-12 dígitos)');
                e.preventDefault();
                return;
            }
            
            // Validar contraseña
            if(password.length < 6) {
                alert('La contraseña debe tener al menos 6 caracteres');
                e.preventDefault();
                return;
            }
        });
    }
    
    // Mostrar mensajes flash
    if(document.querySelector('.alert')) {
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 600);
            });
        }, 5000);
    }
});