<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <?php include 'views/componentes/head.php'; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/register.css">
</head>
<body class="bg-light">

    <div class="container">
        <div class="register-container">
            <div class="form-header">
                <h2>Registro de Cliente</h2>
                <p class="text-muted">Complete todos los campos obligatorios</p>
            </div>


            <form id="formRegistrarUsuarioCliente" method="post">
                <!-- Sección Información Personal -->
                <div class="form-section">
                    <h5 class="section-title">Información Personal</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="cedula" class="form-label required-field">Cédula</label>
                            <input type="number" class="form-control" id="cedula" name="cedula" required>
                        </div>
                        <div class="col-md-6">
                            <label for="fecha_nacimiento" class="form-label required-field">Fecha de Nacimiento</label>
                            <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required>
                        </div>
                        <div class="col-md-6">
                            <label for="nombre" class="form-label required-field">Nombres</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="col-md-6">
                            <label for="apellido" class="form-label required-field">Apellidos</label>
                            <input type="text" class="form-control" id="apellido" name="apellido" required>
                        </div>
                        <div class="col-md-6">
                            <label for="telefono" class="form-label required-field">Teléfono</label>
                            <input type="tel" class="form-control" id="telefono" name="telefono" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edad" class="form-label">Edad</label>
                            <input type="number" class="form-control" id="edad" name="edad" readonly disabled>
                        </div>
                    </div>
                </div>

                <!-- Sección Contacto -->
                <div class="form-section">
                    <h5 class="section-title">Información de Contacto</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="email" class="form-label required-field">Correo Electrónico</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                            <div class="form-text">Ejemplo: usuario@dominio.com</div>
                        </div>
                        <div class="col-md-6">
                            <label for="direccion" class="form-label required-field">Dirección</label>
                            <textarea class="form-control" id="direccion" name="direccion" rows="1" required></textarea>
                        </div>
                    </div>
                </div>

                <!-- Sección Seguridad -->
                <div class="form-section">
                    <h5 class="section-title">Seguridad</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="pass" class="form-label required-field">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div class="form-text">Mínimo 8 caracteres</div>
                        </div>
                        <div class="col-md-6">
                            <label for="confirm_pass" class="form-label required-field">Confirmar Contraseña</label>
                            <input type="password" class="form-control" id="confirm_pass" name="confirm_pass" required>
                        </div>
                    </div>
                </div>

                <!-- Sección Términos -->
                <div class="form-section">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="acceptTerms" required>
                        <label class="form-check-label" for="acceptTerms">
                            Acepto los <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">términos y condiciones</a>
                        </label>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-user-plus me-2"></i>Registrarse
                    </button>
                </div>
            </form>

            <div class="text-center mt-3">
                <p>¿Ya tienes una cuenta? <a href="<?php echo APP_URL; ?>/home/principal">Inicia Sesión</a></p>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?php echo APP_URL; ?>/public/js/registroUsuarioCliente.js" type='module'></script>
    <!-- <script>
        
    $('#formRegistrarUsuarioCliente').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            type: 'POST',
            url: 'http://localhost/projumi/auth/registrarUsuarioCliente',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    Swal.fire({
                        title: '¡Registro exitoso!',
                        text: 'Puede iniciar sesión',
                        icon: 'success',
                        confirmButtonText: 'Iniciar sesión'
                    }).then(() => {
                        window.location.href = 'http://localhost/projumi/home/principal';
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: res.message,
                        icon: 'error'
                    });
                }
            },
            error: function() {
                Swal.fire('Error de servidor', 'No se pudo registrar', 'error');
            }
        });
    });
        // Calcular edad automáticamente
        document.getElementById('fecha_nacimiento').addEventListener('change', function() {
            const fechaNacimiento = new Date(this.value);
            const hoy = new Date();
            let edad = hoy.getFullYear() - fechaNacimiento.getFullYear();
            const mes = hoy.getMonth() - fechaNacimiento.getMonth();
            
            if (mes < 0 || (mes === 0 && hoy.getDate() < fechaNacimiento.getDate())) {
                edad--;
            }
            
            document.getElementById('edad').value = edad;
        });

        // Validación de contraseña
        document.querySelector('form').addEventListener('submit', function(e) {
            const pass = document.getElementById('pass').value;
            const confirmPass = document.getElementById('confirm_pass').value;
            
            if (pass !== confirmPass) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Las contraseñas no coinciden',
                });
            }
            
            if (pass.length < 8) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'La contraseña debe tener al menos 8 caracteres',
                });
            }
        });
    </script> -->
</body>
</html>